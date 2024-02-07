<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ExpenseController extends Controller
{
    public function index() {
        return view('expense.index');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        if ($request->edit_id) {
            $expense = Expense::find($request->edit_id);
            $message = "Expense Updated Successfully";
        } else {
            $expense = new Expense();
            $message = "Expense Created Successfully";
        }

        $expense->name = $request->name;

        $expense->save();

        return array("status" => 1, "message" => $message);
    }

    public function fetch()
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
        $expense = Expense::find($id);
        return $expense;
    }

    public function delete($id)
    {
        Expense::find($id)->delete();
        return array("status" => 1, "message" => "FAQ deleted successfully");
    }
}

