<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleUsersModel extends Model
{
    public $incrementing = false;
    protected $table = 'role_users';
    protected $fillable = [
        'user_id',
        'role_id',
    ];

    public function role()
    {
        return $this->belongsTo(RoleModel::class, 'role_id', 'id');
    }
}
