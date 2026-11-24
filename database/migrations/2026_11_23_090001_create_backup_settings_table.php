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
        Schema::create('backup_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('auto_backup_enabled')->default(true);
            $table->string('schedule_time', 5)->default('02:00');
            $table->unsignedSmallInteger('retention_days')->default(14);
            $table->string('filename_prefix', 50)->default('backup-elite');
            $table->boolean('include_documents')->default(true);
            $table->text('gdrive_client_id')->nullable();
            $table->text('gdrive_client_secret')->nullable();
            $table->text('gdrive_refresh_token')->nullable();
            $table->string('gdrive_folder_id', 128)->nullable();
            $table->string('gdrive_folder_name', 128)->default('Backup-ELITE');
            $table->string('gdrive_account_email', 150)->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backup_settings');
    }
};
