<?php
/**
 * Created by PhpStorm.
 * User: Ahihi
 * Date: 8/4/2019
 * Time: 6:40 PM
 */
namespace App\Exports;

use App\Models\NhanVienModel;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class  HeadingSuuTraSheet implements FromView, WithTitle
{
    public function view() :View
    {
        return view('admin.suutra.export-example');
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Import';
    }
}
