<?php

namespace Tests\Feature;

use App\Models\SiteVisitor;
use App\Models\User;
use App\Services\Presence\OnlineVisitorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SiteVisitorTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_visitor_is_persisted_and_increases_total_count(): void
    {
        Cache::flush();
        $this->assertEquals(0, SiteVisitor::count());

        $user1 = User::factory()->create();
        $this->actingAs($user1)->get('/dashboard');

        $this->assertEquals(1, SiteVisitor::count());
        $this->assertDatabaseHas('site_visitors', [
            'visitor_key' => 'usr_' . $user1->id,
            'user_id' => $user1->id,
        ]);

        $user2 = User::factory()->create();
        $this->actingAs($user2)->get('/dashboard');

        $this->assertEquals(2, SiteVisitor::count());
    }

    public function test_returning_visitor_updates_existing_record_without_duplication(): void
    {
        Cache::flush();
        $user = User::factory()->create();

        $this->actingAs($user)->get('/dashboard');
        $this->assertEquals(1, SiteVisitor::count());

        // Clear touch cache to simulate returning visit after cooldown
        Cache::forget('bbpom_visitor_touch_usr_' . $user->id);

        $this->actingAs($user)->get('/dashboard');
        $this->assertEquals(1, SiteVisitor::count());

        $visitor = SiteVisitor::where('visitor_key', 'usr_' . $user->id)->first();
        $this->assertEquals(2, $visitor->visits_count);
    }

    public function test_service_returns_accurate_cumulative_metrics(): void
    {
        Cache::flush();
        $service = app(OnlineVisitorService::class);

        $stats = $service->recordAndCount(request());
        $this->assertGreaterThanOrEqual(1, $stats['total_visitors']);
        $this->assertGreaterThanOrEqual(1, $stats['total_visits']);
        $this->assertArrayHasKey('authenticated_count', $stats);
        $this->assertArrayHasKey('guest_count', $stats);
    }
}
