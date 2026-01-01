<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BangGiaDichVuModel extends Model
{
    use SoftDeletes;

    protected $table = 'banggia_dichvu';
    protected $fillable =['dichvu','phi','thu_lao','ngayapdung','chiphi_khac','deleted_at',
    ];
}
