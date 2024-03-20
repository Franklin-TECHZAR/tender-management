<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TenderController;
use App\Http\Controllers\LabourController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ExpenseTypeController;
use App\Http\Controllers\PurchaseTypeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\LabourReportController;
use App\Http\Controllers\BalanceLogController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\profileController;
use App\Http\Controllers\VendorPaymentController;
use App\Http\Controllers\ReturnBalanceController;

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
    Route::get('tender/payment-export/{id}', [TenderController::class, 'payment_export']);


    Route::get('purchase_dept/payments/{id}', [PaymentController::class, 'payments']);
    Route::post('purchase_dept/payment-store', [PaymentController::class, 'payment_store']);
    Route::get('purchase_dept/fetch-payment-log', [PaymentController::class, 'fetch_payment_log']);
    Route::get('purchase_dept/remove-payment-log/{id}', [PaymentController::class, 'remove_payment_log']);
    Route::get('purchase_dept/payment-export/{id}', [PaymentController::class, 'payment_export']);

    Route::get('vendor_payment/payments/{id}', [VendorPaymentController::class, 'payments']);
    Route::post('vendor_payment/payment-store', [VendorPaymentController::class, 'payment_store']);
    Route::get('vendor_payment/fetch-payment-log', [VendorPaymentController::class, 'fetch_payment_log']);
    Route::get('vendor_payment/remove-payment-log/{id}', [VendorPaymentController::class, 'remove_payment_log']);
    Route::get('vendor_payment/payment-export/{id}', [VendorPaymentController::class, 'payment_export']);


    Route::get('purchase', [PurchaseController::class, 'index']);
    Route::get('purchase/create', [PurchaseController::class, 'create'])->name('purchase.create');
    Route::get('purchase/create/{id}', [PurchaseController::class, 'edit'])->name('purchase.edit');
    Route::post('purchase/submit', [PurchaseController::class, 'submit']);
    Route::post('purchase/store', [PurchaseController::class, 'store'])->name('purchase.store');
    Route::get('purchase/fetch', [PurchaseController::class, 'fetch']);
    Route::get('purchase/fetch-edit/{id}', [PurchaseController::class, 'fetch_edit']);
    Route::get('purchase/delete/{id}', [PurchaseController::class, 'delete']);
    Route::get('generatePurchase-pdf/{id}', [PurchaseController::class, 'generatePDF'])->name('generatePurchase.pdf');
    Route::get('purchase_export', [PurchaseController::class, 'export']);



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

    Route::get('return_balance', [ReturnBalanceController::class, 'index']);
    Route::post('return_balance/store', [ReturnBalanceController::class, 'store']);
    Route::get('return_balance/fetch', [ReturnBalanceController::class, 'fetch']);
    Route::get('return_balance/fetch-edit/{id}', [ReturnBalanceController::class, 'fetch_edit']);
    Route::get('return_balance/delete/{id}', [ReturnBalanceController::class, 'delete']);

    Route::get('expenses_type', [ExpenseTypeController::class, 'index']);
    Route::post('expenses_type/store', [ExpenseTypeController::class, 'store']);
    Route::get('expenses_type/fetch', [ExpenseTypeController::class, 'fetch']);
    Route::get('expenses_type/fetch-edit/{id}', [ExpenseTypeController::class, 'fetch_edit']);
    Route::get('expenses_type/delete/{id}', [ExpenseTypeController::class, 'delete']);

    Route::get('purchase_type', [PurchaseTypeController::class, 'index']);
    Route::post('purchase_type/store', [PurchaseTypeController::class, 'store']);
    Route::get('purchase_type/fetch', [PurchaseTypeController::class, 'fetch']);
    Route::get('purchase_type/fetch-edit/{id}', [PurchaseTypeController::class, 'fetch_edit']);
    Route::get('purchase_type/delete/{id}', [PurchaseTypeController::class, 'delete']);

    Route::get('expenses', [ExpenseController::class, 'index']);
    Route::post('expenses/store', [ExpenseController::class, 'store']);
    Route::get('expenses/fetch', [ExpenseController::class, 'fetch']);
    Route::get('expenses/fetch-edit/{id}', [ExpenseController::class, 'fetch_edit']);
    Route::get('expenses/delete/{id}', [ExpenseController::class, 'delete']);
    Route::get('expense_export', [ExpenseController::class, 'export']);
    Route::get('generate-pdf/{id}', [ExpenseController::class, 'generatePDF'])->name('generate.pdf');
    Route::get('/get-types', [ExpenseController::class, 'getTypes'])->name('get.types');

    Route::get('labour_report', [LabourReportController::class, 'index']);
    Route::post('labour_report/store', [LabourReportController::class, 'store']);
    Route::get('labour_report/fetch', [LabourReportController::class, 'fetch']);
    Route::get('labour_report/fetch-edit/{id}', [LabourReportController::class, 'fetch_edit']);
    Route::get('labour_report/delete/{id}', [LabourReportController::class, 'delete']);
    Route::get('labour_export', [LabourReportController::class, 'export']);
    Route::post('labour_report/check_date', [LabourReportController::class, 'checkDate'])->name('labour_report.check_date');


    Route::get('balance_log', [BalanceLogController::class, 'index'])->name('Balance_log.index');
    Route::post('balance_log/store', [BalanceLogController::class, 'store']);
    Route::get('balance_log/fetch', [BalanceLogController::class, 'fetch']);
    Route::get('balance_log/fetch-edit/{id}', [BalanceLogController::class, 'fetch_edit']);
    Route::get('balance_log/delete/{id}', [BalanceLogController::class, 'delete']);
    Route::get('balance_log/report', [BalanceLogController::class, 'export']);
    Route::post('balance_log/check_date', [BalanceLogController::class, 'checkDate'])->name('balance_log.check_date');


    Route::get('/salaries', [SalaryController::class, 'create'])->name('salaries.create');
    Route::post('/salaries/store', [SalaryController::class, 'store'])->name('salaries.store');
    Route::get('salaries/fetch', [SalaryController::class, 'fetch']);
    Route::get('salaries/fetch-edit/{id}', [SalaryController::class, 'fetch_edit']);
    Route::get('salaries/delete/{id}', [SalaryController::class, 'delete']);
    Route::get('export', [SalaryController::class, 'export']);
    Route::get('generatesalary-pdf/{id}', [SalaryController::class, 'generatePDF'])->name('generatesalary.pdf');

    Route::get('/purchase_dept', [PaymentController::class, 'create'])->name('purchase_dept.create');
    Route::post('/purchase_dept/store', [PaymentController::class, 'store'])->name('purchase_dept.store');
    Route::get('purchase_dept/fetch', [PaymentController::class, 'fetch']);
    Route::get('purchase_dept/fetch-edit/{id}', [PaymentController::class, 'fetch_edit']);
    Route::get('purchase_dept/delete/{id}', [PaymentController::class, 'delete']);

    Route::get('/vendor_payment', [VendorPaymentController::class, 'create'])->name('vendor_payment.create');
    Route::post('/vendor_payment/store', [VendorPaymentController::class, 'store'])->name('vendor_payment.store');
    Route::get('vendor_payment/fetch', [VendorPaymentController::class, 'fetch']);
    Route::get('vendor_payment/fetch-edit/{id}', [VendorPaymentController::class, 'fetch_edit']);
    Route::get('vendor_payment/delete/{id}', [VendorPaymentController::class, 'delete']);

    Route::get('purchases_report', [ReportController::class, 'purchase_index']);
    Route::get('report/purchase_fetch', [ReportController::class, 'purchase_fetch'])->name('purchases.fetch');
    Route::get('purchases_export/report', [ReportController::class, 'purchase_export']);

    Route::get('salaries_report', [ReportController::class, 'salary_create']);
    Route::get('report/salary_fetch', [ReportController::class, 'salary_fetch'])->name('salaries.fetch');
    Route::get('salaries_report/report', [ReportController::class, 'salary_export']);

    Route::get('expenses_report', [ReportController::class, 'expense_index']);
    Route::get('report/expense_fetch', [ReportController::class, 'expense_fetch'])->name('expenses.fetch');
    Route::get('expense_export/report', [ReportController::class, 'expense_export']);


    /* Users */

    Route::get('users', [UserController::class, 'index']);
    Route::post('users/store', [UserController::class, 'store']);
    Route::get('users/fetch', [UserController::class, 'fetch']);
    Route::get('users/fetch-edit/{id}', [UserController::class, 'fetch_edit']);
    Route::get('users/delete/{id}', [UserController::class, 'delete']);

    /* permission */
    Route::get('permissions', [PermissionController::class, 'index']);
    Route::post('permissions/store', [PermissionController::class, 'store']);
    Route::get('permissions/fetch', [PermissionController::class, 'fetch']);
    Route::get('permissions/fetch-edit/{id}', [PermissionController::class, 'fetch_edit']);
    Route::get('permissions/delete/{id}', [PermissionController::class, 'delete']);

    /*profile*/
    Route::get('profile', [profileController::class, 'index']);
    Route::post('profile/store', [profileController::class, 'store']);
    Route::get('profile/fetch', [profileController::class, 'fetch']);
    Route::get('profile/fetch-edit/{id}', [profileController::class, 'fetch_edit']);


    /* roles */
    Route::get('roles', [RoleController::class, 'index']);
    Route::post('roles/store', [RoleController::class, 'store']);
    Route::get('roles/fetch', [RoleController::class, 'fetch']);
    Route::get('roles/fetch-edit/{id}', [RoleController::class, 'fetch_edit']);
    Route::get('roles/delete/{id}', [RoleController::class, 'delete']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
