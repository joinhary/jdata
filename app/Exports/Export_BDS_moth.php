<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use App\ChiNhanhModel;

class Export_BDS_moth implements FromView
{

    public function __construct($data, $month)
    {
        $this->data = $data;
        $this->month = $month;
    }
    public function view(): View
    {
        $oders = ChiNhanhModel::all();
        $data = $this->data;
        $month = $this->month;
        return view('admin.report.exportBDS_moth', compact('data', 'month'));
    }
}
