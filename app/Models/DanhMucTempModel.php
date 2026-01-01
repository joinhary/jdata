<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DanhMucTempModel extends Model
{
    public $incrementing = false;
    public $timestamps = false;

    protected $table = 'danhmuc_temp';
    protected $fillable = [
        'id',
        'noidung'
    ];

    protected $primaryKey = 'id';
}
