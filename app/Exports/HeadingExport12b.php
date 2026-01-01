<?php

namespace App\Exports;

use App\Models\NhanVienModel;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class  HeadingExport12b implements FromView, WithTitle, WithEvents
{
    private $data;
    private $total;
    private $dateFrom;
    private $dateTo;

    public function __construct($data, $total, $dateFrom, $dateTo)
    {
        $this->data = $data;
        $this->total = $total;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function view(): View
    {
        return view('admin.report.export12b', [
            'data' => $this->data,
            'total' => $this->total,
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return '12B';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $lrow = $event->getSheet()->getDelegate()->getHighestRow();
                $maxrow = $event->getSheet()->getDelegate()->getHighestRow() - 7;
                $cellRange = "A1:L" . $lrow;
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
                $cellRange2 = "A2:L" . $maxrow;
                $event->sheet->getDelegate()->getStyle($cellRange2)->applyFromArray($styleArray);
                $cell2 = ['A', 'B', 'C', 'D', 'E', 'F',
                    'G', 'H', 'I', 'J', 'K', 'L',];

                // All headers
                foreach ($cell2 as $cell) {
                    $event->sheet->getDelegate()->getColumnDimension($cell)->setAutoSize(false);
                    $event->sheet->getDelegate()->getColumnDimension($cell)->setWidth(13);
                }
//                $event->sheet->getDelegate()->getRowDimension(7)->setRowHeight(40);
                $event->sheet->getDelegate()->getRowDimension(1)->setRowHeight(115);

//                for ($i = 8; $i <= $maxrow; $i++) {
//                    $event->sheet->getDelegate()->getRowDimension($i)->setRowHeight(60);
//
//                }

            },
        ];
    }
}
