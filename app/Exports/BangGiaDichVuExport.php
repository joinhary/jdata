<?php

namespace App\Exports;

use App\BangGiaDichVuModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BangGiaDichVuExport implements FromCollection, WithHeadings
{
    public function headings(): array
    {
        return [
            'Dịch vụ',
            'Phí',
            'Thù lao',
            'Chi phí khác',
            'Ngày áp dụng'
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return BangGiaDichVuModel::get(['dichvu', 'phi', 'thu_lao', 'chiphi_khac','ngayapdung']);
    }
}
