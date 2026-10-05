<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\PushSubscription;
use App\Models\Ticket;
use App\Models\TicketResponse;
use App\Models\User;
use App\Services\WebPushService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class ReplyNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Event TicketResponse::created memanggil service; cegah pengiriman push sungguhan di tes.
        $this->mock(WebPushService::class, function ($mock) {
            $mock->shouldReceive('sendReplyNotification')->andReturn(0);
        });
    }

    public function test_creating_response_directly_triggers_push_like_filament_does(): void
    {
        $owner = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);
        $ticket = $this->makeTicket($owner, $admin);

        $this->mock(WebPushService::class, function ($mock) use ($ticket) {
            $mock->shouldReceive('sendReplyNotification')
                ->once()
                ->withArgs(fn ($response) => $response->ticket_id === $ticket->id)
                ->andReturn(1);
        });

        TicketResponse::create([
            'ticket_id' => $ticket->id,
            'user_id' => $admin->id,
            'response_text' => 'Dibuat langsung tanpa controller portal.',
        ]);
    }

    protected function makeTicket(User $owner, ?User $handler = null): Ticket
    {
        $category = Category::firstOrCreate(['name' => 'Hardware', 'slug' => 'hardware']);

        return Ticket::create([
            'user_id' => $owner->id,
            'handled_by_id' => $handler?->id,
            'category_id' => $category->id,
            'subject' => 'PC tidak menyala',
            'description' => 'Komputer tidak bisa hidup.',
            'status' => $handler ? 'diproses' : 'pending',
        ]);
    }

    protected function subscribe(User $user, string $suffix): PushSubscription
    {
        return PushSubscription::create([
            'user_id' => $user->id,
            'endpoint' => 'https://push.example.test/' . $suffix,
            'public_key' => 'pk',
            'auth_token' => 'at',
        ]);
    }

    protected function serviceSpy(array &$captured): WebPushService
    {
        $service = Mockery::mock(WebPushService::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $service->shouldReceive('dispatchNotifications')->andReturnUsing(
            function ($subscriptions, $title, $body, $url) use (&$captured) {
                $captured = [
                    'users' => $subscriptions->pluck('user_id')->unique()->values()->all(),
                    'title' => $title,
                    'body' => $body,
                    'url' => $url,
                ];

                return $subscriptions->count();
            }
        );

        return $service;
    }

    public function test_admin_reply_pushes_only_to_ticket_owner(): void
    {
        $owner = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);
        $otherAdmin = User::factory()->create(['role' => 'admin']);
        $ticket = $this->makeTicket($owner, $admin);

        $this->subscribe($owner, 'owner');
        $this->subscribe($admin, 'admin');
        $this->subscribe($otherAdmin, 'other');

        $response = TicketResponse::create([
            'ticket_id' => $ticket->id,
            'user_id' => $admin->id,
            'response_text' => 'Silakan coba kabel power lain.',
        ]);

        $captured = [];
        $sent = $this->serviceSpy($captured)->sendReplyNotification($response);

        $this->assertSame(1, $sent);
        $this->assertSame([$owner->id], $captured['users']);
        $this->assertSame('Balasan Baru dari Tim IT', $captured['title']);
        $this->assertSame(route('tickets.show', $ticket), $captured['url']);
        $this->assertStringContainsString('Silakan coba kabel power lain.', $captured['body']);
    }

    public function test_employee_reply_pushes_to_handling_admin_only(): void
    {
        $owner = User::factory()->create(['role' => 'user']);
        $handler = User::factory()->create(['role' => 'admin']);
        $otherAdmin = User::factory()->create(['role' => 'admin']);
        $ticket = $this->makeTicket($owner, $handler);

        $this->subscribe($owner, 'owner');
        $this->subscribe($handler, 'handler');
        $this->subscribe($otherAdmin, 'other');

        $response = TicketResponse::create([
            'ticket_id' => $ticket->id,
            'user_id' => $owner->id,
            'response_text' => 'Masih mati.',
        ]);

        $captured = [];
        $this->serviceSpy($captured)->sendReplyNotification($response);

        $this->assertSame([$handler->id], $captured['users']);
    }

    public function test_employee_reply_on_unclaimed_ticket_pushes_to_all_admins(): void
    {
        $owner = User::factory()->create(['role' => 'user']);
        $adminA = User::factory()->create(['role' => 'admin']);
        $adminB = User::factory()->create(['role' => 'admin']);
        $ticket = $this->makeTicket($owner);

        $this->subscribe($adminA, 'a');
        $this->subscribe($adminB, 'b');

        $response = TicketResponse::create([
            'ticket_id' => $ticket->id,
            'user_id' => $owner->id,
            'response_text' => 'Mohon dibantu.',
        ]);

        $captured = [];
        $this->serviceSpy($captured)->sendReplyNotification($response);

        $this->assertEqualsCanonicalizing([$adminA->id, $adminB->id], $captured['users']);
    }

    public function test_reply_endpoint_survives_push_failure(): void
    {
        $owner = User::factory()->create(['role' => 'user']);
        $ticket = $this->makeTicket($owner);

        $this->mock(WebPushService::class, function ($mock) {
            $mock->shouldReceive('sendReplyNotification')->once()->andThrow(new \RuntimeException('push down'));
        });

        $this->actingAs($owner)
            ->post(route('tickets.reply', $ticket), ['response_text' => 'Halo tim IT'])
            ->assertRedirect(route('tickets.show', $ticket));

        $this->assertDatabaseHas('ticket_responses', ['ticket_id' => $ticket->id, 'response_text' => 'Halo tim IT']);
    }

    public function test_bell_endpoint_lists_only_unread_replies_from_others_on_own_tickets(): void
    {
        $owner = User::factory()->create(['role' => 'user']);
        $stranger = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);

        $mine = $this->makeTicket($owner, $admin);
        $theirs = $this->makeTicket($stranger, $admin);

        TicketResponse::create(['ticket_id' => $mine->id, 'user_id' => $admin->id, 'response_text' => 'Balasan untuk saya']);
        TicketResponse::create(['ticket_id' => $mine->id, 'user_id' => $owner->id, 'response_text' => 'Pesan saya sendiri']);
        TicketResponse::create(['ticket_id' => $theirs->id, 'user_id' => $admin->id, 'response_text' => 'Balasan orang lain']);
        TicketResponse::create(['ticket_id' => $mine->id, 'user_id' => $admin->id, 'response_text' => 'Sudah dibaca', 'read_at' => now()]);

        $this->actingAs($owner)
            ->getJson(route('notifications.replies'))
            ->assertOk()
            ->assertJsonPath('count', 1)
            ->assertJsonPath('items.0.snippet', 'Balasan untuk saya')
            ->assertJsonPath('items.0.url', route('tickets.show', $mine));
    }

    public function test_clicking_specific_notification_marks_only_that_item_as_read(): void
    {
        $owner = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);
        $ticket = $this->makeTicket($owner, $admin);

        $reply1 = TicketResponse::create(['ticket_id' => $ticket->id, 'user_id' => $admin->id, 'response_text' => 'Pesan 1']);
        $reply2 = TicketResponse::create(['ticket_id' => $ticket->id, 'user_id' => $admin->id, 'response_text' => 'Pesan 2']);

        $this->assertSame(2, TicketResponse::whereNull('read_at')->count());

        $this->actingAs($owner)->postJson(route('notifications.read-item'), [
            'item_type' => 'reply',
            'id' => $reply1->id,
        ])->assertOk();

        // Hanya pesan 1 yang ditandai dibaca, pesan 2 tetap belum dibaca
        $this->assertNotNull($reply1->fresh()->read_at);
        $this->assertNull($reply2->fresh()->read_at);
        $this->assertSame(1, TicketResponse::whereNull('read_at')->count());
    }

    public function test_mark_all_read_clears_the_bell(): void
    {
        $owner = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);
        $ticket = $this->makeTicket($owner, $admin);

        TicketResponse::create(['ticket_id' => $ticket->id, 'user_id' => $admin->id, 'response_text' => 'Satu']);
        TicketResponse::create(['ticket_id' => $ticket->id, 'user_id' => $admin->id, 'response_text' => 'Dua']);

        $this->actingAs($owner)->postJson(route('notifications.replies.read'))->assertOk();
        $this->actingAs($owner)->getJson(route('notifications.replies'))->assertJsonPath('count', 0);
    }

    public function test_employee_can_subscribe_to_push_from_portal(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->postJson(route('user.push.subscribe'), [
            'endpoint' => 'https://push.example.test/abc',
            'publicKey' => 'pk',
            'authToken' => 'at',
        ])->assertOk()->assertJsonPath('success', true);

        $this->assertDatabaseHas('push_subscriptions', ['user_id' => $user->id, 'endpoint' => 'https://push.example.test/abc']);
    }

    public function test_navbar_renders_bell_and_ticket_page_renders_optin_card(): void
    {
        $owner = User::factory()->create(['role' => 'user']);
        $ticket = $this->makeTicket($owner);

        $this->actingAs($owner)->get('/dashboard')->assertSee('Notifikasi', false);
        $this->actingAs($owner)->get(route('tickets.show', $ticket))->assertSee('Aktifkan notifikasi balasan', false);
    }

    public function test_admin_updating_ticket_status_triggers_ticket_event_and_push(): void
    {
        $owner = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);
        $ticket = $this->makeTicket($owner);

        $this->mock(WebPushService::class, function ($mock) use ($ticket) {
            $mock->shouldReceive('sendStatusNotification')
                ->once()
                ->withArgs(fn ($t, $event) => $t->id === $ticket->id && $event->status === 'diproses')
                ->andReturn(1);
        });

        $this->actingAs($admin);
        $ticket->status = 'diproses';
        $ticket->save();

        $this->assertDatabaseHas('ticket_events', [
            'ticket_id' => $ticket->id,
            'status' => 'diproses',
            'actor_id' => $admin->id,
        ]);
    }

    public function test_ticket_events_to_selesai_and_ditolak_are_recorded(): void
    {
        $owner = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);
        $ticket = $this->makeTicket($owner);

        $this->actingAs($admin);
        $ticket->status = 'selesai';
        $ticket->save();

        $this->assertDatabaseHas('ticket_events', [
            'ticket_id' => $ticket->id,
            'status' => 'selesai',
        ]);

        $ticket->status = 'ditolak';
        $ticket->save();

        $this->assertDatabaseHas('ticket_events', [
            'ticket_id' => $ticket->id,
            'status' => 'ditolak',
        ]);
    }

    public function test_bell_feed_includes_status_event_with_matching_ui_properties(): void
    {
        $owner = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);
        $ticket = $this->makeTicket($owner);

        $this->actingAs($admin);
        $ticket->status = 'diproses';
        $ticket->save();

        $res = $this->actingAs($owner)
            ->getJson(route('notifications.replies'))
            ->assertOk()
            ->assertJsonPath('count', 1);

        $item = $res->json('items.0');
        $this->assertEquals('diproses', $item['type']);
        $this->assertEquals('Pengaduan Diproses', $item['title']);
        $this->assertFalse($item['is_read']);
        $this->assertEquals('ri-loader-4-line', $item['icon']);
    }

    public function test_visiting_ticket_page_does_not_bulk_clear_other_unread_items(): void
    {
        $owner = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);
        $ticket = $this->makeTicket($owner);

        $reply = TicketResponse::create(['ticket_id' => $ticket->id, 'user_id' => $admin->id, 'response_text' => 'Halo']);
        $event = $ticket->events()->create(['actor_id' => $admin->id, 'type' => 'status', 'status' => 'diproses']);

        $this->actingAs($owner)->get(route('tickets.show', $ticket))->assertOk();

        // Item yang belum diklik tetap berstatus unread agar tidak hilang penandanya
        $this->assertSame(1, TicketResponse::whereNull('read_at')->count());
        $this->assertSame(1, $ticket->events()->whereNull('read_at')->count());
    }

    public function test_mark_item_read_can_mark_event_specifically(): void
    {
        $owner = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);
        $ticket = $this->makeTicket($owner);

        $event = $ticket->events()->create(['actor_id' => $admin->id, 'type' => 'status', 'status' => 'diproses']);

        $this->actingAs($owner)->postJson(route('notifications.read-item'), [
            'item_type' => 'event',
            'id' => $event->id,
        ])->assertOk();

        $this->assertNotNull($event->fresh()->read_at);
    }

    public function test_admin_unhandled_status_returns_unified_items_with_tickets_and_replies(): void
    {
        $owner = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);

        // 1. Unhandled ticket
        $unhandledTicket = $this->makeTicket($owner);

        // 2. Claimed ticket with employee reply
        $claimedTicket = $this->makeTicket($owner, $admin);
        $reply = TicketResponse::create([
            'ticket_id' => $claimedTicket->id,
            'user_id' => $owner->id,
            'response_text' => 'Tolong dicek segera ya min.',
        ]);

        $res = $this->actingAs($admin)
            ->getJson(route('admin.tickets.unhandled.status'))
            ->assertOk()
            ->assertJsonStructure([
                'count',
                'items',
                'tickets',
            ]);

        $this->assertSame(2, $res->json('count'));
        $items = collect($res->json('items'));

        $this->assertTrue($items->contains('item_type', 'ticket'));
        $this->assertTrue($items->contains('item_type', 'reply'));

        $ticketItem = $items->firstWhere('item_type', 'ticket');
        $this->assertSame('Aduan Masuk Belum Ditangani', $ticketItem['title']);
        $this->assertTrue($ticketItem['can_claim']);
        $this->assertSame('ri-inbox-archive-line', $ticketItem['icon']);

        $replyItem = $items->firstWhere('item_type', 'reply');
        $this->assertSame('Pesan Baru dari Pelapor', $replyItem['title']);
        $this->assertFalse($replyItem['can_claim']);
        $this->assertSame('ri-chat-3-line', $replyItem['icon']);
    }

    public function test_admin_can_mark_specific_reply_as_read(): void
    {
        $owner = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);
        $ticket = $this->makeTicket($owner, $admin);

        $reply = TicketResponse::create([
            'ticket_id' => $ticket->id,
            'user_id' => $owner->id,
            'response_text' => 'Halo admin',
        ]);

        $this->assertNull($reply->read_at);

        $this->actingAs($admin)
            ->postJson(route('admin.notifications.read-item'), [
                'item_type' => 'reply',
                'id' => $reply->id,
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertNotNull($reply->fresh()->read_at);
    }

    public function test_admin_can_mark_all_replies_as_read(): void
    {
        $owner = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);
        $ticket = $this->makeTicket($owner, $admin);

        $reply1 = TicketResponse::create(['ticket_id' => $ticket->id, 'user_id' => $owner->id, 'response_text' => 'Pesan 1']);
        $reply2 = TicketResponse::create(['ticket_id' => $ticket->id, 'user_id' => $owner->id, 'response_text' => 'Pesan 2']);

        $this->assertSame(2, TicketResponse::whereNull('read_at')->count());

        $this->actingAs($admin)
            ->postJson(route('admin.notifications.read-all'))
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertSame(0, TicketResponse::whereNull('read_at')->count());
    }
}

