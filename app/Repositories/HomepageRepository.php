<?php

namespace App\Repositories;

use App\Models\Category;

class HomepageRepository
{
    public function getHomepageData()
    {
        return Category::query()
            ->with([
                'cards.links',
            ])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }
}