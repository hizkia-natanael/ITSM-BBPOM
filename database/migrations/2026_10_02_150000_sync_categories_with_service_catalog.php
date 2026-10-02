<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $catalogCategories = [
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

        Schema::disableForeignKeyConstraints();

        // 1. Insert or update 12 official categories with explicit IDs 1 to 12
        $categoryMap = [];
        DB::table('categories')->truncate();

        foreach ($catalogCategories as $id => $data) {
            DB::table('categories')->insert([
                'id' => $id,
                'name' => $data['name'],
                'slug' => $data['slug'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $categoryMap[$data['slug']] = $id;
        }

        if (DB::getDriverName() === 'sqlite') {
            DB::statement("DELETE FROM sqlite_sequence WHERE name = 'categories'");
            DB::statement("INSERT INTO sqlite_sequence (name, seq) VALUES ('categories', 12)");
        }

        // Also map snake_case service keys to slug
        $serviceKeyToSlug = [
            'sipt' => 'sipt',
            'srikandi' => 'srikandi',
            'email_corporate' => 'email-corporate',
            'email-corporate' => 'email-corporate',
            'sertifikat_bse' => 'sertifikat-bsre',
            'sertifikat-bsre' => 'sertifikat-bsre',
            'zoom' => 'zoom',
            'formulir_kegiatan' => 'formulir-kegiatan',
            'formulir-kegiatan' => 'formulir-kegiatan',
            'internet' => 'internet',
            'subsite' => 'subsite',
            'infografis' => 'infografis',
            'install_windows' => 'install-windows',
            'install-windows' => 'install-windows',
            'lisensi_office' => 'lisensi-office',
            'lisensi-office' => 'lisensi-office',
            'lainnya' => 'lainnya',
        ];

        // 2. Remap existing tickets by service_key or relations
        $tickets = DB::table('tickets')->get();
        foreach ($tickets as $ticket) {
            $serviceKey = $ticket->service_key;

            if (empty($serviceKey)) {
                // Check details
                if (DB::table('ticket_bsre_details')->where('ticket_id', $ticket->id)->exists()) {
                    $serviceKey = 'sertifikat_bse';
                } elseif (DB::table('ticket_zoom_details')->where('ticket_id', $ticket->id)->exists()) {
                    $serviceKey = 'zoom';
                } elseif (DB::table('ticket_kegiatan_details')->where('ticket_id', $ticket->id)->exists()) {
                    $serviceKey = 'formulir_kegiatan';
                } elseif (DB::table('ticket_windows_details')->where('ticket_id', $ticket->id)->exists()) {
                    $serviceKey = 'install_windows';
                } elseif (DB::table('ticket_office_details')->where('ticket_id', $ticket->id)->exists()) {
                    $serviceKey = 'lisensi_office';
                } elseif (DB::table('ticket_lainnya_details')->where('ticket_id', $ticket->id)->exists()) {
                    $serviceKey = 'lainnya';
                } else {
                    // Check old category
                    $oldCat = DB::table('categories')->where('id', $ticket->category_id)->first();
                    $oldSlug = $oldCat ? $oldCat->slug : '';
                    if (str_contains($oldSlug, 'jaringan') || str_contains($oldSlug, 'internet')) {
                        $serviceKey = 'internet';
                    } elseif (str_contains($oldSlug, 'hardware') || str_contains($oldSlug, 'perangkat')) {
                        $serviceKey = 'install_windows';
                    } elseif (str_contains($oldSlug, 'aplikasi') || str_contains($oldSlug, 'software')) {
                        $serviceKey = 'sipt';
                    } elseif (str_contains($oldSlug, 'akun') || str_contains($oldSlug, 'email')) {
                        $serviceKey = 'email_corporate';
                    } else {
                        $serviceKey = 'lainnya';
                    }
                }
            }

            $targetSlug = $serviceKeyToSlug[$serviceKey] ?? 'lainnya';
            $targetCatId = $categoryMap[$targetSlug] ?? $categoryMap['lainnya'];

            DB::table('tickets')->where('id', $ticket->id)->update([
                'category_id' => $targetCatId,
                'service_key' => $serviceKey,
            ]);
        }

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reversible if needed
    }
};
