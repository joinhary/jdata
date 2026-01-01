<?php
/**
 * Created by PhpStorm.
 * User: Ahihi
 * Date: 8/4/2019
 * Time: 6:40 PM
 */
namespace App\Exports;

use App\ChiNhanhModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ChinhanhExportSheet implements FromCollection, WithTitle, WithHeadings
{
    public function collection()
    {
        return ChiNhanhModel::get(['cn_ten','cn_id']);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Văn phòng';
    }
    public function headings(): array
    {
        return [
            'Tên văn phòng',
            'Mã ván phòng',
        ];
    }
}