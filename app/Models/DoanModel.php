<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoanModel extends Model
{
    public $incrementing = true;
    protected $table = 'doan';
    protected $fillable = [
        'd_nhan',
        'd_parent',
        'd_number',
        'd_trangthai',
        'deleted_at',
        'd_vaitro_fk',
        'isparent',
        'haveparent'
    ];
    protected $keyType = 'varchar';
    protected $primaryKey = 'd_number';
}
