<?php

namespace App\Http\Controllers;

use App\Models\ChiNhanhModel;
use App\Models\DoanModel;
use App\Http\Requests\VanBanRequest;
use App\Models\KieuHopDongMode;
use App\Models\LoaiHopDongTemplate;
use App\Models\Kieuhopdong;
use App\Models\Admin\Builder\vaitro;
use App\Http\Controllers\SuuTraController;
use App\Models\User;
use App\Models\VanBanDoanModel;
use App\Models\VanBanModel;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Ixudra\Curl\Facades\Curl;
use Laracasts\Flash\Flash;
use App\Models\NhanVienModel;
class VanBanController extends Controller
{
    public function index(Request $request)
    {
        $role = Sentinel::check()->user_roles()->first()->slug;
        $str_json = json_encode([]);
        $getNangCao = $request->get('nangcao');
        $getKieu = $request->get('vb_kieuhd');
        $kieu_hd = Kieuhopdong::pluck('kieu_hd', 'id')->prepend('------', '');;
        $nangcao = explode(' ', (substr(str_replace('%', ' ', $request->get('nangcao')), 0)));
        $kieu = explode(' ', (substr(str_replace('%', ' ', $request->get('vb_kieuhd')), 0)));
        $search = array_merge($nangcao, $kieu);
        $id_vp = User::join('nhanvien', 'nhanvien.nv_id', '=', 'id')
            ->join('chinhanh', 'chinhanh.cn_id', '=', 'nhanvien.nv_vanphong')
            ->where('users.id', Sentinel::getUser()->id)->first()->cn_id;
        $data = VanBanModel::join('kieu_hop_dong', 'kieu_hop_dong.lien_ket_id', '=', 'vanban.vb_kieuhd')
//            ->where('kieu_hop_dong.id_vp', $id_vp)
            ->select(
                'vanban.vb_id',
                'vanban.vb_loai',
                'vanban.vb_nhan',
                'kieu_hop_dong.kieu_hd'
            )->distinct('vanban.vb_id');
        $tong = count($data->get());
        if ($getKieu && $getNangCao == null) {
            $data = $data->where('kieu_hop_dong.id', '=', $getKieu);
        }

        if ($getNangCao && $getKieu == null) {
            foreach ($nangcao as $val) {
                $data = $data->where('vb_nhan', 'like', '%' . $val . '%');
//                    ->orwhere('vb_nhan_en', 'like', '%' . $val . '%');
                $str_json = json_encode(array_filter(array(str_replace("%", " ", $nangcao))));
            }
        }

        if ($getKieu && $getNangCao) {
            foreach ($nangcao as $val) {
                $data = $data->where('kieu_hop_dong.id', '=', $getKieu)
                    ->where('vb_nhan_en', 'like', '%' . $val . '%');
                $str_json = json_encode(array_filter(array(str_replace("%", " ", $nangcao))));
            }
        } else {
//            $data = $data->where("vanban.vb_loai", '=', 1);
        }

        if ($role == 'admin' || $role == 'chuyen-vien-so') {

            $data = $data->where('vanban.id_vp', 2020)->orderby('vb_id', 'desc')->paginate(10);
               

		} else {
            $data = $data->where('vanban.id_vp', 2020);

            $data = $data->orderby('vb_id', 'desc')->paginate(10);
        }
		 $count = count($data);
        $vanban = $data;
        return view('admin.vanban.index', compact('vanban', 'tong', 'count', 'str_json', 'getNangCao', 'getKieu', 'kieu_hd'));
    }


    public function lists(Request $request)
    {
        $doan = DoanModel::where('d_vaitro_fk', 0);

        return datatables()->of($doan)->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $id_vp = User::join('nhanvien', 'nhanvien.nv_id', '=', 'id')
            ->join('chinhanh', 'chinhanh.cn_id', '=', 'nhanvien.nv_vanphong')
            ->where('users.id', Sentinel::getUser()->id)->first()->cn_id;
        $kieuhd = Kieuhopdong::where('id_vp', 2020)->pluck('kieu_hd', 'id');
        // dd(1);
        return view('admin.vanban.create', compact('kieuhd'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    use users;

    public function store(VanBanRequest $request)
    {
		$id_vp = NhanVienModel::find(Sentinel::getUser()->id)->nv_vanphong;
        $vanban = new VanBanModel();
        $vanban->vb_nhan = $request->vb_nhan;
        $vanban->vb_kieuhd = $request->vb_kieuhd;
        $vanban->vb_loai = 0;
        $vanban->vb_nhan_en = SuuTraController::convert_vi_to_en($request->vb_nhan);
		$vanban->id_vp='2020';
        $vanban->save();
		$vb=VanBanModel::find($vanban->vb_id);
		$vb->lien_ket=$vanban->vb_id;
		$vb->save();
        $i = 1;
        $user_exec = Sentinel::getUser()->id;
        $description = "Thêm văn bản";
        $this->api_create_log($user_exec, $description);

        return Redirect(route('indexVB'))->with('success', 'Tạo văn bản thành công');
    }

    public function updates(VanBanRequest $request)
    {
        $vanban = new VanBanModel();

        $vanban->vb_nhan = $request->vb_nhan;
        $vanban->vb_kieuhd = $request->vb_kieuhd;
        $vanban->vb_loai = $request->vb_loai;
        $vanban->vb_nhan_en = SuuTraController::convert_vi_to_en($request->vb_nhan);
        $vanban->save();
        $i = 1;
        /*Ghi log*/
        $user_exec = Sentinel::getUser()->id;
        $description = "Thêm văn bản";
        $this->api_create_log($user_exec, $description);

        return Redirect::route('createVBs2', $vanban->vb_id)->with('success', 'Tạo văn bản thành công!');
    }

    public function validate_vb_create($request)
    {
        $validator = Validator::make($request->all(), [
            'vb_nhan' => 'required | unique:vanban,vb_nhan',
            'vb_kieuhd' => 'required',
        ], [
            'vb_nhan.required' => 'Nhãn văn bản không được trống!!',
            'vb_nhan.unique' => 'Nhãn văn bản đã tồn tại',
            'vb_kieuhd.required' => 'Vui lòng chọn kiểu hợp đồng!!',
        ]);
        return $validator;
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        // dd($id);
        $vanban = VanBanModel::find($id);
        // dd($vanban);
        $id_vp = User::join('nhanvien', 'nhanvien.nv_id', '=', 'id')
            ->join('chinhanh', 'chinhanh.cn_id', '=', 'nhanvien.nv_vanphong')
            ->where('users.id', Sentinel::getUser()->id)->first()->cn_id;
            // dd($id_vp);
        $kieuHD = Kieuhopdong::where('id', $vanban->vb_kieuhd)->pluck('kieu_hd', 'id');
        // dd( $kieuHD);
        return view('admin.vanban.edit', compact('vanban', 'kieuHD'));
    }

    public function creates2($id)
    {
        $kieuHD = Kieuhopdong::pluck('kieu_hd', 'id');
        $vanban = VanBanModel::find($id);
        $vb_doan = VanBanDoanModel::where('vb_idfk', $id)
            ->join('doan', 'doan.d_number', '=', 'd_idfk')->where('haveparent', 0)
            ->orderby('sort', 'asc')->get();
        return view('admin.vanban.creates2', compact('vanban', 'vb_doan', 'kieuHD'));
    }

    public function stores2(Request $request, $id)
    {
        $vb_doan = VanBanDoanModel::where('vb_idfk', $id)->get();
        foreach ($vb_doan as $doan) {
            VanBanDoanModel::where('vb_idfk', $id)
                ->where('d_idfk', $doan->d_idfk)
                ->update(['vaitro' => $request->get('vaitro_' . $doan->d_idfk)]);
        }
        return Redirect(route('indexVB'))->with('success', 'Cập nhật văn bản thành công');
    }

    public function update(VanBanRequest $request, $id)
    {
        $vanban = VanBanModel::find($id);
        $vanban->vb_nhan = $request->vb_nhan;
        $vanban->vb_kieuhd = $request->vb_kieuhd;
        $vanban->vb_loai = $request->vb_loai;
        $vanban->vb_nhan_en = SuuTraController::convert_vi_to_en($request->vb_nhan);
        $vanban->save();
        //        $vb_doan = VanBanDoanModel::where('vb_idfk', $id)->delete();
        //        $doan = array_combine($request->doan, $request->vaitro);
        //        $i = 1;

        //        foreach ($doan as $key => $value) {
        //
        //            VanBanDoanModel::create(
        //                [
        //                    'vb_idfk' => $id,
        //                    'd_idfk' => $key,
        //                    'vaitro' => $value,
        //                    'sort' => $i
        //
        //                ]);
        //            $i++;
        //        }
        return Redirect(route('indexVB'))->with('success', 'Cập nhật văn bản thành công');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        VanBanModel::find($id)->delete();
        VanBanDoanModel::where('vb_idfk', $id)->delete();
        return Redirect(route('indexVB'))->with('success', 'Xóa văn bản thành công');
    }

    public function getVaiTroofVB(Request $request)
    {
        $kieuHD = Kieuhopdong::find($request->vanban);
        $vaitro = vaitro::whereIn('vt_id', json_decode($kieuHD->vaitro))
            ->pluck('vt_nhan', 'vt_id');
        return ['status' => 'success', 'data' => $vaitro];
    }

    public function getVBofKieuHD(Request $request)
    {
        $vanban = VanBanModel::where('vb_kieuhd', $request->kieu_hd)->get();
        return ['status' => 'success', 'data' => $vanban];
    }

    function syncTemplate()
    {
        $respone = Curl::to("http://127.0.0.1:8000/api/get-template")
            ->asJson()->get();
        if (isset($respone) && $respone->status == true) {
            $data = $respone->data;
            foreach ($data as $item) {
                $id = Sentinel::getUser()->id;
                $id_vp = User::join('nhanvien', 'nhanvien.nv_id', '=', 'id')
                    ->join('chinhanh', 'chinhanh.cn_id', '=', 'nhanvien.nv_vanphong')
                    ->where('users.id', $id)->first()->cn_id;
                if (!VanBanModel::where('lien_ket', '=', $item->id)->where('id_vp','=',$id_vp)->first()) {
                    VanBanModel::create([
                        'vb_nhan' => $item->name,
                        'lien_ket' => $item->id,
                        'vb_kieuhd' => $item->kind_id,
                        'id_vp' => $id_vp,
                    ]);

                }

            }
        }

        return redirect(route('indexVB'))->with('success', 'Đồng bộ văn bản thành công!');
    }
    function syncTemplateAll()
    {
        $respone = Curl::to("http://127.0.0.1:8000/api/get-template")
            ->asJson()->get();
        if (isset($respone) && $respone->status == true) {
            $data = $respone->data;
            $chinhnah=ChiNhanhModel::get();
            foreach ($chinhnah as $vp){
                foreach ($data as $item) {
                    $id = Sentinel::getUser()->id;
                    $id_vp = User::join('nhanvien', 'nhanvien.nv_id', '=', 'id')
                        ->join('chinhanh', 'chinhanh.cn_id', '=', 'nhanvien.nv_vanphong')
                        ->where('users.id', $id)->first()->cn_id;
                    if (!VanBanModel::where('lien_ket', '=', $item->id)->where('id_vp','=',$id_vp)->first()) {
                        VanBanModel::create([
                            'vb_nhan' => $item->name,
                            'lien_ket' => $item->id,
                            'vb_kieuhd' => $item->kind_id,
                            'id_vp' => $vp->cn_id,
                        ]);

                    }

                }

            }
        }

        return redirect(route('indexVB'))->with('success', 'Đồng bộ văn bản thành công!');
    }

}
