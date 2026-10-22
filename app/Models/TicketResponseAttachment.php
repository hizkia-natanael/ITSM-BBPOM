<?php

namespace App\Models;

use App\Models\Concerns\HasAttachmentDocumentStyles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketResponseAttachment extends Model
{
    use HasFactory, HasAttachmentDocumentStyles;

    protected $fillable = [
        'ticket_response_id',
        'file_path',
        'file_name',
        'file_size',
        'original_size',
        'mime_type',
        'compression_mode',
        'is_compressed',
        'saved_bytes',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'original_size' => 'integer',
            'saved_bytes' => 'integer',
            'is_compressed' => 'boolean',
        ];
    }

    public function response(): BelongsTo
    {
        return $this->belongsTo(TicketResponse::class, 'ticket_response_id');
    }

    public function ticketResponse(): BelongsTo
    {
        return $this->belongsTo(TicketResponse::class, 'ticket_response_id');
    }

    public function getFormattedSizeAttribute(): string
    {
        return $this->formatBytes((int) $this->file_size);
    }

    public function getFormattedOriginalSizeAttribute(): string
    {
        return $this->formatBytes((int) ($this->original_size ?: $this->file_size));
    }

    public function getSavingPercentageAttribute(): int
    {
        $orig = (int) $this->original_size;
        $saved = (int) $this->saved_bytes;
        return ($orig > 0 && $saved > 0) ? (int) round(($saved / $orig) * 100) : 0;
    }

    public function getFileExtensionAttribute(): string
    {
        return strtolower(pathinfo($this->file_name ?: $this->file_path, PATHINFO_EXTENSION));
    }

    public function getIsImageAttribute(): bool
    {
        return in_array($this->file_extension, ['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg']);
    }

    public function getIsPdfAttribute(): bool
    {
        return $this->file_extension === 'pdf';
    }

    public function getExtensionUpperAttribute(): string
    {
        return strtoupper($this->file_extension ?: 'FILE');
    }

    public function getDownloadUrlAttribute(): string
    {
        $ticketId = $this->ticketResponse?->ticket_id;
        return $ticketId ? route('tickets.response-attachments.download', ['ticket' => $ticketId, 'attachment' => $this->id]) : '#';
    }

    public function getPreviewUrlAttribute(): string
    {
        $ticketId = $this->ticketResponse?->ticket_id;
        return $ticketId ? route('tickets.response-attachments.download', ['ticket' => $ticketId, 'attachment' => $this->id, 'inline' => 1]) : '#';
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 0) . ' KB';
        }
        return $bytes . ' B';
    }
}
