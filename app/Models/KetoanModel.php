<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KetoanModel extends Model
{
    protected $table = 'ketoandb';
    public $incrementing = true;

    protected $fillable = [
        'id',
        'id_hopdong',
        'ccv_id',
        'nvnv_id',
        'thuky_id',
        'ngayky',
        'giadv_id'
    ];
}
