<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    public function error404() {
        return view('admin.404');
    }

    public function error500() {
        return view('admin.500');
    }

    public function home() {
        return redirect('/admin/login');
    }

    public function customerClose() {
        return view('admin.khachhang.close');
    }

    public function checkProgress() {
        return response()->json([
            'percent' => session('progress', 0),
        ]);
    }

    public function getSyncLogs() {
        return response()->json([
            'logs' => session('sync_logs', [])
        ]);
    }

    public function getLiveLogs() {
        $logs = session('live_logs', []);
        session()->forget('live_logs');
        return response()->json(['logs' => $logs]);
    }
}
