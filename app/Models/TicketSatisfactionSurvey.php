<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketSatisfactionSurvey extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'speed_rating',
        'quality_rating',
        'communication_rating',
        'system_rating',
        'average_rating',
        'feedback',
    ];

    protected function casts(): array
    {
        return [
            'speed_rating' => 'integer',
            'quality_rating' => 'integer',
            'communication_rating' => 'integer',
            'system_rating' => 'integer',
            'average_rating' => 'float',
        ];
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedScoreAttribute(): string
    {
        return number_format($this->average_rating, 1, '.', '') . ' / 5';
    }
}
