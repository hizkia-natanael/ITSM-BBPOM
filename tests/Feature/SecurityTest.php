<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    /**
     * Test Item #5: Security Headers
     */
    public function test_security_headers_are_present_on_web_responses(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
    }

    /**
     * Test Item #2: Protected Attachments - Owner Access
     */
    public function test_ticket_owner_can_access_attachment(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('document.pdf', 120, 'application/pdf');
        $path = $file->store('attachments', 'public');

        $category = Category::first() ?? Category::create(['name' => 'General']);

        $ticket = Ticket::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'subject' => 'Test Subject',
            'description' => 'Test Description',
            'attachment' => $path,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->get(route('tickets.attachment', $ticket));

        $response->assertStatus(200);
    }

    /**
     * Test Item #2: Protected Attachments - Admin Access
     */
    public function test_admin_can_access_ticket_attachment(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        $file = UploadedFile::fake()->create('evidence.png', 80, 'image/png');
        $path = $file->store('attachments', 'public');

        $category = Category::first() ?? Category::create(['name' => 'General']);

        $ticket = Ticket::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'subject' => 'Admin Test Attachment',
            'description' => 'Test Description',
            'attachment' => $path,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->get(route('tickets.attachment', $ticket));

        $response->assertStatus(200);
    }

    /**
     * Test Item #2: Protected Attachments - Unauthorized User Blocked
     */
    public function test_unauthorized_user_cannot_access_ticket_attachment(): void
    {
        Storage::fake('public');

        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $file = UploadedFile::fake()->create('secret.pdf', 50, 'application/pdf');
        $path = $file->store('attachments', 'public');

        $category = Category::first() ?? Category::create(['name' => 'General']);

        $ticket = Ticket::create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'subject' => 'Private Attachment',
            'description' => 'Test Description',
            'attachment' => $path,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($otherUser)->get(route('tickets.attachment', $ticket));

        $response->assertStatus(403);
    }

    /**
     * Test Item #2: Protected Attachments - Guest Redirected
     */
    public function test_guest_is_redirected_to_login_when_accessing_attachment(): void
    {
        $category = Category::first() ?? Category::create(['name' => 'General']);
        $user = User::factory()->create();

        $ticket = Ticket::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'subject' => 'Guest Check',
            'description' => 'Test Description',
            'status' => 'pending',
        ]);

        $response = $this->get(route('tickets.attachment', $ticket));

        $response->assertRedirect('/login');
    }

    /**
     * Test Item #2: Protected Attachments - Subdetail Attachment
     */
    public function test_owner_can_access_zoom_subdetail_attachment(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('zoom_invitation.pdf', 60, 'application/pdf');
        $path = $file->store('attachments/zoom', 'public');

        $category = Category::first() ?? Category::create(['name' => 'Zoom']);

        $ticket = Ticket::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'subject' => 'Permohonan Akun Zoom',
            'description' => 'Zoom deskripsi',
            'status' => 'pending',
        ]);

        $ticket->zoomDetail()->create([
            'sub_category' => 'permohonan_fasilitasi_zoom',
            'tanggal_pengajuan' => now()->toDateString(),
            'nama' => $user->name,
            'tema_kegiatan' => 'Rapat Koordinasi',
            'waktu_mulai' => now(),
            'waktu_selesai' => now()->addHours(2),
            'jumlah_peserta' => '50 Orang',
            'file_lampiran' => $path,
        ]);

        $response = $this->actingAs($user)->get(route('tickets.attachment', ['ticket' => $ticket, 'type' => 'zoom', 'inline' => 1]));

        $response->assertStatus(200);
    }

    /**
     * Test Item #4: Rate Limiting on Ticket Submission
     */
    public function test_ticket_store_has_rate_limiting(): void
    {
        $user = User::factory()->create();
        $category = Category::first() ?? Category::create(['name' => 'General']);

        $ticketData = [
            'category_id' => $category->id,
            'subject' => 'Spam Test',
            'description' => 'Rate limiting test content with enough characters.',
        ];

        // Perform 10 requests allowed by throttle:10,1
        for ($i = 0; $i < 10; $i++) {
            $this->actingAs($user)->post('/tickets', $ticketData);
        }

        // The 11th request must be throttled
        $response = $this->actingAs($user)->post('/tickets', $ticketData);
        $response->assertStatus(429);
    }

    /**
     * Test Multiple Documents: User can upload multiple attachments and view/download each
     */
    public function test_user_can_submit_multiple_attachments_and_access_them(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        $user = User::factory()->create();
        $category = Category::first() ?? Category::create(['name' => 'General']);

        $file1 = UploadedFile::fake()->create('dokumen_1.pdf', 100, 'application/pdf');
        $file2 = UploadedFile::fake()->create('foto_bukti.png', 200, 'image/png');

        $response = $this->actingAs($user)->post('/tickets', [
            'category_id' => $category->id,
            'subject' => 'Tiket Multi Dokumen',
            'description' => 'Deskripsi laporan lengkap dengan dua dokumen pendukung.',
            'attachments' => [$file1, $file2],
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $ticket = Ticket::where('subject', 'Tiket Multi Dokumen')->latest('id')->first();
        $this->assertNotNull($ticket);
        $this->assertCount(2, $ticket->attachments);

        $attachment1 = $ticket->attachments->first();
        $attachment2 = $ticket->attachments->last();

        // 1. Owner can access both attachments
        $res1 = $this->actingAs($user)->get(route('tickets.attachments.item', ['ticket' => $ticket, 'attachment' => $attachment1]));
        $res1->assertStatus(200);

        $res2 = $this->actingAs($user)->get(route('tickets.attachments.item', ['ticket' => $ticket, 'attachment' => $attachment2, 'inline' => 1]));
        $res2->assertStatus(200);

        // 2. Admin can also access
        $admin = User::factory()->create(['role' => 'admin']);
        $resAdmin = $this->actingAs($admin)->get(route('tickets.attachments.item', ['ticket' => $ticket, 'attachment' => $attachment1]));
        $resAdmin->assertStatus(200);

        // 3. Unauthorized user gets 403 Forbidden
        $otherUser = User::factory()->create();
        $resForbidden = $this->actingAs($otherUser)->get(route('tickets.attachments.item', ['ticket' => $ticket, 'attachment' => $attachment1]));
        $resForbidden->assertStatus(403);
    }

    /**
     * Test Multiple Documents: Cross-ticket attachment access returns 404
     */
    public function test_attachment_belonging_to_another_ticket_returns_404(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $category = Category::first() ?? Category::create(['name' => 'General']);

        $ticketA = Ticket::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'subject' => 'Ticket A',
            'description' => 'Deskripsi Ticket A',
            'status' => 'pending',
        ]);

        $ticketB = Ticket::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'subject' => 'Ticket B',
            'description' => 'Deskripsi Ticket B',
            'status' => 'pending',
        ]);

        $file = UploadedFile::fake()->create('sample.pdf', 50, 'application/pdf');
        $path = $file->store('attachments', 'public');

        $attachmentA = $ticketA->attachments()->create([
            'file_path' => $path,
            'file_name' => 'sample.pdf',
            'file_size' => 50000,
            'mime_type' => 'application/pdf',
        ]);

        // Attempting to access Ticket A's attachment using Ticket B's route must return 404
        $response = $this->actingAs($user)->get(route('tickets.attachments.item', ['ticket' => $ticketB, 'attachment' => $attachmentA]));
        $response->assertStatus(404);
    }
}
