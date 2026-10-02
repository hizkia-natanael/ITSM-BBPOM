<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\TicketResponse;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with comprehensive BBPOM ITSM data.
     */
    public function run(): void
    {
        // =========================================================================
        // 1. Seed Users (Admins & Pegawai BBPOM Semarang)
        // =========================================================================
        $admin = User::updateOrCreate(
            ['email' => 'admin@bbpom.go.id'],
            [
                'name' => 'Administrator IT BBPOM',
                'nip' => '198503122010121001',
                'nik' => '3374011203850001',
                'phone_number' => '081234567801',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $admin1 = User::updateOrCreate(
            ['email' => 'admin1@bbpom.go.id'],
            [
                'name' => 'Admin 1 - Layanan TIK',
                'nip' => '198503122010121001',
                'nik' => '3374011203850002',
                'phone_number' => '081234567802',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $admin2 = User::updateOrCreate(
            ['email' => 'admin2@bbpom.go.id'],
            [
                'name' => 'Admin 2 - Layanan TIK',
                'nip' => '198907152014031002',
                'nik' => '3374011507890003',
                'phone_number' => '081234567803',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $user1 = User::updateOrCreate(
            ['email' => 'user@bbpom.go.id'],
            [
                'name' => 'Ahmad Fauzi (Tata Usaha)',
                'nip' => '199104152015031002',
                'nik' => '3374021504910001',
                'phone_number' => '081234567810',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        $user2 = User::updateOrCreate(
            ['email' => 'siti@bbpom.go.id'],
            [
                'name' => 'Siti Rahmawati, S.Si. (Lab Pengujian Kimia)',
                'nip' => '199204152018012002',
                'nik' => '3374031504920002',
                'phone_number' => '081234567811',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        $user3 = User::updateOrCreate(
            ['email' => 'budi@bbpom.go.id'],
            [
                'name' => 'Budi Santoso, S.Kom. (Tata Usaha & Kearsipan)',
                'nip' => '198705112014031003',
                'nik' => '3374041105870003',
                'phone_number' => '081234567812',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        $user4 = User::updateOrCreate(
            ['email' => 'dian@bbpom.go.id'],
            [
                'name' => 'Dian Kusuma, S.Farm., Apt. (Fungsi Pemeriksaan)',
                'nip' => '199308222019022005',
                'nik' => '3374052208930004',
                'phone_number' => '081234567813',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        $user5 = User::updateOrCreate(
            ['email' => 'rizky@bbpom.go.id'],
            [
                'name' => 'Rizky Pratama, S.Si. (Lab Mikrobiologi)',
                'nip' => '199411032020121004',
                'nik' => '3374060311940005',
                'phone_number' => '081234567814',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        $user6 = User::updateOrCreate(
            ['email' => 'maya@bbpom.go.id'],
            [
                'name' => 'Maya Anggraini, S.Kom. (Fungsi Infokom & PTSP)',
                'nip' => '199002142015032001',
                'nik' => '3374071402900006',
                'phone_number' => '081234567815',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        $user7 = User::updateOrCreate(
            ['email' => 'hendra@bbpom.go.id'],
            [
                'name' => 'Hendra Wijaya, S.H. (Fungsi Penindakan & PPNS)',
                'nip' => '198806172012121002',
                'nik' => '3374081706880007',
                'phone_number' => '081234567816',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        $user8 = User::updateOrCreate(
            ['email' => 'ani@bbpom.go.id'],
            [
                'name' => 'Ani Lestari, S.E. (Keuangan & BMN)',
                'nip' => '198909182014032004',
                'nik' => '3374091809890008',
                'phone_number' => '081234567817',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        // 2. Seed Master Kategori ITSM (12 Katalog Layanan Resmi dengan ID 1-12)
        $officialCategories = [
            1 => ['name' => 'SIPT', 'slug' => 'sipt'],
            2 => ['name' => 'Srikandi', 'slug' => 'srikandi'],
            3 => ['name' => 'Email Corporate', 'slug' => 'email-corporate'],
            4 => ['name' => 'Sertifikat Elektronik (BSrE)', 'slug' => 'sertifikat-bsre'],
            5 => ['name' => 'Fasilitas Zoom', 'slug' => 'zoom'],
            6 => ['name' => 'Formulir & Kegiatan', 'slug' => 'formulir-kegiatan'],
            7 => ['name' => 'Internet & Jaringan', 'slug' => 'internet'],
            8 => ['name' => 'Pengelolaan Subsite', 'slug' => 'subsite'],
            9 => ['name' => 'Desain Infografis', 'slug' => 'infografis'],
            10 => ['name' => 'Install Ulang Windows', 'slug' => 'install-windows'],
            11 => ['name' => 'Lisensi Microsoft Office', 'slug' => 'lisensi-office'],
            12 => ['name' => 'Layanan TIK Lainnya', 'slug' => 'lainnya'],
        ];

        $categories = [];
        foreach ($officialCategories as $id => $data) {
            $categories[$data['slug']] = Category::updateOrCreate(
                ['id' => $id],
                ['name' => $data['name'], 'slug' => $data['slug']]
            );
        }

        // Additional compatibility categories for existing seeder & tests
        $compatCategories = [
            13 => ['name' => 'Jaringan & Konektivitas Internet', 'slug' => 'jaringan-internet'],
            14 => ['name' => 'Perangkat Keras (Hardware & PC)', 'slug' => 'perangkat-keras-hardware'],
            15 => ['name' => 'Aplikasi & Sistem Informasi (Software)', 'slug' => 'aplikasi-software'],
            16 => ['name' => 'Printer, Scanner & Periferal', 'slug' => 'printer-scanner'],
            17 => ['name' => 'Akun, Email & Keamanan Informasi', 'slug' => 'akun-email-keamanan'],
        ];

        foreach ($compatCategories as $id => $data) {
            $categories[$data['slug']] = Category::firstOrCreate(
                ['slug' => $data['slug']],
                ['name' => $data['name']]
            );
        }

        // =========================================================================
        // 3. Seed Comprehensive User Complaint Tickets & Responses
        // =========================================================================
        $ticketsData = [
            // -----------------------------------------------------------------
            // TIKET BARU / UNHANDLED (Pending & handled_by_id = null)
            // Muncul langsung di Card Alert Dashboard "Aduan Belum Ditangani"
            // -----------------------------------------------------------------
            [
                'ticket_number' => 'TIK-BBPOM-001',
                'user_id' => $user4->id, // Dian Kusuma
                'handled_by_id' => null,
                'category_id' => $categories['jaringan-internet']->id,
                'service_key' => 'jaringan',
                'subcategory' => 'koneksi_terputus',
                'subject' => 'Koneksi Jaringan LAN di Ruang Rapat Pimpinan Terputus Menjelang Vicon',
                'description' => 'Kabel LAN di meja ruang rapat pimpinan tidak mendapatkan IP address (unidentified network). Lampu indikator port dinding mati. Mohon segera dicek karena ada jadwal video conference pimpinan pukul 15.30 WIB.',
                'status' => 'pending',
                'created_at' => Carbon::now()->subMinutes(18),
                'updated_at' => Carbon::now()->subMinutes(18),
                'responses' => [],
            ],
            [
                'ticket_number' => 'TIK-BBPOM-002',
                'user_id' => $user7->id, // Hendra Wijaya
                'handled_by_id' => null,
                'category_id' => $categories['aplikasi-software']->id,
                'service_key' => 'aplikasi',
                'subcategory' => 'error_aplikasi',
                'subject' => 'Aplikasi SIPT Terkendala Validasi Token dan Gagal Generate Laporan BAP',
                'description' => 'Saat hendak mencetak Berita Acara Pemeriksaan pada Sistem Informasi Pengawasan Terpadu, sistem memunculkan error token expired berulang padahal sudah login ulang. Dokumen BAP dibutuhkan segera untuk berkas perkara.',
                'status' => 'pending',
                'created_at' => Carbon::now()->subMinutes(42),
                'updated_at' => Carbon::now()->subMinutes(42),
                'responses' => [],
            ],
            [
                'ticket_number' => 'TIK-BBPOM-003',
                'user_id' => $user5->id, // Rizky Pratama
                'handled_by_id' => null,
                'category_id' => $categories['printer-scanner']->id,
                'service_key' => 'printer',
                'subcategory' => 'paper_jam',
                'subject' => 'Printer Barcode Ruang Penerimaan Sampel Uji Macet dan Error E-04',
                'description' => 'Printer label barcode Zebra pada meja registrasi sampel uji mengalami paper jam dan lampu error berkedip oranye kode E-04. Label perekat tersangkut di roller utama dan tidak bisa ditarik manual.',
                'status' => 'pending',
                'created_at' => Carbon::now()->subHours(2),
                'updated_at' => Carbon::now()->subHours(2),
                'responses' => [],
            ],
            [
                'ticket_number' => 'TIK-BBPOM-004',
                'user_id' => $user6->id, // Maya Anggraini
                'handled_by_id' => null,
                'category_id' => $categories['perangkat-keras-hardware']->id,
                'service_key' => 'hardware',
                'subcategory' => 'pc_mati',
                'subject' => 'PC Meja Pelayanan Terpadu Satu Pintu (PTSP) Mati Total',
                'description' => 'Komputer PC unit PTSP di lobi depan mendadak mati dan mengeluarkan bunyi beep 3 kali saat dinyalakan kembali. Layar monitor tetap gelap. Layanan tatap muka masyarakat terhambat.',
                'status' => 'pending',
                'created_at' => Carbon::now()->subHours(3)->subMinutes(15),
                'updated_at' => Carbon::now()->subHours(3)->subMinutes(15),
                'responses' => [],
            ],
            [
                'ticket_number' => 'TIK-BBPOM-005',
                'user_id' => $user1->id, // Ahmad Fauzi
                'handled_by_id' => null,
                'category_id' => $categories['akun-email-keamanan']->id,
                'service_key' => 'akun',
                'subcategory' => 'email_baru',
                'subject' => 'Permohonan Akun Email Dinas @pom.go.id & Google Workspace CPNS Baru',
                'description' => 'Pengajuan pembuatan akun email resmi dinas serta hak akses Google Drive Balai untuk 2 orang CPNS baru yang baru bertugas di Subbag Tata Usaha terhitung tanggal 1 bulan ini.',
                'status' => 'pending',
                'created_at' => Carbon::now()->subHours(5),
                'updated_at' => Carbon::now()->subHours(5),
                'responses' => [],
            ],
            [
                'ticket_number' => 'TIK-BBPOM-006',
                'user_id' => $user2->id, // Siti Rahmawati
                'handled_by_id' => null,
                'category_id' => $categories['jaringan-internet']->id,
                'service_key' => 'jaringan',
                'subcategory' => 'wifi_gangguan',
                'subject' => 'Akses WiFi Gedung B Lantai 2 Sinyal Penuh Namun No Internet Access',
                'description' => 'Seluruh laptop di Lab Kimia Lantai 2 terhubung ke SSID BBPOM-Staff namun status bertuliskan No Internet Access. DNS lookup gagal saat membuka situs internal Badan POM.',
                'status' => 'pending',
                'created_at' => Carbon::now()->subDay()->subHours(3),
                'updated_at' => Carbon::now()->subDay()->subHours(3),
                'responses' => [],
            ],

            // -----------------------------------------------------------------
            // TIKET SEDANG DIPROSES (Diproses & ditugaskan ke Admin 1 / Admin 2)
            // -----------------------------------------------------------------
            [
                'ticket_number' => 'TIK-BBPOM-007',
                'user_id' => $user3->id, // Budi Santoso
                'handled_by_id' => $admin1->id,
                'category_id' => $categories['printer-scanner']->id,
                'service_key' => 'printer',
                'subcategory' => 'driver_scanner',
                'subject' => 'Instalasi Ulang Driver Scanner Fujitsu Fi-7160 di Meja Arsip Digital',
                'description' => 'Scanner arsip naskah dinas Fujitsu tidak terdeteksi oleh aplikasi kearsipan setelah otomatis update sistem operasi semalam. Mohon dibantu install ulang driver PaperStream IP.',
                'status' => 'diproses',
                'created_at' => Carbon::now()->subDay()->subHours(5),
                'updated_at' => Carbon::now()->subHours(4),
                'responses' => [
                    [
                        'user_id' => $admin1->id,
                        'response_text' => 'Halo Pak Budi, aduan sudah kami terima. Paket installer driver Fujitsu versi 3.10 sedang diunduh dan teknisi akan datang ke meja arsip pukul 13.30.',
                        'created_at' => Carbon::now()->subHours(4),
                    ],
                ],
            ],
            [
                'ticket_number' => 'TIK-BBPOM-008',
                'user_id' => $user8->id, // Ani Lestari
                'handled_by_id' => $admin2->id,
                'category_id' => $categories['aplikasi-software']->id,
                'service_key' => 'office',
                'subcategory' => 'permintaan_lisensi_office',
                'subject' => 'Aktivasi Lisensi Microsoft Office 365 Muncul Peringatan Unlicensed Product',
                'description' => 'Aplikasi Microsoft Word dan Excel di PC Subbag Keuangan tidak bisa mengedit data anggaran karena muncul notifikasi bahwa lisensi Office 365 telah kadaluarsa.',
                'status' => 'diproses',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(1),
                'responses' => [
                    [
                        'user_id' => $admin2->id,
                        'response_text' => 'Aduan dalam penanganan. Kami sedang melakukan refresh assign lisensi Microsoft 365 A3 dari portal admin pusat ke akun Bu Ani.',
                        'created_at' => Carbon::now()->subDays(1),
                    ],
                ],
            ],
            [
                'ticket_number' => 'TIK-BBPOM-009',
                'user_id' => $user2->id, // Siti Rahmawati
                'handled_by_id' => $admin2->id,
                'category_id' => $categories['perangkat-keras-hardware']->id,
                'service_key' => 'hardware',
                'subcategory' => 'monitor_mati',
                'subject' => 'Monitor PC Instrumentasi HPLC Laboratorium Kimia Mati Total',
                'description' => 'Monitor LG pada komputer instrumen kromatografi HPLC Lab Kimia Lantai 1 mati total. Lampu LED power padam. Telah dicoba ganti colokan stop kontak tetapi tetap tidak menyala.',
                'status' => 'diproses',
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(2),
                'responses' => [
                    [
                        'user_id' => $admin2->id,
                        'response_text' => 'Adaptor power 19V cadangan telah disiapkan oleh teknisi hardware. Kami jadwalkan pengujian unit monitor di laboratorium pukul 10.00 WIB.',
                        'created_at' => Carbon::now()->subDays(2),
                    ],
                ],
            ],
            [
                'ticket_number' => 'TIK-BBPOM-010',
                'user_id' => $user4->id, // Dian Kusuma
                'handled_by_id' => $admin1->id,
                'category_id' => $categories['akun-email-keamanan']->id,
                'service_key' => 'bsre',
                'subcategory' => 'perpanjangan_sertifikat',
                'subject' => 'Kendala Sertifikat Elektronik BSrE Expired pada TTE Naskah Dinas',
                'description' => 'Sertifikat TTE Balai Sertifikasi Elektronik (BSrE) BSSN milik saya tidak bisa membubuhkan tanda tangan elektronik pada aplikasi SRIKANDI karena masa berlaku habis per akhir bulan lalu.',
                'status' => 'diproses',
                'created_at' => Carbon::now()->subDays(4),
                'updated_at' => Carbon::now()->subDays(3),
                'responses' => [
                    [
                        'user_id' => $admin1->id,
                        'response_text' => 'Berkas permohonan perpanjangan sertifikat TTE Bu Dian telah kami teruskan ke verifikator BSrE BSSN pusat. Menunggu terbitnya link aktivasi passphrase baru.',
                        'created_at' => Carbon::now()->subDays(3),
                    ],
                ],
            ],
            [
                'ticket_number' => 'TIK-BBPOM-011',
                'user_id' => $user6->id, // Maya Anggraini
                'handled_by_id' => $admin1->id,
                'category_id' => $categories['jaringan-internet']->id,
                'service_key' => 'jaringan',
                'subcategory' => 'penarikan_kabel',
                'subject' => 'Penarikan Titik Kabel LAN Baru di Meja Loket Pengaduan Konsumen',
                'description' => 'Penambahan satu titik kabel jaringan Cat6 dari patch panel switch utama menuju meja loket pengaduan konsumen di lantai 1 untuk mendukung sistem antrean elektronik.',
                'status' => 'diproses',
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(4),
                'responses' => [
                    [
                        'user_id' => $admin1->id,
                        'response_text' => 'Penarikan kabel sepanjang 18 meter dan pemasangan conduit sedang dikerjakan oleh tim infrastruktur TIK.',
                        'created_at' => Carbon::now()->subDays(4),
                    ],
                ],
            ],
            [
                'ticket_number' => 'TIK-BBPOM-012',
                'user_id' => $user3->id, // Budi Santoso
                'handled_by_id' => $admin2->id,
                'category_id' => $categories['aplikasi-software']->id,
                'service_key' => 'aplikasi',
                'subcategory' => 'error_upload',
                'subject' => 'Gagal Mengunggah Berkas Laporan Pengawasan pada Aplikasi Internal (HTTP 500)',
                'description' => 'Saat menyimpan berkas laporan berukuran 2.4 MB di modul pengawasan pangan, muncul halaman HTTP 500 Internal Server Error. Kejadian berulang sejak kemarin siang.',
                'status' => 'diproses',
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(6),
                'responses' => [
                    [
                        'user_id' => $admin2->id,
                        'response_text' => 'Pemeriksaan log web server menemukan limit memory_limit PHP dan max_execution_time yang tercapai. Sedang dikoordinasikan penyesuaian konfigurasi server.',
                        'created_at' => Carbon::now()->subDays(6),
                    ],
                ],
            ],

            // -----------------------------------------------------------------
            // TIKET SELESAI (Tuntas & ditangani oleh Admin 1 / Admin 2)
            // -----------------------------------------------------------------
            [
                'ticket_number' => 'TIK-BBPOM-013',
                'user_id' => $user1->id, // Ahmad Fauzi
                'handled_by_id' => $admin1->id,
                'category_id' => $categories['jaringan-internet']->id,
                'service_key' => 'jaringan',
                'subcategory' => 'koneksi_terputus',
                'subject' => 'Koneksi WiFi Ruang Infokom Terputus Sejak Pagi',
                'description' => 'Koneksi WiFi di Ruang Infokom Lantai 2 tidak bisa diakses dari beberapa laptop pegawai. Lampu indikator Access Point berkedip merah. Mohon bantuan pengecekan switch atau access point.',
                'status' => 'selesai',
                'created_at' => Carbon::now()->subMonths(3),
                'updated_at' => Carbon::now()->subMonths(3)->addHours(4),
                'responses' => [
                    [
                        'user_id' => $admin1->id,
                        'response_text' => 'Halo Pak Ahmad, laporan sudah diterima. Tim teknisi jaringan akan memeriksa switch lantai 2 dalam 15 menit.',
                        'created_at' => Carbon::now()->subMonths(3)->addHours(1),
                    ],
                    [
                        'user_id' => $admin1->id,
                        'response_text' => 'Koneksi kabel LAN ke Access Point telah di-crimp ulang dan restart power switch. Akses internet WiFi sudah kembali stabil normal.',
                        'created_at' => Carbon::now()->subMonths(3)->addHours(3),
                    ],
                    [
                        'user_id' => $user1->id,
                        'response_text' => 'Sudah dicoba dan internet lancar kembali. Terima kasih banyak tim IT BBPOM!',
                        'created_at' => Carbon::now()->subMonths(3)->addHours(4),
                    ],
                ],
            ],
            [
                'ticket_number' => 'TIK-BBPOM-014',
                'user_id' => $user2->id, // Siti Rahmawati
                'handled_by_id' => $admin2->id,
                'category_id' => $categories['akun-email-keamanan']->id,
                'service_key' => 'akun',
                'subcategory' => 'reset_password',
                'subject' => 'Permohonan Reset Password Akun Email Dinas @pom.go.id',
                'description' => 'Saya lupa kata sandi akun email dinas dikarenakan baru selesai masa cuti panjang. Mohon bantuan reset password dan tautan verifikasi ke nomor WA dinas.',
                'status' => 'selesai',
                'created_at' => Carbon::now()->subMonths(2)->subDays(10),
                'updated_at' => Carbon::now()->subMonths(2)->subDays(9),
                'responses' => [
                    [
                        'user_id' => $admin2->id,
                        'response_text' => 'Password sementara telah di-generate dan dikirimkan secara aman via SMS/WA resmi ke nomor pegawai terdaftar. Harap segera lakukan penggantian password saat login pertama kali.',
                        'created_at' => Carbon::now()->subMonths(2)->subDays(9),
                    ],
                ],
            ],
            [
                'ticket_number' => 'TIK-BBPOM-015',
                'user_id' => $user6->id, // Maya Anggraini
                'handled_by_id' => $admin1->id,
                'category_id' => $categories['aplikasi-software']->id,
                'service_key' => 'zoom',
                'subcategory' => 'permintaan_link_zoom',
                'subject' => 'Permintaan Link Zoom Meeting Koordinasi Pengawasan Pangan Terpadu',
                'description' => 'Dibutuhkan link Zoom kapasitas 300 peserta untuk Rapat Koordinasi Lintas Sektor Pengawasan Pangan pada hari Selasa pukul 08.30 WIB.',
                'status' => 'selesai',
                'created_at' => Carbon::now()->subMonths(2)->subDays(4),
                'updated_at' => Carbon::now()->subMonths(2)->subDays(3),
                'responses' => [
                    [
                        'user_id' => $admin1->id,
                        'response_text' => 'Link Zoom Meeting, Passcode, dan Host Key telah dibuat dan dikirimkan ke email resmi pemohon.',
                        'created_at' => Carbon::now()->subMonths(2)->subDays(3),
                    ],
                ],
            ],
            [
                'ticket_number' => 'TIK-BBPOM-016',
                'user_id' => $user4->id, // Dian Kusuma
                'handled_by_id' => $admin2->id,
                'category_id' => $categories['aplikasi-software']->id,
                'service_key' => 'kegiatan',
                'subcategory' => 'permintaan_qrcode',
                'subject' => 'Pembuatan Formulir Presensi Online & QR Code Bimtek UMKM Kosmetik',
                'description' => 'Mohon dibuatkan formulir absensi daring dan QR code cetak untuk kegiatan Bimbingan Teknis CPKB UMKM Kosmetik dengan 80 peserta.',
                'status' => 'selesai',
                'created_at' => Carbon::now()->subMonths(1)->subDays(15),
                'updated_at' => Carbon::now()->subMonths(1)->subDays(14),
                'responses' => [
                    [
                        'user_id' => $admin2->id,
                        'response_text' => 'Formulir presensi online dan file QR Code vektor resolusi tinggi telah siap dan dikirimkan via tautan Google Drive dinas.',
                        'created_at' => Carbon::now()->subMonths(1)->subDays(14),
                    ],
                ],
            ],
            [
                'ticket_number' => 'TIK-BBPOM-017',
                'user_id' => $user8->id, // Ani Lestari
                'handled_by_id' => $admin1->id,
                'category_id' => $categories['perangkat-keras-hardware']->id,
                'service_key' => 'hardware',
                'subcategory' => 'restore_backup',
                'subject' => 'Pemulihan File Backup Rekapitulasi BMN di Network Attached Storage (NAS)',
                'description' => 'Folder rekapitulasi BMN triwulan tidak sengaja terhapus di share folder NAS kantor. Mohon bantuan restore dari snapshot backup semalam.',
                'status' => 'selesai',
                'created_at' => Carbon::now()->subMonths(1)->subDays(8),
                'updated_at' => Carbon::now()->subMonths(1)->subDays(7),
                'responses' => [
                    [
                        'user_id' => $admin1->id,
                        'response_text' => 'Snapshot volume NAS tanggal kemarin pukul 23.00 telah berhasil dipulihkan ke folder pemulihan_BMN. Seluruh data utuh.',
                        'created_at' => Carbon::now()->subMonths(1)->subDays(7),
                    ],
                ],
            ],
            [
                'ticket_number' => 'TIK-BBPOM-018',
                'user_id' => $user5->id, // Rizky Pratama
                'handled_by_id' => $admin2->id,
                'category_id' => $categories['perangkat-keras-hardware']->id,
                'service_key' => 'windows',
                'subcategory' => 'permintaan_install_windows',
                'subject' => 'Instalasi Ulang OS Windows 11 Enterprise pada PC Pengujian Mikrobiologi',
                'description' => 'Komputer pengujian mikrobiologi sering mengalami Blue Screen of Death (BSOD) kode MEMORY_MANAGEMENT. Mohon install ulang Windows 11 resmi kantor.',
                'status' => 'selesai',
                'created_at' => Carbon::now()->subDays(18),
                'updated_at' => Carbon::now()->subDays(17),
                'responses' => [
                    [
                        'user_id' => $admin2->id,
                        'response_text' => 'PC telah di-install ulang Windows 11 Pro 64-bit lengkap dengan driver resmi pabrikan dan antivirus dinas. Pengujian kestabilan berjalan 100% normal.',
                        'created_at' => Carbon::now()->subDays(17),
                    ],
                ],
            ],
            [
                'ticket_number' => 'TIK-BBPOM-019',
                'user_id' => $user7->id, // Hendra Wijaya
                'handled_by_id' => $admin1->id,
                'category_id' => $categories['akun-email-keamanan']->id,
                'service_key' => 'akun',
                'subcategory' => 'unblock_sipt',
                'subject' => 'Pembukaan Blokir Akun SIPT Akibat Salah Masukkan PIN Berulang',
                'description' => 'Akun Sistem Informasi Pengawasan Terpadu terblokir sementara karena salah memasukkan PIN keamanan sebanyak 3 kali. Mohon bantuan buka blokir.',
                'status' => 'selesai',
                'created_at' => Carbon::now()->subDays(12),
                'updated_at' => Carbon::now()->subDays(11),
                'responses' => [
                    [
                        'user_id' => $admin1->id,
                        'response_text' => 'Status blokir akun SIPT Pak Hendra telah di-reset pada database master. Silakan login kembali dengan PIN default yang dikirim via SMS.',
                        'created_at' => Carbon::now()->subDays(11),
                    ],
                ],
            ],
            [
                'ticket_number' => 'TIK-BBPOM-020',
                'user_id' => $user6->id, // Maya Anggraini
                'handled_by_id' => $admin2->id,
                'category_id' => $categories['aplikasi-software']->id,
                'service_key' => 'infografis',
                'subcategory' => 'desain_infografis',
                'subject' => 'Desain Infografis Edukasi Publik Bahaya Bahan Kimia Berbahaya pada Kosmetik',
                'description' => 'Mohon bantuan tim media TIK membuatkan infografis edukasi masyarakat format Instagram carousel 5 slide mengenai ciri-ciri kosmetik bermerkuri.',
                'status' => 'selesai',
                'created_at' => Carbon::now()->subDays(9),
                'updated_at' => Carbon::now()->subDays(8),
                'responses' => [
                    [
                        'user_id' => $admin2->id,
                        'response_text' => 'Desain infografis 5 slide telah selesai dikerjakan sesuai pedoman visual branding Badan POM dan file master PNG siap tayang telah diunggah ke repositori media.',
                        'created_at' => Carbon::now()->subDays(8),
                    ],
                ],
            ],
            [
                'ticket_number' => 'TIK-BBPOM-021',
                'user_id' => $user3->id, // Budi Santoso
                'handled_by_id' => $admin1->id,
                'category_id' => $categories['perangkat-keras-hardware']->id,
                'service_key' => 'hardware',
                'subcategory' => 'hdmi_longgar',
                'subject' => 'Perbaikan Kabel Wallplate HDMI Ruang Sidang Utama yang Longgar',
                'description' => 'Tampilan proyektor sering berkedip dan hilang sinyal saat laptop narasumber tersambung ke soket dinding HDMI. Mohon pengencangan konektor.',
                'status' => 'selesai',
                'created_at' => Carbon::now()->subDays(6),
                'updated_at' => Carbon::now()->subDays(5),
                'responses' => [
                    [
                        'user_id' => $admin1->id,
                        'response_text' => 'Konektor wallplate HDMI telah dibongkar dan diganti dengan modul female-to-female gold-plated baru. Pengujian sinyal Full HD proyektor stabil.',
                        'created_at' => Carbon::now()->subDays(5),
                    ],
                ],
            ],

            // -----------------------------------------------------------------
            // TIKET DITOLAK (Ditolak sesuai SOP Kebijakan TIK BBPOM)
            // -----------------------------------------------------------------
            [
                'ticket_number' => 'TIK-BBPOM-022',
                'user_id' => $user1->id, // Ahmad Fauzi
                'handled_by_id' => $admin1->id,
                'category_id' => $categories['perangkat-keras-hardware']->id,
                'service_key' => 'hardware',
                'subcategory' => 'pengadaan_non_standar',
                'subject' => 'Pengajuan Pengadaan Mouse dan Keyboard Mekanikal Gaming RGB Pribadi',
                'description' => 'Mohon diajukan pengadaan mouse ergonomis dan keyboard mekanik RGB dengan anggaran operasional kantor untuk meja kerja Tata Usaha.',
                'status' => 'ditolak',
                'created_at' => Carbon::now()->subMonths(1)->subDays(10),
                'updated_at' => Carbon::now()->subMonths(1)->subDays(9),
                'responses' => [
                    [
                        'user_id' => $admin1->id,
                        'response_text' => 'Mohon maaf, permohonan ditolak. Sesuai standar logistik BMN dan kebijakan TIK BBPOM, pengadaan periferal meja kerja wajib menggunakan perangkat standar kantor.',
                        'created_at' => Carbon::now()->subMonths(1)->subDays(9),
                    ],
                ],
            ],
            [
                'ticket_number' => 'TIK-BBPOM-023',
                'user_id' => $user5->id, // Rizky Pratama
                'handled_by_id' => $admin2->id,
                'category_id' => $categories['aplikasi-software']->id,
                'service_key' => 'aplikasi',
                'subcategory' => 'software_non_resmi',
                'subject' => 'Permintaan Instalasi Game Online di PC Kerja untuk Refreshing Jam Istirahat',
                'description' => 'Mohon izin dibukakan akses administrator untuk instalasi launcher game kasual di PC kerja untuk digunakan saat istirahat siang.',
                'status' => 'ditolak',
                'created_at' => Carbon::now()->subDays(20),
                'updated_at' => Carbon::now()->subDays(19),
                'responses' => [
                    [
                        'user_id' => $admin2->id,
                        'response_text' => 'Mohon maaf, permohonan ditolak. Instalasi software non-kedinasan termasuk game pada perangkat BMN dilarang keras berdasarkan SOP Keamanan Informasi BBPOM.',
                        'created_at' => Carbon::now()->subDays(19),
                    ],
                ],
            ],
            [
                'ticket_number' => 'TIK-BBPOM-024',
                'user_id' => $user7->id, // Hendra Wijaya
                'handled_by_id' => $admin1->id,
                'category_id' => $categories['akun-email-keamanan']->id,
                'service_key' => 'akun',
                'subcategory' => 'cloud_non_resmi',
                'subject' => 'Permintaan Penggunaan Cloud Storage Pribadi untuk Sinkronisasi Berkas Balai',
                'description' => 'Meminta izin mengaktifkan software sync cloud storage pribadi di PC dinas untuk cadangan dokumen penyidikan.',
                'status' => 'ditolak',
                'created_at' => Carbon::now()->subDays(14),
                'updated_at' => Carbon::now()->subDays(13),
                'responses' => [
                    [
                        'user_id' => $admin1->id,
                        'response_text' => 'Mohon maaf, permohonan ditolak. Seluruh data kedinasan bersifat rahasia dan wajib disimpan pada Network Attached Storage (NAS) lokal atau Google Drive Workspace resmi @pom.go.id.',
                        'created_at' => Carbon::now()->subDays(13),
                    ],
                ],
            ],
            [
                'ticket_number' => 'TIK-BBPOM-025',
                'user_id' => $user4->id, // Dian Kusuma
                'handled_by_id' => $admin2->id,
                'category_id' => $categories['perangkat-keras-hardware']->id,
                'service_key' => 'hardware',
                'subcategory' => 'monitor_ultrawide',
                'subject' => 'Pengajuan Penggantian Monitor Layar Lengkung Gaming Ultrawide 34 Inci',
                'description' => 'Mengajukan penggantian monitor kerja dengan tipe curved ultrawide 34 inci untuk kemudahan membuka banyak jendela tab pemeriksaan.',
                'status' => 'ditolak',
                'created_at' => Carbon::now()->subDays(8),
                'updated_at' => Carbon::now()->subDays(7),
                'responses' => [
                    [
                        'user_id' => $admin2->id,
                        'response_text' => 'Mohon maaf, permohonan ditolak. Spesifikasi monitor tersebut di luar plafon standar sarana prasarana perkantoran. Untuk kebutuhan multi-window, disarankan mengajukan dual monitor 22 inci standar dinas.',
                        'created_at' => Carbon::now()->subDays(7),
                    ],
                ],
            ],

            // -----------------------------------------------------------------
            // TIKET HISTORIS BULANAN (Distribusi Kurva Grafik 4-5 Bulan Terakhir)
            // -----------------------------------------------------------------
            [
                'ticket_number' => 'TIK-BBPOM-026',
                'user_id' => $user2->id,
                'handled_by_id' => $admin1->id,
                'category_id' => $categories['jaringan-internet']->id,
                'service_key' => 'jaringan',
                'subcategory' => 'koneksi_terputus',
                'subject' => 'Optimalisasi Bandwidth Jaringan Laboratorium Pengujian Terpadu',
                'description' => 'Kecepatan upload data pengujian ke server pusat sering terhambat saat jam kerja padat. Mohon pengaturan QoS prioritas lab.',
                'status' => 'selesai',
                'created_at' => Carbon::now()->subMonths(4)->subDays(12),
                'updated_at' => Carbon::now()->subMonths(4)->subDays(10),
                'responses' => [
                    [
                        'user_id' => $admin1->id,
                        'response_text' => 'QoS bandwith prioritas sebesar 40 Mbps telah dialokasikan khusus untuk VLAN Laboratorium pada router mikrotik utama.',
                        'created_at' => Carbon::now()->subMonths(4)->subDays(10),
                    ],
                ],
            ],
            [
                'ticket_number' => 'TIK-BBPOM-027',
                'user_id' => $user3->id,
                'handled_by_id' => $admin2->id,
                'category_id' => $categories['perangkat-keras-hardware']->id,
                'service_key' => 'hardware',
                'subcategory' => 'upgrade_ram',
                'subject' => 'Upgrade Kapasitas RAM PC Server Aplikasi Lokal Balai',
                'description' => 'Memory utilization server database sering menyentuh 95% saat jam kerja aktif. Mohon upgrade modul RAM.',
                'status' => 'selesai',
                'created_at' => Carbon::now()->subMonths(4)->subDays(5),
                'updated_at' => Carbon::now()->subMonths(4)->subDays(4),
                'responses' => [
                    [
                        'user_id' => $admin2->id,
                        'response_text' => 'Penambahan modul RAM DDR4 ECC 32GB telah berhasil dipasang dan performa server kembali optimal di bawah 45%.',
                        'created_at' => Carbon::now()->subMonths(4)->subDays(4),
                    ],
                ],
            ],
            [
                'ticket_number' => 'TIK-BBPOM-028',
                'user_id' => $user6->id,
                'handled_by_id' => $admin1->id,
                'category_id' => $categories['aplikasi-software']->id,
                'service_key' => 'subsite',
                'subcategory' => 'subdomain_baru',
                'subject' => 'Pembuatan Subdomain & Hosting Portal Inovasi Layanan Publik',
                'description' => 'Pengajuan konfigurasi DNS subdomain inovasi.bbpomsemarang.id untuk katalog digital pengujian sampel pihak ketiga.',
                'status' => 'selesai',
                'created_at' => Carbon::now()->subMonths(5)->subDays(15),
                'updated_at' => Carbon::now()->subMonths(5)->subDays(14),
                'responses' => [
                    [
                        'user_id' => $admin1->id,
                        'response_text' => 'DNS A record dan virtual host Nginx untuk subdomain inovasi telah aktif dan terpasang sertifikat SSL Let\'s Encrypt.',
                        'created_at' => Carbon::now()->subMonths(5)->subDays(14),
                    ],
                ],
            ],
            [
                'ticket_number' => 'TIK-BBPOM-029',
                'user_id' => $user8->id,
                'handled_by_id' => $admin2->id,
                'category_id' => $categories['printer-scanner']->id,
                'service_key' => 'printer',
                'subcategory' => 'ganti_toner',
                'subject' => 'Penggantian Toner Cartridge Printer HP LaserJet Ruang Keuangan',
                'description' => 'Hasil cetak dokumen SPJ buram dan bergaris tipis karena status toner HP 85A sudah di bawah 5%.',
                'status' => 'selesai',
                'created_at' => Carbon::now()->subMonths(5)->subDays(2),
                'updated_at' => Carbon::now()->subMonths(5)->subDays(1),
                'responses' => [
                    [
                        'user_id' => $admin2->id,
                        'response_text' => 'Toner cartridge original baru telah dipasang dan hasil print naskah cetak kembali tajam dan pekat.',
                        'created_at' => Carbon::now()->subMonths(5)->subDays(1),
                    ],
                ],
            ],
        ];

        // =========================================================================
        // 4. Insert Tickets & Responses into Database
        // =========================================================================
        foreach ($ticketsData as $item) {
            $responses = $item['responses'] ?? [];
            unset($item['responses']);

            $ticket = Ticket::updateOrCreate(
                ['ticket_number' => $item['ticket_number']],
                $item
            );

            // Seed responses
            foreach ($responses as $resp) {
                TicketResponse::firstOrCreate(
                    [
                        'ticket_id' => $ticket->id,
                        'user_id' => $resp['user_id'],
                        'response_text' => $resp['response_text'],
                    ],
                    [
                        'created_at' => $resp['created_at'],
                        'updated_at' => $resp['created_at'],
                    ]
                );
            }
        }

        // =========================================================================
        // 5. Seed Additional Historical Tickets
        // =========================================================================
        $this->call(ManyTicketsSeeder::class);
    }
}
