<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $table = 'templates';
    public  $timestamps = FALSE;

    public function kieu_tai_san()
    {
        return $this->belongsTo('App\Models\KieuModel', 'slug', 'k_keywords');
    }
}
