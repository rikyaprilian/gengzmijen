<?php

namespace Database\Factories;

use App\Models\CardLink;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CardLink>
 */
class CardLinkFactory extends Factory
{
    protected $model = CardLink::class;

    public function definition(): array
    {
        return [

            'uuid' => (string) Str::uuid(),
        
            'card_id' => null,
        
            'title' => fake()->words(2, true),
        
            'subtitle' => fake()->sentence(2),
        
            'url' => fake()->url(),
        
            'icon' => 'link',
        
            'sort_order' => 1,
        
            'is_active' => true,
        
            'expired_at' => null,
        
        ];
    }
}