<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Portal Settings
    |--------------------------------------------------------------------------
    */

    'settings' => [

        'portal_name' => 'Portal Gengz Mijen',

        'security_code' => '123456',

        'homepage_message' => 'Selamat Datang',

        'theme' => 'default',

    ],

    /*
    |--------------------------------------------------------------------------
    | Categories
    |--------------------------------------------------------------------------
    */

    'categories' => [

        'meeting' => [

            'name' => 'Meeting',

            'icon' => 'video',

            'color' => 'blue',

            'sort_order' => 1,

            'is_active' => true,

        ],

        'pelaporan' => [

            'name' => 'Pelaporan',

            'icon' => 'clipboard-list',

            'color' => 'success',

            'sort_order' => 2,

            'is_active' => true,

        ],

        'website' => [

            'name' => 'Website',

            'icon' => 'globe',

            'color' => 'info',

            'sort_order' => 3,

            'is_active' => true,

        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Cards
    |--------------------------------------------------------------------------
    */

    'cards' => [

        'sipgn' => [

            'category' => 'pelaporan',

            'title' => 'SIPGN',

            'description' => 'Portal Sistem Informasi Program Gizi Nasional',

            'badge' => null,

            'sort_order' => 1,

            'is_active' => true,

            'expired_at' => null,

        ],

        'tauwas-care' => [

            'category' => 'pelaporan',

            'title' => 'Tauwas Care',

            'description' => 'Portal Tauwas',

            'badge' => null,

            'sort_order' => 2,

            'is_active' => true,

            'expired_at' => null,

        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Card Links
    |--------------------------------------------------------------------------
    */

    'links' => [

        [
            'card' => 'sipgn',

            'title' => 'SIPGN SIPHR',

            'subtitle' => 'Absensi',

            'url' => 'https://sipgn-siphr.bgn.go.id/',

            'icon' => 'clipboard-check',

            'sort_order' => 1,

            'is_active' => true,

            'expired_at' => null,

        ],

        [
            'card' => 'sipgn',

            'title' => 'Portal Dashboard SIPGN',

            'subtitle' => 'Portal',

            'url' => 'https://portal-sipgn.bgn.go.id/dashboard',

            'icon' => 'layout-dashboard',

            'sort_order' => 2,

            'is_active' => true,

            'expired_at' => null,

        ],

        [
            'card' => 'sipgn',

            'title' => 'MPM SIPGN',

            'subtitle' => 'Management Penerima Manfaat',

            'url' => 'https://mpm-sipgn.bgn.go.id/dashboard',

            'icon' => 'users',

            'sort_order' => 3,

            'is_active' => true,

            'expired_at' => null,

        ],

        [
            'card' => 'sipgn',

            'title' => 'POP SIPGN',

            'subtitle' => 'Point of Production',

            'url' => 'https://pop-sipgn.bgn.go.id/cooking',

            'icon' => 'chef-hat',

            'sort_order' => 4,

            'is_active' => true,

            'expired_at' => null,

        ],

        [
            'card' => 'tauwas-care',

            'title' => 'Login',

            'subtitle' => 'Tauwas Care',

            'url' => 'https://tauwascare.tauwas.bgn.go.id/login',

            'icon' => 'heart-pulse',

            'sort_order' => 1,

            'is_active' => true,

            'expired_at' => null,

        ],

    ],

];