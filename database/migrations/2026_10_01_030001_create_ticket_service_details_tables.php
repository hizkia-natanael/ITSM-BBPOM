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
        // 1. Detail Layanan Zoom
        Schema::create('ticket_zoom_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->string('sub_category', 50); // permintaan_link_zoom, lainnya
            $table->date('tanggal_pengajuan');
            $table->string('nama');
            $table->string('nama_acara')->nullable();
            $table->string('jam')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('file_lampiran')->nullable();
            $table->timestamps();
        });

        // 2. Detail Layanan Formulir & Kegiatan (Daftar Hadir, QR Code, Survey, Pretest/Posttest, Lainnya)
        Schema::create('ticket_kegiatan_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->string('sub_category', 50); // permintaan_daftar_hadir, permintaan_qrcode, survey, post_test_pretest, lainnya
            $table->date('tanggal_pengajuan');
            $table->string('nama');
            $table->string('nama_acara')->nullable();
            $table->string('jam')->nullable();
            $table->string('link_qrcode', 500)->nullable();
            $table->string('judul_survey')->nullable();
            $table->string('judul_acara')->nullable();
            $table->string('jenis_test', 20)->nullable(); // pretest, post_test
            $table->json('pertanyaan_data')->nullable(); // array of questions & answer types
            $table->text('deskripsi')->nullable();
            $table->string('file_lampiran')->nullable();
            $table->timestamps();
        });

        // 3. Detail Layanan Install Ulang Windows
        Schema::create('ticket_windows_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->string('sub_category', 50); // permintaan_install_windows, lainnya
            $table->date('tanggal_pengajuan');
            $table->string('nama');
            $table->string('bidang', 50)->nullable(); // Tata Usaha, Pengujian, Penindakan, Infokom, Pemeriksaan
            $table->string('merk_perangkat')->nullable();
            $table->string('kode_bmn', 100)->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('file_lampiran')->nullable();
            $table->timestamps();
        });

        // 4. Detail Layanan Lisensi Microsoft Office
        Schema::create('ticket_office_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->string('sub_category', 50); // permintaan_lisensi_office, lainnya
            $table->date('tanggal_pengajuan');
            $table->string('nama');
            $table->string('bidang', 50)->nullable(); // Tata Usaha, Pengujian, Penindakan, Infokom, Pemeriksaan
            $table->string('merk_perangkat')->nullable();
            $table->string('kode_bmn', 100)->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('file_lampiran')->nullable();
            $table->timestamps();
        });

        // 5. Detail Layanan Lainnya
        Schema::create('ticket_lainnya_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->date('tanggal_pengajuan');
            $table->string('nama');
            $table->text('deskripsi');
            $table->string('file_lampiran')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_lainnya_details');
        Schema::dropIfExists('ticket_office_details');
        Schema::dropIfExists('ticket_windows_details');
        Schema::dropIfExists('ticket_kegiatan_details');
        Schema::dropIfExists('ticket_zoom_details');
    }
};
