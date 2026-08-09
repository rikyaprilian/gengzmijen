<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Card extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function getSearchTextAttribute(): string
    {
        return collect([
            $this->title,
            $this->description,
            ...$this->links->pluck('title')->toArray(),
            ...$this->links->pluck('subtitle')->toArray(),
        ])
            ->filter()
            ->map(fn($text) => mb_strtolower(trim($text)))
            ->implode(' ');
    }



    protected $fillable = [
        'uuid',
        'category_id',
        'title',
        'description',
        'badge',
        'color',
        'sort_order',
        'is_active',
        'expired_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expired_at' => 'datetime',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function links(): HasMany
    {
        return $this->hasMany(CardLink::class)
            ->orderBy('sort_order');
    }
}
