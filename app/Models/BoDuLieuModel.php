<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoDuLieuModel extends Model
{
    protected $table = 'bo_du_lieu';
    protected $fillable = [
        'bdl_id',
        'bdl_ten',
        'bdl_model',
        'column_list',
        'bdl_value',
        'bdl_prikey'
    ];
    protected $primaryKey = 'bdl_id';
}
