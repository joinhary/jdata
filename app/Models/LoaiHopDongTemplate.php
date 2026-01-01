<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoaiHopDongTemplate extends Model
{
    protected $table = 'loai_hop_dong_templates';
    public $timestamps = false;

    public function loai_hop_dong()
    {
        return $this->belongsTo('App\Models\VanBanModel', 'loai_hop_dong_id', 'vb_id');
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
