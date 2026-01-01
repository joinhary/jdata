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


class HeadingExportBDS implements FromView, WithTitle, WithEvents
{

  private $data;

  private $dateForm;

  private $dateTo;

  private $export;

  private $exportThue;

  public function __construct($data, $dateForm, $dateTo, $export, $exportThue)
  {
    $this->data       = $data;
    $this->dateForm   = $dateForm;
    $this->dateTo     = $dateTo;
    $this->export     = $export;
    $this->exportThue = $exportThue;
  }

  public function view(): View
  {
    return view('admin.report.exportBDS', [
      'data'       => $this->data,
      'dateForm'   => $this->dateForm,
      'dateTo'     => $this->dateTo,
      'export'     => $this->export,
      'exportThue' => $this->exportThue,
    ]);
  }

  /**
   * @return string
   */
  public function title(): string
  {
    return 'BDS';
  }

  public function registerEvents(): array
  {
    return [
      AfterSheet::class => function (AfterSheet $event) {
        $styleArray = [
          'borders' => [
            'allBorders' => [
              'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
              'color'       => ['argb' => '00000000'],
            ],
          ],
        ];
        $maxrow     = $event->getSheet()->getDelegate()->getHighestRow();
        $maxrow2    = $maxrow - 11;
        $cellRange  = "A8:K" . $maxrow;
        $cellRange2 = "A8:K" . $maxrow2;
        $cell2 = ['B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'];
        $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(5);

        // All headers
        foreach ($cell2 as $cell) {
          $event->sheet->getDelegate()->getColumnDimension($cell)->setAutoSize(false);
          $event->sheet->getDelegate()->getColumnDimension($cell)->setWidth(25);
        }
        $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setWrapText(TRUE);
        $event->sheet->getDelegate()->getStyle($cellRange2)->applyFromArray($styleArray);
      },
    ];
  }
}