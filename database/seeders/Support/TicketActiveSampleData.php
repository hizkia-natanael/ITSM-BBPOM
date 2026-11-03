<?php

namespace Database\Seeders\Support;

use Carbon\Carbon;

class TicketActiveSampleData
{
    /**
     * Tiket berstatus pending dan diproses.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function get(): array
    {
        $now = Carbon::now();

        return [
            [
                'ticket_number' => 'TIK-BBPOM-001',
                'user_email' => 'diah.k@bbpom.go.id',
                'handled_by_email' => null,
                'category_id' => 7,
                'service_key' => 'internet',
                'subcategory' => 'koneksi_terputus',
                'subject' => 'Kendala Akses Jaringan LAN Ruang Rapat Pimpinan',
                'description' => "Kabel LAN pada meja ruang rapat pimpinan tidak mendapatkan IP address.\nLampu port switch mati menjelang video conference pimpinan.",
                'status' => 'pending',
                'created_at' => $now->copy()->subMinutes(25),
                'updated_at' => $now->copy()->subMinutes(25),
                'responses' => [],
            ],
            [
                'ticket_number' => 'TIK-BBPOM-002',
                'user_email' => 'a.r@bbpom.go.id',
                'handled_by_email' => 'tri.p@bbpom.go.id',
                'category_id' => 1,
                'service_key' => 'sipt',
                'subcategory' => 'rollback_sampel',
                'subject' => 'Permohonan Rollback Sampel Uji Laboratorium Terpadu',
                'description' => "Mohon bantuan rollback sampel komoditi OT dikarenakan ada revisi formulir pengujian parameter fisika.",
                'status' => 'diproses',
                'created_at' => $now->copy()->subHours(2),
                'updated_at' => $now->copy()->subMinutes(40),
                'responses' => [
                    [
                        'sender_email' => 'tri.p@bbpom.go.id',
                        'text' => 'Halo Pak Rizal, permohonan rollback sampel sudah diverifikasi. Proses unlocking database sedang disiapkan.',
                        'minutes_ago' => 35,
                    ],
                ],
            ],
        ];
    }
}
