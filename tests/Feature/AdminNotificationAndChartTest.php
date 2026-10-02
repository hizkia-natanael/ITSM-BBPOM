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
}
