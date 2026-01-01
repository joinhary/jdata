<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhieuTaiSanModel extends Model
{
    protected $table = 'phieu_taisan';
    protected $fillable = ['pts_id', 'pts_nhan', 'pts_trangthai', 'pts_kieu', 'kh_id', 'created_at', 'updated_at'];
    protected $primaryKey = 'pts_id';
    protected $keyType = 'varchar';
}
