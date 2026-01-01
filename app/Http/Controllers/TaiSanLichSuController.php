<?php

namespace App\Http\Controllers;

use App\Models\ChiNhanhModel;
use App\Http\Requests\TaiSanLichSuCreateRequest;
use App\Models\NhanVienModel;
use App\Models\RoleUsersModel;
use App\Models\TaiSanLichSuModel;
use App\Models\User;
use Sentinel;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaiSanLichSuController extends Controller
{
    use users;
    use Nhanvien;
//  public function __construct()
//     {
//         $this->middleware('user');
//     }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $id_ts)
    {
        $limit = 50;
        $num = num_row($request->page, $limit);
        $lylich_taisan = TaiSanLichSuModel::where([['ts_id', $id_ts], ['deleted_at', null]])
            ->orderBy('updated_at', 'desc')
            ->paginate($limit);
        return view('admin.taisanlichsu.index', compact('id_ts', 'lylich_taisan', 'num'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id_ts)
    {
        if (RoleUsersModel::where('user_id', Sentinel::check()->id)->first()->role_id == 11 || RoleUsersModel::where('user_id', Sentinel::check()->id)->first()->role_id == 10 || RoleUsersModel::where('user_id', Sentinel::check()->id)->first()->role_id == 12|| RoleUsersModel::where('user_id', Sentinel::check()->id)->first()->role_id == 14||Sentinel::check()->isMod()) {


            $ccv_id = Sentinel::findRoleBySlug('cong-chung-vien')->id;
            $role_ccv = RoleUsersModel::where('role_id', $ccv_id)->get();
            $ccv = [];
            foreach ($role_ccv as $item) {
                $user_ccv = User::select('id', 'first_name')
                    ->where('deleted_at', null)
                    ->orderBy('first_name', 'asc')
                    ->find($item->user_id);
                /* if (Activation::completed($user_ccv)) {
                    $ccv[] = $user_ccv;

                 }*/
                $ccv_arr = $this->get_ccv();
                $ccv = collect($ccv_arr)->pluck('first_name', 'id');
            }
            $nvnv_id = Sentinel::findRoleBySlug('chuyen-vien')->id;
            $role_nvnv = RoleUsersModel::where('role_id', $nvnv_id)->get();
            $nvnv = [];
            foreach ($role_nvnv as $item) {
                $user_nvnv = User::select('id', 'first_name')
                    ->where('deleted_at', null)
                    ->orderBy('first_name', 'asc')
                    ->find($item->user_id);
                /*
                            if (Activation::completed($user_nvnv)) {
                                $nvnv[] = $user_nvnv;
                            }*/
                $cv_arr = $this->get_cv();
                $nvnv = collect($cv_arr)->pluck('first_name', 'id');
            }
            return view('admin.taisanlichsu.create', compact('id_ts', 'nvnv', 'ccv'));
        } else {


            return redirect(route('admin.taisan.lichsu.index', $id_ts))->with('error', 'Bạn không có quyền thực hiện thao tác này');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(TaiSanLichSuCreateRequest $request, $id_ts)
    {
        $lylich_hinhanh = json_encode(explode(',', $request->array_name_image));
        $create = TaiSanLichSuModel::create([
            'tinhtrang' => $request->tinhtrang,
            'sohoso' => $request->sohoso,
            'ngayky' => $request->ngayky,
            'so_cc' => $request->so_cc,
            'so_vaoso' => $request->so_vaoso,
            'mota' => $request->mota,
            'ccv_id' => $request->ccv_id,
            'nhanviennv_id' => $request->nhanviennv_id,
            'lylich_loai' => 0,
            'lylich_hinhanh' => $lylich_hinhanh,
            'ts_id' => $id_ts,
        ]);

        if ($create) {
            $my_id = Sentinel::getUser()->id;
            $desciption = 'Đã thêm lịch sử có ID ' . $create->id . ' thuộc tài sản có ID ' . $id_ts;
            $this->api_create_log($my_id, $desciption);
            return redirect(route('admin.taisan.lichsu.index', $id_ts))->with('success', 'Đã tạo thành công');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\TaiSanLichSuModel $taiSanLichSuModel
     * @return \Illuminate\Http\Response
     */
    public function show(TaiSanLichSuModel $taiSanLichSuModel)
    {
        //
    }

    public function get_image(Request $request)
    {
        $id = $request->id;
        $ls_id = $request->ls_id;
        $data = json_decode(TaiSanLichSuModel::where('id', $id)->where('ts_id', $ls_id)->first()->lylich_hinhanh);
        return ['status' => 'success', 'data' => $data];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\TaiSanLichSuModel $taiSanLichSuModel
     * @return \Illuminate\Http\Response
     */
    public function edit($id_ts, $id_ls)
    {

        if (RoleUsersModel::where('user_id', Sentinel::check()->id)->first()->role_id == 11 || RoleUsersModel::where('user_id', Sentinel::check()->id)->first()->role_id == 10||Sentinel::check()->isMod()) {

            $lichsu = TaiSanLichSuModel::find($id_ls);
            if (!User::find(Sentinel::check()->id)->isAdmin()) {
                $creator_vp = NhanVienModel::find($lichsu->ccv_id);
                $current_vp = NhanVienModel::find(Sentinel::check()->id)->nv_vanphong;
                $name=ChiNhanhModel::find($current_vp)->cn_ten;
                if ($current_vp != $creator_vp)
                    return redirect()->back()->with('error', 'Vui lòng liên hệ văn phòng '.$name.' ngăng chặn để chỉnh sửa!');
                }
            $ccv_id = Sentinel::findRoleBySlug('cong-chung-vien')->id;
            $role_ccv = RoleUsersModel::where('role_id', $ccv_id)->get();
            $ccv = [];
            foreach ($role_ccv as $item) {
                $user_ccv = User::select('id', 'first_name')
                    ->where('deleted_at', null)
                    ->orderBy('first_name', 'asc')
                    ->find($item->user_id);
                /*   if (Activation::completed($user_ccv)) {
                       $ccv[] = $user_ccv;
                   }*/
                $ccv_arr = $this->get_ccv();
                $ccv = collect($ccv_arr)->pluck('first_name', 'id');
            }

            $nvnv_id = Sentinel::findRoleBySlug('chuyen-vien')->id;
            $role_nvnv = RoleUsersModel::where('role_id', $nvnv_id)->get();
            $nvnv = [];
            foreach ($role_nvnv as $item) {
                $user_nvnv = User::select('id', 'first_name')
                    ->where('deleted_at', null)
                    ->orderBy('first_name', 'asc')
                    ->find($item->user_id);
                $nvnv_arr = $this->get_tknv();
                $nvnv = collect($nvnv_arr)->pluck('first_name', 'id');
            }

            return view('admin.taisanlichsu.edit', compact('id_ts', 'nvnv', 'ccv', 'lichsu'));
        } else {
            return back()->with('error', 'Bạn không có quyền thực hiện thao tác này');

        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\TaiSanLichSuModel $taiSanLichSuModel
     * @return \Illuminate\Http\Response
     */
    public function update(TaiSanLichSuCreateRequest $request, $id_ts, $id_ls)
    {

        $lichsu = TaiSanLichSuModel::find($id_ls);

        if ($lichsu) {
            $data = [
                'tinhtrang' => $request->tinhtrang,
                'sohoso' => $request->sohoso,
                'so_cc' => $request->so_cc,
                'so_vaoso' => $request->so_vaoso,
                'mota' => $request->mota,
                'ccv_id' => $request->ccv_id,
                'nhanviennv_id' => $request->nhanviennv_id,
                'lylich_loai' => 1,
                'ts_id' => $id_ts,
            ];

            if ($request->array_name_image) {
                $data = array_merge($data, ['lylich_hinhanh' => json_encode(explode(',', $request->array_name_image))]);
            }
            $update = $lichsu->update($data);
            if ($update) {
                $my_id = Sentinel::getUser()->id;
                $desciption = 'Đã sửa lịch sử có ID ' . $lichsu->id . ' thuộc tài sản có ID ' . $id_ts;
                $this->api_create_log($my_id, $desciption);
                return redirect(route('admin.taisan.lichsu.index', $id_ts))->with('success', 'Đã sửa thành công');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\TaiSanLichSuModel $taiSanLichSuModel
     * @return \Illuminate\Http\Response
     */
    public function destroy($id_ts, $id_ls)
    {
        if (RoleUsersModel::where('user_id', Sentinel::check()->id)->first()->role_id == 11 || RoleUsersModel::where('user_id', Sentinel::check()->id)->first()->role_id == 10||Sentinel::check()->isMod()) {

            $lichsu = TaiSanLichSuModel::find($id_ls);
            if ($lichsu) {
                $my_id = Sentinel::getUser()->id;
                $desciption = 'Đã xóa lịch sử có ID ' . $lichsu->id . ' thuộc tài sản có ID ' . $id_ts;
                $log = $this->api_create_log($my_id, $desciption);
                if ($log) {
                    $lichsu->delete();
                }
                return back()->with('success', 'Xóa thành công');

            } else {
                return back()->with('error', 'Thao tác quá nhanh, dòng này đã bị xóa');
            }
        } else {
            return back()->with('error', 'Bạn không có quyền thực hiện thao tác này');
        }
    }

    public function formdata_image(Request $request)
    {

        $validator = Validator::make($request->file('image'), [
            'image' => 'image|mimes:jpg,png,jpeg',
        ]);

        if ($validator->fails()) {
            return ['status' => false, 'message' => $validator->errors()];
        } else {
            if ($file = $request->file('image')) {
                $name_images = [];
                $i = 1;
                foreach ($file as $item) {
                    $folder = public_path('images/lylich');
                    $extension = $item->extension() ?: 'png';
                    $safeName = time() + $i++ . '.' . $extension;
                    $item->move($folder, $safeName);
                    $name_images[] = $safeName;
                }
                return ['status' => true, 'data' => $name_images, 'message' => 'Upload image success'];
            }
        }
    }
}
