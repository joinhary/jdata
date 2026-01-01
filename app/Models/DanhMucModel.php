<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DanhMucModel extends Model
{
    use SoftDeletes;

    protected $table = 'danhmuc_ructrich';

    protected $fillable = [
        'id',
        'nhan',
        'kieu_hd',
        'noidung',
        'ma',
        'id_vb',
        'list_ds',
        'list_ts',
        'duongsu_vaitro',
        'ccv_id',
        'nvnv_id',
        'thuky_id',
        'ngayky',
        'so_cc',
        'post_status',
        'vanphong',
        'anh_bosung',
        'deleted_at',
        'gia_hd'
    ];

    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];
}
