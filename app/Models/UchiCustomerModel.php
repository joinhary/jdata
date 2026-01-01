<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UchiCustomerModel extends Model
{
    protected $connection = 'mysql';
    public $timestamps = false;

    protected $table = 'npo_customer';
    protected $fillable = [
        'customer_info',
        'frequency'
    ];

    protected $primaryKey = 'cid';
}
