<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        return view('vendor.index');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'agency_name' => 'required',
            'contact_name' => 'required',
            'mobile' => 'required',
            'city' => 'required',
            // 'address' => 'required',
            'gst_number' => 'required',
        ]);

        if ($request->edit_id) {
            $vendor = Vendor::find($request->edit_id);
            $message = "Vendor Updated Successfully";
        } else {
            $vendor = new Vendor();
            $message = "Vendor Created Successfully";
        }

        $vendor->agency_name = $request->agency_name;
        $vendor->contact_name = $request->contact_name;
        $vendor->mobile = $request->mobile;
        $vendor->city = $request->city;
        $vendor->address = $request->address;
        $vendor->gst_number = $request->gst_number;

        $vendor->save();

        return array("status" => 1, "message" => $message);
    }

    public function fetch()
    {
        $data = Vendor::get();
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
        $vendor = Vendor::find($id);
        return $vendor;
    }

    public function delete($id)
    {
        Vendor::find($id)->delete();
        return array("status" => 1, "message" => "FAQ deleted successfully");
    }
}
