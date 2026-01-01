<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhieuTaiSanGiaTriModel extends Model
{
    protected $table = 'phieutaisan_giatri';
    protected $fillable = ['pts_id', 'tm_id', 'pts_giatri',];
    protected $keyType = 'varchar';
    protected $primaryKey = 'pts_id';
}
