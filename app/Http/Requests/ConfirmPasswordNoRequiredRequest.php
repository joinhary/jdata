<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class ConfirmPasswordNoRequiredRequest extends FormRequest
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
        if ($this->password_change != null)
            return [
                'password_change' => 'between:6,32',
                'password_change_confirm' => 'same:password_change'
            ];
        else
            return [
                'password_change' => '',
                'password_change_confirm' => 'same:password_change'
            ];
    }

    public function attributes()
    {
        return [
            'password_change' => 'Mật khẩu',
            'password_change_confirm' => 'Mật khẩu xác nhận',
        ];
    }

    public function messages()
    {
        return [
            'password_change.between' => ':attribute độ dài nhỏ nhất là 6 và tối đa là 32 ký tự',
            'password_change_confirm.same' => ':attribute không giống với mật khẩu',
        ];
    }
}
