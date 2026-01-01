<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuuTraLogNganChanModel extends Model
{
    protected $table = 'suutralog_nganchan';
    protected $fillable = [
        'id',
        'ngay_ngan_chan',
        'created_at',
        'updated_at',
        'ngay_bd',
        'ngay_kt',
        'danh_dau',
    ];
    protected $keyType = 'varchar';
    protected $primaryKey = 'id';
}
