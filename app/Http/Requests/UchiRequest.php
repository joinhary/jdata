<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UchiRequest extends FormRequest
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
                        'contract_template' => 'required',
                        'contract_number' => 'unique:mysql.npo_contract',
                        'notary_date' => 'required',
                        'notary' => 'required',
                        'contract_value' => 'regex:/^[0-9.]+$/i',
                        'contract_period' => 'regex:/^[0-9]+$/i'
                    ];
                }
            case 'PATCH':
                {
                    return [];
                }
            case 'PUT':
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
                        'contract_template.required' => 'Vui lòng chọn :attribute',
                        'contract_number.unique' => ':attribute đã tồn tại!',
                        'notary_date.required' => ':attribute không được để trống!',
                        'notary.required' => 'Vui lòng chọn :attribute',
                        'contract_value.regex' => ':attribute chỉ bao gồm các chữ số và dấu chấm',
                        'contract_period.regex' => ':attribute sai định dạng'
                    ];
                }
            case 'PATCH':
                {
                    return [];
                }
            case 'PUT':
            default:
                break;
        }
    }

    public function attributes()
    {
        switch ($this->method()){
            case 'GET':
            case 'DELETE':
                {
                    return [];
                }
            case 'POST':
                {
                    return [
                        'contract_template' => 'Tên hợp đồng',
                        'contract_number' => 'Số hợp đồng',
                        'notary_date' => 'Ngày thụ lý',
                        'notary_id' => 'Công chứng viên',
                        'contract_value' => 'Giá trị hợp đồng',
                        'contract_period' => 'Thời hạn hợp đồng',
                    ];
                }
            case 'PATCH':
                {
                    return [];
                }
            case 'PUT':
            default:
                break;
        }
    }
}
