<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'name' => fake()->words(2, true),
            'slug' => fake()->slug(),
            'icon' => 'folder',
            'color' => 'primary',
            'sort_order' => 0,
            'is_active' => true,
        ];
    }
}