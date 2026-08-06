<?php

namespace App\Repositories;

use App\Models\PortalSetting;

class SettingRepository
{
    public function getAll(): array
    {
        $setting = PortalSetting::query()->first();

        if (! $setting) {
            return [];
        }

        return [
            'portal_name'      => $setting->portal_name,
            'homepage_message' => $setting->homepage_message,
            'security_code'    => $setting->security_code,
            'logo'             => $setting->logo,
            'favicon'          => $setting->favicon,
            'maintenance'      => $setting->maintenance,
        ];
    }
}