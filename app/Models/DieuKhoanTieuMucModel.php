<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DieuKhoanTieuMucModel extends Model
{
    protected $table = 'dieukhoan_tieumuc';
    protected $fillable = [
        'dk_fk',
        'tm_fk',
        'dktm_trangthai'
    ];
    public $incrementing = false;
    public $timestamps = false;
}
