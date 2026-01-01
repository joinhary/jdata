<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaiSanLichSuModel extends Model
{
    use SoftDeletes;
    protected $table = 'lylich_taisan';
    protected $primaryKey = 'id';
    protected $fillable = [
        'sohoso',
        'so_cc',
        'so_vaoso',
        'mota',
        'ngayky',
        'tinhtrang',
        'ccv_id',
        'nhanviennv_id',
        'ts_id',
        'lylich_loai',
        'lylich_hinhanh',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $dates = ['deleted_at'];
}
