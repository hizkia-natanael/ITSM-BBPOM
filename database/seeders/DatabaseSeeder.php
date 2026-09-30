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
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users (Admin & Pegawai)
        $admin = User::updateOrCreate(
            ['email' => 'admin@bbpom.go.id'],
            [
                'name' => 'Administrator IT BBPOM',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $user1 = User::updateOrCreate(
            ['email' => 'user@bbpom.go.id'],
            [
                'name' => 'Ahmad Pegawai BBPOM',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        $user2 = User::updateOrCreate(
            ['email' => 'siti@bbpom.go.id'],
            [
                'name' => 'Siti Rahmawati (Lab Pengujian)',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        $user3 = User::updateOrCreate(
            ['email' => 'budi@bbpom.go.id'],
            [
                'name' => 'Budi Santoso (Tata Usaha)',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        // 2. Seed Master Kategori ITSM
        $categoriesData = [
            ['name' => 'Jaringan & Konektivitas Internet', 'slug' => 'jaringan-internet'],
            ['name' => 'Perangkat Keras (Hardware & PC)', 'slug' => 'perangkat-keras-hardware'],
            ['name' => 'Aplikasi & Sistem Informasi (Software)', 'slug' => 'aplikasi-software'],
            ['name' => 'Printer, Scanner & Periferal', 'slug' => 'printer-scanner'],
            ['name' => 'Akun, Email & Keamanan Informasi', 'slug' => 'akun-email-keamanan'],
        ];

        $categories = [];
        foreach ($categoriesData as $data) {
            $categories[$data['slug']] = Category::firstOrCreate(
                ['slug' => $data['slug']],
                ['name' => $data['name']]
            );
        }

        // 3. Seed Sample Tickets & Responses
        $ticketsData = [
            [
                'ticket_number' => 'TIK-BBPOM-001',
                'user_id' => $user1->id,
                'category_id' => $categories['jaringan-internet']->id,
                'subject' => 'Koneksi WiFi Ruang Infokom Terputus Sejak Pagi',
                'description' => "Selamat pagi tim IT, koneksi WiFi di Ruang Infokom Lantai 2 tidak bisa diakses dari beberapa laptop pegawai. Indikator lampu di Access Point berkedip merah. Mohon bantuan pengecekan switch atau access point-nya. Terima kasih.",
                'status' => 'selesai',
                'created_at' => Carbon::now()->subMonths(3),
                'updated_at' => Carbon::now()->subMonths(3)->addHours(4),
                'responses' => [
                    [
                        'user_id' => $admin->id,
                        'response_text' => "Halo Pak Ahmad, laporan sudah diterima. Tim teknisi jaringan akan memeriksa switch lantai 2 dalam 15 menit.",
                        'created_at' => Carbon::now()->subMonths(3)->addHours(1),
                    ],
                    [
                        'user_id' => $admin->id,
                        'response_text' => "Koneksi kabel LAN ke Access Point telah di-crimp ulang dan restart power switch. Akses internet WiFi sudah kembali stabil normal.",
                        'created_at' => Carbon::now()->subMonths(3)->addHours(3),
                    ],
                    [
                        'user_id' => $user1->id,
                        'response_text' => "Sudah dicoba dan internet lancar kembali. Terima kasih banyak tim IT BBPOM!",
                        'created_at' => Carbon::now()->subMonths(3)->addHours(4),
                    ],
                ],
            ],
            [
                'ticket_number' => 'TIK-BBPOM-002',
                'user_id' => $user2->id,
                'category_id' => $categories['perangkat-keras-hardware']->id,
                'subject' => 'Monitor PC Laboratorium Kimia Mati Total',
                'description' => "Monitor LG pada PC instrumentasi pengujian kimia di Lab Lantai 1 mati total dan tidak keluar gambar sama sekali saat tombol power ditekan. Lampu indikator power tidak menyala. Mohon bantuan penggantian kabel power atau perbaikan.",
                'status' => 'diproses',
                'created_at' => Carbon::now()->subMonths(2),
                'updated_at' => Carbon::now()->subMonths(2)->addDay(),
                'responses' => [
                    [
                        'user_id' => $admin->id,
                        'response_text' => "Tiket sedang diproses oleh Mas Danu (Teknisi Hardware). Adaptor power cadangan sedang disiapkan untuk pengujian di lokasi lab.",
                        'created_at' => Carbon::now()->subMonths(2)->addHours(2),
                    ],
                ],
            ],
            [
                'ticket_number' => 'TIK-BBPOM-003',
                'user_id' => $user3->id,
                'category_id' => $categories['aplikasi-software']->id,
                'subject' => 'Gagal Mengunggah Berkas BAP pada Aplikasi Internal',
                'description' => "Saat hendak menyimpan dan mengunggah dokumen Berita Acara Pemeriksaan (BAP) PDF ukuran 1.8MB, aplikasi memunculkan notifikasi 'HTTP 500 Internal Server Error'. Kejadian ini berulang sejak kemarin sore.",
                'status' => 'diproses',
                'created_at' => Carbon::now()->subMonth(),
                'updated_at' => Carbon::now()->subMonth()->addHours(6),
                'responses' => [
                    [
                        'user_id' => $admin->id,
                        'response_text' => "Laporan sedang dianalisis pada log server web internal. Dugaan awal terkait batasan post_max_size atau timeout query. Akan kami update secepatnya.",
                        'created_at' => Carbon::now()->subMonth()->addHours(2),
                    ],
                ],
            ],
            [
                'ticket_number' => 'TIK-BBPOM-004',
                'user_id' => $user1->id,
                'category_id' => $categories['printer-scanner']->id,
                'subject' => 'Printer Network Canon di Ruang Subbag Tata Usaha Paper Jam',
                'description' => "Kertas tersangkut di roller penarik printer jaringan Canon ImageRunner. Sudah dicoba ditarik perlahan tetapi indikator error paper jam masih menyala dan tidak bisa print.",
                'status' => 'pending',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
                'responses' => [],
            ],
            [
                'ticket_number' => 'TIK-BBPOM-005',
                'user_id' => $user2->id,
                'category_id' => $categories['akun-email-keamanan']->id,
                'subject' => 'Permohonan Reset Password Akun Email Dinas @pom.go.id',
                'description' => "Saya lupa kata sandi akun email dinas dikarenakan baru selesai masa cuti panjang. Mohon bantuan reset password dan tautan verifikasi ke nomor WA dinas.",
                'status' => 'selesai',
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(9),
                'responses' => [
                    [
                        'user_id' => $admin->id,
                        'response_text' => "Password sementara telah di-generate dan dikirimkan secara aman via SMS/WA resmi ke nomor pegawai terdaftar. Harap segera lakukan penggantian password saat login pertama kali.",
                        'created_at' => Carbon::now()->subDays(9),
                    ],
                ],
            ],
            [
                'ticket_number' => 'TIK-BBPOM-006',
                'user_id' => $user3->id,
                'category_id' => $categories['perangkat-keras-hardware']->id,
                'subject' => 'Permintaan Pembelian Mouse Gaming Pribadi',
                'description' => "Mohon diajukan pengadaan mouse ergonomis dengan spesifikasi gaming untuk operasional harian meja kerja.",
                'status' => 'ditolak',
                'created_at' => Carbon::now()->subDays(15),
                'updated_at' => Carbon::now()->subDays(14),
                'responses' => [
                    [
                        'user_id' => $admin->id,
                        'response_text' => "Mohon maaf, pengadaan perangkat periferal kerja hanya melayani mouse standar dinas kantor BBPOM. Spesifikasi yang diminta berada di luar standar pengadaan TI.",
                        'created_at' => Carbon::now()->subDays(14),
                    ],
                ],
            ],
            [
                'ticket_number' => 'TIK-BBPOM-007',
                'user_id' => $user1->id,
                'category_id' => $categories['jaringan-internet']->id,
                'subject' => 'Permintaan Konfigurasi VPN Kantor untuk Work From Home (WFH)',
                'description' => "Mohon bantuan setting akun VPN dinas BBPOM Semarang pada laptop dinas agar dapat mengakses server file share dari rumah.",
                'status' => 'pending',
                'created_at' => Carbon::now()->subHours(4),
                'updated_at' => Carbon::now()->subHours(4),
                'responses' => [],
            ],
        ];

        foreach ($ticketsData as $item) {
            $responses = $item['responses'] ?? [];
            unset($item['responses']);

            $ticket = Ticket::firstOrCreate(
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
    }
}
