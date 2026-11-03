<?php

namespace Database\Seeders\Support;

use Carbon\Carbon;

class TicketCompletedSampleData
{
    /**
     * Tiket berstatus selesai untuk monitoring dan survei kepuasan.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function get(): array
    {
        $now = Carbon::now();

        return [
            [
                'ticket_number' => 'TIK-BBPOM-003',
                'user_email' => 'a.r@bbpom.go.id',
                'handled_by_email' => 'tri.p@bbpom.go.id',
                'category_id' => 2,
                'service_key' => 'srikandi',
                'subcategory' => 'sinkronisasi_akun',
                'subject' => 'Sinkronisasi Hak Akses Naskah Dinas Elektronik Srikandi',
                'description' => "Naskah dinas masuk belum dapat didisposisikan ke staf penguji. Mohon sinkronisasi hak akses.",
                'status' => 'selesai',
                'created_at' => $now->copy()->subDays(3),
                'updated_at' => $now->copy()->subDays(3)->addHours(2),
                'responses' => [
                    [
                        'sender_email' => 'tri.p@bbpom.go.id',
                        'text' => 'Sinkronisasi jabatan pada portal Srikandi Pusat telah berhasil diperbarui.',
                        'minutes_ago' => 4300,
                    ],
                ],
            ],
            [
                'ticket_number' => 'TIK-BBPOM-004',
                'user_email' => 'diah.k@bbpom.go.id',
                'handled_by_email' => 'rizky.a@bbpom.go.id',
                'category_id' => 5,
                'service_key' => 'zoom',
                'subcategory' => 'permintaan_link_zoom',
                'subject' => 'Permintaan Link Zoom Koordinasi Evaluasi Layanan Balai',
                'description' => "Permohonan link Zoom kapasitas 300 partisipan untuk rapat koordinasi lintas substansi BBPOM Semarang.",
                'status' => 'selesai',
                'created_at' => $now->copy()->subDays(2),
                'updated_at' => $now->copy()->subDays(2)->addHours(1),
                'responses' => [
                    [
                        'sender_email' => 'rizky.a@bbpom.go.id',
                        'text' => 'Tautan Zoom Meeting, Passcode, dan Host Key telah aktif dan dikirimkan ke email resmi pemohon.',
                        'minutes_ago' => 2800,
                    ],
                ],
            ],
            [
                'ticket_number' => 'TIK-BBPOM-005',
                'user_email' => 'ima.d@bbpom.go.id',
                'handled_by_email' => 'windi.p@bbpom.go.id',
                'category_id' => 4,
                'service_key' => 'sertifikat-bsre',
                'subcategory' => 'penerbitan_tte',
                'subject' => 'Pembaruan Sertifikat Elektronik TTE BSrE Terkait Masa Berlaku',
                'description' => "Sertifikat elektronik tanda tangan digital sudah mendekati masa kadaluarsa. Mohon rekomendasi perpanjangan.",
                'status' => 'selesai',
                'created_at' => $now->copy()->subDays(1),
                'updated_at' => $now->copy()->subHours(6),
                'responses' => [
                    [
                        'sender_email' => 'windi.p@bbpom.go.id',
                        'text' => 'Penerbitan ulang sertifikat TTE BSrE telah disetujui dan token verifikasi sudah terkirim.',
                        'minutes_ago' => 360,
                    ],
                ],
            ],
            [
                'ticket_number' => 'TIK-BBPOM-006',
                'user_email' => 'dian.c@bbpom.go.id',
                'handled_by_email' => 'rizky.a@bbpom.go.id',
                'category_id' => 3,
                'service_key' => 'email-corporate',
                'subcategory' => 'reset_password',
                'subject' => 'Reset Kata Sandi Akun Email Dinas Resmi Pegawai',
                'description' => "Akun email dinas terkunci setelah pembaruan berkala sistem. Mohon bantuan reset password resmi.",
                'status' => 'selesai',
                'created_at' => $now->copy()->subDays(4),
                'updated_at' => $now->copy()->subDays(4)->addMinutes(45),
                'responses' => [
                    [
                        'sender_email' => 'rizky.a@bbpom.go.id',
                        'text' => 'Kata sandi baru telah diatur dan instruksi login pertama telah dikirimkan ke kontak resmi.',
                        'minutes_ago' => 5700,
                    ],
                ],
            ],
            [
                'ticket_number' => 'TIK-BBPOM-007',
                'user_email' => 'annisa.a@bbpom.go.id',
                'handled_by_email' => 'windi.p@bbpom.go.id',
                'category_id' => 11,
                'service_key' => 'lisensi-office',
                'subcategory' => 'aktivasi_lisensi',
                'subject' => 'Aktivasi Lisensi Microsoft 365 dan Penyiapan Aplikasi Perkantoran',
                'description' => "PC unit baru memerlukan aktivasi lisensi resmi Microsoft 365 agar dapat membuka format dokumen standar Balai.",
                'status' => 'selesai',
                'created_at' => $now->copy()->subDays(5),
                'updated_at' => $now->copy()->subDays(5)->addHours(3),
                'responses' => [
                    [
                        'sender_email' => 'windi.p@bbpom.go.id',
                        'text' => 'Lisensi Microsoft 365 Enterprise telah diaktifkan pada perangkat pemohon dan verifikasi selesai.',
                        'minutes_ago' => 7100,
                    ],
                ],
            ],
        ];
    }
}
