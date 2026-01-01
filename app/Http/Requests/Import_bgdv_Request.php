<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Import_bgdv_Request extends FormRequest
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

    public function rules()
    {
        return [
            'import_bgdv' => 'required|file',
        ];
    }

    public function attributes()
    {
        return [
            'import_bgdv' => 'Import',
        ];
    }

    public function messages()
    {
        return [
            'import_bgdv.required' => ':attribute file không thể để trống',
            'import_bgdv.file' => 'Chỉ có thể :attribute file',
        ];
    }
}
