<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\TeamSettingsController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

//Dashboard view
Route::group(['middleware' => 'auth:sanctum'], function () {
    //Dashboard view
    Route::group(['prefix' => 'dashboard'], function () {
        Route::get('/', [DashboardController::class, 'index']
    )->name('dashboard');
        Route::put('/', [DashboardController::class, 'updateMass']
    )->name('dashboard.update.mass');
        Route::put('/{membership:id}', [DashboardController::class, 'update']
    )->name('dashboard.update');
    });

    //Match view
    Route::group(['prefix' => 'match'], function () {
        Route::get('/{match:id}', [MatchController::class, 'index']
    )->name('match');
        Route::post('/{match:id}', [MatchController::class, 'createSquads']
    )->name('match.squads');
    });

    //Team view
    Route::group(['prefix' => 'team'], function () {
        Route::post('/{team:id}', [TeamSettingsController::class, 'createUser']
    )->name('team.create.new_user');
        Route::post('/{team:id}/match', [TeamSettingsController::class, 'createMatch']
    )->name('team.create.new_match');
    });
});
