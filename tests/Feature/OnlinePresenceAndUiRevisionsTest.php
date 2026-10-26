<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class OnlinePresenceAndUiRevisionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_online_presence_endpoint_returns_realtime_visitor_count(): void
    {
        Cache::forget('bbpom_online_visitors_registry');

        $response = $this->getJson(route('presence.online'));

        $response->assertOk()
            ->assertJsonStructure([
                'online_count',
                'authenticated_count',
                'guest_count',
                'timestamp',
            ]);

        $this->assertGreaterThanOrEqual(1, $response->json('online_count'));
    }

    public function test_login_page_shows_inovasi_tata_kelola_layanan_ti_and_whatsapp_contact_center_and_online_counter(): void
    {
        $response = $this->get('/login');

        $response->assertOk()
            ->assertSee('Inovasi Tata Kelola Layanan TI')
            ->assertSee('Contact Center : Windi (083866277489)')
            ->assertSee('ri-whatsapp-fill', false)
            ->assertSee('Pengunjung Online');
    }

    public function test_user_dashboard_shows_navbar_online_counter_and_home_whatsapp_contact_center_without_footer(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk()
            ->assertSee('Pengunjung Online')
            ->assertSee('Aktif secara realtime')
            ->assertSee('Contact Center : Windi (083866277489)')
            ->assertSee('ri-whatsapp-fill', false)
            ->assertDontSee('<footer', false);
    }

    public function test_admin_ticket_edit_page_shows_tim_layanan_it_on_status_penanganan_card(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::firstOrCreate(
            ['slug' => 'jaringan-internet'],
            ['name' => 'Jaringan & Konektivitas Internet']
        );

        $ticket = Ticket::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'subject' => 'Uji Card Status Penanganan',
            'description' => 'Deskripsi tiket pengujian.',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->get("/admin/tickets/{$ticket->id}/edit");

        $response->assertOk()
            ->assertSee('Status Penanganan Layanan')
            ->assertSee('Kelola progres penanganan tiket ITSM oleh tim layanan IT BBPOM Semarang.')
            ->assertDontSee('Kelola progres penanganan tiket ITSM oleh tim teknisi IT BBPOM Semarang.');
    }
}
