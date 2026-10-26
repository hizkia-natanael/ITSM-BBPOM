<?php

namespace App\Services\Presence;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OnlineVisitorService
{
    private const CACHE_KEY = 'bbpom_online_visitors_registry';
    private const ACTIVE_WINDOW_SECONDS = 300; // 5 minutes

    /**
     * Record the current visitor's heartbeat and compute realtime online counts.
     *
     * @return array{online_count: int, authenticated_count: int, guest_count: int, timestamp: string}
     */
    public function recordAndCount(?Request $request = null): array
    {
        $request = $request ?? request();
        $now = Carbon::now()->getTimestamp();
        $cutoff = $now - self::ACTIVE_WINDOW_SECONDS;

        $visitorKey = $this->resolveVisitorKey($request);
        $userId = Auth::id();

        $registry = Cache::get(self::CACHE_KEY, []);
        if (! is_array($registry)) {
            $registry = [];
        }

        // Prune expired entries outside the 5-minute active window
        foreach ($registry as $key => $meta) {
            if (! is_array($meta) || (($meta['last_activity'] ?? 0) < $cutoff)) {
                unset($registry[$key]);
            }
        }

        // Register / refresh current visitor
        $registry[$visitorKey] = [
            'user_id' => $userId,
            'last_activity' => $now,
        ];

        // Merge active sessions from database session driver when available
        $this->mergeDatabaseSessions($registry, $cutoff);

        Cache::put(self::CACHE_KEY, $registry, now()->addMinutes(10));

        $authenticatedUsers = [];
        $guestSessions = 0;

        foreach ($registry as $meta) {
            if (! empty($meta['user_id'])) {
                $authenticatedUsers[(string) $meta['user_id']] = true;
            } else {
                $guestSessions++;
            }
        }

        $authenticatedCount = count($authenticatedUsers);
        $totalOnline = max(1, $authenticatedCount + $guestSessions);

        return [
            'online_count' => $totalOnline,
            'authenticated_count' => $authenticatedCount,
            'guest_count' => $guestSessions,
            'timestamp' => Carbon::now()->format('H:i') . ' WIB',
        ];
    }

    private function resolveVisitorKey(Request $request): string
    {
        if ($request->hasSession() && $request->session()->getId()) {
            return 'sess_' . $request->session()->getId();
        }

        $ip = (string) $request->ip();
        $ua = substr((string) $request->userAgent(), 0, 120);

        return 'fp_' . sha1($ip . '|' . $ua);
    }

    /**
     * @param array<string, array{user_id: int|string|null, last_activity: int}> $registry
     */
    private function mergeDatabaseSessions(array &$registry, int $cutoff): void
    {
        try {
            if (! Schema::hasTable('sessions')) {
                return;
            }

            $rows = DB::table('sessions')
                ->select(['id', 'user_id', 'last_activity'])
                ->where('last_activity', '>=', $cutoff)
                ->limit(500)
                ->get();

            foreach ($rows as $row) {
                $key = 'sess_' . $row->id;
                $registry[$key] = [
                    'user_id' => $row->user_id,
                    'last_activity' => max((int) $row->last_activity, $registry[$key]['last_activity'] ?? 0),
                ];
            }
        } catch (\Throwable) {
            // Gracefully fallback to cache registry if database sessions table is unavailable
        }
    }
}
