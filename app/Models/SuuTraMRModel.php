<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuuTraMRModel extends Model
{
    protected $table = 'suutra_morong';
    protected $fillable = [
        'st_id',
        'nam_hieu_luc',
        'ngay_hieu_luc',
        'texte',
        'loai',
        'duong_su',
        'tai_san',
        'ngay_chan',
        'ngay_nhap',
        'ngay_cc',
        'so_hd',
        'ten_hd',
        'ccv',
        'vp',
        'ccv_master',
        'so_hd_master',
        'created_at',
        'updated_at',
    ];
    protected $keyType = 'varchar';
    protected $primaryKey = 'st_id';
}
