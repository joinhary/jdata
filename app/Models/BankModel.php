<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\This;

class BankModel extends Model
{
    public $incrementing = false;
    protected $table = 'bank';
    protected $fillable = [
        'order_number',
        'name',
        'id',
    ];

    protected $primaryKey = 'id';

    // public function user()
    // {
    //     return $this->belongsTo('App\Models\User');
    // }

    // public function province()
    // {
    //     return $this->belongsTo('App\ProvinceModel');
    // }
}
