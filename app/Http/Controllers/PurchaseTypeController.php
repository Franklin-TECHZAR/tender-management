<?php

namespace App\Http\Controllers;

use App\Models\PurchaseType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PurchaseTypeController extends Controller
{
    public function index() {
        return view('purchase_type.index');
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        if ($request->edit_id) {
            $PurchaseType = PurchaseType::find($request->edit_id);
            $message = "PurchaseType Updated Successfully";
        } else {
            $PurchaseType = new PurchaseType();
            $message = "PurchaseType Created Successfully";
        }

        $PurchaseType->name = $request->name;

        $PurchaseType->save();

        return array("status" => 1, "message" => $message);
    }

    public function fetch()
    {
        $data = PurchaseType::get();
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
        $PurchaseType = PurchaseType::find($id);
        return $PurchaseType;
    }

    public function delete($id)
    {
        PurchaseType::find($id)->delete();
        return array("status" => 1, "message" => "PurchaseType deleted successfully");
    }
}
