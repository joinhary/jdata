<?php

namespace App\Http\Controllers;


use App\Http\Requests\PermissionsRequest;
use App\Http\Requests\RolesRequest;
use App\Models\PermissionsModel;
use App\Models\RoleModel;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class PermissionsController extends Controller
{
    use users;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function roles_index()
    {
        $limit = 50;
        $num = num_row(request('page'), $limit);
        $role = RoleModel::orderBy('name', 'asc')->paginate($limit);
        return view('admin.roles.index', compact('num', 'role'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function roles_create()
    {
        $permission = PermissionsModel::orderBy('group', 'asc')->get();
        return view('admin.roles.create', compact('permission'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function roles_store(RolesRequest $request)
    {
        $role = Sentinel::getRoleRepository()->createModel()->create([
            'name' => $request->display_name,
            'slug' => $request->slug,
        ]);
        $array_permission = [];
        foreach ($request->permissions as $item) {
            $array_permission[$item] = true;
        }
        $role_test = Sentinel::findRoleById($role->id);
        $role_test->permissions = $array_permission;
        $role_test->save();
        $my_id = Sentinel::getUser()->id;
        $description = 'Tạo phân quyền ' . $role->name;
        $this->api_create_log($my_id, $description);
        return Redirect::route('admin.roles.index')->with('success', 'Phân quyền thành công');
    }
//
//    /**
//     * Display the specified resource.
//     *
//     * @param  int $id
//     * @return \Illuminate\Http\Response
//     */
//    public function show($id)
//    {
//        $limit = 50;
//        $num = num_row(request('page'), $limit);
//        $role = Role::findOrFail($id)->id;
//        $permission_role = PermissionRole::join('roles', 'roles.id', '=', 'permission_role.permission_id')
//            ->where('role_id', $role)->paginate($limit);
//        return view('admin.phanquyen.show', compact('permission_role', 'num'));
//    }
//
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function roles_edit($id)
    {
        $role = Sentinel::findRoleById($id);
        $permission = PermissionsModel::orderBy('group', 'asc')->get();
        return view('admin.roles.edit', compact('role', 'permission'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function roles_update(RolesRequest $request, $id)
    {
        if ($request->permissions == null) {
            return back()->with('error', 'Vui lòng chọn quyền');
        }
        $array_permission = [];
        foreach ($request->permissions as $item) {
            $array_permission[$item] = true;
        }
        $role_test = Sentinel::findRoleById($id);
        $role_test->permissions = $array_permission;
        $role_test->slug = $request->slug;
        $role_test->name = $request->display_name;
        $role_test->save();
        $my_id = Sentinel::getUser()->id;
        $description = 'Sửa phân quyền ' . $role_test->name;
        $this->api_create_log($my_id, $description);
        return Redirect::route('admin.roles.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function roles_destroy($id)
    {
        $role_name = Sentinel::findRoleById($id)->name;
        $my_id = Sentinel::getUser()->id;
        $description = 'Xoá phân quyền ' . $role_name;
        $this->api_create_log($my_id, $description);
        Sentinel::findRoleById($id)->delete();
        return Redirect::route('admin.roles.index');
    }

    public function permissions_index()
    {
        $limit = 50;
        $num = num_row(request('page'), $limit);
        $permissions = PermissionsModel::orderBy('group', 'asc')->paginate($limit);
        return view('admin.permissions.index', compact('num', 'permissions'));
    }

    public function permissions_create()
    {
        return view('admin.permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function permissions_store(PermissionsRequest $request)
    {
        $permission = PermissionsModel::create([
            'group' => $request->group,
            'permissions' => $request->group . "." . $request->permissions,
            'description' => $request->description
        ]);
        $my_id = Sentinel::getUser()->id;
        $description = 'Tạo quyền ' . $permission->permissions;
        $this->api_create_log($my_id, $description);
        return Redirect::route('admin.permissions.index')->with('success', 'Tạo quyền thành công');
    }

    public function permissions_destroy($id)
    {
        $permission_name = PermissionsModel::find($id)->permissions;
        PermissionsModel::find($id)->delete();
        $my_id = Sentinel::getUser()->id;
        $description = 'Xoá quyền ' . $permission_name;
        $this->api_create_log($my_id, $description);
        return Redirect::route('admin.permissions.index');
    }
}
