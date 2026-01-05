<?php

namespace App\Imports;

use App\BangGiaDichVuModel;
use Maatwebsite\Excel\Concerns\ToModel;


use Maatwebsite\Excel\Concerns\WithHeadingRow;


class BangGiaDichVuImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    public function model(array $row)
    {
        if (!isset($row['dich_vu'])) {
            return null;
        }
//        dd($row);
        return BangGiaDichVuModel::create([
            'dichvu' => $row['dich_vu'],
            'phi' => $row['phi'],
            'thu_lao' => $row['thu_lao'],
            'chiphi_khac' => $row['chi_phi_khac'],
            'ngayapdung' => date_create(str_replace('/', '-', $row['ngay_ap_dung'])),
        ]);

    }

    public function headingRow(): int
    {
        return 1;
    }
}
