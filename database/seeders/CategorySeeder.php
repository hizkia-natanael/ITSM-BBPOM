<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Mengisi Master Kategori Layanan TIK BBPOM (12 Katalog Layanan Resmi & Kategori Kompatibilitas).
     */
    public function run(): void
    {
        // 1. Seed Master Kategori ITSM (12 Katalog Layanan Resmi dengan ID 1-12)
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

        foreach ($officialCategories as $id => $data) {
            Category::updateOrCreate(
                ['id' => $id],
                ['name' => $data['name'], 'slug' => $data['slug']]
            );
        }

        // 2. Kategori Kompatibilitas untuk Layanan Helpdesk & Integrasi
        $compatCategories = [
            13 => ['name' => 'Jaringan & Konektivitas Internet', 'slug' => 'jaringan-internet'],
            14 => ['name' => 'Perangkat Keras (Hardware & PC)', 'slug' => 'perangkat-keras-hardware'],
            15 => ['name' => 'Aplikasi & Sistem Informasi (Software)', 'slug' => 'aplikasi-software'],
            16 => ['name' => 'Printer, Scanner & Periferal', 'slug' => 'printer-scanner'],
            17 => ['name' => 'Akun, Email & Keamanan Informasi', 'slug' => 'akun-email-keamanan'],
        ];

        foreach ($compatCategories as $id => $data) {
            Category::firstOrCreate(
                ['slug' => $data['slug']],
                ['name' => $data['name']]
            );
        }
    }
}
