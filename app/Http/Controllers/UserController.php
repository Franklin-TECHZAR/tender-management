<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index()
    {
        $role = Role::select('id', 'name')->get();
        return view('roles_permissions.users', compact('role'));
    }

    public function store(Request $request)
    {
        if ($request->edit_id) {
            $this->validate($request, [
                'email' => 'required|unique:users,email,' . $request->edit_id,
            ]);

            $user = User::find($request->edit_id);
            $message = "User Updated Successfully";
        } else {
            $this->validate($request, [
                'email' => 'required|unique:users,email',
            ]);

            $user = new User();
            $message = "User Created Successfully";
            $user->password = Hash::make($request->input('password'));
        }

        $user->name = $request->input('name');
        $user->email = $request->input('email');

        $user->assignRole($request->input('roles'));

        $user->save();

        return array("status" => 1, "message" => $message);
    }

    public function fetch()
    {
        $data = User::select('id', 'name');
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
        $user = User::select('id', 'name', 'mobile_no', 'email', 'latitude', 'longitude', 'address')->find($id);
        $userRole = $user->roles->pluck('name')->all();
        return ['user' => $user, 'role' => $userRole];
    }

    public function delete($id)
    {
        User::where('id', $id)->delete();
        return array("status" => 1, "message" => "User deleted successfully");
    }
}
