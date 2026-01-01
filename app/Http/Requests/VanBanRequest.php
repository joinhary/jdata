<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VanBanRequest extends FormRequest
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
                    'vb_kieuhd' => 'required',
                    'vb_nhan' => 'required',
                    //'vb_loai' => 'required',

                ];
            }
            case 'PATCH':
            {
                return [
                    'vb_kieuhd' => 'required',
                    'vb_nhan' => 'required|unique:vanban,vb_nhan',
                  //  'vb_loai' => 'required',
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
                    'vb_kieuhd.required' => 'Vui lòng chọn kiểu hợp đồng!',
                    'vb_nhan.required' => 'Vui lòng nhập nhãn!',
                    'vb_loai.required' => 'Vui lòng chọn loại văn bản!',
                ];
            }
            case 'PATCH':
            {
                return [
                    'vb_kieuhd.required' => 'Vui lòng chọn kiểu hợp đồng!',
                    'vb_nhan.required' => 'Vui lòng nhập nhãn!',
                    'vb_loai.required' => 'Vui lòng chọn loại văn bản!',
                ];
            }
            default:
                break;
        }
    }
}
