<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MuonTraModel extends Model
{
    protected $table = 'muontra';
    protected $fillable = [
        'id',
        'id_hopdong',
        'ly_do',
        'ngay_tra',
        'ngay_muon',
        'trangthai_hoso',
        'id_nhanvien',
        'gia_han',
        'created_at',
        'updated_at',
    ];

    protected $primaryKey = 'nv_id';
}
