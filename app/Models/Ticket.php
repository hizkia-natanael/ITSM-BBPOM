<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Ticket extends Model
{
    use HasFactory;

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

                // Ensure uniqueness if id gaps exist
                while (static::where('ticket_number', $ticket->ticket_number)->exists()) {
                    $nextId++;
                    $ticket->ticket_number = 'TIK-BBPOM-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
                }
            }
        });

        static::created(function ($ticket) {
            if ($ticket->status === 'pending' && is_null($ticket->handled_by_id)) {
                try {
                    app(\App\Services\WebPushService::class)->sendTicketNotification($ticket);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('Error sending web push on ticket created: ' . $e->getMessage());
                }
            }
        });
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

    public function bsreDetail(): HasOne
    {
        return $this->hasOne(TicketBsreDetail::class, 'ticket_id');
    }

    public function zoomDetail(): HasOne
    {
        return $this->hasOne(TicketZoomDetail::class, 'ticket_id');
    }

    public function kegiatanDetail(): HasOne
    {
        return $this->hasOne(TicketKegiatanDetail::class, 'ticket_id');
    }

    public function windowsDetail(): HasOne
    {
        return $this->hasOne(TicketWindowsDetail::class, 'ticket_id');
    }

    public function officeDetail(): HasOne
    {
        return $this->hasOne(TicketOfficeDetail::class, 'ticket_id');
    }

    public function lainnyaDetail(): HasOne
    {
        return $this->hasOne(TicketLainnyaDetail::class, 'ticket_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'diproses' => 'Diproses',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'diproses' => 'info',
            'selesai' => 'success',
            'ditolak' => 'danger',
            default => 'gray',
        };
    }
}
