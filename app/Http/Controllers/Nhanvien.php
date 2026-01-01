<?php

namespace App\Http\Controllers;


use App\Models\NhanVienModel;
use App\Models\RoleUsersModel;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;

trait Nhanvien
{
    public function listNhanVien(Request $request)
    {
        $where = "1=1";
        if ($request->nv_tk) {
            $where = "nv_id like '%$request->nv_tk%' or nv_hoten like N'%$request->nv_tk%'";
        }
        $id_vp = NhanVienModel::find(Sentinel::check()->id)->nv_vanphong;
        if (RoleUsersModel::where('user_id', Sentinel::check()->id)->first()->role_id == 10) {
            $nhanvien = NhanVienModel::select('nv_id', 'nv_hoten', 'phone', 'roles.name as nv_tenchucvu', 'cn_ten', 'nv_vanphong', 'id_lienket')
                ->join('users', 'users.id', '=', 'nv_id')
                ->join('chinhanh', 'cn_id', '=', 'nv_vanphong')
                ->join('role_users', 'user_id', '=', 'nv_id')
                ->join('roles', 'roles.id', '=', 'role_id')
                ->whereRaw($where)
                ->where('users.deleted_at', null)
                ->where('users.is_active', null)
                ->orderBy('nv_hoten', 'asc');
            return $nhanvien;
        } else {

            $nhanvien = NhanVienModel::select('nv_id', 'nv_hoten', 'phone', 'roles.name as nv_tenchucvu', 'cn_ten', 'nv_vanphong', 'id_lienket')
                ->join('users', 'users.id', '=', 'nv_id')
                ->join('chinhanh', 'cn_id', '=', 'nv_vanphong')
                ->join('role_users', 'user_id', '=', 'nv_id')
                ->join('roles', 'roles.id', '=', 'role_id')
                ->whereRaw($where)
                ->where('users.deleted_at', null)->where('chinhanh.cn_id', $id_vp)
                ->orderBy('nv_hoten', 'asc');
            return $nhanvien;
        }
    }

    /**
     * Trả về danh sách các công chứng viên
     * @return array
     */
    public function get_ccv()
    {
        //Tìm id role thừa phát lại và lấy danh sách nhân viên có role này
        $ccv_id = Sentinel::findRoleBySlug('cong-chung-vien')->id;
        $ccv_list = RoleUsersModel::where('role_id', $ccv_id)->get();
        //Duyệt mảng danh sách nhân viên là ccv vừa lấy được kiểm tra từng người
        //Nếu tài khoản người đó đã active thì đẩy người đó vào mảng $ccv_arr vừa tạo
        $vp = NhanVienModel::find(Sentinel::check()->id)->nv_vanphong;
        $ccv_arr = [];
        if (!Sentinel::getUser()->isAdmin() && !Sentinel::getUser()->isCVS()) {
            if ($ccv_list) {
                foreach ($ccv_list as $item) {
                    if (NhanVienModel::find($item->user_id)) {
                        if (NhanVienModel::find($item->user_id)->nv_vanphong == $vp) {
                            $user_ccv = User::where('deleted_at', null)
                                ->orderBy('first_name', 'asc')
                                ->find($item->user_id);
                        }
                    }

                    if (isset($user_ccv)) {
                        //Kiểm tra tài khoản đang xét có được active (completed) hay không
                        if (Activation::completed($user_ccv)) {
                            $ccv_arr[] = $user_ccv;
                        }
                    }
                }
            }
        } else {
            if ($ccv_list) {
                foreach ($ccv_list as $item) {
                    if (NhanVienModel::find($item->user_id)) {

                        $user_ccv = User::where('deleted_at', null)
                            ->orderBy('first_name', 'asc')
                            ->find($item->user_id);
                    }

                    if (isset($user_ccv)) {
                        //Kiểm tra tài khoản đang xét có được active (completed) hay không
                        if (Activation::completed($user_ccv)) {
                            $ccv_arr[] = $user_ccv;
                        }
                    }
                }
            }
        }
        return $ccv_arr;
    }

    /**
     * Trả về danh sách các chuyên viên
     * @return array
     */
    public function get_cv()
    {
        //Tìm id role công chứng viên và lấy danh sách nhân viên có role này
        $cv_id = Sentinel::findRoleBySlug('chuyen-vien')->id;
        $cv_list = RoleUsersModel::where('role_id', $cv_id)->get();
        //Duyệt mảng danh sách nhân viên là ccv vừa lấy được kiểm tra từng người
        //Nếu tài khoản người đó đã active thì đẩy người đó vào mảng $ccv_arr vừa tạo
        $cv_arr = [];
        if ($cv_list) {
            foreach ($cv_list as $item) {
                $user_cv = User::where('deleted_at', null)
                    ->orderBy('first_name', 'asc')
                    ->find($item->user_id);
                if ($user_cv) {
                    //Kiểm tra tài khoản đang xét có được active (completed) hay không
                    if (Activation::completed($user_cv)) {
                        $cv_arr[] = $user_cv;
                    }
                }
            }
        }
        return $cv_arr;
    }

    /**
     * Trả về danh sách các chuyên viên
     * @return array
     */
    public function get_tknv()
    {
        //Tìm id role công chứng viên và lấy danh sách nhân viên có role này
        $tknv_id = Sentinel::findRoleBySlug('thu-ky-nghiep-vu')->id;
        $tknv_list = RoleUsersModel::where('role_id', $tknv_id)->get();
        //Duyệt mảng danh sách nhân viên là ccv vừa lấy được kiểm tra từng người
        //Nếu tài khoản người đó đã active thì đẩy người đó vào mảng $ccv_arr vừa tạo
        $tknv_arr = [];
        if ($tknv_list) {
            foreach ($tknv_list as $item) {
                $user_tknv = User::where('deleted_at', null)
                    ->orderBy('first_name', 'asc')
                    ->find($item->user_id);
                if ($user_tknv) {
                    //Kiểm tra tài khoản đang xét có được active (completed) hay không
                    if (Activation::completed($user_tknv)) {
                        $tknv_arr[] = $user_tknv;
                    }
                }
            }
        }
        return $tknv_arr;
    }
}