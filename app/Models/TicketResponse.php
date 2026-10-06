<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TicketResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'reply_to_id',
        'response_text',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (TicketResponse $response) {
            try {
                app(\App\Services\WebPushService::class)->sendReplyNotification($response);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Error sending web push on ticket reply: ' . $e->getMessage());
            }
        });
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketResponseAttachment::class);
    }

    public function attachment(): HasOne
    {
        return $this->hasOne(TicketResponseAttachment::class)->latestOfMany();
    }

    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(TicketResponse::class, 'reply_to_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TicketResponse::class, 'reply_to_id');
    }

    public function getSnippetAttribute(): string
    {
        $text = trim((string) $this->response_text);
        if ($text !== '') {
            return \Illuminate\Support\Str::limit($text, 60);
        }
        $att = $this->attachment;
        if ($att) {
            return 'Berkas: ' . $att->file_name;
        }
        return 'Pesan';
    }

    public function getIsReadAttribute(): bool
    {
        return $this->read_at !== null;
    }
}
