<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CardLink extends Model
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

    protected $fillable = [

        'uuid',
    
        'card_id',
    
        'title',
    
        'subtitle',
    
        'url',
    
        'icon',
    
        'sort_order',
    
        'is_active',
    
        'expired_at',
    
    ];

    protected $casts = [

        'is_active' => 'boolean',
    
        'expired_at' => 'datetime',
    
    ];

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }
}