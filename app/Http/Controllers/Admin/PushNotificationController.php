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
        protected AdminNotificationFeedService $feedService,
    ) {}

    public function config(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json([
            'publicKey' => $this->webPushService->getVapidPublicKey(),
            'isSubscribed' => $user ? $user->pushSubscriptions()->exists() : false,
        ]);
    }

    public function subscribe(Request $request): JsonResponse
    {
        $request->validate(['endpoint' => 'required|string', 'publicKey' => 'nullable|string', 'authToken' => 'nullable|string', 'contentEncoding' => 'nullable|string']);
        PushSubscription::updateOrCreate(
            ['user_id' => $request->user()->id, 'endpoint' => $request->endpoint],
            ['public_key' => $request->publicKey, 'auth_token' => $request->authToken, 'content_encoding' => $request->contentEncoding ?? 'aes128gcm']
        );
        return response()->json(['success' => true, 'message' => 'Notifikasi desktop berhasil diaktifkan.']);
    }

    public function unsubscribe(Request $request): JsonResponse
    {
        $request->validate(['endpoint' => 'required|string']);
        PushSubscription::where('user_id', $request->user()->id)
            ->where('endpoint', $request->endpoint)
            ->delete();
        return response()->json(['success' => true, 'message' => 'Notifikasi desktop telah dinonaktifkan.']);
    }

    public function test(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user && $request->filled('endpoint')) {
            PushSubscription::updateOrCreate(
                ['user_id' => $user->id, 'endpoint' => $request->endpoint],
                [
                    'public_key' => $request->publicKey,
                    'auth_token' => $request->authToken,
                    'content_encoding' => $request->contentEncoding ?? 'aes128gcm',
                ]
            );
        }
        $sent = $user ? $this->webPushService->sendToUser(
            $user,
            'Test Notifikasi Desktop',
            'Halo ' . $user->name . ', ini adalah tes notifikasi real-time dari Helpdesk TI BBPOM.',
            url('/admin'),
            ['tag' => 'test-notification']
        ) : 0;
        return response()->json([
            'success' => true,
            'sentCount' => $sent,
            'message' => $sent > 0
                ? 'Notifikasi pop-up desktop berhasil dikirim ke perangkat Anda.'
                : 'Belum ada langganan push desktop aktif di browser ini. Mohon aktifkan izin notifikasi terlebih dahulu.',
        ]);
    }

    public function unhandledStatus(Request $request): JsonResponse
    {
        return response()->json($this->feedService->getFeed($request->user()));
    }

    public function realtimeCheck(Request $request): JsonResponse
    {
        return response()->json($this->feedService->realtimeCheck($request));
    }

    public function markItemRead(Request $request): JsonResponse
    {
        $request->validate(['item_type' => 'required|in:reply,ticket', 'id' => 'required|integer']);
        $this->feedService->markItemRead((string) $request->input('item_type'), (int) $request->input('id'));
        return response()->json(['success' => true]);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $this->feedService->markAllRepliesRead($request->user());
        return response()->json(['success' => true]);
    }

    public function claimTicket(Request $request, Ticket $ticket): JsonResponse
    {
        if ($ticket->handled_by_id !== null || $ticket->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Aduan ini sudah ditangani oleh admin lain.'], 400);
        }
        $ticket->update(['handled_by_id' => $request->user()->id, 'status' => 'diproses']);
        return response()->json([
            'success' => true,
            'message' => "Aduan {$ticket->ticket_number} berhasil diambil alih dan berstatus Diproses.",
            'edit_url' => url('/admin/tickets/' . $ticket->id . '/edit'),
        ]);
    }
}
