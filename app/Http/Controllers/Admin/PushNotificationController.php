<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PushSubscription;
use App\Models\Ticket;
use App\Models\TicketResponse;
use App\Services\WebPushService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PushNotificationController extends Controller
{
    public function __construct(
        protected WebPushService $webPushService
    ) {}

    /**
     * Get VAPID public key and subscription status for admin.
     */
    public function config(Request $request): JsonResponse
    {
        $user = $request->user();
        $isSubscribed = $user ? $user->pushSubscriptions()->exists() : false;

        return response()->json([
            'publicKey' => $this->webPushService->getVapidPublicKey(),
            'isSubscribed' => $isSubscribed,
        ]);
    }

    /**
     * Store Web Push subscription from Service Worker.
     */
    public function subscribe(Request $request): JsonResponse
    {
        $request->validate([
            'endpoint' => 'required|string',
            'publicKey' => 'nullable|string',
            'authToken' => 'nullable|string',
            'contentEncoding' => 'nullable|string',
        ]);

        $user = $request->user();

        PushSubscription::updateOrCreate(
            [
                'user_id' => $user->id,
                'endpoint' => $request->endpoint,
            ],
            [
                'public_key' => $request->publicKey,
                'auth_token' => $request->authToken,
                'content_encoding' => $request->contentEncoding ?? 'aes128gcm',
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi desktop berhasil diaktifkan.',
        ]);
    }

    /**
     * Remove Web Push subscription.
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        $request->validate([
            'endpoint' => 'required|string',
        ]);

        $user = $request->user();

        PushSubscription::where('user_id', $user->id)
            ->where('endpoint', $request->endpoint)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi desktop telah dinonaktifkan.',
        ]);
    }

    /**
     * Trigger a test notification to logged-in admin.
     */
    public function test(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user && $request->filled('endpoint')) {
            PushSubscription::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'endpoint' => $request->endpoint,
                ],
                [
                    'public_key' => $request->publicKey,
                    'auth_token' => $request->authToken,
                    'content_encoding' => $request->contentEncoding ?? 'aes128gcm',
                ]
            );
            $user->load('pushSubscriptions');
        }

        $sent = $user ? $this->webPushService->sendTestNotificationToUser($user) : 0;

        if ($sent === 0) {
            $sent = $this->webPushService->sendToAdmins(
                'Uji Notifikasi Pop-up Desktop BBPOM',
                'Tes notifikasi pop-up desktop BBPOM berhasil diterima! Sistem siap menampilkan aduan masuk.',
                url('/admin'),
                ['tag' => 'test-notification']
            );
        }

        return response()->json([
            'success' => true,
            'sentCount' => $sent,
            'message' => $sent > 0
                ? "Notifikasi pop-up desktop berhasil dikirim ke perangkat Anda."
                : "Belum ada langganan push desktop aktif di browser ini. Mohon aktifkan izin notifikasi terlebih dahulu.",
        ]);
    }

    /**
     * Get live unhandled tickets count & items for real-time dashboard polling.
     */
    public function unhandledStatus(Request $request): JsonResponse
    {
        $user = $request->user();

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
        // Tiket yang ditangani admin ini atau tiket yang belum ditangani admin mana pun
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

        return response()->json([
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
            ]),
        ]);
    }

    /**
     * Tandai satu notifikasi chat spesifik sebagai terbaca saat diklik oleh admin.
     */
    public function markItemRead(Request $request): JsonResponse
    {
        $request->validate([
            'item_type' => 'required|in:reply,ticket',
            'id' => 'required|integer',
        ]);

        $id = (int) $request->input('id');

        if ($request->input('item_type') === 'reply') {
            TicketResponse::where('id', $id)->update(['read_at' => now()]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Tandai semua chat balasan pegawai sebagai terbaca.
     */
    public function markAllRead(Request $request): JsonResponse
    {
        $user = $request->user();

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

        return response()->json(['success' => true]);
    }

    /**
     * Claim an unhandled ticket directly from topbar notification popover.
     */
    public function claimTicket(Request $request, Ticket $ticket): JsonResponse
    {
        $user = $request->user();

        if ($ticket->handled_by_id !== null || $ticket->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Aduan ini sudah ditangani oleh admin lain.',
            ], 400);
        }

        $ticket->handled_by_id = $user->id;
        $ticket->status = 'diproses';
        $ticket->save();

        return response()->json([
            'success' => true,
            'message' => "Aduan {$ticket->ticket_number} berhasil diambil alih dan berstatus Diproses.",
            'edit_url' => url('/admin/tickets/' . $ticket->id . '/edit'),
        ]);
    }
}
