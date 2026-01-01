<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProvinceModel extends Model
{
    public $incrementing = false;
    protected $table = 'province';
    protected $fillable = [
        'provinceid',
        'name'
    ];

    protected $primaryKey = 'provinceid';

    public function nhanvien()
    {
        return $this->hasOne('App\Models\NhanVienModel', 'nv_tinh');
    }

    public function district()
    {
        return $this->hasMany('App\Models\District', 'provinceid');
    }
}
