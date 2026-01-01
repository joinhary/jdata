<?php
/*
 * name: Nguyễn Quốc Khánh
 */

namespace App\Http\Controllers;

use App\Models\ChiNhanhModel;
use App\Models\NhanVienModel;
use Sentinel;
use Illuminate\Http\Request;

trait Chinhanh
{
    public function listChinhanh(Request $request)
    {
        $where[] = ['cn_trangthai', '=', 1];
        if ($request->cn_ten) {
            $where[] = ['cn_ten', 'LIKE', '%' . $request->cn_ten . '%'];
        }
        $chinhanhs = ChiNhanhModel::select('code_cn','cn_id', 'cn_ten', 'cn_diachi', 'province.name as cn_tinh', 'district.name as cn_quan', 'ward.name as cn_phuong', 'village.name as cn_ap', 'lat', 'lng','status')
            ->join('province', 'provinceid', '=', 'cn_tinh')
            ->join('district', 'districtid', '=', 'cn_quan')
            ->join('ward', 'wardid', '=', 'cn_phuong')
            ->join('village', 'villageid', '=', 'cn_ap');
        if(!Sentinel::inRole('admin')){
            $id=NhanVienModel::find(Sentinel::check()->id)->nv_vanphong;
            $chinhanhs->where('cn_id','=',$id);
        }
        $chinhanh = $chinhanhs->where($where);
        return $chinhanh;
    }
}
