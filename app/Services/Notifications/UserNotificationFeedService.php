<?php

namespace App\Services\Notifications;

use App\Models\TicketEvent;
use App\Models\TicketResponse;
use Illuminate\Support\Str;

class UserNotificationFeedService
{
    public function getFeed(int $userId): array
    {
        $unreadResponsesCount = TicketResponse::whereNull('read_at')
            ->where('user_id', '!=', $userId)
            ->whereHas('ticket', fn ($q) => $q->where('user_id', $userId))
            ->count();
        $unreadEventsCount = TicketEvent::whereNull('read_at')
            ->whereHas('ticket', fn ($q) => $q->where('user_id', $userId))
            ->count();
        $responses = TicketResponse::where('user_id', '!=', $userId)
            ->whereHas('ticket', fn ($q) => $q->where('user_id', $userId))
            ->with(['ticket:id,ticket_number,subject', 'user:id,name'])
            ->latest('id')->take(15)->get()
            ->map(fn (TicketResponse $r) => $this->mapResponseItem($r));
        $events = TicketEvent::whereHas('ticket', fn ($q) => $q->where('user_id', $userId))
            ->with(['ticket:id,ticket_number,subject', 'actor:id,name'])
            ->latest('id')->take(15)->get()
            ->map(fn (TicketEvent $e) => $this->mapEventItem($e));

        $items = $responses->concat($events)
            ->sort(fn ($a, $b) => $a['is_read'] !== $b['is_read'] ? ($a['is_read'] ? 1 : -1) : ($b['timestamp'] <=> $a['timestamp']))
            ->values()->take(15)->all();

        return ['count' => $unreadResponsesCount + $unreadEventsCount, 'items' => $items];
    }

    public function markItemRead(int $userId, string $itemType, int $id): void
    {
        if ($itemType === 'reply') {
            TicketResponse::where('id', $id)
                ->where('user_id', '!=', $userId)
                ->whereHas('ticket', fn ($q) => $q->where('user_id', $userId))
                ->update(['read_at' => now()]);
        } else {
            TicketEvent::where('id', $id)
                ->whereHas('ticket', fn ($q) => $q->where('user_id', $userId))
                ->update(['read_at' => now()]);
        }
    }

    public function markAllRead(int $userId): void
    {
        TicketResponse::whereNull('read_at')
            ->where('user_id', '!=', $userId)
            ->whereHas('ticket', fn ($q) => $q->where('user_id', $userId))
            ->update(['read_at' => now()]);

        TicketEvent::whereNull('read_at')
            ->whereHas('ticket', fn ($q) => $q->where('user_id', $userId))
            ->update(['read_at' => now()]);
    }

    private function mapResponseItem(TicketResponse $r): array
    {
        $snippet = Str::limit(trim((string) preg_replace('/\s+/', ' ', $r->response_text)), 90);
        return [
            'id' => 'reply_' . $r->id,
            'raw_id' => $r->id,
            'item_type' => 'reply',
            'type' => 'reply',
            'title' => 'Balasan Baru dari Tim IT',
            'ticket_number' => $r->ticket->ticket_number,
            'subject' => $r->ticket->subject,
            'sender' => $r->user?->name ?? 'Tim IT',
            'snippet' => $snippet,
            'description' => "[{$r->ticket->ticket_number}] {$snippet}",
            'time_ago' => $r->created_at->diffForHumans(),
            'created_at' => $r->created_at->toIso8601String(),
            'timestamp' => $r->created_at->timestamp,
            'url' => route('tickets.show', $r->ticket_id),
            'is_read' => $r->read_at !== null,
            'icon' => 'ri-chat-3-line',
            'icon_bg' => 'bg-sky-100 text-sky-700 border border-sky-200',
            'badge_color' => 'sky',
        ];
    }

    private function mapEventItem(TicketEvent $e): array
    {
        [$title, $desc, $icon, $iconBg, $badgeColor] = match ($e->status) {
            'diproses' => ['Pengaduan Diproses', 'Tim IT mulai menangani pengaduan ini.', 'ri-loader-4-line', 'bg-amber-100 text-amber-700 border border-amber-200', 'amber'],
            'selesai' => ['Pengaduan Selesai', 'Pengaduan ini telah berhasil diselesaikan.', 'ri-checkbox-circle-line', 'bg-emerald-100 text-emerald-700 border border-emerald-200', 'emerald'],
            'ditolak' => ['Pengaduan Ditolak', 'Pengaduan ini tidak dapat ditindaklanjuti.', 'ri-close-circle-line', 'bg-rose-100 text-rose-700 border border-rose-200', 'rose'],
            default => ['Pembaruan Tiket', 'Status pengaduan Anda telah diperbarui.', 'ri-notification-3-line', 'bg-slate-100 text-slate-700 border border-slate-200', 'slate'],
        };

        return [
            'id' => 'event_' . $e->id,
            'raw_id' => $e->id,
            'item_type' => 'event',
            'type' => $e->status,
            'title' => $title,
            'ticket_number' => $e->ticket->ticket_number,
            'subject' => $e->ticket->subject,
            'sender' => $e->actor?->name ?? 'Tim IT',
            'snippet' => "[{$e->ticket->ticket_number}] {$e->ticket->subject}: {$desc}",
            'description' => "[{$e->ticket->ticket_number}] {$e->ticket->subject}: {$desc}",
            'time_ago' => $e->created_at->diffForHumans(),
            'created_at' => $e->created_at->toIso8601String(),
            'timestamp' => $e->created_at->timestamp,
            'url' => route('tickets.show', $e->ticket_id),
            'is_read' => $e->read_at !== null,
            'icon' => $icon,
            'icon_bg' => $iconBg,
            'badge_color' => $badgeColor,
        ];
    }
}
