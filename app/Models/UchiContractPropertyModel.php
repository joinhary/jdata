<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UchiContractPropertyModel extends Model
{
    protected $connection = 'mysql';
    public $incrementing = false;
    public $timestamps = false;

    protected $table = 'npo_contract_property';
    protected $fillable = [
        'contract_id',
        'property_id'
    ];
}
