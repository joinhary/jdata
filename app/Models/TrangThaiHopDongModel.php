<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrangThaiHopDongModel extends Model
{
    protected $table = 'hopdong_trangthai';
    protected $fillable = [
        'id_hopdong',
        'trangthai',
        'id_nhanvien',
    ];
    protected $keyType = 'varchar';
    protected $primaryKey = 'id_hopdong';
}
