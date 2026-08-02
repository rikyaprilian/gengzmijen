<?php

namespace App\Http\Controllers;

use App\Repositories\HomepageRepository;
use App\Repositories\SettingRepository;

class HomepageController extends Controller
{
    public function __construct(
        protected HomepageRepository $homepageRepository,
        protected SettingRepository $settingRepository,
    ) {
    }

    public function index()
    {
        return view('pages.home', [
            'categories' => $this->homepageRepository->getHomepageData(),
            'settings'   => $this->settingRepository->getAll(),
        ]);
    }
}