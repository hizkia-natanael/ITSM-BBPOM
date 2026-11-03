<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with comprehensive BBPOM ITSM data.
     * Mengisi akun pegawai resmi, kategori katalog, tiket representatif,
     * serta indeks survei kepuasan bintang 5 sempurna (5.0 / 5.0).
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            TicketSeeder::class,
            TicketSatisfactionSurveySeeder::class,
        ]);
    }
}
