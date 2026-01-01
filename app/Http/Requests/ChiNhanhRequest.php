<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChiNhanhRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        switch ($this->method()) {
            case 'GET':
            case 'DELETE':
            {
                return [];
            }
            case 'POST':
            {
                return [
                    'cn_ten' => 'required',
                    'code_cn' => 'required|unique:chinhanh,code_cn',
                    'cn_sdt' => 'required',
                    'cn_diachi' => 'required',
                    'cn_tinh' => 'required',
                    'cn_quan' => 'required',
                    'cn_phuong' => 'required',
                    'cn_ap' => 'required',
                ];
            }
            case 'PATCH':
            {
                return [
                    'cn_ten' => 'required',
                    'code_cn' => 'required|unique:chinhanh,code_cn',
                    'cn_sdt' => 'required',
                    'cn_diachi' => 'required',
                    'cn_tinh' => 'required',
                    'cn_quan' => 'required',
                    'cn_phuong' => 'required',
                    'cn_ap' => 'required',
                ];
            }
            default:
                break;
        }
    }

    public function messages()
    {
        switch ($this->method()) {
            case 'GET':
            case 'DELETE':
            {
                return [];
            }
            case 'POST':
            {
                return [
                    'cn_ten.required' => 'Vui lòng nhập tên văn phòng!',
                    'code_cn.required' => 'Vui lòng nhập mã văn phòng!',
                    'code_cn.unique' => 'Mã văn phòng đã tồn tại!',
                    'cn_sdt.required' => 'Vui lòng nhập số điện thoại văn phòng!',
                    'cn_diachi.required' => 'Vui lòng nhập địa chỉ văn phòng!',
                    'cn_tinh.required' => 'Vui lòng chọn tỉnh/thành phố!',
                    'cn_quan.required' => 'Vui lòng chọn quận/huyện!',
                    'cn_phuong.required' => 'Vui lòng chọn phường/xã!',
                    'cn_ap.required' => 'Vui lòng chọn ấp/khu vực!',
                ];
            }
            case 'PATCH':
            {
                return [
                    'cn_ten.required' => 'Vui lòng nhập tên văn phòng!',
                    'code_cn.required' => 'Vui lòng nhập mã văn phòng!',
                    'code_cn.unique' => 'Mã văn phòng đã tồn tại!',
                    'cn_sdt.required' => 'Vui lòng nhập số điện thoại văn phòng!',
                    'cn_diachi.required' => 'Vui lòng nhập địa chỉ văn phòng!',
                    'cn_tinh.required' => 'Vui lòng chọn tỉnh/thành phố!',
                    'cn_quan.required' => 'Vui lòng chọn quận/huyện!',
                    'cn_phuong.required' => 'Vui lòng chọn phường/xã!',
                    'cn_ap.required' => 'Vui lòng chọn ấp/khu vực!',
                ];
            }
            default:
                break;
        }
    }

    public function attributes()
    {
        switch ($this->method()) {
            case 'GET':
            case 'DELETE':
            {
                return [];
            }
            case 'POST':
            {
                return [
                    'cn_ten' => 'Tên chi nhánh',
                    'code_cn' => 'Mã chi nhánh',
                    'cn_sdt' => 'Số điện thoại',

                    'cn_diachi' => 'Địa chỉ',
                    'cn_tinh' => 'Tỉnh/thành phố',
                    'cn_quan' => 'Quận/huyện',
                    'cn_phuong' => 'Phường/xã',
                    'cn_ap' => 'Ấp/khu vực',
                ];
            }
            case 'PATCH':
            {
                return [
                    'cn_ten' => 'Tên chi nhánh',
                    'code_cn' => 'Mã chi nhánh',
                    'cn_sdt' => 'Số điện thoại',

                    'cn_diachi' => 'Địa chỉ',
                    'cn_tinh' => 'Tỉnh/thành phố',
                    'cn_quan' => 'Quận/huyện',
                    'cn_phuong' => 'Phường/xã',
                    'cn_ap' => 'Ấp/khu vực',
                ];
            }
            default:
                break;
        }
    }
}
