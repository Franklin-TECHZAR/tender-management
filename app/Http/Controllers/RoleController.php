<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $permission = Permission::get();
        return view('roles_permissions.roles', compact('permission'));
    }

    public function store(Request $request)
    {
        if ($request->edit_id) {
            $this->validate($request, [
                'name' => 'required|unique:roles,name,' . $request->edit_id,
                'permission' => 'required',
            ]);

            $role = Role::find($request->edit_id);
            $message = "Role Updated Successfully";
        } else {
            $this->validate($request, [
                'name' => 'required|unique:roles,name',
                'permission' => 'required',
            ]);

            $role = new Role();
            $message = "Role Created Successfully";
        }

        $role->name = $request->input('name');
        $role->save();

        $role->syncPermissions($request->input('permission'));

        return array("status" => 1, "message" => $message);
    }

    public function fetch()
    {
        $data = Role::select('id', 'name');
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
        $role = Role::select('name')->find($id);
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id", $id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();

        return array('role' => $role, 'permissions' => $rolePermissions);
    }

    public function delete($id)
    {
        DB::table("roles")->where('id', $id)->delete();
        return array("status" => 1, "message" => "Role deleted successfully");
    }
}
