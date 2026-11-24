<?php

use App\Services\Users\EmployeeEmailSyncService;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Sinkronisasi otomatis email resmi @pom.go.id untuk akun pegawai eksisting di server
     * tanpa menghapus atau mereset tiket, riwayat chat, password, maupun role.
     */
    public function up(): void
    {
        app(EmployeeEmailSyncService::class)->sync();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Pembaruan email resmi pegawai bersifat non-destruktif (in-place).
    }
};
