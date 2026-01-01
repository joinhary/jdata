<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class TinTucThongBaoModel extends Model
{
    protected $table = 'news';
    protected $fillable =['id','title','content', 'image','category'];
}
