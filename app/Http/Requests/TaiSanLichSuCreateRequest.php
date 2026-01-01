<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaiSanLichSuCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
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
                        'tinhtrang' => 'required',
                        'ngayky' => 'required|date',
                        'so_cc' => 'max:20',
                        'ccv_id' => 'required',
                    ];
                }
            case 'PUT': {
                    return [
                        'tinhtrang' => 'required',
                        'sohoso' => 'required|max:20|unique:lylich_taisan,sohoso,'. $this->id_lsS,
                        'ngayky' => 'required|date',
                        'so_cc' => 'max:20',
                        'so_vaoso' => 'required|max:20|unique:lylich_taisan,so_vaoso,'. $this->id_ls,
                        'mota' => 'required',
                        'ccv_id' => 'required',
                        'nhanviennv_id' => 'required'
                    ];
                }
            case 'PATCH':
                {
                    return [
                        'tinhtrang' => 'required',
                        'sohoso' => 'required|max:20',
                        'so_cc' => 'max:20',
                        'so_vaoso' => 'required|max:20',
                        'mota' => 'required',
                        'ccv_id' => 'required',
                        'nhanviennv_id' => 'required'
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
                        'tinhtrang.required' => ':attribute không thể được bỏ trống',
                        'sohoso.required' => ':attribute không thể được bỏ trống',
                        'sohoso.max' => ':attribute tối đa 20 ký tự',
                        'sohoso.unique' => ':attribute đã tồn tại trong hệ thống',
                        'ngayky.required' => ':attribute không thể được bỏ trống',
                        'ngayky.date' => ':attribute phải là định dạng date',
                        'so_cc.max' => ':attribute tối đa 20 ký tự',
                        'so_vaoso.required' => ':attribute không thể được bỏ trống',
                        'so_vaoso.max' => ':attribute tối đa 20 ký tự',
                        'so_vaoso.unique' => ':attribute đã tồn tại trong hệ thống',
                        'mota.required' => ':attribute không thể được bỏ trống',
                        'ccv_id.required' => ':attribute không thể được bỏ trống',
                        'nhanviennv_id.required' => ':attribute không thể được bỏ trống'
                    ];
                }
            case 'PUT':
            case 'PATCH':
                {
                    return [
                        'tinhtrang.required' => ':attribute không thể được bỏ trống',
                        'sohoso.required' => ':attribute không thể được bỏ trống',
                        'sohoso.max' => ':attribute tối đa 20 ký tự',
                        'sohoso.unique' => ':attribute đã tồn tại trong hệ thống',
                        'ngayky.required' => ':attribute không thể được bỏ trống',
                        'so_cc.max' => ':attribute tối đa 20 ký tự',
                        'so_vaoso.required' => ':attribute không thể được bỏ trống',
                        'so_vaoso.max' => ':attribute tối đa 20 ký tự',
                        'so_vaoso.unique' => ':attribute đã tồn tại trong hệ thống',
                        'mota.required' => ':attribute không thể được bỏ trống',
                        'ccv_id.required' => ':attribute không thể được bỏ trống',
                        'nhanviennv_id.required' => ':attribute không thể được bỏ trống'
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
                        'tinhtrang' => 'Tình trạng',
                        'sohoso' => 'Số hồ sơ',
                        'ngayky' => 'Ngày ký',
                        'so_cc' => 'Số CC',
                        'so_vaoso' => 'Số chính thức',
                        'mota' => 'Nhãn',
                        'ccv_id' => 'Công chứng viên',
                        'nhanviennv_id' => 'Nhân viên nghiệp vụ'
                    ];
                }
            case 'PUT':
            case 'PATCH':
                {
                    return [
                        'tinhtrang' => 'Tình trạng',
                        'sohoso' => 'Số hồ sơ',
                        'so_cc' => 'Số CC',
                        'so_vaoso' => 'Số chính thức',
                        'mota' => 'Nhãn',
                        'ccv_id' => 'Công chứng viên',
                        'nhanviennv_id' => 'Nhân viên nghiệp vụ'
                    ];
                }
            default:
                break;
        }

    }
}
