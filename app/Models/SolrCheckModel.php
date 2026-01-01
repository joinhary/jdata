<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolrCheckModel extends Model
{
    protected $table = 'solr_check';
    protected $fillable = [
        'id',
    'st_id',
    'status',
    'note'
    ];
}