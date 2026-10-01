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
        Schema::create('ticket_bsre_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->string('sub_category'); // reset_password, permintaan_otp, lainnya
            $table->date('tanggal_pengajuan');
            $table->string('nama');
            $table->string('nip')->nullable();
            $table->string('nik')->nullable();
            $table->string('email')->nullable();
            $table->string('nomor_telepon')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('file_lampiran')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_bsre_details');
    }
};
