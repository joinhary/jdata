<?php

namespace App\Exports;

use App\Models\NhanVienModel;
use App\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;

class export12b implements WithMultipleSheets
{
    /**
     * @return \Illuminate\Support\Collection
     */
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

    public function sheets(): array
    {
        $sheets[1] = new HeadingExport12b($this->data, $this->total, $this->dateFrom, $this->dateTo);
        return $sheets;
    }
}
