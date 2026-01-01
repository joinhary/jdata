<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Traloi_Duongsu extends Model
{
    protected $table = 'duongsu_traloi';
    protected $fillable = [
        'id',
        'id_hopdong',
        'id_duongsu',
        'id_tm',
        'traloi',
        'type',
    ];
}
