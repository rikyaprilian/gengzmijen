<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\Manage\ManageAuthController;


// Route::get('/', function () {
//     return view('pages.home');
// });

use App\Http\Controllers\Manage\ManagePortalController;

Route::get('/', [HomepageController::class, 'index'])
    ->name('homepage');

Route::prefix('manage')->group(function () {

    Route::post('/login', [ManageAuthController::class, 'login'])
        ->name('manage.login');

    Route::post('/logout', [ManageAuthController::class, 'logout'])
        ->name('manage.logout');

    Route::get('/status', [ManageAuthController::class, 'status'])
        ->name('manage.status');

    // Settings
    Route::post('/settings', [ManagePortalController::class, 'updateSettings'])
        ->name('manage.settings.update');

    // Categories
    Route::get('/categories', [ManagePortalController::class, 'getCategories'])
        ->name('manage.categories.index');
    Route::post('/categories', [ManagePortalController::class, 'storeCategory'])
        ->name('manage.categories.store');
    Route::put('/categories/{uuid}', [ManagePortalController::class, 'updateCategory'])
        ->name('manage.categories.update');
    Route::delete('/categories/{uuid}', [ManagePortalController::class, 'destroyCategory'])
        ->name('manage.categories.destroy');

    // Cards
    Route::post('/cards', [ManagePortalController::class, 'storeCard'])
        ->name('manage.cards.store');
    Route::put('/cards/{uuid}', [ManagePortalController::class, 'updateCard'])
        ->name('manage.cards.update');
    Route::delete('/cards/{uuid}', [ManagePortalController::class, 'destroyCard'])
        ->name('manage.cards.destroy');
    Route::post('/cards/reorder', [ManagePortalController::class, 'reorderCards'])
        ->name('manage.cards.reorder');

    // Links
    Route::post('/links', [ManagePortalController::class, 'storeLink'])
        ->name('manage.links.store');
    Route::put('/links/{uuid}', [ManagePortalController::class, 'updateLink'])
        ->name('manage.links.update');
    Route::delete('/links/{uuid}', [ManagePortalController::class, 'destroyLink'])
        ->name('manage.links.destroy');
    Route::post('/links/reorder', [ManagePortalController::class, 'reorderLinks'])
        ->name('manage.links.reorder');
});
