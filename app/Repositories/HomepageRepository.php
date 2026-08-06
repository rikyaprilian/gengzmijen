<?php

namespace App\Repositories;

use App\Models\Card;
use App\Models\Category;
use App\Models\PortalSetting;

class HomepageRepository
{
    public function getHomepageData(): array
    {
        return [

            'settings' => PortalSetting::query()->first(),

            'cards' => Card::query()
                ->with([
                    'links',
                    'categories',
                ])
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(),

            'categories' => Category::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(),

        ];
    }
}
