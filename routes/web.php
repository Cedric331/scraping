<?php

use App\Http\Controllers\CacheController;
use App\Http\Controllers\SpiderController;
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
date_default_timezone_set('Europe/Paris');

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'numberUser' => rand(1, 99999),
        'date' => date('l jS \of F Y h:i:s A'),
    ]);
});

Route::post('spider', [SpiderController::class, 'spider']);

Route::get('cache', [CacheController::class, 'getCache']);

Route::post('cache', [CacheController::class, 'postCache']);

Route::delete('cache', [CacheController::class, 'deleteCache']);

//Route::get('/dashboard', function () {
//    return Inertia::render('Dashboard');
//})->middleware(['auth', 'verified'])->name('dashboard');

//require __DIR__.'/auth.php';
