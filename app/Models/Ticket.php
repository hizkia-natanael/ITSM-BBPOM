<?php

namespace App\Models;

use App\Models\Concerns\HasTicketServiceAttributes;
use App\Services\WebPushService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Log;

class Ticket extends Model
{
    use HasFactory, HasTicketServiceAttributes;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'handled_by_id',
        'category_id',
        'service_key',
        'subcategory',
        'subject',
        'description',
        'status',
        'attachment',
    ];

    protected static function boot()
    {
        parent::boot();

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

    public function events(): HasMany
    {
        return $this->hasMany(TicketEvent::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function handledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(TicketResponse::class)->oldest();
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function bsreDetail(): HasOne { return $this->hasOne(TicketBsreDetail::class, 'ticket_id'); }
    public function zoomDetail(): HasOne { return $this->hasOne(TicketZoomDetail::class, 'ticket_id'); }
    public function kegiatanDetail(): HasOne { return $this->hasOne(TicketKegiatanDetail::class, 'ticket_id'); }
    public function windowsDetail(): HasOne { return $this->hasOne(TicketWindowsDetail::class, 'ticket_id'); }
    public function officeDetail(): HasOne { return $this->hasOne(TicketOfficeDetail::class, 'ticket_id'); }
    public function lainnyaDetail(): HasOne { return $this->hasOne(TicketLainnyaDetail::class, 'ticket_id'); }
}
