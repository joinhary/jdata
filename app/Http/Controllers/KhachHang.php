<?php

namespace App\Http\Controllers;


use App\Models\CTVKhachHangModel;
use App\Models\CTVLyLichKhachHangModel;
use App\Models\KhachHangModel;
use App\Models\KieuModel;
use App\Models\KieuTieuMucModel;
use App\Models\LichSuHonNhanModel;
use App\Models\LyLichKhachHangModel;
use App\Models\RoleModel;
use App\Models\TieuMucModel;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Sentinel;
use Illuminate\Http\Request;
use Image;

trait KhachHang
{
    use users;
    use imageHandling;

    public static function get_list_nhan_vien_bySlug($vanphong, $slug)
    {
        $role_kh = Sentinel::findRoleBySlug($slug);
        $tvp = $role_kh->users()->select('id as kh_id', 'first_name', 'phone', 'address', 'k_id', 'nv_vanphong')
            ->join('nhanvien', 'nv_id', '=', 'id')
            ->where('nv_vanphong', $vanphong)
            ->get();
        return $tvp;
    }

    /**
     * Trả về danh sách khách hàng
     * @param Request $request
     * @return mixed
     */
    public static function list_khachhang(Request $request)
    {
        $where = [];
        if ($request->tk_khachhang) {
            $where[] = ['first_name', 'LIKE', '%' . $request->tk_khachhang . '%'];
        }

        $where[] = ['k_id', '!=', '1031'];
        $role_kh = Sentinel::findRoleBySlug('khach-hang');
        $kethon = KieuTieuMucModel::select('ktm_id')->where('ktm_keywords', 'ket-hon')->first()->ktm_id;
        $hon_phoi = TieuMucModel::where('tm_keywords', 'hon-phoi')->first()->tm_id;
        if ($request->sl != null) {
            $khachhang = $role_kh->users()->select('id as kh_id', 'first_name', 'phone', 'address', 'k_id')->where($where)
                ->whereNull('deleted_at')
                ->limit($request->sl)
                ->orderby('id', 'desc')
                ->get();
        } else {
            if ($request->ajax()) {
                $khachhang = $role_kh->users()->select('id as kh_id', 'users.first_name as name', 'users.phone as p', 'users.address as a', 'k_id as k')->where($where)->orderby('id', 'desc')
                    ->whereNull('deleted_at')
                    ->paginate(15);

            } else {
                $khachhang = $role_kh->users()->select('id as kh_id', 'users.first_name as name', 'users.phone as p', 'users.address as a', 'k_id as k')
                    ->where($where)->orderby('id', 'desc')
                    ->whereNull('deleted_at')->paginate(15);
            }
        }
        $bichan = [];
        $giaichap = [];
        $giaitoa = [];
        $vo_chong = [];
        $hon_phoi_id = [];
//        dd($khachhang);
        foreach ($khachhang as $kh) {
//            dd($kh);
            $tt_honnhan = KhachHangModel::select('kh_giatri')
                ->join('tieumuc', 'tieumuc.tm_id', '=', 'khachhang.tm_id')
                ->where('kh_id', $kh->kh_id)
                ->where('tm_keywords', 'tinh-trang-hon-nhan')
                ->pluck('kh_giatri')->first();
            $bichan[] = LyLichKhachHangModel::where('kh_id', $kh->kh_id)->where('tinhtrang', 1)->select('ngayky', 'kh_id')->orderBy('created_at', 'desc')->first();
            $giaichap[] = LyLichKhachHangModel::where('kh_id', $kh->kh_id)->where('tinhtrang', 2)->select('ngayky', 'kh_id')->orderBy('created_at', 'desc')->first();
            $giaitoa[] = LyLichKhachHangModel::where('kh_id', $kh->kh_id)->where('tinhtrang', 2)->select('ngayky', 'kh_id')->orderBy('created_at', 'desc')->first();
            if ($tt_honnhan == $kethon) {
                $kh_giatri = KhachHangModel::select('kh_giatri')->where('kh_id', $kh->kh_id)
                    ->where('tm_id', $hon_phoi)
                    ->first()['kh_giatri']??'';
                $first_name = "";
                if (isset(KhachHangModel::select('kh_giatri')
                        ->where('kh_id', $kh_giatri)
                        ->where('tm_id', 1086)
                        ->first()['kh_giatri'])) {
                    $first_name = KhachHangModel::select('kh_giatri')
                        ->where('kh_id', $kh_giatri)
                        ->where('tm_id', 1086)
                        ->first()['kh_giatri'];
                } else {
                    $find_user_id = User::find($kh_giatri);
                    $first_name = $find_user_id == null ? null : $find_user_id->first_name;
                }
                $hon_phoi_id[] = $kh_giatri;
                $vo_chong[] = $first_name;
            } else {
                $vo_chong[] = null;
                $hon_phoi_id[] = null;
            }
        }
        return ['khachhang' => $khachhang, 'honphoi' => $vo_chong, 'bichan' => $bichan, 'giaichap' => $giaichap, 'giaitoa' => $giaitoa, 'hon_phoi_id' => $hon_phoi_id];
    }

    /**
     * Trả về danh sách khách hàng
     * @param Request $request
     * @return mixed
     */
//    public static function list_khachhang_ctv(Request $request)
//    {
//        $where = [];
//        if ($request->tk_khachhang) {
//            $where[] = ['first_name', 'LIKE', '%' . $request->tk_khachhang . '%'];
//        }
//        $where[] = ['k_id', '1031'];
//        $role_kh = Sentinel::findRoleBySlug('khach-hang');
//        $kethon = KieuTieuMucModel::select('ktm_id')->where('ktm_keywords', 'ket-hon')->first()->ktm_id;
//        $hon_phoi = TieuMucModel::where('tm_keywords', 'hon-phoi')->first()->tm_id;
//        if ($request->sl != null) {
//            $khachhang = $role_kh->users()->select('id as kh_id', 'first_name', 'phone', 'address', 'k_id', 'ctv_id', 'ccv_id', 'request_code')->where($where)
//                ->take($request->sl)
//                ->orderby('id', 'desc')
//                ->get();
//        } else {
//            $khachhang = $role_kh->users()->select('id as kh_id', 'first_name', 'phone', 'address', 'k_id', 'ctv_id', 'ccv_id', 'request_code')->where($where)->get();
//        }
//        $bichan = [];
//        $giaichap = [];
//        $giaitoa = [];
//        $vo_chong = [];
//        foreach ($khachhang as $kh) {
//            (isset(CTVKhachHangModel::select('kh_giatri')
//                    ->where('kh_id', $kh->kh_id)
//                    ->where('tm_id', 1086)
//                    ->first()['kh_giatri'])) ? $kh->first_name = CTVKhachHangModel::select('kh_giatri')
//                ->where('kh_id', $kh->kh_id)
//                ->where('tm_id', 1086)
//                ->first()['kh_giatri'] :
//                $kh->first_name = CTVKhachHangModel::select('kh_giatri')
//                        ->where('kh_id', $kh->kh_id)
//                        ->where('tm_id', 1)
//                        ->first()['kh_giatri'] . " " . CTVKhachHangModel::select('kh_giatri')
//                        ->where('kh_id', $kh->kh_id)
//                        ->where('tm_id', 2)
//                        ->first()['kh_giatri'] . " " . CTVKhachHangModel::select('kh_giatri')
//                        ->where('kh_id', $kh->kh_id)
//                        ->where('tm_id', 6)
//                        ->first()['kh_giatri'];
//            $tt_honnhan = CTVKhachHangModel::select('kh_giatri')
//                ->join('tieumuc', 'tieumuc.tm_id', '=', 'ctv_khachhang.tm_id')
//                ->where('kh_id', $kh->kh_id)
//                ->where('tm_keywords', 'tinh-trang-hon-nhan')
//                ->first()['kh_giatri'];
//            $bichan[] = CTVLyLichKhachHangModel::where('kh_id', $kh->kh_id)->where('tinhtrang', 1)->select('ngayky', 'kh_id')->orderBy('created_at', 'desc')->first();
//            $giaichap[] = CTVLyLichKhachHangModel::where('kh_id', $kh->kh_id)->where('tinhtrang', 2)->select('ngayky', 'kh_id')->orderBy('created_at', 'desc')->first();
//            $giaitoa[] = CTVLyLichKhachHangModel::where('kh_id', $kh->kh_id)->where('tinhtrang', 2)->select('ngayky', 'kh_id')->orderBy('created_at', 'desc')->first();
//            if ($tt_honnhan == $kethon) {
//                $kh_giatri = CTVKhachHangModel::select('kh_giatri')->where('kh_id', $kh->kh_id)
//                    ->where('tm_id', $hon_phoi)
//                    ->first()['kh_giatri'];
//                $first_name = "";
//                (isset(CTVKhachHangModel::select('kh_giatri')
//                        ->where('kh_id', $kh_giatri)
//                        ->where('tm_id', 1086)
//                        ->first()['kh_giatri'])) ? $first_name = CTVKhachHangModel::select('kh_giatri')
//                    ->where('kh_id', $kh_giatri)
//                    ->where('tm_id', 1086)
//                    ->first()['kh_giatri'] :
//                    $first_name = User::find($kh_giatri)->first_name;
//                $vo_chong[] = $first_name;
//            } else {
//                $vo_chong[] = null;
//            }
//        }
//        return ['khachhang' => $khachhang, 'honphoi' => $vo_chong, 'bichan' => $bichan, 'giaichap' => $giaichap, 'giaitoa' => $giaitoa];
//    }

    public static function list_khachhang_app(Request $request)
    {
        $where = [];

        if ($request->tk_khachhang) {
            $where[] = ['first_name', 'LIKE', '%' . $request->tk_khachhang . '%'];
        }
        $role_kh = Sentinel::findRoleBySlug('khach-hang');
        $khachhang = $role_kh->users()->where($where);
        return $khachhang;
    }

    /**
     * Lấy mảng tiểu mục thuộc kiểu  được chọn
     * @param Request $request
     * @param $id
     * @return array
     */
    public function get_tieumuc_kieu(Request $request, $id)
    {
        $kieu = $id;
        if ($request->kieu) {
            $kieu = $request->kieu;
        }
        $tm = KieuModel::select('k_tieumuc')->where('k_id', $kieu)->first();

        $tm_arr = $tm != null ? explode(' ', $tm->k_tieumuc) : [];
        return $tm_arr;
    }

    public function get_kieu_tieumuc_vochong_app($k_id)
    {
        $kieu = $k_id;
        if ($kieu == 0) {
            return null;
        }
        $tm = KieuModel::select('k_tieumuc')->where('k_id', $kieu)->first();
        $tm_arr = explode(' ', $tm->k_tieumuc);
        return $tm_arr;
    }


    public function get_tieumuc_kieu_app(Request $request, $k_id)
    {
        if ($k_id) {
            $kieu = $k_id;
        } elseif ($request->kieu) {
            $kieu = $request->kieu;
        }
        $tm = KieuModel::select('k_tieumuc')->where('k_id', $kieu)->first();
        $tm_arr = explode(' ', $tm->k_tieumuc);
        return $tm_arr;
    }

    public function list_tieumuc_form($tm_arr, $k_id)
    {
        $tieumuc = TieuMucModel::select('tieumuc.tm_id', 'tm_nhan', 'tm_loai', 'tm_keywords', 'tm_batbuoc')
            ->leftjoin('tieumuc_sapxep', 'tieumuc_sapxep.tm_id', '=', 'tieumuc.tm_id')
            ->whereIn('tieumuc.tm_id', $tm_arr)
            ->where('k_id', $k_id)
            ->orderBy('tm_sort', 'asc');
        return $tieumuc;
    }

    /**
     * Lấy về danh sách các câu trả lời cho tiểu mục có loại select
     * @param Request $request
     * @return array
     */
    public function get_kieutm(Request $request)
    {
        $tm = KieuTieuMucModel::select('kieu.k_id as id', 'k_tieumuc')
            ->leftjoin('kieu', 'kieu.k_id', '=', 'kieu_tieumuc.k_id')
            ->where('ktm_id', $request->ktm_id)->first();
        $tm_arr = explode(' ', $tm->k_tieumuc);
        if ($request->kh_id) {
            return ['tm_arr' => $tm_arr, 'k_id' => $tm->id];
        } else {
            $tieumuc = $this->list_tieumuc_form($tm_arr, $tm->id)->get();

            return $tieumuc;
        }
    }

    public function list_tieumuc_form_app($tm_arr, $k_id)
    {
        $tieumuc = TieuMucModel::select('tieumuc.tm_id', 'tm_nhan', 'tm_loai', 'tm_keywords', 'tm_batbuoc')
            ->leftjoin('tieumuc_sapxep', 'tieumuc_sapxep.tm_id', '=', 'tieumuc.tm_id')
            ->whereIn('tieumuc.tm_id', $tm_arr)
            ->where('k_id', $k_id)
            ->orderBy('tm_sort', 'asc')
            ->get();
        foreach ($tieumuc as $tm) {
            if ($tm->tm_loai == 'select') {
                $tm['data'] = KieuTieuMucModel::select('ktm_id as id', 'ktm_traloi as name', 'ktm_keywords')->where('tm_id', $tm->tm_id)->get();
            }
        }

        return $tieumuc;
    }

    public function list_tieumuc_form_app_image($tm_arr, $k_id)
    {
        $tieumuc = TieuMucModel::select('tieumuc.tm_id', 'tm_nhan', 'tm_loai', 'tm_keywords', 'tm_batbuoc')
            ->leftjoin('tieumuc_sapxep', 'tieumuc_sapxep.tm_id', '=', 'tieumuc.tm_id')
            ->whereIn('tieumuc.tm_id', $tm_arr)
            ->where('k_id', $k_id)
            ->where('tm_loai', 'file')
            ->orderBy('tm_sort', 'asc')
            ->get();
        foreach ($tieumuc as $tm) {
            if ($tm->tm_loai == 'select') {
                $tm['data'] = KieuTieuMucModel::select('ktm_id as id', 'ktm_traloi as name', 'ktm_keywords')->where('tm_id', $tm->tm_id)->get();
            }
        }

        return $tieumuc;
    }

    /**
     * Hàm thêm khách hàng
     * @param Request $request
     */
    public function store_customer(Request $request)
    {
//        dd();

        $tm_honnhan = TieuMucModel::select('tm_id')
            ->where('tm_keywords', 'tinh-trang-hon-nhan')
            ->first()->tm_id;
        $honphoi = $request->get('tm-61');
        $save_path = '/khach-hang/avatar/';
        $ten_anh = 'new-user.png';
        /*Xử lý request vào bảng Users*/
        $first_name = $request->first_name;
        $first_name = preg_replace('/\s+/', ' ', $first_name);
        $username = $request->username;
        $password = $request->password;
        $contact = $request->contact;
        $k_id = $request->kieu;
        //Kiểm tra checkbox "Kích hoạt" có check hay không?
        if ($request->ajax()) {
            $activate = false;
        } else {
            $activate = $request->get('activate') ? true : false;
        }

        //Thêm tài khoản khách hàng vào bảng users
        $user = Sentinel::register([
            'email' => $username,
            'password' => $password,
            'first_name' => $first_name,
            'phone' => $contact,
            'pic' => $ten_anh,
            'k_id' => $k_id,
            'user_state' => 0,
            'id_vp' => \App\Models\NhanVienModel::where('nv_id', '=', Sentinel::getUser()->id)->first()->nv_vanphong,
            'id_ccv' => Sentinel::getUser()->id
        ], $activate);
        if (!$request->kh_id) {
            $request->merge(['kh_id' => $user->id]);
        }

        //Kiểm tra request có file hay ko và đổi tên file upload lên rồi lưu lại với tên file mới
        if ($request->hasFile('pic')) {
            $file = $request->file('pic');
//            dd($file);
            $ten_anh = $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move('assets/images/authors', $ten_anh);

            User::where('id', $user->id)->update([
                'pic' => $ten_anh,
            ]);
        }

        //Gắn tài khoản khách hàng vào role tương ứng
        $role = Sentinel::findRoleBySlug('khach-hang');
        $role->users()->attach($user->id);

        /* Xử lý các request vào bảng khách hàng*/
        $tm_diachi = TieuMucModel::select('tm_id')
            ->where('tm_keywords', 'dia-chi-lien-he')
            ->first();
        $tm_honnhan = TieuMucModel::select('tm_id')
            ->where('tm_keywords', 'tinh-trang-hon-nhan')
            ->first()->tm_id;
        $req_honphoi = 'tm-' . TieuMucModel::select('tm_id')
                ->where('tm_keywords', 'hon-phoi')
                ->first()->tm_id;
        $chua_kh_id = KieuTieuMucModel::where('ktm_keywords', 'chua-ket-hon')->first()->ktm_id;
        $tm_kethon = explode(' ', KieuModel::where('k_keywords', 'ket-hon')->first()->k_tieumuc);
        $tm_lyhon = explode(' ', KieuModel::where('k_keywords', 'ly-hon')->first()->k_tieumuc);
//        dd($tm_lyhon);
        $array = [];
        $i = 00;
        foreach ($request->ds_tm as $tm) {
            $tt_kethon = '';
//            dd($request->ds_tm);
            if ($request->hasFile($tm)) {
                //Các request có dạng file (ảnh)
                $file = $request->$tm;
                foreach ($file as $value) {
                    $ten_anh = time() . $i . '.' . $value->getClientOriginalExtension();
                    $value->move('images/khachhang', $ten_anh);
                    array_push($array, $ten_anh);
                    $i++;
                }
                $kh_giatri = json_encode($array);
                $array = [];
//                $kh_giatri = json_encode($this->addImage($request, $save_path, $tm));

            } else {
                //Các request có dạng giá trị thông thường (text, number, date,..)
                $kh_giatri = $request->$tm;
            }
//


            //Cắt "tm-" để lấy ra được id tiểu mục thực tế
            //vd: tm-1 -> 1
            $tm_id = substr($tm, 3);
            if (!$request->ajax()) {
                if ($tm_id == $tm_honnhan) {
                    $tt_kethon = $request->$tm;
                    if ($request->$tm != $chua_kh_id) {
                        KhachHangModel::where('kh_id', $request->$req_honphoi)
                            ->where('tm_id', $tm_id)
                            ->update([
                                'kh_giatri' => $kh_giatri
                            ]);
                    }
                }
                if ($tm == $req_honphoi && $tt_kethon != $chua_kh_id) {
                    KhachHangModel::create([
                        'kh_id' => $request->$req_honphoi,
                        'tm_id' => substr($req_honphoi, 3),
                        'kh_giatri' => $user->id
                    ]);
                }
                if ((in_array($tm_id, $tm_kethon) || in_array($tm_id, $tm_lyhon)) && $tm_id != $tm_honnhan && $tm != $req_honphoi) {
                    KhachHangModel::create([
                        'kh_id' => $request->$req_honphoi,
                        'tm_id' => $tm_id,
                        'kh_giatri' => $kh_giatri
                    ]);
                }
            }

            KhachHangModel::create([
                'kh_id' => $user->id,
                'tm_id' => $tm_id,
                'kh_giatri' => $kh_giatri
            ]);

            if ($tm_id == $tm_diachi->tm_id) {
                User::find($user->id)
                    ->update([
                        'address' => $kh_giatri
                    ]);
            }
            //Kiểm tra tiểu mục hiện tại có phải là tiểu mục kết hôn không.
            //Nếu đúng thì kiểm tra câu trả lời có k_id != 0 thì tiến hành thê lịch sử hôn nhân
            if (!$request->ajax()) {
                if ($tm_id == $tm_honnhan) {
                    if ($request->$tm != $chua_kh_id) {
                        $this->lichsuhonnhan_create($request, $user->id, $request->$req_honphoi, $tm_honnhan, $request->$tm);
                    }
                }
            }
        }
        //Ghi log
        $user_exec = Sentinel::getUser()->id;
        $description = "Tạo đương sự và tài khoản  cho " . $user->first_name;
        $this->api_create_log($user_exec, $description);
        return $user->id;
    }

    /*
    *store kh cho app
    */
    public function store_customer_app(Request $request)
    {
        $save_path = 'images';
        $ten_anh = 'new-user.png';

        /*Xử lý request vào bảng Users*/
        $first_name = $request->first_name;
        $username = $request->username;
        $password = $request->password;
        //        $address = $request->address;
        $contact = $request->contact;
        $k_id = $request->kieu;

        //Kiểm tra request có file hay ko và đổi tên file upload lên rồi lưu lại với tên file mới
        if ($request->hasFile('pic')) {
            $image = $request->file('pic');

            $ten_anh = time() . '.' . $image->getClientOriginalExtension();

            /*$item->move($save_path, $img_name);
            dd($item);*/

            $image->move($save_path, $ten_anh);


        }


        //Kiểm tra checkbox "Kích hoạt" có check hay không?

        $activate = true;


        //Thêm tài khoản khách hàng vào bảng users
        $user = Sentinel::register([
            'email' => $username,
            'password' => $password,
            'first_name' => $first_name,
            'phone' => $contact,
            'pic' => $ten_anh,
            'k_id' => $k_id,
            'user_state' => 0
        ], $activate);

        //Gắn tài khoản khách hàng vào role tương ứng
        $role = Sentinel::findRoleBySlug('khach-hang');
        $role->users()->attach($user->id);


        /* Xử lý các request vào bảng khách hàng*/
        $tm_diachi = TieuMucModel::select('tm_id')
            ->where('tm_keywords', 'dia-chi-lien-he')
            ->first();
        $tm_honnhan = TieuMucModel::select('tm_id')
            ->where('tm_keywords', 'tinh-trang-hon-nhan')
            ->first()->tm_id;
        $req_honphoi = 'tm-' . TieuMucModel::select('tm_id')
                ->where('tm_keywords', 'hon-phoi')
                ->first()->tm_id;
        $chua_kh_id = KieuTieuMucModel::where('ktm_keywords', 'chua-ket-hon')->first()->ktm_id;
        $tm_kethon = explode(' ', KieuModel::where('k_keywords', 'ket-hon')->first()->k_tieumuc);
        $tm_lyhon = explode(' ', KieuModel::where('k_keywords', 'ly-hon')->first()->k_tieumuc);
        $name = explode(',', $request->ds_tm);
        foreach ($name as $tm) {
            $tt_kethon = '';
            if ($request->hasFile($tm)) {
                //Các request có dạng file (ảnh)
                $ten_anh2 = time() . '.' . $request->file($tm)->getClientOriginalExtension();

                /*$item->move($save_path, $img_name);
                dd($item);*/

                $request->file($tm)->move('images/khachhang', $ten_anh2);
                $kh_giatri = json_encode($ten_anh2);

            } else {
                //Các request có dạng giá trị thông thường (text, number, date,..)
                $kh_giatri = $request->$tm;
            }

            //Cắt "tm-" để lấy ra được id tiểu mục thực tế
            //vd: tm-1 -> 1
            $tm_id = substr($tm, 3);
            //nếu là đương sự chính => cập nhật tình trạng hôn nhân cho vợ/chồng
            if ($request->type_ds) {
                if ($tm_id == $tm_honnhan) {
                    $tt_kethon = $request->$tm;
                    if ($request->$tm != $chua_kh_id) {
                        KhachHangModel::where('kh_id', $request->ds2_id)
                            ->where('tm_id', $tm_id)
                            ->update([
                                'kh_giatri' => $kh_giatri
                            ]);
                    }
                }
                if ($tm == $req_honphoi && $tt_kethon != $chua_kh_id) {
                    KhachHangModel::create([
                        'kh_id' => $request->ds2_id,
                        'tm_id' => substr($req_honphoi, 3),
                        'kh_giatri' => $user->id
                    ]);
                }
                if ((in_array($tm_id, $tm_kethon) || in_array($tm_id, $tm_lyhon)) && $tm_id != $tm_honnhan && $tm != $req_honphoi) {
                    KhachHangModel::create([
                        'kh_id' => $request->ds2_id,
                        'tm_id' => $tm_id,
                        'kh_giatri' => $kh_giatri
                    ]);
                }
            }
            KhachHangModel::create([
                'kh_id' => $user->id,
                'tm_id' => $tm_id,
                'kh_giatri' => $kh_giatri
            ]);

            if ($tm_id == $tm_diachi->tm_id) {
                User::find($user->id)
                    ->update([
                        'address' => $kh_giatri
                    ]);
            }

            //Kiểm tra tiểu mục hiện tại có phải là tiểu mục kết hôn không.
            //Nếu đúng thì kiểm tra câu trả lời có k_id != 0 thì tiến hành thê lịch sử hôn nhân
            //            if ($tm_id == $tm_honnhan) {
            //                $check = KieuTieuMucModel::select('k_id')->where('ktm_id', $request->$tm)->first()->k_id;
            //                if ($check != $ktm_id) {
            //                    $this->lichsu_honnhan($request, $user->id   );
            //                }
            //            }
        }
        //Ghi log
        $user_exec = $request->user_id;
        $description = "Tạo đương sự và tài khoản  cho " . $user->first_name;
        $this->api_create_log($user_exec, $description);

        if ($request->ajax()) {
            return ['status' => 'success', 'message' => 'Tạo đương sự thành công!', 'data' => $user->id];
        }
    }

    /**
     * Lấy danh sách tiểu mục form edit
     * @param Request $request
     * @return array
     */
    public function get_tm_edit(Request $request)
    {
        $temp = KhachHangModel::select('kh_giatri')
            ->join('tieumuc', 'tieumuc.tm_id', '=', 'khachhang.tm_id')
            ->where('tm_keywords', 'tinh-trang-hon-nhan')
            ->where('kh_id', $request->kh_id)
            ->first()->kh_giatri;

        $cond = KieuTieuMucModel::select('k_id')
            ->where('ktm_id', $temp)
            ->first()->k_id;

        $tieumuc = $this->get_kieutm($request);
        if ($cond == 0) {
            $k_id = KieuTieuMucModel::select('k_id')
                ->where('ktm_id', $request->ktm_id)
                ->first()->k_id;

            $tieumuc = $this->list_tieumuc_form($tieumuc['tm_arr'], $k_id)->get();
            return $tieumuc;
        } else {
            $old_select = KhachHangModel::join('tieumuc', 'tieumuc.tm_id', '=', 'khachhang.tm_id')
                ->where('kh_id', $request->kh_id)
                ->where('tm_keywords', 'tinh-trang-hon-nhan')
                ->first()->kh_giatri;
            $khachhang = KhachHangModel::select('tieumuc.tm_id', 'tm_nhan', 'tm_loai', 'tm_batbuoc', 'tm_keywords', 'kh_giatri')
                ->join('tieumuc', 'tieumuc.tm_id', '=', 'khachhang.tm_id')
                ->join('tieumuc_sapxep', 'tieumuc_sapxep.tm_id', '=', 'tieumuc.tm_id')
                ->where('kh_id', $request->kh_id)
                ->whereIn('khachhang.tm_id', $tieumuc['tm_arr'])
                ->where('k_id', $tieumuc['k_id'])
                ->orderBy('tm_sort', 'asc')
                ->get();

            $kh_arr = [];
            foreach ($khachhang as $kh) {
                if ($kh->tm_keywords == 'hon-phoi') {
                    $kh->kh_giatri = $kh->kh_giatri == null ? '' : (object)[$kh->kh_giatri => User::find($kh->kh_giatri)->first_name];
                }
                $kh_arr[] = $kh->tm_id;
            }
            if ($request->ktm_id == $old_select) {
                $tm_arr = KhachHangModel::select('tieumuc.tm_id', 'tm_nhan', 'tm_loai', 'tm_batbuoc', 'tm_keywords', 'kh_giatri')
                    ->join('tieumuc', 'tieumuc.tm_id', '=', 'khachhang.tm_id')
                    ->where('kh_id', $request->kh_id)
                    ->whereIn('tieumuc.tm_id', $tieumuc['tm_arr'])
                    ->whereNotIn('tieumuc.tm_id', $kh_arr)
                    ->get();
            } else {
                $tm_arr = TieuMucModel::select('tieumuc.tm_id', 'tm_nhan', 'tm_loai', 'tm_batbuoc', 'tm_keywords')
                    ->whereIn('tm_id', $tieumuc['tm_arr'])
                    ->whereNotIn('tm_id', $kh_arr)
                    ->get();
            }
            foreach ($tm_arr as $item) {
                $khachhang[] = $item;
            }
            return $khachhang;
        }
    }

    /**
     * Xử lý và thêm lịch sử hôn nhân cho khách hàng
     * @param Request - $request Bộ request
     * @param $user1 - id đương sự đang cập nhật
     * @param $user2 - id hôn phối
     * @param $tm_honnhan - id của tiểu mục tình trạng hôn nhân
     * @param $tinhtrang - câu trả lời tiểu mục tình trạng hôn nhân
     */
    public function lichsuhonnhan_create(Request $request, $user1, $user2, $tm_honnhan, $tinhtrang)
    {
        $tm_arr = TieuMucModel::select('tm_id')
            ->where('tm_keywords', 'giay-cn-dang-ky-ket-hon-so')
            ->orWhere('tm_keywords', 'quyet-dinh-ly-hon-so')
            ->orWhere('tm_keywords', 'so-giay-chung-tu-vc')
            ->get();
//        dd($tm_arr);
        $so_chung_nhan = '';

        foreach ($tm_arr as $item) {
            $tm_giayto[] = $item->tm_id;
        }
//        dd($tm_arr);
        foreach ($request->ds_tm as $tm) {

            $tm_id = substr($tm, 3);
            if (in_array($tm_id, $tm_giayto)) {
                $so_chung_nhan = $request->$tm;
            }
        }

        LichSuHonNhanModel::create([
            'ds1_id' => $user1,
            'ds2_id' => $user2,
            'so_chung_nhan' => $so_chung_nhan,
            'lshn_tinhtrang' => $tinhtrang
        ]);

        LichSuHonNhanModel::create([
            'ds1_id' => $user2,
            'ds2_id' => $user1,
            'so_chung_nhan' => $so_chung_nhan,
            'lshn_tinhtrang' => $tinhtrang
        ]);

    }

    public function lichsuhonhan_index($ds1_id)
    {
        $lichsuhonnhan = LichSuHonNhanModel::select('ds2_id', 'first_name', 'ktm_traloi as tinhtrang')
            ->leftjoin('users', 'users.id', '=', 'ds2_id')
            ->leftjoin('kieu_tieumuc', 'ktm_id', '=', 'lshn_tinhtrang')
            ->where('ds1_id', $ds1_id)
            ->get();

        return $lichsuhonnhan;
    }

    /**
     * Menu đa cấp đệ quy
     * @param $kieu
     * @param int $k_id
     * @return array
     */
    public function ordered_menu($kieu, $k_id = 0)
    {
        $temp_array = [];
        foreach ($kieu as $k) {
            if ($k['k_parent'] == $k_id) {
                $nodes = $this->ordered_menu($kieu, $k['k_id']);
                if ($nodes) {
                    $k['nodes'] = $nodes;
                    $k['selectable'] = false;
                }
                $k['href'] = $k['k_id'];
                $k['state'] = ['expanded' => false];
                $temp_array[] = $k;
            }
        }
        return $temp_array;
    }
}
