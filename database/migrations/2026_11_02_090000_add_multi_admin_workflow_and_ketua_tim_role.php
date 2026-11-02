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
        $driver = DB::getDriverName();

        // 1. Tambahkan role 'ketua_tim' pada tabel users
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'user', 'ketua_tim') NOT NULL DEFAULT 'user'");
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role', 30)->default('user')->change();
            });
        }

        // 2. Perluas status tiket dan tambahkan audit petugas per fase
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM('pending', 'diterima', 'diproses', 'selesai', 'ditolak') NOT NULL DEFAULT 'pending'");
        } else {
            Schema::table('tickets', function (Blueprint $table) {
                $table->string('status', 30)->default('pending')->change();
            });
        }

        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('accepted_by_id')->nullable()->after('handled_by_id')->constrained('users')->nullOnDelete();
            $table->timestamp('accepted_at')->nullable()->after('accepted_by_id');

            $table->foreignId('processed_by_id')->nullable()->after('accepted_at')->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable()->after('processed_by_id');

            $table->foreignId('resolved_by_id')->nullable()->after('processed_at')->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable()->after('resolved_by_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['accepted_by_id']);
            $table->dropForeign(['processed_by_id']);
            $table->dropForeign(['resolved_by_id']);

            $table->dropColumn([
                'accepted_by_id',
                'accepted_at',
                'processed_by_id',
                'processed_at',
                'resolved_by_id',
                'resolved_at',
            ]);
        });

        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM('pending', 'diproses', 'selesai', 'ditolak') NOT NULL DEFAULT 'pending'");
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'user') NOT NULL DEFAULT 'user'");
        }
    }
};
