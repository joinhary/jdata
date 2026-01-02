<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\JoshController;
use App\Http\Controllers\users;
use App\Http\Requests\UserRequest;
use App\Mail\Register;
use App\Mail\Restore;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use File;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Redirect;
use Sentinel;
use URL;
use Validator;
use View;
use Yajra\DataTables\DataTables;


class UsersController extends JoshController
{
    use users;

    /**
     * Show a list of all the users.
     *
     * @return View
     */

    public function index()
    {
//        $user = Sentinel::findUserById(2);
//        $activation = Activation::completed($user);
//        return dd($activation);
        // Show the page

        return view('admin.users.index', compact('users'));
    }

    /*
     * Pass data through ajax call
     */
    /**
     * @return mixed
     */
    public function data(Request $request)
    {
        $lay = 500;
        if ($request->filter_roles != null) {
            $users = User::query()
            ->leftJoin('role_users', 'role_users.user_id', '=', 'users.id')
            ->leftJoin('roles', 'roles.id', '=', 'role_users.role_id')
            ->join('nhanvien', 'nhanvien.nv_id', '=', 'users.id')
            ->join('chinhanh', 'chinhanh.cn_id', '=', 'nhanvien.nv_vanphong')
            ->where('roles.id', $request->filter_roles)
            ->where('roles.slug', '!=', 'khach-hang')
            ->where('users.id', '!=', 1599) // Use integer for comparison
            ->skip($lay * ($request->filter_khoi - 1))
            ->take($lay)
            ->orderBy('users.first_name', 'asc')
            ->get([
                'users.id',
                'users.first_name',
                'users.email',
                'users.created_at',
                'roles.name as role_name',
                'cn_ten',
            ]);
        } else {
            if ($request->searching != null) {
                $users = User::leftjoin('role_users', 'user_id', '=', 'users.id')
                    ->leftjoin('roles', 'roles.id', '=', 'role_id')
                    ->where('user_id','!=','1599')
                    ->where('users.id', 'like', $request->searching)
                    ->orWhere('users.email', 'like', $request->searching . '%')
                    ->orWhere('users.first_name', 'like', '%' . $request->searching . '%')
                    ->orderBy('first_name', 'asc')
                    ->get(['users.id', 'users.first_name', 'users.email', 'users.created_at', 'roles.name']);

            } else {
                $users = User::leftjoin('role_users', 'user_id', '=', 'users.id')
                    ->leftjoin('roles', 'roles.id', '=', 'role_id')
                    ->where('user_id','!=','1599')
                    ->where('roles.slug', '!=', 'khach-hang')
                    ->skip($lay * ($request->filter_khoi - 1))
                    ->take($lay)
                    ->orderBy('first_name', 'asc')
                    ->get(['users.id', 'users.first_name', 'users.email', 'users.created_at', 'roles.name']);
            }
        }
        return DataTables::of($users)
//            ->editColumn('created_at', function (User $user) {
//                return $user->created_at->diffForHumans();
//            })
            ->addColumn('status', function ($user) {

                if ($activation = Activation::completed($user)) {
                    return 'Đã kích hoạt';
                } else
                    return 'Chưa kích hoạt';

            })
//            ->addColumn('actions', function ($user) {
//                $actions = '<a href="javascript:void(0)" onclick="ModalInfo(' . $user->id . ')"><i class="livicon" data-name="info" data-size="18" data-loop="true" data-c="#428BCA" data-hc="#428BCA" title="view user"></i></a>
//                            <a href="javascript:void(0)" onclick="ModalChangePassword(' . $user->id . ')"><i class="livicon" data-name="edit" data-size="18" data-loop="true" data-c="#5bc0de" data-hc="#5bc0de" title="update user"></i></a>
//                            <a href=' . route('admin.manager.users.diary', $user->id) . '><i class="livicon" data-name="notebook" data-size="18" data-loop="true" data-c="#428BCA" data-hc="#428BCA" title="diary user"></i></a>';
//                if (Activation::completed($user)) {
//                    $actions .= '<a href="javascript:void(0)" onclick="Block(' . $user->id . ')"><i class="livicon" data-name="ban" data-size="18" data-loop="true" data-c="#f56954" data-hc="#f56954" title="block user"></i></a>';
//                } else {
//                    $actions .= '<a href="javascript:void(0)" onclick="Active(' . $user->id . ')"><i class="livicon" data-name="check-circle-alt" data-size="18" data-loop="true" data-c="#00bc8c" data-hc="#00bc8c" title="active user"></i></a>';
//                }
//                if ((Sentinel::getUser()->id != $user->id) && ($user->id != 1)) {
//                    $actions .= '<a href=' . route('admin.users.confirm-delete', $user->id) . ' data-toggle="modal" data-target="#delete_confirm"><i class="livicon" data-name="user-remove" data-size="18" data-loop="true" data-c="#f56954" data-hc="#f56954" title="delete user"></i></a>';
//                }
//                return $actions;
//            })
//            ->rawColumns(['actions'])
            ->make(true);
    }

    /**
     * Create new user
     *
     * @return View
     */
    public function create()
    {
        // Get all the available groups
        $groups = Sentinel::getRoleRepository()->all();

        $countries = $this->countries;
        // Show the page
        return view('admin.users.create', compact('groups', 'countries'));
    }

    /**
     * User create form processing.
     *
     * @return Redirect
     */
    public function store(UserRequest $request)
    {

        //upload image
        if ($file = $request->file('pic_file')) {
            $extension = $file->extension() ?: 'png';
            $destinationPath = public_path() . '/uploads/users/';
            $safeName = str_random(10) . '.' . $extension;
            $file->move($destinationPath, $safeName);
            $request['pic'] = $safeName;
        }
        //check whether use should be activated by default or not
        $activate = $request->get('activate') ? true : false;

        try {
            // Register the user
            $user = Sentinel::register($request->except('_token', 'password_confirm', 'group', 'activate', 'pic_file'), $activate);

            //add user to 'User' group
            $role = Sentinel::findRoleById($request->get('group'));
            if ($role) {
                $role->users()->attach($user);
            }
            //check for activation and send activation mail if not activated by default
            if (!$request->get('activate')) {
                // Data to be used on the email view
                $data = [
                    'user_name' => $user->first_name . ' ' . $user->last_name,
                    'activationUrl' => URL::route('activate', [$user->id, Activation::create($user)->code])
                ];
                // Send the activation code through email
                Mail::to($user->email)
                    ->send(new Register($data));
            }
            // Activity log for New user create
            activity($user->full_name)
                ->performedOn($user)
                ->causedBy($user)
                ->log('New User Created by ' . Sentinel::getUser()->full_name);
            // Redirect to the home page with success menu
            return Redirect::route('admin.users.index')->with('success', trans('users/message.success.create'));

        } catch (LoginRequiredException $e) {
            $error = trans('admin/users/message.user_login_required');
        } catch (PasswordRequiredException $e) {
            $error = trans('admin/users/message.user_password_required');
        } catch (UserExistsException $e) {
            $error = trans('admin/users/message.user_exists');
        }

        // Redirect to the user creation page
        return Redirect::back()->withInput()->with('error', $error);
    }

    /**
     * User update.
     *
     * @param int $id
     * @return View
     */
    public function edit(User $user)
    {

        // Get this user groups
        $userRoles = $user->getRoles()->pluck('name', 'id')->all();
        // Get a list of all the available groups
        $roles = Sentinel::getRoleRepository()->all();

        $status = Activation::completed($user);

        $countries = $this->countries;

        // Show the page
        return view('admin.users.edit', compact('user', 'roles', 'userRoles', 'countries', 'status'));
    }

    /**
     * User update form processing page.
     *
     * @param User $user
     * @param UserRequest $request
     * @return Redirect
     */
    public function update(User $user, UserRequest $request)
    {


        try {
            $user->update($request->except('pic_file', 'password', 'password_confirm', 'groups', 'activate'));

            if (!empty($request->password)) {
                $user->password = Hash::make($request->password);
            }

            // is new image uploaded?
            if ($file = $request->file('pic_file')) {
                $extension = $file->extension() ?: 'png';
                $destinationPath = public_path() . '/uploads/users/';
                $safeName = str_random(10) . '.' . $extension;
                $file->move($destinationPath, $safeName);
                //delete old pic if exists
                if (File::exists($destinationPath . $user->pic)) {
                    File::delete($destinationPath . $user->pic);
                }
                //save new file path into db
                $user->pic = $safeName;
            }

            //save record
            $user->save();

            // Get the current user groups
            $userRoles = $user->roles()->pluck('id')->all();

            // Get the selected groups

            $selectedRoles = $request->get('groups');

            // Groups comparison between the groups the user currently
            // have and the groups the user wish to have.
            $rolesToAdd = array_diff($selectedRoles, $userRoles);
            $rolesToRemove = array_diff($userRoles, $selectedRoles);

            // Assign the user to groups

            foreach ($rolesToAdd as $roleId) {
                $role = Sentinel::findRoleById($roleId);
                $role->users()->attach($user);
            }

            // Remove the user from groups
            foreach ($rolesToRemove as $roleId) {
                $role = Sentinel::findRoleById($roleId);
                $role->users()->detach($user);
            }

            // Activate / De-activate user

            $status = $activation = Activation::completed($user);

            if ($request->get('activate') != $status) {
                if ($request->get('activate')) {
                    $activation = Activation::exists($user);
                    if ($activation) {
                        Activation::complete($user, $activation->code);
                    }
                } else {
                    //remove existing activation record
                    Activation::remove($user);
                    //add new record
                    Activation::create($user);
                    //send activation mail
                    $data = [
                        'user_name' => $user->first_name . ' ' . $user->last_name,
                        'activationUrl' => URL::route('activate', [$user->id, Activation::exists($user)->code])
                    ];
                    // Send the activation code through email
                    Mail::to($user->email)
                        ->send(new Restore($data));

                }
            }

            // Was the user updated?
            if ($user->save()) {
                // Prepare the success message
                $success = trans('users/message.success.update');
                //Activity log for user update
                activity($user->full_name)
                    ->performedOn($user)
                    ->causedBy($user)
                    ->log('User Updated by ' . Sentinel::getUser()->full_name);
                // Redirect to the user page
                return Redirect::route('admin.users.edit', $user)->with('success', $success);
            }

            // Prepare the error message
            $error = trans('users/message.error.update');
        } catch (UserNotFoundException $e) {
            // Prepare the error message
            $error = trans('users/message.user_not_found', compact('id'));

            // Redirect to the user management page
            return Redirect::route('admin.users.index')->with('error', $error);
        }

        // Redirect to the user page
        return Redirect::route('admin.users.edit', $user)->withInput()->with('error', $error);
    }

    /**
     * Show a list of all the deleted users.
     *
     * @return View
     */
    public function getDeletedUsers()
    {
        // Grab deleted users
        $users = User::onlyTrashed()->get();

        // Show the page
        return view('admin.deleted_users', compact('users'));
    }


    /**
     * Delete Confirm
     *
     * @param int $id
     * @return  View
     */
    public function getModalDelete($id)
    {
        $model = 'users';
        $confirm_route = $error = null;
        try {
            // Get user information
            $user = Sentinel::findById($id);

            // Check if we are not trying to delete ourselves
            if ($user->id === Sentinel::getUser()->id) {
                // Prepare the error message
                $error = trans('users/message.error.delete');

                return view('admin.layouts.modal_confirmation', compact('error', 'model', 'confirm_route'));
            }
        } catch (UserNotFoundException $e) {
            // Prepare the error message
            $error = trans('users/message.user_not_found', compact('id'));
            return view('admin.layouts.modal_confirmation', compact('error', 'model', 'confirm_route'));
        }
        $confirm_route = route('admin.users.delete', ['id' => $user->id]);
        return view('admin.layouts.modal_confirmation', compact('error', 'model', 'confirm_route'));
    }

    /**
     * Delete the given user.
     *
     * @param int $id
     * @return Redirect
     */
    public function destroy($id)
    {
        try {
            // Get user information
            $user = Sentinel::findById($id);
//            dd($user);
            // Check if we are not trying to delete ourselves
            if ($user->id === Sentinel::getUser()->id) {
                // Prepare the error message
//                $error = trans('admin/users/message.error.delete');
                $error = "Không thể thực hiện thao tác này";
                // Redirect to the user management page
                return Redirect::route('admin.users.index')->with('error', $error);
            }
            // Delete the user
            //to allow soft deleted, we are performing query on users model instead of Sentinel model
            User::destroy($id);
            Activation::where('user_id', $user->id)->delete();
            // Prepare the success message
//            $success = trans('users/message.success.delete');
            $success = "Đã xoá người dùng thành công";
            //Activity log for user delete
//            activity($user->full_name)
//                ->performedOn($user)
//                ->causedBy($user)
//                ->log('User deleted by '.Sentinel::getUser()->full_name);
            $my_id = Sentinel::getUser()->id;
            $description = 'Xoá người dùng' . $user->first_name;
            $this->api_create_log($my_id, $description);
            // Redirect to the user management page
            return Redirect::route('admin.manager.users.index')->with('success', $success);
        } catch (UserNotFoundException $e) {
            // Prepare the error message
            $error = trans('admin/users/message.user_not_found', compact('id'));

            // Redirect to the user management page
            return Redirect::route('admin.manager.users.index')->with('error', $error);
        }
    }

    /**
     * Restore a deleted user.
     *
     * @param int $id
     * @return Redirect
     */
    public function getRestore($id)
    {
        try {
            // Get user information
            $user = User::withTrashed()->find($id);
            // Restore the user
            $user->restore();
            // create activation record for user and send mail with activation link
//            $data->user_name = $user->first_name .' '. $user->last_name;
//            $data->activationUrl = URL::route('activate', [$user->id, Activation::create($user)->code]);
            // Send the activation code through email

            $activation = Activation::create($user);

            if ($activation) {
                Activation::complete($user, $activation->code);
            }

//           $data=[
//               'user_name' => $user->first_name .' '. $user->last_name,
//            'activationUrl' => URL::route('activate', [$user->id, Activation::create($user)->code])
//           ];
//            Mail::to($user->email)
//                ->send(new Restore($data));
//            // Prepare the success message
            $success = trans('users/message.success.restored');
            activity($user->full_name)
                ->performedOn($user)
                ->causedBy($user)
                ->log('User restored by ' . Sentinel::getUser()->full_name);
            // Redirect to the user management page
            return Redirect::route('admin.deleted_users')->with('success', $success);
        } catch (UserNotFoundException $e) {
            // Prepare the error message
            $error = trans('users/message.user_not_found', compact('id'));

            // Redirect to the user management page
            return Redirect::route('admin.deleted_users')->with('error', $error);
        }
    }

    /**
     * Display specified user profile.
     *
     * @param int $id
     * @return Response
     */
    public function update_avt($id, Request $request)
    {
        $save_path = 'assets/images/authors/';
        if ($request->hasFile('pic')) {
            $file = $request->file('pic');
            $pic = time() . '.' . $file->getClientOriginalExtension();
            $file->move($save_path, $pic);
            User::find($id)->update([
                'pic' => $pic
            ]);
        }
        return back()->with('success', 'Cập nhật ảnh đại diện thành công');
    }

    

  

    public function lockscreen($id)
    {

        if (Sentinel::check()) {
            $user = Sentinel::findUserById($id);
            return view('admin.lockscreen', compact('user'));
        }
        return view('admin.login');
    }

    public function postLockscreen(Request $request)
    {
        $password = Sentinel::getUser()->password;
        if (Hash::check($request->password, $password)) {
            return 'success';
        } else {
            return 'error';
        }
    }
}
