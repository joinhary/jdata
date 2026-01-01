<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoaiKhachHangTemplate extends Model
{
    protected $table = 'loai_khach_hang_templates';
    public $timestamps = false;

    public function loai_khach_hang()
    {
        return $this->belongsTo('App\Models\KieuModel', 'loai_khach_hang_id', 'k_id');
    }

    public function convertToText($request)
    {
        if (!is_array($request)) {
            $request = $request->all();
        }

        $template = $this->template_transformed;
        foreach ($request as $param => $value) {
            $template = str_replace('<' . str_replace('_', '-', $param) . '>', $value, $template);
        }
        return $template;
    }
}
