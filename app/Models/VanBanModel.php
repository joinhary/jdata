<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VanBanModel extends Model
{
    use SoftDeletes;

    protected $table = 'vanban';
    protected $primaryKey = 'vb_id';
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'vb_id',
        'vb_nhan',
        'vb_kieuhd',
        'loai_vb',
        'vn_nhan_en',
        'deleted_at',
        'id_vp',
        'lien_ket'
    ];

}
