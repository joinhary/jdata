<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\This;

class NhanVienModel extends Model
{
    public $incrementing = false;
    protected $table = 'nhanvien';
    protected $fillable = [
        'nv_id',
        'nv_hoten',
        'nv_tinh',
        'nv_quan',
        'nv_phuong',
        'nv_ap',
        'nv_vanphong',
        'id_uchi',
        'name_uchi',
        'id_lienket',
        'is_active',
    ];

    protected $primaryKey = 'nv_id';

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function province()
    {
        return $this->belongsTo('App\Models\ProvinceModel');
    }
}