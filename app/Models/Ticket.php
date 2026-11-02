<?php

namespace App\Models;

use App\Models\Concerns\HasTicketLifecycleEvents;
use App\Models\Concerns\HasTicketServiceAttributes;
use App\Models\Concerns\HasTicketSubdetailRelations;
use App\Models\Concerns\HasTicketWorkflowAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;
    use HasTicketServiceAttributes;
    use HasTicketWorkflowAttributes;
    use HasTicketSubdetailRelations;
    use HasTicketLifecycleEvents;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'handled_by_id',
        'accepted_by_id',
        'accepted_at',
        'processed_by_id',
        'processed_at',
        'resolved_by_id',
        'resolved_at',
        'category_id',
        'service_key',
        'subcategory',
        'subject',
        'description',
        'status',
        'attachment',
    ];

    protected function casts(): array
    {
        return [
            'accepted_at' => 'datetime',
            'processed_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function events(): HasMany
    {
        return $this->hasMany(TicketEvent::class);
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
}
