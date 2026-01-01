<?php

namespace App\Http\Controllers;

use App\Models\KieuModel;
use App\Models\Template;
use App\Models\TieuMucModel;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TemplateController extends Controller
{
    public function index(Request $request)
    {
        $id_vp = User::join('nhanvien', 'nhanvien.nv_id', '=', 'id')
            ->join('chinhanh', 'chinhanh.cn_id', '=', 'nhanvien.nv_vanphong')
            ->where('users.id', Sentinel::getUser()->id)->first()->cn_id;
        $templates = Template::with('kieu_tai_san')->where('id_vanphong','=',2050)->get();
       if(count($templates)<1){
           $sample = Template::with('kieu_tai_san')->whereNull('id_vanphong')->get();
            foreach ($sample as $item){
                $template = new Template;
                $template->type = 'tai-san';
                $template->slug = $item->slug;
                $template->template = $item->template;
                $template->template_transformed = $item->template_transformed;
                $template->id_vanphong=$id_vp;
                $template->save();
            }
       }
        $kieuTaiSanID = KieuModel::whereKKeywords('tai-san')->first()->k_id;
        $loaiTaiSanCount = KieuModel::whereKParent($kieuTaiSanID)->count();
        $createDisable = $templates->count() >= $loaiTaiSanCount;
        return view('admin.templates.tai-san.index', compact('templates', 'createDisable'));
    }

    public function show($id)
    {

    }

    public function create(Request $request)
    {
        $existedSlug = Template::pluck('slug');
        $validIds = KieuModel::has('parent')
		
//            ->doesntHave('children')
            ->pluck('k_id');
//                dd($validIds);
        $kieuTaiSan = KieuModel::whereKKeywords('tai-san')->first()->getAllChildren()
            ->where('k_trangthai', 1)
            ->whereNotIn('k_keywords', $existedSlug)
            ->whereIn('k_id', $validIds);
        return view('admin.templates.tai-san.create', compact('kieuTaiSan'));
    }

    public function store(Request $request)
    {
        $id_vp = User::join('nhanvien', 'nhanvien.nv_id', '=', 'id')
            ->join('chinhanh', 'chinhanh.cn_id', '=', 'nhanvien.nv_vanphong')
            ->where('users.id', Sentinel::getUser()->id)->first()->cn_id;
        $template = new Template;
        $template->type = 'tai-san';
        $template->slug = $request->slug;
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

        return redirect()->route('admin.templates.tai-san.index');
    }

    public function edit($id, Request $request)
    {
        $template = Template::with('kieu_tai_san')->find($id);
        $kieuTaiSanId = KieuModel::whereKKeywords($template->slug)->first()->k_id;
        $tieuMucString = KieuModel::select('k_tieumuc')->where('k_id', $kieuTaiSanId)->first()->k_tieumuc;
        $tieuMucList = [];
        if ($tieuMucString) {
            $tieuMucList = TieuMucModel::leftjoin('tieumuc_sapxep', 'tieumuc_sapxep.tm_id', '=', 'tieumuc.tm_id')
                ->where('tieumuc_sapxep.k_id', $kieuTaiSanId)
                ->whereIn('tieumuc.tm_id', explode(' ', $tieuMucString))
                ->orderBy('tieumuc_sapxep.tm_sort', 'asc')
                ->select([
                    'tieumuc.tm_id',
                    'tm_nhan',
                    'tm_keywords',
                    'tm_loai',
                    'tm_batbuoc',
                    'tm_sort',
                ])->where('tm_loai', '!=', 'file')->pluck('tm_nhan');
        }
        return view('admin.templates.tai-san.edit', compact('template', 'tieuMucList'));
    }

    public function update($id, Request $request)
    {
//        dd($request);
        $template = Template::with('kieu_tai_san')->find($id);
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

        return redirect()->route('admin.templates.tai-san.index');
    }

    public function delete($id)
    {
        $status = Template::find($id)->delete();
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
}
