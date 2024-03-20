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
use App\Exports\ExpenseExport;
use Maatwebsite\Excel\Facades\Excel;

class BalanceLogController extends Controller
{

    public function index(Request $request)
    {
        $tenderLogs = TenderPaymentLog::with('tender')->get();
        $salaries = Salary::all();
        $expenses = Expense::all();
        $purchases = InvoicePurchase::with('purchaseType')->get();
        $tenders = Tender::pluck('name', 'id');
        $tenderLogsTransformed = $tenderLogs->map(function ($log) {
            return [
                'type' => 'Tender',
                'amount' => $log->amount,
                'description' => $log->description,
                'date' => $log->date,
                'job_order' => isset($log->job_order) ? $log->job_order : null,
            ];
        });

        $allLogs = array_merge($tenderLogs->toArray(), $salaries->toArray(), $expenses->toArray(), $purchases->toArray());
        // dd($allLogs);
        return view('balance_log.index', compact('tenders', 'allLogs'));
    }


    public function export(Request $request)
    {
        $tenderLogs = TenderPaymentLog::with('tender')->get();
        $salaries = Salary::all();
        $expenses = Expense::all();
        $purchases = InvoicePurchase::with('purchaseType')->get();

        $tenderQuery = TenderPaymentLog::with('tender');
        $salaryQuery = Salary::query();
        $expenseQuery = Expense::query();
        $purchaseQuery = InvoicePurchase::with('purchaseType');

        if ($request->has('date_range')) {
            $dates = explode(' - ', $request->date_range);
            $start_date = date('Y-m-d', strtotime($dates[0]));
            $end_date = date('Y-m-d', strtotime($dates[1]));

            $tenderQuery->whereBetween('date', [$start_date, $end_date]);
            $salaryQuery->whereBetween('date', [$start_date, $end_date]);
            $expenseQuery->whereBetween('date', [$start_date, $end_date]);
            $purchaseQuery->whereBetween('date', [$start_date, $end_date]);
        }

        if ($request->has('job_order')) {
            $job_order = $request->job_order;

            $tenderQuery->where('id', $job_order);
            $salaryQuery->where('job_order', $job_order);
            $expenseQuery->where('job_order', $job_order);
            $purchaseQuery->where('job_order_id', $job_order);
        }

        // Retrieve data from each model
        $tenderLogs = $tenderQuery->get();
        $salaries = $salaryQuery->get();
        $expenses = $expenseQuery->get();
        $purchases = $purchaseQuery->get();

        // Merge data from all models
        $allLogs = $tenderLogs->merge($salaries)->merge($expenses)->merge($purchases)->toArray();


        $allLogs = array_merge(
            $tenderLogs->toArray(),
            $salaries->toArray(),
            $expenses->toArray(),
            $purchases->toArray()
        );

        $exportData = [];
        $totalCredit = 0;
        $totalDebit = 0;

        foreach ($allLogs as $index => $log) {
            $jobOrder = '';
            if(isset($log['tender'])) {
                $jobOrder = $log['tender']['id'];
            } elseif(isset($log['job_order'])) {
                $jobOrder = $log['job_order'];
            } elseif(isset($log['job_order_id'])) {
                $jobOrder = $log['job_order_id'];
            }

            // Fetch name from the database based on the job order ID
            $name = '';
            if (!empty($jobOrder)) {
                $tender = Tender::find($jobOrder);
                if ($tender) {
                    $name = $tender->name;
                }
            }

            $description = isset($log['description']) ? $log['description'] : (isset($log['purchase_type']['name']) ? $log['purchase_type']['name'] : '');

            $credit = isset($log['type']) && $log['type'] === 'Credit' && isset($log['amount']) ? $log['amount'] : 0;
            $debit = isset($log['final_total']) ? $log['final_total'] : (!isset($log['type']) || $log['type'] !== 'Credit' && isset($log['amount']) ? $log['amount'] : 0);

            $totalCredit += $credit;
            $totalDebit += $debit;


            $exportData[] = [
                'S.No' => $index + 1,
                'Job Order' => $name,
                'Date' => date("d-m-Y", strtotime($log['date'])),
                'Description' => $description,
                'Credit' => $credit,
                'Debit' => $debit,
            ];
        }

        usort($exportData, function($a, $b) {
            return strtotime($a['Date']) - strtotime($b['Date']);
        });

        $data = [
            'view_file' => 'excel_export.balance_log',
            'export_data' => $exportData,
            'date_range' => $request->date_range,
            'total_credit' => $totalCredit,
            'total_debit' => $totalDebit,
        ];
        return Excel::download(new ExpenseExport($data), 'balance_log.xlsx');
    }
    }
