<?php

namespace App\Http\Controllers;

use App\Repositories\HomepageRepository;
use App\Repositories\SettingRepository;

class HomepageController extends Controller
{
    public function __construct(
        protected HomepageRepository $homepageRepository,
        protected SettingRepository $settingRepository,
    ) {}

    public function index()
    {
        $data = $this->homepageRepository->getHomepageData();

        return view('pages.home', [

            'cards'      => $data['cards'],

            'categories' => $data['categories'],

            'settings'   => $data['settings'],

        ]);
    }
}
