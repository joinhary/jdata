<?php

namespace App\Http\Controllers;

use Sentinel;
use App\Models\User;
use App\Models\KieuModel;
use App\Models\TaiSanLog;
use App\Models\TaiSanModel;
use App\Models\TieuMucModel;
use Illuminate\Http\Request;
use App\Models\NhanVienModel;
use App\Models\RoleUsersModel;
use App\Models\KieuTieuMucModel;
use App\Models\TaiSanGiaTriModel;
use App\Models\TaiSanLichSuModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class TaiSanController extends Controller
{
    public function index(Request $request)
    {
        $taisan = TaiSanModel::join('kieu', 'kieu.k_id', '=', 'taisan.ts_kieu')
            ->where('ts_trangthai', '=', 1)
            ->orderBy('taisan.created_at', 'desc')
            ->select([
                'taisan.ts_id',
                'taisan.ts_nhan',
                'taisan.id_vp',
                'taisan.id_ccv',
                'kieu.k_nhan as k_nhan',
                'kieu.k_parent as k_parent',
            ])->orderby('ts_id', 'desc');
        $tong = $taisan->get();
        if ($request->has('ts_nhan') && $request->get('ts_nhan') != '') {
            $taisan = $taisan->where(function ($ts_nhan) use ($request) {
                return $ts_nhan->where('ts_nhan', 'like', '%' . $request->get('ts_nhan') . '%');
            });
        }
        $bichan = [];
        $giaitoa = [];
        $giaichap = [];
        $search = $request->get('ts_nhan');
        $taisan = $taisan->paginate(20);
        foreach ($taisan as $ts) {
            $bichan[] = TaiSanLichSuModel::where('ts_id', $ts->ts_id)->where('tinhtrang', 1)->select('ngayky',
                'ts_id')->orderBy('created_at', 'desc')->first();
            $giaitoa[] = TaiSanLichSuModel::where('ts_id', $ts->ts_id)->where('tinhtrang', 3)->select('ngayky',
                'ts_id')->orderBy('created_at', 'desc')->first();
            $giaichap[] = TaiSanLichSuModel::where('ts_id', $ts->ts_id)->where('tinhtrang', 2)->select('ngayky',
                'ts_id')->orderBy('created_at', 'desc')->first();

        }
        if ($request->ajax()) {
            return [
                'status' => 'success',
                'data' => $taisan,
                'giaitoa' => $giaitoa,
                'giaichap' => $giaichap,
                'bichan' => $bichan
            ];
        } else {
            $count = $taisan->count();
            for ($i = 0; $i < $count; $i++) {
                $taisan[$i]['stt'] = $i + 1;
            }
            return view('admin.taisan.index', compact('taisan', 'count', 'search', 'tong', 'bichan'));
        }
    }

    public function read_area(Request $request)
    {
        $num = $request->number;
        $read = static::convert_number_to_words($num);
        return ['status' => true, 'data' => $read];
    }


    // lấy kiểu cho menu
    public function getKieu(Request $request)
    {
        $id_kieu = KieuModel::select('k_id')->where('k_keywords', '=', 'tai-san')->first()->k_id;
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

    // hiện menu cho trang chọn kiểu tài sản
    public function ordered_menu($kieu, $k_id = 0)
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

    // trang chọn tiểu mục cho tài sản
    public function create()
    {
        return view('admin.taisan.create');
    }

    public function list_tieumuc_form($tm_arr, $k_id)
    {
        $tieumuc = TieuMucModel::leftjoin('tieumuc_sapxep', 'tieumuc_sapxep.tm_id', '=', 'tieumuc.tm_id')
            ->whereIn('tieumuc.tm_id', $tm_arr)
            ->where('k_id', $k_id)
            ->select([
                'tieumuc.tm_id',
                'tm_nhan',
                'tm_loai',
                'tm_keywords',
                'tm_batbuoc'
            ])->orderBy('tm_sort', 'asc');
        return $tieumuc;
    }

    public function get_kieutm(Request $request)
    {
        $tm = KieuTieuMucModel::select('kieu.k_id as id', 'k_tieumuc')
            ->leftjoin('kieu', 'kieu.k_id', '=', 'kieu_tieumuc.k_id')
            ->where('ktm_id', $request->ktm_id)->first();
        $tm_arr = explode(' ', $tm->k_tieumuc);
        $tieumuc = $this->list_tieumuc_form($tm_arr, $tm->id)->get();
        return $tieumuc;
    }

    public function get_tieumuc_select(Request $request)
    {
        $data = $this->get_kieutm($request);
        return ['status' => 'success', 'data' => $data];
    }

    public function get_tieumuc_options(Request $request)
    {
        $data = KieuTieuMucModel::where('tm_id', '=', $request->tm_id)
            ->where('ktm_status', 1)
            ->pluck('ktm_traloi', 'ktm_id');
        return ['status' => 'success', 'data' => $data];
    }


    // hiện form input nhập
    public function showCreate(Request $request, $id)
{

    // dd($id);
    $tieumuc = KieuModel::select('k_tieumuc')
        ->where('k_id', $id)
        ->first();

    // Convert chuỗi IDs thành mảng số
    $tieumuc_arr = array_filter(explode(' ', trim($tieumuc->k_tieumuc)));
    $tieumuc_arr = array_map('intval', $tieumuc_arr);

    $tieumuc_nhan = TieuMucModel::leftJoin('tieumuc_sapxep', 'tieumuc_sapxep.tm_id', '=', 'tieumuc.tm_id')
        ->where('tieumuc_sapxep.k_id', $id)
        ->whereIn('tieumuc.tm_id', $tieumuc_arr)
        ->select([
            'tieumuc.tm_id',
            'tm_nhan',
            'tm_keywords',
            'tm_loai',
            'tm_batbuoc',
            'tm_sort',
        ])
        ->orderBy('tm_sort', 'asc')
        ->get();

    $loai = $request->loai;
// dd($loai);
    return view('admin.taisan.showcreate', compact('tieumuc_nhan', 'loai','id'));
}


    // lưu database tài sản
    public function showStore(Request $request, $id)
    {
        // dd($request->all());
        $tieumuc = KieuModel::select('k_tieumuc')->where('k_id', $id)->first();
        $tieumuc_arr = explode(' ', $tieumuc->k_tieumuc);
        $tm_req = TieuMucModel::select('tm_id')
            ->whereIn('tm_id', $tieumuc_arr)
            ->where('tm_batbuoc', 1)
            ->get()->toArray();
        $keyts = TieuMucModel::select('tm_id')->where('tm_keywords', '=', 'ten-phieu-tai-san')->first();
        foreach ($request->ds_tm as $tm) {
            $tm_valid = substr($tm, 3);
            if ($tm_valid == $keyts->tm_id) {
                $arr_data[$tm] = $request->get($tm);
                $arr_validator[$tm] = 'required|unique:taisan,ts_nhan,null,id,deleted_at,NULL';
                $arr_messages[$tm . '.required'] = 'Vui lòng điền đầy đủ thông tin!';
                $arr_messages[$tm . '.unique'] = 'Nhãn tài sản đã tồn tại !';
            }
            if (in_array($tm_valid, $tm_req)) {
                $arr_data[$tm] = $request->get($tm);
                $arr_validator[$tm] = 'required';
                $arr_messages[$tm . '.required'] = 'Vui lòng điền đầy đủ thông tin!';
            }
            if ($request->hasFile($tm)) {
                $arr_data[$tm] = $request->file($tm);
                $arr_validator[$tm] = 'image';
                $arr_messages[$tm . '.image'] = 'Tệp tin không hợp lệ !';
            }
        }

        $validated = Validator::make($arr_data, $arr_validator, $arr_messages);
        if ($validated->fails()) {
            return redirect()->back()->withErrors($validated->errors())->withInput();
        } else {
            $i = 00;
            $array = [];
            $save_path = 'imagesTS';
            foreach ($request->ds_tm as $item) {
                $ts_valid = substr($item, 3);
                if ($ts_valid == $keyts->tm_id) {
                    $ts = TaiSanModel::create([
                        'ts_nhan' => $request->get($item),
                        'ts_trangthai' => 1,
                        'ts_kieu' => $id,
                        'id_vp' => \App\Models\NhanVienModel::where('nv_id', '=',
                            Sentinel::getUser()->id)->first()->nv_vanphong,
                        'id_ccv' => Sentinel::getUser()->id
                    ]);
                }
                if ($request->file($item)) {
                    $file = $request->file($item);
                    $ten_anh = time() . $i . '.' . $file->getClientOriginalExtension();
                    $file->move('imagesTS', $ten_anh);
                    $ts_giatri = $ten_anh;
                    $i++;
                } else {
                    $ts_giatri = $request->get($item);
                }

                $test=TaiSanGiaTriModel::create([
                    'ts_id' => $ts->ts_id,
                    'tm_id' => $ts_valid,
                    'ts_giatri' => $ts_giatri,
                ]);
                // dd($test);
            }
        }


        if ($request->loai == 1) {
            return view('admin.khachhang.close');
        } else {
            return Redirect::route('indexTaiSan')->with('success', 'Thêm tài sản thành công!');
        }
    }

    // xóa tài sản
    public function destroys($id)
    {
        if (TaiSanLichSuModel::where('ts_id', $id)->first() != null) {
            if (RoleUsersModel::where('user_id',
                    Sentinel::check()->id)->first()->role_id == 11 || RoleUsersModel::where('user_id',
                    Sentinel::check()->id)->first()->role_id == 10) {
                TaiSanModel::where('ts_id', $id)->delete();

                return Redirect::route('indexTaiSan')->with('success', 'Xóa tài sản thành công!');

            } else {

                return Redirect::route('indexTaiSan')->with('error', 'Bạn không thể xóa tài sản này !');

            }

        } else {
            TaiSanModel::where('ts_id', $id)->delete();

            return Redirect::route('indexTaiSan')->with('success', 'Xóa tài sản thành công!');

        }

    }

    // trang cập nhật tài sản
   public function showEdit($id)
{
   
    // Lấy kiểu tài sản
    $ts_kieu = TaiSanModel::where('ts_id', $id)->value('ts_kieu');

    // Lấy danh sách tiểu mục theo kiểu tài sản
    $tieumuc_arr = explode(' ', KieuModel::where('k_id', $ts_kieu)->value('k_tieumuc'));

    $tieumuc_nhan = TieuMucModel::leftJoin('tieumuc_sapxep', 'tieumuc_sapxep.tm_id', 'tieumuc.tm_id')
        ->where('tieumuc_sapxep.k_id', $ts_kieu)
        ->whereIn('tieumuc.tm_id', $tieumuc_arr)
        ->orderBy('tieumuc_sapxep.tm_sort', 'asc')
        ->select([
            'tieumuc.tm_id',
            'tieumuc.tm_nhan',
            'tieumuc.tm_keywords',
            'tieumuc.tm_loai',
            'tieumuc.tm_batbuoc',
            'tieumuc_sapxep.tm_sort',
        ])
        ->get();

    // Lấy giá trị tiểu mục + giá trị tương ứng của tài sản
    $ts_giatri = TaiSanGiaTriModel::where('ts_id', $id)
        ->select('tm_id', 'ts_giatri')
        ->get();

    // Map dữ liệu ra dạng: [ tm_id => value ]
    $dtb = [];

    foreach ($ts_giatri as $item) {

        // Nếu là nhiều file → decode JSON
        if ($item->ts_giatri && is_string($item->ts_giatri) && $this->isJson($item->ts_giatri)) {
            $dtb[$item->tm_id] = json_decode($item->ts_giatri, true);

        } else {
            $dtb[$item->tm_id] = $item->ts_giatri;
        }
    }
// dd($id);
    return view('admin.taisan.showedit', compact('tieumuc_nhan', 'dtb','id'));
}

/**
 * Helper kiểm tra chuỗi có phải JSON không
 */
private function isJson($string)
{
    json_decode($string);
    return (json_last_error() === JSON_ERROR_NONE);
}


    // lưu dữ liệu cập nhật
    public function showUpdate(Request $request, $id)
    {
        // dd($request->all());
        $this->logCreate($id, $request->note ?? 'Không có ghi chú', '');
        $ts_kieu = TaiSanModel::where('ts_id', $id)->select('ts_kieu')->first()->ts_kieu;
        $tieumuc = KieuModel::select('k_tieumuc')->where('k_id', $ts_kieu)->first();
        $tieumuc_arr = explode(' ', $tieumuc->k_tieumuc);
        $tm_req = TieuMucModel::select('tm_id')
            ->whereIn('tm_id', $tieumuc_arr)
            ->where('tm_batbuoc', 1)
            ->get()->toArray();
        $keyts = TieuMucModel::select('tm_id')->where('tm_keywords', '=', 'ten-phieu-tai-san')->first();
        // dd($keyts);
        $ds_tm = array_keys($request->all());
$ds_tm = array_filter($ds_tm, function ($k) {
    return str_starts_with($k, 'tm-');
});
        foreach ($ds_tm as $tm) {
            $tm_valid = substr($tm, 3);
            if ($tm_valid == $keyts->tm_id) {
                $arr_data[$tm] = $request->get($tm);
                $arr_validator[$tm] = 'required|unique:taisan,ts_nhan,' . $id . ',ts_id';
                $arr_messages[$tm . '.required'] = 'Vui lòng điền đầy đủ thông tin!';
                $arr_messages[$tm . '.unique'] = 'Nhãn tài sản đã tồn tại !';
            }
            if (in_array($tm_valid, $tm_req)) {
                $arr_data[$tm] = $request->get($tm);
                $arr_validator[$tm] = 'required';
                $arr_messages[$tm . '.required'] = 'Vui lòng điền đầy đủ thông tin!';
            }
        }
        $validated = Validator::make($arr_data, $arr_validator, $arr_messages);
        if ($validated->fails()) {
            return redirect()->back()->withErrors($validated->errors())->withInput();
        } else {
            $i = 0;
            $save_path = 'imagesTS';
            $existing_tm_ids = TaiSanGiaTriModel::where('ts_id', $id)
    ->pluck('tm_id')
    ->toArray();

$ds_tm = collect($request->all())
    ->filter(function ($v, $k) {
        return str_starts_with($k, 'tm-');
    });

foreach ($ds_tm as $field => $value) {

    $ts_valid = intval(substr($field, 3));

    // Update tên nhãn tài sản
    if ($ts_valid == $keyts->tm_id) {
        TaiSanModel::where('ts_id', $id)
            ->update(['ts_nhan' => $value]);
    }

    // Chỉ update nếu thuộc loại đã tồn tại
    if (in_array($ts_valid, $existing_tm_ids)) {

        $old_val = TaiSanGiaTriModel::where('ts_id', $id)
            ->where('tm_id', $ts_valid)
            ->value('ts_giatri');

        $tieu_muc = TieuMucModel::where('tm_id', $ts_valid)->value('tm_loai');

        TaiSanGiaTriModel::where('ts_id', $id)
            ->where('tm_id', $ts_valid)
            ->delete();

        // Xử lý file
        if ($tieu_muc == 'file') {
            if ($request->file($field)) {

                if ($old_val && file_exists(public_path("imagesTS/$old_val"))) {
                    unlink(public_path("imagesTS/$old_val"));
                }

                $file = $request->file($field);
                $ten_anh = time() . rand(1000,9999) . '.' . $file->getClientOriginalExtension();
                $file->move('imagesTS', $ten_anh);
                $ts_giatri = $ten_anh;

            } else {
                $ts_giatri = $old_val;
            }

        } else {
            $ts_giatri = $value;
        }

        TaiSanGiaTriModel::create([
            'ts_id'    => $id,
            'tm_id'    => $ts_valid,
            'ts_giatri' => $ts_giatri,
        ]);
    }

    // Nếu không tồn tại → thêm mới
    else {

        $tieu_muc = TieuMucModel::where('tm_id', $ts_valid)->value('tm_loai');

        if ($tieu_muc == 'file' && $request->file($field)) {

            foreach ($request->file($field) as $img) {
                $ten_anh = $id . '_' . time() . '.' . $img->getClientOriginalExtension();
                AppController::upload_nextcloud($ten_anh, $img, '/tai-san/giay-to/');
                $ts_giatri_arr[] = $ten_anh;
            }

            $ts_giatri = json_encode($ts_giatri_arr);
        } else {
            $ts_giatri = $value;
        }

        TaiSanGiaTriModel::create([
            'ts_id'    => $id,
            'tm_id'    => $ts_valid,
            'ts_giatri' => $ts_giatri,
        ]);
    }
}

        }
        return Redirect::route('indexTaiSan')->with('success', 'Cập nhật tài sản thành công!');
    }

    public function showShow($id)
    {
        // dd($id);
        $tai_san_id = $id;
        $taisan = TaiSanModel::where('ts_id', $id)->select('ts_kieu', 'id_vp', 'id_ccv')->first();
        $taisan = TaiSanModel::where('ts_id', $id)
    ->select('ts_kieu', 'id_vp', 'id_ccv')
    ->first();
// dd($taisan->ts_kieu);
if (!$taisan) {
    return abort(404, "Không tìm thấy tài sản.");
}

$kieu = KieuModel::where('k_id', $taisan->ts_kieu)->first();
$raw = $kieu->getRawOriginal('k_tieumuc');   // string “30 1069 1070...”
$tieumuc_arr = array_map('intval', preg_split('/\s+/', trim($raw)));
// dd($id);
$data = TaiSanModel::Join('taisan_giatri', 'taisan_giatri.ts_id', '=', 'taisan.ts_id')
    ->Join('tieumuc', 'tieumuc.tm_id', '=', 'taisan_giatri.tm_id')
    ->where('taisan_giatri.ts_id', $id)
    ->whereIn('taisan_giatri.tm_id', $tieumuc_arr)
    ->get();
// dd(DB::table('taisan_giatri')->where('ts_id', $id)->get());

// dd(TaiSanModel::find($id));


            
        foreach ($data as $val) {

            if ($val->tm_loai == 'select') {
                if (KieuTieuMucModel::where('ktm_id', $val->ts_giatri)->first()) {
                    $val->ts_giatri = KieuTieuMucModel::where('ktm_id', $val->ts_giatri)->first()->ktm_traloi;

                }
            }

        }

        $data = $data->toArray();
        $tai_san_id = $id;

        $lichsu = [];
        return view('admin.taisan.showshow', compact('data', 'taisan', 'lichsu', 'tai_san_id'));
    }

    // đổi kiểu tài sản
    public function changeCreate($id)
    {
        return view('admin.taisan.change', compact('id'));
    }

    // dữ liệu đổi kiểu tìa sản
    public function changeStore($id, $id2)
    {

        $ts_kieu = TaiSanModel::where('ts_id', $id)->first()->ts_kieu;
        $p1 = KieuModel::find($ts_kieu)->change_type ?? '';
        $p2 = KieuModel::find($id2)->change_type ?? '';
        if ($p1 != $p2 || $p2 == "" || $p2 == "") {
            return Redirect::route('indexTaiSan')->withError('Đổi kiểu không thành công. Vui lòng kiểu tra lại !');

        }
        $k_tm1 = explode(' ', KieuModel::find($ts_kieu)->k_tieumuc);
        $k_tm2 = explode(' ', KieuModel::find($id2)->k_tieumuc);
        $tm_create = array_diff($k_tm2, $k_tm1);
        foreach ($tm_create as $tm) {
            if (!TaiSanGiaTriModel::where('ts_id', $id)->where('tm_id', $tm)->first()) {
                TaiSanGiaTriModel::create([
                    'ts_id' => $id,
                    'tm_id' => $tm,
                    'ts_giatri' => null,
                ]);
            }
        }
        $this->logCreate($id, 'Đổi kiểu', '');
        TaiSanModel::find($id)->update(['ts_kieu' => $id2]);
        return Redirect::route('indexTaiSan')->with('success', 'Đổi kiểu tài sản thành công!');
    }

    public static function logCreate($id, $note, $user_id)
    {
        $ts_kieu = TaiSanModel::where('ts_id', $id)->select('ts_kieu')->first()->ts_kieu;
        $ds_kieu = KieuModel::where('k_id', $ts_kieu)->get()->first()->k_tieumuc;
        $tieumuc_arr = explode(' ', $ds_kieu);
        $data = TaiSanModel::leftjoin('taisan_giatri', 'taisan_giatri.ts_id', '=', 'taisan.ts_id')
            ->leftjoin('tieumuc', 'tieumuc.tm_id', '=', 'taisan_giatri.tm_id')
            ->where('taisan_giatri.ts_id', $id)
            ->select([
                'tieumuc.tm_id',
                'tieumuc.tm_nhan',
                'tieumuc.tm_loai',
                'tieumuc.tm_batbuoc',
                'taisan_giatri.ts_giatri',
                'taisan_giatri.ts_id',
            ])->get();
        foreach ($data as $val) {
            if ($val->tm_loai == 'select') {
                if (KieuTieuMucModel::where('ktm_id', $val->ts_giatri)->first()) {
                    $val->ts_giatri = KieuTieuMucModel::where('ktm_id', $val->ts_giatri)->first()->ktm_traloi;

                }
            }
        }
        $data = $data->toArray();
        // $lichsu = TaisanGDModel::where('ts_id', $id)->all();
        $log_content = array('data' => $data, 'lich_su' => '', 'note' => $note ?? 'Không có ghi chú');
        if (!$user_id) {
            $user_id = Sentinel::check()->id;
        }
        TaiSanLog::create([
            'log_content' => json_encode($log_content),
            'ts_id' => $id,
            'creator_id' => $user_id ?? 1
        ]);

        return true;
    }

    /**
     * Generate QR
     * * Input : ID
     * * Output: image directory
     */
    public static function generateQRCode($id)
    {
        $image = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
            ->size(200)
            ->generate($id);
        $output_file = "/QRCode/taisan" . $id . ".png";
        Storage::disk('public')->put($output_file, $image);
        return asset('storage/' . $output_file);
    }

public static function convert_number_to_words($number)
{
    $hyphen      = ' ';
    $conjunction = ' ';
    $separator   = ' ';
    $negative    = 'âm ';
    $decimal     = ' phẩy ';
    $dictionary  = [
        0 => 'không',
        1 => 'một',
        2 => 'hai',
        3 => 'ba',
        4 => 'bốn',
        5 => 'năm',
        6 => 'sáu',
        7 => 'bảy',
        8 => 'tám',
        9 => 'chín',
        10 => 'mười',
        11 => 'mười một',
        12 => 'mười hai',
        13 => 'mười ba',
        14 => 'mười bốn',
        15 => 'mười lăm',
        16 => 'mười sáu',
        17 => 'mười bảy',
        18 => 'mười tám',
        19 => 'mười chín',
        20 => 'hai mươi',
        30 => 'ba mươi',
        40 => 'bốn mươi',
        50 => 'năm mươi',
        60 => 'sáu mươi',
        70 => 'bảy mươi',
        80 => 'tám mươi',
        90 => 'chín mươi',
        100 => 'trăm',
        1000 => 'nghìn',
        1000000 => 'triệu',
        1000000000 => 'tỷ',
        1000000000000 => 'nghìn tỷ',
        1000000000000000 => 'triệu tỷ',
        1000000000000000000 => 'tỷ tỷ'
    ];

    if (!is_numeric($number)) {
        return false;
    }

    // xử lý âm
    if ($number < 0) {
        return $negative . self::convert_number_to_words(abs($number));
    }

    $string = $fraction = null;

    // tách phần thập phân
    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
        $number = (int)$number;
    }

    // đọc phần nguyên
    switch (true) {

        case $number < 21:
            $string = $dictionary[$number];
            break;

        case $number < 100:
            $tens = ((int)($number / 10)) * 10;
            $units = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                if ($units == 1) $string .= ' mốt';
                else if ($units == 5) $string .= ' lăm';
                else $string .= $hyphen . $dictionary[$units];
            }
            break;

        case $number < 1000:
            $hundreds = (int)($number / 100);
            $remainder = $number % 100;

            $string = $dictionary[$hundreds] . ' trăm';

            if ($remainder) {

                if ($remainder < 10) {
                    if ($remainder == 5) $string .= ' lẻ năm';
                    else $string .= ' lẻ ' . $dictionary[$remainder];

                } else {
                    $string .= $conjunction . self::convert_number_to_words($remainder);
                }
            }
            break;

        default:
            // xác định đơn vị lớn nhất
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int)($number / $baseUnit);
            $remainder = $number % $baseUnit;

            $string = self::convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];

            if ($remainder) {
                // xử lý "lẻ" đúng chuẩn
                if ($remainder < 100) {
                    $string .= ' lẻ ' . self::convert_number_to_words($remainder);
                } else {
                    $string .= $separator . self::convert_number_to_words($remainder);
                }
            }
            break;
    }

    // đọc phần thập phân (decimal)
    if ($fraction !== null && is_numeric($fraction)) {
        $string .= $decimal;
        foreach (str_split($fraction) as $digit) {
            $string .= $dictionary[$digit] . ' ';
        }
        $string = trim($string);
    }

    return $string;
}

    public static function convert_date_to_words($number)
    {
        $hyphen = ' ';
        $conjunction = ' ';
        $separator = ' ';
        $negative = 'âm ';
        $decimal = ' phẩy ';
        $dictionary = array(
            0 => 'không',
            1 => 'một',
            2 => 'hai',
            3 => 'ba',
            4 => 'bốn',
            5 => ', năm',
            6 => 'sáu',
            7 => 'bảy',
            8 => 'tám',
            9 => 'chín',
            10 => 'mười',
            11 => 'mười một',
            12 => 'mười hai',
            13 => 'mười ba',
            14 => 'mười bốn',
            15 => 'mười lăm',
            16 => 'mười sáu',
            17 => 'mười bảy',
            18 => 'mười tám',
            19 => 'mười chín',
            20 => 'hai mươi',
            30 => 'ba mươi',
            40 => 'bốn mươi',
            50 => ', năm mươi',
            60 => 'sáu mươi',
            70 => 'bảy mươi',
            80 => 'tám mươi',
            90 => 'chín mươi',
            100 => 'trăm',
            1000 => 'nghìn',
            1000000 => 'triệu',
            1000000000 => 'tỷ',
            1000000000000 => 'nghìn tỷ',
            1000000000000000 => 'nghìn triệu triệu',
            1000000000000000000 => 'tỷ tỷ'
        );

        if (!is_numeric($number)) {
            return false;
        }

        if (($number >= 0 && (int)$number < 0) || (int)$number < 0 - PHP_INT_MAX) {
            // overflow
            trigger_error('convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX, E_USER_WARNING);
            return false;
        }

        if ($number < 0) {
            return $negative . HopDongControllerV2::convert_number_to_words(abs($number));
        }

        $string = $fraction = null;

        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens = ((int)($number / 10)) * 10;
                $units = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    if ($units != 5) {
                        if ($units == 1) {
                            $string .= $hyphen . " mốt";
                        } else {
                            $string .= $hyphen . $dictionary[$units];
                        }

                    } else
                        $string .= $hyphen . "lăm";

                }
                break;
            case $number < 1000:
                $hundreds = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    if ($remainder < 10) {

                        if ($remainder != 5)
                            $string .= ' lẻ' . $conjunction . HopDongControllerV2::convert_number_to_words($remainder);
                        else
                            $string .= ' lẻ' . $conjunction . "năm";

                    } else {
                        $string .= $conjunction . HopDongControllerV2::convert_number_to_words($remainder);

                    }

                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int)($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = HopDongControllerV2::convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction . "không trăm " : $separator;
                    if ($remainder < 10) {
                        $string .= 'lẻ ' . HopDongControllerV2::convert_number_to_words($remainder);

                    } else {
                        $string .= HopDongControllerV2::convert_number_to_words($remainder);

                    }
                }
                break;
        }

        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = array();
            foreach (str_split((string)$fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }

        return $string;
    }
}
