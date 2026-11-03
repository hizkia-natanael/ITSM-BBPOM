<?php

namespace Database\Seeders\Support;

class SurveySampleData
{
    /**
     * Data ulasan kepuasan layanan bintang 5 untuk tiket yang berstatus selesai.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function get(): array
    {
        return [
            'TIK-BBPOM-003' => [
                'speed_rating' => 5,
                'quality_rating' => 5,
                'communication_rating' => 5,
                'system_rating' => 5,
                'average_rating' => 5.0,
                'feedback' => 'Pelayanan helpdesk TIK sangat cepat dan solutif. Sinkronisasi jabatan selesai dalam hitungan menit sehingga alur disposisi naskah lancar kembali.',
            ],
            'TIK-BBPOM-004' => [
                'speed_rating' => 5,
                'quality_rating' => 5,
                'communication_rating' => 5,
                'system_rating' => 5,
                'average_rating' => 5.0,
                'feedback' => 'Link Zoom dan hak co-host disiapkan dengan sangat cepat dan rapi. Penjelasan teknis dari admin sangat jelas dan memuaskan.',
            ],
            'TIK-BBPOM-005' => [
                'speed_rating' => 5,
                'quality_rating' => 5,
                'communication_rating' => 5,
                'system_rating' => 5,
                'average_rating' => 5.0,
                'feedback' => 'Sangat responsif dan komunikatif. Verifikasi data perpanjangan sertifikat BSrE dipandu dengan sangat jelas sampai tuntas.',
            ],
            'TIK-BBPOM-006' => [
                'speed_rating' => 5,
                'quality_rating' => 5,
                'communication_rating' => 5,
                'system_rating' => 5,
                'average_rating' => 5.0,
                'feedback' => 'Respon tim helpdesk sangat prima, cepat dan ramah. Sistem ELITE sangat mempermudah pemantauan tiket layanan.',
            ],
            'TIK-BBPOM-007' => [
                'speed_rating' => 5,
                'quality_rating' => 5,
                'communication_rating' => 5,
                'system_rating' => 5,
                'average_rating' => 5.0,
                'feedback' => 'Penanganan profesional dan efisien. Lisensi langsung aktif dan aplikasi perkantoran siap digunakan tanpa kendala.',
            ],
        ];
    }
}
