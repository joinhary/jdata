<?php

namespace App\Http\Controllers\SPP;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Models\User;
use App\Http\Controllers\SPP\SPPSolariumController;

class SPPController extends Controller
{
    protected $client;

    public function __construct(\Solarium\Client $client)
    {
        $this->client = $client;
    }
    public function success($data, $message)
    {
        return response()->json([
            'status' => 200,
            'message' => $message,
            'data' => $data
        ]);
    }
    public function error($message)
    {
        return response()->json([
            'status' => 500,
            'message' => $message,
            'data' => ''
        ]);
    }
    public function getToken(Request $request)
    {
        $credentials = $request->only(['email', 'password']);


        // Kiểm tra thông tin đăng nhập với Sentinel
        if ($user = Sentinel::authenticate($credentials)) {
            // Tạo JWT token
            $token = JWTAuth::fromUser($user);
            $expires_in = time() + 60 * 60 * 24 * 30;
            $update_user = User::where('id', $user->id)->first();
            if ($update_user->expried_token == null) {
                $update_user->expried_token = $expires_in;
                $update_user->api_token = $token;
            } elseif ($update_user->expried_token < time()) {
                $update_user->expried_token = $expires_in;
                $update_user->api_token = $token;
            } else {
                $token = $update_user->api_token;
            }

            $update_user->save();
            return $this->success([
                'token' => $token,
                'user' => $user,
            ], 'Token created successfully');
        } else {
            return $this->error('Invalid credentials');
        }
    }

    public function vpccList()
    {
        $chinhanh = DB::table('chinhanh')->select('code_cn', 'cn_ten')->get();
        return $this->success($chinhanh, 'Sucess');
    }
    public function suutra(Request $request)
    {
        $spp = new SPPSolariumController($this->client);
        $result = $spp->searchApi($request);
        return $this->success($result, 'Success');
    }
}
