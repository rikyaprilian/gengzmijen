<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::insert([
            [
                'name' => 'Pelaporan',
                'icon' => 'clipboard-data',
                'color' => 'blue',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Operasional',
                'icon' => 'gear',
                'color' => 'green',
                'sort_order' => 2,
                'is_active' => true,
            ],
        ]);
    }
}
