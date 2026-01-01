<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PermissionsRequest extends FormRequest
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
            'group' => 'required',
            'permissions' => 'required|unique:permissions,permissions',
            'description' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'group' => 'Nhóm',
            'permissions' => 'Quyền',
            'description' => 'Mô tả',
        ];
    }

    public function messages()
    {
        return [
            'group.required' => ':attribute không thể để trống',
            'permissions.required' => ':attribute không thể để trống',
            'permissions.unique' => ':attribute đã tồn tại',
            'description.required' => ':attribute không thể để trốnga',
        ];
    }
}
