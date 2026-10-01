<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HelpdeskTest extends TestCase
{
    public function test_admin_is_redirected_to_admin_panel_after_login(): void
    {
        $admin = User::factory()->create([
            'email' => 'admintest@bbpom.go.id',
            'role' => 'admin',
        ]);

        $response = $this->post('/login', [
            'email' => 'admintest@bbpom.go.id',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin');
    }

    public function test_regular_user_is_redirected_to_dashboard_after_login(): void
    {
        $user = User::factory()->create([
            'email' => 'usertest@bbpom.go.id',
            'role' => 'user',
        ]);

        $response = $this->post('/login', [
            'email' => 'usertest@bbpom.go.id',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
    }

    public function test_regular_user_cannot_access_filament_admin_panel(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertForbidden();
    }

    public function test_admin_can_access_filament_admin_panel(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertSuccessful();
    }

    public function test_user_can_view_dashboard_with_stats(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertSuccessful();
        $response->assertSee('Dashboard Layanan TI');
    }

    public function test_user_can_create_ticket_with_auto_generated_ticket_number(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::firstOrCreate(['name' => 'Jaringan', 'slug' => 'jaringan']);

        $response = $this->actingAs($user)->post('/tickets', [
            'category_id' => $category->id,
            'subject' => 'Kendala switch lab uji',
            'description' => 'Switch ethernet mati dan tidak terdeteksi di ruang pengujian lantai 1.',
        ]);

        $ticket = Ticket::where('subject', 'Kendala switch lab uji')->first();
        $this->assertNotNull($ticket);
        $this->assertEquals('pending', $ticket->status);
        $this->assertStringStartsWith('TIK-BBPOM-', $ticket->ticket_number);

        $response->assertRedirect(route('tickets.show', $ticket));
    }

    public function test_user_can_reply_to_ticket(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::firstOrCreate(['name' => 'Hardware', 'slug' => 'hardware']);

        $ticket = Ticket::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'subject' => 'PC tidak menyala',
            'description' => 'Komputer di ruang rapat tidak bisa hidup saat ditekan tombol power.',
            'status' => 'diproses',
        ]);

        $response = $this->actingAs($user)->post(route('tickets.reply', $ticket), [
            'response_text' => 'Sudah dicoba colok ke stopkontak lain dan masih tetap mati.',
        ]);

        $response->assertRedirect(route('tickets.show', $ticket));
        $this->assertDatabaseHas('ticket_responses', [
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'response_text' => 'Sudah dicoba colok ke stopkontak lain dan masih tetap mati.',
        ]);
    }

    public function test_user_can_submit_dynamic_service_ticket(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::firstOrCreate(['name' => 'Aplikasi', 'slug' => 'aplikasi']);

        $response = $this->actingAs($user)->post('/tickets', [
            'service_key' => 'sipt',
            'subcategory_key' => 'rollback_sampel',
            'category_id' => $category->id,
            'sipt_sample_number' => 'SMP-2026-X99',
            'sipt_commodity' => 'Pangan',
        ]);

        $ticket = Ticket::where('user_id', $user->id)
            ->where('subject', 'like', '%SIPT%')
            ->first();

        $this->assertNotNull($ticket);
        $this->assertStringContainsString('SMP-2026-X99', $ticket->description);
        $this->assertStringContainsString('Pangan', $ticket->description);
        $response->assertRedirect(route('tickets.show', $ticket));
    }

    public function test_user_can_view_ticket_detail(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::firstOrCreate(['name' => 'Aplikasi', 'slug' => 'aplikasi']);

        $ticket = Ticket::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'subject' => 'Aplikasi SIPT Error',
            'description' => 'Tidak bisa input data pengujian.',
            'status' => 'diproses',
        ]);

        $response = $this->actingAs($user)->get(route('tickets.show', $ticket));

        $response->assertSuccessful();
        $response->assertSee($ticket->ticket_number);
        $response->assertSee('Aplikasi SIPT Error');
        $response->assertSee('Sedang Diproses');
        $response->assertSee('Kembali ke Riwayat');
        $response->assertSee('Percakapan Diskusi Tiket');
    }

    public function test_user_cannot_view_other_users_ticket(): void
    {
        $user1 = User::factory()->create(['role' => 'user']);
        $user2 = User::factory()->create(['role' => 'user']);
        $category = Category::firstOrCreate(['name' => 'Jaringan', 'slug' => 'jaringan']);

        $ticket = Ticket::create([
            'user_id' => $user1->id,
            'category_id' => $category->id,
            'subject' => 'Tiket Rahasia User 1',
            'description' => 'Hanya untuk user 1.',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user2)->get(route('tickets.show', $ticket));
        $response->assertForbidden();
    }

    public function test_admin_can_view_any_ticket(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::firstOrCreate(['name' => 'Hardware', 'slug' => 'hardware']);

        $ticket = Ticket::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'subject' => 'Printer Rusak',
            'description' => 'Paper jam terus.',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->get(route('tickets.show', $ticket));
        $response->assertSuccessful();
        $response->assertSee('Printer Rusak');
    }

    public function test_user_can_view_ticket_with_structured_specifications(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::firstOrCreate(['name' => 'Aplikasi', 'slug' => 'aplikasi']);

        $structuredDesc = "**Layanan:** SIPT\n**Sub Kategori:** Reset Password\n**Tanggal Pengajuan:** 01/10/2026 10:00\n**Nama Pemohon:** {$user->name}\n---\n### Rincian Kebutuhan / Formulir:\n* **Username SIPT:** user.bbpom\n* **Request Password Baru:** Bbpom2026!\n* **Catatan Tambahan:** Butuh mendesak untuk uji laboratorium.";

        $ticket = Ticket::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'subject' => '[SIPT] Reset Password',
            'description' => $structuredDesc,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->get(route('tickets.show', $ticket));
        $response->assertSuccessful();
        $response->assertSee('Username SIPT');
        $response->assertSee('user.bbpom');
        $response->assertSee('Request Password Baru');
        $response->assertSee('Bbpom2026!');
        $response->assertSee('Catatan Tambahan');
        $response->assertSee('Butuh mendesak untuk uji laboratorium.');
    }
}

