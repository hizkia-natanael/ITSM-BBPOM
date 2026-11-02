<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteVisitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'visitor_key',
        'user_id',
        'ip_address',
        'user_agent',
        'visits_count',
        'first_visited_at',
        'last_visited_at',
    ];

    protected $casts = [
        'visits_count' => 'integer',
        'first_visited_at' => 'datetime',
        'last_visited_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
