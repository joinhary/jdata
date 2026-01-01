<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RutTrichModel extends Model
{
    use SoftDeletes;

    protected $connection = 'sqlsrv';

    protected $table = 'rut_trich';
    protected $fillable = [
        'hd_id',
        'relation_objA',
        'relation_objB',
        'relation_objC',
        'property_info',
        'summary',
        'deleted_at'
    ];

    protected $dates = ['deleted_at'];
    protected $dateFormat = 'Y-m-d H:i:s';
}
