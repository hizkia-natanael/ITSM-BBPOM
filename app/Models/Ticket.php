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

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
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

    public function getSubjectAttribute($value): string
    {
        return preg_replace('/^\[[^\]]+\]\s*/', '', (string)$value);
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

    public function getServiceKeyResolvedAttribute(): ?string
    {
        if (!empty($this->service_key)) {
            return $this->service_key;
        }

        if ($this->relationLoaded('bsreDetail') ? $this->bsreDetail : $this->bsreDetail()->exists()) {
            return 'sertifikat_bse';
        }
        if ($this->relationLoaded('zoomDetail') ? $this->zoomDetail : $this->zoomDetail()->exists()) {
            return 'zoom';
        }
        if ($this->relationLoaded('kegiatanDetail') ? $this->kegiatanDetail : $this->kegiatanDetail()->exists()) {
            return 'formulir_kegiatan';
        }
        if ($this->relationLoaded('windowsDetail') ? $this->windowsDetail : $this->windowsDetail()->exists()) {
            return 'install_windows';
        }
        if ($this->relationLoaded('officeDetail') ? $this->officeDetail : $this->officeDetail()->exists()) {
            return 'lisensi_office';
        }
        if ($this->relationLoaded('lainnyaDetail') ? $this->lainnyaDetail : $this->lainnyaDetail()->exists()) {
            return 'lainnya';
        }

        if ($this->category) {
            $slug = str_replace('-', '_', $this->category->slug);
            if ($slug === 'sertifikat_bsre') {
                return 'sertifikat_bse';
            }
            return $slug;
        }

        return null;
    }

    public function getServiceNameAttribute(): string
    {
        $resolvedKey = $this->service_key_resolved;
        if ($resolvedKey) {
            $services = config('services_gateway.services', []);
            if (isset($services[$resolvedKey]['name'])) {
                return $services[$resolvedKey]['name'];
            }
        }

        return $this->category?->name ?? 'Layanan TIK';
    }
}
