<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UchiContractKindModel extends Model
{
    const CREATED_AT = 'entry_date_time';
    const UPDATED_AT = 'update_date_time';
    protected $connection = 'mysql';

    protected $table = 'npo_contract_kind';
    protected $fillable = [
        'name',
        'parent_kind_id',
        'order_number',
        'entry_user_id',
        'entry_user_name',
        'entry_date_time',
        'update_user_id',
        'update_user_name',
        'update_date_time',

    ];

    protected $primaryKey = 'id';
    protected $dates = ['entry_date_time', 'update_date_time'];
    protected $dateFormat = 'Y-m-d H:i:s';
}
