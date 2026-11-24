<?php

namespace App\Console\Commands;

use App\Services\Users\EmployeeEmailSyncService;
use Illuminate\Console\Command;

class SyncEmployeeEmailsCommand extends Command
{
    protected $signature = 'elite:sync-employee-emails';

    protected $description = 'Sinkronisasi in-place email resmi @pom.go.id seluruh pegawai tanpa menghapus tiket atau chat.';

    public function handle(EmployeeEmailSyncService $syncService): int
    {
        $this->info('Memulai sinkronisasi email resmi pegawai (@pom.go.id)...');

        $stats = $syncService->sync();

        $this->info("Akun pegawai diperbarui : {$stats['updated_users']}");
        $this->line("Akun sudah sesuai       : {$stats['unchanged_users']}");
        $this->line("Detail BSrE diperbarui  : {$stats['updated_bsre_details']}");
        $this->line("Deskripsi tiket disinkron: {$stats['updated_ticket_descriptions']}");

        if ($stats['skipped_conflicts'] > 0) {
            $this->warn("Konflik email dilewati   : {$stats['skipped_conflicts']}");
        }

        return self::SUCCESS;
    }
}
