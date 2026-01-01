<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class QuangCaoModel extends Model
{
    protected $table = 'logo_advs';
    protected $fillable =['id','name', 'link', 'img'];
}