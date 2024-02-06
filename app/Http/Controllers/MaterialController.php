<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $show = $request->show;
        return view('material.index', compact('show'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'unit_type' => 'required',
        ]);

        if ($request->edit_id) {
            $material = Material::find($request->edit_id);
            $message = "Material Updated Successfully";
        } else {
            $material = new Material();
            $message = "Material Created Successfully";
        }

        $material->name = $request->name;
        $material->unit_type = $request->unit_type;

        $material->save();

        return array("status" => 1, "message" => $message);
    }

    public function fetch()
    {
        $data = Material::get();
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
        $material = Material::find($id);
        return $material;
    }

    public function delete($id)
    {
        Material::find($id)->delete();
        return array("status" => 1, "message" => "Material deleted successfully");
    }

}
