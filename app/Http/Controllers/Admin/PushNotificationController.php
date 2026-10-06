<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PushSubscription;
use App\Models\Ticket;
use App\Services\Notifications\AdminNotificationFeedService;
use App\Services\WebPushService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PushNotificationController extends Controller
{
    public function __construct(
        protected WebPushService $webPushService,
        protected AdminNotificationFeedService $feedService
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
        }

        $sent = 0;
        if ($user) {
            $sent = $this->webPushService->sendToUser(
                $user,
                'Test Notifikasi Desktop',
                'Halo ' . $user->name . ', ini adalah tes notifikasi real-time dari Helpdesk TI BBPOM.',
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
        $feedData = $this->feedService->getFeed($request->user());

        return response()->json($feedData);
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

        $this->feedService->markItemRead(
            (string) $request->input('item_type'),
            (int) $request->input('id')
        );

        return response()->json(['success' => true]);
    }

    /**
     * Tandai semua chat balasan pegawai sebagai terbaca.
     */
    public function markAllRead(Request $request): JsonResponse
    {
        $this->feedService->markAllRepliesRead($request->user());

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
