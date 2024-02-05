<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login_submit', [AuthController::class, 'login_submit'])->name('login.submit');

Route::group(['middleware' => ['admin']], function () {
    Route::get('/', [HomeController::class, 'dashboard'])->name('dashboard')->middleware('admin');
    Route::get('dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
