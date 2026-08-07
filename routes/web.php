<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\Manage\ManageAuthController;


// Route::get('/', function () {
//     return view('pages.home');
// });

Route::get('/', [HomepageController::class, 'index'])
    ->name('homepage');


Route::prefix('manage')->group(function () {

    Route::post('/login', [ManageAuthController::class, 'login'])
        ->name('manage.login');

    Route::post('/logout', [ManageAuthController::class, 'logout'])
        ->name('manage.logout');

    Route::get('/status', [ManageAuthController::class, 'status'])
        ->name('manage.status');
});
