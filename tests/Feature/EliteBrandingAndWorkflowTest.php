<?php

namespace Tests\Feature;

use App\Filament\Resources\Tickets\Pages\EditTicket;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EliteBrandingAndWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_branding_elite_is_rendered_across_public_and_auth_surfaces(): void
    {
        $this->assertStringContainsString('ELITE', config('app.name'));

        $this->get('/login')
            ->assertOk()
            ->assertSee('ELITE')
            ->assertSee('E-Layanan IT Terintegrasi');

        $this->get('/')
            ->assertOk()
            ->assertSee('ELITE')
            ->assertSee('E-Layanan IT Terintegrasi');
    }

    public function test_multi_admin_handover_per_status_records_distinct_actors_and_timestamps(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $admin1 = User::factory()->create(['name' => 'Admin 1 - Penerima', 'role' => 'admin']);
        $admin2 = User::factory()->create(['name' => 'Admin 2 - Teknisi Lapangan', 'role' => 'admin']);
        $admin3 = User::factory()->create(['name' => 'Admin 3 - Verifikator Selesai', 'role' => 'admin']);
        $category = Category::firstOrCreate(['slug' => 'jaringan'], ['name' => 'Jaringan']);

        $ticket = Ticket::create([
            'ticket_number' => 'TIK-ELITE-001',
            'user_id' => $user->id,
            'category_id' => $category->id,
            'subject' => 'Kendala Jaringan Multi Admin',
            'description' => 'Testing alur multi-admin',
            'status' => 'pending',
        ]);

        // Tahap 1: Admin 1 menerima aduan
        Livewire::actingAs($admin1)
            ->test(EditTicket::class, ['record' => $ticket->getKey()])
            ->fillForm(['status' => 'diterima'])
            ->call('save')
            ->assertHasNoFormErrors();

        $ticket->refresh();
        $this->assertEquals('diterima', $ticket->status);
        $this->assertEquals($admin1->id, $ticket->accepted_by_id);
        $this->assertNotNull($ticket->accepted_at);

        // Tahap 2: Admin 2 memproses aduan
        Livewire::actingAs($admin2)
            ->test(EditTicket::class, ['record' => $ticket->getKey()])
            ->fillForm(['status' => 'diproses'])
            ->call('save')
            ->assertHasNoFormErrors();

        $ticket->refresh();
        $this->assertEquals('diproses', $ticket->status);
        $this->assertEquals($admin1->id, $ticket->accepted_by_id);
        $this->assertEquals($admin2->id, $ticket->processed_by_id);
        $this->assertNotNull($ticket->processed_at);

        // Tahap 3: Admin 3 menuntaskan aduan
        Livewire::actingAs($admin3)
            ->test(EditTicket::class, ['record' => $ticket->getKey()])
            ->fillForm(['status' => 'selesai'])
            ->call('save')
            ->assertHasNoFormErrors();

        $ticket->refresh();
        $this->assertEquals('selesai', $ticket->status);
        $this->assertEquals($admin3->id, $ticket->resolved_by_id);
        $this->assertNotNull($ticket->resolved_at);

        // Verifikasi User Live Tracker menampilkan seluruh petugas dan jam
        $this->actingAs($user)->get(route('tickets.show', $ticket))
            ->assertOk()
            ->assertSee('Admin 1 - Penerima')
            ->assertSee('Admin 2 - Teknisi Lapangan')
            ->assertSee('Admin 3 - Verifikator Selesai')
            ->assertSee('ELITE Live Tracking Lifecycle')
            ->assertSee('Tuntas Selesai');
    }
}
