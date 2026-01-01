<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuuTraLogModel extends Model
{
    protected $table = 'suutranb_log';
    protected $fillable
        = [
            'id',
            'suutra_id',
            'log_content',
            'user_id',
            'created_at',
            'updated_at',
            'so_hd',
			'uchi_id',
			'office_code',
			'execute_person',
			'execute_content',
			'contract_content',
            'flag_des'

        ];
    protected $keyType = 'string';
    protected $casts = [
    'id' => 'string',
];

    protected $primaryKey = 'id';

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
}
