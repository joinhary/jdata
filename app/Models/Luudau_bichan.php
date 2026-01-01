<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Luudau_bichan extends Model
{
    protected $table = 'luudau_bichan';
    protected $fillable = [
        'id',
        'id_nv',
        'id_st',
        'mota'
    ];
    protected $primaryKey = 'id';
}
