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
        Schema::create('database_backups', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('local_path')->nullable();
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->string('trigger_type', 20)->default('scheduled');
            $table->string('status', 20)->default('pending');
            $table->string('gdrive_status', 20)->default('pending');
            $table->string('gdrive_file_id', 128)->nullable();
            $table->string('gdrive_web_url', 255)->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('local_deleted_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('database_backups');
    }
};
