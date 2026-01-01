<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConvertController extends Controller
{
    use BoDuLieu;

    public function index()
    {
        return view('admin.convert.index');
    }

    public function read_number(Request $request)
    {
        if (is_numeric($request->money)) {
            $data = $this->convert_number_to_string($request->money);
            return ['status' => 'success', 'data' => $data];
        } else {
            return ['status' => 'error', 'message' => 'Input data error, may be false data type given!'];
        }
    }

    public function read_date(Request $request)
    {
        $data = $this->spell_date($request->date);
        return ['status' => 'success', 'data' => $data];
    }
}
