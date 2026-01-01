<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LyLichKhachHangModel extends Model
{
    use SoftDeletes;

    protected $table = 'lylich_khachhang';
    protected $fillable = [
        'id',
        'sohoso',
        'so_cc',
        'so_vaoso',
        'mota',
        'ngayky',
        'tinhtrang',
        'ccv_id',
        'nhanviennv_id',
        'kh_id',
        'lylich_loai',
        'lylich_hinhanh',
        'deleted_at',
        'link_id'
    ];
    protected $primaryKey = 'id';
    protected $dates = ['deleted_at'];
}
