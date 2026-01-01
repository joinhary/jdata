<?php

namespace App\Http\Controllers;

use App\Models\ChiNhanhModel;
use App\Models\ThongBaoChung;
use PHPUnit\Framework\Constraint\Count;
use Sentinel;
use Illuminate\Http\Request;
use App\Models\NhanVienModel;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Str;


class ThongBaoChungController extends Controller
{
    public function randomCode($prefix, $length)
    {
        $code = Str::random($length) . rand(100, 999);
        $code = $prefix . str_shuffle($code);
        return $code;
    }
    public function adminIndex()
    {
        $id = NhanVienModel::where('nv_id', Sentinel::getUser()->id)->first()->nv_vanphong;
        $data = ThongBaoChung::orderBy('created_at', 'desc')->get();
        $data_chan = ThongBaoChung::orderBy('created_at', 'desc')->where('type', 1)->get();

        $tbc_user = ThongBaoChung::leftjoin('chinhanh', 'chinhanh.cn_id', 'vp_id')
            ->where('cn_id', $id)
            ->orwhere('vp_id', 0)
            ->where('type', 0)
            ->orderBy('thong_bao_chung.created_at', 'desc')
            ->get();
        $tbc_user_chan = ThongBaoChung::leftjoin('chinhanh', 'chinhanh.cn_id', 'vp_id')
            ->where('cn_id', $id)
            ->orwhere('vp_id', 0)
            ->where('thong_bao_chung.type', '=', 1)
            ->orderBy('thong_bao_chung.created_at', 'desc')
            ->get();

        return view('home', compact('data', 'data_chan', 'tbc_user', 'tbc_user_chan'));
    }

    public  function convert_vi_to_en($str)
    {
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", "a", $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", "e", $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", "i", $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", "o", $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", "u", $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", "y", $str);
        $str = preg_replace("/(đ)/", "d", $str);
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", "A", $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", "E", $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", "I", $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", "O", $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", "U", $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", "Y", $str);
        $str = preg_replace("/(Đ)/", "D", $str);
        return $str;
    }

    public function index(Request $request)
    {
        $tu_ngay = ($request->tu_ngay ?? false) ? Carbon::parse($request->tu_ngay)->format('m/d/Y') : '';
        $den_ngay = ($request->den_ngay ?? false) ? Carbon::parse($request->den_ngay)->format('m/d/Y') : '';
        $nv_id = $request->nv_id ?? '';
        $vp_id = $request->vp_id ?? '';
        $user = Sentinel::getUser();

        if (!$user) {
            return redirect('admin/signin');
        }
        $nhan_vien_all = NhanVienModel::all();
        $chi_nhanh_all = ChiNhanhModel::all();
        $van_phong_id = $nhan_vien_all->find($user->id)->nv_vanphong;
        $nhan_vien = $nhan_vien_all;
        $thong_bao_chung = ThongBaoChung::where('id', '!=', '');
        if ($request->type == 1) {
            $thong_bao_chung = $thong_bao_chung->where('thong_bao_chung.type', 1);
        }
        // -- search bar --
        if ($nv_id != '') {
            $thong_bao_chung = $thong_bao_chung->where('thong_bao_chung.nv_id', $nv_id);
        }

        if ($vp_id != '') {
            $thong_bao_chung = $thong_bao_chung->where('thong_bao_chung.vp_id', $vp_id);
        }

        if ($tu_ngay != '') {
            $thong_bao_chung = $thong_bao_chung->whereDate('thong_bao_chung.created_at', '>=', Carbon::parse($tu_ngay)->format('Y-m-d'));
        }

        if ($den_ngay != '') {
            $thong_bao_chung = $thong_bao_chung->whereDate('thong_bao_chung.created_at', '<=', Carbon::parse($den_ngay)->format('Y-m-d'));
        }
        $thong_bao_chung->orderBy('id', 'desc');
        $count = count($thong_bao_chung->get());
        //        dd($count);
        $thong_bao_chung = $thong_bao_chung->paginate(10);
        return view('admin.thong_bao_chung.index', compact('thong_bao_chung', 'nhan_vien', 'count', 'tu_ngay', 'den_ngay', 'nv_id', 'chi_nhanh_all', 'user'));
    }

   public function create(Request $request)
{
    $chi_nhanh_all = ChiNhanhModel::all();
    $push = collect([
        '' => 'Không hiển thị',
        '3' => '3 ngày',
        '5' => '5 ngày',
        '7' => '7 ngày',
        '30' => '30 ngày',
        '9999' => 'Luôn hiển thị',
    ]);

    return view('admin.thong_bao_chung.create', compact('chi_nhanh_all', 'push'));
}
    public function addImage(Request $request, $path = 'public/upload_thongbao', $input = 'file')
    {
        $save_path = $path;
        $i = 0;
        $choosen_img = [];
        $image = $request->file($input);

        foreach ($image as $item) {
            $img_name = 'ThongBao' . '_' . time() . '_' .  $i . '.' . $item->getClientOriginalExtension();
            $choosen_img[] = $img_name;
            $item->storeAs('public/upload_thongbao', $img_name);
            $i++;
        }
        return $choosen_img;
    }
    public function getRealName(Request $request, $path = 'public/upload_thongbao', $input = 'file')
    {
        $save_path = $path;
        $i = 0;
        $choosen_img = [];
        $image = $request->file($input);

        foreach ($image as $item) {
            $img_name = $item->getClientOriginalName();
            $choosen_img[] = $img_name;
        }
        return $choosen_img;
    }

    public function store(Request $request)
    {
        $user = Sentinel::getUser();
        $thong_bao_chung = new ThongBaoChung;
        $type = 0;
        if ($request->type) {
            $type = 1;
        }
        $thong_bao_chung->tieu_de = $request->tieu_de ?? '';
        $thong_bao_chung->noi_dung = $request->noi_dung ?? '';
        $thong_bao_chung->nv_id = $user->id ?? 0;
        $thong_bao_chung->vp_id = $request->vp_id ?? 0;
        $thong_bao_chung->type = $type;
        $thong_bao_chung->push = $request->push;
        $thong_bao_chung->so_cv = $request->so_cv ?? '';
        $thong_bao_chung->duong_su = $request->duong_su ?? '';
        $thong_bao_chung->texte = $request->texte ?? '';
        $thong_bao_chung->ma_dong_bo = $this->randomCode('TBC', 4);
        $thong_bao_chung->duong_su_en = $this->convert_vi_to_en($request->duong_su) ?? '';
        $thong_bao_chung->texte_en = $this->convert_vi_to_en($request->texte) ?? '';
        if ($request->hasFile('file')) {
            $pic = json_encode($this->addImage($request, 'public/upload_thongbao', 'file'));
            $realName = json_encode($this->getRealName($request, 'public/upload_thongbao', 'file'));
            // $file = $request->file('file');
            // $ext = $file->extension();
            // $file_name = $request->vp_id . '_' . 'Thongbao' . '_' . time() . '.' . $ext;
            // $file->storeAs('public/upload_thongbao', $file_name);
            $thong_bao_chung->file = $pic;
            $thong_bao_chung->realname = $realName;

        }
        $thong_bao_chung->merge_content = $this->convert_vi_to_en($request->duong_su) . ' ' . $this->convert_vi_to_en($request->texte) . ' ' . $request->tieu_de . ' ' . $request->noi_dung;
        $thong_bao_chung->save();

        //solr deloy
        $this->insert_solr(ThongBaoChung::find($thong_bao_chung->id));

        //Hoicongchung deloy
      
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'http://stp2.hoicongchungviencantho.org/api/store-thong-bao-api',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($thong_bao_chung),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
            ));
            $response = curl_exec($curl);
     
            curl_close($curl);
        
        if ($request->type) {
            return redirect(route('createSuutraSTP'))->with('success', 'Tạo thông báo thành công! Hãy tạo hồ sơ ngăn chặn mới!');
        } else {
            return redirect(route('adminIndex'))->with('success', 'Tạo thông báo thành công !');
        }
    }

    public function edit($id)
    {
        $chi_nhanh_all = ChiNhanhModel::all()->pluck('cn_ten', 'cn_id')->prepend('Tất cả', '');
        $thongbaochung = ThongBaoChung::find($id);
        $push = collect([
            '' => 'Không hiển thị',
            '3' => '3 ngày',
            '5' => '5 ngày',
            '7' => '7 ngày',
            '30' => '30 ngày',
            '9999' => 'Luôn hiển thị',
        ]);
        return view('admin.thong_bao_chung.edit', compact('chi_nhanh_all', 'thongbaochung', 'push'));
    }

    public function update(Request $request, $id)
    {
        $user = Sentinel::getUser();
        $type = 0;
        if ($request->type) {
            $type = 1;
        }
        $tbc = ThongBaoChung::where('id',$id)->first()->update([
            'tieu_de' => $request->tieu_de,
            'noi_dung' => $request->noi_dung,
            'vp_id' => $request->vp_id ?? 0,
            'nv_id' => $user->id,
            'type' => $type,
            'push' => $request->push,
            'so_cv' => $request->so_cv ?? '',
            'duong_su' => $request->duong_su ?? '',
            'texte' => $request->texte ?? '',
            'duong_su_en' => $this->convert_vi_to_en($request->duong_su) ?? '',
            'texte_en' => $this->convert_vi_to_en($request->texte) ?? '',
            'merge_content' => $this->convert_vi_to_en($request->duong_su) . ' ' . $this->convert_vi_to_en($request->texte) . ' ' . $request->tieu_de . ' ' . $request->noi_dung,
        ]);
        if ($request->hasFile('file')) {
            $pic = json_encode($this->addImage($request, 'public/upload_thongbao', 'file'));
            $realName = json_encode($this->getRealName($request, 'public/upload_thongbao', 'file'));
            // $file->storeAs('storage/app/upload_thongbao', $file_name);
            ThongBaoChung::find($id)->update([
                'file' => $pic,
                'realname' =>$realName,
            ]);
        }
        $this->delete_solr($id);
        $this->insert_solr(ThongBaoChung::find($id));
            
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://stp2.hoicongchungviencantho.org/api/update-thong-bao-api/' . ThongBaoChung::where('id',$id)->first()->ma_dong_bo,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode(ThongBaoChung::where('id',$id)->first()),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl); 
        curl_close($curl);
        return redirect(route('adminIndex'))->with('success', 'Cập nhật thông báo thành công !');
    }

    public function delete(Request $request)
    {


        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://stp2.hoicongchungviencantho.org/api/delete-thong-bao-api/' . ThongBaoChung::where('id',$request->id)->first()->ma_dong_bo,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode(ThongBaoChung::where('id',$request->id)->first()),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl); 
        curl_close($curl);
        ThongBaoChung::find($request->id)->delete();
        $this->delete_solr($request->id);
        return redirect(route('adminIndex'))->with('success', 'Xóa thông báo thành công !');
    }

    public function show($id)
    {
        $tbc = ThongBaoChung::find($id);
        if ($tbc->vp_id == 0) {
            $tbc->vp_name = 'Tất cả';
        } else {
            $tbc->vp_name = ChiNhanhModel::find($tbc->vp_id)->cn_ten;
        }
        return view('admin.thong_bao_chung.show', compact('tbc'));
    }
    public static function insert_solr($data)
    {
        // dd(json_encode($data1, JSON_UNESCAPED_UNICODE));
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://localhost:8983/solr/thongbaochung/update/json/docs?commit=true',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
    }
    public static function delete_solr($st_id)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://localhost:8983/solr/thongbaochung/update?_=1659089839080&commitWithin=1000&overwrite=true&wt=json',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => '<delete>   
            <query>id:' . $st_id . '</query>   
         </delete>  ',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/xml'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
    }
}
