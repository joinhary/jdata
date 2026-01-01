<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoanDieukhoanDkModel extends Model
{
    protected $table = 'doan_dkien_dk';
    protected $fillable = [
        'd_id',
        'd_nhan',
        'd_dkien',
        'd_dkhoan',
        'd_parent',
        'isparent',
    ];
    public $timestamps = false;
}
