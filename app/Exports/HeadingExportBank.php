<?php

namespace App\Exports;

use App\Bank;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class  HeadingExportBank implements FromView, WithTitle, WithEvents
{
    private $banks;
    private $data;
    protected $vp;
    protected $dateTo;
    protected $dateFrom;

    public function __construct($banks, $data, $vp, $dateTo, $dateFrom)
    {
        $this->banks = $banks;
        $this->data = $data;
        $this->vp = $vp;
        $this->dateTo = $dateTo;
        $this->dateFrom = $dateFrom;
    }


    public function view(): View
    {
        return view('admin.report.exportBank', [
            'banks'    => $this->banks,
            'data'     => $this->data,
            'vp'       => $this->vp,
            'dateTo'   => $this->dateTo,
            'dateFrom' => $this->dateFrom,
        ]);
    }

    public function title(): string
    {
        return $this->banks->name;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $styleArray = array(
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color'       => ['argb' => '3333333'],
                        ]

                    ]
                );
                $maxrow = $event->getSheet()->getDelegate()->getHighestRow();
                $cellRange2 = "A8:G".$maxrow;
                $cell2 = ['B', 'C', 'D','E','F','G'];
                    $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(5);
 // All headers
                foreach ($cell2 as $cell) {
                    $event->sheet->getDelegate()->getColumnDimension($cell)->setAutoSize(false);
                    $event->sheet->getDelegate()->getColumnDimension($cell)->setWidth(25);
                }
                $event->sheet->getDelegate()->getStyle($cellRange2)->applyFromArray($styleArray);
                $event->sheet->getDelegate()->getStyle($cellRange2)->getAlignment()->setWrapText(true);
            }
        ];
    }
}
