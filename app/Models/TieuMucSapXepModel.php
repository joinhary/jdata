<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TieuMucSapXepModel extends Model
{
    public $incrementing = false;
    public $timestamps = false;

    protected $table = 'tieumuc_sapxep';
    protected $fillable = [
        'tm_id',
        'k_id',
        'tm_sort'
    ];
}
