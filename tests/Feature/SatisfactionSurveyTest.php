<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\TicketSatisfactionSurvey;
use App\Models\User;
use App\Services\Satisfaction\SatisfactionSurveyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SatisfactionSurveyTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $otherUser;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'user']);
        $this->otherUser = User::factory()->create(['role' => 'user']);
        $this->category = Category::create(['name' => 'Aplikasi & Sistem', 'slug' => 'aplikasi']);
    }

    public function test_ticket_owner_can_submit_satisfaction_survey(): void
    {
        $ticket = Ticket::create([
            'ticket_number' => 'TIK-TEST-001',
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'subject' => 'Kendala Jaringan',
            'description' => 'Tidak bisa akses internet',
            'status' => 'selesai',
        ]);

        $response = $this->actingAs($this->user)->post(route('tickets.rating.store', $ticket), [
            'speed_rating' => 5,
            'quality_rating' => 4,
            'communication_rating' => 5,
            'system_rating' => 4,
            'feedback' => 'Pelayanan cepat dan solutif!',
        ]);

        $response->assertRedirect(route('tickets.show', $ticket));
        $this->assertDatabaseHas('ticket_satisfaction_surveys', [
            'ticket_id' => $ticket->id,
            'user_id' => $this->user->id,
            'speed_rating' => 5,
            'quality_rating' => 4,
            'communication_rating' => 5,
            'system_rating' => 4,
            'average_rating' => 4.5,
            'feedback' => 'Pelayanan cepat dan solutif!',
        ]);
        $this->assertTrue($ticket->fresh()->isRated());
    }

    public function test_other_user_cannot_rate_ticket(): void
    {
        $ticket = Ticket::create([
            'ticket_number' => 'TIK-TEST-002',
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'subject' => 'Kendala SIPT',
            'description' => 'Gagal login sistem',
            'status' => 'selesai',
        ]);

        $response = $this->actingAs($this->otherUser)->post(route('tickets.rating.store', $ticket), [
            'speed_rating' => 5,
            'quality_rating' => 5,
            'communication_rating' => 5,
            'system_rating' => 5,
        ]);

        $response->assertSessionHasErrors(['ticket']);
        $this->assertDatabaseMissing('ticket_satisfaction_surveys', ['ticket_id' => $ticket->id]);
    }

    public function test_user_cannot_rate_unresolved_ticket(): void
    {
        $ticket = Ticket::create([
            'ticket_number' => 'TIK-TEST-003',
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'subject' => 'Kendala Email',
            'description' => 'Gagal kirim email',
            'status' => 'diproses',
        ]);

        $response = $this->actingAs($this->user)->post(route('tickets.rating.store', $ticket), [
            'speed_rating' => 5,
            'quality_rating' => 5,
            'communication_rating' => 5,
            'system_rating' => 5,
        ]);

        $response->assertSessionHasErrors(['ticket']);
        $this->assertDatabaseMissing('ticket_satisfaction_surveys', ['ticket_id' => $ticket->id]);
    }

    public function test_user_cannot_submit_duplicate_survey(): void
    {
        $ticket = Ticket::create([
            'ticket_number' => 'TIK-TEST-004',
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'subject' => 'Sertifikat Elektronik',
            'description' => 'Perpanjangan BSrE',
            'status' => 'selesai',
        ]);

        TicketSatisfactionSurvey::create([
            'ticket_id' => $ticket->id,
            'user_id' => $this->user->id,
            'speed_rating' => 5,
            'quality_rating' => 5,
            'communication_rating' => 5,
            'system_rating' => 5,
            'average_rating' => 5.0,
        ]);

        $response = $this->actingAs($this->user)->post(route('tickets.rating.store', $ticket), [
            'speed_rating' => 4,
            'quality_rating' => 4,
            'communication_rating' => 4,
            'system_rating' => 4,
        ]);

        $response->assertSessionHasErrors(['ticket']);
        $this->assertCount(1, TicketSatisfactionSurvey::where('ticket_id', $ticket->id)->get());
    }

    public function test_satisfaction_service_computes_accurate_aggregate_and_login_page_renders_it(): void
    {
        $service = app(SatisfactionSurveyService::class);
        $initial = $service->getSatisfactionIndex();
        $this->assertEquals(0, $initial['total_surveys']);
        $this->assertEquals('5.0 / 5', $initial['formatted']);

        // Insert two surveys with known scores
        $t1 = Ticket::create(['ticket_number' => 'T1', 'user_id' => $this->user->id, 'category_id' => $this->category->id, 'subject' => 'S1', 'description' => 'D1', 'status' => 'selesai']);
        $t2 = Ticket::create(['ticket_number' => 'T2', 'user_id' => $this->user->id, 'category_id' => $this->category->id, 'subject' => 'S2', 'description' => 'D2', 'status' => 'selesai']);

        TicketSatisfactionSurvey::create([
            'ticket_id' => $t1->id, 'user_id' => $this->user->id,
            'speed_rating' => 5, 'quality_rating' => 5, 'communication_rating' => 5, 'system_rating' => 5, 'average_rating' => 5.0,
        ]);
        TicketSatisfactionSurvey::create([
            'ticket_id' => $t2->id, 'user_id' => $this->user->id,
            'speed_rating' => 4, 'quality_rating' => 4, 'communication_rating' => 4, 'system_rating' => 4, 'average_rating' => 4.0,
        ]);

        $result = $service->getSatisfactionIndex();
        $this->assertEquals(2, $result['total_surveys']);
        $this->assertEquals(4.5, $result['score']);
        $this->assertEquals('4.5 / 5', $result['formatted']);

        // Test login page rendering
        $loginResponse = $this->get(route('login'));
        $loginResponse->assertOk();
        $loginResponse->assertSee('4.5 / 5');
        $loginResponse->assertSee('2 ulasan');
    }

    public function test_ticket_show_page_renders_survey_components(): void
    {
        $ticket = Ticket::create([
            'ticket_number' => 'TIK-UI-001',
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'subject' => 'Tes UI Survei',
            'description' => 'Pengujian komponen survei',
            'status' => 'selesai',
        ]);

        // When unrated, prompt banner is displayed
        $response = $this->actingAs($this->user)->get(route('tickets.show', $ticket));
        $response->assertOk();
        $response->assertSee('Bagaimana Pengalaman Anda dengan Layanan TI Ini?');
        $response->assertSee('Beri Penilaian (1-5 Bintang)');

        // Submit rating
        TicketSatisfactionSurvey::create([
            'ticket_id' => $ticket->id,
            'user_id' => $this->user->id,
            'speed_rating' => 5,
            'quality_rating' => 4,
            'communication_rating' => 5,
            'system_rating' => 4,
            'average_rating' => 4.5,
            'feedback' => 'Sangat responsif dan memuaskan',
        ]);

        // When rated, completed card is displayed
        $ratedResponse = $this->actingAs($this->user)->get(route('tickets.show', $ticket));
        $ratedResponse->assertOk();
        $ratedResponse->assertSee('Ulasan &amp; Penilaian Layanan', false);
        $ratedResponse->assertSee('4.5 / 5');
        $ratedResponse->assertSee('Sangat responsif dan memuaskan');
    }
}
