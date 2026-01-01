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


class HeadingExportNotaryBook implements FromView, WithTitle, WithEvents
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }


    public function view(): View
    {
        return view('admin.report.exportNotaryBook', [
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
                $maxrow = $event->getSheet()->getDelegate()->getHighestRow() - 12;
                $cellRange = "A9:I" . $lrow;
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
                    $cellRange2 = "A9:J" . $maxrow;

                } else {
                    $cellRange2 = "A9:J" . $maxrow;

                }

                $event->sheet->getDelegate()->getStyle($cellRange2)->applyFromArray($styleArray);
                $cell2 = [
                    'A',
                    'B',
                    'D',
                    'E',
                    'F',
                    'G',
                    'I',
                ];
                $cell3 = [
                    'C',
                    'H',
                ];
                // All headers
                foreach ($cell2 as $cell) {
                    $event->sheet->getDelegate()->getColumnDimension($cell)->setAutoSize(false);
                    $event->sheet->getDelegate()->getColumnDimension($cell)->setWidth(15);
                }
                foreach ($cell3 as $cell) {
                    $event->sheet->getDelegate()->getColumnDimension($cell)->setAutoSize(false);
                    $event->sheet->getDelegate()->getColumnDimension($cell)->setWidth(50);
                }
                $event->sheet->getDelegate()->getRowDimension(1)->setRowHeight(35);
//                $event->sheet->getDelegate()->getRowDimension(2)->setRowHeight(25);
//                $event->sheet->getDelegate()->getRowDimension(3)->setRowHeight(25);
//                $event->sheet->getDelegate()->getRowDimension(4)->setRowHeight(25);
                $event->sheet->getDelegate()->getRowDimension(6)->setRowHeight(35);
                $event->sheet->getDelegate()->getRowDimension(7)->setRowHeight(50);
//                $event->sheet->getDelegate()->getRowDimension(8)->setRowHeight(10);
                $event->sheet->getDelegate()->getRowDimension(9)->setRowHeight(100);
//                $event->sheet->getDelegate()->getRowDimension(9)->setRowHeight(40);
                for ($i = 11; $i <= $maxrow; $i++) {
                    $event->sheet->getDelegate()->getRowDimension($i)->setRowHeight(80);
                }
            },
        ];
    }

}
