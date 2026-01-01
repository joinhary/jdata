<?php

namespace App\Http\Controllers;

use App\Models\KieuModel;
use App\Models\LoaiHopDongTemplate;
use App\Models\User;
use App\Models\VanBanModel;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LoaiHopDongTemplateController extends Controller
{
    public function index(Request $request)
    {
        $id_vp = User::join('nhanvien', 'nhanvien.nv_id', '=', 'id')
            ->join('chinhanh', 'chinhanh.cn_id', '=', 'nhanvien.nv_vanphong')
            ->where('users.id', Sentinel::getUser()->id)->first()->cn_id;
        $val=$request->vb_nhan;
        $templates = LoaiHopDongTemplate::with('loai_hop_dong')
            ->join('vanban','vanban.vb_id','=','loai_hop_dong_id')
            ->where('vb_nhan', 'like', '%' . $val . '%')
            ->where('id_vanphong','=',2050)->get();
        if(count($templates)<1){
            $data = VanBanModel::join('kieu_hop_dong', 'kieu_hop_dong.lien_ket_id', '=', 'vanban.vb_kieuhd')
                ->where('vanban.id_vp', $id_vp)
                ->select(
                    'vanban.vb_id',
                    'vanban.vb_loai',
                    'vanban.vb_nhan',
                    'kieu_hop_dong.kieu_hd'
                )->distinct('vanban.vb_id')->get();
            foreach ($data as $item){
                $template = new LoaiHopDongTemplate;
                $template->loai_hop_dong_id = $item->vb_id;
                $template->template = "".'/.';
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
                $template->template_transformed = $tempStr.'/.';
                $template->save();
            }
        }



        return view('admin.templates.loai-hop-dong.index', compact('templates','val'));
    }

    public function show($id)
    {

    }

    public function create(Request $request)
    {
        $loaiHopDongExisted = LoaiHopDongTemplate::pluck('loai_hop_dong_id');
        $loaiHopDongList = VanBanModel::whereNotIn('vb_id', $loaiHopDongExisted)->get();
        return view('admin.templates.loai-hop-dong.create', compact('loaiHopDongList'));
    }

    public function store(Request $request)
    {
        $id_vp = User::join('nhanvien', 'nhanvien.nv_id', '=', 'id')
            ->join('chinhanh', 'chinhanh.cn_id', '=', 'nhanvien.nv_vanphong')
            ->where('users.id', Sentinel::getUser()->id)->first()->cn_id;
        $template = new LoaiHopDongTemplate;
        $template->loai_hop_dong_id = $request->loai_hop_dong_id;
        $template->template = $request->template.'/.';
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
        $template->template_transformed = $tempStr.'/.';
        $template->save();

        return redirect()->route('admin.templates.loai-hop-dong.index');
    }

    public function edit($id, Request $request)
    {
        $template = LoaiHopDongTemplate::with('loai_hop_dong')->find($id);
        return view('admin.templates.loai-hop-dong.edit', compact('template'));
    }

    public function update($id, Request $request)
    {
        $template = LoaiHopDongTemplate::with('loai_hop_dong')->find($id);
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

        return redirect()->route('admin.templates.loai-hop-dong.index');
    }

    public function delete($id) {
        $status = LoaiHopDongTemplate::find($id)->delete();
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
        $loaiHopDongId = $request->loai_hop_dong_id;
        $id_vanphong=$request->id_vanphong;
        $template = LoaiHopDongTemplate::whereLoaiHopDongId($loaiHopDongId)->where('id_vanphong','=',$id_vanphong)->first();
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
