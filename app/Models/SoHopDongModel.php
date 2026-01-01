<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoHopDongModel extends Model
{
    public $incrementing = false;
    public $timestamps = false;

    protected $table = 'so_hopdong';
    protected $fillable = [
        'nam',
        'so_hopdong'
    ];

    protected $primaryKey = 'nam';
}
