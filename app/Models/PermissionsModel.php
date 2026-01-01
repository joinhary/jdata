<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class PermissionsModel extends Model
{
    protected $table = 'permissions';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'group', 'permissions', 'description', 'created_at', 'updated_at'];
}