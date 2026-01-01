<?php

namespace App\Models;

use App\ChiNhanhModel;
use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Kieuhopdong
 * @package App\Models
 */
class Kieuhopdong extends Model
{
    use SoftDeletes;

    public $table = 'kieu_hop_dong';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'kieu_hd',
        'vaitro',
        'id_vp',
        'lien_ket_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'kieu_hd' => 'string',
        'vaitro' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'kieu_hd' => 'required'
    ];



}
