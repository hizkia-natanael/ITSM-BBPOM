<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketKegiatanDetail extends Model
{
    use HasFactory;

    protected $table = 'ticket_kegiatan_details';

    protected $fillable = [
        'ticket_id',
        'sub_category',
        'tanggal_pengajuan',
        'nama',
        'nama_acara',
        'jam',
        'link_qrcode',
        'judul_survey',
        'judul_acara',
        'jenis_test',
        'pertanyaan_data',
        'deskripsi',
        'file_lampiran',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pengajuan' => 'date',
            'pertanyaan_data' => 'array',
        ];
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function getSubCategoryLabelAttribute(): string
    {
        return match ($this->sub_category) {
            'permintaan_daftar_hadir' => 'Permintaan Daftar Hadir',
            'permintaan_qrcode' => 'Permintaan QRCODE',
            'survey' => 'Survey',
            'post_test_pretest' => 'Post Test / Pretest',
            'lainnya' => 'Lainnya',
            default => ucfirst(str_replace('_', ' ', $this->sub_category)),
        };
    }

    public function getJenisTestLabelAttribute(): ?string
    {
        if (empty($this->jenis_test)) {
            return null;
        }

        return match ($this->jenis_test) {
            'pretest' => 'Pretest',
            'post_test' => 'Post Test',
            'pretest_posttest' => 'Pretest & Post Test',
            default => ucfirst(str_replace('_', ' ', $this->jenis_test)),
        };
    }
}
