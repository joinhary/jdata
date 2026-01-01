<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class RolesRequest extends FormRequest
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
            'slug' => 'required',
            'display_name' => 'required',
            'permissions' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'slug' => 'Ký hiệu',
            'display_name' => 'Tên hiển thị',
            'permissions' => 'Quyền',
        ];
    }

    public function messages()
    {
        return [
            'slug.required' => ':attribute không thể để trống',
//            'slug.unique' => ':attribute đã tồn tại',
            'display_name.required' => ':attribute không thể để trống',
            'permissions.required' => ':attribute không thể để trống',
        ];
    }
}
