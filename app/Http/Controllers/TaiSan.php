<?php

namespace App\Http\Controllers;

use App\Models\TaiSanModel;
use App\Models\TaiSanGiaTriModel;
use App\Models\KieuModel;
use App\Models\TieuMucModel;
use Illuminate\Http\Request;
trait TaiSan
{
    /**
     * Hàm thêm tài sản
     * @param Request $request
     */
    public function store_taisan(Request $request)
    {
        $save_path = 'imagesTS';
        $ten_anh = '';

        /*Xử lý request vào bảng taisan*/
        $ts_nhan = $request->ts_nhan;
        $ts_kieu = $request->kieu;



        $ts = TaiSanModel::create([
            'ts_nhan' => $ts_nhan,
            'ts_trangthai' => 1,
            'ts_kieu' => $ts_kieu
        ]);

        /* Xử lý các request vào bảng taisan_giatri*/
        $i = 0;
        foreach ($request->ds_tm as $tm) {
            if ($request->file($tm)) {
                //Các request có dạng file (ảnh)
                $ts_giatri = $ten_anh = time() + $i . '.' . $request->$tm->getClientOriginalExtension();
                $request->$tm->move($save_path, $ten_anh);
            } else {
                //Các request có dạng giá trị thông thường (text, number, date,..)
                $ts_giatri = $request->$tm;
            }
            $tm_id = substr($tm, 3);
            TaiSanGiaTriModel::create([
                'ts_id' => $ts->ts_id,
                'tm_id' => $tm_id,
                'ts_giatri' => $ts_giatri,

            ]);
            $i++;
        }

//        $user_exec = Sentinel::getUser()->id;
//        $description = "Tạo khách hàng và tài khoản cho ".$user->first_name;
//        $this->api_create_log($user_exec, $description);
    }
}