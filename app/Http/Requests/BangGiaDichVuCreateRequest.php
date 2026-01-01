<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BangGiaDichVuCreateRequest extends FormRequest
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
        return [
            'dichvu' => 'required',
            'phi' => 'required|numeric',
            'thu_lao' => 'required|numeric',
            'chiphi_khac' => 'required|numeric',

            'ngayapdung' => 'required|date',
        ];
    }

    public function attributes()
    {
        return [
            'dichvu' => 'Dịch vụ',
            'phi' => 'Phí',
            'thu_lao' => 'Thù lao',
            'chiphi_khac' => 'Chi phí khác',

            'ngayapdung' => 'Ngày áp dụng',
        ];
    }

    public function messages()
    {
        return [
            'dichvu.required' => ':attribute không thể để trống',
            'phi.required' => ':attribute không thể để trống',
            'phi.numeric' => ':attribute phải là số',
            'thu_lao.required' => ':attribute không thể để trống',
            'thu_lao.numeric' => ':attribute phải là số',
            'chiphi_khac.required' => ':attribute không thể để trống',
            'chiphi_khac.numeric' => ':attribute phải là số',
            'ngayapdung.required' => ':attribute không thể để trống',
            'ngayapdung.date' => ':attribute phải là định dạng date',
        ];
    }
}
