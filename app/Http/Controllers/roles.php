<?php

namespace App\Http\Controllers;


use App\Models\RoleModel;

trait roles
{
    public function listRoles(){
        $roles = RoleModel::select('id','name')
                    ->whereNotIn('name',['Admin','Khách hàng']);
        return $roles;
    }
}