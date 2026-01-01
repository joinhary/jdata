<?php

namespace App\Models;

use App\Models\KieuModel;
use App\Models\KieuTieuMucModel;
use App\Models\Template;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaiSanModel extends Model
{
    use SoftDeletes;

    protected $table = 'taisan';
    protected $fillable = [
        'ts_id',
        'change_type',
        'ts_nhan',
        'ts_trangthai',
        'ts_kieu',
        'created_at',
        'updated_at',
        'deleted_at',
        'qr_code',
        'id_vp',
        'id_ccv'
    ];
    protected $primaryKey = 'ts_id';
   protected $keyType = 'string';
protected $casts = [
    'ts_id' => 'string',
];

    public function info($id_vanphong)
    {
        $ts_kieu = TaiSanModel::where('ts_id', $this->ts_id)->select('ts_kieu')->first()->ts_kieu;
        $kieu = KieuModel::where('k_id', $ts_kieu)->get()->first();
        $ds_kieu = $kieu->k_tieumuc;
        $tieumuc_arr = explode(' ', $ds_kieu);
        $data = TaiSanModel::leftjoin('taisan_giatri', 'taisan_giatri.ts_id', '=', 'taisan.ts_id')
            ->leftjoin('tieumuc', 'tieumuc.tm_id', '=', 'taisan_giatri.tm_id')
            ->where('taisan_giatri.ts_id', $this->ts_id)
            ->whereIn('taisan_giatri.tm_id', $tieumuc_arr)
            ->select([
                'tieumuc.tm_id',
                'tieumuc.tm_nhan',
                'tieumuc.tm_loai',
                'tieumuc.tm_keywords',
                'tieumuc.tm_batbuoc',
                'taisan_giatri.ts_giatri',
                'taisan_giatri.ts_id',
            ])->get();


        $templateStr = Template::whereType('tai-san')->where('id_vanphong','=',2050)->whereSlug($kieu->k_keywords)->first()->template_transformed;
        foreach ($data as $val) {
            if ($val->tm_loai == 'select') {
                if (KieuTieuMucModel::where('ktm_id', $val->ts_giatri)->first()) {
                    $val->ts_giatri = KieuTieuMucModel::where('ktm_id', $val->ts_giatri)->first()->ktm_traloi;

                }
            }
            if ($val->tm_loai == 'file') {
                $arr = [];
//                dd();
//                if ($val->ts_giatri) {
//                    foreach (json_decode($val->ts_giatri) as $item) {
////                        $arr[] = AppController::convert_nextcloud($item, '/tai-san/giay-to/');
//                    }
//                }

                $val->ts_giatri = collect($arr);
            }
            $templateStr = str_replace('<' . $val->tm_keywords . '>', $val->ts_giatri, $templateStr);
        }
        $thongTinArr = $data->toArray();

        return ['thong_tin_arr' => $thongTinArr, 'thong_tin_str' => $templateStr];
    }
}
