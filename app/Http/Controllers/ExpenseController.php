<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ExpenseController extends Controller
{
    public function index(){
        return view('Expense.create.index');
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

    public function fetch(Request $request)
    {
        $data = Expense::get();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '<div class="dropdown">
                            <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                <i class="dw dw-more"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                <button data-id="' . $row->id . '" class="edit-btn dropdown-item"><i class="dw dw-edit2"></i> Edit</button>
                                <a href="' . url('expenses/payments') . '/' . $row->id . '" class="dropdown-item"><i class="bi bi-cash-stack"></i> Payments</a>
                                <button data-id="' . $row->id . '" class="delete-btn dropdown-item"><i class="dw dw-delete-3"></i> Delete</button>
                            </div>
                        </div>';

                return $btn;
            })
            ->rawColumns(['action'])
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

    public function showPayments($id)
    {
        $expense = Expense::findOrFail($id);
        return view('expense.payments.index', ['expense' => $expense]);
    }
}
