<?php

namespace App\Http\Controllers;

use App\Services\Notifications\UserNotificationFeedService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserNotificationController extends Controller
{
    public function __construct(
        protected UserNotificationFeedService $feedService,
    ) {}

    public function replies(Request $request): JsonResponse
    {
        return response()->json($this->feedService->getFeed((int) $request->user()->id));
    }

    public function markItemRead(Request $request): JsonResponse
    {
        $request->validate([
            'item_type' => 'required|in:reply,event',
            'id' => 'required|integer',
        ]);

        $this->feedService->markItemRead(
            (int) $request->user()->id,
            (string) $request->input('item_type'),
            (int) $request->input('id')
        );

        return response()->json(['success' => true]);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $this->feedService->markAllRead((int) $request->user()->id);

        return response()->json(['success' => true]);
    }
}
