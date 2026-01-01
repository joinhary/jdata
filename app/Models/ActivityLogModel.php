<?php

namespace App\Models;

use App\Helper\Helpers;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ActivityLogModel extends Model
{
    protected $table = 'activity_log';
    protected $primaryKey = 'id';
    protected $fillable
        = [
            'id',
            'log_name',
            'description',
            'subject_id',
            'subject_type',
            'causer_id',
            'causer_type',
            'properties',
            'created_at',
            'updated_at',

        ];
    protected $dates
        = [
            'created_at',
            'updated_at',
        ];
    protected function serializeDate(\DateTimeInterface $date): string
    {
        $timezone = "Asia/Ho_Chi_Minh";
        return $date->setTimezone($timezone)->format('d/m/Y H:i:s');
    }

}
