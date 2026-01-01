<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TieuMucModel extends Model
{
    protected $table = 'tieumuc';
    protected $primaryKey = 'tm_id';
    protected $fillable = [
        'tm_id',
        'tm_nhan',
        'tm_keywords',
        'tm_trogiup',
        'tm_loai',
        'tm_lienket',
        'tm_batbuoc',
        'tm_trangthai',
        'deleted_at'
    ];
}
