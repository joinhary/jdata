<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UchiPropertyModel extends Model
{
    protected $connection = 'mysql';
    public $timestamps = false;

    protected $table = 'npo_property';
    protected $fillable = [
        'type',
        'property_info',
        'owner_info',
        'other_info',
        'land_certificate',
        'land_issue_place',
        'land_issue_date',
        'land_map_number',
        'land_number',
        'land_address',
        'land_area',
        'land_public_area',
        'land_private_area',
        'land_use_purpose',
        'land_use_period',
        'land_use_origin',
        'land_associate_property',
        'land_street',
        'land_district',
        'land_province',
        'land_full_of_area',
        'car_license_number',
        'car_regist_number',
        'car_issue_place',
        'car_issue_date',
        'car_frame_number',
        'car_machine_number',
    ];

    protected $primaryKey = 'id';
}
