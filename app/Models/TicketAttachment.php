<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = (int) $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }

    public function getFileExtensionAttribute(): string
    {
        return strtolower(pathinfo($this->file_name ?: $this->file_path, PATHINFO_EXTENSION));
    }

    public function getIsImageAttribute(): bool
    {
        return in_array($this->file_extension, ['png', 'jpg', 'jpeg', 'gif', 'webp']);
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
            in_array($this->file_extension, ['xls', 'xlsx']) => 'ri-file-excel-2-line text-emerald-700',
            in_array($this->file_extension, ['zip', 'rar', '7z']) => 'ri-file-zip-line text-amber-600',
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
}
