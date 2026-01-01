<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VanBanTempModel extends Model
{
    public $incrementing = false;
    public $timestamps = false;

    protected $table = 'vanban_temp';
    protected $fillable = [
        'id',
        'noidung'
    ];

    protected $primaryKey = 'id';
}
