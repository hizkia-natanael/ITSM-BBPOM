<?php

namespace App\Services\Notifications;

use App\Models\Ticket;
use App\Models\TicketResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminNotificationFeedService
{
    public function getFeed(?User $user): array
    {
        $unhandledTickets = Ticket::whereNull('handled_by_id')->where('status', 'pending')
            ->with(['user', 'category'])->latest()->take(10)->get();
        $unhandledCount = Ticket::whereNull('handled_by_id')->where('status', 'pending')->count();
        $repliesQuery = TicketResponse::whereHas('user', fn ($q) => $q->where('role', 'user'))
            ->whereHas('ticket', function ($q) use ($user) {
                if ($user) {
                    $q->where('handled_by_id', $user->id)->orWhereNull('handled_by_id');
                }
            });
        $unreadRepliesCount = (clone $repliesQuery)->whereNull('read_at')->count();
        $latestReplies = $repliesQuery->with(['ticket:id,ticket_number,subject', 'user:id,name'])->latest('id')->take(10)->get();
        $ticketItems = $unhandledTickets->map(fn ($t) => $this->mapTicketItem($t));
        $replyItems = $latestReplies->map(fn ($r) => $this->mapReplyItem($r));

        $items = $ticketItems->concat($replyItems)
            ->sort(fn ($a, $b) => $a['is_read'] !== $b['is_read'] ? ($a['is_read'] ? 1 : -1) : ($b['timestamp'] <=> $a['timestamp']))
            ->values()->take(15)->all();

        return [
            'count' => $unhandledCount + $unreadRepliesCount,
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

    public function realtimeCheck(Request $request): array
    {
        $hasClientLatestId = $request->has('latest_id');
        $clientLatestId = (int) $request->query('latest_id', 0);
        $totalCount = Ticket::count();
        $latestTicket = Ticket::with('user:id,name')->latest('id')->first(['id', 'ticket_number', 'subject', 'user_id', 'created_at']);
        $latestId = (int) ($latestTicket?->id ?? 0);
        $hasNew = $hasClientLatestId && ($latestId > $clientLatestId);

        $newTickets = $hasNew
            ? Ticket::where('id', '>', $clientLatestId)->with('user:id,name')->latest('id')->take(5)->get()->map(fn ($t) => [
                'id' => $t->id,
                'ticket_number' => $t->ticket_number,
                'subject' => Str::limit($t->subject, 60),
                'user_name' => $t->user?->name ?? 'Pegawai BBPOM',
                'time_ago' => $t->created_at->diffForHumans(),
                'edit_url' => url('/admin/tickets/' . $t->id . '/edit'),
            ])
            : [];

        return ['success' => true, 'latest_id' => $latestId, 'total_count' => $totalCount, 'has_new' => $hasNew, 'new_tickets' => $newTickets];
    }

    public function markItemRead(string $itemType, int $id): void
    {
        if ($itemType === 'reply') {
            TicketResponse::where('id', $id)->update(['read_at' => now()]);
        }
    }

    public function markAllRepliesRead(?User $user): void
    {
        TicketResponse::whereHas('user', fn ($q) => $q->where('role', 'user'))
            ->whereHas('ticket', function ($q) use ($user) {
                if ($user) {
                    $q->where('handled_by_id', $user->id)->orWhereNull('handled_by_id');
                }
            })
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    private function mapTicketItem(Ticket $t): array
    {
        $uName = $t->user?->name ?? 'Pegawai BBPOM';
        $url = url('/admin/tickets/' . $t->id . '/edit');
        return [
            'id' => 'ticket_' . $t->id, 'raw_id' => $t->id, 'item_type' => 'ticket', 'type' => 'unhandled',
            'title' => 'Aduan Masuk Belum Ditangani', 'ticket_number' => $t->ticket_number, 'subject' => $t->subject,
            'sender' => $uName, 'snippet' => "[{$t->ticket_number}] {$uName}: {$t->subject}", 'description' => "[{$t->ticket_number}] {$uName}: {$t->subject}",
            'time_ago' => $t->created_at->diffForHumans(), 'timestamp' => $t->created_at->timestamp,
            'url' => $url, 'edit_url' => $url, 'is_read' => false, 'can_claim' => true,
            'icon' => 'ri-inbox-archive-line', 'icon_bg' => 'bg-amber-100 text-amber-700 border border-amber-200/60', 'badge_color' => 'amber',
        ];
    }

    private function mapReplyItem(TicketResponse $r): array
    {
        $sName = $r->user?->name ?? 'Pelapor';
        $snip = Str::limit(trim((string) preg_replace('/\s+/', ' ', $r->response_text)), 90);
        $url = url('/admin/tickets/' . $r->ticket_id . '/edit');
        return [
            'id' => 'reply_' . $r->id, 'raw_id' => $r->id, 'item_type' => 'reply', 'type' => 'reply',
            'title' => 'Pesan Baru dari Pelapor', 'ticket_number' => $r->ticket->ticket_number, 'subject' => $r->ticket->subject,
            'sender' => $sName, 'snippet' => "[{$r->ticket->ticket_number}] {$sName}: {$snip}", 'description' => "[{$r->ticket->ticket_number}] {$sName}: {$snip}",
            'time_ago' => $r->created_at->diffForHumans(), 'timestamp' => $r->created_at->timestamp,
            'url' => $url, 'edit_url' => $url, 'is_read' => $r->read_at !== null, 'can_claim' => false,
            'icon' => 'ri-chat-3-line', 'icon_bg' => 'bg-sky-100 text-sky-700 border border-sky-200/60', 'badge_color' => 'sky',
        ];
    }
}
