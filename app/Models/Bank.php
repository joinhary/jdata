<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{

    protected $table = 'bank';
    protected $fillable =['id','name','created_at','updated_at'];
    
}
