<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogUchiModel extends Model
{
    protected $table = 'log_load_suutra';
    protected $fillable = [
        'id',
        'run_date_past',
        'run_date_now',
        'row_count',
        'created_at',
        'updated_at'
    ];
    protected $primaryKey = 'id';
}
