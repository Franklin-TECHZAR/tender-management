<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\InvoicePurchase;
use App\Models\Salary;
use App\Models\Tender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    // public function dashboard()
    // {
    //     return view('dashboard');
    // }

    public function dashboard()
    {
    //     $tenders = Tender::where('tenders.job_order', 1)
    // ->join('invoice_purchases', 'tenders.id', '=', 'invoice_purchases.job_order_id')
    // ->join('salaries', 'tenders.id', '=', 'salaries.job_order')
    // ->join('expenses', 'tenders.id', '=', 'expenses.job_order')
    // ->pluck('tenders.name', 'tenders.id');

        $tenders = Tender::where('job_order', 1)
            ->where('status', 1)
            ->pluck('name', 'id');

        $tenderIds = Tender::where('job_order', 1)
            ->where('status', 1)
            ->pluck('id');

        $purchaseData = InvoicePurchase::selectRaw('job_order_id, final_total, DATE_FORMAT(date, "%b") AS month_name')
            ->pluck('final_total', 'month_name')
            ->toArray();

        $purchaseIds = InvoicePurchase::selectRaw('job_order_id, final_total')
            ->pluck('job_order_id');

        $salaryData = Salary::whereNotNull('amount')
            ->selectRaw('job_order, SUM(amount) as total_amount, DATE_FORMAT(date, "%b") AS month_name')
            ->groupBy('month_name','job_order')
            ->pluck('total_amount', 'month_name')
            ->toArray();
            $expenseData = Expense::whereNotNull('amount')
            ->selectRaw('job_order, SUM(amount) as total_amount, DATE_FORMAT(date, "%b") AS month_name')
            ->groupBy('job_order', 'month_name')
            ->pluck('total_amount', 'month_name')
            ->toArray();

        // Pass all the data to the view
        return view('dashboard', compact('purchaseData', 'salaryData', 'expenseData', 'tenders'));
    }

    // public function dashboard()
    // {
    //     $data = [];

    //     $tenderIds = DB::table('tenders')
    //         ->whereIn('job_order', InvoicePurchase::pluck('job_order_id')->toArray())
    //         ->pluck('id')->toArray();

    //     $purchaseData = InvoicePurchase::selectRaw('final_total, job_order_id')
    //         ->pluck('final_total', 'job_order_id')
    //         ->toArray();

    //     $salaryData = DB::table('invoice_purchases')
    //         ->leftJoin('salaries', 'invoice_purchases.job_order_id', '=', 'salaries.job_order')
    //         ->whereNotNull('salaries.amount')
    //         ->selectRaw('salaries.amount, DATE_FORMAT(salaries.date, "%b") AS month_name, salaries.job_order as job_order_id')
    //         ->pluck('salaries.amount', 'job_order_id')
    //         ->toArray();

    //     $expenseData = DB::table('invoice_purchases')
    //         ->leftJoin('expenses', 'invoice_purchases.job_order_id', '=', 'expenses.job_order')
    //         ->whereNotNull('expenses.amount')
    //         ->selectRaw('expenses.amount, DATE_FORMAT(expenses.date, "%b") AS month_name, expenses.job_order as job_order_id')
    //         ->pluck('expenses.amount', 'job_order_id')
    //         ->toArray();
    //         dd($purchaseData);
    //     return view('dashboard', compact('purchaseData', 'salaryData', 'expenseData'));
    // }


// correct

    // public function dashboard()
    // {
    //     $tenders = Tender::where('job_order', 1)
    //     ->where('status', 1)
    //     ->pluck('name', 'id');
    //     $purchaseData = InvoicePurchase::selectRaw('final_total, DATE_FORMAT(date, "%b") AS month_name')
    //         ->pluck('final_total', 'month_name')
    //         ->toArray();

    //         $salaryData = Salary::whereNotNull('amount')
    //         ->selectRaw('amount, DATE_FORMAT(date, "%b") AS month_name')
    //         ->get()
    //         ->groupBy('month_name')
    //         ->map(function ($items) {
    //             return $items->sum('amount');
    //         })
    //         ->toArray();

    //         $expenseData = Expense::whereNotNull('amount')
    //         ->selectRaw('SUM(amount) as total_amount, DATE_FORMAT(date, "%b") AS month_name')
    //         ->groupBy('month_name')
    //         ->pluck('total_amount', 'month_name')
    //         ->toArray();

    //         // dd($salaryData);
    //     return view('dashboard', compact('purchaseData', 'salaryData', 'expenseData','tenders'));
    // }


}
