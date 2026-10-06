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
        Schema::create('ticket_response_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_response_id')->constrained('ticket_responses')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_name');
            $table->unsignedBigInteger('file_size');
            $table->unsignedBigInteger('original_size')->default(0);
            $table->string('mime_type')->nullable();
            $table->string('compression_mode')->default('compressed'); // 'compressed' or 'original'
            $table->boolean('is_compressed')->default(false);
            $table->unsignedBigInteger('saved_bytes')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_response_attachments');
    }
};
