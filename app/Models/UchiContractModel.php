<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UchiContractModel extends Model
{
    const CREATED_AT = 'entry_date_time';
    const UPDATED_AT = 'update_date_time';

    protected $connection = 'mysql';

    protected $table = 'npo_contract';
    protected $fillable = [
        'contract_template_id',
        'contract_number',
        'contract_value',
        'relation_object_A',
        'relation_object_B',
        'relation_object_C',
        'notary_id',
        'drafter_id',
        'received_date',
        'notary_date',
        'user_require_contract',
        'number_copy_of_contract',
        'number_of_page',
        'cost_tt91',
        'cost_draft',
        'cost_notary_outsite',
        'cost_other_determine',
        'cost_total',
        'notary_place_flag',
        'notary_place',
        'bank_id',
        'bank_service_fee',
        'crediter_name',
        'file_name',
        'file_path',
        'error_status',
        'error_user_id',
        'error_description',
        'addition_status',
        'addition_description',
        'cancel_status',
        'cancel_description',
        'cancel_relation_contract_id',
        'contract_period',
        'mortage_cancel_flag',
        'mortage_cancel_date',
        'mortage_cancel_note',
        'original_store_place',
        'note',
        'summary',
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
