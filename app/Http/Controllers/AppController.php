<?php


namespace App\Http\Controllers;

use App\Models\TaiSanModel;
use App\Models\QuangCaoModel;
use App\Models\HopDongModel;
use App\Models\PhieuTaiSanGiaTriModel;
use App\Models\PhieuTaiSanModel;
use App\Models\RoleUsersModel;
use App\Models\SuuTraModel;
use App\Models\TaiSanGiaTriModel;
use App\Models\VaiTroModel;
use Carbon\Carbon;
use App\Models\KhachHangModel;
use App\Models\KieuTieuMucModel;
use App\Models\NhanVienModel;
use App\Models\ProvinceModel;
use App\Models\DistrictModel;
use App\Models\VillageModel;
use App\Models\WardModel;
use App\Models\RoleModel;
use App\Models\ChiNhanhModel;
use App\Models\User;
use App\Models\KieuModel;
use App\Models\TieuMucModel;
use App\Models\BangGiaDichVuModel;
use App\Models\TinTucThongBaoModel;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Cartalyst\Sentinel\Laravel\Facades\Reminder;
use Ixudra\Curl\Facades\Curl;
use function Couchbase\defaultDecoder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Sentinel;
use Cartalyst\Sentinel\Hashing\BcryptHasher;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use phpDocumentor\Reflection\DocBlock;
use DateTime;
use Normalizer;

class AppController extends Controller
{


    /**
     * URL Dùng chung cho app
     */
    public function get_url(Request $request)
    {
        if ($request->dir == 'tai-san') {
            $url = 'http://dotary.miennam24h.vn/images/taisan/';
        } elseif ($request->dir == 'khach-hang') {
            $url = 'http://dotary.miennam24h.vn/images/khachhang/';
        } elseif ($request->dir == 'users') {
            $url = 'http://dotary.miennam24h.vn/images/';
        } elseif ($request->dir == 'hop-dong') {
            $url = 'http://dotary.miennam24h.vn/images/hopdong/';
        } elseif ($request->dir == 'tin-tuc') {
            $url = 'http://dotary.miennam24h.vn/images/tintuc/';
        } elseif ($request->dir == 'quang-cao') {
            $url = 'http://dotary.miennam24h.vn/images/quangcao/';
        }

        return ['status' => true, 'data' => $url];
    }

    /**
     * Danh sach tai khoan nhan vien.
     *
     * @return \Illuminate\Http\Response
     */

    public function nvAccounts_list(Request $request)
    {
        $page = $request->get('page');
        $slug = $request->get('slug');
        $account = NhanVienModel::select('users.id', 'nhanvien.nv_hoten', 'users.email as nv_email', 'users.phone as nv_sdt',
            'nhanvien.nv_tinh as provinceid', 'province.name as nv_tinh', 'nhanvien.nv_quan as districtid',
            'district.name as nv_quan', 'nhanvien.nv_phuong as wardid', 'ward.name as nv_phuong', 'nhanvien.nv_ap as villageid',
            'village.name as nv_ap', 'users.address as nv_diachi', 'role_users.role_id as nv_chucvuid', 'roles.name as nv_chucvu',
            'users.pic', 'chinhanh.cn_id as nv_vanphongid', 'chinhanh.cn_ten as nv_vanphong')
            ->join('users', 'users.id', '=', 'nhanvien.nv_id')
            ->join('role_users', 'role_users.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_users.role_id')
            ->join('chinhanh', 'chinhanh.cn_id', '=', 'nhanvien.nv_vanphong')
            ->join('province', 'province.provinceid', '=', 'nhanvien.nv_tinh')
            ->join('district', 'district.districtid', '=', 'nhanvien.nv_quan')
            ->join('ward', 'ward.wardid', '=', 'nhanvien.nv_phuong')
            ->join('village', 'village.villageid', '=', 'nhanvien.nv_ap')
            ->where('roles.slug', $slug)
            ->where('users.user_state', '<>', '0')
            ->skip($page * 30)->take(30)
            ->orderBy('nv_id', 'ASC')
            ->get();
        return ['status' => true, 'data' => $account];
    }

    /**
     * Tao tai khoan nhan vien moi.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function createNv(Request $request)
    {
        $validated = $this->validate_store_nhanvien($request);
        if ($validated->fails()) {
            return ['status' => false, 'message' => $validated->errors()->all()];
        }
        $save_path = 'images';
        $ten_anh = '';
        $hoten = $request->nv_hoten;
        $password = bcrypt($request->password);
        $sdt = $request->nv_sdt;
        $email = $request->nv_email;
        $provinceid = $request->provinceid;
        $districtid = $request->districtid;
        $wardid = $request->wardid;
        $villageid = $request->villageid;
        $diachi = $request->nv_diachi;
        $vanphongid = $request->nv_vanphongid;
        $chucvuid = $request->nv_chucvuid;
        $pic = $request->file('image');
        //$trangthai = 1;
        $state = 1;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $ten_anh = time() . '.' . $file->getClientOriginalExtension();
            $file->move($save_path, $ten_anh);
        }
        $activate = $request->get('activate') ? true : false;
        $user = Sentinel::register([
            'email' => $email,
            'password' => $password,
            'first_name' => $hoten,
            'phone' => $sdt,
            'address' => $diachi,
            'user_state' => $state,
            'pic' => $ten_anh
        ], $activate);

        $role = Sentinel::findRoleById($chucvuid);
        $role->users()->attach($user->id);

        NhanVienModel::create([
            'nv_id' => $user->id,
            'nv_hoten' => $hoten,
            'nv_tinh' => $provinceid,
            'nv_quan' => $districtid,
            'nv_phuong' => $wardid,
            'nv_ap' => $villageid,
            'nv_vanphong' => $vanphongid,
        ]);
        return ['status' => true, 'message' => 'Thêm tài khoản nhân viên thành công'];
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function validate_store_nhanvien($request)
    {
        $message = $this->validate_message();
        $validator = Validator::make($request->all(), [
            'nv_hoten' => 'required',
            'password' => 'required|min:6|max:16',
            'nv_sdt' => 'required|unique:users,phone',
            'nv_email' => 'unique:users,email',
            'nv_diachi' => 'required',
            'image' => 'image'
        ], $message);
        return $validator;
    }

    /**
     * Cập nhật thông tin tài khoản nhân viên.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function updateNv(Request $request)
    {
        $validated = $this->validate_update_nhanvien($request->all());
        if ($validated->fails()) {
            return ['status' => false, 'message' => $validated->errors()->all()];
        }
        $save_path = 'images';
        $hoten = $request->nv_hoten;
        $sdt = $request->nv_sdt;
        $email = $request->nv_email;
        $provinceid = $request->provinceid;
        $districtid = $request->districtid;
        $wardid = $request->wardid;
        $villageid = $request->villageid;
        $diachi = $request->nv_diachi;
        $vanphongid = $request->nv_vanphongid;
        $chucvuid = $request->nv_chucvuid;
        $pic = $request->file('image');
        $state = 1;
        $id = $request->id;
        $ten_anh = User::find($id)->pic;
        if ($request->hasFile('image')) {
            if ($ten_anh != '') {
                $old_image_path = public_path($save_path . '/' . $ten_anh);
                unlink($old_image_path);
            }
            $file = $request->file('image');
            $ten_anh = time() . '.' . $file->getClientOriginalExtension();
            $file->move($save_path, $ten_anh);
        }
        $activate = $request->get('activate') ? true : false;

        $user = Sentinel::findById($id);
        Sentinel::update($user, [
            'email' => $email,
            'phone' => $sdt,
            'address' => $diachi,
            'first_name' => $hoten,
            'user_state' => $state,
            'pic' => $ten_anh
        ]);
        $check = Activation::exists($user);
        if ($check && !$activate) {
            Activation::remove($user);
        } elseif (!$check && $activate) {
            $activation = Activation::create($user);
        }
        RoleUsersModel::where('user_id', $id)->delete();
        $role = Sentinel::findRoleById($chucvuid);
        $role->users()->attach($id);

        NhanVienModel::find($id)
            ->update([
                'nv_hoten' => $hoten,
                'nv_tinh' => $provinceid,
                'nv_quan' => $districtid,
                'nv_phuong' => $wardid,
                'nv_ap' => $villageid,
                'nv_vanphong' => $vanphongid,
            ]);

        return ['status' => true, 'message' => 'Cập nhật tài khoản nhân viên thành công!'];
    }


    /**
     * doi mat khau tai khoan nhan vien.
     *
     * @param int $id
     * @return mixed
     */
    public function changePassword(Request $request)
    {
        $validated = $this->validate_changepassword($request->all());
        if ($validated->fails()) {
            return ['status' => false, 'message' => $validated->errors()->all()];
        }
        $password = bcrypt($request->password);

        $id = $request->id;

        User::find($id)
            ->update([
                'password' => $password,
            ]);

        return ['status' => true, 'message' => 'Cập nhật mật khẩu thành công!'];
    }


    /**
     * doi mat khau tai user đang login.
     *
     * @param int $id
     * @return mixed
     */
    public function update_password(Request $request)
    {
        $validated = $this->validate_update_password($request->all());
        if ($validated->fails()) {
            return ['status' => false, 'message' => $validated->errors()->all()];
        }

        $user = User::where('id', $request->id)->first();
        if (Hash::check($request->pw_recent, $user->password)) {
            $password = bcrypt($request->password);
            $id = $request->id;

            User::find($id)
                ->update([
                    'password' => $password,
                ]);

            return ['status' => true, 'message' => ['Cập nhật mật khẩu thành công!', 'Vui lòng đăng nhập lại']];
        } else {
            return ['status' => false, 'message' => ['Mật khẩu không đúng']];
        }

    }


    public function validate_update_nhanvien($request)
    {
        $message = $this->validate_message();
        $igrone_phone = [
            'required',
            Rule::unique('users', 'phone')->ignore($request['id'], 'id')
        ];
        $igrone_email = [
            'required',
            Rule::unique('users', 'email')->ignore($request['id'], 'id')
        ];
        $validator = Validator::make($request, [
            'nv_hoten' => 'required',
            'nv_sdt' => $igrone_phone,
            'nv_email' => $igrone_email,
            'nv_diachi' => 'required',
            'image' => 'image'
        ], $message);
        return $validator;

    }

    public function validate_changepassword($request)
    {
        $message = $this->validate_message();
        $validator = Validator::make($request, [
            'password' => 'min:6|max:16|required|same:passwordcf'
        ], $message);
        return $validator;

    }

    /*
     * Hàm validation update password cho user đang login
     */
    public function validate_update_password($request)
    {
        $message = $this->validate_message();
        $validator = Validator::make($request, [
            'pw_recent' => 'required',
            'password' => 'min:6|max:16|required|same:passwordcf'
        ], $message);
        return $validator;
    }

    public function validate_reset_pass($request, $id)
    {
        $message = $this->validate_message();
        $validator = Validator::make($request, [
            'password' => 'required|min:6|max:16|confirmed'
        ], $message);
        return $validator;
    }

    /**
     * @return array
     */
    public function validate_message(): array
    {
        $message = [
            'nv_hoten.required' => 'Vui lòng nhập họ tên nhân viên',
            'nv_sdt.required' => 'Vui lòng nhập số điện thoại nhân viên',
            'password.min' => 'Mật khẩu phải nhiều hơn 6 ký tự',
            'password.required' => 'Mật khẩu không được để trống',
            'password.max' => 'Mật khẩu phải ít hơn 16 ký tự',
            'password.same' => 'Mật khẩu xác nhận không khớp',
            'pw_recent.required' => 'Mật khẩu không đúng',
            'nv_diachi.required' => 'Vui lòng nhập địa chỉ nhân viên',
            'nv_sdt.unique' => 'Số điện thoại đã tồn tại',
            'nv_email.unique' => 'Email đã tồn tại',
            'image.image' => 'Tệp tin không phải hình ảnh'
        ];
        return $message;
    }

    /**
     * Xoa mot tài khoản nhan vien.
     *
     * @param int $id
     * @return mixed
     */
    public function nvDestroy(Request $request)
    {
        $id = $request->get('id');
        User::where('id', $id)
            ->update([
                'user_state' => 0
            ]);

        return ['status' => true, 'message' => 'Xóa tài khoản thành công!'];
    }

    /**
     * Danh sach tai khoan khách hàng.
     *
     * @return \Illuminate\Http\Response
     */
    use KhachHang;

    public function list_duong_su(Request $request)
    {
        $page = $request->get('page');
        $account = $this->list_khachhang_app($request)->skip($page * 30)->take(30)->get(['id', 'first_name', 'phone', 'address', 'pic', 'k_id']);
        return ['status' => true, 'data' => $account];

    }

    /**
     * Danh sach tai khoan khách hàng có tình trạng hôn nhân khác "kết hôn"
     *
     * @return \Illuminate\Http\Response
     */
    use KhachHang;

    public function list_ds(Request $request)
    {
        $page = $request->get('page');
        $khachhang = $this->list_khachhang_app($request)
            ->skip($page * 30)->take(30)->get(['id', 'first_name', 'phone', 'address', 'pic', 'k_id']);
        $tt_honnhan = TieuMucModel::select('tm_id')
            ->where('tm_keywords', 'tinh-trang-hon-nhan')
            ->first()->tm_id;
        $ket_hon = KieuTieuMucModel::where('ktm_keywords', 'ket-hon')->first()->ktm_id;
        $data = [];
        foreach ($khachhang as $kh) {
            $tt_hn = KhachHangModel::select('kh_giatri')->where('kh_id', $kh->kh_id)
                ->where('tm_id', $tt_honnhan)->first();
            if ($tt_hn) {
                if ($tt_hn->kh_giatri != $ket_hon) {
                    $data[] = $kh;
                }
            }
        }
        return ['status' => true, 'data' => $data];

    }


    /**
     * Ham thêm mới đương sự
     */
    public function store_kh(Request $request)
    {
        //dd($request->all());
        $validate_cmnd = $this->valid_cmnd($request);
        $validate_dynamic = $this->validate_dynamic_store($request);
        if ($validate_dynamic->fails()) {
            return ['status' => false, 'message' => $validate_dynamic->errors()->all()];
        }
        $validate_static = $this->validate_static_store($request);
        if ($validate_static->fails()) {
            return ['status' => false, 'message' => $validate_static->errors()->all()];
        }
        $data = $this->store_customer_app($request);
        return ['status' => true, 'message' => 'Thêm đương sự thành công!', 'data' => $data];
    }

    /**
     * Lấy ds tieumuc và tinhtrang_honnhan đương sự đổ form edit.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit_form_customer(Request $request)
    {
        $id = $request->id;
        $account = User::select('id', 'email as username', 'first_name as nhan', 'phone', 'address', 'pic', 'k_id')->find($id);
        $k_id = (int)$account->k_id;
        $request = new Request();
        $tm_arr = $this->get_tieumuc_kieu_app($request, $k_id);
        $tieumuc = $this->list_tieumuc_form_app($tm_arr, $k_id);
        foreach ($tieumuc as $tm) {
            $khachhang = KhachHangModel::select('khachhang.tm_id', 'tm_nhan', 'tm_loai', 'tm_keywords', 'tm_batbuoc', 'kh_giatri')
                ->join('tieumuc', 'tieumuc.tm_id', '=', 'khachhang.tm_id')
                ->where('kh_id', $id)
                ->where('khachhang.tm_id', $tm->tm_id)
                ->first();
            if ($khachhang->tm_loai == 'file') {
                $khachhang->kh_giatri = json_decode($khachhang->kh_giatri);
            }
            $tm->kh_giatri = $khachhang->kh_giatri;
        }

        $tt_honnhan = KhachHangModel::select('kieu_tieumuc.k_id')
            ->join('kieu_tieumuc', 'kieu_tieumuc.ktm_id', '=', 'khachhang.kh_giatri')
            ->join('tieumuc', 'tieumuc.tm_id', '=', 'khachhang.tm_id')
            ->where('kh_id', $id)
            ->where('tieumuc.tm_keywords', '=', 'tinh-trang-hon-nhan')
            ->first()->k_id;
        $tinhtrang_kethon = [];
        if ($tt_honnhan != 0) {
            $k_id = $tt_honnhan;
            $tm_arr_vc = $this->get_kieu_tieumuc_vochong_app($k_id);
            $tinhtrang_kethon = $this->list_tieumuc_form_app($tm_arr_vc, $k_id);
            foreach ($tinhtrang_kethon as $tm_vc) {
                $khachhang = KhachHangModel::select('khachhang.tm_id', 'tm_nhan', 'tm_loai', 'tm_keywords', 'tm_batbuoc', 'kh_giatri')
                    ->join('tieumuc', 'tieumuc.tm_id', '=', 'khachhang.tm_id')
                    ->where('kh_id', $id)
                    ->where('khachhang.tm_id', $tm_vc->tm_id)
                    ->first();
                if ($khachhang->tm_loai == 'file') {
                    $khachhang->kh_giatri = json_decode($khachhang->kh_giatri);
                }
                $tm_vc->kh_giatri = $khachhang->kh_giatri;
                if ($tm_vc->tm_keywords == 'hon-phoi') {
                    $tm_vc['data'] = User::select('first_name')->where('id', $tm_vc->kh_giatri)->first()->first_name;
                }
            }
        }
        return ['status' => true, 'data' => compact('tieumuc', 'tinhtrang_kethon')];
    }

    /*
     * Update đương sự
     *
     */
    public function update_kh(Request $request)
    {
        //dd($request->all());
        $id = (int)$request->id;
        /*Validate*/
        $validate_dynamic = $this->validate_dynamic_store($request);
        if ($validate_dynamic->fails()) {
            return ['status' => false, 'message' => $validate_dynamic->errors()->all()];
        }
        $save_path = 'images';
        $kh_path = 'images/khachhang';
        $ten_anh = 'new-user.png';

        /*Xử lý request vào bảng Users*/
        $first_name = $request->first_name;
        $username = $request->username;
        $password = $request->password;
        $contact = $request->contact;
        $k_id = $request->kieu;

        //Kiểm tra request có file hay ko và đổi tên file upload lên rồi lưu lại với tên file mới
        if ($request->hasFile('pic')) {
            $file = $request->file('pic');
            $ten_anh = time() . '.' . $file->getClientOriginalExtension();
            $file->move($save_path, $ten_anh);
        }

        //Kiểm tra checkbox "Kích hoạt" có check hay không?

        $activate = $request->activate ? true : false;
        //Cập nhật thông tin bảng users
        $user = Sentinel::findById($id);
        Sentinel::update($user, [
            'first_name' => $first_name,
            'email' => $username,
            'pic' => $ten_anh,
            'k_id' => $k_id,
            'phone' => $contact,
        ]);

        /* Xử lý các request vào bảng khách hàng*/
        $tm_diachi = TieuMucModel::select('tm_id')
            ->where('tm_keywords', 'dia-chi-lien-he')
            ->first()->tm_id;
        $tt_honnhan = TieuMucModel::select('tm_id')
            ->where('tm_keywords', 'tinh-trang-hon-nhan')
            ->first()->tm_id;
        $tm_honphoi = TieuMucModel::select('tm_id')
            ->where('tm_keywords', 'hon-phoi')
            ->first()->tm_id;
        $chua_kh_id = KieuTieuMucModel::where('ktm_keywords', 'chua-ket-hon')->first()->ktm_id;
        $lyhon_id = KieuTieuMucModel::where('ktm_keywords', 'ly-hon')->first()->ktm_id;
        $tm_kethon = explode(' ', KieuModel::where('k_keywords', 'ket-hon')->first()->k_tieumuc);
        $tm_lyhon = explode(' ', KieuModel::where('k_keywords', 'ly-hon')->first()->k_tieumuc);
        $req_honnhan = 'tm_' . $tt_honnhan;
        $req_honphoi = 'tm_' . $tm_honphoi;

        //nếu là đương sự chính
        if ($request->type_ds) {
            /*Lấy các tiểu mục có của khách hàng đã chỉ định id trong bảng khachhang*/
            $khachhang = KhachHangModel::select('tieumuc.tm_id', 'tm_loai')
                ->join('tieumuc', 'tieumuc.tm_id', '=', 'khachhang.tm_id')
                ->where('kh_id', $id)
                ->get();
            /*Lấy tm_id từ từng dòng mẫu tin khachhang đặt vào 1 mảng để kiểm tra các request có tồn tại trong mảng này hay không */
            foreach ($khachhang as $item) {
                if ($item->tm_id == $tm_honphoi) {
                    $vc_ds_hientai = KhachHangModel::select('kh_giatri')->where('kh_id', $id)
                        ->where('tm_id', $tm_honphoi)->first()->kh_giatri;
                }
                //lấy từng item ghép với 'tm_' để được name của request, sau đó kiểm tra với mảng ds_tm.
                //Nếu không nằm trong ds_tm thì tiến hành xóa mẫu tin đó
                $ds_tm_item = 'tm_' . $item->tm_id;
                if (!in_array($ds_tm_item, $request->ds_tm)) {
                    KhachHangModel::where('kh_id', $id)->where('tm_id', $item->tm_id)->delete();
                } else {
                    $kh_tm[] = $item->tm_id;
                    //Kiểm tra nếu item này có tm_loai là file thì đẩy vào mảng $tm_file
                    if ($item->tm_loai == 'file') {
                        $tm_file[] = $item->tm_id;
                    }
                }
            }

            $i = 0;
            /*Xử lý từng request để phục vụ chức năng cập nhật*/
            foreach ($request->ds_tm as $tm) {
                //Tách name của input ra khỏi 'tm_' để lấy được id tiểu mục
                $tm_id = substr($tm, 3);

                //Kiểm tra nếu tm_id nằm trong mảng kh_tm đã có của khách hàng
                //thì thực hiện lấy giá trị hiện tại dòng đó gán vào biến $giatri, sau đó xóa dòng tin đó
                //ngược lại thì thực hiện như thêm mới thông tin cho khách hàng
                if (in_array($tm_id, $kh_tm)) {
                    $giatri = KhachHangModel::select('kh_giatri')->where('kh_id', $id)->where('tm_id', $tm_id)->first()->kh_giatri;
                    KhachHangModel::where('kh_id', $id)->where('tm_id', $tm_id)->delete();
                    //thêm giá trị mới sau khi xóa giá trị cũ
                    if (!in_array($tm_id, $tm_file)) {
                        if ($request->hasFile($tm)) {
                            foreach ($request->file($tm) as $item) {
                                $img_name = time() + $i . '.' . $item->getClientOriginalExtension();
                                $temp[] = $img_name;
                                $item->move($kh_path, $img_name);
                                $i++;
                            }
                            $kh_giatri = json_encode($temp);
                            $temp = [];
                        } else {
                            $kh_giatri = $request->$tm;
                        }
                    }
                    //Kiểm tra nếu request đang xét là id tiểu mục kiểu file thì xử lý theo dạng file (nếu có file)
                    //ngược lại sẽ lưu giá trị ảnh cũ
                    if (in_array($tm_id, $tm_file)) {
                        if ($request->hasFile($tm)) {
                            foreach ($request->file($tm) as $item) {
                                $img_name = time() + $i . '.' . $item->getClientOriginalExtension();
                                $temp[] = $img_name;
                                $item->move($kh_path, $img_name);
                                $i++;
                            }
                            $kh_giatri = json_encode($temp);
                            $temp = [];
                        } else {
                            if (is_null($request->$tm)) {
                                $kh_giatri = $request->$tm;
                            } else {
                                $kh_giatri = $giatri;
                            }
                        }
                    }
                } //Xử lý thêm mới cho các tiểu mục phát sinh thêm
                else {
                    if ($request->hasFile($tm)) {
                        foreach ($request->file($tm) as $item) {
                            $img_name = time() + $i . '.' . $item->getClientOriginalExtension();
                            $temp[] = $img_name;
                            $item->move($kh_path, $img_name);
                            $i++;
                        }
                        $kh_giatri = json_encode($temp);
                        $temp = [];
                    } else {
                        //Các request có dạng giá trị thông thường (text, number, date,..)
                        $kh_giatri = $request->$tm;
                    }
                }

                KhachHangModel::create([
                    'kh_id' => $id,
                    'tm_id' => $tm_id,
                    'kh_giatri' => $kh_giatri
                ]);

                //Lấy request của tiểu mục là địa chỉ liên hệ để gán cho cột address bảng users
                if ($tm_id == $tm_diachi) {
                    $address = $kh_giatri;
                    Sentinel::update($user, [
                        'address' => $address,
                    ]);
                }
            }
            //xử lý tình trạng hôn nhân cho vợ chồng của đương sự
            //lấy tình trạng hôn nhân của đương sự 2
            $ds2_honphoi = KhachHangModel::select('kh_giatri')
                ->where('kh_id', $request->ds2_id)
                ->where('tm_id', $tt_honnhan)->first()->kh_giatri;
            //kiểm tra tt_hôn nhân xem ds2 có từng có hôn phối hay chưa
            if ($ds2_honphoi != $chua_kh_id) { //nếu đương sự 2 từng kết hôn, xóa tiểu mục hôn nhân cũ cho ds2
                $tt_honnhan_giatri = KhachHangModel::select('kh_giatri')
                    ->where('kh_id', $request->ds2_id)->where('tm_id', $tt_honnhan)->first()->kh_giatri;
                $tt_honnhan_keyword = KieuTieuMucModel::select('ktm_keywords')
                    ->where('tm_id', $tt_honnhan)
                    ->where('ktm_id', $tt_honnhan_giatri)->first()->ktm_keywords;
                //mảng ds_tm hôn nhân - dùng cho việc thêm hôn nhân cho ds2
                $tm_honphoi_arr = explode(' ', KieuModel::where('k_keywords', $tt_honnhan_keyword)->first()->k_tieumuc);
                //duyệt mảng tiểu mục hôn nhân, xóa hết các dòng giá trị của hôn nhân trước
                foreach ($tm_honphoi_arr as $tm_id) {
                    KhachHangModel::where('kh_id', $request->ds2_id)->where('tm_id', $tm_id)->delete();
                }
            }
            //thêm mới mẫu tin cho ds2 - dựa vào mảng tiểu mục hôn nhân của ds1
            $tt_honnhan_ds1 = KhachHangModel::select('kh_giatri')
                ->where('kh_id', $id)->where('tm_id', $tt_honnhan)->first()->kh_giatri;
            $ds1_honnhan_keyword = KieuTieuMucModel::select('ktm_keywords')
                ->where('tm_id', $tt_honnhan)
                ->where('ktm_id', $tt_honnhan_ds1)->first()->ktm_keywords;
            //mảng ds_tm hôn nhân - dùng cho việc thêm hôn nhân cho ds2
            $tm_honphoi_ds1_arr = explode(' ', KieuModel::where('k_keywords', $ds1_honnhan_keyword)->first()->k_tieumuc);
            $j = 0;
            foreach ($tm_honphoi_ds1_arr as $tm_id) {
                $tm = 'tm_' . $tm_id;
                if ($request->hasFile($tm)) {
                    //lấy giá trị file hình của đương sự chính lưu qua cho vợ chồng đương sự
                    $gia_tri = KhachHangModel::select('kh_giatri')->where('kh_id', $user->id)
                        ->where('tm_id', $tm_id)->first()->kh_giatri;
                    $kh_giatri = $gia_tri;
                } else {
                    //Các request có dạng giá trị thông thường (text, number, date,..)
                    $kh_giatri = $request->$tm;
                }

                if ($tm_id == $tm_honphoi) {
                    KhachHangModel::create([
                        'kh_id' => $request->ds2_id,
                        'tm_id' => $tm_id,
                        'kh_giatri' => $id
                    ]);
                } else {
                    KhachHangModel::create([
                        'kh_id' => $request->ds2_id,
                        'tm_id' => $tm_id,
                        'kh_giatri' => $kh_giatri
                    ]);
                }
            }
            //cập nhật tình trạng hôn nhân cho ds2
            KhachHangModel::where('kh_id', $request->ds2_id)
                ->where('tm_id', $tt_honnhan)
                ->update([
                    'kh_giatri' => $tt_honnhan_ds1
                ]);
        } else {
            //Nếu là đương sự vợ chồng
            //Thêm tài khoản khách hàng vào bảng users
            $user_vc = Sentinel::register([
                'email' => $username,
                'password' => $password,
                'first_name' => $first_name,
                'phone' => $contact,
                'pic' => $ten_anh,
                'k_id' => $k_id,
                'user_state' => 0
            ], $activate);
            $i = 1;
            foreach ($request->ds_tm as $tm) {
                $tm_id = substr($tm, 3);
                if ($request->hasFile($tm)) {
                    //Các request có dạng file (ảnh)
                    foreach ($request->file($tm) as $item) {
                        $img_name = time() + $i . '.' . $item->getClientOriginalExtension();
                        $temp[] = $img_name;
                        $save_path = 'images/khachhang';
                        $item->move($save_path, $img_name);
                        $i++;
                    }
                    $kh_giatri = json_encode($temp);
                    $temp = [];
                } else {
                    //Các request có dạng giá trị thông thường (text, number, date,..)
                    $kh_giatri = $request->$tm;
                }
                //thêm mới vợ chồng đương sự chính
                KhachHangModel::create([
                    'kh_id' => $user_vc->id,
                    'tm_id' => $tm_id,
                    'kh_giatri' => $kh_giatri
                ]);

                if ($tm_id == $tm_diachi) {
                    User::find($user_vc->id)
                        ->update([
                            'address' => $kh_giatri
                        ]);
                }
            }
        }

        foreach ($request->ds_tm as $tm) {
            $tm_id = substr($tm, 3);
            //Kiểm tra tiểu mục hiện tại có phải là tiểu mục kết hôn không.
            //Nếu đúng thì kiểm tra câu trả lời có k_id != 0 thì tiến hành thê lịch sử hôn nhân
            if ($request->type_ds) {
                if ($tm_id == $tt_honnhan) {
                    if ($request->$tm != $chua_kh_id) {
                        $this->lichsuhonnhan_create($request, $user->id, $request->$req_honphoi, $tt_honnhan, $request->$tm);
                    }
                }
            }
        }

        //Ghi log
        if ($request->user_id) {
            $user_exec = $request->user_id;
            $description = "Điều chỉnh thông tin khách hàng và tài khoản cho " . $user->first_name;
            $this->api_create_log($user_exec, $description);
        }
        return ['status' => true, 'message' => 'Điều chỉnh đương sự thành công!'];

    }


    /**
     * Xóa đương sự.
     *
     * @param int $id
     * @return mixed
     */
    public function destroy_duong_su(Request $request)
    {
        $id = $request->id;
        try {
            // Get user information
            $user = Sentinel::findById($id);
            // Check if we are not trying to delete ourselves
            if ($user->id === $request->user_id) {
                // Prepare the error message
                $error = trans('admin/users/message.error.delete');
                // Redirect to the user management page
                return ['status' => false, 'message' => $error];
            }
            // Delete the user
            //to allow soft deleted, we are performing query on users model instead of Sentinel model
            User::destroy($id);
            Activation::where('user_id', $user->id)->delete();
            $user_exec = $request->user_id;
            $description = "Xóa tài khoản và thông tin đương sự " . $user->first_name;
            $this->api_create_log($user_exec, $description);
            return ['status' => true, 'message' => 'Xóa đương sự thành công!'];
        } catch (UserNotFoundException $e) {
            return ['status' => false, 'message' => 'Không tìm thấy người dùng!'];
        }
    }


    /**
     * Hàm validate chứng minh nhân dân
     * @param $request
     * @return mixed
     */
    public function valid_cmnd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kh_giatri' => 'unique:khachhang'
        ], [
            'kh_giatri.unique' => 'Số giấy tờ tùy thân đã tồn tại!'
        ]);
        if ($validator->fails()) {
            return ['status' => 'error', 'message' => $validator->errors()->first()];
        }
        return ['status' => 'success', 'message' => 'Passed!'];
    }

    /**
     * Hàm validate các request động từ danh sách tiểu mục cho tạo mới khách hàng
     * @param $request
     * @return mixed
     */
    public function validate_dynamic_store(Request $request)
    {
        $id = $request->k_id;
        $tm_arr = $this->get_tieumuc_kieu_app($request, $id);
        $tm_req = TieuMucModel::select('tm_id')
            ->whereIn('tm_id', $tm_arr)
            ->where('tm_batbuoc', 1)
            ->get();
        foreach ($tm_req as $req_arr) {
            $tm_batbuoc[] = $req_arr->tm_id;
        }
        foreach ($request->ds_tm as $tm) {
            $tm_valid = substr($tm, 3);
            if (in_array($tm_valid, $tm_batbuoc)) {
                $arr_data[$tm] = $request->get($tm);
                $arr_validator[$tm] = 'required';
                $arr_messages[$tm . '.required'] = 'Vui lòng điền đầy đủ thông tin!';
            }
            if ($request->hasFile($tm)) {
                $arr_data[$tm] = $request->file($tm);
                $arr_validator[$tm . '.*'] = 'image';
                $arr_messages[$tm . '.image'] = 'Tệp tin không phải hình ảnh!';
            }
        }
        $validated = Validator::make($arr_data, $arr_validator, $arr_messages);
        return $validated;
    }

    /**
     * Hàm validate các request tĩnh trong form create khách hàng (avatar, tài khoản, nhãn)
     * @param Request $request
     * @return mixed
     */
    public function validate_static_store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'username' => 'required | unique:users,email',
            'first_name' => 'required | unique:users',
            'password' => 'min:6|max:16|required|same:passwordcf',
            'pic' => 'image'
        ], [
            'username.required' => 'Vui lòng nhập tên đăng nhập!',
            'username.unique' => 'Tên đăng nhập đã tồn tại!',
            'first_name.unique' => 'Nhãn đương sự đã tồn tại!',
            'password.min' => 'Mật khẩu phải chứa ít nhất 6 kí tự',
            'password.max' => 'Mật khẩu phải chứa nhiều nhất 16 kí tự',
            'password.required' => 'Mật khẩu không được để trống',
            'password.same' => 'Mật khẩu xác nhận không khớp',
            'pic.image' => 'Tệp tin không phải hình ảnh!',
        ]);

        return $validated;
    }

    /**
     * Lấy thông tin chi tiết một đương sự theo id
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function chi_tiet_duong_su(Request $request)
    {
        $id = $request->kh_id;
        $khachhang = KhachHangModel::select('tm_nhan', 'tm_loai', 'kh_giatri', 'tm_keywords', 'khachhang.created_at')
            ->where('kh_id', $id)
            ->join('tieumuc', 'tieumuc.tm_id', '=', 'khachhang.tm_id')
            ->orderBy('khachhang.created_at', 'asc')
            ->get();
        foreach ($khachhang as $kh) {
            if ($kh->tm_keywords == 'hon-phoi') {
                $vc_ds = User::where('id', $kh->kh_giatri)->first()->first_name;
                //xử lý tên đương sư - loại bỏ số giấy tờ khỏi tên
                $arr = explode(" ", $vc_ds);
                array_pop($arr);
                $vc_ds = join(" ", $arr);
                $kh->kh_giatri = $vc_ds;
            } elseif ($kh->tm_loai == 'select') {
                $kh->kh_giatri = KieuTieuMucModel::where('ktm_id', $kh->kh_giatri)->first()->ktm_traloi;
            } elseif ($kh->tm_loai == 'file') {
                $kh->kh_giatri = json_decode($kh->kh_giatri);
            }
        }
        return ['status' => true, 'data' => $khachhang];
    }


    /**
     * Tìm kiếm nhân viên
     */
    public function search_nhanvien(Request $request)
    {
        $page = $request->get('page');
        $keyword = $request->get('keyword');
        $slug = $request->get('slug');
        $account = NhanVienModel::select('users.id', 'nhanvien.nv_hoten', 'users.email as nv_email', 'users.phone as nv_sdt',
            'nhanvien.nv_tinh as provinceid', 'province.name as nv_tinh', 'nhanvien.nv_quan as districtid',
            'district.name as nv_quan', 'nhanvien.nv_phuong as wardid', 'ward.name as nv_phuong', 'nhanvien.nv_ap as villageid',
            'village.name as nv_ap', 'users.address as nv_diachi', 'nhanvien.nv_chucvu as nv_chucvuid', 'roles.name as nv_chucvu',
            'users.pic', 'chinhanh.cn_id as nv_vanphongid', 'chinhanh.cn_ten as nv_vanphong')
            ->join('users', 'users.id', '=', 'nhanvien.nv_id')
            ->join('roles', 'roles.id', '=', 'nhanvien.nv_chucvu')
            ->join('chinhanh', 'chinhanh.cn_id', '=', 'nhanvien.nv_vanphong')
            ->join('province', 'province.provinceid', '=', 'nhanvien.nv_tinh')
            ->join('district', 'district.districtid', '=', 'nhanvien.nv_quan')
            ->join('ward', 'ward.wardid', '=', 'nhanvien.nv_phuong')
            ->join('village', 'village.villageid', '=', 'nhanvien.nv_ap')
            ->where('roles.slug', $slug)
            ->where('users.user_state', '<>', '0')
            ->where('first_name', 'LIKE', '%' . $keyword . '%')
            ->skip($page * 20)->take(20)
            ->orderBy('nv_id', 'ASC')
            ->get();
        return ['status' => true, 'data' => $account];
    }

    /**
     * Tìm kiếm khách hàng
     */
    public function search_khachHang(Request $request)
    {
        $page = $request->get('page');
        $keyword = $request->get('keyword');
        $kh = $this->list_khachhang_app($request)
            ->where('first_name', 'LIKE', '%' . $keyword . '%')
            ->skip($page * 20)->take(20)
            ->get(['id', 'first_name', 'phone', 'address', 'pic', 'user_state']);
        return ['status' => true, 'data' => $kh];
    }

    /**
     * Tìm kiếm khách hàng có tình trạng hôn nhân khác 'kết hôn'
     */
    public function search_ds(Request $request)
    {
        $page = $request->get('page');
        $keyword = $request->get('keyword');
        $khachhang = $this->list_khachhang_app($request)
            ->where('first_name', 'LIKE', '%' . $keyword . '%')
            ->skip($page * 20)->take(20)
            ->get(['id', 'first_name', 'phone', 'address', 'pic', 'user_state']);
        $tt_honnhan = TieuMucModel::select('tm_id')
            ->where('tm_keywords', 'tinh-trang-hon-nhan')
            ->first()->tm_id;
        $ket_hon = KieuTieuMucModel::where('ktm_keywords', 'ket-hon')->first()->ktm_id;
        $data = [];
        foreach ($khachhang as $kh) {
            $tt_hn = KhachHangModel::select('kh_giatri')->where('kh_id', $kh->kh_id)
                ->where('tm_id', $tt_honnhan)->first();
            if ($tt_hn) {
                if ($tt_hn->kh_giatri != $ket_hon) {
                    $data[] = $kh;
                }
            }
        }
        return ['status' => true, 'data' => $data];
    }

    /**
     * Xoa mot tài khoản khách hàng.
     *
     * @param int $id
     * @return mixed
     */
    public function khDestroy(Request $request)
    {
        $id = $request->get('id');
        User::where('id', $id)
            ->update([
                'user_state' => 0
            ]);
        return ['status' => true, 'message' => 'Xóa tài khoản thành công!'];
    }

    /**
     * Danh sách kiểu cơ sở của đương sự
     */
    public function kieu_cs_duongsu_list(Request $request)
    {
        $id_kieu = KieuModel::select('k_id')->where('k_keywords', '=', 'duong-su')->first()->k_id;
        $kieu = KieuModel::select('k_nhan as text', 'k_parent', 'k_id')
            ->where('k_trangthai', 1)
            ->get();
        if ($request->k_method == 'normal') {
            return ['status' => true, 'data' => $kieu];
        } else {
            $data = $this->ordered_menu($kieu, $id_kieu);
            return ['status' => true, 'data' => $data];
        }
    }

    /**
     * Danh sách kiểu cơ sở của tài sản
     */
    public function kieu_cs_taisan_list(Request $request)
    {
        $id_kieu = KieuModel::select('k_id')->where('k_keywords', 'tai-san')->first()->k_id;
        $kieu = KieuModel::select('k_nhan as text', 'k_parent', 'k_id')
            ->where('k_trangthai', 1)
            ->get();
        if ($request->k_method == 'normal') {
            return ['status' => true, 'data' => $kieu];
        } else {
            $data = $this->ordered_menu($kieu, $id_kieu);
            return ['status' => true, 'data' => $data];
        }
    }

    /*
     * Hàm kiểm tra kiểu cơ sở tài sản
     */
    public function check_kieu_cs_taisan(Request $request)
    {
        $kieu = KieuModel::select('k_nhan as text', 'k_parent', 'k_id')
            ->where('k_trangthai', 1)
            ->where('k_parent', $request->kieu)
            ->get();
        return ['status' => true, 'data' => $kieu];
    }

    /**
     * Hàm chỉ trả về 1 menu đã được phân nút
     * @param $kieu
     * @param $k_id
     * @return array
     */
    public function ordered_menu($kieu, $k_id)
    {
        $temp_array = [];
        foreach ($kieu as $k) {
            if ($k['k_parent'] == $k_id) {
                $nodes = $this->ordered_menu($kieu, $k['k_id']);
                if ($nodes) {
                    $k['nodes'] = $nodes;
                }
                $k['href'] = $k['k_id'];
                $k['state'] = ['expanded' => false];
                $k['selectable'] = true;
                $temp_array[] = $k;
            }
        }
        return $temp_array;
    }

    /**
     * Lấy danh sách kiểu tiểu mục dùng chung cho đương sự + tài sản
     */
    use KhachHang;

    public function kieu_list(Request $request)
    {
        $tm_arr = $this->get_tieumuc_kieu_app($request, '');
        $data = $this->list_tieumuc_form_app($tm_arr, $request->kieu);
        return ['status' => true, 'data' => $data];
    }

    /**
     * Lấy danh sách kiểu tiểu mục vo/chong duong su
     */
    public function kieutm_vo_chong_list(Request $request)
    {
        $k_id = KieuTieuMucModel::find($request->kieu)->k_id;
        $tm_arr = $this->get_kieu_tieumuc_vochong_app($k_id);
        $data = $this->list_tieumuc_form_app($tm_arr, $k_id);
        return ['status' => true, 'data' => $data];
    }


    /**
     * lay danh sach tinh thanh
     */
    public function provinces_list(Request $request)
    {
        $province = ProvinceModel::select('provinceid', 'name')
            ->orderBy('name', 'ASC')
            ->get();
        return ['status' => true, 'data' => $province];
    }

    /**
     * lay danh sach quan huyen
     */
    public function districts_list(Request $request)
    {
        $id = $request->get('provinceid');
        $district = DistrictModel::select('districtid', 'name')
            ->where('provinceid', '=', $id)
            ->orderBy('name', 'ASC')
            ->get();
        return ['status' => true, 'data' => $district];
    }

    /**
     * lay danh sach phuong xa
     */
    public function wards_list(Request $request)
    {
        $id = $request->get('districtid');
        $ward = WardModel::select('wardid', 'name')
            ->where('districtid', '=', $id)
            ->orderBy('name', 'ASC')
            ->get();
        return ['status' => true, 'data' => $ward];
    }

    /**
     * lay danh sach khu vực - ấp
     */
    public function villages_list(Request $request)
    {
        $id = $request->get('wardid');
        $village = VillageModel::select('villageid', 'name')
            ->where('wardid', '=', $id)
            //->orderBy('name', 'ASC')
            ->get();
        return ['status' => true, 'data' => $village];
    }

    /**
     * lay danh sach role nhân viên
     */
    public function roles_list(Request $request)
    {
        $role = RoleModel::all()
            ->where('slug', '<>', 'khach-hang')->values();
        //dd($role);
        return ['status' => true, 'data' => $role];
    }

    /**
     * Lay danh sach chi nhanh.
     *
     * @return \Illuminate\Http\Response
     */
    use Chinhanh;

    public function dsChinhanh(Request $request)
    {
        $chinhanh = $this->listChinhanh($request)->get();
        return ['status' => true, 'data' => $chinhanh];
    }

    /**
     * Bang gia dịch vụ
     */
    public function bangGiaDichVu()
    {
        $data = BangGiaDichVuModel::all()->values();
        foreach ($data as $item) {
            $item->ngayapdung = Carbon::parse($item->ngayapdung)->format('d/m/Y');
        }
        return ['status' => true, 'data' => $data];
    }

    public function time_elapsed_string($datetime, $full = false)
    {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'năm',
            'm' => 'tháng',
            'w' => 'tuần',
            'd' => 'ngày',
            'h' => 'giờ',
            'i' => 'phút',
            's' => 'giây',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? '' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' trước' : 'vừa xong';
    }

    /**
     * Tin tức, thông báo
     */
    public function tin_tuc_thong_bao(Request $request)
    {
        $page = $request->page;

        $tintuc = TinTucThongBaoModel::select('id', 'title', 'image', 'category', 'created_at as ngay')
            ->skip(10 * $page)->take(10)->orderBy('created_at', 'desc')->get();

        foreach ($tintuc as $news) {
            $news->ngay = $this->time_elapsed_string($news->ngay);
        }
        return ['status' => true, 'data' => $tintuc];

    }

    /**
     * Tin tức thông báo - get post detail by id
     */
    public function get_news_detail(Request $request)
    {
        $id = $request->id;
        $data = TinTucThongBaoModel::find($id)->content;
        return ['status' => true, 'data' => $data];
    }

    // TÀI SẢN
    //Danh sách tài sản
    public function danhsach_taisan(Request $request)
    {
        $page = $request->page;
        $taisan = TaiSanModel::select('ts_id', 'ts_nhan', 'ts_kieu', 'kieu.k_nhan')
            ->join('kieu', 'ts_kieu', '=', 'kieu.k_id')
            ->where('ts_trangthai', '=', '1')
            ->skip($page * 30)->take(30)->get();
        return ['status' => true, 'data' => $taisan];
    }

    // Lấy thông tin chi tiết tài sản
    public function chitiet_taisan(Request $request)
    {
        $ts_id = $request->ts_id;
        //lấy ts_kieu phục vụ cho việc lấy kiểu tiểu mục
        $taisan = TaiSanModel::select('ts_id', 'ts_nhan', 'ts_trangthai', 'ts_kieu')
            ->where('ts_trangthai', '=', 1)
            ->where('ts_id', '=', $ts_id)
            ->first();
        //Lấy kiểu tiểu mục dựa vào ts_kieu
        $k_tieumuc = KieuModel::select('taisan.ts_id', 'kieu.k_id', 'kieu.k_nhan', 'kieu.k_tieumuc')
            ->where('kieu.k_id', '=', $taisan->ts_kieu)
            ->join('taisan', 'taisan.ts_kieu', '=', 'kieu.k_id')
            ->first();
        //Tách lấy kiểu tiểu mục và đẩy vào mảng $tm_arr
        $tm_arr = explode(' ', $k_tieumuc->k_tieumuc);
        //Lấy thông tin tài sản dựa vào từng kiểu tiểu mục trong mảng $tm_arr
        $tieumuc = TieuMucModel::select('tieumuc.tm_id', 'tm_nhan', 'tm_loai', 'tm_keywords', 'tm_batbuoc')
            ->leftjoin('tieumuc_sapxep', 'tieumuc_sapxep.tm_id', '=', 'tieumuc.tm_id')
            ->whereIn('tieumuc.tm_id', $tm_arr)
            ->where('k_id', $k_tieumuc->k_id)
            ->orderBy('tm_sort', 'asc')->get();
        //add ts_giatri vào từng tiểu mục
        foreach ($tieumuc as $tm) {
            $taisan_giatri = TaiSanGiaTriModel::select('ts_giatri')
                ->where('taisan_giatri.tm_id', '=', $tm->tm_id)
                ->where('taisan_giatri.ts_id', '=', $ts_id)
                ->first()->ts_giatri;
            if ($tm->tm_loai == 'file') {
                $tm->ts_giatri = json_decode($taisan_giatri);
            } else {
                $tm->ts_giatri = $taisan_giatri;
            }
            //Lấy thêm data cho các tiểu mục loại select
            if ($tm->tm_loai == 'select') {
                $tm['data'] = KieuTieuMucModel::select('ktm_id as id', 'ktm_traloi as name', 'ktm_keywords')->where('tm_id', $tm->tm_id)->get();
            }
        }
        return ['status' => true, 'data' => $tieumuc];
    }

    //Tìm kiếm tài sản theo nhãn
    public function search_taisan(Request $request)
    {
        $page = $request->page;
        $keyword = $request->keyword;
        $taisan = TaiSanModel::select('ts_id', 'ts_nhan', 'ts_kieu', 'kieu.k_nhan')
            ->join('kieu', 'ts_kieu', '=', 'kieu.k_id')
            ->where('ts_trangthai', '=', '1')
            ->where('ts_nhan', 'LIKE', '%' . $keyword . '%')
            ->skip($page * 30)->take(30)->get();
        return ['status' => true, 'data' => $taisan];
    }

    /**
     * Ham thêm mới tài sản
     */
    public function store_taisan(Request $request)
    {
        $validate_dynamic = $this->validate_dynamic_store($request);
        if ($validate_dynamic->fails()) {
            return ['status' => false, 'message' => $validate_dynamic->errors()->all()];
        }
        $save_path = 'images/taisan';
        $ten_anh = '';
        if ($request->user_role != 'khach-hang') {
            /*Xử lý request vào bảng taisan*/
            $ts_nhan = $request->ts_nhan;
            $ts_kieu = $request->kieu;

            $ts = TaiSanModel::create([
                'ts_nhan' => $ts_nhan,
                'ts_trangthai' => 1,
                'ts_kieu' => $ts_kieu
            ]);

            /* Xử lý các request vào bảng taisan_giatri*/
            $i = 0;
            foreach ($request->ds_tm as $tm) {
                //Các request có dạng file (ảnh)
                if ($request->hasFile($tm)) {
                    foreach ($request->file($tm) as $item) {
                        $img_name = time() + $i . '.' . $item->getClientOriginalExtension();
                        $temp[] = $img_name;
                        $item->move($save_path, $img_name);
                        $i++;
                    }
                    $ts_giatri = json_encode($temp);
                    $temp = [];
                } else {
                    //Các request có dạng giá trị thông thường (text, number, date,..)
                    $ts_giatri = $request->$tm;
                }
                $tm_id = substr($tm, 3);
                TaiSanGiaTriModel::create([
                    'ts_id' => $ts->ts_id,
                    'tm_id' => $tm_id,
                    'ts_giatri' => $ts_giatri,

                ]);
                $i++;
            }
            $user_exec = $request->user_id;
            $description = "Tạo tài sản mới " . $ts_nhan;
            $this->api_create_log($user_exec, $description);
            return ['status' => true, 'message' => 'Thêm tài sản thành công!'];
        } else {
            /*Xử lý request vào bảng phieu_taisan*/
            $pts_nhan = $request->pts_nhan;
            $pts_kieu = $request->kieu;
            $kh_id = $request->user_id;

            $pts = PhieuTaiSanModel::create([
                'pts_nhan' => $pts_nhan,
                'pts_trangthai' => 1,
                'pts_kieu' => $pts_kieu,
                'kh_id' => $kh_id
            ]);
            $i = 0;
            /* Xử lý các request vào bảng phieutaisan_giatri*/
            foreach ($request->ds_tm as $tm) {
                //Các request có dạng file (ảnh)
                if ($request->hasFile($tm)) {
                    foreach ($request->file($tm) as $item) {
                        $img_name = time() + $i . '.' . $item->getClientOriginalExtension();
                        $temp[] = $img_name;
                        $item->move($save_path, $img_name);
                        $i++;
                    }
                    $pts_giatri = json_encode($temp);
                    $temp = [];
                } else {
                    //Các request có dạng giá trị thông thường (text, number, date,..)
                    $pts_giatri = $request->$tm;
                }
                $tm_id = substr($tm, 3);
                PhieuTaiSanGiaTriModel::create([
                    'pts_id' => $pts->pts_id,
                    'tm_id' => $tm_id,
                    'pts_giatri' => $pts_giatri,

                ]);
            }
            return ['status' => true, 'message' => 'Thêm phiếu tài sản thành công!'];
        }
    }

    /**
     * Edit tài sản
     */
    public function edit_taisan(Request $request)
    {
        $ts_id = (int)$request->ts_id;
        $ts_kieu = TaiSanModel::select('ts_id', 'ts_kieu')
            ->where('ts_id', '=', $ts_id)
            ->first()->ts_kieu;
        $request->kieu = $ts_kieu;
        $request->k_id = $ts_kieu;
        /*Validate*/
        $validate_dynamic = $this->validate_dynamic_store($request);
        if ($validate_dynamic->fails()) {
            return ['status' => false, 'message' => $validate_dynamic->errors()->all()];
        }

        $save_path = 'images/taisan';
        $ts_nhan = $request->ts_nhan;
        /* Xử lý cập nhật thông tin tài sản - tiểu mục động */

        /*Lấy các tiểu mục có của khách hàng đã chỉ định id trong bảng khachhang*/
        $tm_taisan = TaiSanGiaTriModel::select('tieumuc.tm_id', 'tieumuc.tm_loai')
            ->join('tieumuc', 'tieumuc.tm_id', '=', 'taisan_giatri.tm_id')
            ->where('ts_id', $ts_id)
            ->get();

        /*Lấy tm_id từ từng dòng mẫu tin khachhang đặt vào 1 mảng để kiểm tra các request có tồn tại trong mảng này hay không */
        foreach ($tm_taisan as $item) {
            $ts_tm[] = $item->tm_id;
            //Kiểm tra nếu item này có tm_loai là file thì đẩy vào mảng $tm_file
            if ($item->tm_loai == 'file') {
                $tm_file[] = $item->tm_id;
            }

            //lấy từng item ghép với 'tm_' để được name của request, sau đó kiểm tra với mảng ds_tm.
            //Nếu không nằm trong ds_tm thì tiến hành xóa mẫu tin đó
            $ds_tm_item = 'tm_' . $item->tm_id;
            if (!in_array($ds_tm_item, $request->ds_tm)) {
                TaiSanGiaTriModel::where('ts_id', $ts_id)->where('tm_id', $item->tm_id)->delete();
            }
        }

        $i = 0;
        /*Xử lý từng request để phục vụ chức năng cập nhật*/
        foreach ($request->ds_tm as $tm) {
            //Tách name của input ra khỏi 'tm-' để lấy được id tiểu mục
            $tm_id = substr($tm, 3);

            //Kiểm tra nếu tm_id nằm trong mảng tm_id đã có của tài sản
            //thì thực hiện lấy giá trị hiện tại dòng đó gán vào biến $giatri, sau đó xóa dòng tin đó
            //ngược lại thì thực hiện như thêm mới thông tin cho tài sản
            if (in_array($tm_id, $ts_tm)) {
                $giatri = TaiSanGiaTriModel::select('ts_giatri')->where('ts_id', $ts_id)->where('tm_id', $tm_id)->first()->ts_giatri;
                TaiSanGiaTriModel::where('ts_id', $ts_id)->where('tm_id', $tm_id)->delete();
                if (!in_array($tm_id, $tm_file)) {
                    if ($request->hasFile($tm)) {
                        foreach ($request->file($tm) as $item) {
                            $img_name = time() + $i . '.' . $item->getClientOriginalExtension();
                            $temp[] = $img_name;
                            $item->move($save_path, $img_name);
                            $i++;
                        }
                        $ts_giatri = json_encode($temp);
                        $temp = [];
                    } else {
                        $ts_giatri = $request->$tm;
                    }
                }
                //Kiểm tra nếu request đang xét là id tiểu mục kiểu file thì xử lý theo dạng file (nếu có file)
                //ngược lại sẽ lưu giá trị ảnh cũ
                elseif (in_array($tm_id, $tm_file)) {
                    if ($request->hasFile($tm)) {
                        foreach ($request->file($tm) as $item) {
                            $img_name = time() + $i . '.' . $item->getClientOriginalExtension();
                            $temp[] = $img_name;
                            $item->move($save_path, $img_name);
                            $i++;
                        }
                        $ts_giatri = json_encode($temp);
                        $temp = [];
                    } else {
                        if (is_null($request->$tm)) {
                            $ts_giatri = $request->$tm;
                        } else {
                            $ts_giatri = $giatri;
                        }
                    }
                }
            } //xử lý theo kiểu thêm mới
            else {
                if ($request->hasFile($tm)) {
                    foreach ($request->file($tm) as $item) {
                        $img_name = time() + $i . '.' . $item->getClientOriginalExtension();
                        $temp[] = $img_name;
                        $item->move($save_path, $img_name);
                        $i++;
                    }
                    $ts_giatri = json_encode($temp);
                    $temp = [];
                } else {
                    //Các request có dạng giá trị thông thường (text, number, date,..)
                    $ts_giatri = $request->$tm;
                }
            }

            TaiSanGiaTriModel::create([
                'ts_id' => $ts_id,
                'tm_id' => $tm_id,
                'ts_giatri' => $ts_giatri
            ]);

        }

        TaiSanModel::find($ts_id)
            ->update([
                'ts_nhan' => $ts_nhan
            ]);


        $user_exec = $request->user_id;
        $description = "Điều chỉnh tài sản " . $ts_nhan;
        $this->api_create_log($user_exec, $description);

        return ['status' => true, 'message' => 'Cập nhật thông tin tài sản thành công!'];
    }

    // xóa tài sản
    public function destroy_taisan(Request $request)
    {
        TaiSanModel::where('ts_id', $request->ts_id)->update([
            'ts_trangthai' => 0
        ]);
        return ['status' => true, 'message' => 'Xóa tài sản thành công'];
    }

    /*
     * Danh sách phiếu tài sản
     */
    public function danhsach_phieutaisan(Request $request)
    {
        $user_id = $request->user_id;
        $phieutaisan = PhieuTaiSanModel::select('pts_id', 'pts_nhan', 'pts_kieu', 'phieu_taisan.created_at', 'kieu.k_nhan')
            ->join('kieu', 'pts_kieu', '=', 'kieu.k_id')
            ->where('pts_trangthai', '1')
            ->where('kh_id', $user_id)->get();
        foreach ($phieutaisan as $pts) {
            $created_at = $pts->created_at->addDays(5);
            $ngayhen = Carbon::parse($created_at)->format('d/m/Y');
            $pts->ngayhen = $ngayhen;
        }
        return ['status' => true, 'data' => $phieutaisan];
    }

    /*
     * Tìm kiếm phiếu tài sản
     */
    public function search_phieutaisan(Request $request)
    {
        $user_id = $request->user_id;
        $keyword = $request->keyword;
        $phieutaisan = PhieuTaiSanModel::select('pts_id', 'pts_nhan', 'pts_kieu', 'phieu_taisan.created_at', 'kieu.k_nhan')
            ->join('kieu', 'pts_kieu', '=', 'kieu.k_id')
            ->where('pts_trangthai', '1')
            ->where('pts_nhan', 'LIKE', '%' . $keyword . '%')
            ->where('kh_id', $user_id)->get();
        foreach ($phieutaisan as $pts) {
            $created_at = $pts->created_at->addDays(5);
            $ngayhen = Carbon::parse($created_at)->format('d/m/Y');
            $pts->ngayhen = $ngayhen;
        }
        return ['status' => true, 'data' => $phieutaisan];
    }

    /*
     * Lấy thông tin chi tiết phiếu tài sản
     */
    public function chitiet_phieutaisan(Request $request)
    {
        $pts_id = $request->pts_id;
        //lấy pts_kieu phục vụ cho việc lấy kiểu tiểu mục
        $phieutaisan = PhieuTaiSanModel::select('pts_id', 'pts_nhan', 'pts_trangthai', 'pts_kieu')
            ->where('pts_trangthai', '=', 1)
            ->where('pts_id', '=', $pts_id)
            ->first();
        //Lấy kiểu tiểu mục dựa vào pts_kieu
        $k_tieumuc = KieuModel::select('phieu_taisan.pts_id', 'kieu.k_id', 'kieu.k_nhan', 'kieu.k_tieumuc')
            ->where('kieu.k_id', '=', $phieutaisan->pts_kieu)
            ->join('phieu_taisan', 'phieu_taisan.pts_kieu', '=', 'kieu.k_id')
            ->first();
        //Tách lấy kiểu tiểu mục và đẩy vào mảng $tm_arr
        $tm_arr = explode(' ', $k_tieumuc->k_tieumuc);
        //Lấy thông tin tài sản dựa vào từng kiểu tiểu mục trong mảng $tm_arr
        $tieumuc = TieuMucModel::select('tieumuc.tm_id', 'tm_nhan', 'tm_loai', 'tm_keywords', 'tm_batbuoc')
            ->leftjoin('tieumuc_sapxep', 'tieumuc_sapxep.tm_id', '=', 'tieumuc.tm_id')
            ->whereIn('tieumuc.tm_id', $tm_arr)
            ->where('k_id', $k_tieumuc->k_id)
            ->orderBy('tm_sort', 'asc')->get();
        //add ts_giatri vào từng tiểu mục
        foreach ($tieumuc as $tm) {
            $phieutaisan_giatri = PhieuTaiSanGiaTriModel::select('pts_giatri')
                ->where('phieutaisan_giatri.tm_id', '=', $tm->tm_id)
                ->where('phieutaisan_giatri.pts_id', '=', $pts_id)
                ->first()->pts_giatri;
            if ($tm->tm_loai == 'file') {
                $tm->pts_giatri = json_decode($phieutaisan_giatri);
            } else {
                $tm->pts_giatri = $phieutaisan_giatri;
            }
            //Lấy thêm data cho các tiểu mục loại select
            if ($tm->tm_loai == 'select') {
                $tm['data'] = KieuTieuMucModel::select('ktm_id as id', 'ktm_traloi as name', 'ktm_keywords')->where('tm_id', $tm->tm_id)->get();
            }
        }
        return ['status' => true, 'data' => $tieumuc];
    }

    /**
     * Edit phiếu tài sản
     */
    public function edit_phieutaisan(Request $request)
    {
        //dd($request->all());
        $pts_id = (int)$request->pts_id;
        $pts_kieu = PhieuTaiSanModel::select('pts_id', 'pts_kieu')
            ->where('pts_id', '=', $pts_id)
            ->first()->pts_kieu;
        $request->kieu = $pts_kieu;
        $request->k_id = $pts_kieu;
        /*Validate*/
        $validate_dynamic = $this->validate_dynamic_store($request);
        if ($validate_dynamic->fails()) {
            return ['status' => false, 'message' => $validate_dynamic->errors()->all()];
        }

        $save_path = 'images/taisan';
        $pts_nhan = $request->pts_nhan;
        /* Xử lý cập nhật thông tin phiếu tài sản - tiểu mục động */

        /*Lấy các tiểu mục có của khách hàng đã chỉ định id trong bảng khachhang*/
        $tm_phieutaisan = PhieuTaiSanGiaTriModel::select('tieumuc.tm_id', 'tieumuc.tm_loai')
            ->join('tieumuc', 'tieumuc.tm_id', '=', 'phieutaisan_giatri.tm_id')
            ->where('pts_id', $pts_id)
            ->get();

        /*Lấy tm_id từ từng dòng mẫu tin khachhang đặt vào 1 mảng để kiểm tra các request có tồn tại trong mảng này hay không */
        foreach ($tm_phieutaisan as $item) {
            $pts_tm[] = $item->tm_id;
            //Kiểm tra nếu item này có tm_loai là file thì đẩy vào mảng $tm_file
            if ($item->tm_loai == 'file') {
                $tm_file[] = $item->tm_id;
            }

            //lấy từng item ghép với 'tm_' để được name của request, sau đó kiểm tra với mảng ds_tm.
            //Nếu không nằm trong ds_tm thì tiến hành xóa mẫu tin đó
            $ds_tm_item = 'tm_' . $item->tm_id;
            if (!in_array($ds_tm_item, $request->ds_tm)) {
                PhieuTaiSanGiaTriModel::where('pts_id', $pts_id)->where('tm_id', $item->tm_id)->delete();
            }
        }

        $i = 0;
        /*Xử lý từng request để phục vụ chức năng cập nhật*/
        foreach ($request->ds_tm as $tm) {
            //Tách name của input ra khỏi 'tm-' để lấy được id tiểu mục
            $tm_id = substr($tm, 3);

            //Kiểm tra nếu tm_id nằm trong mảng tm_id đã có của tài sản
            //thì thực hiện lấy giá trị hiện tại dòng đó gán vào biến $giatri, sau đó xóa dòng tin đó
            //ngược lại thì thực hiện như thêm mới thông tin cho tài sản
            if (in_array($tm_id, $pts_tm)) {
                $giatri = PhieuTaiSanGiaTriModel::select('pts_giatri')->where('pts_id', $pts_id)->where('tm_id', $tm_id)->first()->pts_giatri;
                PhieuTaiSanGiaTriModel::where('pts_id', $pts_id)->where('tm_id', $tm_id)->delete();
                if (!in_array($tm_id, $tm_file)) {
                    if ($request->hasFile($tm)) {
                        foreach ($request->file($tm) as $item) {
                            $img_name = time() + $i . '.' . $item->getClientOriginalExtension();
                            $temp[] = $img_name;
                            $item->move($save_path, $img_name);
                            $i++;
                        }
                        $pts_giatri = json_encode($temp);
                        $temp = [];
                    } else {
                        $pts_giatri = $request->$tm;
                    }
                }
                //Kiểm tra nếu request đang xét là id tiểu mục kiểu file thì xử lý theo dạng file (nếu có file)
                //ngược lại sẽ lưu giá trị ảnh cũ
                elseif (in_array($tm_id, $tm_file)) {
                    if ($request->hasFile($tm)) {
                        //$pts_giatri = json_encode($this->addImage($request, $path = 'images/taisan', $tm));
                        foreach ($request->file($tm) as $item) {
                            $img_name = time() + $i . '.' . $item->getClientOriginalExtension();
                            $temp[] = $img_name;
                            $item->move($save_path, $img_name);
                            $i++;
                        }
                        $pts_giatri = json_encode($temp);
                        $temp = [];
                    } else {
                        if (is_null($request->$tm)) {
                            $pts_giatri = $request->$tm;
                        } else {
                            $pts_giatri = $giatri;
                        }
                    }
                }
            } //xử lý theo kiểu thêm mới
            else {
                if ($request->hasFile($tm)) {
                    //$pts_giatri = json_encode($this->addImage($request, $path = 'images/taisan', $tm));
                    foreach ($request->file($tm) as $item) {
                        $img_name = time() + $i . '.' . $item->getClientOriginalExtension();
                        $temp[] = $img_name;
                        $item->move($save_path, $img_name);
                        $i++;
                    }
                    $pts_giatri = json_encode($temp);
                    $temp = [];
                } else {
                    //Các request có dạng giá trị thông thường (text, number, date,..)
                    $pts_giatri = $request->$tm;
                }
            }

            PhieuTaiSanGiaTriModel::create([
                'pts_id' => $pts_id,
                'tm_id' => $tm_id,
                'pts_giatri' => $pts_giatri
            ]);

        }

        PhieuTaiSanModel::find($pts_id)
            ->update([
                'pts_nhan' => $pts_nhan
            ]);

        return ['status' => true, 'message' => 'Cập nhật phiếu tài sản thành công!'];
    }

    // xóa phiếu tài sản -- khách hàng tự xóa
    public function destroy_phieutaisan(Request $request)
    {
        PhieuTaiSanModel::where('pts_id', $request->pts_id)->delete();
        $tieumuc = PhieuTaiSanGiaTriModel::select('pts_id', 'tm_id')
            ->where('pts_id', $request->pts_id)->get();
        foreach ($tieumuc as $tm) {
            PhieuTaiSanGiaTriModel::where('pts_id', $request->pts_id)
                ->where('tm_id', $tm->tm_id)->delete();
        }
        return ['status' => true, 'message' => 'Xóa phiếu tài sản thành công'];
    }

    // xóa phiếu tài sản -- auto xóa sau khi quá ngày hẹn
    public function auto_destroy_phieutaisan()
    {
        $phieutaisan = PhieuTaiSanModel::select('pts_id', 'created_at')->get();
        foreach ($phieutaisan as $pts) {
            //lấy giá trị ngày hẹn
            $created_at = $pts->created_at->addDays(5);
            $ngayhen = Carbon::parse($created_at)->format('d/m/Y');

            //Lấy ngày hiện tại
            $currentDay = Carbon::parse(Carbon::today())->format('d/m/Y');

            //So sánh ngày hẹn và ngày hiện tại nếu bằng nhau thì xóa phiếu tài sản
            if ($ngayhen == $currentDay) {
                PhieuTaiSanModel::where('pts_id', $pts->pts_id)->delete();
                $tieumuc = PhieuTaiSanGiaTriModel::select('pts_id', 'tm_id')
                    ->where('pts_id', $pts->pts_id)->get();
                foreach ($tieumuc as $tm) {
                    PhieuTaiSanGiaTriModel::where('pts_id', $pts->pts_id)
                        ->where('tm_id', $tm->tm_id)->delete();
                }
            }

        }
        return ['status' => true, 'message' => 'Xóa phiếu tài sản quá hẹn thành công'];
    }

    /*
     * Hàm login
     */
    public function login(Request $request)
    {
        $user = User::where('email', $request->username)->first();
        if ($user) {
            $check_activate = Activation::where('user_id', $user->id)->first();
            if ($check_activate) {
                if (Hash::check($request->password, $user->password)) {
                    $user_role = User::select('roles.name', 'roles.slug')
                        ->leftjoin('role_users', 'role_users.user_id', '=', 'users.id')
                        ->leftjoin('roles', 'roles.id', '=', 'role_users.role_id')
                        ->where('users.id', $user->id)
                        ->first();
                    $user->user_role = $user_role->name;
                    $user->role_slug = $user_role->slug;
                    return ['status' => true, 'data' => $user];
                } else {
                    return ['status' => false, 'message' => ['Tên đăng nhập hoặc mật khẩu không đúng']];
                }
            } else {
                return ['status' => false, 'message' => ['Tên đăng nhập không tồn tại']];
            }
        } else {
            return ['status' => false, 'message' => ['Tên đăng nhập không tồn tại']];
        }
    }

    /*
     * Gởi mã xác nhận change password
     */
    public function send_email(Request $request)
    {
        $email = $request->email;
        $check_email = User::where('email', $email)->first();
        if (!$check_email) {
            $k_tieumuc = TieuMucModel::where('tm_keywords', 'email-khach-hang')
                ->first()->tm_id;
            $user = KhachHangModel::where('tm_id', $k_tieumuc)
                ->where('kh_giatri', $email)->first();
            if (!$user) {
                return ['status' => false, 'message' => ['Email không khớp với bất kỳ tài khoản nào']];
            } else {
                //tạo dãy số random làm mã xác nhận
                $code = str_random(6);
                //xử lý quên pass cho khách hàng
                $user = Sentinel::findById($user->kh_id);
                if (Reminder::exists($user)) {
                    Reminder::exists($user)->delete();
                }
                Reminder::create($user);
                Reminder::where('user_id', $user->id)
                    ->update([
                        'code' => bcrypt($code),
                    ]);
                $data = [
                    'email' => $email,
                    'body' => 'Xin chào, \n Chúng tôi đã nhận được yêu cầu đổi mật khẩu của bạn. \n Mã xác nhận của bạn là: ' . $code,
                ];
                Mail::send([], $data, function ($message) use ($data) {
                    $message->from('vandiepbui96@gmail.com', 'Phòng công chứng');
                    $message->to($data['email'])->setBody($data['body']);
                    $message->subject('Mã xác nhận quên mật khẩu đăng nhập');
                });
                return ['status' => true, 'message' => ['Mã xác nhận đã được gởi đến email của bạn'], 'data' => $user];
            }
        } else {
            //tạo dãy số random làm mã xác nhận
            $code = str_random(6);
            $user = Sentinel::findById($check_email->id);
            if (Reminder::exists($user)) {
                Reminder::exists($user)->delete();
            }
            Reminder::create($user);
            Reminder::where('user_id', $user->id)
                ->update([
                    'code' => bcrypt($code),
                ]);
            //xử lý quên pass cho nhân viên
            $data = [
                'email' => $email,
                'body' => 'Xin chào, \n Chúng tôi đã nhận được yêu cầu đổi mật khẩu của bạn. \n Mã xác nhận của bạn là: ' . $code,
            ];
            Mail::send([], $data, function ($message) use ($data) {
                $message->from('vandiepbui96@gmail.com', 'Phòng công chứng');
                $message->to($data['email'])->setBody($data['body']);
                $message->subject('Mã xác nhận quên mật khẩu đăng nhập');
            });
            return ['status' => true, 'message' => ['Mã xác nhận đã được gởi đến email của bạn'], 'data' => $user];
        }
    }

    /*
     * Hàm forget password
     */
    public function forget_password(Request $request)
    {
        $code = $request->code;
        $password = bcrypt($request->password);
        $id = $request->id;
        $validated = $this->validate_changepassword($request->all());
        if ($validated->fails()) {
            return ['status' => false, 'message' => $validated->errors()->all()];
        }
        $user = Sentinel::findById($id);
        $ma = Reminder::exists($user)->code;
        if (Hash::check($code, $ma)) {
            User::find($id)
                ->update([
                    'password' => $password,
                ]);
            return ['status' => true, 'message' => ['Cập nhật mật khẩu thành công!']];
        } else {
            return ['status' => false, 'message' => ['Mã xác nhận không đúng']];
        }


    }

    /*
     * Hàm lấy danh sách hợp đồng sưu tra - tab giao dich
     */
    public function danh_sach_suu_tra_giao_dich(Request $request)
    {
        $page = $request->page;
        $suutra = SuuTraModel::select('st_id', 'ten_hd', 'ngay_nhap', 'so_hd')
            ->skip(30 * $page)->take(30)->get();
        return ['status' => true, 'data' => $suutra];
    }

    /*
     * Hàm lấy danh sách hợp đồng sưu tra - tab bi chan
     */
    public function danh_sach_suu_tra_ngan_chan(Request $request)
    {
        $page = $request->page;
        $suutra = SuuTraModel::select('st_id', 'ten_hd', 'ngay_nhap', 'so_hd')
            ->where('ngan_chan', '!=', 0)
            ->skip(30 * $page)->take(30)->get();
        return ['status' => true, 'data' => $suutra];
    }

    /**
     * Tìm kiếm hợp đồng sưu tra - tab giao dich
     */
    public function search_giao_dich(Request $request)
    {
        $keyword = $request->get('keyword');
        $suutra = SuuTraModel::select('st_id', 'ten_hd', 'ngay_nhap', 'so_hd')
            ->where('texte', 'LIKE', '%' . $keyword . '%')
            ->get();
        return ['status' => true, 'data' => $suutra];
    }

    /**
     * Tìm kiếm hợp đồng sưu tra - tab ngan chan
     */
    public function search_ngan_chan(Request $request)
    {
        $keyword = $request->get('keyword');
        $suutra = SuuTraModel::select('st_id', 'ten_hd', 'ngay_nhap', 'so_hd')
            ->where('texte', 'LIKE', '%' . $keyword . '%')
            ->where('ngan_chan', '!=', 0)
            ->get();
        return ['status' => true, 'data' => $suutra];
    }

    /*
     * Hàm lấy chi tiet hợp đồng sưu tra
     */
    public function chi_tiet_hop_dong(Request $request)
    {
        $st_id = $request->st_id;
        $detail = SuuTraModel::select('st_id', 'ten_hd', 'ngay_nhap', 'so_hd', 'nam_hieu_luc', 'ngay_hieu_luc', 'texte', 'loai', 'duong_su', 'tai_san', 'ngan_chan', 'ngay_cc', 'nhanvien.nv_hoten as ccv', 'chinhanh.cn_ten as vp')
            ->join('nhanvien', 'ccv', '=', 'nhanvien.nv_id')
            ->join('chinhanh', 'vp', '=', 'chinhanh.cn_id')
            ->where('st_id', $st_id)->first();
        return ['status' => true, 'data' => $detail];
    }

    /*
     * Hàm lấy danh sách hợp đồng (hồ sơ)
     */
    public function hopdong_list(Request $request)
    {
        $page = $request->page;
        $list = HopDongModel::select('hopdong.id', 'kieuhopdongs.kieu_hd', 'ngayky', 'so_cc')
            ->join('kieuhopdongs', 'hopdong.kieu_hd', '=', 'kieuhopdongs.id')
            ->skip($page * 30)->take(30)->get();
        foreach ($list as $hopdong) {
            if ($hopdong) {
                $ngay_ky = Carbon::parse($hopdong->ngayky)->format('d/m/Y');
                $hopdong->ngayky = $ngay_ky;
            }
        }
        return ['status' => true, 'data' => $list];
    }

    /*
     * Tìm kiếm hồ sơ theo số công chứng
     */
    public function search_hopdong(Request $request)
    {
        $keyword = $request->keyword;
        $hopdong = HopDongModel::select('hopdong.id', 'kieuhopdongs.kieu_hd', 'ngayky', 'so_cc')
            ->join('kieuhopdongs', 'hopdong.kieu_hd', '=', 'kieuhopdongs.id')
            ->where('so_cc', $keyword)->first();

        if ($hopdong) {
            $ngay_ky = Carbon::parse($hopdong->ngayky)->format('d/m/Y');
            $hopdong->ngayky = $ngay_ky;
        }

        return ['status' => true, 'data' => $hopdong];
    }

    /*
     * Chi tiết hợp đồng theo id
     */
    public function chitiet_hopdong(Request $request)
    {
        $id = $request->id;

        $hopdong = HopDongModel::join('kieuhopdongs', 'hopdong.kieu_hd', '=', 'kieuhopdongs.id')
            ->join('vanban', 'hopdong.id_vb', '=', 'vanban.vb_id')
            ->join('users as u1', 'hopdong.ccv_id', '=', 'u1.id')
            ->join('users as u2', 'hopdong.nvnv_id', '=', 'u2.id')
            ->join('users as u3', 'hopdong.thuky_id', '=', 'u3.id')
            ->where('hopdong.id', $id)
            ->select('hopdong.id', 'nhan', 'kieuhopdongs.kieu_hd', 'noidung', 'rut_trich', 'ma', 'vanban.vb_nhan', 'list_ds', 'duongsu_vaitro', 'list_ts', 'u1.first_name as ccv', 'u2.first_name as nvnv', 'u3.first_name as thu_ky', 'ngayky', 'so_cc', 'anh_bosung as images_server')
            ->first();
        if (!$hopdong) {
            return ['status' => false, 'message' => ['Hợp đồng không tồn tại']];
        }
        //xử lý danh sách đương sự thành kiểu mảng
        $list_ds = json_decode($hopdong->list_ds);
        $images = json_decode($hopdong->images_server);
        foreach ($list_ds as $ds_id) {
            $ds = User::where('id', $ds_id)->first()->first_name;
            $arr = explode(" ", $ds);
            array_pop($arr);
            $ds = join(" ", $arr);
            $duongsu[] = $ds;
        }
        $hopdong->list_ds = $duongsu;
        $hopdong->images_server = $images;

        //xử lý duongsu_vaitro thành kiểu mảng
        $ds_vaitro = json_decode($hopdong->duongsu_vaitro);
        foreach ($ds_vaitro as $index => $value) {
            $vaitro_ds = VaiTroModel::where('vt_id', $index)->first()->vt_nhan;
            $name_ds = User::where('id', $value)->first()->first_name;
            $arr = explode(" ", $name_ds);
            array_pop($arr);
            $name_ds = join(" ", $arr);
            $vaitro = (object)null;
            $vaitro->vt_name = $vaitro_ds;
            $vaitro->ds_name = $name_ds;
            $vt_arr[] = $vaitro;
        }
        $hopdong->duongsu_vaitro = $vt_arr;

        return ['status' => true, 'data' => $hopdong];
    }

    /*
    * Chi tiết hợp đồng theo id ds đang login và id hợp đồng
    */
    public function chitiet_hd(Request $request)
    {
        $id = $request->hd_id;
        $kh_id = $request->kh_id;

        $hopdong = HopDongModel::join('kieuhopdongs', 'hopdong.kieu_hd', '=', 'kieuhopdongs.id')
            ->join('vanban', 'hopdong.id_vb', '=', 'vanban.vb_id')
            ->join('users as u1', 'hopdong.ccv_id', '=', 'u1.id')
            ->join('users as u2', 'hopdong.nvnv_id', '=', 'u2.id')
            ->join('users as u3', 'hopdong.thuky_id', '=', 'u3.id')
            ->where('hopdong.id', $id)
            ->where('hopdong.list_ds', 'LIKE', '%' . $kh_id . '%')
            ->select('hopdong.id', 'nhan', 'kieuhopdongs.kieu_hd', 'noidung', 'rut_trich', 'ma', 'vanban.vb_nhan', 'list_ds', 'duongsu_vaitro', 'list_ts', 'u1.first_name as ccv', 'u2.first_name as nvnv', 'u3.first_name as thu_ky', 'ngayky', 'so_cc', 'anh_bosung as images_server')
            ->first();
        if (!$hopdong) {
            return ['status' => false, 'message' => ['Hợp đồng không tồn tại']];
        }
        //xử lý danh sách đương sự thành kiểu mảng
        $list_ds = json_decode($hopdong->list_ds);
        $images = json_decode($hopdong->images_server);
        foreach ($list_ds as $ds_id) {
            $ds = User::where('id', $ds_id)->first()->first_name;
            $arr = explode(" ", $ds);
            array_pop($arr);
            $ds = join(" ", $arr);
            $duongsu[] = $ds;
        }
        $hopdong->list_ds = $duongsu;
        $hopdong->images_server = $images;

        //xử lý duongsu_vaitro thành kiểu mảng
        $ds_vaitro = json_decode($hopdong->duongsu_vaitro);
        foreach ($ds_vaitro as $index => $value) {
            $vaitro_ds = VaiTroModel::where('vt_id', $index)->first()->vt_nhan;
            $name_ds = User::where('id', $value)->first()->first_name;
            $arr = explode(" ", $name_ds);
            array_pop($arr);
            $name_ds = join(" ", $arr);
            $vaitro = (object)null;
            $vaitro->vt_name = $vaitro_ds;
            $vaitro->ds_name = $name_ds;
            $vt_arr[] = $vaitro;
        }
        $hopdong->duongsu_vaitro = $vt_arr;

        return ['status' => true, 'data' => $hopdong];
    }


    /*
     * DS Hồ sơ của tôi
     */
    public function my_files_list(Request $request)
    {
        $page = $request->page;
        $ds_id = $request->ds_id;
        $list = HopDongModel::select('hopdong.id', 'kieuhopdongs.kieu_hd', 'ngayky', 'so_cc')
            ->join('kieuhopdongs', 'hopdong.kieu_hd', '=', 'kieuhopdongs.id')
            ->where('hopdong.list_ds', 'LIKE', '%' . $ds_id . '%')
            ->skip($page * 20)->take(20)->get();
        foreach ($list as $hopdong) {
            if ($hopdong) {
                $ngay_ky = Carbon::parse($hopdong->ngayky)->format('d/m/Y');
                $hopdong->ngayky = $ngay_ky;
            }
        }
        return ['status' => true, 'data' => $list];

    }

    /*
     * Tìm kiếm hồ sơ của tôi
     */

    public function search_my_files(Request $request)
    {
        $keyword = $request->keyword;
        $ds_id = $request->ds_id;
        $hopdong = HopDongModel::select('hopdong.id', 'kieuhopdongs.kieu_hd', 'ngayky', 'so_cc')
            ->join('kieuhopdongs', 'hopdong.kieu_hd', '=', 'kieuhopdongs.id')
            ->where('so_cc', $keyword)
            ->where('hopdong.list_ds', 'LIKE', '%' . $ds_id . '%')
            ->first();

        if ($hopdong) {
            $ngay_ky = Carbon::parse($hopdong->ngayky)->format('d/m/Y');
            $hopdong->ngayky = $ngay_ky;
        }

        return ['status' => true, 'data' => $hopdong];
    }

    /*
     * Upload hình ảnh bổ sung cho hợp đồng
     */
    public function upload_images_hs(Request $request)
    {
        $images = 'anh_bosung';
        $id = $request->hd_id;
        $i = 0;
        $save_path = 'images/hopdong';
        if ($request->hasFile($images)) {
            foreach ($request->file($images) as $img) {
                $img_name = time() + $i . '.' . $img->getClientOriginalExtension();
                $arr_img[] = $img_name;
                $img->move($save_path, $img_name);
                $i++;
            }
            $anh_hd = json_encode($arr_img);
        }
        HopDongModel::where('id', $id)
            ->update([
                'anh_bosung' => $anh_hd
            ]);
        return ['status' => true, 'data' => $arr_img, 'message' => 'Cập nhật hình ảnh hợp đồng thành công!'];
    }

    /*
     * Danh sách logo quảng cáo
     */
    public function get_logo()
    {
        $list = QuangCaoModel::select('img')->get();
        return ['status' => true, 'data' => $list];
    }

    public function searchSuuTra(Request $request)
    {
        //        $index=SuuTraModel::reindex();
        $index = SuuTraModel::select('texte', 'duong_su')->first();

////dd(SuuTraModel::first()->basicInfo());
//dd($index);
        $str_json = json_encode([]);
        $getcoban = $request->get('coban');
        $getNangCao = $request->get('nangcao');
        $count = 0;
        $array = [
            'suutranb.ccv',
            'suutranb.vp',
            'suutranb.texte',
            'suutranb.ngay_nhap',
            'suutranb.ngay_cc',
            'suutranb.so_hd',
            'suutranb.ten_hd',
            'suutranb.st_id',
            'suutranb.duong_su',
            'suutranb.picture',
            'users.first_name',
            'chinhanh.cn_ten',
            'suutranb.ngan_chan',
            'suutranb.chu_y',
            'suutranb.status',
            'suutranb.ccv_master',
            'suutranb.vp_master',
            'suutranb.cancel_status',
            'suutranb.cancel_description',
            'suutranb.ma_phan_biet',
            'suutranb.ma_dong_bo',
            'suutranb.uchi_id_ngan_chan',
            'suutranb.updated_at',
            'suutranb.created_at',
            'suutranb.note'

        ];
        $status = false;
        $priority = $request->priority;
        $data = SuuTraModel::query()->leftjoin('users', 'users.id', '=', 'suutranb.ccv')
            ->leftjoin('chinhanh', 'cn_id', '=', 'suutranb.vp')
            ->select($array)->where('complete', '=', 2);
//dd($data);
        //        $listData = $data->get()->chunk(1000);
//        foreach ($listData as $item) {
//            foreach ($item as $i) {
//                $hosts = [env('ELASTIC_HOST')];
//
//                $client = ClientBuilder::create()
//                    ->setHosts($hosts)
//                    ->build();
//                $params = [
//                    'index' => 'ngram_three',
//                    'type' => '_doc',
//                    'id' => $i->st_id,
//                    'body' => $i->toArray()
//                ];
//                $response = $client->index($params);
//                SuuTraModel::where('st_id', '=', $i->st_id)->update([
//                    'complete' => 2
//                ]);
//            }
//
//        }


        if ($getcoban && $getNangCao) {
            $keyNC = '"' . $getNangCao . '"';
            $keyCB = '"' . $getcoban . '"';
            if (!$status) {
                //
                $data = $data->FullTextSearch('suutranb.duong_su', $getcoban)
                    ->FullTextSearch('suutranb.texte', $getNangCao)->orderByDesc("suutranb.st_id")->limit(20)->get();
            } else {
                if ($priority) {
                    $data = SuuTraModel::searchByQuery(
                        [
                            'bool' => [
                                'must' => [
                                    0 => [
                                        'match' => [
                                            'duong_su' => [
                                                'query' => $getcoban,
                                                'analyzer' => 'my_analyzer',
                                            ],
                                        ],
                                    ],
                                    1 => [
                                        'match' => [
                                            'texte' => [
                                                'query' => $getNangCao,
                                                'analyzer' => 'my_analyzer',
                                            ],
                                        ],
                                    ],

                                ],
                                'should' => [
                                    0 => [
                                        'match' => [
                                            'ngan_chan' => [
                                                'query' => '1',
                                                'boost' => 10
                                            ],

                                        ],
                                    ],
                                ]

                            ]
                        ], '', '', '20', ''

                    )->paginate();

                } else {
                    $data = SuuTraModel::searchByQuery(
                        [
                            'bool' => [
                                'must' => [
                                    0 => [
                                        'match' => [
                                            'duong_su' => [
                                                'query' => $getcoban,
                                                'analyzer' => 'my_analyzer',
                                            ],
                                        ],
                                    ],
                                    1 => [
                                        'match' => [
                                            'texte' => [
                                                'query' => $getNangCao,
                                                'analyzer' => 'my_analyzer',
                                            ],
                                        ],
                                    ],

                                ],
//                                'should'=>[
//                                    0 => [
//                                        'match' => [
//                                            'ngan_chan' => '1',
//                                        ],
//                                    ],
//                                ]

                            ]
                        ], '', '', '20', ''

                    )->paginate();

                }

            }


            $count = $data->count();
        } else {
            if ($getNangCao) {

                $key = '"' . $getNangCao . '"';
                if (true) {
                    if (is_integer(strpos($getNangCao, 'd'))) {
                        $getNangCao2 = str_replace("d", "đ", $getNangCao);
//                        $data = SuuTraModel::searchByQuery(['match' => [
//                            'texte' => $getNangCao],'analyzer'=>'my_analyzer'],'','','20')->paginate();
////                    dd($data);
                        $data = $data->FullTextSearch('suutranb.texte', $getNangCao)
                            ->OrFullTextSearch('suutranb.texte', $getNangCao2)->orderByDesc("suutranb.st_id")->limit(20)->get();
                    } else {
                        $data = $data->FullTextSearch('suutranb.texte', $getNangCao)->orderByDesc("suutranb.st_id")->limit(20)->get();
//                        $data = SuuTraModel::searchByQuery(['match' => ['texte' => ['query'=>$getNangCao,'analyzer'=>'my_analyzer']]])->paginate();
                    }
                } else {
                    if ($priority) {
                        $data = SuuTraModel::searchByQuery(
                            [
                                'bool' => [
                                    'should' => [
                                        0 => [
                                            'match' => [
                                                'texte' => [
                                                    'query' => $getNangCao,
                                                    'analyzer' => 'my_analyzer',
                                                ],
                                            ],
                                        ],
                                        1 => [
                                            'match' => [
                                                'ngan_chan' => [
                                                    'query' => '1',
                                                    'boost' => 10
                                                ],

                                            ],
                                        ],
                                    ],
                                ],
                            ])->paginate();

                    } else {
                        $data = SuuTraModel::searchByQuery(
                            [
                                'bool' => [
                                    'should' => [
                                        0 => [
                                            'match' => [
                                                'texte' => [
                                                    'query' => $getNangCao,
                                                    'analyzer' => 'my_analyzer',
                                                ],
                                            ],
                                        ],
//                    1 => [
//                        'match' => [
//                            'ngan_chan' => '1',
//                        ],
//                    ],
                                    ],
                                ],
                            ], '', '', '', '')->paginate();

                    }

                }

                $count = $data->count();

            } elseif ($getcoban) {
//                dd($status);
                if (true) {
                    if (is_integer(strpos($getcoban, 'd'))) {
                        $getcoban2 = str_replace("d", "đ", $getcoban);
                        $data = $data->FullTextSearch('duong_su', $getcoban)
                            ->OrFullTextSearch('duong_su', $getcoban2)->orderByDesc("suutranb.st_id")->limit(20)->get();
                    } else {
                        $data = $data->FullTextSearch('duong_su', $getcoban)->orderByDesc("suutranb.st_id")->limit(20)->get();

                    }
                } else {
                    if ($priority) {

                        $data = SuuTraModel::searchByQuery(
                            [
                                'bool' => [
                                    'should' => [
                                        0 => [
                                            'match' => [
                                                'duong_su' => [
                                                    'query' => $getcoban,
                                                    'analyzer' => 'my_analyzer',
                                                ],
                                            ],
                                        ],
                                        1 => [
                                            'match' => [
                                                'ngan_chan' => [
                                                    'query' => '1',
                                                    'boost' => 10
                                                ],

                                            ],
                                        ],
                                    ],
                                ],
                            ])->paginate();

                    } else {
                        $data = SuuTraModel::searchByQuery(
                            [
                                'bool' => [
                                    'should' => [
                                        0 => [
                                            'match' => [
                                                'duong_su' => [
                                                    'query' => $getcoban,
                                                    'analyzer' => 'my_analyzer',
                                                ],
                                            ],
                                        ],
//                                        1 => [
//                                            'match' => [
//                                                'ngan_chan' => '1',
//                                            ],
//                                        ],
                                    ],
                                ],
                            ], '', '', '', '')->paginate();

                    }


                }


                $count = $data->count();

            } else {
            }

        }
        if ($getcoban || $getNangCao) {
//        $data=collect($data->toArray());
//dd($data);
        } else {
            $data = $data->orderByDesc("suutranb.st_id")->limit(20)->get();

        }
        $str_json2 = json_encode(array_filter(array(str_replace("%", " ", $getcoban))));
        $str_json = json_encode(array_filter(array(str_replace("%", " ", $getNangCao))));
        return $data;
    }
    public static function convert($normal_string){
        // $old_string=$normal_string;
        if($normal_string&& is_string($normal_string)){
            $normal_string=Normalizer::normalize($normal_string, Normalizer::FORM_KC);
        }
        return $normal_string;
    }
    public static function convert_unicode($data){

        $data=collect($data)->map(function($key,$item){
            return $converted[$item]=AppController::convert($key);
        });

        $data=$data->forget('st_id');
        $data=$data->forget('pic');
        $data=$data->forget('release_file_name');
        // $old_string=$normal_string;
        return $data;
    }
    public static function convert_unicode_object($data){

        $data=collect($data)->map(function($key,$item){
            return $converted[$item]=AppController::convert($key);
        });

        $data=$data->forget('st_id');
        $data=$data->forget('pic');
        $data=$data->forget('release_file_name');
        $data=(object)$data->toArray();
        // $old_string=$normal_string;
        return $data;
    }
}
