<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketWindowsDetail extends Model
{
    use HasFactory;

    protected $table = 'ticket_windows_details';

    protected $fillable = [
        'ticket_id',
        'sub_category',
        'tanggal_pengajuan',
        'nama',
        'bidang',
        'merk_perangkat',
        'kode_bmn',
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
            'permintaan_install_windows' => 'Permintaan Install Ulang Windows',
            'lainnya' => 'Lainnya',
            default => ucfirst(str_replace('_', ' ', $this->sub_category)),
        };
    }
}
