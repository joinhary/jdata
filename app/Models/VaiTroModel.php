<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VaiTroModel extends Model
{
    protected $table = 'vaitros';
    protected $fillable = ['vt_id', 'vt_nhan', 'created_at', 'updated_at', 'deleted_at'];
    protected $primaryKey = 'vt_id';
}
