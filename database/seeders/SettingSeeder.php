<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::insert([

            [
                'key' => 'portal_name',
                'value' => 'Portal Pelaporan BGN',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'key' => 'portal_subtitle',
                'value' => 'Karya Gengs Mijen',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'key' => 'portal_logo',
                'value' => 'logo-bgn.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'key' => 'portal_favicon',
                'value' => 'logo-bgn.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}
