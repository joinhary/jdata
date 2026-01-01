<?php

namespace App\Http\Controllers;

use Kieuhopdong;
use App\Models\TaiSanModel;
use App\Models\NhanVienModel;
use App\Models\SuuTraModel;
use App\Models\User;
use App\Models\VanBanModel;
use Carbon\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CongVanNganChanController extends Controller
{
    use ImageHandling;

    public function create(Request $request)
    {
        $nhomHopDong = Kieuhopdong::all();
        $tenhd = VanBanModel::where('vb_kieuhd', $nhomHopDong->first()->id ?? '')->where('vb_nhan', 'like',
            'M%')->get();
        return view('admin.suutra.cong-van-ngan-chan.create', compact('tenhd', 'nhomHopDong'));
    }

    public function edit(Request $request, $id)
    {
        $congVanNganChan = SuuTraModel::find($id);
        $congVanNganChan->ngay_cc = Carbon::createFromFormat('d/m/Y', $congVanNganChan->ngay_cc)->format('Y-m-d');

        $selectedKieuHopDong = VanBanModel::find($congVanNganChan->van_ban_id);
        $nhomHopDong = Kieuhopdong::all();
        $selectedNhomHopDong = $nhomHopDong->where('id', $selectedKieuHopDong->vb_kieuhd)->first();
        $tenhd = VanBanModel::where('vb_kieuhd', $selectedNhomHopDong->id)->where('vb_nhan', 'like', 'M%')->get();

        $duongSuIds = json_decode($congVanNganChan->duong_su_ids, true);
        $duongSu = [
            'benA' => [],
            'benB' => [],
            'benC' => []
        ];
        foreach ($duongSuIds as $key => $duongSuArr) {
            foreach ($duongSuArr as $duongSuId) {
                $ds = User::find($duongSuId);
                $dsInfo = $ds->info();
                $ds->thong_tin_str = $dsInfo['thong_tin_str'];
                $ds->thong_tin_arr = $dsInfo['thong_tin_arr'];
                $duongSu[$key][] = $ds;
            }
        }

        $taiSanIds = json_decode($congVanNganChan->tai_san_ids, true);
        $taiSan = [];
        foreach ($taiSanIds as $taiSanId) {
            $ts = TaiSanModel::select('ts_id', 'ts_id as id', 'ts_nhan as text')->where('ts_trangthai', '=',
                '1')->find($taiSanId);
            $tsInfo = $ts->info();
            $ts->thong_tin_str = $tsInfo['thong_tin_str'];
            $ts->thong_tin_arr = $tsInfo['thong_tin_arr'];
            $taiSan[] = $ts;
        }
        return view('admin.suutra.cong-van-ngan-chan.edit',
            compact('tenhd', 'nhomHopDong', 'congVanNganChan', 'duongSu', 'taiSan', 'selectedNhomHopDong'));
    }

    public function update(Request $request, $id)
    {
        $vp = NhanVienModel::find($request->id_ccv)->nv_vanphong;
        $ngayCc = date("d/m/Y", strtotime($request->ngay_cc));
        $ten_hd = Kieuhopdong::find($request->kieu_hop_dong_id)->kieu_hd . ': ' . VanBanModel::find($request->van_ban_id)->vb_nhan;

        if ($request->hasFile('pic')) {
            $choosen_img = [];
            $nameRoot = $request->st_id;
            foreach ($request->file('pic') as $key => $item) {
                $img_path = 'CVNC/' . $nameRoot;
                $img_name = 'pic_' . ($key + 1) . '_' . Carbon::now()->format('Ymdhis') . '.' . $item->getClientOriginalExtension();
                $choosen_img[] = 'storage/' . $img_path . '/' . $img_name;
                Storage::disk('public')->putFileAs($img_path, $item, $img_name);
            }
            $pic = json_encode($choosen_img);
        }

        SuuTraModel::find($id)->update([
            'ccv' => $request->id_ccv,
            'ngay_cc' => $ngayCc,
            'ten_hd' => $ten_hd,
            'texte' => SuuTraController::cleanSpaces($request->noidung),
            'duong_su' => SuuTraController::cleanSpaces($request->duongsu),
            'nam_hieu_luc' => $request->nam_hl,
            'ngay_hieu_luc' => $request->ngay_hl,
            'ngan_chan' => $request->loai,
            'vp' => $vp,
            'ngay_nhap' => Carbon::today()->format('d/m/Y'),
            'van_ban_id' => $request->van_ban_id,
            'tai_san_ids' => $request->tai_san,
            'duong_su_ids' => $request->duong_su
        ]);

        if (isset($pic)) {
            SuuTraModel::find($id)->update([
                'picture' => $pic
            ]);
        }

        return redirect()->route('indexSuuTra')->with('success', 'Đã import file thành công');
    }

    public function listKieuVanBan(Request $request)
    {
        return VanBanModel::where('vb_kieuhd', $request->id ?? '')->where('vb_nhan', 'like', 'M%')->get();
    }

    public function store(Request $request)
    {
        $pic = null;
        $year = date("Y", strtotime($request->ngay_cc));
        $sohd = $request->so_hd . '/' . $year;
        // Tìm văn phòng người tạo
        $vp = NhanVienModel::find($request->id_ccv)->nv_vanphong;
        $suuTra = SuuTraModel::where('so_hd', $sohd)->first();
        if ($suuTra && $suuTra->vp == $vp) {
            return back()->with('error', 'Mã hợp đồng đã tồn tại');
        }

        $ten_hd = Kieuhopdong::find($request->kieu_hop_dong_id)->kieu_hd . ': ' . VanBanModel::find($request->van_ban_id)->vb_nhan;

        $ngayCc = date("d/m/Y", strtotime($request->ngay_cc));
        $checked = null;
        if (Sentinel::check()->isPC()) {
            $checked = 1;
        }

        $obj = SuuTraModel::create([
            'ccv' => $request->id_ccv,
            'ngay_cc' => $ngayCc,
            'so_hd' => $sohd,
            'ten_hd' => $ten_hd,
            'texte' => SuuTraController::cleanSpaces($request->noidung),
            'duong_su' => SuuTraController::cleanSpaces($request->duongsu),
            'nam_hieu_luc' => $request->nam_hl,
            'ngay_hieu_luc' => $request->ngay_hl,
            'ngan_chan' => $request->loai,
            'vp' => $vp,
            'ngay_nhap' => Carbon::today()->format('d/m/Y'),
            'picture' => $pic,
            'chu_y' => $request->chu_y,
            'status' => $checked,
            'van_ban_id' => $request->van_ban_id,
            'tai_san_ids' => $request->tai_san,
            'duong_su_ids' => $request->duong_su
        ]);


        if ($request->hasFile('pic')) {
            $choosen_img = [];
            $nameRoot = $obj->st_id;
            foreach ($request->file('pic') as $key => $item) {
                $img_path = 'CVNC/' . $nameRoot;
                $img_name = 'pic_' . ($key + 1) . '.' . $item->getClientOriginalExtension();
                $choosen_img[] = 'storage/' . $img_path . '/' . $img_name;
                Storage::disk('public')->putFileAs($img_path, $item, $img_name);
            }
            $pic = json_encode($choosen_img);
            $obj->picture = $pic;
            $obj->save();
        }
        return redirect()->route('indexSuuTra')->with('success', 'Đã import file thành công');
    }

    public static function thongTinDuongSu(Request $request)
    {
        $id = $request->id;
        $data = User::find($id)->info();
        $thongTinStr = $data['thong_tin_str'];
        $thongTinArr = $data['thong_tin_arr'];
        $lichSuHonNhanArr = $data['lich_su_hon_nhan'];

        return json_encode([
            'thong_tin_str' => $thongTinStr,
            'thong_tin_arr' => $thongTinArr,
            'lich_su_hon_nhan_arr' => $lichSuHonNhanArr,
        ]);
    }

    public static function thongTinTaiSan(Request $request)
    {
        $id = $request->id;
        $data = TaiSanModel::find($id)->info();
        return json_encode([
            'thong_tin_arr' => $data['thong_tin_arr'],
            'thong_tin_str' => $data['thong_tin_str']
        ]);
    }

    public static function listDuongSu(Request $request)
    {
        $where = [];
        if ($request->search_str) {
            $where[] = ['first_name', 'LIKE', '%' . $request->search_str . '%'];
        }
        $role_kh = Sentinel::findRoleBySlug('khach-hang');
        $listDuongSu = $role_kh->users()->where($where)->get()->map(function ($item) {
            $data = $item->info();
            $item->thong_tin_str = $data['thong_tin_str'];
            return $item;
        });
        return ['status' => true, 'data' => $listDuongSu];
    }
}
