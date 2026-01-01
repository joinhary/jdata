<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class KieuModel extends Model
{
    protected $table = 'kieu';
    protected $fillable = [
        'k_id',
        'k_nhan',
        'k_keywords',
        'k_parent',
        'k_tieumuc',
        'k_ganvoi',
        'k_trangthai',
        'deleted_at'
    ];

    protected $primaryKey = 'k_id';
    public function children()
    {
        return $this->hasMany('App\Models\KieuModel', 'k_parent', 'k_id');
    }

    public function parent()
    {
        return $this->belongsTo('App\Models\KieuModel', 'k_id', 'k_parent');
    }

    public function getAllChildren()
    {
        $sections = new Collection();
        foreach ($this->children as $section) {
            $sections->push($section);
            $sections = $sections->merge($section->getAllChildren());
        }
        return $sections;
    }
}
