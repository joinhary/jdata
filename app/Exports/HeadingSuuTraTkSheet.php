<?php
/**
 * Created by PhpStorm.
 * User: Ahihi
 * Date: 8/4/2019
 * Time: 6:40 PM
 */

namespace App\Exports;

use App\Models\NhanVienModel;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;


class HeadingSuuTraTkSheet implements FromView, WithTitle, WithEvents
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }


    public function view(): View
    {
        return view('admin.report.export', [
            'data' => $this->data
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Sưu tra';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $lrow = $event->getSheet()->getDelegate()->getHighestRow();
                $maxrow = $event->getSheet()->getDelegate()->getHighestRow() - 3;
                $cellRange = "A7:M" . $lrow;

                // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setWrapText(true);

                $styleArray = array(
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '00000000'],
                        ]

                    ]
                );
                if (NhanVienModel::find(Sentinel::getUser()->id)->nv_vanphong == 18) {
                    $cellRange2 = "A7:M" . $maxrow;

                } else {
                    $cellRange2 = "A7:I" . $maxrow;

                }

                $event->sheet->getDelegate()->getStyle($cellRange2)->applyFromArray($styleArray);
                $cell2 = ['B', 'C', 'D', 'E', 'F',
                    'G', 'H', 'I', 'J', 'K', 'L', 'M'];

                // All headers
                foreach ($cell2 as $cell) {
                    $event->sheet->getDelegate()->getColumnDimension($cell)->setAutoSize(false);
                    $event->sheet->getDelegate()->getColumnDimension($cell)->setWidth(15);
                }
                $event->sheet->getDelegate()->getRowDimension(7)->setRowHeight(40);

                for ($i = 8; $i <= $maxrow; $i++) {
                    $event->sheet->getDelegate()->getRowDimension($i)->setRowHeight(60);

                }

            },
        ];
    }

}
