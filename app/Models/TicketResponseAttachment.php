<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketResponseAttachment extends Model
{
    use HasFactory;

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
        if ($orig > 0 && $saved > 0) {
            return (int) round(($saved / $orig) * 100);
        }
        return 0;
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

    public function getIconClassAttribute(): string
    {
        return match (true) {
            $this->is_image => 'ri-image-2-line text-emerald-600',
            $this->is_pdf => 'ri-file-pdf-2-line text-rose-600',
            in_array($this->file_extension, ['doc', 'docx']) => 'ri-file-word-2-line text-blue-600',
            in_array($this->file_extension, ['xls', 'xlsx', 'csv']) => 'ri-file-excel-2-line text-emerald-700',
            in_array($this->file_extension, ['zip', 'rar', '7z', 'tar', 'gz']) => 'ri-file-zip-line text-amber-600',
            in_array($this->file_extension, ['ppt', 'pptx']) => 'ri-file-ppt-2-line text-orange-600',
            in_array($this->file_extension, ['ai', 'psd', 'cdr']) => 'ri-palette-line text-purple-600',
            default => 'ri-file-text-line text-slate-600',
        };
    }

    public function getBadgeClassAttribute(): string
    {
        return match (true) {
            $this->is_image => 'bg-emerald-50 text-emerald-700 border-emerald-200/60',
            $this->is_pdf => 'bg-rose-50 text-rose-700 border-rose-200/60',
            in_array($this->file_extension, ['doc', 'docx']) => 'bg-blue-50 text-blue-700 border-blue-200/60',
            in_array($this->file_extension, ['zip', 'rar', '7z']) => 'bg-amber-50 text-amber-700 border-amber-200/60',
            default => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    }

    public function getExtensionUpperAttribute(): string
    {
        return strtoupper($this->file_extension ?: 'FILE');
    }

    public function getDocumentColorClassAttribute(): string
    {
        return match (true) {
            $this->is_image => 'bg-emerald-600 text-white',
            $this->is_pdf => 'bg-rose-600 text-white',
            in_array($this->file_extension, ['doc', 'docx']) => 'bg-blue-600 text-white',
            in_array($this->file_extension, ['xls', 'xlsx', 'csv']) => 'bg-teal-600 text-white',
            in_array($this->file_extension, ['zip', 'rar', '7z', 'tar', 'gz']) => 'bg-amber-600 text-white',
            in_array($this->file_extension, ['ppt', 'pptx']) => 'bg-orange-600 text-white',
            in_array($this->file_extension, ['ai', 'psd', 'cdr']) => 'bg-purple-600 text-white',
            default => 'bg-slate-600 text-white',
        };
    }

    public function getDocumentBgClassAttribute(): string
    {
        return match (true) {
            $this->is_image => 'bg-emerald-50/70 border-emerald-200/70',
            $this->is_pdf => 'bg-rose-50/70 border-rose-200/70',
            in_array($this->file_extension, ['doc', 'docx']) => 'bg-blue-50/70 border-blue-200/70',
            in_array($this->file_extension, ['xls', 'xlsx', 'csv']) => 'bg-teal-50/70 border-teal-200/70',
            in_array($this->file_extension, ['zip', 'rar', '7z', 'tar', 'gz']) => 'bg-amber-50/70 border-amber-200/70',
            in_array($this->file_extension, ['ppt', 'pptx']) => 'bg-orange-50/70 border-orange-200/70',
            in_array($this->file_extension, ['ai', 'psd', 'cdr']) => 'bg-purple-50/70 border-purple-200/70',
            default => 'bg-slate-100/70 border-slate-200/70',
        };
    }

    public function getDownloadUrlAttribute(): string
    {
        $ticketId = $this->ticketResponse?->ticket_id;
        if (!$ticketId) {
            return '#';
        }
        return route('tickets.response-attachments.download', [
            'ticket' => $ticketId,
            'attachment' => $this->id,
        ]);
    }

    public function getPreviewUrlAttribute(): string
    {
        $ticketId = $this->ticketResponse?->ticket_id;
        if (!$ticketId) {
            return '#';
        }
        return route('tickets.response-attachments.download', [
            'ticket' => $ticketId,
            'attachment' => $this->id,
            'inline' => 1,
        ]);
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
