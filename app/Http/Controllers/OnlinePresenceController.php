<?php

namespace App\Http\Controllers;

use App\Services\Presence\OnlineVisitorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OnlinePresenceController extends Controller
{
    public function status(Request $request, OnlineVisitorService $presenceService): JsonResponse
    {
        return response()->json($presenceService->recordAndCount($request));
    }
}
