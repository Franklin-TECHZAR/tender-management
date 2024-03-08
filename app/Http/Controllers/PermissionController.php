<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class PermissionController extends Controller
{
    public function index()
    {
        return view('roles_permissions.permissions');
    }

    public function store(Request $request)
    {
        if ($request->edit_id) {
            $this->validate($request, [
                'name' => 'required|unique:permissions,name,' . $request->edit_id,
            ]);

            $permission = Permission::find($request->edit_id);
            $message = "Permission Updated Successfully";
        } else {
            $this->validate($request, [
                'name' => 'required|unique:permissions,name',
            ]);

            $permission = new Permission();
            $message = "Permission Created Successfully";
        }

        $permission->name = $request->input('name');
        $permission->save();

        return array("status" => 1, "message" => $message);
    }

    public function fetch()
    {
        $data = Permission::select('id', 'name');
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {

                $btn = '<div class="dropdown">
                            <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                <i class="dw dw-more"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                <button data-id="' . $row->id . '" class="edit-btn dropdown-item" href="#"><i class="dw dw-edit2"></i> Edit</button>
                                <button data-id="' . $row->id . '" class="delete-btn dropdown-item" href="#"><i class="dw dw-delete-3"></i> Delete</button>
                            </div>
                        </div>';

                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    public function fetch_edit($id)
    {
        $permission = Permission::select('name')->find($id);
        return $permission;
    }

    public function delete($id)
    {
        DB::table("permissions")->where('id', $id)->delete();
        return array("status" => 1, "message" => "Permission deleted successfully");
    }
}
