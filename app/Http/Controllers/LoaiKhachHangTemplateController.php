<?php

namespace App\Http\Controllers;

use App\Models\KieuModel;
use App\Models\LoaiKhachHangTemplate;
use App\Models\Template;
use App\Models\User;
use App\Models\VanBanModel;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LoaiKhachHangTemplateController extends Controller
{
    use KhachHang;
    public function index()
    {
        $id_vp = User::join('nhanvien', 'nhanvien.nv_id', '=', 'id')
            ->join('chinhanh', 'chinhanh.cn_id', '=', 'nhanvien.nv_vanphong')
            ->where('users.id', Sentinel::getUser()->id)->first()->cn_id;
        $templates = LoaiKhachHangTemplate::with('loai_khach_hang')->where('id_vanphong','=',$id_vp)->get();
        if(count($templates)<1){
            $sample = LoaiKhachHangTemplate::with('loai_khach_hang')->whereNull('id_vanphong')->get();
            foreach ($sample as $item){
                $template = new LoaiKhachHangTemplate;
                $template->loai_khach_hang_id = $item->loai_khach_hang_id;
                $template->template = $item->template;
                $template->template_transformed = $item->template_transformed;
                $template->id_vanphong=$id_vp;
                $template->save();
            }
        }
        $kieuDuongSuID = KieuModel::whereKKeywords('duong-su')->first()->k_id;
        $loaiKhachHangCount = KieuModel::whereKParent($kieuDuongSuID)->count();
        $createDisable = $templates->count() >= $loaiKhachHangCount;
        return view('admin.templates.loai-khach-hang.index', compact('templates', 'createDisable'));
    }

    public function show($id)
    {

    }

    public function create(Request $request)
    {
        $kieuDuongSuID = KieuModel::whereKKeywords('duong-su')->first()->k_id;
        $loaiKhachHangExisted = LoaiKhachHangTemplate::pluck('loai_khach_hang_id');
        $loaiKhachHangList = KieuModel::whereKParent($kieuDuongSuID)
            ->whereNotIn('k_id', $loaiKhachHangExisted)
            ->get();
//        dd($loaiKhachHangList);
        return view('admin.templates.loai-khach-hang.create', compact('loaiKhachHangList'));
    }

    public function store(Request $request)
    {
        $id_vp = User::join('nhanvien', 'nhanvien.nv_id', '=', 'id')
            ->join('chinhanh', 'chinhanh.cn_id', '=', 'nhanvien.nv_vanphong')
            ->where('users.id', Sentinel::getUser()->id)->first()->cn_id;
        $template = new LoaiKhachHangTemplate;
        $template->loai_khach_hang_id = $request->loai_khach_hang_id;
        $template->template = $request->template;
        $template->id_vanphong=$id_vp;
        $tempStr = $template->template ?? '';
        while (str_contains($tempStr, '< ')) {
            $tempStr = str_replace('< ', '<', $tempStr);
        }
        while (str_contains($tempStr, ' >')) {
            $tempStr = str_replace(' >', '>', $tempStr);
        }
        $template->template = $tempStr;

        preg_match_all('/\<(.*?)\>/s', $tempStr, $matches);
        foreach ($matches[0] as $key => $match) {
            $tempNeedle = '<' . Str::slug($match) . '>';
            $tempStr = str_replace($match, $tempNeedle, $tempStr);
        }
        $template->template_transformed = $tempStr;
        $template->save();

        return redirect()->route('admin.templates.loai-khach-hang.index');
    }

    public function edit($id, Request $request)
    {
        $template = LoaiKhachHangTemplate::with('loai_khach_hang')->find($id);
        $loaiKhachHangId = $template->loai_khach_hang_id;
        $tieuMucArr = KieuModel::find($loaiKhachHangId);
        $tieuMucList = [];
        if ($tieuMucArr) {
            $tieuMucList = $this->list_tieumuc_form(explode(' ', $tieuMucArr->k_tieumuc), $loaiKhachHangId)->where('tm_loai', '!=', 'file')->pluck('tm_nhan');
        }
        return view('admin.templates.loai-khach-hang.edit', compact('template', 'tieuMucList'));
    }

    public function update($id, Request $request)
    {
        $template = LoaiKhachHangTemplate::with('loai_khach_hang')->find($id);
        $tempStr = $request->template ?? '';
        while (str_contains($tempStr, '< ')) {
            $tempStr = str_replace('< ', '<', $tempStr);
        }
        while (str_contains($tempStr, ' >')) {
            $tempStr = str_replace(' >', '>', $tempStr);
        }
        $template->template = $tempStr;

        preg_match_all('/\<(.*?)\>/s', $tempStr, $matches);
        foreach ($matches[0] as $key => $match) {
            $tempNeedle = '<' . Str::slug($match) . '>';
            $tempStr = str_replace($match, $tempNeedle, $tempStr);
        }
        $template->template_transformed = $tempStr;
        $template->save();

        return redirect()->route('admin.templates.loai-khach-hang.index');
    }

    public function delete($id) {
        $status = LoaiKhachHangTemplate::find($id)->delete();
        if ($status) {
            return json_encode([
                'code' => 200,
                'status' => true,
                'message' => 'Xóa thành công'
            ]);
        }
        return json_encode([
            'code' => 400,
            'status' => false,
            'message' => 'Xóa không thành công'
        ]);
    }

    public function convertToText(Request $request) {
        $loaiKhachHangId = $request->loai_khach_hang_id;
        $template = LoaiKhachHangTemplate::whereLoaiKhachHangId($loaiKhachHangId)->first();
        if (!$template) {
            return response()->json([
                'data' => '',
                'message' => 'Không tìm thấy template cho loại hợp đồng này'
            ]);
        }
        return response()->json([
            'data' => $template->convertToText($request),
            'message' => ''
        ]);
    }
}
