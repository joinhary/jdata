<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorySearchModel extends Model
{
    protected $table = 'history_search';
    protected $fillable
        = [
            'id',
            'user_id',
            'url',
            'client_ip',
            'created_at',
            'updated_at',
            'vp_id',
            'file'
        ];

    public function user()
    {
        return $this->hasOne(User::class,'id','user_id');
    }
}
