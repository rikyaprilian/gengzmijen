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
        PortalSetting::create([

            'portal_name' => 'Portal Pelaporan BGN',

            'homepage_message' => 'Selamat datang di Portal Pelaporan BGN',

            'security_code' => Hash::make('123456'),

            'logo' => null,

            'favicon' => null,

            'maintenance' => false,

        ]);
    }
}