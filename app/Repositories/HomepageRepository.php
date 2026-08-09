<?php

namespace App\Repositories;

use App\Models\Card;
use App\Models\Category;
use App\Models\PortalSetting;

class HomepageRepository
{
    public function __construct(
        protected SettingRepository $settingRepository,
    ) {}

    public function getHomepageData(): array
    {
        $setting = PortalSetting::first();
        if (! $setting) {
            $setting = new PortalSetting($this->settingRepository->getAll());
        }

        return [
            'settings' => $setting,

            'cards' => Card::query()
                ->with([
                    'links' => function ($query) {
                        $query->orderBy('sort_order');
                    },
                    'categories',
                ])
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('expired_at')
                      ->orWhere('expired_at', '>', now());
                })
                ->orderBy('sort_order')
                ->get(),

            'categories' => Category::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
        ];
    }
}
