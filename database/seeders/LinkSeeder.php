<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Link;
use Illuminate\Database\Seeder;

class LinkSeeder extends Seeder
{
    public function run(): void
    {
        $pelaporan = Category::where('name', 'Pelaporan')->first();

        Link::insert([

            [
                'category_id' => $pelaporan->id,
                'title' => 'SIPGN SIPHR',
                'description' => 'Sistem Informasi Human Resource',
                'url' => 'https://sipgn-siphr.bgn.go.id/',
                'icon' => 'people',
                'color' => 'blue',
                'sort_order' => 1,
                'is_active' => true,
                'open_new_tab' => true,
                'click_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'category_id' => $pelaporan->id,
                'title' => 'SIPGN SIPSMO Sign',
                'description' => 'Platform Tanda Tangan Digital',
                'url' => 'https://sipgn-sipsmo-web.bgn.go.id/Sign',
                'icon' => 'pen',
                'color' => 'purple',
                'sort_order' => 2,
                'is_active' => true,
                'open_new_tab' => true,
                'click_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'category_id' => $pelaporan->id,
                'title' => 'Project Management',
                'description' => 'Monitoring Progress Project',
                'url' => 'https://pm-sipgn.bgn.go.id/auth/login',
                'icon' => 'kanban',
                'color' => 'orange',
                'sort_order' => 3,
                'is_active' => true,
                'open_new_tab' => true,
                'click_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'category_id' => $pelaporan->id,
                'title' => 'SIPGN Public',
                'description' => 'Portal SIPGN',
                'url' => 'https://sipgn.bgn.go.id/public',
                'icon' => 'globe',
                'color' => 'green',
                'sort_order' => 4,
                'is_active' => true,
                'open_new_tab' => true,
                'click_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'category_id' => $pelaporan->id,
                'title' => 'MPM SIPGN',
                'description' => 'Monitoring Pelaksanaan MBG',
                'url' => 'https://mpm-sipgn.bgn.go.id/',
                'icon' => 'clipboard2-data',
                'color' => 'red',
                'sort_order' => 5,
                'is_active' => true,
                'open_new_tab' => true,
                'click_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}
