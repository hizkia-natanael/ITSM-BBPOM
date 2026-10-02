<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PushSubscription;
use App\Models\Ticket;
use App\Services\WebPushService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
    public function unhandledStatus(): JsonResponse
    {
        $unhandledTickets = Ticket::whereNull('handled_by_id')
            ->where('status', 'pending')
            ->with(['user', 'category'])
            ->latest()
            ->take(5)
            ->get();

        $count = Ticket::whereNull('handled_by_id')
            ->where('status', 'pending')
            ->count();

        return response()->json([
            'count' => $count,
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
}
