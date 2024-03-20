<?php

namespace App\Http\Controllers;

use App\Models\BalanceLog;
use App\Models\Tender;
use App\Models\Salary;
use App\Models\Expense;
use App\Models\TenderPaymentLog;
use App\Http\Controllers\Controller;
use App\Models\InvoicePurchase;
use App\Models\PurchaseType;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BalanceLogController extends Controller
{
    public function index(Request $request)
    {
        $tenderLogs = TenderPaymentLog::with('tender')->get();
        $salaries = Salary::all();
        $expenses = Expense::all();
        $purchases = InvoicePurchase::with('purchaseType')->get();
        $tenders = Tender::pluck('name', 'id');
        return view('balance_log.index', compact('tenders', 'tenderLogs', 'salaries', 'expenses', 'purchases'));
    }

    public function export(Request $request)
    {
        // $query = Salary::orderBy('date', 'ASC')->get();
        $query = Salary::query();
        if ($request->has('date_range')) {
            $dates = explode(' - ', $request->date_range);

            $start_date = date('Y-m-d', strtotime($dates[0]));
            $end_date = date('Y-m-d', strtotime($dates[1]));

            $query->whereBetween('date', [$start_date, $end_date]);
        }

        if ($request->has('job_order')) {
            $query->where('job_order', $request->job_order);
        }

        $salary = $query->orderBy('date', 'ASC')->get();
        $date_range = $request->date_range;
        if ($salary->isEmpty()) {
            return redirect()->back()->with('error', 'No data found based on the selected criteria.');
        }

        $total_amount = $salary->sum('amount');

        $export_data = $salary->map(function ($salary, $index) {
            $jobOrderName = Tender::find($salary->job_order)->name;
            $payment_to = Labour::find($salary->labour_id)->name;
            return [
                'S.No' => $index + 1,
                'Job Order' => $jobOrderName,
                'Labour' => $salary->labour,
                'Date' => date("d-m-Y", strtotime($salary->date)),
                'Type' => $salary->type,
                'Description' => $salary->description,
                'Payment Mode' => $salary->payment_mode,
                'Payment To' => $salary->payment_to,
                'Payment Details' => $salary->payment_details,
                'Amount' => '₹' . number_format($salary->amount, 2),
            ];
        });

        $total_amount = "₹" . number_format($total_amount, 2);

        $export_data[] = [
            'S.No' => '',
            'Job Order' => '',
            'Labour' => '',
            'Date' => '',
            'Type' => '',
            'Description' => '',
            'Payment Mode' => '',
            'Payment To' => '',
            'Payment Details' => 'Total',
            'Amount' => $total_amount,
        ];

        $data = [
            'view_file' => 'excel_export.salary_export',
            'export_data' => $export_data,
            'date_range' =>  $date_range,
        ];

        return Excel::download(new ExpenseExport($data), 'salaries.xlsx');
    }
}
