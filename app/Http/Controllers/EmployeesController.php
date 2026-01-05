<?php

namespace App\Http\Controllers;

use URL;
use App\Models\User;
use App\Models\RoleModel;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\ChiNhanhModel;
use App\Models\NhanVienModel;
use Illuminate\Http\Response;
use App\Models\RoleUsersModel;
use App\Models\UchiUsersModel;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\NhanVienRequest;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use PHPUnit\Framework\Constraint\Count;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Cartalyst\Sentinel\Laravel\Facades\Activation;

class EmployeesController extends Controller
{
    use users;
    use Nhanvien;
    use Chinhanh;
    use roles;

    public function index(Request $request)
    {
        // dd($request->all());
        $search = $request->nv_tk;
        $user_id = Sentinel::getUser()->id;
        $role = NhanVienModel::where('nv_id', '=', $user_id)->first()->nv_vanphong;
        $roles = Sentinel::check()->user_roles()->first()->slug;
        // dd($roles);
        $vanphong= ChiNhanhModel::select('cn_id','cn_ten')->whereNull('deleted_at')->get();
        $vanphong_sl=$request->vanphong_id;
        if($request->vanphong_id!=''){
if ($roles == 'admin') {
            
             $tong = NhanVienModel::join('users', 'users.id', '=', 'nv_id')
                ->join('chinhanh', 'cn_id', '=', 'nv_vanphong')
                ->join('role_users', 'user_id', '=', 'nv_id')
                ->join('roles', 'roles.id', '=', 'role_id')
                ->whereNull('users.deleted_at')
                ->where('nv_vanphong',$vanphong_sl)
                ->orderBy('nv_hoten', 'asc')->get();
            $nhanvien = $this->listNhanVien($request)->where('nv_vanphong',$vanphong_sl);
            $count = Count($nhanvien->get());
        } else {
           
           
            $tong = NhanVienModel::join('users', 'users.id', '=', 'nv_id')
                ->join('chinhanh', 'cn_id', '=', 'nv_vanphong')
                ->join('role_users', 'user_id', '=', 'nv_id')
                ->join('roles', 'roles.id', '=', 'role_id')
                ->whereNull('users.deleted_at')
                ->where('nv_vanphong',$vanphong_sl)
                ->where('role_users.role_id', '=', $role)
                ->orderBy('nv_hoten', 'asc')->get();
            $nhanvien = $this->listNhanVien($request);
            
            $count = Count($nhanvien->get());
            $nhanvien->where('nhanvien.nv_vanphong', '=', $role)->where('nv_vanphong',$vanphong_sl);
        }
        }else{
            $tong = NhanVienModel::join('users', 'users.id', '=', 'nv_id')
                ->join('chinhanh', 'cn_id', '=', 'nv_vanphong')
                ->join('role_users', 'user_id', '=', 'nv_id')
                ->join('roles', 'roles.id', '=', 'role_id')
                ->whereNull('users.deleted_at')
              
                ->where('role_users.role_id', '=', $role)
                ->orderBy('nv_hoten', 'asc')->get();
            $nhanvien = $this->listNhanVien($request);
            
            $count = Count($nhanvien->get());
            
        }
        
        
        $nhanvien = $nhanvien->paginate(20);
        return view('admin.nhanvien.index', compact('nhanvien', 'count', 'tong', 'search', 'roles','vanphong','vanphong_sl'));
    }

    public function create(Request $request)
    {
      
        $chinhanh = $this->listChinhanh($request)->pluck('cn_ten', 'cn_id');
        $chucvu = $this->listRoles()->where('id', '!=', 10)->pluck('name', 'id');
        foreach ($chucvu as $key => $value) {
            if ($key != 10 && $key != 11 && $key != 20 && $key != 22) {
                $chuc[$key] = $value;
            }
        }
        if (!Sentinel::inRole('admin')) {
            $chucvu = $chuc;
        }
        return view('admin.nhanvien.create', compact('chinhanh', 'chucvu', 'chuc'));
    }

    public function store(NhanVienRequest $request)
    {
        $validated = $this->validate_store_nhanvien($request->all());
        if ($validated->fails()) {
            return redirect()->back()->withErrors($validated->errors())->withInput();
        }
        $nv_hoten = $request->nv_hoten;
        $username = $request->email;
        $password = $request->password;
        $address = $request->address;
        $phone = $request->phone;
        $nv_tinh = $request->nv_tinh;
        $nv_quan = $request->nv_quan;
        $nv_phuong = $request->nv_phuong;
        $nv_ap = $request->nv_ap;
        $nv_vanphong = $request->nv_vanphong;
        $nv_chucvu = $request->nv_chucvu;
        $id_uchi = $request->id_uchi;
        $id_lienket = $request->id_lienket;
        try {
            $name_uchi = UchiUsersModel::find($id_uchi)->account;
        } catch (\Exception $e) {
            $name_uchi = '';
        }
        $save_path = 'assets/images/authors/';

        if ($request->hasFile('pic')) {

            $file = $request->file('pic');
            $pic = time() . '.' . $file->getClientOriginalExtension();
            $file->move($save_path, $pic);
        } else {
            $pic = null;
        }

        $activate = $request->get('activate') ? true : false;

        $user = Sentinel::register([
            'email' => $username,
            'password' => $password,
            'first_name' => $nv_hoten,
            'address' => $address,
            'phone' => $phone,
            'pic' => $pic
        ], $activate);

        $role = Sentinel::findRoleById($nv_chucvu);
        $role->users()->attach($user->id);


        // Send the activation code through email
        //        Mail::to($user->email)
        //            ->send(new Register($data));

        NhanVienModel::create([
            'nv_id' => $user->id,
            'nv_hoten' => $nv_hoten,
            'nv_tinh' => $nv_tinh,
            'nv_quan' => $nv_quan,
            'nv_phuong' => $nv_phuong,
            'nv_ap' => $nv_ap,
            'nv_vanphong' => $nv_vanphong,
            'id_uchi' => $id_uchi,
            'name_uchi' => $name_uchi,
            'id_lienket' => $id_lienket
        ]);

        /*Ghi log*/
        $user_exec = Sentinel::getUser()->id;
        $description = "Tạo nhân viên và tài khoản cho " . $user->first_name;
        $this->api_create_log($user_exec, $description);

        return Redirect::route('indexNhanVien')->with('success', 'Thêm nhân viên thành công!');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {

        $users = $this->getUserInfo($id);
        // dd($users);
        $pic = User::find($id)->pic;
        if (isset(User::find($id)->nhanvien()->get()->nv_tinh)) {
            $nhanvien = User::find($id)->nhanvien()->select('nv_hoten', 'name_uchi', 'roles.name as nv_tenchucvu', 'cn_ten', 'province.name as nv_tentinh', 'district.name as nv_tenquan', 'ward.name as nv_tenphuong', 'village.name as nv_tenap')
                ->join('province', 'provinceid', '=', 'nv_tinh')
                ->join('district', 'districtid', '=', 'nv_quan')
                ->join('ward', 'wardid', '=', 'nv_phuong')
                ->join('chinhanh', 'cn_id', '=', 'nv_vanphong')
                ->join('village', 'villageid', '=', 'nv_ap')
                ->join('role_users', 'user_id', '=', 'nv_id')
                ->join('roles', 'roles.id', '=', 'role_id')
                ->first();
            $address = 'Số ' . $users->address . ', ' . lcfirst($nhanvien->nv_tenap) . ', ' . lcfirst($nhanvien->nv_tenphuong) . ', ' . lcfirst($nhanvien->nv_tenquan) . ', ' . lcfirst($nhanvien->tentinh);
        } else {
            $nhanvien = User::find($id)->nhanvien()->select('nv_id', 'nv_hoten', 'roles.name as nv_tenchucvu', 'cn_ten')
                ->join('chinhanh', 'cn_id', '=', 'nv_vanphong')
                ->join('role_users', 'user_id', '=', 'nv_id')
                ->join('roles', 'roles.id', '=', 'role_id')
                ->first();
            $address = "";
        }

        return view('admin.nhanvien.detail', compact('nhanvien', 'users', 'address', 'pic'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
      // dd(1);
        $user = $this->getUserInfo($id);
        $pic = User::find($id)->pic;
        $nhanvien = $user->nhanvien()->select('nhanvien.*', 'role_id as nv_chucvu', 'phone', 'address','nhanvien.is_active')
            ->join('users', 'id', '=', 'nhanvien.nv_id')
            ->join('role_users', 'user_id', '=', 'nv_id')
            ->first();
        $request = new Request();
        $chinhanh = $this->listChinhanh($request)->pluck('cn_ten', 'cn_id');
        $chucvu = $this->listRoles()->where('id', '!=', 10)->pluck('name', 'id');

        foreach ($chucvu as $key => $value) {
            if ($key != 10 && $key != 11 && $key != 20 && $key != 22) {
                $chuc[$key] = $value;
            }
        }
        if (!Sentinel::inRole('admin')) {
            $chucvu = $chuc;
        }
        /* $tinhthanh = ProvinceModel::orderBy('name','asc')->pluck('name','provinceid');
         $quanhuyen = DistrictModel::where('provinceid',$nhanvien->nv_tinh)->orderBy('name','asc')->pluck('name','districtid');
         $phuongxa = WardModel::where('districtid',$nhanvien->nv_quan)->orderBy('name','asc')->pluck('name','wardid');
         $ap = VillageModel::where('wardid',$nhanvien->nv_phuong)->orderBy('name','asc')->pluck('name','villageid');*/

        try {
            $uchi_users = UchiUsersModel::select(DB::raw("CONCAT(family_name, ' ', first_name, '-', account) as acc_name"), 'id')->pluck('acc_name', 'id');
            $uchi_users->prepend('-Chọn tài khoản UCHI-', '');
        } catch (\Exception $e) {
            $uchi_users = ['' => '-Chọn tài khoản UCHI-'];
        };
        return view('admin.nhanvien.edit', compact('nhanvien', 'chinhanh', 'chucvu', 'user', 'pic'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $validated = $this->validate_update_nhanvien($request->all(), $id);
        if ($validated->fails()) {
            return redirect()->back()->withErrors($validated->errors())->withInput();
        }

        $nv_hoten = $request->nv_hoten;
        $phone = $request->phone;
        $nv_tinh = $request->nv_tinh;
        $nv_quan = $request->nv_quan;
        $nv_phuong = $request->nv_phuong;
        $nv_ap = $request->nv_ap;
        $address = $request->address;
        $nv_vanphong = $request->nv_vanphong;
        $nv_chucvu = $request->nv_chucvu;
        $id_uchi = $request->id_uchi;
        $is_active = $request->trangthai;
       $request->validate([
    'nv_hoten' => 'required',
    'nv_vanphong' => 'required',
    'nv_chucvu' => 'required',
], [
    'nv_hoten.required' => 'Vui lòng nhập tên nhân viên!',
    'nv_vanphong.required' => 'Vui lòng chọn văn phòng!',
    'nv_chucvu.required' => 'Vui lòng chọn chức vụ!',
]);

        $save_path = 'assets/images/authors/';
        if ($request->hasFile('pic')) {
            $file = $request->file('pic');
            $pic = time() . '.' . $file->getClientOriginalExtension();
            $file->move($save_path, $pic);
            User::find($id)->update([
                'pic' => $pic
            ]);
        }


        try {
            $name_uchi = UchiUsersModel::find($id_uchi)->account;
        } catch (\Exception $e) {
            $name_uchi = '';
        }

        $role_user_current = RoleUsersModel::where('user_id', $id)->first()->role_id;

        NhanVienModel::where('nv_id', $id)
            ->update([
                'nv_hoten' => $nv_hoten,
                /*'nv_tinh' => $nv_tinh,
                'nv_quan' => $nv_quan,
                'nv_phuong' => $nv_phuong,
                'nv_ap' => $nv_ap,*/
                'nv_vanphong' => $nv_vanphong,
                'is_active' => $is_active,
                /*                'id_uchi' => $id_uchi,
                                'name_uchi' => $name_uchi,*/
            ]);

        $user = Sentinel::findById($id);
        Sentinel::update($user, [
            'first_name' => $nv_hoten,
            'phone' => $phone,
            'address' => $address
        ]);

        if ($nv_chucvu != $role_user_current) {
            $role_current = Sentinel::findRoleById($role_user_current);
            $role_new = Sentinel::findRoleById($nv_chucvu);
            $role_current->users()->detach($user);
            $role_new->users()->attach($user);
        }

        /*Ghi log*/
        $user_exec = Sentinel::getUser()->id;
        $description = "Cập nhật thông tin nhân viên " . $user->first_name;
        $this->api_create_log($user_exec, $description);

        return Redirect::route('showNhanVien', ['id' => $id])->with('success', 'Cập nhật nhân viên thành công!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return mixed
     */
    public function destroy($id)
    {
        try {
            // Get user information
            $user = Sentinel::findById($id);
            // Check if we are not trying to delete ourselves
            if ($user->id === Sentinel::getUser()->id) {
                // Prepare the error message
                $error = trans('admin/users/message.error.delete');
                // Redirect to the user management page
                return Redirect::route('indexNhanVien')->with('error', $error);
            }
            // Delete the user
            //to allow soft deleted, we are performing query on users model instead of Sentinel model
            // User::destroy($id);
            $description = "Xóa đương sự và tài khoản " . $user->first_name;
            $user = User::find($id)->update(['is_active' => 0]);
            NhanVienModel::where('nv_id', $id)->update(['is_active' => 0]);
            Activation::where('user_id', $id)->update(['completed' => 0]);
            $user_exec = Sentinel::getUser()->id;
            $this->api_create_log($user_exec, $description);
            return Redirect::route('indexNhanVien')->with('success', 'Xóa nhân viên thành công!');
        } catch (UserNotFoundException $e) {
            return Redirect::route('indexNhanVien')->with('error', 'Không tìm thấy người dùng!');
        }
    }

    /**
     * Validate thông tin request cho hàm store()
     * @param array $request
     * @return mixed
     */
    public function validate_store_nhanvien(array $request)
    {
        $message = $this->validate_message();
        $validator = Validator::make($request, [
            'nv_hoten' => 'required',
            'email' => 'required|email| unique:users,email',
            'password' => 'required | min:6 | max:16',
            /*            'phone' => 'required | unique:users',*/
            /*            'address' => 'required',*/
            //'id_uchi' => 'required | unique:nhanvien'
        ], $message);
        return $validator;
    }

    /**
     * Validate thông tin request cho hàm update()
     * @param array $request
     * @param $id
     * @return mixed
     */
    public function validate_update_nhanvien(array $request, $id)
    {
        $message = $this->validate_message();
        $ignore_user = [
            'required',
            Rule::unique('users')->ignore($id)
        ];
        $ignore_nv = [
            'required',
            Rule::unique('nhanvien')->ignore($id)
        ];
        $validator = Validator::make($request, [
            'nv_hoten' => 'required',
            /*            'phone' => $ignore_user,*/
            /*            'address' => 'required',*/
            //'id_uchi' => $ignore_nv
        ], $message);
        return $validator;
    }


    /**
     * @return array
     */
    public function validate_message(): array
    {
        $message = [
            'nv_hoten.required' => 'Vui lòng nhập họ tên nhân viên!',
            /*            'phone.required' => 'Vui lòng nhập số điện thoại nhân viên!',*/
            'email.required' => 'Vui lòng nhập tên đăng nhập nhân viên!',
            'password.required' => 'Vui lòng nhập mật khẩu nhân viên!',
            'password.min' => 'Mật khẩu phải dài hơn 6 ký tự!',
            'password.max' => 'Mật khẩu phải ngắn hơn 16 ký tự!',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp!',
            /*            'address.required' => 'Vui lòng nhập địa chỉ nhân viên!',*/
            /*            'phone.unique' => 'Số điện thoại đã tồn tại!',*/
            'email.unique' => 'Email đã tồn tại!',
            'email.email' => 'Tên đăng nhập phải là dạng mail'
            //'id_uchi.unique' => 'Vui lòng chọn lại tài khoản UCHI!',
            //'id_uchi.required' => 'Vui lòng chọn tài khoản UCHI!'
        ];
        return $message;
    }
}