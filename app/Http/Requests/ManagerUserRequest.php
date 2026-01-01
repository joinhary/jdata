<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ManagerUserRequest extends FormRequest
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
                        'first_name' => 'required|min:3',
                        'phone' => 'required|digits_between:10,11|numeric|unique:users,phone',
                        'address' => 'required',
                        'email' => 'required|email|unique:users,email',
                        'password' => 'required|between:3,32',
                        'password_confirm' => 'required|same:password',
                    ];
                }
            case 'PUT':
            case 'PATCH':
                {
                    return [
                        'first_name' => 'required|min:3',
                        'phone' => 'required|digits_between:10,11|numeric|unique:users,phone',
                        'address' => 'required',
                        'email' => 'required|unique:users,email,' . $this->user->id,
                        'password_confirm' => 'sometimes|same:password',
                    ];
                }
            default:
                break;
        }

    }

    public function messages()
    {
        return [
            'first_name.required' =>'Vui lòng nhập :attribute',
            'phone.required' =>'Vui lòng nhập :attribute',
            'phone.digits_between' =>'độ dài :attribute không phù hợp',
            'phone.numeric' =>':attribute chỉ nhận số',
            'phone.unique' =>':attribute đã tồn tại trong hệ thống',
            'address.required' =>'Vui lòng nhập :attribute',
            'email.required' =>'Vui lòng nhập :attribute',
            'email.unique' =>':attribute đã tồn tại trong hệ thống',
            'password.required' =>'Vui lòng nhập :attribute',
        ];
    }

    public function attributes()
    {
        return [
            'first_name' => 'họ và tên',
            'phone' => 'số điện thoại',
            'address' => 'địa chỉ',
            'email' => 'email',
            'password' => 'mật khẩu',
        ];
    }

}

