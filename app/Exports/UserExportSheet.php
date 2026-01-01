<?php
/**
 * Created by PhpStorm.
 * User: Ahihi
 * Date: 8/4/2019
 * Time: 6:40 PM
 */
namespace App\Exports;

use App\RoleModel;
use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class UserExportSheet implements FromCollection, WithTitle,WithHeadings
{
    public function collection()
    {
        $role = RoleModel::whereNotIn('slug',['admin','khach-hang','quan-tri-vien'])->pluck('id');
        $user = User::join('role_users','id','=','user_id')
            ->whereIn('role_id',$role)->get(['first_name as name','id']);
        return $user;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Nhân viên';
    }
    public function headings(): array
    {
        return [
            'Tên Nhân viên',
            'Mã nhân viên',
        ];
    }
}