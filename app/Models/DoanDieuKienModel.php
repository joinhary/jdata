<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoanDieuKienModel extends Model
{
    protected $table = 'doan_dieukien';
    protected $fillable = [
        'd_number',
        'dieukien',
        'cautraloi',
        'trangthai',
        'd_dkid',
        'flag'
    ];
    public $timestamps = false;
}
