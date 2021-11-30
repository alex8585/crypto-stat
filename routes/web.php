<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\StatisticsController;

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

require __DIR__ . '/auth.php';


Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('statistics', [StatisticsController::class, 'index'])->name('statistics');
    //Route::get('/', [UsersController::class, 'index'])->name('users');
    //Route::get('users', [UsersController::class, 'index'])->name('users');
    //Route::get('activities', [ActivitiesController::class, 'index'])->name('activities');
});
