<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CardLink extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'card_id',
        'title',
        'url',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }
}