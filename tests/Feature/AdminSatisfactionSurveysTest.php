<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\TicketSatisfactionSurvey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSatisfactionSurveysTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $user;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->user = User::factory()->create(['role' => 'user']);
        $this->category = Category::create(['name' => 'Aplikasi & Sistem', 'slug' => 'aplikasi']);
    }

    public function test_admin_dashboard_renders_satisfaction_surveys_widget_and_navbar(): void
    {
        $ticket = Ticket::create([
            'ticket_number' => 'TIK-SURV-100',
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'subject' => 'Kendala Portal',
            'description' => 'Aplikasi error',
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
            'feedback' => 'Bagus sekali layanannya',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin');
        $response->assertOk();
        $response->assertSee('Survei Kepuasan');
        $response->assertSee('Evaluasi &amp; Ulasan Kepuasan Layanan TI', false);
        $response->assertSee('Bagus sekali layanannya');
        $response->assertSee('5.0');
    }

    public function test_admin_can_access_satisfaction_surveys_resource_table(): void
    {
        $ticket = Ticket::create([
            'ticket_number' => 'TIK-SURV-200',
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'subject' => 'Permohonan Akun',
            'description' => 'Permohonan akun baru',
            'status' => 'selesai',
        ]);

        TicketSatisfactionSurvey::create([
            'ticket_id' => $ticket->id,
            'user_id' => $this->user->id,
            'speed_rating' => 4,
            'quality_rating' => 4,
            'communication_rating' => 4,
            'system_rating' => 4,
            'average_rating' => 4.0,
            'feedback' => 'Cepat selesai mantap',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/satisfaction-surveys');
        $response->assertOk();
        $response->assertSee('Survei Kepuasan &amp; Ulasan Layanan', false);
        $response->assertSee('TIK-SURV-200');
        $response->assertSee('Cepat selesai mantap');
    }

    public function test_regular_user_cannot_access_satisfaction_surveys_admin_resource(): void
    {
        $response = $this->actingAs($this->user)->get('/admin/satisfaction-surveys');
        $response->assertForbidden();
    }
}
