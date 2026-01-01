<?php

namespace App\Http\Controllers;

use App\Models\ChiNhanhModel;
use App\Models\KhachHangModel;
use App\Models\LyLichKhachHangModel;
use App\Models\NhanVienModel;
use App\Models\RoleUsersModel;
use App\Models\User;
use Carbon\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LyLichKhachHangController extends Controller
{
    use users;
    use Nhanvien;
    use ImageHandling;

    public function __construct()
    {
        $this->middleware('user');
    }

    /**
     * Hiển thị danh sách các lý lịch của khách hàng
     *
     * @param $idKH
     * @return \Illuminate\Http\Response
     */
    public function index($idKH)
    {
        $khachhang = User::select('id', 'first_name')->find($idKH);
        $lylich = LyLichKhachHangModel::where('kh_id', $idKH)
            ->where('deleted_at', null)
            ->paginate(50);
        return view('admin.khachhang.lylich.index', compact('lylich', 'khachhang'));
    }

    /**
     * Hiển thị form để điền thông tin lý lịch mới cho khách hàng
     *
     * @param $idKH
     * @return \Illuminate\Http\Response
     */
    public function create($idKH)
    {
        if (Sentinel::check()->isTruongVP() || Sentinel::inRole('admin')|| Sentinel::check()->isCCV()|| Sentinel::check()->isLuuTru()||Sentinel::check()->isMod()) {
            //Lấy danh sách công chứng viên chuyển danh sách thành dạng collection
            $ccv_arr = $this->get_ccv();
            $ccv = collect($ccv_arr)->pluck('first_name', 'id');

            //Lấy danh sách chuyên viên chuyển danh sách thành dạng collection
            $cv_arr = $this->get_cv();
            $cv = collect($cv_arr)->pluck('first_name', 'id');
            return view('admin.khachhang.lylich.create', compact('idKH', 'ccv', 'cv'));
        } else {
            return Redirect::route('indexLyLich', ['id' => $idKH])->with('error', 'Bạn không có quyền thực hiện thao tác này!');

        }
    }

    /**
     * Lưu một lý lịch mới cho khách hàng được chỉ định
     *
     * @param \Illuminate\Http\Request $request
     * @param $idKH
     * @return mixed
     */
    public function store(Request $request, $idKH)
    {
        //Validate thông tin các request
        $validated = $this->validator_create_lylich($request->all());
        if ($validated->fails()) {
            return redirect()->back()->withErrors($validated->errors())->withInput();
        }

        $sohoso = $request->sohoso;
        $so_cc = $request->so_cc;
        $so_vaoso = $request->so_vaoso;
        $mota = $request->mota;
        //Định dạng lại format ngày
        //Tìm các ký tự "/" chuyển thành "-" rồi chuyển thành chuỗi ngày có định dạng Y-m-d
        $date_raw = new Carbon(str_replace('/', '-', $request->ngayky));
        $ngayky = $date_raw->toDateString();
        $tinhtrang = $request->tinhtrang;
        $ccv_id = $request->ccv_id;
        $nhanviennv_id = $request->nhanviennv_id;
        $lylich_hinhanh = '';
        $path = "images/lylich";
        if ($request->hasFile('lylich_hinhanh')) {
            $lylich_hinhanh = json_encode($this->addImage($request, $path, 'lylich_hinhanh'));
        }

        $id_lylich=LyLichKhachHangModel::create([
            'sohoso' => $sohoso,
            'so_cc' => $so_cc,
            'so_vaoso' => $so_vaoso,
            'mota' => $mota,
            'ngayky' => $ngayky,
            'tinhtrang' => $tinhtrang,
            'ccv_id' => $ccv_id,
            'nhanviennv_id' => $nhanviennv_id,
            'kh_id' => $idKH,
            'lylich_loai' => 1,
            'lylich_hinhanh' => $lylich_hinhanh
        ]);

        //get value id hôn phối
        $kh = KhachHangModel::leftjoin('tieumuc', 'tieumuc.tm_id', '=', 'khachhang.tm_id')
            ->where('kh_id', '=', $idKH)
            ->where('tm_keywords', '=', 'hon-phoi')->first();
        if (isset($kh->kh_giatri)) {
            $id_hon_phoi = $kh->kh_giatri;
            $id_lylich_hp=LyLichKhachHangModel::create([
                'sohoso' => $sohoso,
                'so_cc' => $so_cc,
                'so_vaoso' => $so_vaoso,
                'mota' => $mota,
                'ngayky' => $ngayky,
                'tinhtrang' => $tinhtrang,
                'ccv_id' => $ccv_id,
                'nhanviennv_id' => $nhanviennv_id,
                'kh_id' => $id_hon_phoi,
                'lylich_loai' => 1,
                'lylich_hinhanh' => $lylich_hinhanh,
                'link_id'=>$id_lylich->id
            ]);
            LyLichKhachHangModel::find($id_lylich->id)->update([
                'link_id'=>$id_lylich_hp->id
            ]);
            $khachhang = Sentinel::findUserById($id_hon_phoi)->first_name;
            $user_exec = Sentinel::getUser()->id;
            $description = "Thêm lịch sử số " . $sohoso . ' cho khách hàng ' . $khachhang;
            $this->api_create_log($user_exec, $description);
        }
        //Ghi log
        $khachhang = Sentinel::findUserById($idKH)->first_name;
        $user_exec = Sentinel::getUser()->id;
        $description = "Thêm lịch sử số " . $sohoso . ' cho khách hàng ' . $khachhang;
        $this->api_create_log($user_exec, $description);

        return Redirect::route('indexLyLich', ['id' => $idKH])->with('success', 'Thêm lý lịch thành công!');
    }


    /**
     * Hiện form chỉnh sửa thông tin của một lý lịch
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (RoleUsersModel::where('user_id', Sentinel::check()->id)->first()->role_id == 11 || RoleUsersModel::where('user_id', Sentinel::check()->id)->first()->role_id == 10||Sentinel::check()->isMod()) {

            $lylich = LyLichKhachHangModel::find($id);
            if (!User::find(Sentinel::check()->id)->isAdmin()) {
                $creator_vp = NhanVienModel::find($lylich->ccv_id);
                $current_vp = NhanVienModel::find(Sentinel::check()->id)->nv_vanphong;
                $name=ChiNhanhModel::find($current_vp)->cn_ten;
                if ($current_vp != $creator_vp)
                    return redirect()->back()->with('error', 'Vui lòng liên hệ văn phòng '.$name.' ngăng chặn để chỉnh sửa!');


            }
            //Định dạng lại ngày từ Y-m-d thành d-m-Y và chuyển thành chuỗi d/m/Y
            $images = [];
            if ($lylich->lylich_hinhanh) {
                $images = json_decode($lylich->lylich_hinhanh);
            }

            $idKH = $lylich->kh_id;
            //Lấy danh sách công chứng viên chuyển danh sách thành dạng collection
            $ccv_arr = $this->get_ccv();
            $ccv = collect($ccv_arr)->pluck('first_name', 'id');

            //Lấy danh sách chuyên viên chuyển danh sách thành dạng collection
            $cv_arr = $this->get_cv();
            $cv = collect($cv_arr)->pluck('first_name', 'id');

            return view('admin.khachhang.lylich.edit', compact('lylich', 'images', 'idKH', 'ccv', 'cv'));
        } else {
            return redirect()->back()->with('error', 'Bạn không có quyền thực hiện thao tác này!');

        }
    }

    /**
     * Cập nhật một lý lịch của khách hàng
     *
     * @param \Illuminate\Http\Request $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //Validate thông tin các request
        $validated = $this->validator_update_lylich($request->all(), $id);
        if ($validated->fails()) {
            return redirect()->back()->withErrors($validated->errors())->withInput();
        }

        $lylich_hinhanh = LyLichKhachHangModel::find($id)->lylich_hinhanh;

        $sohoso = $request->sohoso;
        $so_cc = $request->so_cc;
        $so_vaoso = $request->so_vaoso;
        $mota = $request->mota;
        $tinhtrang = $request->tinhtrang;
        $ccv_id = $request->ccv_id;
        $nhanviennv_id = $request->nhanviennv_id;
        $path = "images/lylich";
        if ($request->hasFile('lylich_hinhanh')) {
            $lylich_hinhanh = json_encode($this->addImage($request, $path, 'lylich_hinhanh'));
        }

        LyLichKhachHangModel::where('id', $id)
            ->update([
                'sohoso' => $sohoso,
                'so_cc' => $so_cc,
                'so_vaoso' => $so_vaoso,
                'mota' => $mota,
                'tinhtrang' => $tinhtrang,
                'ccv_id' => $ccv_id,
                'nhanviennv_id' => $nhanviennv_id,
                'lylich_loai' => 1,
                'lylich_hinhanh' => $lylich_hinhanh
            ]);
        $link_id=LyLichKhachHangModel::where('id', $id)->first()->link_id;
        if($link_id){
            LyLichKhachHangModel::find($link_id) ->update([
                'sohoso' => $sohoso,
                'so_cc' => $so_cc,
                'so_vaoso' => $so_vaoso,
                'mota' => $mota,
                'tinhtrang' => $tinhtrang,
                'ccv_id' => $ccv_id,
                'nhanviennv_id' => $nhanviennv_id,
                'lylich_loai' => 1,
                'lylich_hinhanh' => $lylich_hinhanh
            ]);
        }
        //Ghi log
        $kh_id = LyLichKhachHangModel::join('users', 'users.id', '=', 'kh_id')
            ->where('lylich_khachhang.id', '=', $id)
            ->first()->kh_id;
        //get id honphoi

        $user_exec = Sentinel::getUser()->id;
        $description = 'Cập nhật lịch sử có ID ' . $id . ' của khách hàng' . $kh_id;
        $this->api_create_log($user_exec, $description);

        return Redirect::route('indexLyLich', ['idKH' => $kh_id])->with('success', 'Cập nhật lý lịch thành công!');
    }

    /**
     * Xóa lịch sử khách hàng, sử dụng SoftDelete
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if (RoleUsersModel::where('user_id', Sentinel::check()->id)->first()->role_id == 11 || RoleUsersModel::where('user_id', Sentinel::check()->id)->first()->role_id == 10||Sentinel::check()->isMod()) {
            // Tìm lý lịch và kiểm tra có tồn tại lý lịch đó hay không
            $lylich = LyLichKhachHangModel::find($request->id);
            if (!$lylich) {
                return redirect()->back()->with('error', 'Lịch sử không tồn tại!');
            }
            $lylich->delete();
            // Ghi log
            $user_exec = Sentinel::getUser()->id;
            $description = 'Xóa lịch sử có ID' . $request->id . 'của khách hàng' . $lylich->kh_id;
            $this->api_create_log($user_exec, $description);
            return redirect()->back()->with('success', 'Xóa lịch sử thành công!');

        } else {
            return redirect()->back()->with('error', 'Bạn không có quyền thực hiện thao tác này!');

        }
    }

    /**
     * Validate input sohoso gửi lên từ form create/edit bằng AJAX
     * @param Request $request
     * @return array
     */
    public function validate_sohs(Request $request)
    {
        $message = [
            'sohoso.unique' => 'Số HS/CV đã tồn tại!'
        ];
        if ($request->type == 'create') {
            $check = Validator::make($request->all(), [
                'sohoso' => 'unique:lylich_khachhang'
            ], $message);
        } else {
            $check = Validator::make($request->all(), [
                'sohoso' => [
                    Rule::unique('lylich_khachhang')->ignore($request->id)
                ]
            ], $message);
        }
        if ($check->fails()) {
            return ['status' => 'error', 'message' => $check->errors()->first()];
        }

        return ['status' => 'success', 'message' => 'Passed'];
    }


    /**
     * Validate input so_vaoso gửi lên từ form create/edit bằng AJAX
     * @param Request $request
     * @return array
     */
    public function validate_sovaoso(Request $request)
    {
        $message = [
            'so_vaoso.unique' => 'Số vào sổ đã tồn tại!'
        ];
        if ($request->type == 'create') {
            $check = Validator::make($request->all(), [
                'so_vaoso' => 'unique:lylich_khachhang'
            ], $message);
        } else {
            $check = Validator::make($request->all(), [
                'so_vaoso' => [
                    Rule::unique('lylich_khachhang')->ignore($request->id)
                ]
            ], $message);
        }
        if ($check->fails()) {
            return ['status' => 'error', 'message' => $check->errors()->first()];
        }

        return ['status' => 'success', 'message' => 'Passed'];
    }

    public function validator_create_lylich(array $request)
    {
        $message = $this->message_validator();
        $validator = Validator::make($request, [
            'ngayky' => 'required',
        ], $message);
        return $validator;
    }

    public function validator_update_lylich(array $request, $id)
    {
        $message = $this->message_validator();
        $ignore = [
            'required',
            Rule::unique('lylich_khachhang')->ignore($id)
        ];

        $validator = Validator::make($request, [

            'lylich_hinhanh.*' => 'image'
        ], $message);

        return $validator;
    }

    /**
     * Các thông điệp validate
     * @return array
     */
    public function message_validator(): array
    {
        $message = [
            'sohoso.required' => 'Vui lòng không để trống số HS/CV!',
            'ngayky.required' => 'Vui lòng không để trống ngày ký!',
            'so_vaoso.required' => 'Vui lòng không để trống số vào sổ!',
            'mota.required' => 'Vui lòng không để trống nhãn!',
            'sohoso.unique' => 'Số HS/SV đã tồn tại!',
            'so_vaoso.unique' => 'Số vào sổ đã tồn tại!',
            'lylich_hinhanh.image' => 'Vui lòng chọn đúng định dạng ảnh!'
        ];
        return $message;
    }

    /**
     * Trả về AJAX danh sách hình ảnh của lý lịch được chỉ định
     * @param Request $request
     * @return array
     */
    public function get_image(Request $request)
    {
        $id = $request->id;
        $data = json_decode(LyLichKhachHangModel::find($id)->lylich_hinhanh);
        return ['status' => 'success', 'data' => $data];
    }

}
