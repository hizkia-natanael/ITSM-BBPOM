<?php

namespace App\Services\Presence;

use App\Models\SiteVisitor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class OnlineVisitorService
{
    private const CACHE_TOTAL_KEY = 'bbpom_total_visitors_count';
    private const CACHE_TOUCH_PREFIX = 'bbpom_visitor_touch_';

    /**
     * Record visitor presence into persistent storage and return cumulative stats.
     *
     * @return array{total_visitors: int, online_count: int, total_visits: int, authenticated_count: int, guest_count: int, timestamp: string}
     */
    public function recordAndCount(?Request $request = null): array
    {
        $request = $request ?? request();
        $this->recordVisitor($request);

        $totalVisitors = $this->resolveTotalVisitors();
        $authenticatedCount = SiteVisitor::whereNotNull('user_id')->count();
        $guestCount = max(0, $totalVisitors - $authenticatedCount);

        return [
            'total_visitors' => $totalVisitors,
            'online_count' => $totalVisitors,
            'total_visits' => (int) SiteVisitor::sum('visits_count'),
            'authenticated_count' => $authenticatedCount,
            'guest_count' => $guestCount,
            'timestamp' => Carbon::now()->format('H:i') . ' WIB',
        ];
    }

    private function recordVisitor(Request $request): void
    {
        $visitorKey = $this->resolveVisitorKey($request);
        $touchKey = self::CACHE_TOUCH_PREFIX . $visitorKey;

        if (Cache::has($touchKey)) {
            return;
        }

        $userId = Auth::id();
        $ip = (string) $request->ip();
        $ua = substr((string) $request->userAgent(), 0, 255);
        $now = now();

        if ($userId && $request->hasSession() && $request->session()->getId()) {
            $guestKey = 'sess_' . $request->session()->getId();
            $guestRecord = SiteVisitor::where('visitor_key', $guestKey)->whereNull('user_id')->first();
            if ($guestRecord) {
                $guestRecord->update(['visitor_key' => $visitorKey, 'user_id' => $userId]);
            }
        }

        $visitor = SiteVisitor::where('visitor_key', $visitorKey)->first();

        if ($visitor) {
            $visitor->update([
                'user_id' => $userId ?? $visitor->user_id,
                'ip_address' => $ip,
                'user_agent' => $ua,
                'visits_count' => $visitor->visits_count + 1,
                'last_visited_at' => $now,
            ]);
        } else {
            SiteVisitor::create([
                'visitor_key' => $visitorKey,
                'user_id' => $userId,
                'ip_address' => $ip,
                'user_agent' => $ua,
                'visits_count' => 1,
                'first_visited_at' => $now,
                'last_visited_at' => $now,
            ]);
            Cache::forget(self::CACHE_TOTAL_KEY);
        }

        Cache::put($touchKey, true, now()->addMinutes(5));
    }

    public function resolveTotalVisitors(): int
    {
        return (int) Cache::remember(self::CACHE_TOTAL_KEY, 300, function () {
            return max(1, SiteVisitor::count());
        });
    }

    public function resolveVisitorKey(Request $request): string
    {
        if (Auth::check()) {
            return 'usr_' . Auth::id();
        }

        if ($request->hasSession() && $request->session()->getId()) {
            return 'sess_' . $request->session()->getId();
        }

        $ip = (string) $request->ip();
        $ua = substr((string) $request->userAgent(), 0, 120);

        return 'fp_' . sha1($ip . '|' . $ua);
    }
}
