<?php

namespace App\Services\Notifications;

use App\Models\Ticket;
use App\Models\TicketResponse;
use App\Models\User;
use Illuminate\Support\Str;

class AdminNotificationFeedService
{
    /**
     * Build unified notification feed data for admin topbar bell.
     *
     * @param User|null $user
     * @return array{count: int, items: array, tickets: array}
     */
    public function getFeed(?User $user): array
    {
        // 1. Aduan belum ditangani (pending & handled_by_id null)
        $unhandledTickets = Ticket::whereNull('handled_by_id')
            ->where('status', 'pending')
            ->with(['user', 'category'])
            ->latest()
            ->take(10)
            ->get();

        $unhandledCount = Ticket::whereNull('handled_by_id')
            ->where('status', 'pending')
            ->count();

        // 2. Chat balasan dari pegawai (pelapor)
        $repliesQuery = TicketResponse::query()
            ->whereHas('user', fn ($q) => $q->where('role', 'user'))
            ->whereHas('ticket', function ($q) use ($user) {
                if ($user) {
                    $q->where('handled_by_id', $user->id)
                      ->orWhereNull('handled_by_id');
                }
            });

        $unreadRepliesCount = (clone $repliesQuery)
            ->whereNull('read_at')
            ->count();

        $latestReplies = $repliesQuery
            ->with(['ticket:id,ticket_number,subject', 'user:id,name'])
            ->latest('id')
            ->take(10)
            ->get();

        $totalCount = $unhandledCount + $unreadRepliesCount;

        // Petakan tiket belum ditangani ke dalam item umpan notifikasi
        $ticketItems = $unhandledTickets->map(function ($t) {
            $userName = $t->user?->name ?? 'Pegawai BBPOM';
            return [
                'id' => 'ticket_' . $t->id,
                'raw_id' => $t->id,
                'item_type' => 'ticket',
                'type' => 'unhandled',
                'title' => 'Aduan Masuk Belum Ditangani',
                'ticket_number' => $t->ticket_number,
                'subject' => $t->subject,
                'sender' => $userName,
                'snippet' => "[{$t->ticket_number}] {$userName}: {$t->subject}",
                'description' => "[{$t->ticket_number}] {$userName}: {$t->subject}",
                'time_ago' => $t->created_at->diffForHumans(),
                'timestamp' => $t->created_at->timestamp,
                'url' => url('/admin/tickets/' . $t->id . '/edit'),
                'edit_url' => url('/admin/tickets/' . $t->id . '/edit'),
                'is_read' => false,
                'can_claim' => true,
                'icon' => 'ri-inbox-archive-line',
                'icon_bg' => 'bg-amber-100 text-amber-700 border border-amber-200/60',
                'badge_color' => 'amber',
            ];
        });

        // Petakan chat pegawai ke item umpan notifikasi
        $replyItems = $latestReplies->map(function ($r) {
            $senderName = $r->user?->name ?? 'Pelapor';
            $snippet = Str::limit(trim((string) preg_replace('/\s+/', ' ', $r->response_text)), 90);
            return [
                'id' => 'reply_' . $r->id,
                'raw_id' => $r->id,
                'item_type' => 'reply',
                'type' => 'reply',
                'title' => 'Pesan Baru dari Pelapor',
                'ticket_number' => $r->ticket->ticket_number,
                'subject' => $r->ticket->subject,
                'sender' => $senderName,
                'snippet' => "[{$r->ticket->ticket_number}] {$senderName}: {$snippet}",
                'description' => "[{$r->ticket->ticket_number}] {$senderName}: {$snippet}",
                'time_ago' => $r->created_at->diffForHumans(),
                'timestamp' => $r->created_at->timestamp,
                'url' => url('/admin/tickets/' . $r->ticket_id . '/edit'),
                'edit_url' => url('/admin/tickets/' . $r->ticket_id . '/edit'),
                'is_read' => $r->read_at !== null,
                'can_claim' => false,
                'icon' => 'ri-chat-3-line',
                'icon_bg' => 'bg-sky-100 text-sky-700 border border-sky-200/60',
                'badge_color' => 'sky',
            ];
        });

        $items = $ticketItems->concat($replyItems)
            ->sort(function ($a, $b) {
                if ($a['is_read'] !== $b['is_read']) {
                    return $a['is_read'] ? 1 : -1;
                }
                return $b['timestamp'] <=> $a['timestamp'];
            })
            ->values()
            ->take(15)
            ->all();

        return [
            'count' => $totalCount,
            'items' => $items,
            'tickets' => $unhandledTickets->map(fn ($t) => [
                'id' => $t->id,
                'ticket_number' => $t->ticket_number,
                'user_name' => $t->user?->name ?? 'Pegawai BBPOM',
                'category' => $t->category?->name ?? 'Layanan TI',
                'subject' => $t->subject,
                'time_ago' => $t->created_at->diffForHumans(),
                'edit_url' => url('/admin/tickets/' . $t->id . '/edit'),
            ])->all(),
        ];
    }

    /**
     * Mark single notification item as read.
     */
    public function markItemRead(string $itemType, int $id): void
    {
        if ($itemType === 'reply') {
            TicketResponse::where('id', $id)->update(['read_at' => now()]);
        }
    }

    /**
     * Mark all replies as read for specified admin user.
     */
    public function markAllRepliesRead(?User $user): void
    {
        TicketResponse::query()
            ->whereHas('user', fn ($q) => $q->where('role', 'user'))
            ->whereHas('ticket', function ($q) use ($user) {
                if ($user) {
                    $q->where('handled_by_id', $user->id)
                      ->orWhereNull('handled_by_id');
                }
            })
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}
