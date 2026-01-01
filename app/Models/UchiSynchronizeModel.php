<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UchiSynchronizeModel extends Model
{
    protected $connection = 'mysql';

    public $incrementing = false;
    public $timestamps = false;

    protected $table = 'npo_synchronize';
    protected $fillable = [
        'type',
        'data_id',
        'authentication_id',
        'action',
        'status',
        'entry_date_time'
    ];

    protected $date = ['entry_date_time'];
    protected $dateFormat = 'Y-m-d H:i:s';
}
