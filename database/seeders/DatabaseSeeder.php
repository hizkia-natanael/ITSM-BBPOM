<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with comprehensive BBPOM ITSM data.
     * DatabaseSeeder memanggil seluruh seeder modular secara terstruktur:
     * 1. UserSeeder: 179 akun pegawai resmi Berita Acara & akun demo.
     * 2. CategorySeeder: 12 katalog layanan resmi TIK & kategori kompatibilitas.
     * 3. TicketSeeder: 29 tiket aduan komprehensif beserta riwayat penanganannya.
     * 4. ManyTicketsSeeder: Data historis tiket untuk kurva grafik analitik bulanan.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            TicketSeeder::class,
            ManyTicketsSeeder::class,
        ]);
    }
}
