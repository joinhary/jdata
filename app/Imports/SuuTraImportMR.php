<?php

namespace App\Imports;

use App\BangGiaDichVuModel;
use App\SuuTraModel;
use App\SuuTraMRModel;
use Carbon\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('slug');

//use Maatwebsite\Excel\Concerns\WithHeadingRow;


class SuuTraImportMR implements ToModel , WithStartRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    public function model(array $row)
    {
        if (!isset($row['15'])) {
            return null;
        }
    /*    dump([
            'so_hd_master' => $row['0'],
            'ngay_cc' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['2']),
//            'ngay_cc' => $row['2'],
            'texte'=>$row['15'],
            'loai'=>$row['13'],
            'duong_su'=>$row['7'],
            'ten_hd'=>$row['15'],
            'ccv_master'=>$row['17']]);*/
        if(Sentinel::check()->id==$row['22']) {
            return SuuTraMRModel::create([

                'so_hd_master' => $row['0'],
                'ngay_cc' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['2']),
//            'ngay_cc' => $row['2'],
                'texte' => $row['15'],
                'loai' => $row['13'],
                'duong_su' => $row['7'],
                'ten_hd' => $row['15'],
                'ccv_master' => $row['17'],
                'ccv' => $row['22'],
                'ngaynhap'=>Carbon::today(),

            ]);
        }else{
            return null;

        }

    }
    public function startRow(): int
    {
        return 7;
    }
}
