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
        $email = 'admintest_' . uniqid() . '@bbpom.go.id';
        $admin = User::factory()->create([
            'email' => $email,
            'role' => 'admin',
        ]);

        $response = $this->post('/login', [
            'email' => $email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin');
    }

    public function test_regular_user_is_redirected_to_dashboard_after_login(): void
    {
        $email = 'usertest_' . uniqid() . '@bbpom.go.id';
        $user = User::factory()->create([
            'email' => $email,
            'role' => 'user',
        ]);

        $response = $this->post('/login', [
            'email' => $email,
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

        $ticket = $user->tickets()->where('subject', 'Kendala switch lab uji')->latest('id')->first();
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

    public function test_user_can_submit_bsre_reset_password_ticket(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso@bbpom.go.id',
            'nip' => null,
            'nik' => null,
            'phone_number' => null,
        ]);

        $response = $this->actingAs($user)->post('/tickets', [
            'service_key' => 'sertifikat_bse',
            'subcategory' => 'reset_password',
            'tanggal_pengajuan' => '2026-10-01',
            'nama' => 'Budi Santoso',
            'nip' => '198801012015011001',
            'nik' => '3301010101880001',
            'email' => 'budi.santoso@bbpom.go.id',
            'nomor_telepon' => '081234567890',
        ]);

        $ticket = $user->tickets()->where('service_key', 'sertifikat_bse')
            ->where('subcategory', 'reset_password')
            ->latest('id')
            ->first();

        $this->assertNotNull($ticket);
        $this->assertStringContainsString('Reset Password BSRE', $ticket->subject);
        $this->assertNotNull($ticket->bsreDetail);
        $this->assertEquals('198801012015011001', $ticket->bsreDetail->nip);
        $this->assertEquals('3301010101880001', $ticket->bsreDetail->nik);
        $this->assertEquals('budi.santoso@bbpom.go.id', $ticket->bsreDetail->email);
        $this->assertEquals('081234567890', $ticket->bsreDetail->nomor_telepon);

        // Verify user profile was auto-updated with NIP, NIK, Phone
        $user->refresh();
        $this->assertEquals('198801012015011001', $user->nip);
        $this->assertEquals('3301010101880001', $user->nik);
        $this->assertEquals('081234567890', $user->phone_number);

        $response->assertRedirect(route('tickets.show', $ticket));
    }

    public function test_user_can_submit_bsre_permintaan_otp_ticket(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'name' => 'Siti Rahma',
            'email' => 'siti.rahma@bbpom.go.id',
            'nip' => '199002022019022002',
            'nik' => '3302020202900002',
            'phone_number' => '089876543210',
        ]);

        $response = $this->actingAs($user)->post('/tickets', [
            'service_key' => 'sertifikat_bse',
            'subcategory' => 'permintaan_otp',
            'tanggal_pengajuan' => '2026-10-01',
            'nama' => 'Siti Rahma',
            'nip' => '199002022019022002',
            'nik' => '3302020202900002',
            'email' => 'siti.rahma@bbpom.go.id',
            'nomor_telepon' => '089876543210',
        ]);

        $ticket = $user->tickets()->where('service_key', 'sertifikat_bse')
            ->where('subcategory', 'permintaan_otp')
            ->latest('id')
            ->first();

        $this->assertNotNull($ticket);
        $this->assertStringContainsString('Permintaan OTP', $ticket->subject);
        $this->assertNotNull($ticket->bsreDetail);
        $this->assertEquals('permintaan_otp', $ticket->bsreDetail->sub_category);

        $response->assertRedirect(route('tickets.show', $ticket));
    }

    public function test_user_can_submit_bsre_lainnya_ticket(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->post('/tickets', [
            'service_key' => 'sertifikat_bse',
            'subcategory' => 'lainnya',
            'tanggal_pengajuan' => '2026-10-01',
            'nama' => $user->name,
            'deskripsi' => 'Mohon bantuan penerbitan sertifikat BSrE baru untuk pegawai mutasi.',
        ]);

        $ticket = $user->tickets()->where('service_key', 'sertifikat_bse')
            ->where('subcategory', 'lainnya')
            ->latest('id')
            ->first();

        $this->assertNotNull($ticket);
        $this->assertNotNull($ticket->bsreDetail);
        $this->assertEquals('lainnya', $ticket->bsreDetail->sub_category);
        $this->assertEquals('Mohon bantuan penerbitan sertifikat BSrE baru untuk pegawai mutasi.', $ticket->bsreDetail->deskripsi);

        $response->assertRedirect(route('tickets.show', $ticket));
    }

    public function test_user_can_view_bsre_ticket_details(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::firstOrCreate(['name' => 'Sertifikat BSrE / TTE', 'slug' => 'sertifikat-bsre']);

        $ticket = Ticket::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'service_key' => 'sertifikat_bse',
            'subcategory' => 'reset_password',
            'subject' => '[Sertifikat BSrE] Reset Password BSRE - ' . $user->name,
            'description' => 'Permohonan reset password BSrE',
            'status' => 'pending',
        ]);

        $ticket->bsreDetail()->create([
            'sub_category' => 'reset_password',
            'tanggal_pengajuan' => '2026-10-01',
            'nama' => $user->name,
            'nip' => '198501012010121001',
            'nik' => '3301010101850001',
            'email' => $user->email,
            'nomor_telepon' => '081234567890',
        ]);

        $response = $this->actingAs($user)->get(route('tickets.show', $ticket));

        $response->assertSuccessful();
        $response->assertSee('Formulir Khusus Layanan');
        $response->assertSee('Sertifikat BSrE / TTE');
        $response->assertSee('Reset Password BSRE');
        $response->assertSee('198501012010121001');
        $response->assertSee('3301010101850001');
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

    public function test_user_can_submit_zoom_link_ticket(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->post('/tickets', [
            'service_key' => 'zoom',
            'subcategory' => 'permintaan_link_zoom',
            'tanggal_pengajuan' => '2026-10-01',
            'nama' => $user->name,
            'nama_acara' => 'Rapat Koordinasi Pengawasan Pangan',
            'jam' => '09:00 - 12:00 WIB',
        ]);

        $ticket = $user->tickets()->where('service_key', 'zoom')
            ->where('subcategory', 'permintaan_link_zoom')
            ->latest('id')
            ->first();

        $this->assertNotNull($ticket);
        $this->assertNotNull($ticket->zoomDetail);
        $this->assertEquals('Rapat Koordinasi Pengawasan Pangan', $ticket->zoomDetail->nama_acara);
        $this->assertEquals('09:00 - 12:00 WIB', $ticket->zoomDetail->jam);

        $response->assertRedirect(route('tickets.show', $ticket));

        // Test view details
        $viewResponse = $this->actingAs($user)->get(route('tickets.show', $ticket));
        $viewResponse->assertSuccessful();
        $viewResponse->assertSee('Zoom Meeting');
        $viewResponse->assertSee('Rapat Koordinasi Pengawasan Pangan');
    }

    public function test_user_can_submit_kegiatan_daftar_hadir_and_qrcode_tickets(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        // 1. Daftar Hadir
        $response1 = $this->actingAs($user)->post('/tickets', [
            'service_key' => 'formulir_kegiatan',
            'subcategory' => 'permintaan_daftar_hadir',
            'tanggal_pengajuan' => '2026-10-01',
            'nama' => $user->name,
            'nama_acara' => 'Bimtek CPPOB UMKM',
            'jam' => '08:00 - 11:30 WIB',
        ]);

        $ticket1 = $user->tickets()->where('service_key', 'formulir_kegiatan')
            ->where('subcategory', 'permintaan_daftar_hadir')
            ->latest('id')
            ->first();

        $this->assertNotNull($ticket1);
        $this->assertNotNull($ticket1->kegiatanDetail);
        $this->assertEquals('Bimtek CPPOB UMKM', $ticket1->kegiatanDetail->nama_acara);

        // 2. QR Code
        $response2 = $this->actingAs($user)->post('/tickets', [
            'service_key' => 'formulir_kegiatan',
            'subcategory' => 'permintaan_qrcode',
            'tanggal_pengajuan' => '2026-10-01',
            'nama' => $user->name,
            'link_qrcode' => 'https://bbpomsemarang.pom.go.id/survey-layanan',
        ]);

        $ticket2 = $user->tickets()->where('service_key', 'formulir_kegiatan')
            ->where('subcategory', 'permintaan_qrcode')
            ->latest('id')
            ->first();

        $this->assertNotNull($ticket2);
        $this->assertEquals('https://bbpomsemarang.pom.go.id/survey-layanan', $ticket2->kegiatanDetail->link_qrcode);

        $viewResponse = $this->actingAs($user)->get(route('tickets.show', $ticket2));
        $viewResponse->assertSuccessful();
        $viewResponse->assertSee('https://bbpomsemarang.pom.go.id/survey-layanan');
    }

    public function test_user_can_submit_survey_with_questions(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $questions = [
            ['text' => 'Bagaimana kepuasan Anda terhadap kecepatan respon petugas?', 'tipe' => 'Pilihan Skala 4'],
            ['text' => 'Apakah sarana laboratorium memenuhi standar?', 'tipe' => 'Yes/No'],
        ];

        $response = $this->actingAs($user)->post('/tickets', [
            'service_key' => 'formulir_kegiatan',
            'subcategory' => 'survey',
            'tanggal_pengajuan' => '2026-10-01',
            'nama' => $user->name,
            'judul_survey' => 'Survei Kepuasan Pelanggan Pengujian 2026',
            'pertanyaan_json' => json_encode($questions),
        ]);

        $ticket = $user->tickets()->where('service_key', 'formulir_kegiatan')
            ->where('subcategory', 'survey')
            ->latest('id')
            ->first();

        $this->assertNotNull($ticket);
        $this->assertNotNull($ticket->kegiatanDetail);
        $this->assertEquals('Survei Kepuasan Pelanggan Pengujian 2026', $ticket->kegiatanDetail->judul_survey);
        $this->assertCount(2, $ticket->kegiatanDetail->pertanyaan_data);

        $viewResponse = $this->actingAs($user)->get(route('tickets.show', $ticket));
        $viewResponse->assertSuccessful();
        $viewResponse->assertSee('Survei Kepuasan Pelanggan Pengujian 2026');
        $viewResponse->assertSee('Bagaimana kepuasan Anda terhadap kecepatan respon petugas?');
    }

    public function test_user_can_submit_install_windows_ticket(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->post('/tickets', [
            'service_key' => 'install_windows',
            'subcategory' => 'permintaan_install_windows',
            'tanggal_pengajuan' => '2026-10-01',
            'nama' => $user->name,
            'bidang' => 'Pengujian',
            'merk_perangkat' => 'Lenovo ThinkPad L14 Gen 2',
            'kode_bmn' => '3.05.01.04.001.000789',
            'deskripsi' => 'Sering mengalami blue screen dan hang saat membuka aplikasi analisis.',
        ]);

        $ticket = $user->tickets()->where('service_key', 'install_windows')->latest('id')->first();
        $this->assertNotNull($ticket);
        $this->assertNotNull($ticket->windowsDetail);
        $this->assertEquals('Pengujian', $ticket->windowsDetail->bidang);
        $this->assertEquals('Lenovo ThinkPad L14 Gen 2', $ticket->windowsDetail->merk_perangkat);
        $this->assertEquals('3.05.01.04.001.000789', $ticket->windowsDetail->kode_bmn);

        $viewResponse = $this->actingAs($user)->get(route('tickets.show', $ticket));
        $viewResponse->assertSuccessful();
        $viewResponse->assertSee('Install Ulang Windows');
        $viewResponse->assertSee('3.05.01.04.001.000789');
    }

    public function test_user_can_submit_office_license_ticket(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->post('/tickets', [
            'service_key' => 'lisensi_office',
            'subcategory' => 'permintaan_lisensi_office',
            'tanggal_pengajuan' => '2026-10-01',
            'nama' => $user->name,
            'bidang' => 'Tata Usaha',
            'merk_perangkat' => 'Dell Latitude 3420',
            'kode_bmn' => '3.05.01.04.001.000999',
            'deskripsi' => 'Muncul peringatan unlicensed product pada Microsoft Word dan Excel.',
        ]);

        $ticket = $user->tickets()->where('service_key', 'lisensi_office')->latest('id')->first();
        $this->assertNotNull($ticket);
        $this->assertNotNull($ticket->officeDetail);
        $this->assertEquals('Tata Usaha', $ticket->officeDetail->bidang);
        $this->assertEquals('Dell Latitude 3420', $ticket->officeDetail->merk_perangkat);

        $viewResponse = $this->actingAs($user)->get(route('tickets.show', $ticket));
        $viewResponse->assertSuccessful();
        $viewResponse->assertSee('Lisensi Microsoft Office');
        $viewResponse->assertSee('Dell Latitude 3420');
    }

    public function test_user_can_submit_general_lainnya_ticket(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->post('/tickets', [
            'service_key' => 'lainnya',
            'tanggal_pengajuan' => '2026-10-01',
            'nama' => $user->name,
            'deskripsi' => 'Printer HP LaserJet di Ruang Tata Usaha paper jam dan roller tidak menarik kertas.',
        ]);

        $ticket = $user->tickets()->where('service_key', 'lainnya')->latest('id')->first();
        $this->assertNotNull($ticket);
        $this->assertNotNull($ticket->lainnyaDetail);
        $this->assertEquals('Printer HP LaserJet di Ruang Tata Usaha paper jam dan roller tidak menarik kertas.', $ticket->lainnyaDetail->deskripsi);

        $viewResponse = $this->actingAs($user)->get(route('tickets.show', $ticket));
        $viewResponse->assertSuccessful();
        $viewResponse->assertSee('Layanan TIK Umum / Lainnya');
    }
}

