<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tender;
use App\Models\InvoicePurchase;
use App\Models\Salary;
use App\Models\Labour;
use App\Models\ExpenseType;
use App\Models\Expense;
use App\Models\Vendor;
use App\Models\Material;
use App\Models\CompanySetting;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\ExpenseExport;
use App\Exports\PurchaseExport;
use App\Models\PurchaseType;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function purchase_index()
    {
        $tenders = Tender::where('job_order', 1)
            ->where('status', 1)
            ->pluck('name', 'id');
        $purchaseTypes = PurchaseType::pluck('name', 'id');
        return view('Report.Purchse_Report.index', compact('tenders', 'purchaseTypes'));
    }

    public function purchase_fetch()
    {
        $purchases = InvoicePurchase::with('invoiceProduct')->get();
        // $purchases = InvoicePurchase::orderBy('date', 'ASC')->get();
        $vendors = Vendor::pluck('agency_name', 'id');
        $purchaseType = PurchaseType::pluck('name', 'id');
        $materials = Material::pluck('name', 'id');
        return response()->json(['purchases' => $purchases, 'vendors' => $vendors, 'purchaseType' => $purchaseType, 'materials' => $materials]);
    }


    // public function purchase_fetch()
    // {
    //     $data = InvoicePurchase::with(['vendor:id,agency_name', 'material:id,name'])
    //         ->orderBy('id', 'DESC')
    //         ->get();
    //     return DataTables::of($data)
    //         ->addIndexColumn()
    //         ->addColumn('amount', function ($row) {
    //             return "<span class='pull-right'>₹" . number_format($row->amount, 2) . "</span>";
    //         })
    //         ->addColumn('total', function ($row) {
    //             return "<span class='pull-right'>₹" . number_format($row->total, 2) . " /-</span>";
    //         })
    //         ->addColumn('vendor', function ($row) {
    //             return $row->vendor ? $row->vendor->agency_name : '';
    //         })
    //         ->addColumn('material', function ($row) {
    //             return $row->material ? $row->material->name : '';
    //         })
    //         ->addColumn('action', function ($row) {
    //             $btn = '<div class="dropdown">
    //                 <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
    //                                 <i class="dw dw-more"></i>
    //                             </a>
    //                             <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
    //                                 <a href="/generatePurchase-pdf/' . $row->id . '" class="dropdown-item" target="_blank"><i class="dw dw-download"></i> Download Receipt</a>
    //                                 <a href="/purchase/create/' . $row->id . '" class="dropdown-item"><i class="dw dw-edit2"></i> Edit</a>
    //                                 <button data-id="' . $row->id . '" class="delete-btn dropdown-item"><i class="dw dw-delete-3"></i> Delete</button>
    //                                 </div>
    //                                 </div>';
    //             return $btn;
    //         })
    //         ->rawColumns(['action', 'amount', 'total'])
    //         ->make(true);
    // }


    public function purchase_export(Request $request)
    {
        $query = InvoicePurchase::query();
        // $query = Expense::query();

        if ($request->has('date_range')) {
            $dates = explode(' - ', $request->date_range);

            $start_date = date('Y-m-d', strtotime($dates[0]));
            $end_date = date('Y-m-d', strtotime($dates[1]));

            $query->whereBetween('date', [$start_date, $end_date]);
        }

        if ($request->has('job_orders')) {
            $query->whereIn('job_order_id', $request->job_orders);
        }

        if ($request->has('purchase_type')) {
            $query->where('type', $request->type);
        }

        // $purchases = $query->orderBy('date', 'ASC')->get();

        $purchases = $query->with('vendor', 'invoiceProduct.material')->get();
        // dd($purchases);

        if ($purchases->isEmpty()) {
            return redirect()->back()->with('error', 'No data found based on the selected criteria.');
        }

        $export_data = [];
        $total_amount = 0;
        $date_range = $request->date_range;

        foreach ($purchases as $purchase) {
            if (!$purchase->deleted_at) {
                foreach ($purchase->invoiceProduct as $invoiceProduct) {
                    if (!$invoiceProduct->deleted_at) {
                        $jobOrderName = Tender::find($purchase->job_order_id)->name;
                        $PurchaseType = PurchaseType::find($purchase->type)->name;
                        $export_data[] = [
                            'S.No' => count($export_data) + 1,
                            'Job Order' => $jobOrderName,
                            'Type' => $PurchaseType,
                            'Date' => date("d-m-Y", strtotime($purchase->date)),
                            'Invoice No' => $purchase->invoice_no,
                            'Vendor' => $purchase->vendor->agency_name,
                            'Product/Material' => $invoiceProduct->material->name,
                            'Quantity' => $invoiceProduct->quantity,
                            'Amount' => '₹' . number_format($invoiceProduct->amount, 2),
                            'GST' => $invoiceProduct->gst . '%',
                            'Total' => '₹' . number_format($invoiceProduct->total, 2),
                        ];

                        $total_amount += $invoiceProduct->total;
                    }
                }
            }
        }

        $total_amount = "₹" . number_format($total_amount, 2);

        $export_data[] = [
            'S.No' => '',
            'Job Order' => '',
            'Type' => '',
            'Date' => '',
            'Invoice No' => '',
            'Vendor' => '',
            'Product/Material' => '',
            'Quantity' => '',
            'Amount' => '',
            'GST' => '',
            'Total' => $total_amount,
        ];

        $data = [
            'view_file' => 'excel_export.purchase_export',
            'export_data' => $export_data,
            'date_range' =>  $date_range,
        ];

        return Excel::download(new PurchaseExport($data), 'purchase.xlsx');
    }

    //salary

    public function salary_create()
    {
        $tenders = Tender::where('job_order', 1)
            ->where('status', 1)
            ->pluck('name', 'id');
        $labours = Labour::get('name');
        return view('Report.Salaries_Report.index', compact('tenders', 'labours'));
    }


    public function salary_fetch()
    {
        $salaries = Salary::orderBy('date', 'ASC')->get();
        $labours = Labour::pluck('name', 'id')->all();
        // dd($labours);
        return response()->json(['salaries' => $salaries, 'labours' => $labours]);
    }

    public function salary_export(Request $request)
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


    // expenses

    public function expense_index()
    {
        $tenders = Tender::where('job_order', 1)
            ->where('status', 1)
            ->pluck('name', 'id');
        $ExpenseType = ExpenseType::pluck('name', 'id');
        $Expense = Expense::get()->pluck('type');
        return view('Report.Expenses_Report.index', compact('tenders', 'ExpenseType', 'Expense'));
    }

    public function expense_fetch()
    {
        // $expenseType = ExpenseType::get()->pluck('name','id');
        $expenseType = ExpenseType::pluck('name', 'id');
        $expense = Expense::orderBy('date', 'ASC')->get();
        return response()->json(['expense' => $expense, 'expenseType' => $expenseType]);
    }


    public function expense_export(Request $request)
    {
        $query = Expense::query();

        if ($request->has('date_range')) {
            $dates = explode(' - ', $request->date_range);

            $start_date = date('Y-m-d', strtotime($dates[0]));
            $end_date = date('Y-m-d', strtotime($dates[1]));

            $query->whereBetween('date', [$start_date, $end_date]);
        }

        if ($request->has('job_order')) {
            $query->where('job_order', $request->job_order);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $expenses = $query->orderBy('date', 'ASC')->get();

        if ($expenses->isEmpty()) {
            return redirect()->back()->with('error', 'No data found based on the selected criteria.');
        }

        $total_amount = $expenses->sum('amount');
        $date_range = $request->date_range;
        $export_data = $expenses->map(function ($expense, $index) {
            $jobOrderName = Tender::find($expense->job_order)->name;
            $ExpenseType = ExpenseType::find($expense->type)->name;
            return [
                'S.No' => $index + 1,
                'Job Order' => $jobOrderName,
                'Payment To' => $expense->payment_to,
                'Date' => date("d-m-Y", strtotime($expense->date)),
                'Type' => $ExpenseType,
                'Description' => $expense->description,
                'Payment Mode' => $expense->payment_mode,
                'Payment Details' => $expense->payment_details,
                'Amount' => '₹' . number_format($expense->amount, 2),
            ];
        });

        $total_amount = "₹" . number_format($total_amount, 2);

        $export_data[] = [
            'S.No' => '',
            'Job Order' => '',
            'Payment To' => '',
            'Date' => '',
            'Type' => '',
            'Description' => '',
            'Payment Mode' => '',
            'Payment Details' => 'Total',
            'Amount' => $total_amount,
        ];

        $data = [
            'view_file' => 'excel_export.expense_export',
            'export_data' => $export_data,
            'date_range' =>  $date_range,
        ];

        return Excel::download(new ExpenseExport($data), 'expenses.xlsx');
    }
}
