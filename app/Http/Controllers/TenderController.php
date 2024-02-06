<?php

namespace App\Http\Controllers;

use App\Models\Tender;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TenderController extends Controller
{
    public function index()
    {
        return view('tender.index');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'city' => 'required',
            'address' => 'required',
            'budget' => 'required',
            'description' => 'required',
        ]);

        if ($request->edit_id) {
            $tender = Tender::find($request->edit_id);
            $message = "Tender Updated Successfully";
        } else {
            $tender = new Tender();
            $message = "Tender Created Successfully";
            $tender->status = 1;
        }

        $tender->name = $request->name;
        $tender->city = $request->city;
        $tender->address = $request->address;
        $tender->budget = $request->budget;
        $tender->description = $request->description;

        $tender->save();

        return array("status" => 1, "message" => $message);
    }

    public function fetch()
    {
        $data = Tender::get();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('status', function ($row) {
                if ($row->status == 1) {
                    return '<span class="badge badge-success">Active</span>';
                } else {
                    return '<span class="badge badge-danger">InActive</span>';
                }
            })
            ->addColumn('action', function ($row) {

                $status_btn = '';
                if ($row->status == 1) {
                    $status_btn = '<button data-id="' . $row->id . '" data-status="InActive" class="change-status-btn dropdown-item"><i class="bi bi-arrow-up-right-circle"></i> Change to Active</button>';
                } else {
                    $status_btn = '<button data-id="' . $row->id . '" data-status="Active" class="change-status-btn dropdown-item"><i class="bi bi-arrow-up-right-circle"></i> Change to InActive</button>';
                }

                $btn = '<div class="dropdown">
                            <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                <i class="dw dw-more"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                <button data-id="' . $row->id . '" class="edit-btn dropdown-item"><i class="dw dw-edit2"></i> Edit</button>
                                ' . $status_btn . '
                                <button data-id="' . $row->id . '" class="delete-btn dropdown-item"><i class="dw dw-delete-3"></i> Delete</button>
                            </div>
                        </div>';

                return $btn;
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }
    public function fetch_edit($id)
    {
        $tender = Tender::find($id);
        return $tender;
    }

    public function delete($id)
    {
        Tender::find($id)->delete();
        return array("status" => 1, "message" => "FAQ deleted successfully");
    }
}
