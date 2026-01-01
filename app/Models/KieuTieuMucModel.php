<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KieuTieuMucModel extends Model
{
    protected $table = 'kieu_tieumuc';
    protected $fillable = [
        'ktm_id',
        'tm_id',
        'k_id',
        'ktm_traloi',
        'ktm_status',
        'deleted_at'
    ];
    protected $primaryKey = 'ktm_id';
}
