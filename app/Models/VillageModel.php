<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VillageModel extends Model
{
    public $incrementing = false;
    protected $table = 'village';
    protected $primaryKey = 'villagedid';
    protected $fillable = [
        'villageid',
        'name',
        'wardid'
    ];

    public function ward()
    {
        return $this->belongsTo('App\Models\WardModel');
    }
}
