<?php

namespace App\Http\Controllers\Admin\Builder;

use App\Models\ChiNhanhModel;
use App\Http\Requests;
use App\Http\Requests\Admin\Builder\CreateKieuhopdongRequest;
use App\Http\Requests\Admin\Builder\UpdateKieuhopdongRequest;
use App\KieuHopDongMode;
use App\Repositories\Admin\Builder\KieuhopdongRepository;
use App\Http\Controllers\AppBaseController as InfyOmBaseController;
use App\Models\User;
use App\VaiTroModel;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use App\Models\Kieuhopdong;
use Flash;
use Ixudra\Curl\Facades\Curl;
use PHPUnit\Framework\Constraint\Count;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use function Sodium\compare;

class KieuhopdongController extends InfyOmBaseController
{
    /** @var  KieuhopdongRepository */
    private $kieuhopdongRepository;

    public function __construct(KieuhopdongRepository $kieuhopdongRepo)
    {
        $this->kieuhopdongRepository = $kieuhopdongRepo;
    }

    public function index(Request $request)
    {
        $role = Sentinel::check()->user_roles()->first()->slug;
        $getKieuhd = $request->get('kieu_hd');
        $id_vp = User::join('nhanvien', 'nhanvien.nv_id', '=', 'id')
            ->join('chinhanh', 'chinhanh.cn_id', '=', 'nhanvien.nv_vanphong')
            ->where('users.id', Sentinel::getUser()->id)->first()->cn_id;
        $this->kieuhopdongRepository->pushCriteria(new RequestCriteria($request));
        if ($request->ajax()) {
            return ['status' => 'success', 'data' => $this->kieuhopdongRepository->all()];
        }
        $kieuhopdongs = $this->kieuhopdongRepository->where('kieu_hd', 'like', '%' . $getKieuhd . '%');
        $count = Count($kieuhopdongs->get());
        if ($role == 'admin' || $role == 'chuyen-vien-so') {
            $kieuhopdongs = $kieuhopdongs->paginate(10);
        } else {
            $kieuhopdongs = $kieuhopdongs->where('id_vp', $id_vp);
            $kieuhopdongs = $kieuhopdongs->paginate(10);
        }
        $tong = $this->kieuhopdongRepository->all();
		
        return view('admin.kieuhopdongs.index', compact('tong', 'count'))->with('kieuhopdongs', $kieuhopdongs);
    }

    public function create()
    {
        return view('admin.kieuhopdongs.create');
    }

    public function store(CreateKieuhopdongRequest $request)
    {
        $id = Sentinel::getUser()->id;
        $id_vp = User::join('nhanvien', 'nhanvien.nv_id', '=', 'id')
            ->join('chinhanh', 'chinhanh.cn_id', '=', 'nhanvien.nv_vanphong')
            ->where('users.id', $id)->first()->cn_id;
        Kieuhopdong::create([
            'kieu_hd' => $request->kieu_hd,
            'id_vp' => $id_vp,
            'lien_ket_id'=>$request->lien_ket_id
        ]);
        Flash::success('Kieuhopdong saved successfully.');
        return redirect(route('admin.kieuhopdongs.index'))->with('success', 'Tạo kiểu hợp đồng thành công!');
    }

    public function show($id)
    {
        $kieuhopdong = $this->kieuhopdongRepository->findWithoutFail($id);
        if (empty($kieuhopdong)) {
            Flash::error('Kieuhopdong not found');
            return redirect(route('kieuhopdongs.index'));
        }
        return view('admin.kieuhopdongs.show', compact('kieuhopdong'));
    }

    public function edit($id)
    {
        $kieuhopdong = $this->kieuhopdongRepository->findWithoutFail($id);
//        $vaitro = VaiTroModel::all()->pluck('vt_nhan', 'vt_id');
        if (empty($kieuhopdong)) {
            Flash::error('Kieuhopdong not found');
            return redirect(route('kieuhopdongs.index'));
        }
        return view('admin.kieuhopdongs.edit', compact('kieuhopdong'));
    }

    public function update($id, UpdateKieuhopdongRequest $request)
    {
        $kieuhopdong = $this->kieuhopdongRepository->findWithoutFail($id);
        if (empty($kieuhopdong)) {
            Flash::error('Kieuhopdong not found');
            return redirect(route('kieuhopdongs.index'));
        }
        Kieuhopdong::find($id)->update([
            'kieu_hd' => $request->kieu_hd,
        ]);
        Flash::success('Kieuhopdong updated successfully.');
        return redirect(route('admin.kieuhopdongs.index'))->with('success', 'Cập nhật kiểu hợp đồng thành công!');
    }

    public function getModalDelete($id = null)
    {
        $error = '';
        $model = '';
        $confirm_route = route('admin.kieuhopdongs.delete', ['id' => $id]);
        return View('admin.layouts/modal_confirmation', compact('error', 'model', 'confirm_route'));
    }

    public function getDelete($id = null)
    {
        $sample = Kieuhopdong::destroy($id);
        return redirect(route('admin.kieuhopdongs.index'))->with('success', Lang::get('message.success.delete'));
    }

    function syncKind()
    {
        $respone = Curl::to("http://127.0.0.1:8000/api/get-kind")
            ->asJson()->get();
        if(isset($respone)&&$respone->status==true){
            $data=$respone->data;
            foreach ($data as $item) {
                $id = Sentinel::getUser()->id;
                $id_vp = User::join('nhanvien', 'nhanvien.nv_id', '=', 'id')
                    ->join('chinhanh', 'chinhanh.cn_id', '=', 'nhanvien.nv_vanphong')
                    ->where('users.id', $id)->first()->cn_id;
                if(!Kieuhopdong::where('lien_ket_id','=',$item->id)->where('id_vp','=',$id_vp)->first()){
                    Kieuhopdong::create([
                        'kieu_hd' => $item->name,
                        'lien_ket_id' => $item->id,
                        'id_vp' => $id_vp,
                    ]);
                }


            }
        }

        Flash::success('Kieuhopdong saved successfully.');
        return redirect(route('admin.kieuhopdongs.index'))->with('success', 'Tạo kiểu hợp đồng thành công!');
    }
    function syncAllKind()
    {
        $respone = Curl::to("http://127.0.0.1:8000/api/get-kind")
            ->asJson()->get();
        if(isset($respone)&&$respone->status==true){
            $data=$respone->data;
            $chinhanh=ChiNhanhModel::get();

            foreach ($chinhanh as $vp){
                foreach ($data as $item) {
                    $id = Sentinel::getUser()->id;
                    $id_vp = User::join('nhanvien', 'nhanvien.nv_id', '=', 'id')
                        ->join('chinhanh', 'chinhanh.cn_id', '=', 'nhanvien.nv_vanphong')
                        ->where('users.id', $id)->first()->cn_id;
                    if(!Kieuhopdong::where('lien_ket_id','=',$item->id)->where('id_vp','=',$vp->cn_id)->first()){
                        Kieuhopdong::create([
                            'kieu_hd' => $item->name,
                            'lien_ket_id' => $item->id,
                            'id_vp' => $vp->cn_id,
                        ]);
                    }


                }

            }
        }

        Flash::success('Kieuhopdong saved successfully.');
        return redirect(route('admin.kieuhopdongs.index'))->with('success', 'Tạo kiểu hợp đồng thành công!');
    }

}
