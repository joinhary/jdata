<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaiSanLog extends Model
{
    protected $table = 'tai_san_logs';

    protected $fillable = [
        'ts_id',
        'log_content',
        'creator_id'
    ];

}
