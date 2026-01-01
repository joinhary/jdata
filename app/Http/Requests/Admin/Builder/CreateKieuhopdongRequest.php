<?php

namespace App\Http\Requests\Admin\Builder;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Admin\Builder\Kieuhopdong;

class CreateKieuhopdongRequest extends FormRequest
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
        return Kieuhopdong::$rules;
    }
}
