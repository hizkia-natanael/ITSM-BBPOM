<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public static function resolveForService(?string $serviceKey): ?self
    {
        if (empty($serviceKey)) {
            return static::where('slug', 'lainnya')->first() ?? static::first();
        }

        $aliasMap = [
            'sertifikat_bse' => 'sertifikat-bsre',
            'sertifikat-bse' => 'sertifikat-bsre',
            'sertifikat_bsre' => 'sertifikat-bsre',
            'bsre' => 'sertifikat-bsre',
            'internet_jaringan' => 'internet',
            'pengelolaan_subsite' => 'subsite',
            'desain_infografis' => 'infografis',
        ];

        $targetSlug = $aliasMap[$serviceKey] ?? Str::slug($serviceKey);

        return static::where('slug', $targetSlug)
            ->orWhere('slug', $serviceKey)
            ->orWhere('slug', Str::slug($serviceKey))
            ->orWhere('slug', str_replace('_', '-', $serviceKey))
            ->first() ?? (static::where('slug', 'lainnya')->first() ?? static::first());
    }
}
