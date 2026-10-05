<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\TicketResponse;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ManyTicketsSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'user')->get();
        if ($users->isEmpty()) {
            $users = User::all();
        }

        $admin1 = User::where('email', 'tri.p@bbpom.go.id')->first() ?? User::where('email', 'admin1@bbpom.go.id')->first() ?? User::where('role', 'admin')->first();
        $admin2 = User::where('email', 'windi.p@bbpom.go.id')->first() ?? User::where('email', 'admin2@bbpom.go.id')->first() ?? $admin1;
        $admin3 = User::where('email', 'rizky.a@bbpom.go.id')->first() ?? $admin1;

        $categories = Category::all();
        if ($categories->isEmpty()) {
            $categories = [
                Category::create(['name' => 'Jaringan & Konektivitas Internet', 'slug' => 'jaringan-internet']),
                Category::create(['name' => 'Perangkat Keras (Hardware & PC)', 'slug' => 'perangkat-keras-hardware']),
                Category::create(['name' => 'Aplikasi & Sistem Informasi (Software)', 'slug' => 'aplikasi-software']),
                Category::create(['name' => 'Printer, Scanner & Periferal', 'slug' => 'printer-scanner']),
                Category::create(['name' => 'Akun, Email & Keamanan Informasi', 'slug' => 'akun-email-keamanan']),
            ];
            $categories = collect($categories);
        }

        $sampleTickets = [
            [
                'number' => 'TIK-BBPOM-030',
                'cat' => 'akun-email-keamanan',
                'subject' => 'Permohonan Reset Password Akun Presensi Simpeg Mobile',
                'desc' => 'Akun presensi mobile simpeg terkunci karena lupa kata sandi setelah pergantian HP dinas. Mohon reset.',
                'status' => 'selesai',
                'days_ago' => 25,
                'resp' => 'Password akun simpeg telah direset ke default dan link ganti password dikirim via email.',
            ],
            [
                'number' => 'TIK-BBPOM-031',
                'cat' => 'akun-email-keamanan',
                'subject' => 'Verifikasi Perpanjangan Sertifikat Elektronik BSrE Pegawai Baru',
                'desc' => 'Sertifikat TTE baru untuk verifikator uji naskah dinas siap diaktifkan melalui portal BSSN.',
                'status' => 'selesai',
                'days_ago' => 23,
                'resp' => 'Perpanjangan sertifikat TTE telah diverifikasi dan aktif.',
            ],
            [
                'number' => 'TIK-BBPOM-032',
                'cat' => 'aplikasi-software',
                'subject' => 'Permintaan Akun Zoom Meeting untuk Rapat Koordinasi Evaluasi Balai',
                'desc' => 'Mohon dijadwalkan link Zoom meeting kapasitas 300 peserta untuk hari Kamis jam 09.00 WIB.',
                'status' => 'selesai',
                'days_ago' => 21,
                'resp' => 'Link zoom meeting dan host key telah dikirimkan ke email pemohon.',
            ],
            [
                'number' => 'TIK-BBPOM-033',
                'cat' => 'perangkat-keras-hardware',
                'subject' => 'Perbaikan PC Ruang Infokom Muncul Layar Biru (BSOD)',
                'desc' => 'PC kerja mendadak restart sendiri dan menampilkan error blue screen saat membuka spreadsheet besar.',
                'status' => 'selesai',
                'days_ago' => 19,
                'resp' => 'Modul RAM yang bermasalah telah diganti dengan part baru cadangan BMN.',
            ],
            [
                'number' => 'TIK-BBPOM-034',
                'cat' => 'aplikasi-software',
                'subject' => 'Permohonan Pembuatan Link Presensi Online & QR Code Pelatihan Keamanan Pangan',
                'desc' => 'Dibutuhkan form absensi daring dan generate QR code untuk kegiatan Bimbingan Teknis UMKM Pangan.',
                'status' => 'selesai',
                'days_ago' => 16,
                'resp' => 'Form presensi online dan file QR Code siap cetak telah selesai dibuat.',
            ],
            [
                'number' => 'TIK-BBPOM-035',
                'cat' => 'jaringan-internet',
                'subject' => 'Akses Jaringan WiFi BBPOM-Staff Terputus di Gedung B Lantai 3',
                'desc' => 'Sinyal WiFi full bar tetapi no internet access sejak jam 10 pagi ini.',
                'status' => 'selesai',
                'days_ago' => 14,
                'resp' => 'Kabel uplink Access Point telah diperbaiki dan router switch di-reboot.',
            ],
            [
                'number' => 'TIK-BBPOM-036',
                'cat' => 'aplikasi-software',
                'subject' => 'Permohonan Pembuatan Kuesioner Survey Kepuasan Masyarakat (SKM)',
                'desc' => 'Mohon dibuatkan link kuesioner evaluasi survei kepuasan pelanggan triwulan II dengan 9 indikator.',
                'status' => 'selesai',
                'days_ago' => 12,
                'resp' => 'Survei SKM telah dipublikasikan dan spreadsheet rekap otomatis telah dibagikan ke pemohon.',
            ],
            [
                'number' => 'TIK-BBPOM-037',
                'cat' => 'printer-scanner',
                'subject' => 'Printer Epson L3110 Hasil Cetak Bergaris dan Tinta Putus-Putus',
                'desc' => 'Sudah dilakukan head cleaning mandiri 2x tapi hasil cetak naskah tetap bergaris merah.',
                'status' => 'selesai',
                'days_ago' => 10,
                'resp' => 'Teknisi melakukan deep flush ink dan pembersihan printhead secara manual.',
            ],
            [
                'number' => 'TIK-BBPOM-038',
                'cat' => 'aplikasi-software',
                'subject' => 'Permohonan Subdomain Inovasi Pengujian Sampel Pangan Pihak Ketiga',
                'desc' => 'Dibutuhkan subdomain uji-pangan.bbpomsemarang.id untuk katalog pengujian sampel.',
                'status' => 'selesai',
                'days_ago' => 8,
                'resp' => 'Subdomain dan virtual host web telah terdaftar dan aktif.',
            ],
            [
                'number' => 'TIK-BBPOM-039',
                'cat' => 'perangkat-keras-hardware',
                'subject' => 'Kamera Web Logitech C920 Tidak Terdeteksi saat Zoom Meeting Rapat',
                'desc' => 'Webcam eksternal di ruang rapat kecil tidak muncul di device manager laptop.',
                'status' => 'selesai',
                'days_ago' => 6,
                'resp' => 'Port USB hub di meja rapat diganti dengan port berkekuatan daya baru, kamera berfungsi normal.',
            ],
            [
                'number' => 'TIK-BBPOM-040',
                'cat' => 'perangkat-keras-hardware',
                'subject' => 'Permintaan Akses Instalasi Software Khusus Analisis Spektrofotometri',
                'desc' => 'Membutuhkan hak akses administrator sementara untuk memasang software analisis lab pengujian.',
                'status' => 'selesai',
                'days_ago' => 4,
                'resp' => 'Instalasi telah didampingi langsung oleh teknisi IT dan software siap digunakan.',
            ],
            [
                'number' => 'TIK-BBPOM-041',
                'cat' => 'jaringan-internet',
                'subject' => 'Pemeriksaan Koneksi VPN Kantor yang Sering Mengalami RTO di Jam Sibuk',
                'desc' => 'Akses tunnel VPN BBPOM mengalami lonjakan ping saat membuka aplikasi persuratan dari luar kantor.',
                'status' => 'diproses',
                'days_ago' => 2,
                'resp' => 'Sedang dilakukan pemantauan bandwidth gateway firewall dan routing table.',
            ],
            [
                'number' => 'TIK-BBPOM-042',
                'cat' => 'akun-email-keamanan',
                'subject' => 'Pengecekan Keamanan Laptop Pegawai dari Indikasi Pop-up Malware Browser',
                'desc' => 'Laptop sering muncul popup iklan tak dikenal saat membuka browser dinas. Mohon scanning virus komprehensif.',
                'status' => 'diproses',
                'days_ago' => 1,
                'resp' => 'Full scan corporate antivirus sedang berjalan dan ekstensi browser tidak resmi dinonaktifkan.',
            ],
        ];

        foreach ($sampleTickets as $idx => $t) {
            $cat = Category::where('slug', $t['cat'])->first() ?? $categories->first();
            $assignedUser = $users[$idx % $users->count()];
            $handlingAdmin = ($t['status'] === 'pending') ? null : (($idx % 3 === 0) ? $admin1 : (($idx % 3 === 1) ? $admin2 : $admin3));

            $createdAt = Carbon::now()->subDays($t['days_ago'])->subHours($idx * 2 + 1);

            $ticket = Ticket::updateOrCreate(
                ['ticket_number' => $t['number']],
                [
                    'user_id' => $assignedUser->id,
                    'handled_by_id' => $handlingAdmin?->id,
                    'category_id' => $cat->id,
                    'subject' => $t['subject'],
                    'description' => $t['desc'],
                    'status' => $t['status'],
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt->copy()->addHours(2),
                ]
            );

            if (!empty($t['resp']) && $handlingAdmin) {
                TicketResponse::firstOrCreate(
                    [
                        'ticket_id' => $ticket->id,
                        'user_id' => $handlingAdmin->id,
                        'response_text' => $t['resp'],
                    ],
                    [
                        'created_at' => $createdAt->copy()->addHours(1),
                        'updated_at' => $createdAt->copy()->addHours(1),
                    ]
                );
            }
        }
    }
}
