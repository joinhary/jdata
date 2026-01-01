<?php

namespace App\Exports;

use App\HopDongModel;
use App\KeToanModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Sentinel;
use App\Models\NhanVienModel;
use App\User;
use App\VanBanModel;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class BCTKExportNhom implements FromView, WithTitle, ShouldAutoSize, WithEvents
{

    private $count;
    private $nhom;
    private $tong;

    public function __construct($count, $nhom, $tong)
    {
        $this->count = $count;
        $this->nhom = $nhom;
        $this->tong = $tong;

    }

    public function view(): View
    {
        return view('admin.report.export_theo_nhom', [
            'count' => $this->count,
            'nhom' => $this->nhom,
            'tong' => $this->tong

        ]);
    }

    public function title(): string
    {
        return 'Báo cáo thống kê';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $lrow = $event->getSheet()->getDelegate()->getHighestRow();
                $maxrow = $event->getSheet()->getDelegate()->getHighestRow();
                $cellRange = "A1:D" . $lrow;

                // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setWrapText(true);
//                $event->sheet->getDelegate()->getStyle("E4:G4")->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setVertical(Alignment::VERTICAL_TOP);

                $styleArray = array(
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '00000000'],
                        ]

                    ]
                );


                $cellRange2 = "A1:C" . ($maxrow);


                $event->sheet->getDelegate()->getStyle($cellRange2)->applyFromArray($styleArray);
                $cell2 = ['B','C'];

                // All headers
                foreach ($cell2 as $cell) {
                    $event->sheet->getDelegate()->getColumnDimension($cell)->setAutoSize(false);

                    $event->sheet->getDelegate()->getColumnDimension($cell)->setWidth(40);
                }

                $event->sheet->getDelegate()->getRowDimension(1)->setRowHeight(40);

//                for ($i = 6; $i <= $maxrow - 2; $i++) {
//                    $event->sheet->getDelegate()->getRowDimension($i)->setRowHeight(60);
//
//                }

            },
        ];
    }
}
