<?php

namespace App\Imports;

use App\BangGiaDichVuModel;
use App\ChiNhanhModel;
use App\Http\Controllers\SuuTraController;
use App\Http\Controllers\users;
use App\NhanVienModel;
use App\RoleUsersModel;
use App\SuuTraModel;
use App\User;
use App\VanBanModel;
use Carbon\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Ixudra\Curl\Facades\Curl;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('slug');

class SuuTraImport implements ToModel, WithStartRow
{
    public function model(array $row)
    {
        $vp = NhanVienModel::find(Sentinel::check()->id)->nv_vanphong;

        if (!isset($row['14'])) {
            return null;
        }
        $ngay_cc = is_numeric($row[2]) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['2']) : $row[2];
        $time = date("Y", strtotime($row['2']));
        $sohd = $row['0'] . '/' . $time;
        $checked = null;
        if (Sentinel::check()->isPC()) {
            $checked = 1;
        }
        if (SuuTraModel::where('so_hd', $sohd)->first() != null && SuuTraModel::where('so_hd', $sohd)->first()->vp == $vp || is_numeric($row['20']) == false || is_numeric($row['21']) == false) {
            return null;

        } else {
            if (RoleUsersModel::where('user_id', Sentinel::check()->id)->first()->role_id == 10 || RoleUsersModel::where('user_id', Sentinel::check()->id)->first()->role_id == 18 || Sentinel::check()->isPC()) {
                $suutra = SuuTraModel::create([
                    'so_hd' => $sohd,
                    'ngay_cc' => date("d/m/Y", strtotime($row['2'])),
                    'texte' => SuuTraController::cleanSpaces($row[14]),
                    'texte_en' => SuuTraController::convert_vi_to_en(SuuTraController::cleanSpaces($row[14])),
                    'duong_su' => SuuTraController::cleanSpaces($row['6']),
                    'duong_su_en' => SuuTraController::convert_vi_to_en(SuuTraController::cleanSpaces($row['6'])),
                    'ten_hd' => $row['12'],
                    'vp' => $vp,
                    'ccv' => $row['20'],
                    'ngan_chan' => $row['22'],
                    'ngay_nhap' => Carbon::today()->format('d/m/Y'),
                    'ma_phan_biet' => 'D',
                ]);
                $id = $suutra->st_id;
                $ma_dong_bo = ChiNhanhModel::find($vp)->code_cn;
                $ten_vanphong = ChiNhanhModel::find($vp)->cn_ten;
                $ten_ccv = NhanVienModel::find($row['20'])->nv_hoten;
                $ngay = date("d/m/Y", strtotime($row['2']));
                $ngay_cc = Carbon::parse($ngay)->format('Y-m-d');
//                $stp = Curl::to('http://127.0.0.1:8000/api/push-data-suutra-stp')
                $stp = Curl::to('http://dev-k.dotary.net/api/push-data-suutra-stp')
                    ->withData([
                        'synchronize_id' => $ma_dong_bo . $id . '_D',
                        'type' => '01',
                        'property_info' => SuuTraController::cleanSpaces($row[14]),
                        'transaction_content' => SuuTraController::cleanSpaces($row[14]),
                        'notary_date' => $ngay_cc,
                        'notary_office_name' => $ten_vanphong,
                        'contract_number' => $sohd . '_D',
                        'contract_name' => $row['12'],
                        'relation_object' => SuuTraController::cleanSpaces($row['6']),
                        'notary_person' => $ten_ccv,
                        'notary_place' => $ten_vanphong,
                        'cancel_status' => 0,
                        'entry_user_name' => $ten_ccv . '_D',
                        'update_user_name' => $ten_ccv . '_D',
                    ])->post();
                SuuTraModel::where('st_id', $id)->update(['ma_dong_bo' => $ma_dong_bo . $id . '_D']);
                return $suutra;

            } else {
                $suutra = SuuTraModel::create([
                    'so_hd' => $sohd,
                    'ngay_cc' => date("d/m/Y", strtotime($row['2'])),
                    'texte' => SuuTraController::cleanSpaces($row[14]),
                    'texte_en' => SuuTraController::convert_vi_to_en(SuuTraController::cleanSpaces($row[14])),
                    'duong_su' => SuuTraController::cleanSpaces($row['6']),
                    'duong_su_en' => SuuTraController::convert_vi_to_en(SuuTraController::cleanSpaces($row['6'])),
                    'ten_hd' => $row['12'],
                    'vp' => $vp,
                    'ccv' => $row['20'],
                    'ngay_nhap' => Carbon::today()->format('d/m/Y'),
                    'status' => $checked
                ]);
                $id = $suutra->st_id;
                $ma_dong_bo = ChiNhanhModel::find($vp)->code_cn;
                $ten_vanphong = ChiNhanhModel::find($vp)->cn_ten;
                $ten_ccv = NhanVienModel::find($row['20'])->nv_hoten;
                $ngay = date("d/m/Y", strtotime($row['2']));
                $ngay_cc = Carbon::parse($ngay)->format('Y-m-d');
//                $stp = Curl::to('http://127.0.0.1:8000/api/push-data-suutra-stp')
                $stp = Curl::to('http://dev-k.dotary.net/api/push-data-suutra-stp')
                    ->withData([
                        'synchronize_id' => $ma_dong_bo . $id . '_D',
                        'type' => '01',
                        'property_info' => SuuTraController::cleanSpaces($row[14]),
                        'transaction_content' => SuuTraController::cleanSpaces($row[14]),
                        'notary_date' => $ngay_cc,
                        'notary_office_name' => $ten_vanphong,
                        'contract_number' => $sohd . '_D',
                        'contract_name' => $row['12'],
                        'relation_object' => SuuTraController::cleanSpaces($row['6']),
                        'notary_person' => $ten_ccv,
                        'notary_place' => $ten_vanphong,
                        'cancel_status' => 0,
                        'entry_user_name' => $ten_ccv . '_D',
                        'update_user_name' => $ten_ccv . '_D',
                    ])->post();
                SuuTraModel::where('st_id', $id)->update(['ma_dong_bo' => $ma_dong_bo . $id . '_D']);
                return $suutra;
            }
        }
    }

    public function startRow(): int
    {
        return 7;
    }
}
