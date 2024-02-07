<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TenderController;
use App\Http\Controllers\LabourController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\PurchaseController;

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
    Route::get('tender/chage-status', [TenderController::class, 'chage_status']);

    Route::get('tender/payments/{id}', [TenderController::class, 'payments']);
    Route::post('tender/payment-store', [TenderController::class, 'payment_store']);
    Route::get('tender/fetch-payment-log', [TenderController::class, 'fetch_payment_log']);
    Route::get('tender/remove-payment-log/{id}', [TenderController::class, 'remove_payment_log']);



    Route::get('labours', [LabourController::class, 'index']);
    Route::post('labours/store', [LabourController::class, 'store']);
    Route::get('labours/fetch', [LabourController::class, 'fetch']);
    Route::get('labours/fetch-edit/{id}', [LabourController::class, 'fetch_edit']);
    Route::get('labours/delete/{id}', [LabourController::class, 'delete']);

    Route::get('materials', [MaterialController::class, 'index']);
    Route::post('materials/store', [MaterialController::class, 'store']);
    Route::get('materials/fetch', [MaterialController::class, 'fetch']);
    Route::get('materials/fetch-edit/{id}', [MaterialController::class, 'fetch_edit']);
    Route::get('materials/delete/{id}', [MaterialController::class, 'delete']);

    Route::get('vendors', [VendorController::class, 'index']);
    Route::post('vendors/store', [VendorController::class, 'store']);
    Route::get('vendors/fetch', [VendorController::class, 'fetch']);
    Route::get('vendors/fetch-edit/{id}', [VendorController::class, 'fetch_edit']);
    Route::get('vendors/delete/{id}', [VendorController::class, 'delete']);

    Route::get('expenses', [ExpenseController::class, 'index']);
    Route::post('expenses/store', [ExpenseController::class, 'store']);
    Route::get('expenses/fetch', [ExpenseController::class, 'fetch']);
    Route::get('expenses/fetch-edit/{id}', [ExpenseController::class, 'fetch_edit']);
    Route::get('expenses/delete/{id}', [ExpenseController::class, 'delete']);

    Route::get('purchase', [PurchaseController::class, 'index']);
    Route::post('purchase/store', [PurchaseController::class, 'store']);
    Route::get('purchase/fetch', [PurchaseController::class, 'fetch']);
    Route::get('purchase/fetch-edit/{id}', [PurchaseController::class, 'fetch_edit']);
    Route::get('purchase/delete/{id}', [PurchaseController::class, 'delete']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


