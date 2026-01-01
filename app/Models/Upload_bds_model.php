<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Upload_bds_model extends Model
{
    protected $table = 'upload_bds';
    protected $fillable = [
        'id',
        'user_id',
        'name',
        'date',
        'vpcc_id',
        'file',
        'created_at',
        'updated_at',
        'vpcc_name',
        'accepted',
        'edit_description'

    ];
}