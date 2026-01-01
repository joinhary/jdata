<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KieuHopDongMode extends Model
{
    protected $table = 'kieuhopdongs';
    public $incrementing = true;

    protected $fillable = [
        'id',
        'kieu_hd',
    ];
}
