<?php

namespace App\Http\Controllers;

use App\Models\ExpenseType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ExpenseTypeController extends Controller
{
    public function index() {
        return view('expense_type.index');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        if ($request->edit_id) {
            $ExpenseType = ExpenseType::find($request->edit_id);
            $message = "ExpenseType Updated Successfully";
        } else {
            $ExpenseType = new ExpenseType();
            $message = "ExpenseType Created Successfully";
        }

        $ExpenseType->name = $request->name;

        $ExpenseType->save();

        return array("status" => 1, "message" => $message);
    }

    public function fetch()
    {
        $data = ExpenseType::get();
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
        $ExpenseType = ExpenseType::find($id);
        return $ExpenseType;
    }

    public function delete($id)
    {
        ExpenseType::find($id)->delete();
        return array("status" => 1, "message" => "ExpenseType deleted successfully");
    }
}

