<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UchiTransactionPropertyModel extends Model
{
    protected $connection = 'sqlsrv2';
    const CREATED_AT = 'entry_date_time';
    const UPDATED_AT = 'update_date_time';

    protected $table = 'npo_transaction_property';
    protected $fillable = [
        'synchronize_id',
        'type',
        'property_info',
        'land_street',
        'land_district',
        'land_province',
        'land_full_of_area',
        'transaction_content',
        'notary_date',
        'notary_office_name',
        'contract_id',
        'contract_number',
        'contract_name',
        'contract_kind',
        'contract_value',
        'relation_object',
        'notary_person',
        'notary_place',
        'notary_fee',
        'note',
        'contract_period',
        'mortage_cancel_flag',
        'mortage_cancel_date',
        'mortage_cancel_note',
        'cancel_status',
        'cancel_description',
        'entry_user_id',
        'entry_user_name',
        'entry_date_time',
        'update_user_id',
        'update_user_name',
        'update_date_time',
    ];

    protected $primaryKey = 'tpid';

    protected $dates = ['entry_date_time', 'update_date_time'];
    protected $dateFormat = 'Y-m-d H:i:s';
}
