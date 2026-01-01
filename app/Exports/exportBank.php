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

class exportBank implements WithMultipleSheets
{
    protected $banks;
    protected $data;
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

    public function sheets(): array
    {
        $sheets = [];
        foreach ($this->banks as $val) {
            $arr = [];
            foreach ($this->data as $item) {

                if ($item->bank_id == $val->id) {
                    array_push($arr, $item);
                }
            }
            $sheets[] = new HeadingExportBank($val, $arr, $this->vp, $this->dateTo, $this->dateFrom);
        }
        return $sheets;
    }
}