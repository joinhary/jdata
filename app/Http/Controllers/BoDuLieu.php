<?php


namespace App\Http\Controllers;


use App\Models\BoDuLieuModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use NumberFormatter;

trait BoDuLieu
{
    public function listbodulieu()
    {
        $bodulieu = BoDuLieuModel::select('bdl_id', 'bdl_ten');
        return $bodulieu;
    }

    public function convert_number_to_string($number)
    {
        $fn = new NumberFormatter('vi', NumberFormatter::SPELLOUT);
        return $fn->format($number);
    }

    public function spell_date($date_inp){
        $dn = new NumberFormatter('vi', NumberFormatter::SPELLOUT);
        $dstring = Carbon::parse($date_inp);
        $date = $dstring->day;
        $month = $dstring->month;
        $year = $dstring->year;
        $date_string = 'Ngày ' . $dn->format($date) . ' tháng ' . $dn->format($month) . ' năm ' . $dn->format($year);
        return  $date_string;
    }
}