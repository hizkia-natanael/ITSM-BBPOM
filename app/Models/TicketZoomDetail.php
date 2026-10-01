<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketZoomDetail extends Model
{
    use HasFactory;

    protected $table = 'ticket_zoom_details';

    protected $fillable = [
        'ticket_id',
        'sub_category',
        'tanggal_pengajuan',
        'nama',
        'nama_acara',
        'jam',
        'deskripsi',
        'file_lampiran',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pengajuan' => 'date',
        ];
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function getSubCategoryLabelAttribute(): string
    {
        return match ($this->sub_category) {
            'permintaan_link_zoom' => 'Permintaan Link Zoom',
            'lainnya' => 'Lainnya',
            default => ucfirst(str_replace('_', ' ', $this->sub_category)),
        };
    }
}
