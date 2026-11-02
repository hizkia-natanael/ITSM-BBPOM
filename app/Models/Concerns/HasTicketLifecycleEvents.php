<?php

namespace App\Models\Concerns;

use App\Models\TicketEvent;
use App\Services\WebPushService;
use Illuminate\Support\Facades\Log;

trait HasTicketLifecycleEvents
{
    public static function bootHasTicketLifecycleEvents(): void
    {
        static::creating(function ($ticket) {
            if (empty($ticket->ticket_number)) {
                $lastTicket = static::latest('id')->first();
                $nextId = $lastTicket ? ($lastTicket->id + 1) : 1;
                $ticket->ticket_number = 'TIK-BBPOM-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

                while (static::where('ticket_number', $ticket->ticket_number)->exists()) {
                    $nextId++;
                    $ticket->ticket_number = 'TIK-BBPOM-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
                }
            }
        });

        static::created(function ($ticket) {
            if ($ticket->status === 'pending' && is_null($ticket->handled_by_id)) {
                try {
                    app(WebPushService::class)->sendTicketNotification($ticket);
                } catch (\Throwable $e) {
                    Log::warning('Error sending web push on ticket created: ' . $e->getMessage());
                }
            }
        });

        static::updated(function ($ticket) {
            if (!$ticket->wasChanged('status') || !in_array($ticket->status, TicketEvent::NOTIFIABLE_STATUSES, true)) {
                return;
            }

            $actorId = auth()->id();
            if ($actorId !== null && $actorId === $ticket->user_id) {
                return;
            }

            $event = $ticket->events()->create([
                'actor_id' => $actorId,
                'type' => 'status',
                'status' => $ticket->status,
            ]);

            try {
                app(WebPushService::class)->sendStatusNotification($ticket, $event);
            } catch (\Throwable $e) {
                Log::warning('Error sending web push on ticket status change: ' . $e->getMessage());
            }
        });
    }
}
