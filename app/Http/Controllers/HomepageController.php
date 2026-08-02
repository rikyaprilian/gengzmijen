<?php

namespace App\Http\Controllers;

use App\Repositories\HomepageRepository;

class HomepageController extends Controller
{
    public function __construct(
        protected HomepageRepository $repository
    ) {
    }

    public function index()
    {
        $categories = $this->repository->getHomepageData();

        return view('homepage.index', compact('categories'));
    }
}