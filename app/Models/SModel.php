<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SModel extends Model
{
      protected $table = 's';

    // KHÔNG dùng primary key
    protected $primaryKey = null;
    public $incrementing = false;

    // BẮT BUỘC
    public $timestamps = false;

    protected $fillable = ['val'];
}
