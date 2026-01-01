<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KieuHopDongController extends Controller
{
    public function index()
    {
        return view('admin.kieuhopdongs.index');
    }
}
