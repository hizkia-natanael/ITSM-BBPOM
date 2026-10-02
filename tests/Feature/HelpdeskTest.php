<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HelpdeskTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $categoriesData = [
            ['name' => 'Jaringan & Konektivitas Internet', 'slug' => 'jaringan-internet'],
            ['name' => 'Perangkat Keras (Hardware & PC)', 'slug' => 'perangkat-keras-hardware'],
            ['name' => 'Aplikasi & Sistem Informasi (Software)', 'slug' => 'aplikasi-software'],
            ['name' => 'Printer, Scanner & Periferal', 'slug' => 'printer-scanner'],
            ['name' => 'Akun, Email & Keamanan Informasi', 'slug' => 'akun-email-keamanan'],
        ];

        foreach ($categoriesData as $data) {
            Category::firstOrCreate(
                ['slug' => $data['slug']],
                ['name' => $data['name']]
            );
        }
    }

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

    public function test_user_ticket_index_is_paginated(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::firstOrCreate(['name' => 'Hardware', 'slug' => 'hardware']);
        $uniq = uniqid();

        for ($i = 1; $i <= 15; $i++) {
            $t = Ticket::create([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'ticket_number' => "TIK-PGN-{$uniq}-{$i}",
                'subject' => "Masalah pengujian {$uniq} nomor {$i}",
                'description' => "Deskripsi detail keluhan {$i}",
                'status' => 'pending',
            ]);
            $t->created_at = now()->subMinutes(20 - $i);
            $t->saveQuietly();
        }

        $response = $this->actingAs($user)->get(route('tickets.index'));
        $response->assertSuccessful();
        $response->assertSee("Masalah pengujian {$uniq} nomor 15");
        $response->assertSee('page=2');

        $page2Response = $this->actingAs($user)->get(route('tickets.index', ['page' => 2]));
        $page2Response->assertSuccessful();
        $page2Response->assertSee("Masalah pengujian {$uniq} nomor 1");
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

    public function test_user_can_submit_internet_ticket(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::firstOrCreate(['name' => 'Jaringan', 'slug' => 'jaringan']);

        $response = $this->actingAs($user)->post('/tickets', [
            'service_key' => 'internet',
            'subcategory_key' => 'lemot_mati',
            'category_id' => $category->id,
            'ap_location' => 'Gedung A Lantai 2 Subbag TU',
            'complaint_details' => 'Sinyal WiFi sering RTO dan tidak bisa akses intranet.',
        ]);

        $ticket = $user->tickets()->where('service_key', 'internet')->latest('id')->first();
        $this->assertNotNull($ticket);
        $this->assertStringContainsString('Internet', $ticket->subject);
        $this->assertStringContainsString('Gedung A Lantai 2 Subbag TU', $ticket->description);
        $this->assertStringContainsString('Sinyal WiFi sering RTO', $ticket->description);

        $response->assertRedirect(route('tickets.show', $ticket));
    }

    public function test_user_can_submit_subsite_ticket(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::firstOrCreate(['name' => 'Website', 'slug' => 'website']);

        $response = $this->actingAs($user)->post('/tickets', [
            'service_key' => 'subsite',
            'subcategory_key' => 'upload_berita',
            'category_id' => $category->id,
            'news_title' => 'Sosialisasi Keamanan Pangan Segar',
            'news_content' => 'BBPOM Semarang melaksanakan inspeksi rutin bersama dinas kesehatan.',
            'target_date' => '2026-10-05',
        ]);

        $ticket = $user->tickets()->where('service_key', 'subsite')->latest('id')->first();
        $this->assertNotNull($ticket);
        $this->assertStringContainsString('Upload Berita', $ticket->subject);
        $this->assertStringContainsString('Sosialisasi Keamanan Pangan Segar', $ticket->description);

        $response->assertRedirect(route('tickets.show', $ticket));
    }

    public function test_user_can_submit_infografis_ticket(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::firstOrCreate(['name' => 'Media', 'slug' => 'media']);

        $response = $this->actingAs($user)->post('/tickets', [
            'service_key' => 'infografis',
            'subcategory_key' => 'pembuatan_infografis',
            'category_id' => $category->id,
            'infographic_title' => 'Tips Memilih Kosmetik Aman',
            'infographic_content' => 'Poin KLIK: Kemasan, Label, Izin Edar, Kedaluwarsa.',
        ]);

        $ticket = $user->tickets()->where('service_key', 'infografis')->latest('id')->first();
        $this->assertNotNull($ticket);
        $this->assertStringContainsString('Pembuatan Infografis', $ticket->subject);
        $this->assertStringContainsString('Tips Memilih Kosmetik Aman', $ticket->description);

        $response->assertRedirect(route('tickets.show', $ticket));
    }

    public function test_user_can_view_create_screen_for_services(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $services = ['sipt', 'srikandi', 'email_corporate', 'sertifikat_bse', 'zoom', 'formulir_kegiatan', 'internet', 'subsite', 'infografis', 'install_windows', 'lisensi_office', 'lainnya'];

        foreach ($services as $service) {
            $response = $this->actingAs($user)->get(route('tickets.create', ['service' => $service]));
            $response->assertSuccessful();
            $response->assertSee('App Gateway Layanan');
        }
    }

    public function test_admin_panel_is_fixed_light_mode_and_renders_interactive_widgets(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Verify Fixed Light Mode, Top Navigation, and Global Search configuration
        $panel = \Filament\Facades\Filament::getPanel('admin');
        $this->assertFalse($panel->hasDarkMode());
        $this->assertTrue($panel->hasTopNavigation());
        $this->assertNull($panel->getGlobalSearchProvider());

        // Verify widget data contracts
        $chartWidget = new \App\Filament\Widgets\TicketTrendChart();
        $counts = $chartWidget->getStatusCounts();
        $this->assertArrayHasKey('total', $counts);
        $this->assertArrayHasKey('pending', $counts);
        $this->assertArrayHasKey('diproses', $counts);
        $this->assertArrayHasKey('selesai', $counts);
        $this->assertArrayHasKey('ditolak', $counts);

        // Verify admin dashboard HTTP response
        $response = $this->actingAs($admin)->get('/admin');
        $response->assertSuccessful();
        $response->assertSee('Dashboard Admin');
        $response->assertSee('logo-bpom.png');
        $response->assertSee('Layanan Admin TIK');
        $response->assertSee('BBPOM Di Semarang');
        $response->assertSee('Keluar');
        $response->assertSee('admin/logout');
        $response->assertSee('Pengaduan (Tickets)');
        $response->assertSee('Kategori Layanan');
        $response->assertSee('Data Pegawai');
        $response->assertDontSee('fi-global-search-ctn');

        // Verify /admin/login redirects to /login and admin logout redirects to /login
        $this->get('/admin/login')->assertRedirect('/login');
        $logoutResponse = $this->actingAs($admin)->post('/admin/logout');
        $logoutResponse->assertRedirect('/login');

        // Verify Livewire Widgets render properly
        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Filament\Widgets\TicketTrendChart::class)
            ->assertSee('Tren Pengaduan Layanan TI')
            ->assertSee('Per Bulan')
            ->assertSee('Per Tahun')
            ->assertSee('Rentang Waktu')
            ->assertSee('Pending')
            ->assertSee('Diproses')
            ->assertSee('Selesai')
            ->assertSee('Ditolak')
            ->call('setPeriod', 'tahun')
            ->assertSet('period', 'tahun')
            ->call('setPeriod', 'custom')
            ->assertSet('period', 'custom')
            ->assertSee('Dari:')
            ->assertSee('Sampai:');

        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Filament\Widgets\TicketStatsOverview::class)
            ->assertSee('Total Pengaduan')
            ->assertSee('Tiket Pending')
            ->assertSee('Tiket Diproses')
            ->assertSee('Tiket Selesai')
            ->assertSee('Tiket Ditolak');
    }

    public function test_ticket_status_displays_handling_admin_name_to_user(): void
    {
        $admin1 = User::factory()->create([
            'name' => 'Admin 1 - Layanan TIK',
            'role' => 'admin',
        ]);

        $admin2 = User::factory()->create([
            'name' => 'Admin 2 - Layanan TIK',
            'role' => 'admin',
        ]);

        $user = User::factory()->create(['role' => 'user']);
        $category = Category::firstOrCreate(['slug' => 'jaringan'], ['name' => 'Jaringan']);

        $ticket = Ticket::create([
            'ticket_number' => 'TIK-TEST-001',
            'user_id' => $user->id,
            'handled_by_id' => $admin1->id,
            'category_id' => $category->id,
            'subject' => 'Kendala WiFi Admin 1',
            'description' => 'Deskripsi kendala pengujian untuk admin 1',
            'status' => 'diproses',
        ]);

        // User views ticket show page
        $responseShow = $this->actingAs($user)->get(route('tickets.show', $ticket));
        $responseShow->assertSuccessful();
        $responseShow->assertSee('Sedang diproses oleh Admin 1 - Layanan TIK');
        $responseShow->assertSee('Petugas Penanggung Jawab');
        $responseShow->assertSee('Admin 1 - Layanan TIK');

        // User views ticket index and dashboard
        $responseIndex = $this->actingAs($user)->get(route('tickets.index'));
        $responseIndex->assertSuccessful();
        $responseIndex->assertSee('Sedang diproses oleh Admin 1 - Layanan TIK');

        $responseDashboard = $this->actingAs($user)->get(route('dashboard'));
        $responseDashboard->assertSuccessful();
        $responseDashboard->assertSee('Sedang diproses oleh Admin 1 - Layanan TIK');

        // Now change handler to Admin 2 and status to selesai
        $ticket->update([
            'handled_by_id' => $admin2->id,
            'status' => 'selesai',
        ]);

        $responseShowSelesai = $this->actingAs($user)->get(route('tickets.show', $ticket));
        $responseShowSelesai->assertSuccessful();
        $responseShowSelesai->assertSee('Selesai ditangani oleh Admin 2 - Layanan TIK');
        $responseShowSelesai->assertSee('Admin 2 - Layanan TIK');
    }

    public function test_admin_reply_auto_assigns_handler_and_moves_pending_to_diproses(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin 1',
            'role' => 'admin',
        ]);

        $user = User::factory()->create(['role' => 'user']);
        $category = Category::firstOrCreate(['slug' => 'software'], ['name' => 'Software']);

        $ticket = Ticket::create([
            'ticket_number' => 'TIK-TEST-002',
            'user_id' => $user->id,
            'handled_by_id' => null,
            'category_id' => $category->id,
            'subject' => 'Kendala Aplikasi Test',
            'description' => 'Aplikasi error',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post(route('tickets.reply', $ticket), [
            'response_text' => 'Halo, pengaduan ini sedang saya tangani sekarang.',
        ]);

        $response->assertRedirect(route('tickets.show', $ticket));

        $ticket->refresh();
        $this->assertEquals($admin->id, $ticket->handled_by_id);
        $this->assertEquals('diproses', $ticket->status);
    }

    public function test_filament_auto_stamps_logged_in_admin_when_status_updated(): void
    {
        $admin1 = User::factory()->create([
            'name' => 'Admin 1 - Layanan TIK',
            'role' => 'admin',
        ]);

        $admin2 = User::factory()->create([
            'name' => 'Admin 2 - Layanan TIK',
            'role' => 'admin',
        ]);

        $user = User::factory()->create(['role' => 'user']);
        $category = Category::firstOrCreate(['slug' => 'hardware'], ['name' => 'Hardware']);

        $ticket = Ticket::create([
            'ticket_number' => 'TIK-AUTO-001',
            'user_id' => $user->id,
            'handled_by_id' => null,
            'category_id' => $category->id,
            'subject' => 'Kendala PC',
            'description' => 'PC tidak mau menyala',
            'status' => 'pending',
        ]);

        // Admin 1 updates status to diproses via Filament
        \Livewire\Livewire::actingAs($admin1)
            ->test(\App\Filament\Resources\Tickets\Pages\EditTicket::class, [
                'record' => $ticket->getKey(),
            ])
            ->fillForm([
                'status' => 'diproses',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $ticket->refresh();
        $this->assertEquals($admin1->id, $ticket->handled_by_id);
        $this->assertEquals('diproses', $ticket->status);

        // User views ticket and sees Admin 1 handling
        $response1 = $this->actingAs($user)->get(route('tickets.show', $ticket));
        $response1->assertSee('Sedang diproses oleh Admin 1 - Layanan TIK');

        // Later Admin 2 finishes the ticket via Filament
        \Livewire\Livewire::actingAs($admin2)
            ->test(\App\Filament\Resources\Tickets\Pages\EditTicket::class, [
                'record' => $ticket->getKey(),
            ])
            ->fillForm([
                'status' => 'selesai',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $ticket->refresh();
        $this->assertEquals($admin2->id, $ticket->handled_by_id);
        $this->assertEquals('selesai', $ticket->status);

        // User views ticket and sees Admin 2 finished it
        $response2 = $this->actingAs($user)->get(route('tickets.show', $ticket));
        $response2->assertSee('Selesai ditangani oleh Admin 2 - Layanan TIK');
    }

    public function test_tab_favicon_logo_bpom_is_configured_across_portal_views(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);

        // Home / Login page
        $resHome = $this->get('/');
        $resHome->assertSee('images/logo-bpom.png');

        // User dashboard
        $resDash = $this->actingAs($user)->get('/dashboard');
        $resDash->assertSee('images/logo-bpom.png');

        // User create ticket
        $resCreate = $this->actingAs($user)->get('/tickets/create');
        $resCreate->assertSee('images/logo-bpom.png');

        // Admin Filament panel
        $resAdmin = $this->actingAs($admin)->get('/admin');
        $resAdmin->assertSee('images/logo-bpom.png');
    }

    public function test_all_twelve_services_and_all_subcategories_can_be_submitted_and_viewed_by_admin(): void
    {
        Category::firstOrCreate(['slug' => 'jaringan-internet'], ['name' => 'Jaringan & Konektivitas Internet']);
        Category::firstOrCreate(['slug' => 'perangkat-keras-hardware'], ['name' => 'Perangkat Keras (Hardware & PC)']);
        Category::firstOrCreate(['slug' => 'aplikasi-software'], ['name' => 'Aplikasi & Sistem Informasi (Software)']);
        Category::firstOrCreate(['slug' => 'printer-scanner'], ['name' => 'Printer, Scanner & Periferal']);
        Category::firstOrCreate(['slug' => 'akun-email-keamanan'], ['name' => 'Akun, Email & Keamanan Informasi']);
        Category::firstOrCreate(['slug' => 'sertifikat-bsre'], ['name' => 'Sertifikat BSrE / TTE']);

        $user = User::factory()->create(['role' => 'user', 'name' => 'Test Pegawai', 'nip' => '199001012020011001']);
        $admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin Verifier']);

        $submissions = [
            // 1. SIPT
            'sipt_reset' => [
                'service_key' => 'sipt',
                'subcategory' => 'reset_password',
                'sipt_username' => 'ahmad123',
                'sipt_password' => 'PassBaru123!',
            ],
            'sipt_rollback' => [
                'service_key' => 'sipt',
                'subcategory' => 'rollback_sampel',
                'sipt_sample_number' => 'SMP-2026-X01',
                'sipt_commodity' => 'Pangan',
            ],
            'sipt_repair' => [
                'service_key' => 'sipt',
                'subcategory' => 'perbaikan_sampel',
                'sipt_sample_code' => 'KD-9988',
                'sipt_commodity' => 'Obat',
                'sipt_sample_name' => 'Paracetamol Tab',
                'sipt_before_repair' => 'Batch 01',
                'sipt_after_repair' => 'Batch 02',
            ],
            'sipt_track' => [
                'service_key' => 'sipt',
                'subcategory' => 'cek_posisi_sampel',
                'sipt_sample_code' => 'KD-9989',
                'sipt_commodity' => 'Kosmetik',
                'sipt_sample_name' => 'Body Lotion',
            ],
            'sipt_other' => [
                'service_key' => 'sipt',
                'subcategory' => 'lainnya',
                'service_description' => 'Kendala error validasi data pengujian di modul SIPT',
            ],

            // 2. SRIKANDI
            'srikandi_reset' => [
                'service_key' => 'srikandi',
                'subcategory' => 'reset_password',
                'srikandi_username' => 'srikandi_user',
                'srikandi_password' => 'SrikandiPass123!',
            ],
            'srikandi_create' => [
                'service_key' => 'srikandi',
                'subcategory' => 'buat_akun',
                'srikandi_fullname' => 'Pegawai Baru',
                'srikandi_nik' => '3301234567890002',
                'srikandi_phone' => '081234567891',
                'srikandi_email' => 'baru@bbpom.go.id',
                'srikandi_req_username' => 'pegawai_baru',
                'srikandi_req_password' => 'AwalPass123!',
            ],
            'srikandi_disable' => [
                'service_key' => 'srikandi',
                'subcategory' => 'nonaktif_akun',
                'srikandi_username' => 'pegawai_mutasi',
                'srikandi_disable_reason' => 'Mutasi ke unit kerja BBPOM lain',
            ],
            'srikandi_mfa' => [
                'service_key' => 'srikandi',
                'subcategory' => 'reset_mfa',
                'srikandi_username' => 'pegawai_mfa',
            ],
            'srikandi_other' => [
                'service_key' => 'srikandi',
                'subcategory' => 'lainnya',
                'service_description' => 'Gagal verifikasi berkas naskah keluar di Srikandi',
            ],

            // 3. EMAIL CORPORATE
            'email_reset' => [
                'service_key' => 'email_corporate',
                'subcategory' => 'reset_password',
                'email_nip' => '199001012020011001',
                'email_password' => 'EmailNewPass123!',
            ],
            'email_other' => [
                'service_key' => 'email_corporate',
                'subcategory' => 'lainnya',
                'service_description' => 'Kuota penyimpanan webmail telah mencapai 98%',
            ],

            // 4. SERTIFIKAT BSRE
            'bsre_reset' => [
                'service_key' => 'sertifikat_bse',
                'subcategory' => 'reset_password',
                'tanggal_pengajuan' => '2026-10-01',
                'nama' => 'Test Pegawai',
                'nip' => '199001012020011001',
                'nik' => '3301234567890001',
                'email' => 'pegawai@bbpom.go.id',
                'nomor_telepon' => '081234567890',
            ],
            'bsre_otp' => [
                'service_key' => 'sertifikat_bse',
                'subcategory' => 'permintaan_otp',
                'tanggal_pengajuan' => '2026-10-01',
                'nama' => 'Test Pegawai',
                'nip' => '199001012020011001',
                'nik' => '3301234567890001',
                'email' => 'pegawai@bbpom.go.id',
                'nomor_telepon' => '081234567890',
            ],
            'bsre_other' => [
                'service_key' => 'sertifikat_bse',
                'subcategory' => 'lainnya',
                'tanggal_pengajuan' => '2026-10-01',
                'nama' => 'Test Pegawai',
                'deskripsi' => 'Perpanjangan masa berlaku sertifikat elektronik BSrE',
            ],

            // 5. ZOOM
            'zoom_link' => [
                'service_key' => 'zoom',
                'subcategory' => 'permintaan_link_zoom',
                'tanggal_pengajuan' => '2026-10-01',
                'nama' => 'Test Pegawai',
                'nama_acara' => 'Rapat Koordinasi Tim Pengawasan',
                'jam' => '09:00 - 12:00 WIB',
            ],
            'zoom_other' => [
                'service_key' => 'zoom',
                'subcategory' => 'lainnya',
                'tanggal_pengajuan' => '2026-10-01',
                'nama' => 'Test Pegawai',
                'deskripsi' => 'Pengaturan 8 breakout rooms webinar dinas',
            ],

            // 6. FORMULIR & KEGIATAN
            'kegiatan_hadir' => [
                'service_key' => 'formulir_kegiatan',
                'subcategory' => 'permintaan_daftar_hadir',
                'tanggal_pengajuan' => '2026-10-01',
                'nama' => 'Test Pegawai',
                'nama_acara' => 'Bimtek Keamanan Pangan Bagi UMKM',
                'jam' => '08:30 - 15:30 WIB',
            ],
            'kegiatan_qrcode' => [
                'service_key' => 'formulir_kegiatan',
                'subcategory' => 'permintaan_qrcode',
                'tanggal_pengajuan' => '2026-10-01',
                'nama' => 'Test Pegawai',
                'link_qrcode' => 'https://bbpomsemarang.pom.go.id/bimtek-pangan',
            ],
            'kegiatan_survey' => [
                'service_key' => 'formulir_kegiatan',
                'subcategory' => 'survey',
                'tanggal_pengajuan' => '2026-10-01',
                'nama' => 'Test Pegawai',
                'judul_survey' => 'Survei Kepuasan Stakeholder 2026',
                'pertanyaan_json' => json_encode([['text' => 'Bagaimana kepuasan pelayanan kami?', 'tipe' => 'Pilihan Skala 4']]),
            ],
            'kegiatan_test' => [
                'service_key' => 'formulir_kegiatan',
                'subcategory' => 'post_test_pretest',
                'tanggal_pengajuan' => '2026-10-01',
                'nama' => 'Test Pegawai',
                'judul_acara' => 'Pelatihan Uji Laboratorium Kimia',
                'jenis_test' => 'pretest',
                'pertanyaan_json' => json_encode([['text' => 'Berapa standar pH pengujian?', 'tipe' => 'Multiple Answer']]),
            ],
            'kegiatan_other' => [
                'service_key' => 'formulir_kegiatan',
                'subcategory' => 'lainnya',
                'tanggal_pengajuan' => '2026-10-01',
                'nama' => 'Test Pegawai',
                'deskripsi' => 'Pembuatan link landing page kegiatan monitoring',
            ],

            // 7. INTERNET & JARINGAN
            'internet_lemot' => [
                'service_key' => 'internet',
                'subcategory' => 'lemot_mati',
                'ap_location' => 'Lantai 2 Ruang Infokom',
                'complaint_details' => 'Koneksi WiFi sering mengalami Request Time Out (RTO)',
            ],
            'internet_ap' => [
                'service_key' => 'internet',
                'subcategory' => 'tambah_ap',
                'ap_location' => 'Ruang Arsip Baru',
                'complaint_details' => 'Ruangan tidak terjangkau sinyal WiFi kantor',
            ],
            'internet_other' => [
                'service_key' => 'internet',
                'subcategory' => 'lainnya',
                'service_description' => 'Kabel LAN port 12 di meja kerja tidak tersambung',
            ],

            // 8. SUBSITE
            'subsite_news' => [
                'service_key' => 'subsite',
                'subcategory' => 'upload_berita',
                'news_title' => 'BBPOM Semarang Gelar Pengawasan Pasar',
                'news_content' => 'Kegiatan pengawasan serentak obat dan makanan...',
                'target_date' => '2026-10-05',
            ],
            'subsite_doc' => [
                'service_key' => 'subsite',
                'subcategory' => 'upload_dokumen',
                'doc_name' => 'SK Standar Pelayanan Tahun 2026',
                'doc_location' => 'Menu Standar Pelayanan Publik',
            ],
            'subsite_del' => [
                'service_key' => 'subsite',
                'subcategory' => 'hapus_dokumen',
                'doc_name' => 'Pengumuman Tender 2021',
                'doc_url' => 'https://semarang.pom.go.id/dokumen/tender2021.pdf',
            ],

            // 9. INFOGRAFIS
            'infografis_create' => [
                'service_key' => 'infografis',
                'subcategory' => 'pembuatan_infografis',
                'infographic_title' => 'Edukasi Cek KLIK Obat Aman',
                'infographic_content' => 'Informasi langkah Cek Kemasan, Label, Izin Edar, dan Kedaluwarsa',
            ],

            // 10. INSTALL WINDOWS
            'windows_install' => [
                'service_key' => 'install_windows',
                'subcategory' => 'permintaan_install_windows',
                'tanggal_pengajuan' => '2026-10-01',
                'nama' => 'Test Pegawai',
                'bidang' => 'Tata Usaha',
                'merk_perangkat' => 'Lenovo ThinkPad L14',
                'kode_bmn' => '3.05.02.01.002.88888',
                'deskripsi' => 'Sistem operasi korup dan blue screen berulang',
            ],
            'windows_other' => [
                'service_key' => 'install_windows',
                'subcategory' => 'lainnya',
                'tanggal_pengajuan' => '2026-10-01',
                'nama' => 'Test Pegawai',
                'deskripsi' => 'Pembersihan malware dan optimalisasi startup Windows',
            ],

            // 11. LISENSI OFFICE
            'office_license' => [
                'service_key' => 'lisensi_office',
                'subcategory' => 'permintaan_lisensi_office',
                'tanggal_pengajuan' => '2026-10-01',
                'nama' => 'Test Pegawai',
                'bidang' => 'Pengujian',
                'merk_perangkat' => 'Dell Latitude 3420',
                'kode_bmn' => '3.05.02.01.002.77777',
                'deskripsi' => 'Aktivasi lisensi resmi Microsoft 365 unlicensed',
            ],
            'office_other' => [
                'service_key' => 'lisensi_office',
                'subcategory' => 'lainnya',
                'tanggal_pengajuan' => '2026-10-01',
                'nama' => 'Test Pegawai',
                'deskripsi' => 'Microsoft Word freeze dan crash saat membuka dokumen template dinas',
            ],

            // 12. LAINNYA
            'general_other' => [
                'service_key' => 'lainnya',
                'subcategory' => 'lainnya',
                'tanggal_pengajuan' => '2026-10-01',
                'nama' => 'Test Pegawai',
                'deskripsi' => 'Printer Epson di ruang TU mengalami paper jam konstan',
            ],
        ];

        foreach ($submissions as $label => $payload) {
            $prevCount = Ticket::count();

            $response = $this->actingAs($user)->post('/tickets', $payload);
            $response->assertSessionHasNoErrors();

            $newCount = Ticket::count();
            $this->assertEquals($prevCount + 1, $newCount, "Submission failed for [{$label}]");

            $ticket = Ticket::latest('id')->first();
            $this->assertNotNull($ticket, "Ticket record was not found for [{$label}]");
            $this->assertEquals('pending', $ticket->status);
            $this->assertEquals($payload['service_key'], $ticket->service_key);
            $this->assertEquals($payload['subcategory'], $ticket->subcategory);
            $this->assertNotNull($ticket->category_id);
            $this->assertTrue(Category::where('id', $ticket->category_id)->exists(), "Category ID {$ticket->category_id} not found in database for [{$label}]");

            // Verify specific detail relation exists when applicable
            if ($payload['service_key'] === 'sertifikat_bse') {
                $this->assertNotNull($ticket->bsreDetail, "bsreDetail missing for [{$label}]");
            } elseif ($payload['service_key'] === 'zoom') {
                $this->assertNotNull($ticket->zoomDetail, "zoomDetail missing for [{$label}]");
            } elseif ($payload['service_key'] === 'formulir_kegiatan') {
                $this->assertNotNull($ticket->kegiatanDetail, "kegiatanDetail missing for [{$label}]");
            } elseif ($payload['service_key'] === 'install_windows') {
                $this->assertNotNull($ticket->windowsDetail, "windowsDetail missing for [{$label}]");
            } elseif ($payload['service_key'] === 'lisensi_office') {
                $this->assertNotNull($ticket->officeDetail, "officeDetail missing for [{$label}]");
            } elseif ($payload['service_key'] === 'lainnya') {
                $this->assertNotNull($ticket->lainnyaDetail, "lainnyaDetail missing for [{$label}]");
            }

            // Admin can view the ticket in Filament Admin
            \Livewire\Livewire::actingAs($admin)
                ->test(\App\Filament\Resources\Tickets\Pages\EditTicket::class, [
                    'record' => $ticket->getKey(),
                ])
                ->assertSuccessful();
        }
    }
}

