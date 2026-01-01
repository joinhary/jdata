<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UchiUsersModel extends Model
{
    const CREATED_AT = 'entry_date_time';
    const UPDATED_AT = 'update_date_time';

    protected $connection = 'sqlsrv2';

    protected $table = 'npo_user';
    protected $fillable = [
        'family_name',
        'first_name',
        'account',
        'password',
        'sex',
        'active_flg',
        'hidden_flg',
        'role',
        'birthday',
        'telephone',
        'mobile',
        'email',
        'address',
        'last_login_date',
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
