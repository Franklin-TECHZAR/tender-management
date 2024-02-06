<?php

namespace App\Http\Controllers;

use App\Models\Tender;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TenderController extends Controller
{
    public function index(Request $request)
    {
        $show = $request->show;
        return view('tender.index', compact('show'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'city' => 'required',
            'address' => 'required',
            'budget' => 'required',
        ]);

        if ($request->edit_id) {
            $tender = Tender::find($request->edit_id);
            $message = "Tender Updated Successfully";
        } else {
            $tender = new Tender();
            $message = "Tender Created Successfully";
            $tender->status = 1;
            $tender->job_order = 0;
        }

        $tender->name = $request->name;
        $tender->city = $request->city;
        $tender->address = $request->address;
        $tender->budget = $request->budget;
        $tender->description = $request->description;

        $tender->save();

        return array("status" => 1, "message" => $message);
    }

    public function fetch(Request $request)
    {
        if ($request->show == 'New') {
            $job_order = 0;
        } else {
            $job_order = 1;
        }
        $data = Tender::where('job_order', $job_order)->orderBy('status', "DESC")->get();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('status', function ($row) {
                if ($row->status == 1) {
                    return '<span class="badge badge-success">Active</span>';
                } else {
                    return '<span class="badge badge-danger">InActive</span>';
                }
            })
            ->addColumn('budget_text', function ($row) {
                return "<span class='pull-right'>₹" . number_format($row->budget, 2) . "</span>";
            })
            ->addColumn('action', function ($row) {

                if ($row->status == 0) {
                    $status_btn = '<button data-id="' . $row->id . '" data-status="Active" class="change-status-btn dropdown-item"><i class="bi bi-arrow-up-right-circle"></i> Change to Active</button>';
                } else {
                    $status_btn = '<button data-id="' . $row->id . '" data-status="InActive" class="change-status-btn dropdown-item"><i class="bi bi-arrow-up-right-circle"></i> Change to InActive</button>';
                }

                if ($row->job_order == 0) {
                    $job_order = '<button data-id="' . $row->id . '" data-status="Add" class="job-order-change-btn dropdown-item"><i class="bi bi-plus-circle"></i> Add to Job Order</button>';
                } else {
                    $job_order = '<button data-id="' . $row->id . '" data-status="Remove" class="job-order-change-btn dropdown-item"><i class="bi bi-x-circle"></i> Remove from Job Order</button>';
                }

                $btn = '<div class="dropdown">
                            <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                <i class="dw dw-more"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                <button data-id="' . $row->id . '" class="edit-btn dropdown-item"><i class="dw dw-edit2"></i> Edit</button>
                                ' . $job_order . $status_btn . '
                                <button data-id="' . $row->id . '" class="delete-btn dropdown-item"><i class="dw dw-delete-3"></i> Delete</button>
                            </div>
                        </div>';

                return $btn;
            })
            ->rawColumns(['action', 'status', 'budget_text'])
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

    public function chage_status(Request $request)
    {
        $id = $request->edit_id;
        $status = $request->status;
        $tender = Tender::find($id);
        if ($status == "Active") {
            $tender->status = 1;
        } else if ($status == "InActive") {
            $tender->status = 0;
        } else if ($status == "Add") {
            $tender->job_order = 1;
        } else if ($status == "Remove") {
            $tender->job_order = 0;
        }
        $tender->save();
        return array("status" => 1, "message" => "Status Updated successfully");
    }
}
