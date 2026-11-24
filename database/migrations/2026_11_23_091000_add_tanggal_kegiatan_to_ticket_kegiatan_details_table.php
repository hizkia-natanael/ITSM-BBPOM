<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ticket_kegiatan_details', function (Blueprint $table) {
            $table->date('tanggal_kegiatan')->nullable()->after('nama_acara');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_kegiatan_details', function (Blueprint $table) {
            $table->dropColumn('tanggal_kegiatan');
        });
    }
};
