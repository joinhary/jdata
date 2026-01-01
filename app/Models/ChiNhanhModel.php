<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChiNhanhModel extends Model
{
    use SoftDeletes;

    protected $table = 'chinhanh';
    protected $fillable = [
        'cn_id',
        'cn_ten',
        'cn_sdt',
        'cn_code_uchi',
        'cn_tenvp_uchi',
        'cn_ndd',
        'cn_tinh',
        'cn_quan',
        'cn_phuong',
        'cn_ap',
        'cn_diachi',
        'lat',
        'lng',
        'cn_trangthai',
        'deleted_at',
        'code_cn',
        'login_code',
		'status'

    ];
    protected $guarded = 'cn_id';
    protected $primaryKey = 'cn_id';
    protected $dates = ['deleted_at'];
}
