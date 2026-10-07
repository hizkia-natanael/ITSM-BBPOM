<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\TicketResponse;
use App\Models\TicketResponseAttachment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TicketChatFileSharingTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $admin;
    protected Ticket $ticket;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake(config('filesystems.attachment_disk', 'local'));

        $this->user = User::factory()->create(['role' => 'user']);
        $this->admin = User::factory()->create(['role' => 'admin']);

        $category = Category::firstOrCreate(['slug' => 'general'], ['name' => 'General']);

        $this->ticket = Ticket::create([
            'ticket_number' => 'TICK-TEST-001',
            'user_id' => $this->user->id,
            'category_id' => $category->id,
            'subject' => 'Permintaan Infografis Kampanye Obat',
            'description' => 'Mohon dibuatkan file infografis',
            'status' => 'pending',
            'priority' => 'medium',
        ]);
    }

    public function test_user_can_send_text_reply_without_attachment(): void
    {
        $response = $this->actingAs($this->user)->post(route('tickets.reply', $this->ticket), [
            'response_text' => 'Terima kasih, mohon segera diproses.',
        ]);

        $response->assertRedirect(route('tickets.show', $this->ticket));
        $this->assertDatabaseHas('ticket_responses', [
            'ticket_id' => $this->ticket->id,
            'user_id' => $this->user->id,
            'response_text' => 'Terima kasih, mohon segera diproses.',
        ]);
        $this->assertDatabaseCount('ticket_response_attachments', 0);
    }

    public function test_user_can_send_reply_with_original_size_file(): void
    {
        $file = UploadedFile::fake()->create('desain_asli.png', 500, 'image/png');

        $response = $this->actingAs($this->user)->post(route('tickets.reply', $this->ticket), [
            'response_text' => 'Ini file master infografis ukuran penuh.',
            'attachment' => $file,
            'compression_mode' => 'original',
        ]);

        $response->assertRedirect(route('tickets.show', $this->ticket));

        $ticketResponse = TicketResponse::where('ticket_id', $this->ticket->id)->latest('id')->first();
        $this->assertNotNull($ticketResponse);
        $this->assertEquals('Ini file master infografis ukuran penuh.', $ticketResponse->response_text);

        $attachment = $ticketResponse->attachments()->first();
        $this->assertNotNull($attachment);
        $this->assertEquals('desain_asli.png', $attachment->file_name);
        $this->assertEquals('original', $attachment->compression_mode);
        $this->assertFalse($attachment->is_compressed);
        $this->assertEquals(0, $attachment->saved_bytes);

        // Verify storage file exists on private disk
        $disk = config('filesystems.attachment_disk', 'local');
        $this->assertTrue(Storage::disk($disk)->exists($attachment->file_path));
    }

    public function test_user_can_send_reply_with_compressed_image_file(): void
    {
        // Create a large fake image (e.g. 2500x2500 px)
        $file = UploadedFile::fake()->image('infografis_resolusi_tinggi.jpg', 2500, 2500);

        $response = $this->actingAs($this->user)->post(route('tickets.reply', $this->ticket), [
            'response_text' => 'Ini draf infografis terkompresi.',
            'attachment' => $file,
            'compression_mode' => 'compressed',
        ]);

        $response->assertRedirect(route('tickets.show', $this->ticket));

        $ticketResponse = TicketResponse::where('ticket_id', $this->ticket->id)->latest('id')->first();
        $this->assertNotNull($ticketResponse);

        $attachment = $ticketResponse->attachments()->first();
        $this->assertNotNull($attachment);
        $this->assertEquals('infografis_resolusi_tinggi.jpg', $attachment->file_name);
        $this->assertEquals('compressed', $attachment->compression_mode);

        $disk = config('filesystems.attachment_disk', 'local');
        $this->assertTrue(Storage::disk($disk)->exists($attachment->file_path));
    }

    public function test_user_can_send_file_only_without_typing_text(): void
    {
        $file = UploadedFile::fake()->create('panduan_sop.pdf', 300, 'application/pdf');

        $response = $this->actingAs($this->user)->post(route('tickets.reply', $this->ticket), [
            'response_text' => '',
            'attachment' => $file,
            'compression_mode' => 'original',
        ]);

        $response->assertRedirect(route('tickets.show', $this->ticket));

        $ticketResponse = TicketResponse::where('ticket_id', $this->ticket->id)->latest('id')->first();
        $this->assertNotNull($ticketResponse);
        $this->assertStringContainsString('panduan_sop.pdf', $ticketResponse->response_text);

        $this->assertDatabaseHas('ticket_response_attachments', [
            'ticket_response_id' => $ticketResponse->id,
            'file_name' => 'panduan_sop.pdf',
        ]);
    }

    public function test_admin_can_reply_with_attachment_and_auto_assigns_status(): void
    {
        $file = UploadedFile::fake()->create('revisi_infografis_final.zip', 800, 'application/zip');

        $response = $this->actingAs($this->admin)->post(route('tickets.reply', $this->ticket), [
            'response_text' => 'Berikut revisi berkas infografis dari tim IT.',
            'attachment' => $file,
            'compression_mode' => 'original',
        ]);

        $response->assertRedirect(route('tickets.show', $this->ticket));

        $this->ticket->refresh();
        $this->assertEquals('diproses', $this->ticket->status);
        $this->assertEquals($this->admin->id, $this->ticket->handled_by_id);

        $ticketResponse = TicketResponse::where('ticket_id', $this->ticket->id)->latest('id')->first();
        $this->assertNotNull($ticketResponse);
        $this->assertEquals($this->admin->id, $ticketResponse->user_id);
        $this->assertCount(1, $ticketResponse->attachments);
    }

    public function test_ticket_owner_and_admin_can_download_response_attachment(): void
    {
        $file = UploadedFile::fake()->create('laporan.pdf', 100, 'application/pdf');

        $this->actingAs($this->user)->post(route('tickets.reply', $this->ticket), [
            'response_text' => 'Lampiran laporan',
            'attachment' => $file,
            'compression_mode' => 'original',
        ]);

        $attachment = TicketResponseAttachment::latest('id')->first();
        $this->assertNotNull($attachment);

        // Ticket owner can download
        $downloadUrl = route('tickets.response-attachments.download', [
            'ticket' => $this->ticket->id,
            'attachment' => $attachment->id,
        ]);

        $ownerResponse = $this->actingAs($this->user)->get($downloadUrl);
        $ownerResponse->assertOk();

        // Admin can download
        $adminResponse = $this->actingAs($this->admin)->get($downloadUrl);
        $adminResponse->assertOk();

        // Inline preview works
        $previewResponse = $this->actingAs($this->user)->get($downloadUrl . '?inline=1');
        $previewResponse->assertOk();
    }

    public function test_unauthorized_user_cannot_download_response_attachment(): void
    {
        $otherUser = User::factory()->create(['role' => 'user']);
        $file = UploadedFile::fake()->create('rahasia.pdf', 100, 'application/pdf');

        $this->actingAs($this->user)->post(route('tickets.reply', $this->ticket), [
            'response_text' => 'File rahasia',
            'attachment' => $file,
            'compression_mode' => 'original',
        ]);

        $attachment = TicketResponseAttachment::latest('id')->first();

        $downloadUrl = route('tickets.response-attachments.download', [
            'ticket' => $this->ticket->id,
            'attachment' => $attachment->id,
        ]);

        // Other regular user is rejected with 403
        $forbiddenResponse = $this->actingAs($otherUser)->get($downloadUrl);
        $forbiddenResponse->assertForbidden();

        // Guest is redirected to login
        auth()->logout();
        $guestResponse = $this->get($downloadUrl);
        $guestResponse->assertRedirect(route('login'));
    }

    public function test_response_attachment_view_renders_in_ticket_show(): void
    {
        $file = UploadedFile::fake()->image('infografis_sample.png', 400, 300);

        $this->actingAs($this->user)->post(route('tickets.reply', $this->ticket), [
            'response_text' => 'Silakan tinjau desain ini.',
            'attachment' => $file,
            'compression_mode' => 'compressed',
        ]);

        $showResponse = $this->actingAs($this->user)->get(route('tickets.show', $this->ticket));
        $showResponse->assertOk();
        $showResponse->assertSee('infografis_sample.png');
        $showResponse->assertSee('Silakan tinjau desain ini.');
    }

    public function test_ajax_reply_with_attachment_returns_json_and_stores_file(): void
    {
        $file = UploadedFile::fake()->create('dokumen_teknis.pdf', 250, 'application/pdf');

        $response = $this->actingAs($this->user)->postJson(route('tickets.reply', $this->ticket), [
            'response_text' => 'Pesan via AJAX fetch',
            'attachment' => $file,
            'compression_mode' => 'original',
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('ticket_responses', [
            'ticket_id' => $this->ticket->id,
            'response_text' => 'Pesan via AJAX fetch',
        ]);

        $this->assertDatabaseHas('ticket_response_attachments', [
            'file_name' => 'dokumen_teknis.pdf',
        ]);
    }

    public function test_admin_edit_ticket_page_renders_chat_component(): void
    {
        $response = $this->actingAs($this->admin)->get("/admin/tickets/{$this->ticket->id}/edit");
        $response->assertOk();
        $content = $response->getContent();
        $this->assertStringContainsString('bbpom-conversation-card', $content);
        $this->assertStringContainsString('remixicon', $content);
        $this->assertStringContainsString('bbpomTicketConversation', $content);
        $this->assertStringContainsString('triggerFilePicker', $content);
        $this->assertStringContainsString('ri-send-plane-fill', $content);
    }

    public function test_admin_can_send_ajax_reply_with_attachment(): void
    {
        $file = UploadedFile::fake()->image('panduan_sistem.jpg', 2500, 2500);

        $response = $this->actingAs($this->admin)->postJson(route('tickets.reply', $this->ticket), [
            'response_text' => 'Berikut panduan dari tim teknis.',
            'attachment' => $file,
            'compression_mode' => 'compressed',
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
        ]);

        $this->ticket->refresh();
        $this->assertSame((int) $this->admin->id, (int) $this->ticket->handled_by_id);
        $this->assertSame('diproses', $this->ticket->status);

        $this->assertDatabaseHas('ticket_response_attachments', [
            'ticket_response_id' => $this->ticket->responses()->latest()->first()->id,
            'is_compressed' => true,
        ]);
    }

    public function test_attachment_model_has_robust_hex_colors_for_file_types(): void
    {
        $pdfAtt = new TicketResponseAttachment(['file_name' => 'dokumen.pdf']);
        $this->assertSame('#e11d48', $pdfAtt->document_hex_color);
        $this->assertSame('#fff1f2', $pdfAtt->document_card_bg_hex);

        $docAtt = new TicketResponseAttachment(['file_name' => 'surat.docx']);
        $this->assertSame('#2563eb', $docAtt->document_hex_color);
        $this->assertSame('#eff6ff', $docAtt->document_card_bg_hex);

        $excelAtt = new TicketResponseAttachment(['file_name' => 'laporan.xlsx']);
        $this->assertSame('#0d9488', $excelAtt->document_hex_color);
        $this->assertSame('#f0fdfa', $excelAtt->document_card_bg_hex);
        $this->assertSame('#99f6e4', $excelAtt->document_card_border_hex);
        $this->assertSame('ri-file-excel-2-line', $excelAtt->pure_icon_class);

        $zipAtt = new TicketResponseAttachment(['file_name' => 'arsip.zip']);
        $this->assertSame('#d97706', $zipAtt->document_hex_color);
        $this->assertSame('ri-file-zip-line', $zipAtt->pure_icon_class);

        $imgAtt = new TicketResponseAttachment(['file_name' => 'foto.png']);
        $this->assertSame('#059669', $imgAtt->document_hex_color);
        $this->assertSame('ri-image-2-line', $imgAtt->pure_icon_class);
    }

    public function test_user_can_reply_to_specific_message_quote(): void
    {
        // Admin sends first message
        $adminResponse = TicketResponse::create([
            'ticket_id' => $this->ticket->id,
            'user_id' => $this->admin->id,
            'response_text' => 'Mohon kirimkan format resolusi gambar yang diinginkan.',
            'read_at' => null,
        ]);

        // User quotes admin response
        $ajaxResponse = $this->actingAs($this->user)->postJson(route('tickets.reply', $this->ticket), [
            'response_text' => 'Kami memerlukan format PNG transparan 1080x1080.',
            'reply_to_id' => $adminResponse->id,
        ]);

        $ajaxResponse->assertOk();
        $ajaxResponse->assertJsonStructure([
            'success',
            'response' => [
                'id',
                'response_text',
                'reply_to' => [
                    'id',
                    'user_name',
                    'snippet',
                ],
            ],
        ]);

        $this->assertDatabaseHas('ticket_responses', [
            'ticket_id' => $this->ticket->id,
            'user_id' => $this->user->id,
            'response_text' => 'Kami memerlukan format PNG transparan 1080x1080.',
            'reply_to_id' => $adminResponse->id,
        ]);

        $userResponse = TicketResponse::where('ticket_id', $this->ticket->id)
            ->where('user_id', $this->user->id)
            ->first();

        $this->assertNotNull($userResponse->replyTo);
        $this->assertSame($adminResponse->id, $userResponse->replyTo->id);
        $this->assertTrue($adminResponse->replies->contains($userResponse));
    }

    public function test_chat_sync_returns_delta_messages_and_marks_counterpart_as_read(): void
    {
        // User creates message 1
        $userMsg = TicketResponse::create([
            'ticket_id' => $this->ticket->id,
            'user_id' => $this->user->id,
            'response_text' => 'Pesan pertama dari pemohon.',
            'read_at' => null,
        ]);

        // Admin syncs all messages after_id=0
        $syncAsAdmin = $this->actingAs($this->admin)->getJson(route('tickets.chat-sync', [
            'ticket' => $this->ticket,
            'after_id' => 0,
        ]));

        $syncAsAdmin->assertOk();
        $syncAsAdmin->assertJsonCount(1, 'responses');
        $syncAsAdmin->assertJsonPath('responses.0.id', $userMsg->id);
        $syncAsAdmin->assertJsonPath('responses.0.is_me', false);

        // Counterpart message is marked as read
        $this->assertNotNull($userMsg->fresh()->read_at);
        $this->assertTrue($userMsg->fresh()->is_read);

        // Admin replies with message 2
        $adminMsg = TicketResponse::create([
            'ticket_id' => $this->ticket->id,
            'user_id' => $this->admin->id,
            'response_text' => 'Tanggapan dari admin TIK.',
            'read_at' => null,
        ]);

        // User syncs only messages after_id=userMsg->id
        $syncAsUser = $this->actingAs($this->user)->getJson(route('tickets.chat-sync', [
            'ticket' => $this->ticket,
            'after_id' => $userMsg->id,
        ]));

        $syncAsUser->assertOk();
        $syncAsUser->assertJsonCount(1, 'responses');
        $syncAsUser->assertJsonPath('responses.0.id', $adminMsg->id);
        $syncAsUser->assertJsonPath('responses.0.is_me', false);
        $syncAsUser->assertJsonPath('ticket_status', $this->ticket->status);
        $syncAsUser->assertJsonPath('has_admin_response', true);
        $syncAsUser->assertJsonStructure([
            'success',
            'ticket_status',
            'has_admin_response',
            'updated_at_formatted',
            'responses',
            'latest_id',
        ]);

        // Admin message is marked as read by user
        $this->assertNotNull($adminMsg->fresh()->read_at);
        $this->assertTrue($adminMsg->fresh()->is_read);
    }

    public function test_unauthorized_user_cannot_sync_other_users_ticket_chat(): void
    {
        $otherUser = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($otherUser)->getJson(route('tickets.chat-sync', [
            'ticket' => $this->ticket,
            'after_id' => 0,
        ]));

        $response->assertForbidden();
    }

    public function test_single_file_above_one_mb_is_compressed_into_zip(): void
    {
        // 1.5 MB text file (highly compressible)
        $content = str_repeat('BBPOM Semarang Layanan TIK Helpdesk Sistem ', 38000); // ~1.6MB
        $file = UploadedFile::fake()->createWithContent('laporan_tahunan.txt', $content);

        $response = $this->actingAs($this->user)->post(route('tickets.reply', $this->ticket), [
            'response_text' => 'File laporan berukuran di atas 1MB.',
            'attachment' => $file,
            'compression_mode' => 'compressed',
        ]);

        $response->assertRedirect(route('tickets.show', $this->ticket));

        $ticketResponse = TicketResponse::where('ticket_id', $this->ticket->id)->latest('id')->first();
        $this->assertNotNull($ticketResponse);

        $attachment = $ticketResponse->attachments()->first();
        $this->assertNotNull($attachment);
        $this->assertEquals('laporan_tahunan.txt.zip', $attachment->file_name);
        $this->assertEquals('application/zip', $attachment->mime_type);
        $this->assertTrue($attachment->is_compressed);
        $this->assertGreaterThan(0, $attachment->saved_bytes);
    }

    public function test_single_file_below_or_equal_to_one_mb_is_not_zipped(): void
    {
        // 500 KB document (below 1 MB threshold)
        $file = UploadedFile::fake()->create('formulir_singkat.txt', 500, 'text/plain');

        $response = $this->actingAs($this->user)->post(route('tickets.reply', $this->ticket), [
            'response_text' => 'File dokumen di bawah 1MB.',
            'attachment' => $file,
            'compression_mode' => 'compressed',
        ]);

        $response->assertRedirect(route('tickets.show', $this->ticket));

        $ticketResponse = TicketResponse::where('ticket_id', $this->ticket->id)->latest('id')->first();
        $this->assertNotNull($ticketResponse);

        $attachment = $ticketResponse->attachments()->first();
        $this->assertNotNull($attachment);
        $this->assertEquals('formulir_singkat.txt', $attachment->file_name);
        $this->assertFalse($attachment->is_compressed);
    }

    public function test_multiple_files_are_automatically_bundled_into_single_zip(): void
    {
        $file1 = UploadedFile::fake()->create('dokumen1.pdf', 300, 'application/pdf');
        $file2 = UploadedFile::fake()->create('dokumen2.docx', 400, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        $file3 = UploadedFile::fake()->image('gambar3.jpg', 600, 400);

        $response = $this->actingAs($this->user)->post(route('tickets.reply', $this->ticket), [
            'response_text' => 'Melampirkan beberapa berkas.',
            'attachments' => [$file1, $file2, $file3],
            'compression_mode' => 'compressed',
        ]);

        $response->assertRedirect(route('tickets.show', $this->ticket));

        $ticketResponse = TicketResponse::where('ticket_id', $this->ticket->id)->latest('id')->first();
        $this->assertNotNull($ticketResponse);

        $this->assertCount(1, $ticketResponse->attachments);
        $attachment = $ticketResponse->attachments()->first();
        $this->assertNotNull($attachment);

        $this->assertStringStartsWith('arsip_lampiran_3_berkas_', $attachment->file_name);
        $this->assertStringEndsWith('.zip', $attachment->file_name);
        $this->assertEquals('application/zip', $attachment->mime_type);

        $disk = config('filesystems.attachment_disk', 'local');
        $this->assertTrue(Storage::disk($disk)->exists($attachment->file_path));
    }

    public function test_conversation_stream_component_contains_file_chip_size_styling(): void
    {
        $response = $this->actingAs($this->admin)->get("/admin/tickets/{$this->ticket->id}/edit");
        $response->assertOk();
        $content = $response->getContent();
        $this->assertStringContainsString('bbpom-file-chip', $content);
        $this->assertStringContainsString('bbpom-file-chip-size', $content);
        $this->assertStringContainsString('white-space: nowrap', $content);
    }
}

