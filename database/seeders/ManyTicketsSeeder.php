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
        $user = User::where('email', 'user@bbpom.go.id')->first();
        if (!$user) {
            $user = User::first();
        }

        $admin = User::where('role', 'admin')->first() ?? $user;

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
                'number' => 'TIK-BBPOM-008',
                'cat' => 'akun-email-keamanan',
                'subject' => 'Permohonan Reset Password Akun Presensi Simpeg',
                'desc' => 'Akun presensi mobile simpeg terkunci karena lupa kata sandi setelah pergantian hp dinas. Mohon reset.',
                'status' => 'selesai',
                'days_ago' => 20,
                'resp' => 'Password akun simpeg telah direset ke default dan link ganti password dikirim via email.',
            ],
            [
                'number' => 'TIK-BBPOM-009',
                'cat' => 'akun-email-keamanan',
                'subject' => 'Kendala Sertifikat Elektronik BSrE Expired pada TTE Naskah Dinas',
                'desc' => 'Saat menandatangani dokumen dinas di aplikasi persuratan muncul notifikasi sertifikat BSrE kadaluarsa.',
                'status' => 'diproses',
                'days_ago' => 18,
                'resp' => 'Sedang diverifikasi ke portal BSrE BSSN untuk penerbitan perpanjangan sertifikat elektronik.',
            ],
            [
                'number' => 'TIK-BBPOM-010',
                'cat' => 'aplikasi-software',
                'subject' => 'Permintaan Akun Zoom Meeting untuk Rapat Koordinasi Balai',
                'desc' => 'Mohon dijadwalkan link Zoom meeting kapasitas 300 peserta untuk hari Kamis jam 09.00 WIB.',
                'status' => 'selesai',
                'days_ago' => 16,
                'resp' => 'Link zoom meeting dan host key telah dikirimkan ke email pemohon.',
            ],
            [
                'number' => 'TIK-BBPOM-011',
                'cat' => 'perangkat-keras-hardware',
                'subject' => 'Perbaikan PC Ruang Infokom Muncul Layar Biru (BSOD)',
                'desc' => 'PC kerja mendadak restart sendiri dan menampilkan error blue screen saat membuka spreadsheet besar.',
                'status' => 'pending',
                'days_ago' => 15,
                'resp' => null,
            ],
            [
                'number' => 'TIK-BBPOM-012',
                'cat' => 'aplikasi-software',
                'subject' => 'Permohonan Pembuatan Link Presensi Online & QR Code Bimtek',
                'desc' => 'Dibutuhkan form absensi daring dan generate QR code untuk kegiatan Bimbingan Teknis UMKM Pangan.',
                'status' => 'selesai',
                'days_ago' => 14,
                'resp' => 'Form presensi online dan file QR Code siap cetak telah selesai dibuat.',
            ],
            [
                'number' => 'TIK-BBPOM-013',
                'cat' => 'aplikasi-software',
                'subject' => 'Lisensi Microsoft Office 365 Muncul Notifikasi Unlicensed Product',
                'desc' => 'Word dan Excel tidak bisa menyimpan dokumen karena ada peringatan aktivasi lisensi produk.',
                'status' => 'diproses',
                'days_ago' => 12,
                'resp' => 'Akun Microsoft 365 sedang direfresh kuota lisensinya oleh admin TIK.',
            ],
            [
                'number' => 'TIK-BBPOM-014',
                'cat' => 'printer-scanner',
                'subject' => 'Instalasi Ulang Driver Scanner Fujitsu Fi-7160 di Meja Arsip',
                'desc' => 'Scanner arsip tidak terbaca di komputer setelah update Windows 11 otomatis semalam.',
                'status' => 'selesai',
                'days_ago' => 11,
                'resp' => 'Driver PaperStream IP Fujitsu versi terbaru telah diinstal dan diuji coba berjalan normal.',
            ],
            [
                'number' => 'TIK-BBPOM-015',
                'cat' => 'jaringan-internet',
                'subject' => 'Akses Jaringan WiFi BBPOM-Staff Terputus di Gedung B Lantai 3',
                'desc' => 'Sinyal WiFi full bar tetapi no internet access sejak jam 10 pagi ini.',
                'status' => 'pending',
                'days_ago' => 10,
                'resp' => null,
            ],
            [
                'number' => 'TIK-BBPOM-016',
                'cat' => 'aplikasi-software',
                'subject' => 'Permohonan Pembuatan Kuesioner Survey Kepuasan Masyarakat (SKM)',
                'desc' => 'Mohon dibuatkan link kuesioner evaluasi survei kepuasan pelanggan triwulan II dengan 9 indikator.',
                'status' => 'selesai',
                'days_ago' => 9,
                'resp' => 'Survei SKM telah dipublikasikan dan spreadsheet rekap otomatis telah dibagikan ke pemohon.',
            ],
            [
                'number' => 'TIK-BBPOM-017',
                'cat' => 'akun-email-keamanan',
                'subject' => 'Permintaan Akun Email Dinas Baru untuk Pegawai Baru',
                'desc' => 'Pengajuan pembuatan akun email resmi domain @pom.go.id bagi staf administrasi yang baru bertugas.',
                'status' => 'selesai',
                'days_ago' => 8,
                'resp' => 'Akun email dinas telah diaktifkan di mail server pusat.',
            ],
            [
                'number' => 'TIK-BBPOM-018',
                'cat' => 'printer-scanner',
                'subject' => 'Printer Epson L3110 Hasil Cetak Bergaris dan Tinta Putus-Putus',
                'desc' => 'Sudah dilakukan head cleaning mandiri 2x tapi hasil cetak naskah tetap bergaris merah.',
                'status' => 'diproses',
                'days_ago' => 7,
                'resp' => 'Teknisi sedang melakukan deep flush ink dan pembersihan printhead secara manual.',
            ],
            [
                'number' => 'TIK-BBPOM-019',
                'cat' => 'aplikasi-software',
                'subject' => 'Permohonan Pembuatan Subdomain & Subsite Inovasi Layanan',
                'desc' => 'Dibutuhkan subdomain inovasi.bbpomsemarang.id untuk katalog pengujian sampel pangan.',
                'status' => 'pending',
                'days_ago' => 6,
                'resp' => null,
            ],
            [
                'number' => 'TIK-BBPOM-020',
                'cat' => 'aplikasi-software',
                'subject' => 'Desain Infografis Layanan Publik Pengawasan Obat Tradisional',
                'desc' => 'Mohon bantuan tim media TIK untuk pembuatan infografis edukasi publik format media sosial.',
                'status' => 'selesai',
                'days_ago' => 5,
                'resp' => 'Desain grafis selesai dan file resolusi tinggi telah diunggah ke Google Drive dinas.',
            ],
            [
                'number' => 'TIK-BBPOM-021',
                'cat' => 'perangkat-keras-hardware',
                'subject' => 'Permintaan Pemulihan File Backup di Network Shared Folder (NAS)',
                'desc' => 'Folder laporan triwulan tidak sengaja ter-replace oleh staf lain, mohon bantuan restore snapshot semalam.',
                'status' => 'selesai',
                'days_ago' => 4,
                'resp' => 'Snapshot volume NAS tanggal kemarin jam 23.00 telah direstore ke folder pemulihan.',
            ],
            [
                'number' => 'TIK-BBPOM-022',
                'cat' => 'perangkat-keras-hardware',
                'subject' => 'Kamera Web Logitech C920 Tidak Terdeteksi saat Zoom Meeting',
                'desc' => 'Webcam eksternal di ruang rapat kecil tidak muncul di device manager laptop.',
                'status' => 'selesai',
                'days_ago' => 3,
                'resp' => 'Port USB hub di meja rapat diganti dengan port berkekuatan daya baru, kamera berfungsi normal.',
            ],
            [
                'number' => 'TIK-BBPOM-023',
                'cat' => 'perangkat-keras-hardware',
                'subject' => 'Permintaan Akses Instalasi Software Khusus Analisis Spektrofotometri',
                'desc' => 'Membutuhkan hak akses administrator sementara untuk memasang software analisis lab pengujian.',
                'status' => 'selesai',
                'days_ago' => 2,
                'resp' => 'Instalasi telah didampingi langsung oleh teknisi IT dan software siap digunakan.',
            ],
            [
                'number' => 'TIK-BBPOM-024',
                'cat' => 'jaringan-internet',
                'subject' => 'Pemasangan Titik Kabel LAN Baru di Ruang Pelayanan Publik',
                'desc' => 'Penambahan satu titik kabel data Cat6 dari switch utama menuju meja loket pengaduan konsumen.',
                'status' => 'diproses',
                'days_ago' => 1,
                'resp' => 'Penarikan kabel dan crimping RJ45 sedang dalam pengerjaan teknisi jaringan.',
            ],
            [
                'number' => 'TIK-BBPOM-025',
                'cat' => 'perangkat-keras-hardware',
                'subject' => 'Pengajuan Penggantian Keyboard Mekanikal Gaming RGB',
                'desc' => 'Mohon dibelikan keyboard mekanik RGB untuk kenyamanan mengetik laporan.',
                'status' => 'ditolak',
                'days_ago' => 1,
                'resp' => 'Mohon maaf, pengadaan periferal dinas harus sesuai standar logistik kantor BBPOM (standar membrane office).',
            ],
            [
                'number' => 'TIK-BBPOM-026',
                'cat' => 'aplikasi-software',
                'subject' => 'Aplikasi SIPT Terkunci Akibat Salah Masukkan PIN Berulang',
                'desc' => 'Akun Sistem Informasi Pengawasan Terpadu terblokir sementara karena salah PIN 3 kali.',
                'status' => 'selesai',
                'days_ago' => 1,
                'resp' => 'Status blokir akun SIPT telah dibuka kembali oleh admin.',
            ],
            [
                'number' => 'TIK-BBPOM-027',
                'cat' => 'perangkat-keras-hardware',
                'subject' => 'Kabel HDMI Wallplate Ruang Sidang Utama Longgar',
                'desc' => 'Tampilan proyektor sering kedap-kedip saat proyektor tersambung ke laptop narasumber.',
                'status' => 'selesai',
                'days_ago' => 0,
                'resp' => 'Konektor wallplate HDMI telah dikencangkan dan kabel extender diganti baru.',
            ],
            [
                'number' => 'TIK-BBPOM-028',
                'cat' => 'akun-email-keamanan',
                'subject' => 'Pengecekan Keamanan Laptop Pegawai dari Indikasi Malware',
                'desc' => 'Laptop sering muncul popup iklan tak dikenal saat membuka browser dinas. Mohon scanning virus.',
                'status' => 'diproses',
                'days_ago' => 0,
                'resp' => 'Full scan antivirus corporate sedang dijalankan dan browser extensions mencurigakan dinonaktifkan.',
            ],
            [
                'number' => 'TIK-BBPOM-029',
                'cat' => 'jaringan-internet',
                'subject' => 'Koneksi VPN Kantor Sering RTO (Request Time Out) di Jam Sibuk',
                'desc' => 'Akses tunnel VPN BBPOM mengalami lonjakan ping dan lambat saat akses database naskah dinas.',
                'status' => 'pending',
                'days_ago' => 0,
                'resp' => null,
            ],
        ];

        foreach ($sampleTickets as $idx => $t) {
            $cat = Category::where('slug', $t['cat'])->first() ?? $categories->first();
            $createdAt = Carbon::now()->subDays($t['days_ago'])->subHours($idx * 2)->subMinutes($idx * 7);

            $ticket = Ticket::updateOrCreate(
                ['ticket_number' => $t['number']],
                [
                    'user_id' => $user->id,
                    'category_id' => $cat->id,
                    'subject' => $t['subject'],
                    'description' => $t['desc'],
                    'status' => $t['status'],
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt->copy()->addHours(2),
                ]
            );

            if (!empty($t['resp'])) {
                TicketResponse::firstOrCreate(
                    [
                        'ticket_id' => $ticket->id,
                        'user_id' => $admin->id,
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
