<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KhachHangModel extends Model
{
    public $incrementing = false;
    protected $table = 'khachhang';
    protected $fillable = [
        'kh_id',
        'tm_id',
        'kh_giatri'
    ];
}
