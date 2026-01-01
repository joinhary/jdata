<?php

namespace App\Http\Controllers;

use App\Mail\Register;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use File;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Redirect;
use Sentinel;
use URL;
use Validator;
use View;

trait users
{
    public function getUserInfo($id)
    {
        $info = User::select('id', 'email', 'phone', 'address','first_name')->find($id);
        return $info;
    }

//    public function getListUsers()
//    {
//        return User::select('email', 'first_name', 'phone', 'address');
//    }
//
//    public function getListUsersWhere($search)
//    {
//        return User::select('email', 'first_name', 'phone', 'address')
//            ->where('email', 'like', '%' . $search . '%')
//            ->orWhere('phone', 'like', '%' . $search . '%')
//            ->orWhere('first_name', 'like', '%' . $search . '%');
//    }

    /**
     * @param Request $request
     * @param $activate
     */
    public function register_api(Request $request): void
    {
//        dd($request->get('activate'));
        $activate = $request->get('activate') ? true : false;

        $user = Sentinel::register($request->except('_token', 'password_confirm', 'roles', 'activate'), $activate);

        //add user to 'User' group
        $role = Sentinel::findRoleById($request->get('roles'));
        if ($role) {
            $role->users()->attach($user);
        }
        //check for activation and send activation mail if not activated by default
//        if (!$request->get('activate')) {
//            // Data to be used on the email view
//            $data = [
//                'user_name' => $user->first_name,
//                'activationUrl' => URL::route('activate', [$user->id, Activation::create($user)->code])
//            ];
//            // Send the activation code through email
//            Mail::to($user->email)
//                ->send(new Register($data));
//        }
        // Activity log for New user create
        $my_id = Sentinel::getUser()->id;
        $description = 'Đã tạo người dùng '. $user->first_name;
        $this->api_create_log($my_id,$description);

    }


    /**
     * @param $my_id ;
     * @param $description
     */
    public function api_create_log($my_id, $description)
    {
        if ($my = Sentinel::findById($my_id)) {
            activity($my->first_name)
                ->performedOn($my)
                ->causedBy($my)
                ->log($description);
            return ['status' => true];
        } else {
            return ['status' => false, 'message' => 'Người dùng không tồn tại'];
        }
    }

    public function api_change_password($id,$password)
    {
        $user = Sentinel::findById($id);
        if (!empty($password)) {
            $user->password = Hash::make($password);
        }
    }


    /**
     * @param $my_id ;
     * @param $description
     */

}
