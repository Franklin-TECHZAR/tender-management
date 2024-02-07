<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Labour;
use Yajra\DataTables\Facades\DataTables;

class LabourController extends Controller
{
    public function index(Request $request) {
        return view('labour.index');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'type' => 'required',
            'mobile' => 'required',
            // 'address' => 'required',
        ]);

        if ($request->edit_id) {
            $labour = Labour::find($request->edit_id);
            $message = "Labour Updated Successfully";
        } else {
            $labour = new Labour();
            $message = "Labour Created Successfully";
        }

        $labour->name = $request->name;
        $labour->type = $request->type;
        $labour->mobile = $request->mobile;
        $labour->address = $request->address;

        $labour->save();

        return array("status" => 1, "message" => $message);
    }

    public function fetch()
    {
        $data = Labour::get();
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
        $labour = Labour::find($id);
        return $labour;
    }

    public function delete($id)
    {
        Labour::find($id)->delete();
        return array("status" => 1, "message" => "Labour deleted successfully");
    }
}
