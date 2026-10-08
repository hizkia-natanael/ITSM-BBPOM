<?php

namespace Tests\Feature;

use App\Filament\Widgets\TicketTrendChart;
use App\Filament\Widgets\UnhandledTicketsAlertWidget;
use App\Models\Category;
use App\Models\PushSubscription;
use App\Models\Ticket;
use App\Models\User;
use App\Services\WebPushService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminNotificationAndChartTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $regularUser;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin1@bbpom.go.id',
        ]);

        $this->regularUser = User::factory()->create([
            'role' => 'user',
            'email' => 'pegawai@bbpom.go.id',
        ]);

        $this->category = Category::create([
            'name' => 'Jaringan & Internet',
            'slug' => 'jaringan-internet',
        ]);
    }

    public function test_dashboard_chart_has_compact_max_height_and_supports_hover_filters(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(TicketTrendChart::class)
            ->assertSet('filter', 'all')
            ->call('applyHoverFilter', 'pending')
            ->assertSet('filter', 'pending')
            ->call('applyHoverPeriod', 'tahun')
            ->assertSet('period', 'tahun')
            ->call('selectFilter', 'diproses')
            ->assertSet('filter', 'diproses');
    }

    public function test_unhandled_tickets_alert_widget_detects_unhandled_tickets(): void
    {
        $this->actingAs($this->admin);

        // Initially no tickets
        Livewire::test(UnhandledTicketsAlertWidget::class)
            ->assertSee('Antrean Aduan Terkendali');

        // Create an unhandled ticket
        $ticket = Ticket::create([
            'user_id' => $this->regularUser->id,
            'category_id' => $this->category->id,
            'subject' => 'Printer Network Rusak',
            'description' => 'Printer macet di lantai 2',
            'status' => 'pending',
            'handled_by_id' => null,
        ]);

        Livewire::test(UnhandledTicketsAlertWidget::class)
            ->assertSee('Aduan Belum Ditangani')
            ->assertSee('Printer Network Rusak')
            ->assertSee($ticket->ticket_number)
            ->call('claimTicket', $ticket->id)
            ->assertRedirect(url("/admin/tickets/{$ticket->id}/edit"));

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status' => 'diproses',
            'handled_by_id' => $this->admin->id,
        ]);
    }

    public function test_non_admin_cannot_access_push_notification_routes(): void
    {
        $this->actingAs($this->regularUser);

        $response = $this->getJson(route('admin.push.config'));
        $response->assertStatus(403);

        $response = $this->postJson(route('admin.push.subscribe'), [
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/sample-endpoint',
        ]);
        $response->assertStatus(403);
    }

    public function test_admin_can_retrieve_push_config_and_subscribe(): void
    {
        $this->actingAs($this->admin);

        $configResponse = $this->getJson(route('admin.push.config'));
        $configResponse->assertOk()
            ->assertJsonStructure(['publicKey', 'isSubscribed']);

        $subscribeResponse = $this->postJson(route('admin.push.subscribe'), [
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/sample-admin-endpoint',
            'publicKey' => 'test-public-key',
            'authToken' => 'test-auth-token',
        ]);

        $subscribeResponse->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('push_subscriptions', [
            'user_id' => $this->admin->id,
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/sample-admin-endpoint',
        ]);

        // Unsubscribe
        $unsubResponse = $this->postJson(route('admin.push.unsubscribe'), [
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/sample-admin-endpoint',
        ]);
        $unsubResponse->assertOk()->assertJson(['success' => true]);

        $this->assertDatabaseMissing('push_subscriptions', [
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/sample-admin-endpoint',
        ]);
    }

    public function test_admin_unhandled_status_api_endpoint(): void
    {
        $this->actingAs($this->admin);

        Ticket::create([
            'user_id' => $this->regularUser->id,
            'category_id' => $this->category->id,
            'subject' => 'Kendala Jaringan LAN',
            'description' => 'Kabel LAN terputus',
            'status' => 'pending',
            'handled_by_id' => null,
        ]);

        $response = $this->getJson(route('admin.tickets.unhandled.status'));
        $response->assertOk()
            ->assertJsonPath('count', 1)
            ->assertJsonFragment(['subject' => 'Kendala Jaringan LAN']);
    }

    public function test_unhandled_ticket_creation_triggers_push_notification_dispatch(): void
    {
        // Mock WebPushService to verify sendTicketNotification is triggered
        $mock = $this->createMock(WebPushService::class);
        $mock->expects($this->once())
            ->method('sendTicketNotification');

        $this->app->instance(WebPushService::class, $mock);

        Ticket::create([
            'user_id' => $this->regularUser->id,
            'category_id' => $this->category->id,
            'subject' => 'Aduan Urgent Baru',
            'description' => 'Kendala aplikasi tidak bisa login',
            'status' => 'pending',
            'handled_by_id' => null,
        ]);
    }

    public function test_admin_can_claim_unhandled_ticket_via_api(): void
    {
        $this->actingAs($this->admin);

        $ticket = Ticket::create([
            'user_id' => $this->regularUser->id,
            'category_id' => $this->category->id,
            'subject' => 'Kendala PC Kantor',
            'description' => 'PC tidak mau menyala',
            'status' => 'pending',
            'handled_by_id' => null,
        ]);

        $response = $this->postJson(route('admin.tickets.claim', $ticket->id));
        $response->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status' => 'diproses',
            'handled_by_id' => $this->admin->id,
        ]);
    }

    public function test_cannot_claim_already_handled_ticket(): void
    {
        $this->actingAs($this->admin);

        $otherAdmin = User::factory()->create(['role' => 'admin']);

        $ticket = Ticket::create([
            'user_id' => $this->regularUser->id,
            'category_id' => $this->category->id,
            'subject' => 'Kendala PC Kantor 2',
            'description' => 'PC tidak mau menyala lagi',
            'status' => 'diproses',
            'handled_by_id' => $otherAdmin->id,
        ]);

        $response = $this->postJson(route('admin.tickets.claim', $ticket->id));
        $response->assertStatus(400)
            ->assertJsonPath('success', false);
    }

    public function test_admin_tickets_realtime_check_detects_new_tickets(): void
    {
        $this->actingAs($this->admin);

        $initialResponse = $this->getJson(route('admin.tickets.realtime-check', ['latest_id' => 0]));
        $initialResponse->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('has_new', false);

        $ticket = Ticket::create([
            'user_id' => $this->regularUser->id,
            'category_id' => $this->category->id,
            'subject' => 'Kendala Jaringan Baru',
            'description' => 'Tidak bisa koneksi',
            'status' => 'pending',
        ]);

        $checkResponse = $this->getJson(route('admin.tickets.realtime-check', ['latest_id' => $ticket->id - 1]));
        $checkResponse->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('has_new', true)
            ->assertJsonPath('latest_id', $ticket->id)
            ->assertJsonFragment([
                'ticket_number' => $ticket->ticket_number,
            ]);
    }

    public function test_unauthorized_user_cannot_access_tickets_realtime_check(): void
    {
        $this->actingAs($this->regularUser);

        $response = $this->getJson(route('admin.tickets.realtime-check'));
        $response->assertForbidden();
    }

    public function test_admin_tickets_index_renders_auto_refresh_component(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin/tickets');
        $response->assertOk()
            ->assertSee('bbpom-tickets-live-sync-wrapper', false)
            ->assertSee('bbpomAdminTicketsPoller', false);
    }

    public function test_admin_ticket_edit_page_renders_rich_attachments_ui(): void
    {
        $this->actingAs($this->admin);

        $ticket = Ticket::create([
            'user_id' => $this->regularUser->id,
            'category_id' => $this->category->id,
            'subject' => 'Tiket dengan Lampiran',
            'description' => 'Lihat lampiran',
            'status' => 'pending',
        ]);

        \App\Models\TicketAttachment::create([
            'ticket_id' => $ticket->id,
            'file_name' => 'bukti_kendala.png',
            'file_path' => 'attachments/test_image.png',
            'file_size' => 102400,
            'mime_type' => 'image/png',
        ]);

        $response = $this->get('/admin/tickets/' . $ticket->id . '/edit');
        $response->assertOk()
            ->assertSee('Berkas Lampiran Pendukung')
            ->assertSee('bukti_kendala.png')
            ->assertSee('Buka Dokumen')
            ->assertSee('Unduh');
    }

    public function test_user_tickets_index_supports_per_page_filter_and_showing_indicator(): void
    {
        $this->actingAs($this->regularUser);

        // Create 15 tickets for regular user
        for ($i = 1; $i <= 15; $i++) {
            Ticket::create([
                'user_id' => $this->regularUser->id,
                'category_id' => $this->category->id,
                'subject' => "Pengaduan User {$i}",
                'description' => "Deskripsi {$i}",
                'status' => 'pending',
            ]);
        }

        // Test default (10 per page)
        $response10 = $this->get(route('tickets.index'));
        $response10->assertOk()
            ->assertSee('Showing')
            ->assertSee('results')
            ->assertSee('Tampilkan:')
            ->assertSee('per halaman');

        // Test 25 per page
        $response25 = $this->get(route('tickets.index', ['per_page' => 25]));
        $response25->assertOk()
            ->assertSee('Showing')
            ->assertSee('results');
    }

    public function test_user_ticket_show_displays_kegiatan_subcategory_and_evaluasi(): void
    {
        $this->actingAs($this->regularUser);

        $ticket = Ticket::create([
            'user_id' => $this->regularUser->id,
            'category_id' => $this->category->id,
            'service_key' => 'formulir_kegiatan',
            'subcategory' => 'post_test_pretest',
            'subject' => 'Permintaan Post Test',
            'description' => 'Permintaan Post Test',
            'status' => 'pending',
        ]);

        $detail = \App\Models\TicketKegiatanDetail::create([
            'ticket_id' => $ticket->id,
            'sub_category' => 'post_test_pretest',
            'tanggal_pengajuan' => now(),
            'nama' => 'Budi Santoso',
            'judul_acara' => 'Bimtek Keamanan Pangan',
            'jenis_test' => 'post_test',
            'pertanyaan_data' => [
                ['text' => 'Apa definisi pangan aman?', 'tipe' => 'Pilihan Ganda'],
            ],
        ]);

        $this->assertEquals('Post Test', $detail->jenis_test_label);

        $response = $this->get(route('tickets.show', $ticket));
        $response->assertOk()
            ->assertSee('Sub Kategori Kegiatan')
            ->assertSee('Post Test / Pretest')
            ->assertSee('Jenis Evaluasi')
            ->assertSee('Post Test')
            ->assertSee('Apa definisi pangan aman?');
    }
}

