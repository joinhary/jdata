<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictModel extends Model
{
    public $incrementing = false;
    protected $table = 'district';
    protected $fillable = [
        'districtid',
        'name',
        'provinceid'
    ];

    protected $primaryKey = 'districtid';

    public function province()
    {
        return $this->belongsTo('App\Models\ProvinceModel');
    }

//
    public function ward()
    {
        return $this->hasMany('App\Models\WardModel', 'districtid');
    }
}
