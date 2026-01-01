<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NhanVienRequest extends FormRequest
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
                    'nv_hoten' => 'required',
                    'email' => 'required',
                    'password' => 'required',
                    'nv_vanphong' => 'required',
                    'nv_chucvu' => 'required',
                ];
            }
            case 'PATCH':
            {
                return [
                    'nv_hoten' => 'required',
                    'email' => 'required',
                    'password' => 'required',
                    'nv_vanphong' => 'required',
                    'nv_chucvu' => 'required',
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
                    'nv_hoten.required' => 'Vui lòng nhập tên nhân viên!',
                    'email.required' => 'Vui lòng nhập email!',
                    'password.required' => 'Vui lòng nhập mật khẩu!',
                    'nv_vanphong.required' => 'Vui lòng chọn văn phòng!',
                    'nv_chucvu.required' => 'Vui lòng chọn chức vụ!',
                ];
            }
            case 'PATCH':
            {
                return [
                    'nv_hoten.required' => 'Vui lòng nhập tên nhân viên!',
                    'email.required' => 'Vui lòng nhập email!',
                    'password.required' => 'Vui lòng nhập mật khẩu!',
                    'nv_vanphong.required' => 'Vui lòng chọn văn phòng!',
                    'nv_chucvu.required' => 'Vui lòng chọn chức vụ!',
                ];
            }
            default:
                break;
        }
    }


}
