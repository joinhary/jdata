<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DieuKhoanDoanModel extends Model
{
    public $incrementing = false;
    protected $table = 'dieukhoan_doan';
    protected $fillable = [
        'dk_idfk',
        'd_fk',
        'dkd_trangthai'
    ];

    public $timestamps = false;
}
