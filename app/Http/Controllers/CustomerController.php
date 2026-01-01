<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\KhachHangGDModel;
use App\Models\KhachHangLog;
use App\Models\KhachHangModel;
use App\Models\KieuModel;
use App\Models\KieuTieuMucModel;
use App\Models\LichSuHonNhanModel;
use App\Models\LyLichKhachHangModel;
use App\Models\NhanVienModel;
use App\Models\RoleModel;
use App\Models\RoleUsersModel;
use App\Models\TieuMucModel;
use App\Models\User;
use Carbon\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Facebook\WebDriver\Exception\NullPointerException;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Ixudra\Curl\Facades\Curl;
use PDF;
use Sentinel;





class CustomerController extends Controller
{
    

    use KhachHang;

    public function index(Request $request)
    {
        $k = KieuModel::select('k_id')->where('k_keywords', 'duong-su')->first();
        $kieu = KieuModel::where('k_parent', $k->k_id)
            ->where('k_trangthai', 1)
            ->pluck('k_nhan', 'k_id');
        $t = LyLichKhachHangModel::select('kh_id')->where('tinhtrang', 1)->get();
        $nganchan = [];
        foreach ($t as $item) {
            $nganchan[] = $item->kh_id;
        }
        $search = $request->tk_khachhang;
//        dd($search);
        if ($request->ajax()) {
            $kh_res = $this->list_khachhang($request);
            $khachhang = $kh_res['khachhang'];
            $bichan = $kh_res['bichan'];
            $giaichap = $kh_res['giaichap'];
            $giaitoa = $kh_res['giaitoa'];
            $vochong_id = $kh_res['hon_phoi_id'];
            return [
                'status' => 'success',
                'data' => $khachhang,
                'honphoi' => $kh_res['honphoi'],
                'bichan' => $bichan,
                'giaitoa' => $giaitoa,
                'giaichap' => $giaichap,
                'honphoi_id' => $vochong_id
            ];
        } else {
            $kh_res = $this->list_khachhang($request);
            $khachhang = $kh_res['khachhang'];
            $vochong = $kh_res['honphoi'];
            $bichan = $kh_res['bichan'];
          //  dd($khachhang->links());
            $count = $khachhang->count();
            $tong = User::leftjoin('role_users', 'role_users.user_id', 'users.id')
                ->leftjoin('roles', 'roles.id', 'role_users.role_id')
                ->where('roles.slug', 'khach-hang')->get()->count();
            return view('admin.khachhang.index',
                compact('khachhang', 'tong', 'count', 'vochong', 'kieu', 'nganchan', 'search', 'bichan'));
        }
    }

    public function khachHangList(Request $request)
    {
        $khachhang = Sentinel::findRoleBySlug('khach-hang')->users()->select('id as kh_id', 'first_name', 'phone',
            'k_id')
            ->whereNull('deleted_at')
            ->orderby('id', 'desc');
        if ($param = $request->param) {
            $khachhang = $khachhang->where(function ($q) use ($param) {
                $q->where('first_name', 'like', $param)
                    ->orWhere('phone', 'like', $param);
            });
        }
        return $khachhang->get();
    }

    public function create(Request $request)
    {
        $k_id = $request->kieu;
        $tm_arr = $this->get_tieumuc_kieu($request, $k_id);
        $tieumuc = $this->list_tieumuc_form($tm_arr, $k_id)->get();

//        dd([$tm_arr, $tieumuc]);
        $loai = $request->loai;
        if ($request->ajax()) {
            return ['status' => 'success', 'data' => $tieumuc];
        } else {

            return view('admin.khachhang.create', compact('tieumuc', 'k_id', 'loai'));
        }
    }

    public function store(Request $request)
    {
        /*Validate các request tiểu mục động*/
//        dd($request->all());
        $validate_tm = $this->validate_dynamic($request);
        if ($validate_tm->fails()) {
            if ($request->ajax()) {
                return ['status' => 'error', 'message' => $validate_tm->errors()];
            } else {
                return redirect()->back()->withErrors($validate_tm->errors())->withInput();
            }
        }
        /*Validate các request tĩnh (ảnh đại diện, tài khoản đăng nhập, nhãn)*/
        $validate_user = $this->validate_static_store($request);
        if ($validate_user->fails()) {
            if ($request->ajax()) {
                return ['status' => 'error', 'message' => $validate_user->errors()];
            } else {
                return redirect()->back()->withErrors($validate_user->errors())->withInput();
            }
        }
        /*Gọi trait thực thi lưu thông tin khách hàng*/
        $id = $this->store_customer($request);
        $this->logCreate($id, $request->note ?? 'Không có ghi chú', '');
        if ($request->ajax()) {
            return ['status' => 'success', 'message' => 'Tạo đương sự thành công!', 'data' => $id];
        } else {
            if ($request->loai == 1) {
                return view('admin.khachhang.close');
            } else {
                return Redirect::route('indexKhachHang')->with('success', 'Thêm đương sự thành công!');
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param int $id
     * @return mixed
     */
    public function show(Request $request, $id)
    {
        $khachhang = KhachHangModel::select('tm_nhan', 'tm_loai', 'tm_keywords', 'kh_giatri')
            ->where('kh_id', $id)
            ->join('tieumuc', 'tieumuc.tm_id', '=', 'khachhang.tm_id')
            ->orderBy('tieumuc.tm_id', 'asc')
            ->get();
        $hon_phoi_id = KhachHangModel::join('tieumuc', 'tieumuc.tm_id', '=', 'khachhang.tm_id')
            ->where('tm_keywords', 'hon-phoi')
            ->where('kh_id', $id)
            ->first();
        if ($hon_phoi_id) {
            $honphoi = KhachHangModel::select('tm_nhan', 'tm_loai', 'tm_keywords', 'kh_giatri', 'khachhang.tm_id')
                ->where('kh_id', $hon_phoi_id->kh_giatri)
                ->join('tieumuc', 'tieumuc.tm_id', '=', 'khachhang.tm_id')
                ->orderBy('khachhang.created_at', 'asc')
                ->get();
            foreach ($honphoi as $hp) {
                if ($hp->tm_loai == 'select' && $hp->tm_keywords != 'hon-phoi') {
                    $hp->kh_giatri = KieuTieuMucModel::find($hp->kh_giatri)['ktm_traloi'];
                }

                if ($hp->tm_loai == 'select' && $hp->tm_keywords == 'hon-phoi') {
                    $hp->kh_giatri = User::find($hp->kh_giatri)['first_name'];

                }
                if ($hp->tm_loai == 'file') {
                    if ($hp->kh_giatri) {
//                        $list = [];
//                        foreach (json_decode($hp->kh_giatri) as $item) {
////                            dd($item);
//                            $list[] = $item;
//                        }
//                        $hp->kh_giatri = json_encode($list);
                    }
                }

            }
        } else {
            $honphoi = [];
        }
        foreach ($khachhang as $kh) {
            if ($kh->tm_loai == 'select' && $kh->tm_keywords != 'hon-phoi') {
                $kh->kh_giatri = KieuTieuMucModel::find($kh->kh_giatri)['ktm_traloi'];
            }
            if ($kh->tm_loai == 'select' && $kh->tm_keywords == 'hon-phoi') {
                $kh->kh_giatri = User::find($kh->kh_giatri)['first_name'];
            }
            if ($kh->tm_loai == 'file') {
                if ($kh->kh_giatri) {

//                    $kh->kh_giatri = json_encode($kh->kh_giatri);
                }
            }
        }

        $kh_gd = [];
        $lichsuhonnhan = $this->lichsuhonhan_index($id);
        $account = User::select('id', 'email as username', 'first_name as nhan', 'phone', 'address', 'pic', 'id_vp',
            'id_ccv')->find($id);
        $cmnd = (KhachHangModel::where('kh_id', $id)->where('tm_id', 6)->first()) ? KhachHangModel::where('kh_id',
            $id)->where('tm_id', 6)->first()->kh_giatri : '';
        $imagesArray = [];
        if ($request->ajax()) {
            return ['status' => 'success', 'data' => ['khachhang' => $khachhang, 'nhan' => $account->nhan]];
        }
        return view('admin.khachhang.detail',
            compact('khachhang', 'honphoi', 'account', 'lichsuhonnhan', 'kh_gd', 'cmnd', 'imagesArray'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $account = User::select('id', 'email as username', 'first_name as nhan', 'phone', 'address', 'pic',
            'k_id')->find($id);
        $activation = Activation::completed($account);
        $request = new Request();
        $tm_arr = $this->get_tieumuc_kieu($request, $account->k_id);
        $k_id = $account->k_id;
        $tieumuc = $this->list_tieumuc_form($tm_arr, $k_id)->get();
        $khachhang = KhachHangModel::select('khachhang.tm_id', 'tm_nhan', 'tm_loai', 'tm_keywords', 'tm_batbuoc',
            'kh_giatri')
            ->join('tieumuc', 'tieumuc.tm_id', '=', 'khachhang.tm_id')
            ->where('kh_id', $id)
            ->orderBy('khachhang.created_at', 'asc')
            ->get();
        $kh_arr = [];
        $tm_honphoi = TieuMucModel::where('tm_keywords', 'hon-phoi')->first()->tm_id;
        $honphoi = null;
        foreach ($khachhang as $kh) {
            $kh_arr[] = $kh->tm_id;
            if ($kh->tm_id == $tm_honphoi) {
                (isset(KhachHangModel::select('kh_giatri')
                        ->where('kh_id', $kh->kh_giatri)
                        ->where('tm_id', 1086)
                        ->first()['kh_giatri'])) ? $kh->first_name = KhachHangModel::select('kh_giatri')
                    ->where('kh_id', $kh->kh_giatri)
                    ->where('tm_id', 1086)
                    ->first()['kh_giatri'] :
                    $kh->first_name = KhachHangModel::select('kh_giatri')
                            ->where('kh_id', $kh->kh_giatri)
                            ->where('tm_id', 1)
                            ->first()['kh_giatri'] . " " . KhachHangModel::select('kh_giatri')
                            ->where('kh_id', $kh->kh_giatri)
                            ->where('tm_id', 2)
                            ->first()['kh_giatri'] . " " . KhachHangModel::select('kh_giatri')
                            ->where('kh_id', $kh->kh_giatri)
                            ->where('tm_id', 6)
                            ->first()['kh_giatri'];

                $honphoi[$kh->kh_giatri] = $kh->first_name;
            }
//            if ($kh->tm_loai == "file") {
////                $array = [];
//                $kh->kh_giatri
//                if ($kh->kh_giatri) {
//                    foreach (json_decode($kh->kh_giatri) as $item) {
////                        $array[] = AppController::convert_nextcloud($item, '/khach-hang/giay-to/');
//                    }
//                    $kh->kh_giatri = json_encode($array);
//                }
//
//            }
//            dd($kh->kh_giatri);
        }
//        dd($khachhang);
        $tt_honnhan = KhachHangModel::select('kieu_tieumuc.k_id')
            ->leftjoin('kieu_tieumuc', 'kieu_tieumuc.ktm_id', '=', 'khachhang.kh_giatri')
            ->leftjoin('tieumuc', 'tieumuc.tm_id', '=', 'khachhang.tm_id')
            ->where('kh_id', $id)
            ->where('tieumuc.tm_keywords', '=', 'tinh-trang-hon-nhan')
            ->first();
        $tt_honnhan = $tt_honnhan == null ? 0 : $tt_honnhan->k_id;
        $tinhtrang_kethon = [];
        if ($tt_honnhan != 0) {
            $tm_honnhan = KhachHangModel::select('k_tieumuc')
                ->leftjoin('tieumuc', 'tieumuc.tm_id', '=', 'khachhang.tm_id')
                ->leftjoin('kieu_tieumuc', 'ktm_id', '=', 'kh_giatri')
                ->leftjoin('kieu', 'kieu.k_id', '=', 'kieu_tieumuc.k_id')
                ->where('tm_keywords', 'tinh-trang-hon-nhan')
                ->where('kh_id', $id)
                ->first();
            $tinhtrang_kethon = explode(' ', $tm_honnhan->k_tieumuc);
        }

        $lichsuhonnhan = $this->lichsuhonhan_index($id);

        $ktm_kethon = KieuTieuMucModel::select('ktm_id')->where('ktm_keywords', 'ket-hon')->first()->ktm_id;
        //        $tm_arr = $this->get_tieumuc_kieu($request, $k_id);
        //        $tieumuc = $this->list_tieumuc_form($tm_arr, $k_id)->get();
        return view('admin.khachhang.edit',
            compact('khachhang', 'honphoi', 'tieumuc', 'account', 'kh_arr', 'tm_arr', 'tinhtrang_kethon',
                'lichsuhonnhan', 'ktm_kethon', 'k_id', 'tieumuc', 'activation'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return mixed
     */
    public function update(Request $request, $id)
    {
        $note = $request->note ?? "Không có ghi chú";
        $this->logCreate($id, $note, '');
        $user = User::find($id);
        $activation = Activation::exists($user);
        $ten_anh = '';
        if ($request->file('pic')) {
            $file = $request->file('pic');
            $ten_anh = time() . '.' . $file->getClientOriginalExtension();
            $file->move('assets/images/authors', $ten_anh);
            $save_path = '/khach-hang/avatar/';
//            AppController::upload_nextcloud($ten_anh, $file, $save_path);
//            AppController::upload_nextcloud_thumb($ten_anh, $file, "/khach-hang/avatar/thumbnail/");

            User::where('id', $id)->update([
                'pic' => $ten_anh
            ]);
        }
        if ($request->activate) {
            if ($activation) {
                Activation::complete($user, $activation->code);
                $my_id = Sentinel::getUser()->id;
                $desciption = 'Kích hoạt cho người dùng ' . $user->first_name;
                $this->api_create_log($my_id, $desciption);
//            return ['status' => true, 'message' => 'Đã kích hoạt thành công'];
            } else {
                if (!Activation::completed($user)) {
                    Activation::complete($user, Activation::create($user)->code);
                    $my_id = Sentinel::getUser()->id;
                    User::where('id', $id)->update([
                        'ref_id' => Sentinel::check()->id
                    ]);
                    $desciption = 'Kích hoạt cho người dùng ' . $user->first_name;
                    $this->api_create_log($my_id, $desciption);
                }
            }

        }
        if ($request->passwordChange) {
            User::where('id', $id)->update([
                'password' => Hash::make($request->passwordChange),
                'email' => $request->usernameChange,
                'first_name' => $request->first_nameChange,
            ]);

        } else {
            $first_nameChange = preg_replace('/\s+/', ' ', $request->first_nameChange);

            User::where('id', $id)->update([
                'email' => $request->usernameChange,
                'first_name' => $first_nameChange
            ]);
        }


//        $file_path = $this->generateQRCode($id);
//        $user->qr_code = $file_path;
//        $user->save();
        /*Validate các request tiểu mục động*/
        $validate_tm = $this->validate_dynamic($request);
        if ($validate_tm->fails()) {
            return redirect()->back()->withErrors($validate_tm->errors())->withInput();
        }

        $save_path = 'images';
        $tt_honnhan_hientai = '';
        $tt_honnhan = '';

        /* Xử lý cập nhật thông tin khách hàng - tiểu mục động */
        /*Lấy ra id của tiểu mục là địa chỉ liên hệ để đẩy vào cột address bảng users*/
        $tm_diachi = TieuMucModel::select('tm_id')
            ->where('tm_keywords', 'dia-chi-lien-he')
            ->first()->tm_id;

        /*Lấy ra id của tiểu mục là thông tin liên lạc (số điện thoại) để đẩy vào cột phone trong bảng users*/
        $tm_phone = TieuMucModel::select('tm_id')
            ->where('tm_keywords', 'dien-thoai-so')
            ->first()->tm_id;

        /*Lấy id của tiểu mục là tình trạng hôn nhân để phục vụ xử lý request đưa vào bảng khachhang*/
        $tm_honnhan = TieuMucModel::select('tm_id')
            ->where('tm_keywords', 'tinh-trang-hon-nhan')
            ->first()->tm_id;
        $tm_honnhan_id = 'tm-' . $tm_honnhan;
        /*Lấy các tiểu mục có của khách hàng đã chỉ định id trong bảng khachhang*/
        $tm_kh = KhachHangModel::select('tieumuc.tm_id', 'tm_loai', 'kh_giatri')
            ->join('tieumuc', 'tieumuc.tm_id', '=', 'khachhang.tm_id')
            ->where('kh_id', $id)
            ->get();

        $honphoi = KhachHangModel::join('tieumuc', 'tieumuc.tm_id', '=', 'khachhang.tm_id')
            ->where('kh_id', $id)->where('tm_keywords', 'hon-phoi')->first();
        $honphoi_req = 'tm-' . TieuMucModel::select('tm_id')
                ->where('tm_keywords', 'hon-phoi')
                ->first()->tm_id;
        try {
            if ($tt_hientai = KhachHangModel::select('tieumuc.tm_id', 'tm_loai', 'kh_giatri')
                ->join('tieumuc', 'tieumuc.tm_id', '=', 'khachhang.tm_id')
                ->where('kh_id', $id)
                ->where('tieumuc.tm_id', 14)->first()
            ) {
                $tt_hientai = KhachHangModel::select('tieumuc.tm_id', 'tm_loai', 'kh_giatri')
                    ->join('tieumuc', 'tieumuc.tm_id', '=', 'khachhang.tm_id')
                    ->where('kh_id', $id)
                    ->where('tieumuc.tm_id', 14)->first()->kh_giatri;
                //                if ($request->get('tm-14') == 1 && $tt_hientai != 1) {
                //                    return redirect()->back()->withErrors("Không thể chuyển về trạng thái chưa kết hôn khi đã kết hôn");
                //                }
                if ($request->get('tm-14') == 4 && $tt_hientai != 4 && $tt_hientai != 1) {
                    if (KhachHangModel::select('tieumuc.tm_id', 'tm_loai', 'kh_giatri')
                        ->join('tieumuc', 'tieumuc.tm_id', '=', 'khachhang.tm_id')
                        ->where('kh_id', $id)
                        ->where('tieumuc.tm_id', 61)->first()
                    ) {

                        $hp = KhachHangModel::select('tieumuc.tm_id', 'tm_loai', 'kh_giatri')
                            ->join('tieumuc', 'tieumuc.tm_id', '=', 'khachhang.tm_id')
                            ->where('kh_id', $id)
                            ->where('tieumuc.tm_id', 61)->first()->kh_giatri;

                        KhachHangModel::where('kh_id', $hp)
                            ->where('khachhang.tm_id', 13)
                            ->update([
                                'kh_giatri' => 11
                            ]);
                    }
                }
            }
        } catch (NullPointerException $e) {
        }
        /* Câu trả lời chưa kết hôn*/
        $tl_chua_kh = KieuTieuMucModel::select('ktm_id')->where('ktm_keywords', 'chua-ket-hon')->first()->ktm_id;
        $tl_kethon = KieuTieuMucModel::select('ktm_id')->where('ktm_keywords', 'ket-hon')->first()->ktm_id;

        /*Lấy tm_id từ từng dòng mẫu tin khachhang đặt vào 1 mảng để kiểm tra các request có tồn tại trong mảng này hay không */
        $tm_file = [];
        foreach ($tm_kh as $item) {
            $kh_tm[] = $item->tm_id;
            //Kiểm tra nếu item này có tm_loai là file thì đẩy vào mảng $tm_file

            if ($item->tm_loai == 'file') {
                $tm_file[] = $item->tm_id;
            }

            if ($item->tm_id == $tm_honnhan) {
                $tt_honnhan_hientai = $item->kh_giatri;

                if ($tt_honnhan_hientai != $tl_chua_kh) {
                    if ($tt_honnhan_hientai == "") {
                        $tt_honnhan_hientai = 1;
                    }
                    $tmp_arr = KieuTieuMucModel::select('k_tieumuc')->leftjoin('kieu', 'kieu.k_id', '=',
                        'kieu_tieumuc.k_id')->where('ktm_id', $tt_honnhan_hientai)->first()->k_tieumuc;
                    $bo_tm_honnhan = explode(' ', $tmp_arr);
                    if ($honphoi != null) {
                        KhachHangModel::where('kh_id', $honphoi->kh_giatri)
                            ->whereIn('khachhang.tm_id', $bo_tm_honnhan)
                            ->delete();
                    }
                }
            }
            //lấy từng item ghép với 'tm-' để được name của request, sau đó kiểm tra với mảng ds_tm.
            //Nếu không nằm trong ds_tm thì tiến hành xóa mẫu tin đó
            $ds_tm_item = 'tm-' . $item->tm_id;
            if (!in_array($ds_tm_item, $request->ds_tm)) {
                KhachHangModel::where('kh_id', $id)->where('tm_id', $item->tm_id)->delete();
            }
        }
        if ($request->$tm_honnhan_id) {
            $bo_tm_honnhan_new = explode(' ', KieuTieuMucModel::select('k_tieumuc')->leftjoin('kieu', 'kieu.k_id', '=',
                'kieu_tieumuc.k_id')->where('ktm_id', $request->$tm_honnhan_id)->first()->k_tieumuc);
        } else {
            $bo_tm_honnhan_new = [];
        }
        $i = 0;
        /*Xử lý từng request để phục vụ chức năng cập nhật*/
        $phone = '0';
        $array = [];
        $i = 00;
        foreach ($request->ds_tm as $key => $tm) {
            //Tách name của input ra khỏi 'tm-' để lấy được id tiểu mục
            $tm_id = substr($tm, 3);

            //Kiểm tra nếu tm_id nằm trong mảng tm_id đã có của khách hàng
            //thì thực hiện lấy giá trị hiện tại dòng đó gán vào biến $giatri, sau đó xóa dòng tin đó
            //ngược lại thì thực hiện như thêm mới thông tin cho khách hàng
            if (in_array($tm_id, $kh_tm)) {
                $giatri = KhachHangModel::select('kh_giatri')->where('kh_id', $id)->where('tm_id',
                    $tm_id)->first()->kh_giatri;

                KhachHangModel::where('kh_id', $id)->where('tm_id', $tm_id)->delete();
                $kh_giatri = $request->$tm;
                //Kiểm tra nếu request đang xét là id tiểu mục kiểu file thì xử lý theo dạng file (nếu có file)
                //ngược lại sẽ lưu giá trị ảnh cũ

                if (in_array($tm_id, $tm_file)) {
                    if ($tm_id == 27) {
                    }
                    if ($request->hasFile($tm)) {
                        $file = $request->$tm;
                        foreach ($file as $value) {
                            $ten_anh = time() . $i . '.' . $value->getClientOriginalExtension();
                            $value->move('images/khachhang', $ten_anh);
                            array_push($array, $ten_anh);
                            $i++;
                        }
                        $kh_giatri = json_encode($array);
                        $array = [];
//                        $kh_giatri = json_encode($this->addImage($request, 'images/suutra', $tm));

                    } else {
                        $kh_giatri = $giatri;
                    }

                }
            } else {
                if ($request->file($tm)) {
                    $file = $request->$tm;
                    foreach ($file as $value) {
                        $ten_anh = time() . $i . '.' . $value->getClientOriginalExtension();
                        $value->move('images/khachhang', $ten_anh);
                        array_push($array, $ten_anh);
                        $i++;
                    }
                    $kh_giatri = json_encode($array);
                    $array = [];

//                    $kh_giatri = json_encode($this->addImage($request, 'images/suutra', $tm));
                    //Các request có dạng file (ảnh)


                } else {
                    //Các request có dạng giá trị thông thường (text, number, date,..)
                    $kh_giatri = $request->$tm;
                }
            }
            if ($tm_id == $tm_honnhan) {

                if ($request->$tm != $tt_honnhan_hientai) {
                    if ($request->$honphoi_req != null) {
                        KhachHangModel::where('kh_id', $request->$honphoi_req)
                            ->where('tm_id', $tm_id)
                            ->update([
                                'kh_giatri' => $kh_giatri
                            ]);
                    }
                    if ($request->$tm != $tt_honnhan_hientai && $request->$tm == 3) {
                        $this->lichsuhonnhan_create($request, $id, $request->$honphoi_req, $tm_honnhan, $request->$tm);
                    }

                    foreach ($bo_tm_honnhan_new as $value) {
                        if ($value != 61) {
                            KhachHangModel::where('kh_id', $request->$honphoi_req)
                                ->where('tm_id', $tm_id)
                                ->update([
                                    'kh_giatri' => $kh_giatri
                                ]);
                        }
                    }
                }
            }
            if ($honphoi != null) {
                if ($tm_id == $honphoi->tm_id && $request->$tm == $tl_kethon) {
                    if ($honphoi_req != '') {
                        KhachHangModel::create([
                            'kh_id' => $request->$honphoi_req,
                            'tm_id' => $honphoi->tm_id,
                            'kh_giatri' => $id
                        ]);
                        foreach ($bo_tm_honnhan_new as $value) {
                            if ($value != 61) {
                                KhachHangModel::where('kh_id', $request->$honphoi_req)
                                    ->where('tm_id', $tm_id)
                                    ->update([
                                        'kh_giatri' => $kh_giatri
                                    ]);
                            }
                        }
                    }
                }
            }
            if (in_array($tm_id, $bo_tm_honnhan_new) && $tm_id != $tm_honnhan) {

                if ($honphoi != null && $honphoi_req != '') {
                    if ($tm_id == $honphoi->tm_id) {
                        KhachHangModel::create([
                            'kh_id' => $request->$honphoi_req,
                            'tm_id' => $honphoi->tm_id,
                            'kh_giatri' => $id
                        ]);
                    } else {
                        if ($honphoi_req != '') {
                            KhachHangModel::create([
                                'kh_id' => $request->$honphoi_req,
                                'tm_id' => $tm_id,
                                'kh_giatri' => $kh_giatri
                            ]);
                        }
                    }
                }
            }
            try {
                KhachHangModel::create([
                    'kh_id' => $id,
                    'tm_id' => $tm_id,
                    'kh_giatri' => $kh_giatri
                ]);
            } catch (\Exception $e) {

                if (isset($request->$tm)) {
                    $ten_anh = time() + $i . '.' . $request->$tm->getClientOriginalExtension();
                    $request->$tm->move('images/suutra', $ten_anh);
                    $kh_giatri = json_encode($ten_anh);
//                    $kh_giatri = json_encode($this->addImage($request, 'images/suutra', $tm));
                }
                KhachHangModel::create([
                    'kh_id' => $id,
                    'tm_id' => $tm_id,
                    'kh_giatri' => $kh_giatri
                ]);
            }


            //Tạm thời lấy request của tiểu mục là địa chỉ liên hệ để gán cho cột address bảng users
            if ($tm_id == $tm_diachi) {
                $address = $kh_giatri;
            } else {
                $address = null;
            }

            //Lấy giá trị request của tiểu mục điện thoại gán cho cột phone bảng users
            if ($tm_id == $tm_phone) {
                $phone = $kh_giatri;
            }
            //Kiểm tra nếu request hiện tại là tiểu mục tình trạng hôn nhân thì kiểm tra điều kiện
            //nếu giá trị request khác giá trị hiện tại của khách hàng thì sẽ thực hiện ghi lịch sử hôn nhân
            if ($tm_id == $tm_honnhan) {
                if ($request->$tm != $giatri) {

                    if ($request->$tm_honnhan_id == $tl_kethon) {
                        $this->lichsuhonnhan_create($request, $id, $request->$honphoi_req, $tm_honnhan, $request->$tm);
                    }
                }
            }
        }

        if ($honphoi == null) {
            if ($honphoi_req != '') {
                if ($request->$honphoi_req) {
                    KhachHangModel::create([
                        'kh_id' => $request->$honphoi_req,
                        'tm_id' => 61,
                        'kh_giatri' => $id
                    ]);

                    foreach ($bo_tm_honnhan_new as $value) {

                        if ($value != 61) {
                            KhachHangModel::where('kh_id', $request->$honphoi_req)
                                ->where('tm_id', $value)
                                ->update([
                                    'kh_giatri' => $kh_giatri
                                ]);
                        }
                    }
                }
            }
        }
        ($request->get('tm-1086')) ? $name = $request->get('tm-1086') : $name = $request->get('tm-1') . " " . $request->get('tm-2') . " " . $request->get('tm-6');
        $user = Sentinel::findById($id);
        if ($request->get('old-cmnd') != null && $request->get('old-cmnd') != $request->get('tm-6')) {
            $name = $name . " (" . $request->get('old-cmnd') . ")";
            $name = preg_replace('/\s+/', ' ', $name);


            Sentinel::update($user, [
                'first_name' => $name,
                'phone' => $phone,
                'address' => $address,
            ]);

        } else {

            Sentinel::update($user, [
                'phone' => $phone,
                'address' => $address,
            ]);

        }

        /*Ghi log*/
        $user_exec = Sentinel::getUser()->id;
        $description = "Cập nhật thông tin đương sự " . Sentinel::findById($id)->first_name;
        $this->api_create_log($user_exec, $description);
        return Redirect::route('showKhachHang', ['id' => $id])->with('success',
            'Cập nhật thông tin đương sự thành công!');
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
                return ['status' => 'error', 'message' => $error];
            }
            if (KhachHangModel::where('kh_id', $id)->where('tm_id',
                    61)->first() == null && LyLichKhachHangModel::where('kh_id', $id)->first() == null) {
                User::find($id)->delete();
                KhachHangModel::where('kh_id', $id)->delete();
                $user_exec = Sentinel::getUser()->id;
                $description = "Xóa tài khoản và thông tin đương sự " . $user->first_name;
                $this->api_create_log($user_exec, $description);
//                $url="http://115.75.98.239:5555/api/FingerprintFeature/delete/".$id;
//                $res=Curl::to($url)
//                    ->post();
                return Redirect::route('indexKhachHang')->with('success', 'Xóa đương sự thành công!');
            } else {
                if (RoleUsersModel::where('user_id', Sentinel::check()->id)->first()->role_id == 11
                    || RoleUsersModel::where('user_id', Sentinel::check()->id)->first()->role_id == 10) {
                    User::find($id)->delete();
                    KhachHangModel::where('kh_id', $id)->delete();

                    $user_exec = Sentinel::getUser()->id;
                    $description = "Xóa tài khoản và thông tin đương sự " . $user->first_name;
                    $this->api_create_log($user_exec, $description);
                    return Redirect::route('indexKhachHang')->with('success', 'Xóa đương sự thành công!');
                } else {
                    return Redirect::route('indexKhachHang')->with('error', 'Không có quyền xóa đương sự này!');
                }
            }
            // Delete the user
            //to allow soft deleted, we are performing query on users model instead of Sentinel model

        } catch (UserNotFoundException $e) {
            return ['status' => 'error', 'message' => 'Không tìm thấy người dùng!'];
        }
    }

    /**
     * Hàm trả về danh sách tiểu mục thuộc 1 câu trả lời cho AJAX
     * @param Request $request
     * @return array
     */
    public function get_tieumuc_select(Request $request)
    {
        $data = $this->get_kieutm($request);
        $tm_rmv = [];
        $ktm = KieuTieuMucModel::join('kieu', 'kieu.k_id', '=', 'kieu_tieumuc.k_id')
            ->where('ktm_id', $request->current_val)
            ->first();
        if ($ktm) {
            $ktm_arr = explode(' ', $ktm->k_tieumuc);
            $tm_rmv = TieuMucModel::select('tm_keywords')->whereIn('tm_id', $ktm_arr)->get();
        }
        return ['status' => 'success', 'data' => ['list_tm' => $data, 'tm_rmv' => $tm_rmv]];
    }

    /**
     * Hàm trả về các câu trả lời cho 1 tiểu mục cho AJAX
     * @param Request $request
     * @return array
     */
    public function get_tieumuc_options(Request $request)
    {
        $data = KieuTieuMucModel::select('ktm_id', 'ktm_traloi')
            ->where('tm_id', $request->tm_id)
            ->get();
        return ['status' => 'success', 'data' => $data];
    }

    function get_history($kh_id)
    {
        $vp = NhanVienModel::find(Sentinel::check()->id)->nv_vanphong;
        if (Sentinel::inRole('admin')) {
            $data = KhachHangGDModel::select('hopdong.*', 'vb_nhan', 'first_name', 'cn_ten')->leftjoin('hopdong',
                'hopdong.id', '=', 'hd_id')
                ->leftjoin('vanban', 'vb_id', '=', 'id_vb')
                ->leftjoin('users', 'users.id', '=', 'hopdong.ccv_id')
                ->leftjoin('chinhanh', 'cn_id', '=', 'hopdong.vanphong')
                ->whereNotNull('ngayky')
                ->where('kh_id', $kh_id)->paginate(10);
            $count = KhachHangGDModel::leftjoin('hopdong', 'hopdong.id', '=', 'hd_id')
                ->where('kh_id', $kh_id)->whereNotNull('ngayky')
                ->count();
        } else {
            $data = KhachHangGDModel::select('hopdong.*', 'vb_nhan', 'first_name', 'cn_ten')->leftjoin('hopdong',
                'hopdong.id', '=', 'hd_id')
                ->leftjoin('vanban', 'vb_id', '=', 'id_vb')
                ->leftjoin('users', 'users.id', '=', 'hopdong.ccv_id')
                ->leftjoin('chinhanh', 'cn_id', '=', 'hopdong.vanphong')
                ->where('hopdong.vanphong', $vp)
                ->whereNotNull('ngayky')
                ->where('kh_id', $kh_id)->paginate(10);
            $count = KhachHangGDModel::leftjoin('hopdong', 'hopdong.id', '=', 'hd_id')
                ->where('kh_id', $kh_id)
                ->whereNotNull('ngayky')
                ->where('hopdong.vanphong', $vp)
                ->count();
        }

        foreach ($data as $item) {
            $name = (User::find($item->nvnv_id)) ? User::find($item->nvnv_id)->first_name : "";
            $item['nvnv_id'] = ($item->nvnv_id != 0) ? $name : "";
        }

        $ten = User::find($kh_id)->first_name;
        return view('admin.khachhang.history', compact('data', 'ten', 'count'));
    }

    /**
     * Hàm trả về danh sách tiểu mục form edit cho AJAX
     * @param Request $request
     * @return array
     */
    public function get_tieumuc_edit(Request $request)
    {
        $data = $this->get_tm_edit($request);
        //        $ktm_arr = explode(' ', KieuTieuMucModel::join('kieu', 'kieu.k_id', '=', 'kieu_tieumuc.k_id')
        //            ->where('ktm_id', $request->ktm_id)
        //            ->first());
        //        $ktm_arr = array_sum($ktm_arr) > 0 ? $ktm_arr->k_tieumuc : [];
        $lists_rmv = KieuTieuMucModel::join('kieu', 'kieu.k_id', '=', 'kieu_tieumuc.k_id')
            ->where('ktm_id', $request->current_val)
            ->first();
        $ktm_rmv_arr = explode(' ', $lists_rmv == null ? '' : $lists_rmv->k_tieumuc);

        $tm_rmv = TieuMucModel::select('tm_keywords')->whereIn('tm_id', $ktm_rmv_arr)->get();
        return ['status' => 'success', 'data' => ['list_tm' => $data, 'tm_rmv' => $tm_rmv]];
    }

    /**
     * Lấy về danh sách kiểu khách hàng phục vụ tạo khách hàng cho AJAX
     * @param Request $request
     * @return array
     */
    public function get_kieu(Request $request)
    {
        $id_kieu = KieuModel::select('k_id')->where('k_keywords', '=', $request->keyword)->first()->k_id;
        $kieu = KieuModel::select('k_nhan as text', 'k_parent', 'k_id')
            ->where('k_trangthai', 1)
            ->get();
        if ($request->k_method == 'normal') {
            return ['status' => 'success', 'data' => $kieu];
        } else {
            $data = $this->ordered_menu($kieu, $id_kieu);
            return ['status' => 'success', 'data' => $data];
        }
    }

    /**
     * Hàm Validate trùng số CMND hoặc số giấy tờ tùy thân
     * @param Request $request
     * @return array
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
    public function validate_dynamic(Request $request)
    {
        $canhan = KieuModel::select('k_id')->where('k_keywords', 'ca-nhan')->first()->k_id;
        if ($request->k_id) {
            $id = $request->k_id;
        } else {
            $id = $canhan;
        }
        $tm_arr = $this->get_tieumuc_kieu($request, $id);
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
            'pic.*' => 'image',
        ], [
            'username.required' => 'Vui lòng nhập tên đăng nhập!',
            'username.unique' => 'Tên đăng nhập đã tồn tại!',
            'first_name.required' => 'Vui lòng nhập nhãn khách hàng!',
            'first_name.unique' => 'Nhãn khách hàng đã tồn tại!',
            'pic.image' => 'Tệp tin không phải hình ảnh!',
        ]);

        return $validated;
    }

    /**
     * Hàm validate các request tĩnh (avatar, tài khoản, nhãn) cho cập nhật khách hàng
     * @param Request $request
     * @return mixed
     */
    public function validate_static_update(Request $request, $id)
    {
        $validated = Validator::make($request->all(), [
            'username' => [
                'required',
                Rule::unique('users', 'email')->ignore($id)
            ],
            'first_name' => [
                'required',
                Rule::unique('users', 'first_name')->ignore($id)
            ],
            'pic' => 'image'
        ], [
            'username.required' => 'Vui lòng nhập tên đăng nhập!',
            'username.unique' => 'Tên đăng nhập đã tồn tại!',
            'first_name.required' => 'Vui lòng nhập nhãn khách hàng!',
            'first_name.unique' => 'Nhãn khách hàng đã tồn tại!',
            'pic.image' => 'Tệp tin không phải hình ảnh!',
        ]);

        return $validated;
    }

    public function change_type_kh($idKH, $k_newID)
    {
        $user = User::find($idKH)->update(['k_id' => $k_newID]);
        $kieu_old = KieuModel::find($user->k_id)->k_nhan;
        $kieu_new = KieuModel::find($k_newID)->k_nhan;
        /*Ghi log*/
        $user_exec = Sentinel::getUser()->id;
        $description = "Đổi kiểu khách hàng " . Sentinel::findById($idKH)->first_name . ' từ ' . $kieu_old . ' sang ' . $kieu_new;
        $this->api_create_log($user_exec, $description);
        return Redirect::route('indexKhachHang')->with('success', 'Đổi kiểu khách hàng thành công!');
    }

    //    public function get_honphoi_tm($k_id){
    //        $tm_ttkh = TieuMucModel::where('tm_keywords', 'tinh-trang-hon-nhan')->first()->tm_id;
    //        $ds_tm = KieuModel::find($k_id)->k_tieumuc;
    //        $tm_arr = explode(' ', $ds_tm);
    //        $data = TieuMucModel::select('tieumuc.tm_id', 'tm_nhan', 'tm_loai', 'tm_keywords', 'tm_batbuoc')
    //                ->leftjoin('tieumuc_sapxep', 'tieumuc_sapxep.tm_id', '=', 'tieumuc.tm_id')
    //                ->whereIn('tieumuc.tm_id', $tm_arr)
    //                ->where('tieumuc.tm_id', '<>',$tm_ttkh)
    //                ->where('k_id', $k_id)
    //                ->orderBy('tm_sort', 'asc')
    //                ->get();
    //        return ['status' => 'success', 'data' => $data];
    //    }

    public function find_khachhang_select2(Request $request)
    {
        $term = trim($request->q);
        if (empty($term)) {
            return \Response::json([]);
        }

        $tm_honnhan = TieuMucModel::where('tm_keywords', 'tinh-trang-hon-nhan')->first()->tm_id;
        $tm_kethon = KieuTieuMucModel::where('ktm_keywords', 'ket-hon')->first()->ktm_id;
        $role_kh = $role_kh = Sentinel::findRoleBySlug('khach-hang');
        $kh_list = $role_kh->users()->select('id', 'first_name', 'phone', 'address', 'k_id')
            ->join('khachhang', 'kh_id', '=', 'id')
            ->where('tm_id', $tm_honnhan)
            ->where('kh_giatri', '<>', $tm_kethon)
            ->where('first_name', 'LIKE', '%' . $term . '%')->orderBy('users.created_at', 'desc')->limit(5)->get();
        $khachhang = [];
        foreach ($kh_list as $kh) {
            $khachhang[] = ['id' => $kh->id, 'text' => $kh->first_name];
        }

        return \Response::json($khachhang);
    }

    public function find_khachhang_select2All(Request $request)
    {
        $term = trim($request->q);
        if (empty($term)) {
            return \Response::json([]);
        }

        $role_kh = $role_kh = Sentinel::findRoleBySlug('khach-hang');
        $kh_list = $role_kh->users()->select('id', 'first_name', 'phone', 'address', 'k_id')
            ->where('first_name', 'LIKE', '%' . $term . '%')->distinct('id')->orderBy('users.id',
                'desc')->limit(5)->get();
        $khachhang = [];
        foreach ($kh_list as $kh) {
            $khachhang[] = ['id' => $kh->id, 'text' => $kh->first_name];
        }
        return \Response::json($khachhang);
    }

    /**
     * ghi log lại cho bảng khách hàng
     */

    public static function logCreate($id, $note, $user_id)
    {

        $khachhang = KhachHangModel::select('tm_nhan', 'tm_loai', 'tm_keywords', 'kh_giatri')
            ->where('kh_id', $id)
            ->join('tieumuc', 'tieumuc.tm_id', '=', 'khachhang.tm_id')
            ->get();

        $hon_phoi_id = KhachHangModel::join('tieumuc', 'tieumuc.tm_id', '=', 'khachhang.tm_id')
            ->where('tm_keywords', 'hon-phoi')
            ->where('kh_id', $id)
            ->first();

        if ($hon_phoi_id) {
            $honphoi = KhachHangModel::select('tm_nhan', 'tm_loai', 'tm_keywords', 'kh_giatri', 'khachhang.tm_id')
                ->where('kh_id', $hon_phoi_id->kh_giatri)
                ->join('tieumuc', 'tieumuc.tm_id', '=', 'khachhang.tm_id')
                ->orderBy('khachhang.created_at', 'asc')
                ->get();
            foreach ($honphoi as $hp) {

                if ($hp->tm_loai == 'select' && $hp->tm_keywords != 'hon-phoi') {
                    $hp->kh_giatri = KieuTieuMucModel::find($hp->kh_giatri)['ktm_traloi'];
                }
                if ($hp->tm_loai == 'file' && $hp->tm_keywords != 'hon-phoi') {
                    $hp->kh_giatri = KieuTieuMucModel::find($hp->tm_id)['ktm_traloi'];
                }
                if ($hp->tm_loai == 'select' && $hp->tm_keywords == 'hon-phoi') {
                    $hp->kh_giatri = User::find($hp->kh_giatri)['first_name'];
                }
            }
        } else {
            $honphoi = [];
        }

        foreach ($khachhang as $kh) {
            if ($kh->tm_loai == 'select' && $kh->tm_keywords != 'hon-phoi') {
                $kh->kh_giatri = KieuTieuMucModel::find($kh->kh_giatri)['ktm_traloi'];
            }
            if ($kh->tm_loai == 'select' && $kh->tm_keywords == 'hon-phoi') {
                $kh->kh_giatri = User::find($kh->kh_giatri)['first_name'];
            }
        }
        $basic_info = User::find($id);
        $honnhan_info = LichSuHonNhanModel::where('ds1_id', $id)->first();
        $result = array('basic' => $basic_info, 'tieu_muc' => $khachhang, 'hon_nhan' => $honnhan_info, 'note' => $note);
        $log = KhachHangLog::create([
            'kh_id' => $id,
            'log_content' => json_encode($result),
            'creator_id' => Sentinel::check()->id ?? 1,
        ]);
        return $log;
    }

    /**
     * Generate QR
     * * Input : ID
     * * Output: image directory
     */
//    public static function generateQRCode($id)
//    {
//        $image = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
//            ->size(200)
//            ->generate($id);
//        $output_file = "QRCode/khachhang" . $id . ".png";
//        Storage::disk('public')->put($output_file, $image);
//        return $output_file;
//    }

    public static function aa()
    {
        return 'a';
    }

    /**
     * Returns an array of images
     * * Input : khach_hang_id
     * * Output: Array with sub-arrays contains images for each hop_dong_id of that khach_hang_id
     */
    public static function imagesArray($khach_hang_id)
    {
        $time = Date('h:i:s');
        // create an empty array for outputing
        $result = array();
        // if $khach_hang_id is null, return an empty array
        if (!$khach_hang_id) {
            return [];
        }
        $ho_so = DB::table('khachhang_gd')->where('kh_id', $khach_hang_id)->join('hopdong', 'hopdong.id', '=',
            'khachhang_gd.hd_id')
            ->where('ho_so_id', '!=', null)->get()->map(function ($hs) {
                return $hs->id;
            })->toArray();
        // retrieve rows of images of this customer
        $images_rows = DB::table('ho_so_images')->whereIn('ho_so_id', array_unique($ho_so))->get();
        // convert results to sub-arrays and save it to the result list, if $images_rows = null, skip it
        foreach ($images_rows as $row) {
            // create an empty array for each hop_dong_id
            $temp_arr = array();
            // save each images
            $temp_arr['img1'] = $row->img1 ? AppController::convert_nextcloud($row->img1,
                '/ho-so/anh-that/') : AppController::convert_nextcloud("khach_hang_empty.png", '/ho-so/anh-that/');
            $temp_arr['img2'] = $row->img2 ? AppController::convert_nextcloud($row->img2,
                '/ho-so/anh-that/') : AppController::convert_nextcloud("khach_hang_empty.png", '/ho-so/anh-that/');
            $temp_arr['img3'] = $row->img3 ? AppController::convert_nextcloud($row->img3,
                '/ho-so/anh-that/') : AppController::convert_nextcloud("khach_hang_empty.png", '/ho-so/anh-that/');
            // $temp_arr['img1'] = $row->img1;
            // $temp_arr['img2'] = $row->img2;
            // $temp_arr['img3'] = $row->img3;
            $temp_arr['date'] = Carbon::parse($row->created_at)->format('d/m/Y');
            // put them in the result array with the key is hop_dong_id
            $result[$row->ho_so_id] = $temp_arr;
        }
        // return the result
        return $result;
    }

    public function printPDF(Request $request)
    {
        $khach_hang_id = $request->khach_hang_id;
        $ho_so_id = $request->ho_so_id;
        if (!($khach_hang_id && $ho_so_id)) {
            return false;
        }
        $images = DB::table('ho_so_images')->where('ho_so_id', $ho_so_id)->first();
        $qr_code = User::find($khach_hang_id)->qr_code;
        $data = [
            'img1' => $images->img1 ? asset('storage/' . $images->img1) : asset('images/khach_hang_empty.png'),
            'img2' => $images->img2 ? asset('storage/' . $images->img2) : asset('images/khach_hang_empty.png'),
            'img3' => $images->img3 ? asset('storage/' . $images->img3) : asset('images/khach_hang_empty.png'),
            'date' => Carbon::parse($images->created_at)->format('d/m/Y'),
            'ho_so_id' => $ho_so_id,
            'khach_hang_id' => $khach_hang_id,
            'qr_code' => asset('storage/' . $qr_code),
        ];
        $pdf = PDF::loadView('admin.khachhang.pdf', $data);
        return $pdf->download('anh_that_' . $khach_hang_id . '_' . $ho_so_id . '.pdf');
    }
    // ------------- Update for fingerprint api -------------------

    /**
     * update fingerprints for api
     *
     * @param Request $request
     * @return void
     */
    public static function updateFingerprint(Request $request)
    {
        // retrieve user id
        $user_id = $request->user_id;
        $user = User::find($user_id);
        // check if user exists
        if (!$user) {
            return json_encode([
                'code' => '400',
                'status' => false,
                'data' => ['user_id' => $user_id],
                'message' => 'Không tìm thấy người dùng'
            ]);
        }
        // default type (finger type)
        $type = $request->type ?? '0';
        switch ($type) {
            case '1':
                $index = 'right_1';
                break;
            case '2':
                $index = 'right_2';
                break;
            case '3':
                $index = 'right_3';
                break;
            case '4':
                $index = 'right_4';
                break;
            case '5':
                $index = 'right_5';
                break;
            case '6':
                $index = 'left_1';
                break;
            case '7':
                $index = 'left_2';
                break;
            case '8':
                $index = 'left_3';
                break;
            case '9':
                $index = 'left_4';
                break;
            case '10':
                $index = 'left_5';
                break;
            default:
                return json_encode([
                    'code' => '400',
                    'status' => false,
                    'data' => ['type' => $type],
                    'message' => 'Không tìm thấy loại vân tay'
                ]);
        }

        // check which hand
        if ($type > 5) {
            $fingerprint_id = TieuMucModel::where('tm_keywords', 'van-tay-trai')->first()->tm_id;
        } else {
            $fingerprint_id = TieuMucModel::where('tm_keywords', 'van-tay-phai')->first()->tm_id;
        }


        $data = KhachHangModel::where([['kh_id', $user_id], ['tm_id', $fingerprint_id]])->first();
        $fingerprints = [];
        // decode data to an array
        if ($data) {
            $fingerprints = $data->kh_giatri ? json_decode($data->kh_giatri) : [];
        } else {
            $data = new KhachHangModel;
            $data->kh_id = $user_id;
            $data->tm_id = $fingerprint_id;
            $data->kh_giatri = json_encode([]);
            $data->save();
        }

        // image data of a fingerprint
        $image = $request->image;
        if ($image) {
            try {
                $extension = explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
            } catch (\Exception $e) {
                $extension = 'png';
            }
            $image = str_replace('data:image/' . $extension . ';base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $ten_anh = $user->id . '_' . $index . '.' . $extension;
            $picture = base64_decode($image);

            file_put_contents(public_path() . '/images/khachhang/' . $ten_anh, $picture);
            if (is_array($fingerprints)) {
                $fingerprints[$index] = $ten_anh;
            } else {
                $fingerprints->{$index} = $ten_anh;
            }
            KhachHangModel::where([
                ['kh_id', $user_id],
                ['tm_id', $fingerprint_id]
            ])->update(['kh_giatri' => json_encode($fingerprints)]);
        } else {
            return json_encode([
                'code' => '400',
                'status' => false,
                'data' => ['type' => $type],
                'message' => 'Không tìm thấy dữ liệu ảnh'
            ]);
        }
        return json_encode([
            'code' => '200',
            'status' => true,
            'data' => ['user_id' => $user_id, 'type' => $index, 'data' => $data],
            'message' => 'Cập nhật thành công'
        ]);
    }

    //------------- End update for fingerprint api -------------------
}

?>
