<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KhachHangLog extends Model
{
    protected $table = 'khach_hang_logs';
    protected $fillable = [
        'kh_id',
        'log_content',
        'creator_id'
    ];
}
