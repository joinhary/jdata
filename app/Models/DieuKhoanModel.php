<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DieuKhoanModel extends Model
{
    use SoftDeletes;

    public $incrementing = false;
    protected $table = 'dieukhoan';
    protected $fillable = [
        'dk_id',
        'dk_nhan',
        'dk_phaply1',
        'dk_phaply2',
        'dk_noidung',
        'deleted_at'
    ];

    protected $primaryKey = 'dk_id';
    protected $dates = ['deleted_at'];
}
