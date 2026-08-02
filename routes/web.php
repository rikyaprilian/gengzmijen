<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomepageController;

// Route::get('/', function () {
//     return view('pages.home');
// });

Route::get('/', [HomepageController::class, 'index'])
    ->name('homepage');