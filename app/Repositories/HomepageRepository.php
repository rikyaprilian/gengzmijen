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
                        $query->orderBy('sort_order')
                              ->where(function ($q) {
                                  // Tampilkan link yang: tidak punya expired_at, ATAU expired_at >= hari ini
                                  $q->whereNull('expired_at')
                                    ->orWhere('expired_at', '>=', today());
                              });
                    },
                    'categories',
                ])
                ->where('is_active', true)
                ->where(function ($q) {
                    // Card expired: tidak punya expired_at ATAU expired_at >= hari ini
                    $q->whereNull('expired_at')
                      ->orWhere('expired_at', '>=', today());
                })
                ->whereHas('links', function ($query) {
                    // Hanya kartu yang memiliki minimal 1 tautan aktif
                    $query->where(function ($q) {
                        $q->whereNull('expired_at')
                          ->orWhere('expired_at', '>=', today());
                    });
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
