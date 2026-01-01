<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VanBanDoanModel extends Model
{
    protected $table = 'vanban_doan';
    protected $fillable =['vb_idfk','d_idfk','sort','vaitro'];
    public $timestamps = 0;
}
