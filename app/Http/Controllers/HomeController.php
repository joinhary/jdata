<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Models\Email;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('admin');
    // }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }
    public function send_email(Request $request)
    {
        $code = rand(1000,9999);
        Email::where('email',$request->e)->delete();
        $e = Email::create([
            'email'=>$request->e,
            'code'=>$code
        ]);
//        dd(Email::all());
        Mail::raw('Active Code: '.$code, function ($message) use($request) {
            $message->from(env('MAIL_USERNAME'), $name = null);
            $message->sender(env('MAIL_USERNAME'), $name = null);
            $message->subject('Active Code');
            $message->to($request->e, $name = null);
        });
    }
}
