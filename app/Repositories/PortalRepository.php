<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Setting;

class PortalRepository
{
    public function getHomepage(): array
    {
        return [
            'settings' => Setting::all()->pluck('value', 'key'),

            'categories' => Category::query()
                ->where('is_active', true)
                ->with([
                    'cards' => function ($query) {
                        $query->where('is_active', true)
                            ->orderBy('sort_order');
                    },
                    'cards.links' => function ($query) {
                        $query->where('is_active', true)
                            ->orderBy('sort_order');
                    }
                ])
                ->orderBy('sort_order')
                ->get(),
        ];
    }
}