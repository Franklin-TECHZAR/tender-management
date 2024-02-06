<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TenderController;
use App\Http\Controllers\LabourController;

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
    Route::get('/', [HomeController::class, 'dashboard']);
    Route::get('dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

    Route::get('tender', [TenderController::class, 'index']);
    Route::post('tender/store', [TenderController::class, 'store']);
    Route::get('tender/fetch', [TenderController::class, 'fetch']);
    Route::get('tender/fetch-edit/{id}', [TenderController::class, 'fetch_edit']);
    Route::get('tender/delete/{id}', [TenderController::class, 'delete']);

    Route::get('labours', [LabourController::class, 'index']);
    Route::post('labours/store', [LabourController::class, 'store']);
    Route::get('labours/fetch', [LabourController::class, 'fetch']);
    Route::get('labours/fetch-edit/{id}', [LabourController::class, 'fetch_edit']);
    Route::get('labours/delete/{id}', [LabourController::class, 'delete']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


