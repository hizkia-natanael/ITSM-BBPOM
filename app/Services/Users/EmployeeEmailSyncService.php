<?php

namespace App\Services\Users;

use App\Models\Ticket;
use App\Models\TicketBsreDetail;
use App\Models\User;
use Database\Seeders\Support\OfficialEmployeeEmails;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EmployeeEmailSyncService
{
    /**
     * Sinkronisasi in-place email resmi pegawai (@pom.go.id) tanpa menghapus user, tiket, atau chat.
     *
     * @return array{
     *     updated_users: int,
     *     unchanged_users: int,
     *     updated_bsre_details: int,
     *     updated_ticket_descriptions: int,
     *     skipped_conflicts: int
     * }
     */
    public function sync(): array
    {
        $stats = [
            'updated_users' => 0,
            'unchanged_users' => 0,
            'updated_bsre_details' => 0,
            'updated_ticket_descriptions' => 0,
            'skipped_conflicts' => 0,
        ];

        if (! Schema::hasTable('users')) {
            return $stats;
        }

        DB::transaction(function () use (&$stats): void {
            foreach (OfficialEmployeeEmails::all() as $nip => $newEmail) {
                $newEmail = strtolower(trim($newEmail));
                $user = User::where('nip', (string) $nip)->first();

                if (! $user) {
                    continue;
                }

                $conflictExists = User::whereRaw('LOWER(email) = ?', [$newEmail])
                    ->where('id', '!=', $user->id)
                    ->exists();

                if ($conflictExists) {
                    $stats['skipped_conflicts']++;
                    continue;
                }

                $oldEmail = trim((string) $user->email);

                if (strtolower($oldEmail) !== $newEmail) {
                    DB::table('users')
                        ->where('id', $user->id)
                        ->update([
                            'email' => $newEmail,
                            'updated_at' => now(),
                        ]);

                    if (Schema::hasTable('password_reset_tokens') && $oldEmail !== '') {
                        DB::table('password_reset_tokens')->where('email', $oldEmail)->delete();
                    }

                    $stats['updated_users']++;
                    $this->syncExistingTicketEmails($user->id, (string) $nip, $oldEmail, $newEmail, $stats);
                } else {
                    $stats['unchanged_users']++;
                }
            }
        });

        return $stats;
    }

    /**
     * Perbarui referensi teks email lama di tabel detail BSrE dan deskripsi tiket milik pegawai terkait.
     *
     * @param  array<string, int>  $stats
     */
    private function syncExistingTicketEmails(int $userId, string $nip, string $oldEmail, string $newEmail, array &$stats): void
    {
        if ($oldEmail === '') {
            return;
        }

        if (Schema::hasTable('ticket_bsre_details') && Schema::hasTable('tickets')) {
            $ticketIds = Ticket::where('user_id', $userId)->pluck('id');
            $updatedBsre = TicketBsreDetail::query()
                ->where(function ($q) use ($ticketIds, $nip): void {
                    $q->whereIn('ticket_id', $ticketIds)->orWhere('nip', $nip);
                })
                ->whereRaw('LOWER(email) = ?', [strtolower($oldEmail)])
                ->update(['email' => $newEmail]);

            $stats['updated_bsre_details'] += $updatedBsre;
        }

        if (Schema::hasTable('tickets')) {
            $pattern = '/(?<![a-z0-9._%+-])'.preg_quote($oldEmail, '/').'(?![a-z0-9.-])/i';
            $tickets = Ticket::where('user_id', $userId)
                ->where('description', 'like', '%'.$oldEmail.'%')
                ->get(['id', 'description']);

            foreach ($tickets as $ticket) {
                $replaced = preg_replace($pattern, $newEmail, (string) $ticket->description);
                if ($replaced !== null && $replaced !== $ticket->description) {
                    DB::table('tickets')->where('id', $ticket->id)->update(['description' => $replaced]);
                    $stats['updated_ticket_descriptions']++;
                }
            }
        }
    }
}
