<?php

namespace App\Repositories;

use App\Models\Setting;

class SettingRepository
{
    public function getAll(): array
    {
        return Setting::pluck('value', 'key')->toArray();
    }
}