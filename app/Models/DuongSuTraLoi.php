<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DuongSuTraLoi extends Model
{
    public $incrementing = true;
    protected $table = 'duongsu_traloi';
    protected $fillable = [
        'id',
        'id_hopdong',
        'id_duongsu',
        'id_tm',
        'traloi',
        'type',

    ];
    protected $primaryKey = 'id';
    public $timestamps = false;

}
