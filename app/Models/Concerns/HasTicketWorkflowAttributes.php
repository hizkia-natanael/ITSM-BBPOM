<?php

namespace App\Models\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasTicketWorkflowAttributes
{
    public function acceptedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'accepted_by_id');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by_id');
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by_id');
    }

    /**
     * Catat penanggung jawab dan stempel waktu otomatis saat status berubah.
     */
    public function syncWorkflowStage(string $status, ?int $actorId = null): void
    {
        $user = auth()->user();
        $adminId = ($user && $user->isAdmin()) ? $user->id : null;
        $actorId = $actorId ?: $adminId;
        $this->status = $status;

        if ($status === 'diterima') {
            $this->accepted_by_id = $this->accepted_by_id ?: ($actorId ?: $this->handled_by_id);
            $this->accepted_at = $this->accepted_at ?: now();
            if ($actorId) $this->handled_by_id = $actorId;
        } elseif ($status === 'diproses') {
            $this->processed_by_id = $actorId ?: $this->handled_by_id;
            $this->processed_at = now();
            if ($actorId) $this->handled_by_id = $actorId;
            if (!$this->accepted_at) {
                $this->accepted_by_id = $this->accepted_by_id ?: $this->processed_by_id;
                $this->accepted_at = now();
            }
        } elseif (in_array($status, ['selesai', 'ditolak'], true)) {
            $this->resolved_by_id = $actorId ?: $this->handled_by_id;
            $this->resolved_at = now();
            if ($actorId) $this->handled_by_id = $actorId;
        } elseif ($status === 'pending') {
            $this->handled_by_id = null;
        }
    }

    /**
     * Otomatis sinkronisasi workflow stage pada event saving model.
     */
    public static function bootHasTicketWorkflowAttributes(): void
    {
        static::saving(function ($ticket) {
            if ($ticket->isDirty('status')) {
                $status = $ticket->status;
                $user = auth()->user();
                $actorId = ($user && $user->isAdmin()) ? $user->id : null;

                if ($status === 'diterima') {
                    $ticket->accepted_by_id = $ticket->accepted_by_id ?: ($actorId ?: $ticket->handled_by_id);
                    $ticket->accepted_at = $ticket->accepted_at ?: now();
                    if ($actorId && !$ticket->isDirty('handled_by_id')) {
                        $ticket->handled_by_id = $actorId;
                    }
                } elseif ($status === 'diproses') {
                    $ticket->processed_by_id = $ticket->processed_by_id ?: ($actorId ?: $ticket->handled_by_id);
                    $ticket->processed_at = $ticket->processed_at ?: now();
                    if ($actorId && !$ticket->isDirty('handled_by_id')) {
                        $ticket->handled_by_id = $actorId;
                    }
                    if (!$ticket->accepted_at) {
                        $ticket->accepted_by_id = $ticket->accepted_by_id ?: $ticket->processed_by_id;
                        $ticket->accepted_at = now();
                    }
                } elseif (in_array($status, ['selesai', 'ditolak'], true)) {
                    $ticket->resolved_by_id = $ticket->resolved_by_id ?: ($actorId ?: $ticket->handled_by_id);
                    $ticket->resolved_at = $ticket->resolved_at ?: now();
                    if ($actorId && !$ticket->isDirty('handled_by_id')) {
                        $ticket->handled_by_id = $actorId;
                    }
                } elseif ($status === 'pending') {
                    if (!$ticket->isDirty('handled_by_id')) {
                        $ticket->handled_by_id = null;
                    }
                }
            }
        });
    }

    /**
     * Ambil nama admin penangan aktif terakhir untuk display ringkas.
     */
    public function getLatestHandlerNameAttribute(): ?string
    {
        if ($this->resolvedBy) return $this->resolvedBy->name;
        if ($this->processedBy) return $this->processedBy->name;
        if ($this->acceptedBy) return $this->acceptedBy->name;
        return $this->handledBy?->name;
    }
}
