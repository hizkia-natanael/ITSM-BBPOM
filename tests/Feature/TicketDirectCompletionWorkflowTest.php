<?php

namespace Tests\Feature;

use App\Filament\Resources\Tickets\Pages\EditTicket;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TicketDirectCompletionWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_direct_completion_from_diterima_syncs_stage_3_timestamp_with_stage_4(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['name' => 'Admin Utama', 'role' => 'admin']);
        $category = Category::firstOrCreate(['slug' => 'jaringan'], ['name' => 'Jaringan']);

        $ticket = Ticket::create([
            'ticket_number' => 'TIK-ELITE-DIR-1',
            'user_id' => $user->id,
            'category_id' => $category->id,
            'subject' => 'Aduan Langsung Selesai',
            'description' => 'Aduan langsung diselesaikan dari admin diterima',
            'status' => 'pending',
        ]);

        // Tahap 1 -> Diterima dengan stempel waktu lampau
        $ticket->created_at = now()->subHours(2);
        $ticket->status = 'diterima';
        $ticket->accepted_at = now()->subHour();
        $ticket->save();

        $ticket->refresh();
        $this->assertEquals('diterima', $ticket->status);
        $this->assertNotNull($ticket->accepted_at);
        $this->assertNull($ticket->processed_at);

        // Tahap 2 -> Langsung Selesai (tanpa diproses)
        Livewire::actingAs($admin)
            ->test(EditTicket::class, ['record' => $ticket->getKey()])
            ->fillForm(['status' => 'selesai'])
            ->call('save')
            ->assertHasNoFormErrors();

        $ticket->refresh();
        $this->assertEquals('selesai', $ticket->status);
        $this->assertNotNull($ticket->resolved_at);
        $this->assertNotNull($ticket->processed_at);
        $this->assertEquals(
            $ticket->resolved_at->format('Y-m-d H:i:s'),
            $ticket->processed_at->format('Y-m-d H:i:s')
        );

        $expectedTime = $ticket->resolved_at->copy()->timezone('Asia/Jakarta')->format('d/m/Y H:i') . ' WIB';

        $stepperHtml = view('components.ticket-status-stepper', ['ticket' => $ticket])->render();
        $this->assertEquals(2, substr_count($stepperHtml, $expectedTime));
        $this->assertStringNotContainsString('--:-- WIB', $stepperHtml);

        $response = $this->actingAs($user)->get(route('tickets.show', $ticket));
        $response->assertOk();
        $response->assertSee('Tahap 3');
        $response->assertSee('Tahap 4');
        $response->assertSee($expectedTime);
        $response->assertDontSee('--:-- WIB');
    }

    public function test_view_stepper_mirrors_stage_4_timestamp_when_processed_at_is_null_for_completed_ticket(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['name' => 'Admin Cepat', 'role' => 'admin']);
        $category = Category::firstOrCreate(['slug' => 'aplikasi'], ['name' => 'Aplikasi']);
        $resolvedDate = Carbon::create(2026, 11, 18, 14, 30, 0, 'Asia/Jakarta');

        $ticket = Ticket::create([
            'ticket_number' => 'TIK-ELITE-DIR-2',
            'user_id' => $user->id,
            'category_id' => $category->id,
            'subject' => 'Tiket Selesai Legacy Tanpa Processed At',
            'description' => 'Tiket langsung selesai di database tanpa processed_at',
            'status' => 'selesai',
            'accepted_by_id' => $admin->id,
            'accepted_at' => $resolvedDate->copy()->subMinutes(10),
            'processed_by_id' => null,
            'processed_at' => null,
            'resolved_by_id' => $admin->id,
            'resolved_at' => $resolvedDate,
        ]);

        $expectedTime = $resolvedDate->format('d/m/Y H:i') . ' WIB';

        $stepperHtml = view('components.ticket-status-stepper', ['ticket' => $ticket])->render();
        $this->assertEquals(2, substr_count($stepperHtml, $expectedTime));
        $this->assertStringNotContainsString('--:-- WIB', $stepperHtml);

        $response = $this->actingAs($user)->get(route('tickets.show', $ticket));
        $response->assertOk();
        $response->assertSee($expectedTime);
        $response->assertDontSee('--:-- WIB');
    }
}
