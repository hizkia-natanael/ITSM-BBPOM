<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketBsreDetail extends Model
{
    use HasFactory;

    protected $table = 'ticket_bsre_details';

    protected $fillable = [
        'ticket_id',
        'sub_category',
        'tanggal_pengajuan',
        'nama',
        'nip',
        'nik',
        'email',
        'nomor_telepon',
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
            'reset_password' => 'Reset Password BSRE',
            'permintaan_otp' => 'Permintaan OTP',
            'lainnya' => 'Lainnya',
            default => ucfirst(str_replace('_', ' ', $this->sub_category)),
        };
    }
}
