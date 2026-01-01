<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LichSuHonNhanModel extends Model
{
    protected $table = 'lichsu_honnhan';
    protected $fillable = [
        'ds1_id',
        'ds2_id',
        'lshn_id',
        'ds_id',
        'hon_phoi',
        'so_chung_nhan',
        'lshn_tinhtrang'
    ];

    protected $primaryKey = 'lshn_id';
}
