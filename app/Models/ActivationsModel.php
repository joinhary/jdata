<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivationsModel extends Model
{
    protected $table = 'activations';
    protected $fillable = ['id', 'user_id', 'code', 'completed', 'completed_at', 'created_at', 'updated_at'];
    protected $primaryKey = 'id';
}