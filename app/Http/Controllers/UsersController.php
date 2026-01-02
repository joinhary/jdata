<?php

namespace App\Http\Controllers;


use Hash;
use Sentinel;
use Carbon\Carbon;
use App\Models\User;
use App\Models\RoleModel;
use Illuminate\Http\Request;
use App\Models\ChiNhanhModel;
use App\Models\RoleUsersModel;
use App\Models\ActivityLogModel;
use App\Models\PermissionsModel;
use Yajra\DataTables\DataTables;
// use DataTables;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\ManagerUserRequest;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use App\Http\Requests\ConfirmPasswordNoRequiredRequest;

class UsersController extends Controller
{
    use users;

    function random_ten()
    {
        $ho = ["Nguyễn", "Lê", "Trần", "Phạm", "Phan", "Huỳnh", "Võ", "Đặng", "Bùi", "Đỗ", "Hồ", "Ngô", "Dương", "Lý", "An", "Âu",
            "Khoa", "Khiếu", "Mai", "Lâm", "Mã", "Mộc", "Phi", "Sử", "Thịnh", "Ưng", "Vi", "Yên", "Thiêu", "Vương"];
        $lot = ["Thị", "Văn", "Thanh", "Hoàng", "Lê", "Lý", ""];
        $ten = ["Linh", "Nhân", "Khang", "Lợi", "Nhựt", "Tiền",
            "Long", "Quang", "Đức", "Vinh", "Hoàng", "Thành", "Ngân",
            "Loan", "Luân", "Trúc", "Khánh", "Lộc", "Vân", "Phương", "Thúy", "Nhẫn", "Hùng", "Hưng", "Điệp"];
        $a = rand(0, 29);
        $b = rand(0, 6);
        $c = rand(0, 24);
        return $ho[$a] . " " . $lot[$b] . " " . $ten[$c];
    }

    public function getIndex(Request $request)
    {
//        dd($this->random_ten());
//        $i = 0;
//        while ($i < 1000) {
//            Sentinel::register([
//                'email' => md5(str_random(10)) . '@gmail.com',
//                'first_name' => $this->random_ten(),
//                'password' => str_random(10),
//            ]);
//            $i++;
//        }
        $bo_dem = CEIL(count(User::all()) / 100);
        $khoi = [];
        for ($i = 1; $i <= $bo_dem; $i++) {
            $khoi[] = $i;
        }
        $roles = RoleModel::where('slug', '!=', 'khach-hang')->where('id','!=','1599')->get(['id', 'name']);
        $permission = PermissionsModel::get(['id', 'permissions']);
        $vanphong = ChiNhanhModel::select('cn_id','cn_ten')
    ->whereNull('deleted_at')
    ->get();
        // dd($vanphong->first());
        $vanphong_sl=$request->vanphong_id;
        return view('admin.quanlynguoidung.index', compact('roles', 'khoi', 'permission','vanphong','vanphong_sl'));
    }


    public function register(ManagerUserRequest $request)
    {

        try {
            // Register the user
            $this->register_api($request);
            // Redirect to the home page with success menu
            return Redirect::back()->with('success', trans('users/message.success.create'));

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

    public function create_log(Request $request)
    {
        $my_id = $request->my_id;
        $description = $request->description;
        return $this->api_create_log($my_id, $description);
    }

    public function ajax_active(Request $request)
    {
        $user = Sentinel::findById($request->id);
        $activation = Activation::exists($user);
        if ($activation) {
            Activation::complete($user, $activation->code);
            $my_id = Sentinel::getUser()->id;
            $desciption = 'Kích hoạt cho người dùng ' . $user->first_name;
            $this->api_create_log($my_id, $desciption);
            return ['status' => true, 'message' => 'Đã kích hoạt thành công'];
        } else {
            if (!Activation::completed($user)) {
                Activation::complete($user, Activation::create($user)->code);
                $my_id = Sentinel::getUser()->id;
                $desciption = 'Kích hoạt cho người dùng ' . $user->first_name;
                $this->api_create_log($my_id, $desciption);
                return ['status' => true, 'message' => 'Đã kích hoạt thành công'];
            } else {
                return ['status' => true, 'message' => 'Người dùng đã được kích hoạt không thể thực hiện thao tác này'];

            }
        }
    }

    public function ajax_block(Request $request)
    {
        $user = Sentinel::findById($request->id);
        if (Activation::completed($user)) {
            Activation::remove($user);
            $my_id = Sentinel::getUser()->id;
            $desciption = 'Khoá người dùng ' . $user->first_name;
            $this->api_create_log($my_id, $desciption);
            return ['status' => true, 'message' => 'Đã khoá người dùng thành công'];
        } else {
            return ['status' => false, 'message' => 'Người dùng đã bị khoá không thể thực hiện thao tác này'];
        }
    }


    public function info_user(Request $request)
    {
        $user = Sentinel::findById($request->id);
        $my_id = Sentinel::getUser()->id;
        $description = 'Xem thông tin ' . $user->first_name;
        $this->api_create_log($my_id, $description);
        return $user;
    }


    public function change_password(ConfirmPasswordNoRequiredRequest $request)
    {
      dd($request->all());
//        dd($request->roles_change);
        if (!empty($request->activate_changepassword)) {
            $user = Sentinel::findById($request->id_user_change);
            $my_id = Sentinel::getUser()->id;
            if (!empty($request->password_change)) {
                $user->password = Hash::make($request->password_change);
                $user->save();
                $my_id = Sentinel::getUser()->id;
                $desciption = 'Cập nhật password cho người dùng ' . $user->first_name;
                $this->api_create_log($my_id, $desciption);
                return back()->with('success', 'Cập nhật mật khẩu thành công');
            }
            if (!empty($request->roles_change)) {
                $roles = Sentinel::findRoleById($request->roles_change);
                if ($role_user = RoleUsersModel::where('user_id', $request->id_user_change)->first()) {
//                    dd($role_user);
                    $my_id = RoleUsersModel::where('user_id', $request->id_user_change)
                        ->update([
                            'role_id' => $request->roles_change,
                        ]);
//                    $roles_hientai = Sentinel::findRoleById($role_user->role_id);
//                    $roles_hientai->users()->detach($user);
//                    $roles->users()->attach($user);
//                    $my_id = Sentinel::getUser()->id;
                    $desciption = 'Cập nhật phân quyền cho người dùng ' . $user->first_name;
                    $this->api_create_log($my_id, $desciption);
                    return back()->with('success', 'Cập nhật quyền thành công');
                } else {
                    $roles->users()->attach($user);
                    $my_id = Sentinel::getUser()->id;
                    $desciption = 'Cập nhật phân quyền cho người dùng ' . $user->first_name;
                    $this->api_create_log($my_id, $desciption);
                    return back()->with('success', 'Cập nhật quyền thành công');
                }
            }
            if (count($request->roles_personal_change) > 0) {
                $array_permission = [];
                foreach ($request->roles_personal_change as $item) {
                    $array_permission[$item] = true;
                }
                $user->permissions = $array_permission;
                $user->save();
                $desciption = 'Cập nhật phân quyền cá nhân cho người dùng ' . $user->first_name;
                $this->api_create_log($my_id, $desciption);
                return back()->with('success', 'Cập nhật quyền cá nhân thành công');
            }
            return back()->with('error', 'Không có gì được thay đổi');
        } else {
            return back()->with('error', 'Vui lòng xác nhận lại thông tin');
        }
    }

    public function diary($id)
    {
        $limit = 500;
        $page  = request('page', 1);
$num   = ($page - 1) * $limit + 1;
        $activity = ActivityLogModel::where('causer_id', $id)->orderBy('created_at', 'desc')->paginate($limit);
        $user = Sentinel::findById($id);
        $my_id = Sentinel::getUser()->id;
        $desciption = 'Xem nhật ký người dùng ' . $user->first_name;
        $this->api_create_log($my_id, $desciption);
        return view('admin.quanlynguoidung.diary', compact('activity', 'user', 'num'));
    }

    public function destroy(Request $request)
    {
        $user = User::findOrFail($request->id_user)->update(['deleted_at' => Carbon::now()]);
        return \redirect()->route('admin.manager.users.index')->with('success', 'Xóa thành công');
    }
   public function data(Request $request)
{
    $limit = 500;
    $page  = max((int) $request->filter_khoi, 1);

    $query = User::query()
        ->leftJoin('role_users', 'role_users.user_id', '=', 'users.id')
        ->leftJoin('roles', 'roles.id', '=', 'role_users.role_id')
        ->join('nhanvien', 'nhanvien.nv_id', '=', 'users.id')
        ->join('chinhanh', 'chinhanh.cn_id', '=', 'nhanvien.nv_vanphong')
        ->where('users.id', '!=', 1599)
        ->where('roles.slug', '!=', 'khach-hang');

    if (!empty($request->filter_roles)) {
        $query->where('roles.id', (int) $request->filter_roles);
    }

    if (!empty($request->vanphong_id)) {
        $query->where('chinhanh.cn_id', (int) $request->vanphong_id);
    }

    if (!empty($request->searching)) {
        $search = trim($request->searching);

        $query->where(function ($q) use ($search) {
            $q->where('users.id', 'like', "%$search%")
              ->orWhere('users.email', 'like', "%$search%")
              ->orWhere('users.first_name', 'like', "%$search%")
              ->orWhere('chinhanh.cn_ten', 'like', "%$search%");
        });
    }

    $query->groupBy(
        'users.id',
        'users.first_name',
        'users.email',
        'users.created_at',
        'roles.name',
        'chinhanh.cn_ten'
    );

    $query->orderBy('users.first_name', 'asc')
          ->skip($limit * ($page - 1))
          ->take($limit);

    $users = $query->get([
        'users.id',
        'users.first_name',
        'users.email',
        'users.created_at',
        'roles.name as role_name',
        'chinhanh.cn_ten',
    ]);

    return DataTables::of($users)
        ->addIndexColumn()
        ->addColumn('status', function ($user) {
            return Activation::completed($user)
                ? 'Đã kích hoạt'
                : 'Chưa kích hoạt';
        })
        ->make(true);
}
public function show($id)
    {
      // dd($id);
        try {
            // Get the user information
            $id = (int) $id;
            $user = Sentinel::findUserById($id);
// dd($user->id);
            //get country name
            // if ($user->country) {
            //     $user->country = $this->countries[$user->country];
            // }
        } catch (UserNotFoundException $e) {
            // Prepare the error message
            $error = trans('users/message.user_not_found', compact('id'));
            // Redirect to the user management page
            return Redirect::route('admin.users.index')->with('error', $error);
        }
        // dd($id);
        // Show the page
        return view('admin.users.show', compact('user','id'));

    }
   public function passwordreset(Request $request)
{
    $data = $request->validate(
    [
        'id'       => ['required', 'integer'],
        'password' => ['required', 'min:6'],
    ],
    [
        'password.required' => 'Vui lòng nhập mật khẩu',
        'password.min'      => 'Mật khẩu phải có ít nhất 6 ký tự',
    ]
);

$user = Sentinel::findUserById((int)$data['id']);

if (!$user) {
    return response()->json([
        'status'  => 'error',
        'message' => 'Người dùng không tồn tại'
    ], 404);
}

// ✅ Sentinel update password
Sentinel::update($user, [
    'password' => $data['password'],
]);

return response()->json([
    'status'  => 'success',
    'message' => 'Đổi mật khẩu thành công'
]);
}
public function update_avt($id, Request $request)
    {
      dd($request->all());
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
}
