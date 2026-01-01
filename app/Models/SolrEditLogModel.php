<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolrEditLogModel extends Model
{
    protected $table = 'solr_edit_log';
    public $incrementing = true;
    protected $fillable = [
        'id',
    'user_id',
    'data_before',
    'data_after'
    ];
}
