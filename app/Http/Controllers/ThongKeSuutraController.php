<?php

namespace App\Http\Controllers;

use App\Models\ChiNhanhModel;
use App\Exports\BaoCaoExportSheet;
use App\Exports\HeadingSuuTraTkSheet;
use App\Exports\SuuTraExport;
use App\Models\Kieuhopdong;
use App\Models\NhanVienModel;
use App\Models\RoleUsersModel;
use App\Models\SuuTraModel;
use App\Models\VanBanModel;
use Carbon\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ThongKeSuutraController extends Controller
{
    public function SeachSuuTra(Request $request)
    {

        $role = Sentinel::check()->user_roles()->first()->slug;

//        dd(1);
        /* khối dữ liệu */
        $khoidulieu = [
            'D' => 'Jdata',
            'U' => 'Dữ liệu khác'
        ];
        $khoidulieu = collect($khoidulieu)->prepend('------', '');
        /* theo tên văn phòng */
        $ten_vp_master = SuuTraModel::select('vp_master')->groupBy('vp_master')->get();
        $data_vp = [];
        foreach ($ten_vp_master as $val) {
            $data_vp[$val->vp_master] = $val->vp_master;
        }
        $chinhanh = collect($data_vp)->prepend('------', '');
        /* theo loại */
        $bichan = [
            '0' => 'Giải tỏa',
            '1' => 'Bị chặn',
            '2' => 'Cảnh báo'
        ];
        $bichan = collect($bichan)->prepend('------', '');
        /* theo tên hợp đồng*/
        $ten_hd_master = SuuTraModel::select('ten_hd')->groupBy('ten_hd')->get();
        $data_hp = [];
        foreach ($ten_hd_master as $val) {
            if ($val->ten_hd != null)
                $data_hp[$val->ten_hd] = $val->ten_hd;
        }
        $tenhd = collect($data_hp)->prepend('------', '');
        /* theo công chứng viên */
        if ($role == "admin" || $role == 'chuyen-vien-so') {
            $ten_ccv_master = SuuTraModel::select('ccv_master')->groupBy('ccv_master')->get();

        } else {
            $ten_ccv_master = SuuTraModel::select('ccv_master')->where('suutranb.vp', NhanVienModel::find(Sentinel::check()->id)->nv_vanphong)
                ->groupBy('ccv_master')->get();

        }
        $data_ccv = [];
        foreach ($ten_ccv_master as $val) {
            if ($val->ccv_master != null)
                $data_ccv[$val->ccv_master] = $val->ccv_master;
        }
        $ccv = collect($data_ccv)->prepend('------', '');
        $str_json = json_encode([]);
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
            'suutranb.ccv_master',
            'suutranb.vp_master',
            'suutranb.cancel_status',
            'suutranb.cancel_description',
            'suutranb.ma_dong_bo',
            'suutranb.ma_phan_biet',
            'suutranb.thu_lao',
            'suutranb.phi_cong_chung'

        ];
        if ($role == "admin" || $role == 'chuyen-vien-so') {
            $data = SuuTraModel::leftjoin('users', 'users.id', '=', 'suutranb.ccv')
                ->leftjoin('chinhanh', 'cn_id', '=', 'suutranb.vp')
                ->select($array);
        } else {
            $data = SuuTraModel::leftjoin('users', 'users.id', '=', 'suutranb.ccv')
                ->where('suutranb.sync_code', ChiNhanhModel::find(NhanVienModel::find(Sentinel::check()->id)->nv_vanphong)->code_cn)
                ->select($array);
        }

        /* thống kê tất cả */
//        if ($request->radio == 1) {
//            if (RoleUsersModel::where('user_id', Sentinel::check()->id)->first()->role_id == 10) {
//                $data = SuuTraModel::leftjoin('users', 'users.id', '=', 'suutranb.ccv')
//                    ->leftjoin('chinhanh', 'cn_id', '=', 'suutranb.vp')
//                    ->select($array);
//            } else {
//                $data = SuuTraModel::leftjoin('users', 'users.id', '=', 'suutranb.ccv')
//                    ->leftjoin('chinhanh', 'cn_id', '=', 'suutranb.vp')
//                    ->where('suutranb.vp', NhanVienModel::find(Sentinel::check()->id)->nv_vanphong)
//                    ->select($array);
//            }
//            $count = count($data->get());
//            $data = $data->orderby('st_id', 'desc')->paginate(10);
//            return view('admin.thongkesuutra.vpso.search', compact('data', 'khoidulieu', 'count', 'str_json', 'tenhd', 'chinhanh', 'ccv', 'bichan', 'request'));
//        }

        /* thông kê dữ liệu dotary */
        if ($request->khoidulieu) {
            $data->where('suutranb.ma_phan_biet', $request->get('khoidulieu'));
        }
        /* thống kê theo văn phòng */
        if ($request->get('theonvnv')) {
            $data->where('suutranb.vp_master', 'like', '%' . $request->get('theonvnv') . '%');
        }
        /* thống kê theo tên hợp đồng*/
        if ($request->get('theotenhd')) {
            $data->where('suutranb.ten_hd', 'like', '%' . $request->get('theotenhd') . '%');
        }
        /* thống kê theo công chứng viên */
        if ($request->get('theoccv')) {
            $data->where('suutranb.ccv_master', 'like', '%' . $request->get('theoccv') . '%');
        }
        /* thống kê theo ngày */
        if ($request->get('tungay') != '' && $request->get('denngay') == '') {
            $data->whereDate('suutranb.ngay_cc', '>=', $request->get('tungay'));
        }
        if ($request->get('tungay') == '' && $request->get('denngay') != '') {
            $data->whereDate('suutranb.ngay_cc', '<=', $request->get('denngay'));
        }
        if ($request->get('tungay') != '' && $request->get('denngay') != '') {
            $data->whereDate('suutranb.ngay_cc', '>=', $request->get('tungay'))
                ->whereDate('suutranb.ngay_cc', '<=', $request->get('denngay'));
        }
        /* thông kế theo loại */
        if ($request->get('loai') == 1) {
            $data->where('suutranb.ngan_chan', '=', 1);
        }
        if ($request->get('loai') == 2) {
            $data->where('suutranb.ngan_chan', '=', 2);
        }
        if ($request->get('loai') == 0) {
            $data->where('suutranb.ngan_chan', '=', 0);
            if ($request->khoidulieu) {
                $data->where('suutranb.ma_phan_biet', $request->get('khoidulieu'));
            }
            /* thống kê theo văn phòng */
            if ($request->get('theonvnv')) {
                $data->where('suutranb.vp_master', 'like', '%' . $request->get('theonvnv') . '%');
            }
            /* thống kê theo tên hợp đồng*/
            if ($request->get('theotenhd')) {
                $data->where('suutranb.ten_hd', 'like', '%' . $request->get('theotenhd') . '%');
            }
            /* thống kê theo công chứng viên */
            if ($request->get('theoccv')) {
                $data->where('suutranb.ccv_master', 'like', '%' . $request->get('theoccv') . '%');
            }
            /* thống kê theo ngày */
            if ($request->get('tungay') != '' && $request->get('denngay') == '') {
                $data->whereDate('suutranb.ngay_cc', '>=', $request->get('tungay'));
            }
            if ($request->get('tungay') == '' && $request->get('denngay') != '') {
                $data->whereDate('suutranb.ngay_cc', '<=', $request->get('denngay'));
            }
            if ($request->get('tungay') != '' && $request->get('denngay') != '') {
                $data->whereDate('suutranb.ngay_cc', '>=', $request->get('tungay'))
                    ->whereDate('suutranb.ngay_cc', '<=', $request->get('denngay'));
            }
        }
        $count = count($data->get());
        $data = $data->orderby('st_id', 'desc')->paginate(10);
//        dd($data);
        return view('admin.thongkesuutra.vpso.search', compact('data', 'count', 'khoidulieu', 'str_json', 'tenhd', 'chinhanh', 'ccv', 'bichan'));
    }

    public function export(Request $request)
    {
        /* khối dữ liệu */
        $khoidulieu = [
            'D' => 'Jdata',
            'U' => 'Dữ liệu khác'
        ];
        $khoidulieu = collect($khoidulieu)->prepend('------', '');
        /* theo tên văn phòng */
        $ten_vp_master = SuuTraModel::select('vp_master')->groupBy('vp_master')->get();
        $data_vp = [];
        foreach ($ten_vp_master as $val) {
            $data_vp[$val->vp_master] = $val->vp_master;
        }
        $chinhanh = collect($data_vp)->prepend('------', '');
        /* theo loại */
        $bichan = [
            '0' => 'Giải tỏa',
            '1' => 'Bị chặn',
            '2' => 'Cảnh báo'
        ];
        $bichan = collect($bichan)->prepend('------', '');
        /* theo tên hợp đồng*/
        $ten_hd_master = SuuTraModel::select('ten_hd')->groupBy('ten_hd')->get();
        $data_hp = [];
        foreach ($ten_hd_master as $val) {
            if ($val->ten_hd != null)
                $data_hp[$val->ten_hd] = $val->ten_hd;
        }
        $tenhd = collect($data_hp)->prepend('------', '');
        /* theo công chứng viên */
        $ten_ccv_master = SuuTraModel::select('ccv_master')->groupBy('ccv_master')->get();
        $data_ccv = [];
        foreach ($ten_ccv_master as $val) {
            if ($val->ccv_master != null)
                $data_ccv[$val->ccv_master] = $val->ccv_master;
        }
        $ccv = collect($data_ccv)->prepend('------', '');
        $str_json = json_encode([]);
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
            'suutranb.ccv_master',
            'suutranb.vp_master',
            'suutranb.cancel_status',
            'suutranb.cancel_description',
            'suutranb.ma_dong_bo',
            'suutranb.ma_phan_biet'
        ];
        /* thống kê tất cả */
        if ($request->radio == 1) {
            if (RoleUsersModel::where('user_id', Sentinel::check()->id)->first()->role_id == 10) {
                $data = SuuTraModel::leftjoin('users', 'users.id', '=', 'suutranb.ccv')
                    ->leftjoin('chinhanh', 'cn_id', '=', 'suutranb.vp')
                    ->select($array);
                $data = $data->orderby('st_id', 'desc')->get();
                return Excel::download(new SuuTraExport($data), 'exportsuutra.xlsx');
            } else {
                $data = SuuTraModel::leftjoin('users', 'users.id', '=', 'suutranb.ccv')
                    ->leftjoin('chinhanh', 'cn_id', '=', 'suutranb.vp')
                    ->where('suutranb.vp', NhanVienModel::find(Sentinel::check()->id)->nv_vanphong)
                    ->select($array);
                $data = $data->orderby('st_id', 'desc')->get();
                return Excel::download(new SuuTraExport($data), 'exportsuutra.xlsx');
            }
        }
        $data = SuuTraModel::leftjoin('users', 'users.id', '=', 'suutranb.ccv')
            ->leftjoin('chinhanh', 'cn_id', '=', 'suutranb.vp')
            ->select($array);
        /* thông kê dữ liệu dotary */
        if ($request->khoidulieu) {
            $data->where('suutranb.ma_phan_biet', $request->get('khoidulieu'));
        }
        /* thống kê theo văn phòng */
        if ($request->get('theonvnv')) {
            $data->where('suutranb.vp_master', 'like', '%' . $request->get('theonvnv') . '%');
        }
        /* thống kê theo tên hợp đồng*/
        if ($request->get('theotenhd')) {
            $data->where('suutranb.ten_hd', 'like', '%' . $request->get('theotenhd') . '%');
        }
        /* thống kê theo công chứng viên */
        if ($request->get('theoccv')) {
            $data->where('suutranb.ccv_master', 'like', '%' . $request->get('theoccv') . '%');
        }
        /* thống kê theo ngày */
        if ($request->get('tungay') != '' && $request->get('denngay') == '') {
            $data->whereDate('suutranb.ngay_cc', '>=', $request->get('tungay'));
        }
        if ($request->get('tungay') == '' && $request->get('denngay') != '') {
            $data->whereDate('suutranb.ngay_cc', '<=', $request->get('denngay'));
        }
        if ($request->get('tungay') != '' && $request->get('denngay') != '') {
            $data->whereDate('suutranb.ngay_cc', '>=', $request->get('tungay'))
                ->whereDate('suutranb.ngay_cc', '<=', $request->get('denngay'));
        }
        /* thông kế theo loại */
        if ($request->get('loai') == 1) {
            $data->where('suutranb.ngan_chan', '=', 1);
        }
        if ($request->get('loai') == 2) {
            $data->where('suutranb.ngan_chan', '=', 2);
        }
        if ($request->get('loai') == 0) {
            $data->where('suutranb.ngan_chan', '=', 0);
            if ($request->khoidulieu) {
                $data->where('suutranb.ma_phan_biet', $request->get('khoidulieu'));
            }
            /* thống kê theo văn phòng */
            if ($request->get('theonvnv')) {
                $data->where('suutranb.vp_master', 'like', '%' . $request->get('theonvnv') . '%');
            }
            /* thống kê theo tên hợp đồng*/
            if ($request->get('theotenhd')) {
                $data->where('suutranb.ten_hd', 'like', '%' . $request->get('theotenhd') . '%');
            }
            /* thống kê theo công chứng viên */
            if ($request->get('theoccv')) {
                $data->where('suutranb.ccv_master', 'like', '%' . $request->get('theoccv') . '%');
            }
            /* thống kê theo ngày */
            if ($request->get('tungay') != '' && $request->get('denngay') == '') {
                $data->whereDate('suutranb.ngay_cc', '>=', $request->get('tungay'));
            }
            if ($request->get('tungay') == '' && $request->get('denngay') != '') {
                $data->whereDate('suutranb.ngay_cc', '<=', $request->get('denngay'));
            }
            if ($request->get('tungay') != '' && $request->get('denngay') != '') {
                $data->whereDate('suutranb.ngay_cc', '>=', $request->get('tungay'))
                    ->whereDate('suutranb.ngay_cc', '<=', $request->get('denngay'));
            }
        }
        $data = $data->orderby('st_id', 'desc')->get();
        return Excel::download(new SuuTraExport($data), 'exportsuutra.xlsx');
    }



    public function SeachSuuTra6thang(Request $request)
    {

        $role = Sentinel::check()->user_roles()->first()->slug;

//        dd(1);
        /* khối dữ liệu */
        $khoidulieu = [
            'D' => 'Jdata',
            'U' => 'Dữ liệu khác'
        ];
        $khoidulieu = collect($khoidulieu)->prepend('------', '');
        /* theo tên văn phòng */
        $ten_vp_master = SuuTraModel::select('vp_master')->groupBy('vp_master')->get();
        $data_vp = [];
        foreach ($ten_vp_master as $val) {
            $data_vp[$val->vp_master] = $val->vp_master;
        }
        $chinhanh = collect($data_vp)->prepend('------', '');
        /* theo loại */
        $bichan = [
            '0' => 'Giải tỏa',
            '1' => 'Bị chặn',
            '2' => 'Cảnh báo'
        ];
        $bichan = collect($bichan)->prepend('------', '');
        /* theo tên hợp đồng*/
        $ten_hd_master = SuuTraModel::select('ten_hd')->groupBy('ten_hd')->get();
        $data_hp = [];
        foreach ($ten_hd_master as $val) {
            if ($val->ten_hd != null)
                $data_hp[$val->ten_hd] = $val->ten_hd;
        }
        $tenhd = collect($data_hp)->prepend('------', '');
        /* theo công chứng viên */
        if ($role == "admin" || $role == 'chuyen-vien-so') {
            $ten_ccv_master = SuuTraModel::select('ccv_master')->groupBy('ccv_master')->get();

        } else {
            $ten_ccv_master = SuuTraModel::select('ccv_master')->where('suutranb.vp', NhanVienModel::find(Sentinel::check()->id)->nv_vanphong)
                ->groupBy('ccv_master')->get();

        }

        $data_ccv = [];
        foreach ($ten_ccv_master as $val) {
            if ($val->ccv_master != null)
                $data_ccv[$val->ccv_master] = $val->ccv_master;
        }
        $ccv = collect($data_ccv)->prepend('------', '');
        $str_json = json_encode([]);
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
            'suutranb.ccv_master',
            'suutranb.vp_master',
            'suutranb.cancel_status',
            'suutranb.cancel_description',
            'suutranb.ma_dong_bo',
            'suutranb.ma_phan_biet',
            'suutranb.thu_lao',
            'suutranb.phi_cong_chung'

        ];
        if ($role == "admin" || $role == 'chuyen-vien-so') {
            $data = SuuTraModel::leftjoin('users', 'users.id', '=', 'suutranb.ccv')
                ->leftjoin('chinhanh', 'cn_id', '=', 'suutranb.vp')
                ->select($array);
        } else {
            $data = SuuTraModel::leftjoin('users', 'users.id', '=', 'suutranb.ccv')
                ->leftjoin('chinhanh', 'cn_id', '=', 'suutranb.vp')
                ->where('suutranb.vp', NhanVienModel::find(Sentinel::check()->id)->nv_vanphong)
                ->select($array);
        }

        /* thống kê tất cả */
//        if ($request->radio == 1) {
//            if (RoleUsersModel::where('user_id', Sentinel::check()->id)->first()->role_id == 10) {
//                $data = SuuTraModel::leftjoin('users', 'users.id', '=', 'suutranb.ccv')
//                    ->leftjoin('chinhanh', 'cn_id', '=', 'suutranb.vp')
//                    ->select($array);
//            } else {
//                $data = SuuTraModel::leftjoin('users', 'users.id', '=', 'suutranb.ccv')
//                    ->leftjoin('chinhanh', 'cn_id', '=', 'suutranb.vp')
//                    ->where('suutranb.vp', NhanVienModel::find(Sentinel::check()->id)->nv_vanphong)
//                    ->select($array);
//            }
//            $count = count($data->get());
//            $data = $data->orderby('st_id', 'desc')->paginate(10);
//            return view('admin.thongkesuutra.vpso.search', compact('data', 'khoidulieu', 'count', 'str_json', 'tenhd', 'chinhanh', 'ccv', 'bichan', 'request'));
//        }

        /* thông kê dữ liệu dotary */
        if ($request->khoidulieu) {
            $data->where('suutranb.ma_phan_biet', $request->get('khoidulieu'));
        }
        /* thống kê theo văn phòng */
        if ($request->get('theonvnv')) {
            $data->where('suutranb.vp_master', 'like', '%' . $request->get('theonvnv') . '%');
        }
        /* thống kê theo tên hợp đồng*/
        if ($request->get('theotenhd')) {
            $data->where('suutranb.ten_hd', 'like', '%' . $request->get('theotenhd') . '%');
        }
        /* thống kê theo công chứng viên */
        if ($request->get('theoccv')) {
            $data->where('suutranb.ccv_master', 'like', '%' . $request->get('theoccv') . '%');
        }
        /* thống kê theo ngày */
        if ($request->get('tungay') != '' && $request->get('denngay') == '') {
            $data->whereDate('suutranb.ngay_cc', '>=', $request->get('tungay'));
        }
        if ($request->get('tungay') == '' && $request->get('denngay') != '') {
            $data->whereDate('suutranb.ngay_cc', '<=', $request->get('denngay'));
        }
        if ($request->get('tungay') != '' && $request->get('denngay') != '') {
            $data->whereDate('suutranb.ngay_cc', '>=', $request->get('tungay'))
                ->whereDate('suutranb.ngay_cc', '<=', $request->get('denngay'));
        }
        /* thông kế theo loại */
        if ($request->get('loai') == 1) {
            $data->where('suutranb.ngan_chan', '=', 1);
        }
        if ($request->get('loai') == 2) {
            $data->where('suutranb.ngan_chan', '=', 2);
        }
        if ($request->get('loai') == 0) {
            $data->where('suutranb.ngan_chan', '=', 0);
            if ($request->khoidulieu) {
                $data->where('suutranb.ma_phan_biet', $request->get('khoidulieu'));
            }
            /* thống kê theo văn phòng */
            if ($request->get('theonvnv')) {
                $data->where('suutranb.vp_master', 'like', '%' . $request->get('theonvnv') . '%');
            }
            /* thống kê theo tên hợp đồng*/
            if ($request->get('theotenhd')) {
                $data->where('suutranb.ten_hd', 'like', '%' . $request->get('theotenhd') . '%');
            }
            /* thống kê theo công chứng viên */
            if ($request->get('theoccv')) {
                $data->where('suutranb.ccv_master', 'like', '%' . $request->get('theoccv') . '%');
            }
            /* thống kê theo ngày */
            if ($request->get('tungay') != '' && $request->get('denngay') == '') {
                $data->whereDate('suutranb.ngay_cc', '>=', $request->get('tungay'));
            }
            if ($request->get('tungay') == '' && $request->get('denngay') != '') {
                $data->whereDate('suutranb.ngay_cc', '<=', $request->get('denngay'));
            }
            if ($request->get('tungay') != '' && $request->get('denngay') != '') {
                $data->whereDate('suutranb.ngay_cc', '>=', $request->get('tungay'))
                    ->whereDate('suutranb.ngay_cc', '<=', $request->get('denngay'));
            }
        }
        $count = count($data->get());
        $data = $data->orderby('st_id', 'desc');
        return $data;
    }

    function export6Thang(Request $request)
    {
        $user_id = Sentinel::check()->id;
        $van_phong_id = NhanVienModel::find($user_id)->nv_vanphong;
        $ten_van_phong = ChiNhanhModel::find($van_phong_id)->cn_ten;
//        $dates = $request->get('dates');
//        dd($dates);
        $tungay = $request->get('tungay') ?? Carbon::now()->format('Y-m-d');
        $denngay = $request->get('denngay') ?? Carbon::now()->format('Y-m-d');
//        dd($tungay);
        $suutra = SuuTraModel::where('vp', '=', $van_phong_id)->whereDate('suutranb.ngay_cc', '>=', $tungay)
            ->whereDate('suutranb.ngay_cc', '<=', $denngay);
        $so_luong_nhan_vien = NhanVienModel::where("nv_vanphong", '=', $van_phong_id)->count();

        $so_chung_thuc_chu_ky = SuuTraModel::where('vp', '=', $van_phong_id)->whereDate('suutranb.ngay_cc', '>=', $tungay)
            ->whereDate('suutranb.ngay_cc', '<=', $denngay)
            ->where('suutranb.ten_hd', 'like', '%' . "chữ ký" . '%')->count();

        $so_hop_dong_giao_dich = SuuTraModel::where('vp', '=', $van_phong_id)->whereDate('suutranb.ngay_cc', '>=', $tungay)
                ->whereDate('suutranb.ngay_cc', '<=', $denngay)
                ->count() - $so_chung_thuc_chu_ky;

        $tong_so = SuuTraModel::where('vp', '=', $van_phong_id)->whereDate('suutranb.ngay_cc', '>=', $tungay)
                ->whereDate('suutranb.ngay_cc', '<=', $denngay)
                ->count() - $so_chung_thuc_chu_ky;
        $phi_chung_thuc_chu_ky = SuuTraModel::where('vp', '=', $van_phong_id)->whereDate('suutranb.ngay_cc', '>=', $tungay)
            ->whereDate('suutranb.ngay_cc', '<=', $denngay)
            ->where('suutranb.ten_hd', 'like', '%' . "chữ ký" . '%')->sum('phi_cong_chung');
        $phi_hop_dong_giao_dich = SuuTraModel::where('vp', '=', $van_phong_id)->whereDate('suutranb.ngay_cc', '>=', $tungay)
                ->whereDate('suutranb.ngay_cc', '<=', $denngay)
                ->sum('phi_cong_chung') - $phi_chung_thuc_chu_ky;
//        $phi_chung_thuc_chu_ky=SuuTraModel::
//        where('vp','=',$van_phong_id)
//            ->where('suutranb.ten_hd', 'like', '%' . "chữ ký" . '%')->sum('phi_cong_chung');
        $thu_lao_hop_dong_giao_dich = SuuTraModel::where('vp', '=', $van_phong_id)->whereDate('suutranb.ngay_cc', '>=', $tungay)
            ->whereDate('suutranb.ngay_cc', '<=', $denngay)
            ->sum('thu_lao');
        $data['so_luong_nhan_vien'] = $so_luong_nhan_vien;
        $data['so_chung_thuc_chu_ky'] = $so_chung_thuc_chu_ky;
        $data['so_hop_dong_giao_dich'] = $so_hop_dong_giao_dich;
        $data['phi_chung_thuc_chu_ky'] = $phi_chung_thuc_chu_ky;
        $data['phi_hop_dong_giao_dich'] = $phi_hop_dong_giao_dich;
        $data['thu_lao_hop_dong_giao_dich'] = $thu_lao_hop_dong_giao_dich;
        $data['tong_so'] = $tong_so;
        $data['ten_van_phong'] = $ten_van_phong;
        $tu_ngay = explode('-', $tungay);
        $den_ngay = explode('-', $denngay);
        $data['Y_tungay'] = $tu_ngay[0];
        $data['M_tungay'] = $tu_ngay[1];
        $data['D_tungay'] = $tu_ngay[2];
        $data['Y_denngay'] = $den_ngay[0];
        $data['M_denngay'] = $den_ngay[1];
        $data['D_denngay'] = $den_ngay[2];
        return Excel::download(new BaoCaoExportSheet($data), '12A.xlsx');

//        dd($phi_hop_dong_giao_dich);

    }
}
