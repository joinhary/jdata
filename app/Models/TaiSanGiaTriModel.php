<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaiSanGiaTriModel extends Model
{
    protected $table = 'taisan_giatri';
    protected $fillable = ['ts_id', 'tm_id', 'ts_giatri',];
    protected $keyType = 'string';
    protected $casts = [
    'ts_id' => 'string',   // ❌ Laravel không hỗ trợ
];
    protected $primaryKey = 'ts_id';
}
