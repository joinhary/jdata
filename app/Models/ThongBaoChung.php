<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ThongBaoChung extends Model
{
    use SoftDeletes;

    protected $table = 'thong_bao_chung';
    public $incrementing = true;
    protected $primaryKey = 'id';
    protected $fillable = [
        'tieu_de',
        'noi_dung',
        'nv_id',
        'vp_id',
        'type',
        'file',
        'push',
        'pic',
        'realname',
        'duong_su',
        'texte',
        'duong_su_en',
        'texte_en',
        'so_cv',
        'created_indirect',
        'ma_dong_bo'

    ];
}
