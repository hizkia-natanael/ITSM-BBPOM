<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketLainnyaDetail extends Model
{
    use HasFactory;

    protected $table = 'ticket_lainnya_details';

    protected $fillable = [
        'ticket_id',
        'tanggal_pengajuan',
        'nama',
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
}
