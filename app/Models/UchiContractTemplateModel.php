<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UchiContractTemplateModel extends Model
{
    const CREATED_AT = 'entry_date_time';
    const UPDATED_AT = 'update_date_time';

    protected $connection = 'mysql';
    protected $table = 'npo_contract_template';
    protected $fillable = [
        'name',
        'kind_id',
        'kind_id_tt08',
        'code',
        'description',
        'file_name',
        'file_path',
        'active_flg',
        'relate_object_number',
        'relate_object_A_display',
        'relate_object_B_display',
        'relate_object_C_display',
        'period_flag',
        'period_req_flag',
        'mortage_cancel_func',
        'sync_option',
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
