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
        Schema::table('ticket_responses', function (Blueprint $table) {
            $table->timestamp('read_at')->nullable()->after('response_text');
            $table->index(['ticket_id', 'read_at']);
        });

        // Pesan lama dianggap sudah dibaca agar lonceng tidak langsung menampilkan notifikasi lama.
        DB::table('ticket_responses')->whereNull('read_at')->update(['read_at' => now()]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_responses', function (Blueprint $table) {
            $table->dropIndex(['ticket_id', 'read_at']);
            $table->dropColumn('read_at');
        });
    }
};
