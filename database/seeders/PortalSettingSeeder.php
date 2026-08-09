<?php

namespace Database\Seeders;

use App\Models\PortalSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PortalSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PortalSetting::updateOrCreate(
            ['id' => 1],
            [
                'portal_name' => 'Portal Link BGN',
                'homepage_message' => 'Tautan Harian Operasional BGN',
                'security_code' => 'gass',
                'logo' => null,
                'favicon' => null,
                'maintenance' => false,
            ]
        );
    }
}