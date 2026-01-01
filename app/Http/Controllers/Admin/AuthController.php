<?php

namespace App\Http\Controllers\Admin;

use App\Models\ChiNhanhModel;
use App\Http\Controllers\JoshController;
use App\Http\Controllers\users;
use App\Http\Requests\ConfirmPasswordRequest;
use App\Http\Requests\ForgotRequest;
use App\Http\Requests\UserRequest;
use App\Models\NhanVienModel;
use App\Models\SModel;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Reminder;
// use Sentinel;
use stdClass;
use URL;
use Validator;
use View;
use Illuminate\Support\Facades\Hash;
use App\ActivityLogModel;
use Carbon\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
class AuthController extends JoshController
{
    use users;

    /**
     * Account sign in.
     *
     * @return View
     */
    public function getSignin()
    {
        // Is the user logged in?
        if (Sentinel::check()) {
            return Redirect::route('admin');
        }

        // Show the page
        return view('admin.login');
    }

    /**
     * Account sign in form processing.
     * @param Request $request
     * @return Redirect
     */
   public function postSignin(Request $request)
{
    try {
        /* ===== 1. CHECK SERVER KEY ===== */
        $ip = $_SERVER['SERVER_ADDR']
            ?? $_SERVER['LOCAL_ADDR']
            ?? '192.168.18.47';
// dd($ip);
        $key = 'notarysoft';
        $decry = SModel::first();

        if (!$decry) {
            return back()->withInput()
                ->with('swal_error', 'Unrecognizable key for this server');
        }

        [$hashIp, $hashKey] = explode('~', $decry->val);

        if (!Hash::check($ip, $hashIp) || !Hash::check($key, $hashKey)) {
            return back()->withInput()
                ->with('swal_error', 'Unrecognizable key for this server');
        }

        /* ===== 2. AUTHENTICATE ===== */
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember-me');

        if (!$user = Sentinel::authenticate($credentials, $remember)) {
            return back()->withInput()
                ->with('swal_error', 'Tài khoản hoặc mật khẩu không chính xác');
        }

        /* ===== 3. CHECK NHÂN VIÊN ===== */
        $nhanVien = NhanVienModel::find($user->id);

        if (!$nhanVien) {
            Sentinel::logout();
            return back()->with('swal_error', 'Tài khoản chưa được gán nhân viên');
        }

        /* ===== 4. CHECK LOGIN CODE ===== */
        $chiNhanh = ChiNhanhModel::find($nhanVien->nv_vanphong);

        if ($chiNhanh && $chiNhanh->login_code && $chiNhanh->login_code != $request->login_code) {
            Sentinel::logout();
            return back()->with('swal_error', 'Mã truy cập không đúng');
        }

        /* ===== 5. LOG ===== */
        $this->api_create_log($user->id, 'Đăng nhập');

        return redirect()->route('admin')
            ->with('success', 'Đăng nhập thành công');

    } catch (NotActivatedException $e) {
        return back()->withInput()
            ->with('swal_warning', 'Tài khoản chưa được kích hoạt');

    } catch (ThrottlingException $e) {
        return back()->withInput()
            ->with('swal_error', 'Tài khoản tạm thời bị khóa do đăng nhập sai nhiều lần');
    }
}


    /**
     * Account sign up form processing.
     *
     * @return Redirect
     */
    public function postSignup(UserRequest $request)
    {

        try {
            // Register the user
            $user = Sentinel::registerAndActivate([
                'first_name' => $request->get('first_name'),
                'last_name' => $request->get('last_name'),
                'email' => $request->get('email'),
                'password' => $request->get('password'),
            ]);

            //add user to 'User' group
            $role = Sentinel::findRoleById(2);
            $role->users()->attach($user);


            // Log the user in
            $name = Sentinel::login($user, false);
            //Activity log

            activity($name->full_name)
                ->performedOn($user)
                ->causedBy($user)
                ->log('Registered');
            //activity log ends
            // Redirect to the home page with success menu
            return Redirect::route("admin.dashboard")->with('success', trans('auth/message.signup.success'));
        } catch (UserExistsException $e) {
            $this->messageBag->add('email', trans('auth/message.account_already_exists'));
        }

        // Ooops.. something went wrong
        return Redirect::back()->withInput()->withErrors($this->messageBag);
    }

    /**
     * User account activation page.
     *
     * @param number $userId
     * @param string $activationCode
     * @return
     */
    public function getActivate($userId, $activationCode = null)
    {
        // Is user logged in?
        if (Sentinel::check()) {
            return Redirect::route('admin.dashboard');
        }

        $user = Sentinel::findById($userId);
        $activation = Activation::create($user);

        if (Activation::complete($user, $activation->code)) {
            // Activation was successful
            // Redirect to the login page
            return Redirect::route('signin')->with('success', trans('auth/message.activate.success'));
        } else {
            // Activation not found or not completed.
            $error = trans('auth/message.activate.error');
            return Redirect::route('signin')->with('error', $error);
        }
    }

    /**
     * Forgot password form processing page.
     * @param Request $request
     *
     * @return Redirect
     */
    public function postForgotPassword(ForgotRequest $request)
    {
        $data = new stdClass();

        try {
            // Get the user password recovery code
            $user = Sentinel::findByCredentials(['email' => $request->get('email')]);

            if (!$user) {
                return back()->with('error', trans('auth/message.account_email_not_found'));
            }
            $activation = Activation::completed($user);
            if (!$activation) {
                return back()->with('error', trans('auth/message.account_not_activated'));
            }
            $reminder = Reminder::exists($user) ?: Reminder::create($user);
            // Data to be used on the email view

            $data->user_name = $user->first_name . ' ' . $user->last_name;
            $data->forgotPasswordUrl = URL::route('forgot-password-confirm', [$user->id, $reminder->code]);

            // Send the activation code through email

            Mail::to($user->email)
                ->send(new ForgotPassword($data));
        } catch (UserNotFoundException $e) {
            // Even though the email was not found, we will pretend
            // we have sent the password reset code through email,
            // this is a security measure against hackers.
        }

        //  Redirect to the forgot password
        return back()->with('success', trans('auth/message.forgot-password.success'));
    }

    /**
     * Forgot Password Confirmation page.
     *
     * @param number $userId
     * @param  string $passwordResetCode
     * @return View
     */
    public function getForgotPasswordConfirm($userId, $passwordResetCode = null)
    {
        // Find the user using the password reset code
        if (!$user = Sentinel::findById($userId)) {
            // Redirect to the forgot password page
            return Redirect::route('forgot-password')->with('error', trans('auth/message.account_not_found'));
        }
        if ($reminder = Reminder::exists($user)) {
            if ($passwordResetCode == $reminder->code) {
                return view('admin.auth.forgot-password-confirm');
            } else {
                return 'code does not match';
            }
        } else {
            return 'does not exists';
        }

        // Show the page
        // return View('admin.auth.forgot-password-confirm');
    }

    /**
     * Forgot Password Confirmation form processing page.
     *
     * @param Request $request
     * @param number $userId
     * @param  string $passwordResetCode
     * @return Redirect
     */
    public function postForgotPasswordConfirm(ConfirmPasswordRequest $request, $userId, $passwordResetCode = null)
    {

        // Find the user using the password reset code
        $user = Sentinel::findById($userId);
        if (!$reminder = Reminder::complete($user, $passwordResetCode, $request->get('password'))) {
            // Ooops.. something went wrong
            return Redirect::route('signin')->with('error', trans('auth/message.forgot-password-confirm.error'));
        }

        // Password successfully reseted
        return Redirect::route('signin')->with('success', trans('auth/message.forgot-password-confirm.success'));
    }

    /**
     * Logout page.
     *
     * @return Redirect
     */
    public function getLogout()
    {

        if (Sentinel::check()) {
            //Activity log
            $user = Sentinel::getuser();
            activity($user->full_name)
                ->performedOn($user)
                ->causedBy($user)
                ->log('LoggedOut');
            // Log the user out
            Sentinel::logout();
            // Redirect to the users page
            return redirect('admin/signin')->with('success', 'Đăng xuất tài khoản thành công!');
        } else {

            // Redirect to the users page
            return redirect('admin/signin')->with('error', 'Bạn phải đăng nhập!!');
        }
    }

    /**
     * Account sign up form processing for register2 page
     *
     * @param Request $request
     *
     * @return Redirect
     */
    public function postRegister2(UserRequest $request)
    {

        try {
            // Register the user
            $user = Sentinel::registerAndActivate(array(
                'first_name' => $request->get('first_name'),
                'last_name' => $request->get('last_name'),
                'email' => $request->get('email'),
                'password' => $request->get('password'),
            ));

            //add user to 'User' group
            $role = Sentinel::findRoleById(2);
            $role->users()->attach($user);

            // Log the user in
            Sentinel::login($user, false);

            // Redirect to the home page with success menu
            return Redirect::route("admin.dashboard")->with('success', trans('auth/message.signup.success'));
        } catch (UserExistsException $e) {
            $this->messageBag->add('email', trans('auth/message.account_already_exists'));
        }

        // Ooops.. something went wrong
        return Redirect::back()->withInput()->withErrors($this->messageBag);
    }
    public function historyLogin(Request $request)
    {
        try {
            $query = ActivityLogModel::orderByDesc('created_at');
            $total = $query->get()->groupBy('subject_id')->count();
            $date = $request->date;
            if ($date) {
                $dates = Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
                $query->whereDate('created_at', $dates);
            }
            if (Sentinel::inRole('admin') || Sentinel::check()->isCVS()) {
                $data = $query->get()->groupBy('subject_id');
            } else {
                $currentVp = NhanVienModel::find(Sentinel::check()->id)->nv_vanphong;
                $data = $query->get()->filter(function ($item) use ($currentVp) {

                    $idVp = NhanVienModel::find($item->subject_id)->nv_vanphong;
                    return $idVp == $currentVp;
                })->groupBy('subject_id');
            }

            $count = $data->count();
            return view('admin.history_login.index', compact('data', 'count', 'date', 'total'));
        } catch (Exception $exception) {
            return $exception;
        }
    }
}
