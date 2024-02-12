<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use App\Models\Expense;
use App\Models\Tender;
use App\Models\ExpenseType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use PDF;


class ExpenseController extends Controller
{

    public function index(){
        $tenders = Tender::where('job_order', 1)
                         ->where('status', 1)
                         ->pluck('name');
        $ExpenseType = ExpenseType::get()->pluck('name');
        $Expense = Expense::get()->pluck('type');
        return view('Expense.create.index', compact('tenders', 'ExpenseType','Expense'));
    }

    public function getTypes(Request $request)
    {
        $jobOrder = $request->input('job_order');
        $types = Expense::where('job_order', $jobOrder)->pluck('type');
        return response()->json(['types' => $types]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'job_order' => 'required',
            'payment_to' => 'required',
            'date' => 'required',
            'type' => 'required',
            'amount' => 'required|numeric',
            // 'description' => 'required',
            'payment_mode' => 'required',
            'payment_details' => 'required',
        ]);

        if ($request->edit_id) {
            $Expense = Expense::find($request->edit_id);
            $message = "Expenses Updated Successfully";
        } else {
            $Expense = new Expense();
            $message = "Expenses Created Successfully";
        }

        $Expense->job_order = $request->job_order;
        $Expense->payment_to = $request->payment_to;
        $Expense->date = $request->date;
        $Expense->type = $request->type;
        $Expense->amount = $request->amount;
        $Expense->description = $request->description;
        $Expense->payment_mode = $request->payment_mode;
        $Expense->payment_details = $request->payment_details;
        $Expense->save();

        return array("status" => 1, "message" => $message);
    }

    public function fetch()
    {
        $data = Expense::get();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('amount', function ($row) {
                return "<span class='pull-right'>₹" . number_format($row->amount, 2) . "</span>";
            })
            ->addColumn('action', function ($row) {
                $btn = '<div class="dropdown">
                            <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                <i class="dw dw-more"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                            <button data-id="' . $row->id . '" class="export-btn dropdown-item"><i class="dw dw-download"></i> Download Receipt</a>
                            <button data-id="' . $row->id . '" class="view-btn dropdown-item"><i class="dw dw-view"></i> View</button>
                            <button data-id="' . $row->id . '" class="edit-btn dropdown-item"><i class="dw dw-edit2"></i> Edit</button>
                            <button data-id="' . $row->id . '" class="delete-btn dropdown-item"><i class="dw dw-delete-3"></i> Delete</button>
                            </div>
                        </div>';

                return $btn;
            })
            ->rawColumns(['action','amount'])
            ->make(true);
    }

        public function fetch_edit($id)
        {
            $Expense = Expense::find($id);
            return $Expense;
        }

        public function delete($id)
        {
            Expense::find($id)->delete();

            return array("status" => 1, "message" => "Expenses deleted successfully");
        }

            public function generatePDF($id)
            {
                $expense = Expense::findOrFail($id);
                $company_settings = CompanySetting::first();
                $address = $company_settings->address;
                $mobile = $company_settings->mobile;
                $email = $company_settings->email;
                $name = $company_settings->name;
                $data = [
                    'expense' => $expense,
                    'address' => $address,
                    'mobile' => $mobile,
                    'email' => $email,
                    'name' => $name,
                ];
                $pdf = PDF::loadView('pdf_export.expense_receipt', $data);
                return $pdf->stream('payment_receipt.pdf');
                    // return $pdf->download('payment_receipt.pdf');
            }

}
