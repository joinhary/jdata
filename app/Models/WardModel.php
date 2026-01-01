<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WardModel extends Model
{
    public $incrementing = false;
    protected $table = 'ward';
    protected $primaryKey = 'wardid';
    protected $fillable = [
        'wardid',
        'name',
        'districtid'
    ];

    public function district()
    {
        return $this->belongsTo('App\Models\DistrictModel');
    }

    public function village()
    {
        return $this->hasMany('App\Models\VillageModel', 'wardid');
    }
}
