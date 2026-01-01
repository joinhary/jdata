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

class ExportBDS implements WithMultipleSheets
{
    /**
     * @return \Illuminate\Support\Collection
     */
    private $data;
    private $dateForm;
    private $dateTo;
    private $export;
    private $exportThue;

    public function __construct($data,$dateForm,$dateTo,$export,$exportThue)
    {
        $this->data = $data;
		$this->dateForm = $dateForm;
		$this->dateTo = $dateTo;
		$this->export = $export;
		$this->exportThue = $exportThue;

    }

    public function sheets(): array
    {
        $sheets[1] = new HeadingExportBDS($this->data,$this->dateForm,$this->dateTo,$this->export,$this->exportThue);
        return $sheets;
    }
}
