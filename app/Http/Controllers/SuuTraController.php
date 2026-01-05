<?php

namespace App\Http\Controllers;

use DB;
use App\Models\User;
use App\Models\KieuModel;
use Carbon\Carbon;
use App\Models\SuuTraModel;
use App\Models\VanBanModel;
use App\Models\LogUchiModel;
use App\Models\TieuMucModel;
use App\Models\ChiNhanhModel;
use App\Models\NhanVienModel;
use App\Models\ThongBaoChung;
use DebugBar\DebugBar;
use App\SModels\olrCheckModel;
use App\Models\SuuTraLogModel;
use App\Models\SolrEditLogModel;
use App\Models\HistorySearchModel;
use App\Models\TaiSanModel;
use Illuminate\Http\Request;
use App\Imports\SuuTraImport;
use Ixudra\Curl\Facades\Curl;
use Elasticsearch\ClientBuilder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use App\Exports\SuuTraExampleExport;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Models\Kieuhopdong;
use App\Models\SolrCheckModel;
use App\Http\Controllers\SolariumController_vp;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class SuuTraController extends Controller
{
    use users;
    use imageHandling;
  

    public static function convert_vi_to_en($str)
    {
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", "a", $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", "e", $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", "i", $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", "o", $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", "u", $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", "y", $str);
        $str = preg_replace("/(đ)/", "d", $str);
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", "A", $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", "E", $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", "I", $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", "O", $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", "U", $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", "Y", $str);
        $str = preg_replace("/(Đ)/", "D", $str);
        return $str;
    }
    public static function insert_solr($data, $note)
    {
        $create = new SolrCheckModel;
        $create->st_id = $data->st_id;
        $create->note = $note;
        $create->save();
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://localhost:8983/solr/timkiemsuutra/update/json/docs?commit=true',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
    }
    public static function insert_solr_data($suutra1, $limit=500)
    {
       
        $create = new SolrCheckModel;
        $create->st_id = $suutra1->st_id;
        $create->save();
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://localhost:8983/solr/timkiemsuutra/update/json/docs?commit=true',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_POST => true, // <-- change this
            CURLOPT_POSTFIELDS => json_encode($suutra1),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
    //    dd($curl);
        $response = curl_exec($curl);
        // dd($response);
        curl_close($curl);
        Log::info('Solr response: ' . $response);
    }
    public static function delete_solr($st_id)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://localhost:8983/solr/timkiemsuutra/update?_=1659089839080&commitWithin=1000&overwrite=true&wt=json',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => '<delete>   
            <query>st_id:' . $st_id . '</query>   
         </delete>  ',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/xml'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
    }
    public static function check_status_server()
    {
        $ping = @fsockopen(env("E_URL"), 9200, $errno, $errstr, 10);
        if (!$ping) {
            return false; //sv down
        } else {
            @fclose($ping);
            return true; //sv up
        }
    }
    public function updateMortage()
    {
        $data = Curl::to('http://127.0.0.1:8000/api/get-mortage-note')->asJson()
            ->get();
        foreach ($data->data as $item) {
            $suutra = SuuTraModel::where('uchi_id', $item->tpid)->whereNull('deleted_at')->first();

            if ($suutra) {
                SuuTraModel::where('uchi_id', $item->tpid)->update([
                    'undisputed_date' => Carbon::createFromFormat('d/m/Y', $item->mortage_cancel_date)->format('Y-m-d'),
                    'undisputed_note' => $item->mortage_cancel_note,
                    'updated_at' => Carbon::now()
                ]);
                Curl::to('http://127.0.0.1:8000/api/update-checked')
                    ->withData(array('tpid' => $item->tpid))
                    ->post();
            }
        }
        return view('admin.suutra.reload');
    }
    public function updateReverse()
    {
        $data = SuuTraModel::query()->whereNull('confirm')->limit(500);

        foreach ($data->get() as $i) {

            $item = AppController::convert_unicode($i);
            $item = $item->toArray();
            $item['confirm'] = 1;
            $update = SuutraModel::where('st_id', $i->st_id)->update($item);
        }
        //bat dau
        return view('admin.suutra.reload');
    }

    public function duongSuIndex()
    {

        $array = [
            'suutranb.ccv',
            'suutranb.vp',
            'suutranb.texte',
            'suutranb.ngay_nhap',
            'suutranb.ngay_cc',
            'suutranb.so_hd',
            'suutranb.ten_hd',
            'suutranb.st_id',
            'suutranb.duong_su',
            'suutranb.picture',
            'users.first_name',
            'chinhanh.cn_ten',
            'suutranb.ngan_chan',
            'suutranb.chu_y',
            'suutranb.status',
            'suutranb.ccv_master',
            'suutranb.vp_master',
            'suutranb.cancel_status',
            'suutranb.cancel_description',
            'suutranb.ma_phan_biet',
            'suutranb.ma_dong_bo',
            'suutranb.uchi_id_ngan_chan',
            'suutranb.updated_at',
            'suutranb.created_at',
            'suutranb.note',
            'suutranb.real_name'


        ];
        $status = false;

        $data = SuuTraModel::query()->where('complete', '=', 4)->limit(200);

        foreach ($data->get() as $i) {
            $duong_su_index = "";
            $duong_su = preg_replace('/[^\p{L}\p{N}\s]/u', '', $i->duong_su);
            $duong_su = str_replace("\n", ' ', $duong_su);
            $duong_su = explode(' ', SuuTraController::convert_vi_to_en($duong_su));
            foreach ($duong_su as $item) {
                switch (strlen($item)) {
                    case 3:
                        $duong_su_index .= $item . " ";
                        break;
                    case 9:
                        $sub1 = substr($item, 0, 3);
                        $sub2 = substr($item, 3, 3);
                        $sub3 = substr($item, 6, 3);
                        $duong_su_index .= $sub1 . " " . $sub2 . " " . $sub3 . " ";
                        break;
                    case 12:
                        $sub1 = substr($item, 0, 3);
                        $sub2 = substr($item, 3, 3);
                        $sub3 = substr($item, 6, 3);
                        $sub4 = substr($item, 9, 3);

                        $duong_su_index .= $sub1 . " " . $sub2 . " " . $sub3 . " " . $sub4 . " ";
                        break;
                    default:
                        break;
                }
            }
            SuuTraModel::where('st_id', '=', $i->st_id)->update([
                'duong_su_index' => utf8_encode($duong_su_index),
                'complete' => 5
            ]);
        }
        //bat dau
        return view('admin.suutra.reload');
    }

    public function updateEnColumn()
    {


        $data = SuuTraModel::select('duong_su', 'st_id', 'uchi_id', 'complete', 'duong_su', 'duong_su_en', 'texte', 'texte_en')->where('texte_en', '')->limit(400);
        foreach ($data->get() as $i) {
            $texte_en = SuuTraController::convert_vi_to_en($i->texte);
            dd($texte_en);
            SuuTraModel::where('st_id', '=', $i->st_id)->update([
                'texte_en' => $texte_en,
                'complete' => 999
            ]);
        }

        //bat dau
        return view('admin.suutra.reload');
    }

    public function updateKieuVanBan()
    {
        $data = SuuTraModel::whereNull('loai')->limit(500)->get();
        //         #items: array:14 [▼
        //     1 => "Chuyển nhượng - mua bán"
        //     2 => "Tặng - cho"
        //     3 => "Thế chấp - cầm cố"
        //     4 => "Thuê - Mượn"
        //     5 => "Bảo lãnh"
        //     6 => "Ủy quyền"
        //     7 => "Góp vốn"
        //     8 => "Di chúc - thừa kế"
        //     9 => "Tài sản vợ chồng"
        //     10 => "Vay"
        //     11 => "Giao dịch khác"
        //     12 => "Hủy"
        //     13 => "Đặt cọc"
        //     14 => "Chứng thực chữ ký"
        //   ]
        foreach ($data as $item) {
            $st_id = $item->st_id;
            $item = $item->ten_hd;
            if ((stristr(mb_strtolower($item), "chuyển nhượng") || stristr(mb_strtolower($item), "hđcn")
                || stristr(mb_strtolower($item), "mua")
                || stristr(mb_strtolower($item), "bán"))) {
                SuuTraModel::find($st_id)->update(['loai' => 1]);
            } elseif (stristr(mb_strtolower($item), "tặng cho") || stristr(mb_strtolower($item), "tặng")) {
                SuuTraModel::find($st_id)->update(['loai' => 2]);
            } elseif (
                stristr(mb_strtolower($item), "thế chấp") || stristr(mb_strtolower($item), "cầm cố")
                || stristr(mb_strtolower($item), "hđtc")
            ) {
                SuuTraModel::find($st_id)->update(['loai' => 3]);
            } elseif (stristr(mb_strtolower($item), "ủy quyền")) {
                SuuTraModel::find($st_id)->update(['loai' => 6]);
            } elseif (stristr(mb_strtolower($item), "thuê") || stristr(mb_strtolower($item), "mượn")) {
                SuuTraModel::find($st_id)->update(['loai' => 4]);
            } elseif (stristr(mb_strtolower($item), "thừa kế")) {
                SuuTraModel::find($st_id)->update(['loai' => 8]);
            } elseif (stristr(mb_strtolower($item), "vợ chồng")) {
                SuuTraModel::find($st_id)->update(['loai' => 9]);
            } elseif (stristr(mb_strtolower($item), "di chúc")) {
                SuuTraModel::find($st_id)->update(['loai' => 8]);
            } elseif (stristr(mb_strtolower($item), "chữ ký")) {
                SuuTraModel::find($st_id)->update(['loai' => 14]);
            } elseif (stristr(mb_strtolower($item), "vay")) {
                SuuTraModel::find($st_id)->update(['loai' => 10]);
            } elseif (stristr(mb_strtolower($item), "góp vốn")) {
                SuuTraModel::find($st_id)->update(['loai' => 7]);
            } elseif (
                stristr(mb_strtolower($item), "thanh lý") || stristr(mb_strtolower($item), "huỷ")
                || stristr(
                    mb_strtolower($item),
                    "HỦY"
                )
                || stristr(mb_strtolower($item), "hủy")
                || stristr(mb_strtolower($item), "chấm dứt")
            ) {
                SuuTraModel::find($st_id)->update(['loai' => 12]);
            } elseif (
                stristr(mb_strtolower($item), "đặt cọc")
            ) {
                SuuTraModel::find($st_id)->update(['loai' => 13]);
            } elseif (stristr(mb_strtolower($item), "bảo lãnh")) {
                SuuTraModel::find($st_id)->update(['loai' => 5]);
            } else {
                SuuTraModel::find($st_id)->update(['loai' => 11]);
            }
        }

        return view('admin.suutra.reload');
    }

    public function updateSyncCode()
    {

        $array = [
            'suutranb.ccv',
            'suutranb.vp',
            'suutranb.texte',
            'suutranb.ngay_nhap',
            'suutranb.ngay_cc',
            'suutranb.so_hd',
            'suutranb.ten_hd',
            'suutranb.st_id',
            'suutranb.duong_su',
            'suutranb.picture',
            'users.first_name',
            'chinhanh.cn_ten',
            'suutranb.ngan_chan',
            'suutranb.chu_y',
            'suutranb.status',
            'suutranb.ccv_master',
            'suutranb.vp_master',
            'suutranb.cancel_status',
            'suutranb.cancel_description',
            'suutranb.ma_phan_biet',
            'suutranb.ma_dong_bo',
            'suutranb.uchi_id_ngan_chan',
            'suutranb.updated_at',
            'suutranb.created_at',
            'suutranb.note',
            'suutranb.real_name'


        ];
        $status = false;

        $data = SuuTraModel::query()->whereNull('sync_code')->limit(200);
        foreach ($data->get() as $i) {
            $syncKey = "000";
            if (!is_null($i->ma_dong_bo)) {
                $syncKey = explode("_", $i->ma_dong_bo)[0];
            }
            //dd($syncKey);
            SuuTraModel::where('st_id', '=', $i->st_id)->update([
                'sync_code' => $syncKey,
                'complete' => 10
            ]);
        }
        //bat dau
        return view('admin.suutra.reload');
    }

    function multi_strpos($haystack, $needles, $offset = 0)
    {

        foreach ($needles as $n) {
            if (strpos($haystack, $n, $offset) !== false)
                return strpos($haystack, $n, $offset);
        }
        return false;
    }
    public function vn_to_str($str)
    {

        $unicode = array(

            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',

            'd' => 'đ',

            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',

            'i' => 'í|ì|ỉ|ĩ|ị',

            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',

            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',

            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',

            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',

            'D' => 'Đ',

            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',

            'I' => 'Í|Ì|Ỉ|Ĩ|Ị',

            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',

            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',

            'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',

        );

        foreach ($unicode as $nonUnicode => $uni) {

            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }

        return $str;
    }
    public function index(Request $request)
    {
        DB::disableQueryLog();
        try {
            $index = SuuTraModel::select('texte', 'duong_su')->first();

            //        $index=SuuTraModel::reindex();

            ////dd(SuuTraModel::first()->basicInfo());
            //dd($index);
            $str_json = [];
            $str_json2 = [];
            $str_json_symbol = [];
            $str_json2_symbol = [];
            $getcoban = preg_replace('/\s+/', ' ', $request->get('coban'));
            $getcoban = str_replace(",", "", $getcoban);
            $getNangCao = preg_replace('/\s+/', ' ', $request->get('nangcao'));
            //$getNangCao=str_replace(",","",$getNangCao);

            $getNangCao = str_replace(array('\'', ';', '<', '>'), '', $getNangCao);
            $getcoban = str_replace(array(
                '\'',
                ',', ';', '<', '>'
            ), '', $getcoban);
            $getNangCao = $this->vn_to_str($getNangCao);
            $getcoban = $this->vn_to_str($getcoban);
            $id_vp = User::join('nhanvien', 'nhanvien.nv_id', '=', 'id')
                ->join('chinhanh', 'chinhanh.cn_id', '=', 'nhanvien.nv_vanphong')
                ->where('users.id', Sentinel::getUser()->id)->first()->cn_id;
            $ipaddress = '';
            if (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                    $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
                } else {
                    if (isset($_SERVER['HTTP_X_FORWARDED'])) {
                        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
                    } else {
                        if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
                            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
                        } else {
                            if (isset($_SERVER['HTTP_FORWARDED'])) {
                                $ipaddress = $_SERVER['HTTP_FORWARDED'];
                            } else {
                                if (isset($_SERVER['REMOTE_ADDR'])) {
                                    $ipaddress = $_SERVER['REMOTE_ADDR'];
                                } else {
                                    $ipaddress = 'UNKNOWN';
                                }
                            }
                        }
                    }
                }
            }
            if ($getcoban || $getNangCao) {
            }
            $space = strlen(self::convert_vi_to_en($getcoban));

            $lengh = 5;
            if ($space > 23) {
                $lengh = 1000;
            }
            $count = 0;
            $array = [
                'suutranb.ccv',
                'suutranb.vp',
                'suutranb.texte',
                'suutranb.ngay_nhap',
                'suutranb.ngay_cc',
                'suutranb.so_hd',
                'suutranb.ten_hd',
                'suutranb.st_id',
                'suutranb.duong_su',
                'suutranb.picture',
                'users.first_name',
                'chinhanh.cn_ten',
                'suutranb.ngan_chan',
                'suutranb.chu_y',
                'suutranb.status',
                'suutranb.ccv_master',
                'suutranb.vp_master',
                'suutranb.cancel_status',
                'suutranb.cancel_description',
                'suutranb.ma_phan_biet',
                'suutranb.ma_dong_bo',
                'suutranb.uchi_id_ngan_chan',
                'suutranb.updated_at',
                'suutranb.created_at',
                'suutranb.note',
                'suutranb.real_name',
                'suutranb.is_update',
                'suutranb.contract_period',
                'suutranb.property_info',
                'suutranb.transaction_content',
                'release_doc_number',
                'release_doc_date',
                'release_file_name',
                'release_file_path',
                'suutranb.uchi_id',
                'suutranb.release_doc_receive_date',
                'suutranb.prevent_doc_receive_date',
                'undisputed_date',
                'undisputed_note',
                'deleted_note'


            ];
            $status = false;
            $priority = $request->priority;
            $advanced = $request->advanced;
            $data = SuuTraModel::query()->leftjoin('users', 'users.id', '=', 'suutranb.ccv')
                ->leftjoin('chinhanh', 'cn_id', '=', 'suutranb.vp')
                ->select($array);

            if ($request->prevent) {
                $data = $data->whereIn('ngan_chan', [1, 3]);
            }

            $countPrevent = SuuTraModel::where('ngan_chan', 3)->count();
            $code = ChiNhanhModel::find(NhanVienModel::find(Sentinel::check()->id)->nv_vanphong);
            $countOffice = SuuTraModel::where('sync_code', $code->code_cn)->count();
            $isOffice = $request->isOffice;
            $so_hd = null;
            if ($request->isOffice == "true") {

                $data = $data->where('sync_code', $code->code_cn);
                $so_hd = $request->so_hd;
                if ($so_hd) {
                    $data = $data->where('so_hd', 'like', '%' . $so_hd . '%');
                }
                //$data=$data->orderBy('so_hd','desc');
            }

            $count = 0;
            $findMe = array('*', '"', '%');
            if ($getcoban && $getNangCao) {

                $keyNC = '"' . $getNangCao . '"';
                $keyCB = '"' . $getcoban . '"';

                if (true) {
                    /**
                     * if (str_contains($getNangCao, ' ')) {
                     *
                     * $status = preg_match_all('/\"([^\"]*?)\"/', $getNangCao, $matches);
                     *
                     * if ($status > 0) {
                     * $lengh = 8;
                     * $whereLike = [];
                     * $whereExact = "";
                     * $exactTerm = array_unique($matches[0]);
                     * $likeTerm = (trim(str_replace($exactTerm, '', $getNangCao)));
                     * $termIndex = "";
                     *
                     * //check dau
                     * if (strlen($getNangCao) != mb_strlen($getNangCao, 'utf-8')) {
                     * //co dau
                     *
                     * //$key=join("",$exactTerm);
                     * //$key=str_replace('"',"",$key);
                     * //$keyUpper=mb_strtoupper($key);
                     * //$keyLower=mb_strtolower($key);
                     * //$capitalKey=ucwords($key);
                     * //$whereLike[] = ['duong_su', 'LIKE', '%' . $keyUpper . '%'];
                     * //$whereLike[] = ['duong_su', 'LIKE', '%' . $keyLower . '%'];
                     * //$whereLike[] = ['duong_su', 'LIKE', '%' . $capitalKey . '%'];
                     * $key = join(" ", $exactTerm);
                     * if ($likeTerm) {
                     * if (strlen($likeTerm) == 4) {
                     * $lengh = 5;
                     * } elseif (strlen($likeTerm) == 9 || strlen($likeTerm) == 12) {
                     * $lengh = 500;
                     * }
                     * $lengh = 1000;
                     *
                     * } else {
                     * $whereExact = 'contains(suutranb.texte,' . "'" . $key . "'" . ')';
                     *
                     * }////
                     *
                     *
                     * } else {
                     * if (strlen($likeTerm) == 4) {
                     * $lengh = 5;
                     * } elseif (strlen($likeTerm) == 9 || strlen($likeTerm) == 12) {
                     * $lengh = 500;
                     * }
                     * $lengh = 1000;
                     *
                     * //khong dau
                     * $key = join(" AND ", $exactTerm);
                     * //$whereExact='contains(suutranb.duong_su_en,' ."'". $key."'".')';
                     * if ($likeTerm) {
                     * $likeTerm = str_replace(" ", ",", $likeTerm);
                     *
                     * $whereExact = 'contains(suutranb.texte_en,' . "'NEAR((" . $key . "," . $likeTerm . "), " . $lengh . ")'" . ')';
                     *
                     * } else {
                     * $whereExact = 'contains(suutranb.texte_en,' . "'" . $key . "'" . ')';
                     *
                     * }
                     *
                     * }
                     *
                     *
                     * if ($whereExact) {
                     * $data = $data->whereRaw($whereExact)->where($whereLike);
                     *
                     * } else {
                     * $data = $data->where($whereLike);
                     *
                     * }
                     * //$data= $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->paginate(20)->onEachSide(2);;
                     * //$count=$data->total();
                     * } else {
                     *
                     * $key = join(",", explode(" ", $getNangCao));
                     * if (strlen($getNangCao) != mb_strlen($getNangCao, 'utf-8')) {
                     * $where = 'contains(suutranb.texte,' . "'NEAR((" . $key . "), " . $lengh . ")'" . ')';
                     * $data = $data->whereRaw($where);
                     * } else {
                     * $where = 'contains(suutranb.texte_en,' . "'NEAR((" . $key . "), " . $lengh . ")'" . ')';
                     * $data = $data->whereRaw($where);
                     * }
                     * //$data= $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->paginate(20)->onEachSide(2);;
                     *
                     * }
                     *
                     *
                     * } else {
                     * //tim 1 word
                     * if (strlen($getNangCao) != mb_strlen($getNangCao, 'utf-8')) {
                     * //co dau
                     * $key = "'" . $getNangCao . "'";
                     * $where = 'contains(suutranb.texte,' . $key . ')';
                     * $data = $data->whereraw($where);
                     *
                     * $data = $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->paginate(20)->onEachSide(2);;
                     * } else {
                     * $key = "'" . $getNangCao . "'";
                     * $where = 'contains(suutranb.texte_en,' . $key . ')';
                     * $data = $data->whereraw($where);
                     * //$data=$data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->paginate(20)->onEachSide(2);;
                     * }
                     *
                     * }
                     **/


                    if (str_contains($getNangCao, ' ')) {

                        $status = preg_match_all('/\"([^\"]*?)\"/', $getNangCao, $matches);
                        if ($status > 0) {
                            //trong nhay
                            $whereLike = [];
                            $whereExact = "";
                            $exactTerm = array_unique($matches[0]);
                            foreach ($exactTerm as $word) {
                                $str_json[] = str_replace('"', "", $word);
                            }

                            $likeTerm = (trim(str_replace($exactTerm, '', $getNangCao)));

                            $termIndex = "";
                            //check dau
                            if (strlen($getNangCao) != mb_strlen($getNangCao, 'utf-8')) {


                                //co dau

                                //$key=join("",$exactTerm);
                                //$key=str_replace('"',"",$key);
                                //$keyUpper=mb_strtoupper($key);
                                //$keyLower=mb_strtolower($key);
                                //$capitalKey=ucwords($key);
                                //$whereLike[] = ['texte', 'LIKE', '%' . $keyUpper . '%'];
                                //$whereLike[] = ['texte', 'LIKE', '%' . $keyLower . '%'];
                                //$whereLike[] = ['texte', 'LIKE', '%' . $capitalKey . '%'];
                                $key = join(",", $exactTerm);
                                if ($likeTerm) {
                                    if (strlen($likeTerm) == 4) {
                                        $lengh = 5;
                                    } elseif (strlen($likeTerm) == 9 || strlen($likeTerm) == 12) {
                                        $lengh = 500;
                                    }

                                    if (str_contains($likeTerm, "*")) {
                                        ///
                                        $normal = "";
                                        $reverse = "";
                                        foreach (explode(" ", $likeTerm) as $word) {

                                            $str_json[] = str_replace(["*", "%"], "", $word);
                                            if (str_contains($word, "*")) {
                                                $word = strrev($word);
                                                $word = '"' . $word . '"';
                                                $reverse = $word . " " . $reverse;
                                            } else {
                                                $normal .= $word . " ";
                                            }
                                        }
                                        $normal = trim($normal);
                                        if ($normal) {
                                            if (count(explode(' ', $normal)) > 1) {
                                                $keyNormal = join(",", explode(" ", $normal));
                                                $where = 'contains(suutranb.texte,' . "'NEAR((" . $keyNormal . "),MAX)'" . ')';
                                                $data = $data->whereRaw($where);
                                            } else {
                                                if ($normal) {
                                                    $normal = '"' . str_replace('%', '*', $normal) . '"';
                                                    $where = 'contains(suutranb.texte_en,' . "'" . $normal . "'" . ')';
                                                    $data = $data->whereRaw($where);
                                                }
                                            }
                                        }
                                        $reverse = join(' OR ', explode(' ', trim($reverse)));
                                        $where = 'contains(suutranb.texte_reverse,' . "'" . $reverse . "'" . ')';
                                        $data = $data->whereRaw($where);
                                    } else {
                                        foreach (explode(" ", $likeTerm) as $word) {

                                            $str_json[] = str_replace(["*", "%"], "", $word);
                                        }
                                        //khong co *
                                        $string = join(",", explode(" ", trim($likeTerm)));

                                        //$key = join(",", $exactTerm);
                                        $whereExact = 'contains(suutranb.texte,' . "'NEAR((" . $key . "," . $string . ") ,MAX)'" . ')';
                                    }
                                } else {

                                    // $whereExact = 'contains(suutranb.texte,' . "'" . $key . "'" . ')';
                                    $string = str_replace(["*", "%"], "", $getNangCao);
                                    //$string = str_replace($str_json, "", $string);
                                    $string = str_replace(['"'], "", $string);
                                    foreach (explode(" ", $string) as $word) {
                                        $str_json[] = $word;
                                    }

                                    $string = join(",", explode(" ", trim($string)));
                                    $whereExact = 'contains(suutranb.texte,' . "'" . $key . "'" . ')';
                                }
                            } else {
                                //dd($lengh);
                                //khong dau
                                if (strlen($likeTerm) == 4) {
                                    $lengh = 5;
                                } elseif (strlen($likeTerm) == 9 || strlen($likeTerm) == 12) {
                                    $lengh = 500;
                                }
                                $lengh = 1000;

                                $key = join(",", $exactTerm);
                                //$whereExact='contains(suutranb.texte_en,' ."'". $key."'".')';
                                if ($likeTerm) {
                                    if (str_contains($likeTerm, "*")) {
                                        ///
                                        $normal = "";
                                        $reverse = "";
                                        foreach (explode(" ", $likeTerm) as $word) {

                                            $str_json[] = str_replace(["*", "%"], "", $word);
                                            if (str_contains($word, "*")) {
                                                $word = strrev($word);
                                                $word = '"' . $word . '"';
                                                $reverse = $word . " " . $reverse;
                                            } else {
                                                $normal .= $word . " ";
                                            }
                                        }

                                        $normal = trim($normal);

                                        if ($normal) {
                                            if (count(explode(' ', $normal)) > 1) {
                                                $keyNormal = join(",", explode(" ", $normal));
                                                $where = 'contains(suutranb.texte_en,' . "'NEAR((" . $keyNormal . "),MAX)'" . ')';
                                                $data = $data->whereRaw($where);
                                            } else {
                                                if ($normal) {
                                                    $normal = '"' . str_replace('%', '*', $normal) . '"';
                                                    $where = 'contains(suutranb.texte_en,' . "'" . $normal . "'" . ')';
                                                    $data = $data->whereRaw($where);
                                                }
                                            }
                                        }
                                        $reverse = join(' OR ', explode(' ', trim($reverse)));
                                        $where = 'contains(suutranb.texte_reverse,' . "'" . $reverse . "'" . ')';
                                        $data = $data->whereRaw($where);
                                    } else {
                                        $string = str_replace(["*", "%"], "", $getNangCao);
                                        $string = str_replace($str_json, "", $string);
                                        $string = str_replace(['"'], "", $string);
                                        foreach (explode(" ", $string) as $word) {
                                            $str_json[] = $word;
                                        }

                                        $string = join(",", explode(" ", trim($string)));
                                        $whereExact = 'contains(suutranb.texte_en,' . "'NEAR((" . $key . "," . $string . ") ,MAX)'" . ')';
                                    }
                                } else {
                                    $key = join(" AND ", $exactTerm);
                                    $whereExact = 'contains(suutranb.texte_en,' . "'" . $key . "'" . ')';
                                }
                            }


                            if ($whereExact) {
                                $data = $data->whereRaw($whereExact)->where($whereLike);
                            } else {
                                $data = $data->where($whereLike);
                            }
                            //here
                            //$data = $data->select($array)->orderByDesc("suutranb.st_id")->paginate(20)->onEachSide(2);;
                            //$count=$data->total();
                        } else {
                            //khong trong nhay
                            if (str_contains($getNangCao, "*")) {

                                //co dau * khong trong nhay
                                $normal = "";
                                $reverse = "";
                                foreach (explode(" ", $getNangCao) as $word) {
                                    $str_json[] = str_replace(["*", "%"], "", $word);
                                    if (str_contains($word, "*")) {
                                        $word = strrev($word);
                                        $word = '"' . $word . '"';
                                        $reverse = $word . " " . $reverse;
                                    } else {
                                        $normal .= $word . " ";
                                    }
                                }

                                $normal = trim($normal);

                                if (strlen($getNangCao) != mb_strlen($getNangCao, 'utf-8')) {
                                    if (count(explode(' ', $normal)) > 1) {
                                        $key = join(",", explode(" ", $normal));
                                        $where = 'contains(suutranb.texte,' . "'NEAR((" . $key . "),MAX)'" . ')';
                                        $data = $data->whereRaw($where);
                                    } else {
                                        if ($normal) {
                                            $where = 'contains(suutranb.texte,' . "'" . $normal . "'" . ')';

                                            $data = $data->whereRaw($where);
                                        }
                                    }
                                } else {
                                    if (count(explode(' ', $normal)) > 1) {
                                        $key = join(",", explode(" ", $normal));
                                        $where = 'contains(suutranb.texte_en,' . "'NEAR((" . $key . "),MAX)'" . ')';
                                        $data = $data->whereRaw($where);
                                    } else {
                                        if ($normal) {
                                            $normal = '"' . str_replace('%', '*', $normal) . '"';
                                            $where = 'contains(suutranb.texte_en,' . "'" . $normal . "'" . ')';
                                            $data = $data->whereRaw($where);
                                        }
                                    }
                                }

                                $reverse = join(' OR ', explode(' ', trim($reverse)));
                                $where = 'contains(suutranb.texte_reverse,' . "'" . $reverse . "'" . ')';
                                $data = $data->whereRaw($where);
                                // $data = $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->paginate(20)->onEachSide(2);

                            } else {
                                $keyArray = [];
                                foreach (explode(" ", str_replace("%", "*", $getNangCao)) as $item) {
                                    $str_json[] = str_replace(["*", "%"], "", $item);
                                    $keyArray[] = '"' . $item . '"';
                                }
                                $key = join(",", $keyArray);
                                $lengh = 200;
                                if (strlen($getNangCao) != mb_strlen($getNangCao, 'utf-8')) {
                                    $where = 'contains(suutranb.texte,' . "'NEAR((" . $key . "),MAX)'" . ')';
                                    $data = $data->whereRaw($where);
                                } else {
                                    $where = 'contains(suutranb.texte_en,' . "'NEAR((" . $key . "),MAX)'" . ')';

                                    $data = $data->whereRaw($where);
                                }
                                // $data = $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->paginate(20)->onEachSide(2);;

                            }
                        }
                    } else {

                        //tim 1 word
                        $str_json[] = str_replace(["*", "%"], "", $getNangCao);
                        if (str_contains($getNangCao, "*")) {
                            $str_json[] = str_replace("*", "", $getNangCao);
                            $key = "'" . '"' . strrev($getNangCao) . '"' . "'";

                            $where = 'contains(suutranb.texte_reverse,' . $key . ')';
                            $data = $data->whereraw($where);
                            //$data = $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->paginate(20)->onEachSide(2);;

                        } else {
                            $key = str_replace("%", "*", $getNangCao);
                            if (strlen($getNangCao) != mb_strlen($getNangCao, 'utf-8')) {
                                //co dau
                                $where = 'contains(suutranb.texte,' . "'" . $key . "'" . ')';
                                $data = $data->whereraw($where);

                                $data = $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id");;
                            } else {
                                $key = '"' . $key . '"';
                                $where = 'contains(suutranb.texte_en,' . "'" . $key . "'" . ')';
                                $data = $data->whereraw($where);
                                //$data = $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->paginate(20)->onEachSide(2);;
                            }
                        }
                    }
                    if (str_contains($getcoban, ' ')) {
                        $status = preg_match_all('/\"([^\"]*?)\"/', $getcoban, $matches);

                        if ($status > 0) {
                            $lengh = 8;
                            $whereLike = [];
                            $whereExact = "";
                            $exactTerm = array_unique($matches[0]);
                            foreach ($exactTerm as $word) {
                                if (is_int(self::multi_strpos($word, $findMe))) {
                                    $str_json2_symbol[] = str_replace(['"', '*', '%'], "", $word);
                                } else {
                                    $str_json2[] = str_replace(['"', '*', '%'], "", $word);
                                }
                            }
                            $likeTerm = (trim(str_replace($exactTerm, '', $getcoban)));
                            foreach (explode(" ", $likeTerm) as $word) {
                                if (is_int(self::multi_strpos($word, $findMe))) {
                                    $str_json2_symbol[] = str_replace(['"', '*', '%'], "", $word);
                                } else {
                                    $str_json2[] = str_replace(['"', '*', '%'], "", $word);
                                }
                            }
                            $termIndex = "";
                            if ($likeTerm) {
                                foreach (explode(" ", $likeTerm) as $term) {
                                    if (str_contains($term, "*")) {
                                        $likeTerm = (trim(str_replace($term, '', $likeTerm)));
                                        $term = str_replace('*', "", $term);
                                        $termIndex .= '"' . $term . '"';
                                        //$whereLike[] = ['duong_su', 'LIKE', '%' . $term . '%'];

                                    }
                                }
                                if ($termIndex) {
                                    $data = $data->whereRaw('contains(suutranb.duong_su_index,' . "'" . $termIndex . "'" . ')');
                                }
                            }
                            //check dau
                            if (strlen($getcoban) != mb_strlen($getcoban, 'utf-8')) {
                                //co dau

                                //$key=join("",$exactTerm);
                                //$key=str_replace('"',"",$key);
                                //$keyUpper=mb_strtoupper($key);
                                //$keyLower=mb_strtolower($key);
                                //$capitalKey=ucwords($key);
                                //$whereLike[] = ['duong_su', 'LIKE', '%' . $keyUpper . '%'];
                                //$whereLike[] = ['duong_su', 'LIKE', '%' . $keyLower . '%'];
                                //$whereLike[] = ['duong_su', 'LIKE', '%' . $capitalKey . '%'];
                                $key = join(",", $exactTerm);
                                if ($likeTerm) {
                                    if (strlen($likeTerm) == 4) {
                                        $lengh = 5;
                                    } elseif (strlen($likeTerm) == 9 || strlen($likeTerm) == 12) {
                                        $lengh = 500;
                                    }
                                    $likeTerm = str_replace(" ", ",", $likeTerm);
                                    $whereExact = 'contains(suutranb.duong_su,' . "'NEAR((" . $key . "," . $likeTerm . "), " . $lengh . ",TRUE)'" . ')';
                                } else {
                                    $key = join(" AND ", $exactTerm);
                                    $whereExact = 'contains(suutranb.duong_su,' . "'" . $key . "'" . ')';
                                }
                            } else {
                                if (strlen($likeTerm) == 4) {
                                    $lengh = 10;
                                } elseif (strlen($likeTerm) == 9 || strlen($likeTerm) == 12) {
                                    $lengh = 500;
                                }
                                //khong dau
                                $key = join(" , ", $exactTerm);
                                //$whereExact='contains(suutranb.duong_su_en,' ."'". $key."'".')';
                                if ($likeTerm) {
                                    $likeTerm = str_replace(" ", ",", $likeTerm);
                                    $whereExact = 'contains(suutranb.duong_su_en,' . "'NEAR((" . $key . "," . $likeTerm . "), " . $lengh . ",TRUE)'" . ')';
                                } else {
                                    $key = join(" AND ", $exactTerm);
                                    $whereExact = 'contains(suutranb.duong_su_en,' . "'" . $key . "'" . ')';
                                }
                            }


                            if ($whereExact) {
                                $data = $data->whereraw($whereExact)->where($whereLike);
                            } else {
                                $data = $data->where($whereLike);
                            }
                            //$data= $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->paginate(20)->onEachSide(2);;
                            //$count=$data->total();
                        } else {

                            $key = join(",", explode(" ", $getcoban));
                            $key = join(",", explode(" ", $getcoban));
                            $whereLike = [];
                            $whereExact = "";
                            $exactTerm = array_unique($matches[0]);
                            foreach ($exactTerm as $word) {
                                if (is_int(self::multi_strpos($word, $findMe))) {
                                    $str_json2_symbol[] = str_replace(['"', '*', '%'], "", $word);
                                } else {
                                    $str_json2[] = str_replace(['"', '*', '%'], "", $word);
                                }
                            }
                            $likeTerm = (trim(str_replace($exactTerm, '', $getcoban)));
                            foreach (explode(" ", $likeTerm) as $word) {
                                if (is_int(self::multi_strpos($word, $findMe))) {
                                    $str_json2_symbol[] = str_replace(['"', '*', '%'], "", $word);
                                } else {
                                    $str_json2[] = str_replace(['"', '*', '%'], "", $word);
                                }
                            }
                            $termIndex = "";
                            if ($likeTerm) {
                                foreach (explode(" ", $likeTerm) as $term) {
                                    if (str_contains($term, "*")) {
                                        $likeTerm = (trim(str_replace($term, '', $likeTerm)));
                                        $term = str_replace('*', "", $term);
                                        $termIndex .= '"' . $term . '"';
                                        //$whereLike[] = ['duong_su', 'LIKE', '%' . $term . '%'];

                                    }
                                }
                                if ($termIndex) {
                                    $data = $data->whereRaw('contains(suutranb.duong_su_index,' . "'" . $termIndex . "'" . ')');
                                }
                            }

                            if (strlen($getcoban) != mb_strlen($getcoban, 'utf-8')) {
                                //$whereExact='contains(suutranb.duong_su_en,' ."'". $key."'".')';
                                if ($likeTerm) {
                                    if (count(explode(' ', $likeTerm)) > 1) {

                                        $likeTerm = str_replace(" ", ",", $likeTerm);
                                        $whereExact = 'contains(suutranb.duong_su,' . "'NEAR((" . $likeTerm . "), " . $lengh . ",TRUE)'" . ')';
                                    } else {
                                        $whereExact = 'contains(suutranb.duong_su,' . "'" . $likeTerm . "'" . ')';
                                    }
                                }

                                $data = $data->whereRaw($whereExact);
                            } else {
                                $lengh = 5;
                                if ($likeTerm) {
                                    if (count(explode(' ', $likeTerm)) > 1) {

                                        $likeTerm = str_replace(" ", ",", $likeTerm);
                                        $whereExact = 'contains(suutranb.duong_su_en,' . "'NEAR((" . $likeTerm . "), " . $lengh . ",TRUE)'" . ')';
                                    } else {
                                        $whereExact = 'contains(suutranb.duong_su_en,' . "'" . $likeTerm . "'" . ')';
                                    }
                                }

                                $data = $data->whereRaw($whereExact);
                            }
                            //$data= $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->paginate(20)->onEachSide(2);;

                        }
                    } else {
                        foreach (explode(" ", $getcoban) as $word) {
                            if (is_int(self::multi_strpos($word, $findMe))) {
                                $str_json2_symbol[] = str_replace(['"', '*', '%'], "", $word);
                            } else {
                                $str_json2[] = str_replace(['"', '*', '%'], "", $word);
                            }
                        }
                        //tim 1 word
                        if (strlen($getcoban) != mb_strlen($getcoban, 'utf-8')) {
                            //co dau
                            $key = "'" . $getcoban . "'";
                            $where = 'contains(suutranb.duong_su,' . $key . ')';
                            $data = $data->whereraw($where);

                            //$data = $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->paginate(20)->onEachSide(2);;
                        } else {
                            $key = "'" . $getcoban . "'";
                            $where = 'contains(suutranb.duong_su_en,' . $key . ')';
                            $data = $data->whereraw($where);
                            //$data=$data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->paginate(20)->onEachSide(2);;
                        }
                    }

                    $count = $data->count();
                    $data = $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->paginate(20)->onEachSide(2);;
                } else {
                    if ($priority) {
                        if ($request->exactly) {
                            $data = SuuTraModel::searchByQuery(
                                [
                                    'bool' => [
                                        'should' => [
                                            0 => [
                                                'match_phrase' => [
                                                    'duong_su' => [
                                                        'query' => $getNangCao,
                                                        'analyzer' => 'my_analyzer',
                                                    ],
                                                ],
                                            ],
                                            1 => [
                                                'match' => [
                                                    'ngan_chan' => [
                                                        'query' => '3',
                                                        'boost' => 10
                                                    ],

                                                ],
                                            ],
                                        ],
                                    ],
                                ]
                            )->paginate(20)->onEachSide(2);;
                        } else {
                            $data = SuuTraModel::searchByQuery(
                                [
                                    'bool' => [
                                        'must' => [
                                            0 => [
                                                'match' => [
                                                    'duong_su' => [
                                                        'query' => $getcoban,
                                                        'analyzer' => 'my_analyzer',
                                                    ],
                                                ],
                                            ],
                                            1 => [
                                                'match' => [
                                                    'texte' => [
                                                        'query' => $getNangCao,
                                                        'analyzer' => 'my_analyzer',
                                                    ],
                                                ],
                                            ],

                                        ],
                                        'should' => [
                                            0 => [
                                                'match' => [
                                                    'ngan_chan' => [
                                                        'query' => '3',
                                                        'boost' => 10
                                                    ],

                                                ],
                                            ],
                                        ]

                                    ]
                                ],
                                '',
                                '',
                                '20',
                                ''

                            )->paginate(20)->onEachSide(2);;
                        }
                    } else {
                        if ($request->exactly) {
                            $data = SuuTraModel::searchByQuery(
                                [
                                    'bool' => [
                                        'must' => [
                                            0 => [
                                                'match_phrase' => [
                                                    'duong_su' => [
                                                        'query' => $getcoban,
                                                        'analyzer' => 'my_analyzer',
                                                    ],
                                                ],
                                            ],


                                        ],
                                        //                                'should'=>[
                                        //                                    0 => [
                                        //                                        'match' => [
                                        //                                            'ngan_chan' => '1',
                                        //                                        ],
                                        //                                    ],
                                        //                                ]

                                    ]
                                ],
                                '',
                                '',
                                '20',
                                ''

                            )->paginate(20)->onEachSide(2);;
                        } else {
                            $data = SuuTraModel::searchByQuery(
                                [
                                    'bool' => [
                                        'must' => [
                                            0 => [
                                                'match' => [
                                                    'duong_su' => [
                                                        'query' => $getcoban,
                                                        'analyzer' => 'my_analyzer',
                                                    ],
                                                ],
                                            ],
                                            1 => [
                                                'match' => [
                                                    'texte' => [
                                                        'query' => $getNangCao,
                                                        'analyzer' => 'my_analyzer',
                                                    ],
                                                ],
                                            ],

                                        ],
                                        //                                'should'=>[
                                        //                                    0 => [
                                        //                                        'match' => [
                                        //                                            'ngan_chan' => '1',
                                        //                                        ],
                                        //                                    ],
                                        //                                ]

                                    ]
                                ],
                                '',
                                '',
                                '20',
                                ''

                            )->paginate(20)->onEachSide(2);;
                        }
                    }
                }


                //$count = $data->count();
            } else {
                if ($getNangCao) {

                    $key = '"' . $getNangCao . '"';
                    if (true) {
                        if (str_contains($getNangCao, ' ')) {

                            $status = preg_match_all('/\"([^\"]*?)\"/', $getNangCao, $matches);
                            if ($status > 0) {
                                //trong nhay
                                $whereLike = [];
                                $whereExact = "";
                                $exactTerm = array_unique($matches[0]);
                                foreach ($exactTerm as $word) {
                                    $str_json[] = str_replace('"', "", $word);
                                }

                                $likeTerm = (trim(str_replace($exactTerm, '', $getNangCao)));

                                $termIndex = "";
                                //check dau
                                if (strlen($getNangCao) != mb_strlen($getNangCao, 'utf-8')) {


                                    //co dau

                                    //$key=join("",$exactTerm);
                                    //$key=str_replace('"',"",$key);
                                    //$keyUpper=mb_strtoupper($key);
                                    //$keyLower=mb_strtolower($key);
                                    //$capitalKey=ucwords($key);
                                    //$whereLike[] = ['texte', 'LIKE', '%' . $keyUpper . '%'];
                                    //$whereLike[] = ['texte', 'LIKE', '%' . $keyLower . '%'];
                                    //$whereLike[] = ['texte', 'LIKE', '%' . $capitalKey . '%'];
                                    $key = join(",", $exactTerm);
                                    if ($likeTerm) {
                                        if (strlen($likeTerm) == 4) {
                                            $lengh = 5;
                                        } elseif (strlen($likeTerm) == 9 || strlen($likeTerm) == 12) {
                                            $lengh = 500;
                                        }

                                        if (str_contains($likeTerm, "*")) {
                                            ///
                                            $normal = "";
                                            $reverse = "";
                                            foreach (explode(" ", $likeTerm) as $word) {

                                                $str_json[] = str_replace(["*", "%"], "", $word);
                                                if (str_contains($word, "*")) {
                                                    $word = strrev($word);
                                                    $word = '"' . $word . '"';
                                                    $reverse = $word . " " . $reverse;
                                                } else {
                                                    $normal .= $word . " ";
                                                }
                                            }
                                            $normal = trim($normal);
                                            if ($normal) {
                                                if (count(explode(' ', $normal)) > 1) {
                                                    $keyNormal = join(",", explode(" ", $normal));
                                                    $where = 'contains(suutranb.texte,' . "'NEAR((" . $keyNormal . "),MAX)'" . ')';
                                                    $data = $data->whereRaw($where);
                                                } else {
                                                    if ($normal) {
                                                        $normal = '"' . str_replace('%', '*', $normal) . '"';
                                                        $where = 'contains(suutranb.texte_en,' . "'" . $normal . "'" . ')';
                                                        $data = $data->whereRaw($where);
                                                    }
                                                }
                                            }
                                            $reverse = join(' OR ', explode(' ', trim($reverse)));
                                            $where = 'contains(suutranb.texte_reverse,' . "'" . $reverse . "'" . ')';
                                            $data = $data->whereRaw($where);
                                        } else {
                                            foreach (explode(" ", $likeTerm) as $word) {

                                                $str_json[] = str_replace(["*", "%"], "", $word);
                                            }
                                            //khong co *
                                            $string = join(",", explode(" ", trim($likeTerm)));
                                            $whereExact = 'contains(suutranb.texte,' . "'NEAR((" . $key . "," . $string . ") ,MAX)'" . ')';
                                        }
                                    } else {

                                        // $whereExact = 'contains(suutranb.texte,' . "'" . $key . "'" . ')';
                                        $string = str_replace(["*", "%"], "", $getNangCao);
                                        //$string = str_replace($str_json, "", $string);
                                        $string = str_replace(['"'], "", $string);
                                        foreach (explode(" ", $string) as $word) {
                                            $str_json[] = $word;
                                        }

                                        $string = join(",", explode(" ", trim($string)));
                                        $key = join(" AND ", $exactTerm);
                                        $whereExact = 'contains(suutranb.texte,' . "'" . $key . "'" . ')';
                                    }
                                } else {
                                    //dd($lengh);
                                    //khong dau
                                    if (strlen($likeTerm) == 4) {
                                        $lengh = 5;
                                    } elseif (strlen($likeTerm) == 9 || strlen($likeTerm) == 12) {
                                        $lengh = 500;
                                    }
                                    $lengh = 1000;

                                    $key = join(" ", $exactTerm);
                                    //$whereExact='contains(suutranb.texte_en,' ."'". $key."'".')';
                                    if ($likeTerm) {
                                        if (str_contains($likeTerm, "*")) {
                                            ///
                                            $normal = "";
                                            $reverse = "";
                                            foreach (explode(" ", $likeTerm) as $word) {

                                                $str_json[] = str_replace(["*", "%"], "", $word);
                                                if (str_contains($word, "*")) {
                                                    $word = strrev($word);
                                                    $word = '"' . $word . '"';
                                                    $reverse = $word . " " . $reverse;
                                                } else {
                                                    $normal .= $word . " ";
                                                }
                                            }

                                            $normal = trim($normal);

                                            if ($normal) {
                                                if (count(explode(' ', $normal)) > 1) {
                                                    $keyNormal = join(",", explode(" ", $normal));
                                                    $where = 'contains(suutranb.texte_en,' . "'NEAR((" . $keyNormal . "),MAX)'" . ')';
                                                    $data = $data->whereRaw($where);
                                                } else {
                                                    if ($normal) {
                                                        $normal = '"' . str_replace('%', '*', $normal) . '"';
                                                        $where = 'contains(suutranb.texte_en,' . "'" . $normal . "'" . ')';
                                                        $data = $data->whereRaw($where);
                                                    }
                                                }
                                            }
                                            $reverse = join(' OR ', explode(' ', trim($reverse)));
                                            $where = 'contains(suutranb.texte_reverse,' . "'" . $reverse . "'" . ')';
                                            $data = $data->whereRaw($where);
                                        } else {
                                            $string = str_replace(["*", "%"], "", $getNangCao);
                                            $string = str_replace($str_json, "", $string);
                                            $string = str_replace(['"'], "", $string);
                                            foreach (explode(" ", $string) as $word) {
                                                $str_json[] = $word;
                                            }

                                            $string = join(",", explode(" ", trim($string)));
                                            $whereExact = 'contains(suutranb.texte_en,' . "'NEAR((" . $key . "," . $string . ") ,MAX)'" . ')';
                                        }
                                    } else {
                                        $whereExact = 'contains(suutranb.texte_en,' . "'" . $key . "'" . ')';
                                    }
                                }


                                if ($whereExact) {
                                    $data = $data->whereRaw($whereExact)->where($whereLike);
                                } else {
                                    $data = $data->where($whereLike);
                                }
                                //here
                                $data = $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->paginate(20)->onEachSide(2);;
                                //$count=$data->total();
                            } else {
                                //khong trong nhay
                                if (str_contains($getNangCao, "*")) {

                                    //co dau * khong trong nhay
                                    $normal = "";
                                    $reverse = "";
                                    foreach (explode(" ", $getNangCao) as $word) {
                                        $str_json[] = str_replace(["*", "%"], "", $word);
                                        if (str_contains($word, "*")) {
                                            $word = strrev($word);
                                            $word = '"' . $word . '"';
                                            $reverse = $word . " " . $reverse;
                                        } else {
                                            $normal .= $word . " ";
                                        }
                                    }

                                    $normal = trim($normal);

                                    if (strlen($getNangCao) != mb_strlen($getNangCao, 'utf-8')) {
                                        if (count(explode(' ', $normal)) > 1) {
                                            $key = join(",", explode(" ", $normal));
                                            $where = 'contains(suutranb.texte,' . "'NEAR((" . $key . "),MAX)'" . ')';
                                            $data = $data->whereRaw($where);
                                        } else {
                                            if ($normal) {
                                                $where = 'contains(suutranb.texte,' . "'" . $normal . "'" . ')';

                                                $data = $data->whereRaw($where);
                                            }
                                        }
                                    } else {
                                        if (count(explode(' ', $normal)) > 1) {
                                            $key = join(",", explode(" ", $normal));
                                            $where = 'contains(suutranb.texte_en,' . "'NEAR((" . $key . "),MAX)'" . ')';
                                            $data = $data->whereRaw($where);
                                        } else {
                                            if ($normal) {
                                                $normal = '"' . str_replace('%', '*', $normal) . '"';
                                                $where = 'contains(suutranb.texte_en,' . "'" . $normal . "'" . ')';
                                                $data = $data->whereRaw($where);
                                            }
                                        }
                                    }

                                    $reverse = join(' OR ', explode(' ', trim($reverse)));
                                    $where = 'contains(suutranb.texte_reverse,' . "'" . $reverse . "'" . ')';
                                    $data = $data->whereRaw($where);
                                    $data = $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->paginate(20)->onEachSide(2);
                                } else {
                                    $keyArray = [];
                                    foreach (explode(" ", str_replace("%", "*", $getNangCao)) as $item) {
                                        $str_json[] = str_replace(["*", "%"], "", $item);
                                        $keyArray[] = '"' . $item . '"';
                                    }
                                    $key = join(",", $keyArray);
                                    $lengh = 200;
                                    if (strlen($getNangCao) != mb_strlen($getNangCao, 'utf-8')) {
                                        $where = 'contains(suutranb.texte,' . "'NEAR((" . $key . "),MAX)'" . ')';
                                        $data = $data->whereRaw($where);
                                    } else {
                                        $where = 'contains(suutranb.texte_en,' . "'NEAR((" . $key . "),MAX)'" . ')';

                                        $data = $data->whereRaw($where);
                                    }
                                    $data = $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->paginate(20)->onEachSide(2);;
                                }
                            }
                        } else {

                            //tim 1 word
                            $str_json[] = str_replace(["*", "%"], "", $getNangCao);
                            if (str_contains($getNangCao, "*")) {
                                $str_json[] = str_replace("*", "", $getNangCao);
                                $key = "'" . '"' . strrev($getNangCao) . '"' . "'";

                                $where = 'contains(suutranb.texte_reverse,' . $key . ')';
                                $data = $data->whereraw($where);
                                $data = $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->paginate(20)->onEachSide(2);;
                            } else {
                                $key = str_replace("%", "*", $getNangCao);
                                if (strlen($getNangCao) != mb_strlen($getNangCao, 'utf-8')) {
                                    //co dau
                                    $where = 'contains(suutranb.texte,' . "'" . $key . "'" . ')';
                                    $data = $data->whereraw($where);

                                    $data = $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->paginate(20)->onEachSide(2);;
                                } else {
                                    $key = '"' . $key . '"';
                                    $where = 'contains(suutranb.texte_en,' . "'" . $key . "'" . ')';
                                    $data = $data->whereraw($where);
                                    $data = $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->paginate(20)->onEachSide(2);;
                                }
                            }
                        }
                    } else {
                        if ($priority) {
                            if ($request->exactly) {
                                $data = SuuTraModel::searchByQuery(
                                    [
                                        'bool' => [
                                            'should' => [
                                                0 => [
                                                    'match_phrase' => [
                                                        'texte' => [
                                                            'query' => $getNangCao,
                                                            'analyzer' => 'my_analyzer',
                                                        ],
                                                    ],
                                                ],
                                                1 => [
                                                    'match' => [
                                                        'ngan_chan' => [
                                                            'query' => '3',
                                                            'boost' => 10
                                                        ],

                                                    ],
                                                ],
                                            ],
                                        ],
                                    ]
                                )->paginate(20)->onEachSide(2);;
                            } else {
                                $data = SuuTraModel::searchByQuery(
                                    [
                                        'bool' => [
                                            'should' => [
                                                0 => [
                                                    'match' => [
                                                        'texte' => [
                                                            'query' => $getNangCao,
                                                            'analyzer' => 'my_analyzer',
                                                        ],
                                                    ],
                                                ],
                                                1 => [
                                                    'match' => [
                                                        'ngan_chan' => [
                                                            'query' => '3',
                                                            'boost' => 10
                                                        ],

                                                    ],
                                                ],
                                            ],
                                        ],
                                    ]
                                )->paginate(20)->onEachSide(2);;
                            }
                        } else {
                            if ($request->exactly) {
                                $data = SuuTraModel::searchByQuery(
                                    [
                                        'bool' => [
                                            'should' => [
                                                0 => [
                                                    'match_phrase' => [
                                                        'texte' => [
                                                            'query' => $getNangCao,
                                                            'analyzer' => 'my_analyzer',
                                                        ],
                                                    ],
                                                ],
                                                //                                        1 => [
                                                //                                            'match' => [
                                                //                                                'ngan_chan' => '1',
                                                //                                            ],
                                                //                                        ],
                                            ],
                                        ],
                                    ],
                                    '',
                                    '',
                                    '',
                                    ''
                                )->paginate(20)->onEachSide(2);;
                            } else {
                                $data = SuuTraModel::searchByQuery(
                                    [
                                        'bool' => [
                                            'should' => [
                                                0 => [
                                                    'match' => [
                                                        'texte' => [
                                                            'query' => $getNangCao,
                                                            'analyzer' => 'my_analyzer',
                                                        ],
                                                    ],
                                                ],
                                                //                                        1 => [
                                                //                                            'match' => [
                                                //                                                'ngan_chan' => '1',
                                                //                                            ],
                                                //                                        ],
                                            ],
                                        ],
                                    ],
                                    '',
                                    '',
                                    '',
                                    ''
                                )->paginate(20)->onEachSide(2);;
                            }
                        }
                    }

                    //  $count = $data->count();

                } elseif ($getcoban) {

                    //                dd($status);
                    $where = "";
                    if (true) {
                        if (str_contains($getcoban, ' ')) {
                            $status = preg_match_all('/\"([^\"]*?)\"/', $getcoban, $matches);


                            if ($status > 0) {

                                $whereLike = [];
                                $whereExact = "";
                                $exactTerm = array_unique($matches[0]);
                                foreach ($exactTerm as $word) {
                                    $word = trim($word);
                                    if (is_int(self::multi_strpos($word, $findMe))) {
                                        $str_json2_symbol[] = str_replace(['"', '*', '%'], "", $word);
                                    } else {
                                        $str_json2[] = str_replace(['"', '*', '%'], "", $word);
                                    }
                                }
                                $likeTerm = (trim(str_replace($exactTerm, '', $getcoban)));
                                $termIndex = "";
                                if ($likeTerm) {

                                    foreach (explode(" ", $likeTerm) as $term) {
                                        if (str_contains($term, "*")) {
                                            $likeTerm = (trim(str_replace($term, '', $likeTerm)));
                                            $term = str_replace('*', "", $term);
                                            $str_json2[] = $term;
                                            $termIndex .= '"' . $term . '"';
                                            //$whereLike[] = ['duong_su', 'LIKE', '%' . $term . '%'];

                                        } else {
                                            $str_json2[] = $likeTerm;
                                        }
                                    }
                                    if ($termIndex) {
                                        $data = $data->whereRaw('contains(suutranb.duong_su_index,' . "'" . $termIndex . "'" . ')');
                                    }
                                }
                                //check dau
                                if (strlen($getcoban) != mb_strlen($getcoban, 'utf-8')) {
                                    //co dau

                                    //$key=join("",$exactTerm);
                                    //$key=str_replace('"',"",$key);
                                    //$keyUpper=mb_strtoupper($key);
                                    //$keyLower=mb_strtolower($key);
                                    //$capitalKey=ucwords($key);
                                    //$whereLike[] = ['duong_su', 'LIKE', '%' . $keyUpper . '%'];
                                    //$whereLike[] = ['duong_su', 'LIKE', '%' . $keyLower . '%'];
                                    //$whereLike[] = ['duong_su', 'LIKE', '%' . $capitalKey . '%'];
                                    $key = join(",", $exactTerm);
                                    if ($likeTerm) {
                                        if (strlen($likeTerm) == 4) {
                                            $lengh = 5;
                                        } elseif (strlen($likeTerm) == 9 || strlen($likeTerm) == 12) {
                                            $lengh = 500;
                                        }

                                        $likeTerm = join(",", explode(" ", $likeTerm));
                                        $whereExact = 'contains(suutranb.duong_su,' . "'NEAR((" . $key . "," . $likeTerm . "), " . $lengh . ",TRUE)'" . ')';
                                    } else {
                                        $key = join(" AND ", $exactTerm);
                                        $whereExact = 'contains(suutranb.duong_su,' . "'" . $key . "'" . ')';
                                    }
                                } else {
                                    //dd($lengh);
                                    //khong dau
                                    if (strlen($likeTerm) == 4) {
                                        $lengh = 5;
                                    } elseif (strlen($likeTerm) == 9 || strlen($likeTerm) == 12) {
                                        $lengh = 500;
                                    }

                                    $key = join(",", $exactTerm);
                                    //$whereExact='contains(suutranb.duong_su_en,' ."'". $key."'".')';
                                    if ($likeTerm) {
                                        $likeTerm = join(",", explode(" ", $likeTerm));
                                        $whereExact = 'contains(suutranb.duong_su_en,' . "'NEAR((" . $key . "," . $likeTerm . "), " . $lengh . ",TRUE)'" . ')';
                                    } else {
                                        $key = join(" AND ", $exactTerm);
                                        $whereExact = 'contains(suutranb.duong_su_en,' . "'" . $key . "'" . ')';
                                    }
                                }

                                if ($whereExact) {
                                    $data = $data->whereRaw($whereExact)->where($whereLike);
                                } else {
                                    $data = $data->where($whereLike);
                                }
                                $data = $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->paginate(20)->onEachSide(2);;
                                //$count=$data->total();
                            } else {

                                $key = join(",", explode(" ", $getcoban));
                                foreach (explode(",", $key) as $word) {
                                    if (is_int(self::multi_strpos($word, $findMe))) {
                                        $str_json2_symbol[] = str_replace(['"', '*', '%'], "", $word);
                                    } else {
                                        $str_json2[] = str_replace(['"', '*', '%'], "", $word);
                                    }
                                    //$str_json2[]=$word;
                                }
                                $whereLike = [];
                                $whereExact = "";
                                $exactTerm = array_unique($matches[0]);
                                $likeTerm = (trim(str_replace($exactTerm, '', $getcoban)));
                                $termIndex = "";
                                if ($likeTerm) {
                                    foreach (explode(" ", $likeTerm) as $term) {
                                        if (str_contains($term, "*")) {
                                            $likeTerm = (trim(str_replace($term, '', $likeTerm)));
                                            $term = str_replace('*', "", $term);
                                            $termIndex .= '"' . $term . '"';
                                            //$whereLike[] = ['duong_su', 'LIKE', '%' . $term . '%'];

                                        } else {
                                        }
                                    }
                                    if ($termIndex) {
                                        $data = $data->whereRaw('contains(suutranb.duong_su_index,' . "'" . $termIndex . "'" . ')');
                                    }
                                }
                                if (strlen($getcoban) != mb_strlen($getcoban, 'utf-8')) {
                                    if ($likeTerm) {
                                        if (strlen($likeTerm) == 4) {
                                            $lengh = 10;
                                        } elseif (strlen($likeTerm) == 9 || strlen($likeTerm) == 12) {
                                            $lengh = 500;
                                        }
                                        if (count(explode(' ', $likeTerm)) > 1) {

                                            $likeTerm = str_replace(" ", ",", $likeTerm);
                                            $whereExact = 'contains(suutranb.duong_su,' . "'NEAR((" . $likeTerm . "), " . $lengh . ",TRUE)'" . ')';
                                        } else {
                                            $whereExact = 'contains(suutranb.duong_su,' . "'" . $likeTerm . "'" . ')';
                                        }
                                    } else {
                                        $whereExact = 'contains(suutranb.duong_su,' . "'" . $key . "'" . ')';
                                    }
                                    $data = $data->whereRaw($whereExact);
                                } else {
                                    if ($likeTerm) {
                                        if (strlen($likeTerm) == 4) {
                                            $lengh = 5;
                                        } elseif (strlen($likeTerm) == 9 || strlen($likeTerm) == 12) {
                                            $lengh = 500;
                                        }
                                        if (count(explode(' ', $likeTerm)) > 1) {

                                            $likeTerm = str_replace(" ", ",", $likeTerm);
                                            $whereExact = 'contains(suutranb.duong_su_en,' . "'NEAR((" . $likeTerm . "), " . $lengh . ",TRUE)'" . ')';
                                        } else {
                                            $whereExact = 'contains(suutranb.duong_su_en,' . "'" . $likeTerm . "'" . ')';
                                        }
                                    } else {
                                        $whereExact = 'contains(suutranb.duong_su_en,' . "'" . $key . "'" . ')';
                                    }
                                    $data = $data->whereRaw($whereExact);
                                }
                                $data = $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->paginate(20)->onEachSide(2);;
                            }
                        } else {
                            //tim 1 word
                            $str_json2[] = $getcoban;
                            if (strlen($getcoban) != mb_strlen($getcoban, 'utf-8')) {
                                //co dau
                                $key = "'" . $getcoban . "'";
                                $where = 'contains(suutranb.duong_su,' . $key . ')';
                                $data = $data->whereraw($where);

                                $data = $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->paginate(20)->onEachSide(2);;
                            } else {
                                $key = "'" . $getcoban . "'";
                                $where = 'contains(suutranb.duong_su_en,' . $key . ')';
                                $data = $data->whereraw($where);
                                $data = $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->paginate(20)->onEachSide(2);;
                            }
                        }
                    } else {
                        if ($priority) {

                            if ($request->exactly) {
                                $data = SuuTraModel::searchByQuery(
                                    [
                                        'bool' => [
                                            'should' => [
                                                0 => [
                                                    'match_phrase' => [
                                                        'duong_su' => [
                                                            'query' => $getNangCao,
                                                            'analyzer' => 'my_analyzer',
                                                        ],
                                                    ],
                                                ],
                                                1 => [
                                                    'match' => [
                                                        'ngan_chan' => [
                                                            'query' => '3',
                                                            'boost' => 10
                                                        ],

                                                    ],
                                                ],
                                            ],
                                        ],
                                    ]
                                )->paginate(20)->onEachSide(2);;
                            } else {
                                $data = SuuTraModel::searchByQuery(
                                    [
                                        'bool' => [
                                            'should' => [
                                                0 => [
                                                    'match' => [
                                                        'duong_su' => [
                                                            'query' => $getcoban,
                                                            'analyzer' => 'my_analyzer',
                                                        ],
                                                    ],
                                                ],
                                                1 => [
                                                    'match' => [
                                                        'ngan_chan' => [
                                                            'query' => '3',
                                                            'boost' => 10
                                                        ],

                                                    ],
                                                ],
                                            ],
                                        ],
                                    ]
                                )->paginate(20)->onEachSide(2);;
                            }
                        } else {
                            if ($request->exactly) {
                                $data = SuuTraModel::searchByQuery(
                                    [
                                        'bool' => [
                                            'should' => [
                                                0 => [
                                                    'match_phrase' => [
                                                        'duong_su' => [
                                                            'query' => $getcoban,
                                                            'analyzer' => 'my_analyzer',
                                                        ],
                                                    ],
                                                ],
                                                //                                        1 => [
                                                //                                            'match' => [
                                                //                                                'ngan_chan' => '1',
                                                //                                            ],
                                                //                                        ],
                                            ],
                                        ],
                                    ],
                                    '',
                                    '',
                                    '',
                                    ''
                                )->paginate(20)->onEachSide(2);;
                            } else {
                                $data = SuuTraModel::searchByQuery(
                                    [
                                        'bool' => [
                                            'should' => [
                                                0 => [
                                                    'match' => [
                                                        'duong_su' => [
                                                            'query' => $getcoban,
                                                            'analyzer' => 'my_analyzer',
                                                        ],
                                                    ],
                                                ],
                                                //                                        1 => [
                                                //                                            'match' => [
                                                //                                                'ngan_chan' => '1',
                                                //                                            ],
                                                //                                        ],
                                            ],
                                        ],
                                    ],
                                    '',
                                    '',
                                    '',
                                    ''
                                )->paginate(20)->onEachSide(2);;
                            }
                        }
                    }
                } else {
                }
            }
            if ($getcoban || $getNangCao) {
                //        $data=collect($data->toArray());
                //dd($data);
            } else {
                $data = $data->orderByDesc("suutranb.st_id")->paginate(20)->onEachSide(2);;
            }
            $count = $data->total();
            $loadTaiSan = false;
            $option = [];
            $option['accuracy'] = "complementary";
            $option['diacritics'] = true;
            $option['separateWordSearch'] = true;
            $status = preg_match_all('/\"([^\"]*?)\"/', $getcoban, $matches);
            //$str_json2=json_encode(["Thanh Nhuan","109"]);
            //dd($str_json2);
            $str_json2 = json_encode($str_json2, JSON_UNESCAPED_UNICODE);
            //$str_json2 = json_encode(array_filter(array(str_replace('"', "", $getcoban))));
            $str_json = json_encode($str_json, JSON_UNESCAPED_UNICODE);
            $str_json_symbol = json_encode($str_json_symbol, JSON_UNESCAPED_UNICODE);
            $str_json2_symbol = json_encode($str_json2_symbol, JSON_UNESCAPED_UNICODE);
            //dd($str_json2,$option);
            //            $str_json = json_encode(array_filter(array(str_replace('"', "", $getNangCao))));
            $exactly = $request->exactly;
        } catch (Exception $e) {
            return redirect(route('searchSolr'))->with('error', 'Vui lòng nhập lại đúng cú pháp tìm kiếm hoặc liên hệ hỗ trợ để được hướng dẫn!');
        }

        $loadTaiSan = false;
        $isPrevent = $request->prevent ?? false;
        $getcoban = $request->coban;
        $getNangCao = $request->nangcao;
        //dd($str_json_symbol,$str_json);
        return view('admin.suutra.index', compact('isOffice', 'so_hd', 'countOffice', 'str_json2_symbol', 'str_json_symbol', 'countPrevent', 'isPrevent', 'str_json2', 'count', 'data', 'getcoban', 'getNangCao', 'str_json', 'loadTaiSan'));
    }

    public function indexOther(Request $request)
    {
        $loadTaiSan = true;
        //        $index=SuuTraModel::reindex();

        ////dd(SuuTraModel::first()->basicInfo());
        //dd($index);
        $str_json = json_encode([]);
        $getcoban = $request->get('coban');
        $getNangCao = $request->get('nangcao') ? $request->get('nangcao') : "";
        $str_json = json_encode([]);
        $getcoban = preg_replace('/\s+/', ' ', $getcoban);
        $getcoban = str_replace(",", " ", $getcoban);
        $getNangCao = preg_replace('/\s+/', ' ', $getNangCao);
        //$getNangCao=str_replace(",","",$getNangCao);
        $getNangCao = str_replace(array('\'', ';', '<', '>'), '', $getNangCao);
        $space = substr_count($getcoban, ' ');
        $count = 0;
        $array = [
            'suutranb.ccv',
            'suutranb.vp',
            'suutranb.texte',
            'suutranb.ngay_nhap',
            'suutranb.ngay_cc',
            'suutranb.so_hd',
            'suutranb.ten_hd',
            'suutranb.st_id',
            'suutranb.duong_su',
            'suutranb.picture',
            'users.first_name',
            'chinhanh.cn_ten',
            'suutranb.ngan_chan',
            'suutranb.chu_y',
            'suutranb.status',
            'suutranb.ccv_master',
            'suutranb.vp_master',
            'suutranb.cancel_status',
            'suutranb.cancel_description',
            'suutranb.ma_phan_biet',
            'suutranb.ma_dong_bo',
            'suutranb.uchi_id_ngan_chan',
            'suutranb.updated_at',
            'suutranb.created_at',
            'suutranb.note',
            'suutranb.real_name',
            'suutranb.contract_period',
            'suutranb.property_info',
            'suutranb.transaction_content',


        ];
        $status = false;
        $priority = $request->priority;
        $data = SuuTraModel::query()->leftjoin('users', 'users.id', '=', 'suutranb.ccv')
            ->leftjoin('chinhanh', 'cn_id', '=', 'suutranb.vp')
            ->select($array);
        if (true) {
            if ($getNangCao != null) {
                $lengh = 8;

                if (str_contains($getNangCao, ' ')) {
                    $status = preg_match_all('/\"([^\"]*?)\"/', $getNangCao, $matches);


                    if ($status > 0) {

                        $whereLike = [];
                        $whereExact = "";
                        $exactTerm = array_unique($matches[0]);
                        $likeTerm = (trim(str_replace($exactTerm, '', $getNangCao)));
                        $termIndex = "";
                        if ($likeTerm) {
                            foreach (explode(" ", $likeTerm) as $term) {
                                if (str_contains($term, "*")) {
                                    $likeTerm = (trim(str_replace($term, '', $likeTerm)));
                                    $term = str_replace('*', "", $term);
                                    $termIndex .= '"' . $term . '"';
                                    //$whereLike[] = ['texte', 'LIKE', '%' . $term . '%'];

                                }
                            }
                            if ($termIndex) {
                                $data = $data->whereRaw('contains(suutranb.texte_index,' . "'" . $termIndex . "'" . ')');
                            }
                        }
                        //check dau
                        if (strlen($getNangCao) != mb_strlen($getNangCao, 'utf-8')) {
                            //co dau

                            //$key=join("",$exactTerm);
                            //$key=str_replace('"',"",$key);
                            //$keyUpper=mb_strtoupper($key);
                            //$keyLower=mb_strtolower($key);
                            //$capitalKey=ucwords($key);
                            //$whereLike[] = ['texte', 'LIKE', '%' . $keyUpper . '%'];
                            //$whereLike[] = ['texte', 'LIKE', '%' . $keyLower . '%'];
                            //$whereLike[] = ['texte', 'LIKE', '%' . $capitalKey . '%'];
                            $key = join(" AND ", $exactTerm);
                            if ($likeTerm) {
                                if (strlen($likeTerm) == 4) {
                                    $lengh = 5;
                                } elseif (strlen($likeTerm) == 9 || strlen($likeTerm) == 12) {
                                    $lengh = 500;
                                }
                                $likeTerm = join(",", explode(" ", $likeTerm));
                                $whereExact = 'contains(suutranb.texte,' . "'NEAR((" . $key . "," . $likeTerm . "), " . $lengh . ",TRUE)'" . ')';
                            } else {
                                $whereExact = 'contains(suutranb.texte,' . "'" . $key . "'" . ')';
                            }
                        } else {
                            //dd($lengh);
                            //khong dau
                            if (strlen($likeTerm) == 4) {
                                $lengh = 5;
                            } elseif (strlen($likeTerm) == 9 || strlen($likeTerm) == 12) {
                                $lengh = 500;
                            }
                            $lengh = 1000;

                            $key = join(" AND ", $exactTerm);
                            //$whereExact='contains(suutranb.texte_en,' ."'". $key."'".')';
                            if ($likeTerm) {
                                $likeTerm = str_replace(" ", ",", $likeTerm);
                                $whereExact = 'contains(suutranb.texte_en,' . "'NEAR((" . $key . "," . $likeTerm . "), " . $lengh . ")'" . ')';
                            } else {
                                $whereExact = 'contains(suutranb.texte_en,' . "'" . $key . "'" . ')';
                            }
                            $data = $data->whereRaw($whereExact);
                        }


                        if ($whereExact) {
                            $data = $data->whereRaw($whereExact)->where($whereLike);
                        } else {
                            $data = $data->where($whereLike);
                        }
                        $data = $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->paginate(20)->onEachSide(2);;
                        //$count=$data->total();
                    } else {

                        $key = join(",", explode(" ", $getNangCao));
                        if (strlen($getNangCao) != mb_strlen($getNangCao, 'utf-8')) {
                            $where = 'contains(suutranb.texte,' . "'NEAR((" . $key . "), " . $lengh . ")'" . ')';
                            $data = $data->whereRaw($where);
                        } else {
                            $where = 'contains(suutranb.texte_en,' . "'NEAR((" . $key . "), " . $lengh . ")'" . ')';
                            $data = $data->whereRaw($where);
                        }
                        $data = $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->paginate(20)->onEachSide(2);;
                    }
                } else {
                    //tim 1 word
                    if (strlen($getNangCao) != mb_strlen($getNangCao, 'utf-8')) {
                        //co dau
                        $key = "'" . $getNangCao . "'";
                        $where = 'contains(suutranb.texte,' . $key . ')';
                        $data = $data->whereraw($where);

                        $data = $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->paginate(20)->onEachSide(2);;
                    } else {
                        $key = "'" . $getNangCao . "'";
                        $where = 'contains(suutranb.texte_en,' . $key . ')';
                        $data = $data->whereraw($where);
                        $data = $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->paginate(20)->onEachSide(2);;
                    }
                }
            } else {
                $data = $data->whereNull('st_id');
                $count = $data->count();
            }
        } else {
            if ($priority) {
                if ($request->exactly) {

                    $data = SuuTraModel::searchByQuery(
                        [
                            'bool' => [
                                'should' => [
                                    0 => [
                                        'match_phrase' => [
                                            'texte' => [
                                                'query' => $getNangCao,
                                                'analyzer' => 'my_analyzer',
                                            ],
                                        ],
                                    ],
                                    1 => [
                                        'match' => [
                                            'ngan_chan' => [
                                                'query' => '3',
                                                'boost' => 10
                                            ],

                                        ],
                                    ],
                                ],
                            ],
                        ]
                    )->paginate(20)->onEachSide(2);;
                } else {
                    $data = SuuTraModel::searchByQuery(
                        [
                            'bool' => [
                                'should' => [
                                    0 => [
                                        'match' => [
                                            'texte' => [
                                                'query' => $getNangCao,
                                                'analyzer' => 'my_analyzer',
                                            ],
                                        ],
                                    ],
                                    1 => [
                                        'match' => [
                                            'ngan_chan' => [
                                                'query' => '3',
                                                'boost' => 10
                                            ],

                                        ],
                                    ],
                                ],
                            ],
                        ]
                    )->paginate(20)->onEachSide(2);;
                }
            } else {
                if ($request->exactly) {
                    $data = SuuTraModel::searchByQuery(
                        [
                            'bool' => [
                                'should' => [
                                    0 => [
                                        'match_phrase' => [
                                            'texte' => [
                                                'query' => $getNangCao,
                                                'analyzer' => 'my_analyzer',
                                            ],
                                        ],
                                    ],
                                    //                                        1 => [
                                    //                                            'match' => [
                                    //                                                'ngan_chan' => '1',
                                    //                                            ],
                                    //                                        ],
                                ],
                            ],
                        ],
                        '',
                        '',
                        '',
                        ''
                    )->paginate(20)->onEachSide(2);;
                } else {
                    $data = SuuTraModel::searchByQuery(
                        [
                            'bool' => [
                                'should' => [
                                    0 => [
                                        'match' => [
                                            'texte' => [
                                                'query' => $getNangCao,
                                                'analyzer' => 'my_analyzer',
                                            ],
                                        ],
                                    ],
                                    //                                        1 => [
                                    //                                            'match' => [
                                    //                                                'ngan_chan' => '1',
                                    //                                            ],
                                    //                                        ],
                                ],
                            ],
                        ],
                        '',
                        '',
                        '',
                        ''
                    )->paginate(20)->onEachSide(2);;
                }
            }
        }
        if (!$getNangCao) {
            $data = $data->whereNull('st_id')->paginate(20)->onEachSide(2);;
        }
        $loadTaiSan = true;
        //$count = $data->count();
        $str_json2 = json_encode(array_filter(array(str_replace('"', "", $getcoban))));
        $str_json = json_encode(array_filter(array(str_replace('"', "", $getNangCao))));
        $count = $data->total();
        return view('admin.suutra.index', compact('str_json2', 'count', 'data', 'getcoban', 'getNangCao', 'str_json', 'priority', 'loadTaiSan'));
    }

    public function print(Request $request)
    {
        dd(1);
        DB::disableQueryLog();
        try {
            $index = SuuTraModel::select('texte', 'duong_su')->first();

            //        $index=SuuTraModel::reindex();

            ////dd(SuuTraModel::first()->basicInfo());
            //dd($index);
            $str_json = [];
            $str_json2 = [];
            $str_json_symbol = [];
            $str_json2_symbol = [];
            $getcoban = preg_replace('/\s+/', ' ', $request->get('coban'));
            $getcoban = str_replace(",", "", $getcoban);
            $getNangCao = preg_replace('/\s+/', ' ', $request->get('nangcao'));
            //$getNangCao=str_replace(",","",$getNangCao);

            $getNangCao = str_replace(array('\'', ';', '<', '>'), '', $getNangCao);
            $getcoban = str_replace(array(
                '\'',
                ',', ';', '<', '>'
            ), '', $getcoban);
            $getNangCao = $this->vn_to_str($getNangCao);
            $getcoban = $this->vn_to_str($getcoban);
            $id_vp = User::join('nhanvien', 'nhanvien.nv_id', '=', 'id')
                ->join('chinhanh', 'chinhanh.cn_id', '=', 'nhanvien.nv_vanphong')
                ->where('users.id', Sentinel::getUser()->id)->first()->cn_id;
            $ipaddress = '';
            if (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                    $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
                } else {
                    if (isset($_SERVER['HTTP_X_FORWARDED'])) {
                        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
                    } else {
                        if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
                            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
                        } else {
                            if (isset($_SERVER['HTTP_FORWARDED'])) {
                                $ipaddress = $_SERVER['HTTP_FORWARDED'];
                            } else {
                                if (isset($_SERVER['REMOTE_ADDR'])) {
                                    $ipaddress = $_SERVER['REMOTE_ADDR'];
                                } else {
                                    $ipaddress = 'UNKNOWN';
                                }
                            }
                        }
                    }
                }
            }
            if ($getcoban || $getNangCao) {
            }
            $space = strlen(self::convert_vi_to_en($getcoban));

            $lengh = 5;
            if ($space > 23) {
                $lengh = 1000;
            }
            $count = 0;
            $array = [
                'suutranb.ccv',
                'suutranb.vp',
                'suutranb.texte',
                'suutranb.ngay_nhap',
                'suutranb.ngay_cc',
                'suutranb.so_hd',
                'suutranb.ten_hd',
                'suutranb.st_id',
                'suutranb.duong_su',
                'suutranb.picture',
                'users.first_name',
                'chinhanh.cn_ten',
                'suutranb.ngan_chan',
                'suutranb.chu_y',
                'suutranb.status',
                'suutranb.ccv_master',
                'suutranb.vp_master',
                'suutranb.cancel_status',
                'suutranb.cancel_description',
                'suutranb.ma_phan_biet',
                'suutranb.ma_dong_bo',
                'suutranb.uchi_id_ngan_chan',
                'suutranb.updated_at',
                'suutranb.created_at',
                'suutranb.note',
                'suutranb.real_name',
                'suutranb.is_update',
                'suutranb.contract_period',
                'suutranb.property_info',
                'suutranb.transaction_content',
                'release_doc_number',
                'release_doc_date',
                'release_file_name',
                'release_file_path',
                'suutranb.uchi_id',
                'suutranb.release_doc_receive_date',
                'suutranb.prevent_doc_receive_date',
                'undisputed_date',
                'undisputed_note',
                'deleted_note'


            ];
            $status = false;
            $priority = $request->priority;
            $advanced = $request->advanced;
            $data = SuuTraModel::query()->leftjoin('users', 'users.id', '=', 'suutranb.ccv')
                ->leftjoin('chinhanh', 'cn_id', '=', 'suutranb.vp')
                ->select($array);

            if ($request->prevent) {
                $data = $data->whereIn('ngan_chan', [1, 3]);
            }
            $countPrevent = SuuTraModel::where('ngan_chan', 3)->count();
            $code = ChiNhanhModel::find(NhanVienModel::find(Sentinel::check()->id)->nv_vanphong);
            $countOffice = SuuTraModel::where('sync_code', $code->code_cn)->count();
            $isOffice = $request->isOffice;
            $so_hd = null;
            if ($request->isOffice == "true") {
                $data = $data->where('sync_code', $code->code_cn);
                $so_hd = $request->so_hd;
                if ($so_hd) {
                    $data = $data->where('so_hd', 'like', '%' . $so_hd . '%');
                }
            }
            $count = 0;
            $findMe = array('*', '"', '%');
            if ($getcoban && $getNangCao) {


                $keyNC = '"' . $getNangCao . '"';
                $keyCB = '"' . $getcoban . '"';

                if (true) {
                    /**
                     * if (str_contains($getNangCao, ' ')) {
                     *
                     * $status = preg_match_all('/\"([^\"]*?)\"/', $getNangCao, $matches);
                     *
                     * if ($status > 0) {
                     * $lengh = 8;
                     * $whereLike = [];
                     * $whereExact = "";
                     * $exactTerm = array_unique($matches[0]);
                     * $likeTerm = (trim(str_replace($exactTerm, '', $getNangCao)));
                     * $termIndex = "";
                     *
                     * //check dau
                     * if (strlen($getNangCao) != mb_strlen($getNangCao, 'utf-8')) {
                     * //co dau
                     *
                     * //$key=join("",$exactTerm);
                     * //$key=str_replace('"',"",$key);
                     * //$keyUpper=mb_strtoupper($key);
                     * //$keyLower=mb_strtolower($key);
                     * //$capitalKey=ucwords($key);
                     * //$whereLike[] = ['duong_su', 'LIKE', '%' . $keyUpper . '%'];
                     * //$whereLike[] = ['duong_su', 'LIKE', '%' . $keyLower . '%'];
                     * //$whereLike[] = ['duong_su', 'LIKE', '%' . $capitalKey . '%'];
                     * $key = join(" ", $exactTerm);
                     * if ($likeTerm) {
                     * if (strlen($likeTerm) == 4) {
                     * $lengh = 5;
                     * } elseif (strlen($likeTerm) == 9 || strlen($likeTerm) == 12) {
                     * $lengh = 500;
                     * }
                     * $lengh = 1000;
                     *
                     * } else {
                     * $whereExact = 'contains(suutranb.texte,' . "'" . $key . "'" . ')';
                     *
                     * }////
                     *
                     *
                     * } else {
                     * if (strlen($likeTerm) == 4) {
                     * $lengh = 5;
                     * } elseif (strlen($likeTerm) == 9 || strlen($likeTerm) == 12) {
                     * $lengh = 500;
                     * }
                     * $lengh = 1000;
                     *
                     * //khong dau
                     * $key = join(" AND ", $exactTerm);
                     * //$whereExact='contains(suutranb.duong_su_en,' ."'". $key."'".')';
                     * if ($likeTerm) {
                     * $likeTerm = str_replace(" ", ",", $likeTerm);
                     *
                     * $whereExact = 'contains(suutranb.texte_en,' . "'NEAR((" . $key . "," . $likeTerm . "), " . $lengh . ")'" . ')';
                     *
                     * } else {
                     * $whereExact = 'contains(suutranb.texte_en,' . "'" . $key . "'" . ')';
                     *
                     * }
                     *
                     * }
                     *
                     *
                     * if ($whereExact) {
                     * $data = $data->whereRaw($whereExact)->where($whereLike);
                     *
                     * } else {
                     * $data = $data->where($whereLike);
                     *
                     * }
                     * //$data= $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->get();;
                     * //$count=$data->count();
                     * } else {
                     *
                     * $key = join(",", explode(" ", $getNangCao));
                     * if (strlen($getNangCao) != mb_strlen($getNangCao, 'utf-8')) {
                     * $where = 'contains(suutranb.texte,' . "'NEAR((" . $key . "), " . $lengh . ")'" . ')';
                     * $data = $data->whereRaw($where);
                     * } else {
                     * $where = 'contains(suutranb.texte_en,' . "'NEAR((" . $key . "), " . $lengh . ")'" . ')';
                     * $data = $data->whereRaw($where);
                     * }
                     * //$data= $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->get();;
                     *
                     * }
                     *
                     *
                     * } else {
                     * //tim 1 word
                     * if (strlen($getNangCao) != mb_strlen($getNangCao, 'utf-8')) {
                     * //co dau
                     * $key = "'" . $getNangCao . "'";
                     * $where = 'contains(suutranb.texte,' . $key . ')';
                     * $data = $data->whereraw($where);
                     *
                     * $data = $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->get();;
                     * } else {
                     * $key = "'" . $getNangCao . "'";
                     * $where = 'contains(suutranb.texte_en,' . $key . ')';
                     * $data = $data->whereraw($where);
                     * //$data=$data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->get();;
                     * }
                     *
                     * }
                     **/


                    if (str_contains($getNangCao, ' ')) {

                        $status = preg_match_all('/\"([^\"]*?)\"/', $getNangCao, $matches);
                        if ($status > 0) {
                            //trong nhay
                            $whereLike = [];
                            $whereExact = "";
                            $exactTerm = array_unique($matches[0]);
                            foreach ($exactTerm as $word) {
                                $str_json[] = str_replace('"', "", $word);
                            }

                            $likeTerm = (trim(str_replace($exactTerm, '', $getNangCao)));

                            $termIndex = "";
                            //check dau
                            if (strlen($getNangCao) != mb_strlen($getNangCao, 'utf-8')) {


                                //co dau

                                //$key=join("",$exactTerm);
                                //$key=str_replace('"',"",$key);
                                //$keyUpper=mb_strtoupper($key);
                                //$keyLower=mb_strtolower($key);
                                //$capitalKey=ucwords($key);
                                //$whereLike[] = ['texte', 'LIKE', '%' . $keyUpper . '%'];
                                //$whereLike[] = ['texte', 'LIKE', '%' . $keyLower . '%'];
                                //$whereLike[] = ['texte', 'LIKE', '%' . $capitalKey . '%'];
                                $key = join(",", $exactTerm);
                                if ($likeTerm) {
                                    if (strlen($likeTerm) == 4) {
                                        $lengh = 5;
                                    } elseif (strlen($likeTerm) == 9 || strlen($likeTerm) == 12) {
                                        $lengh = 500;
                                    }

                                    if (str_contains($likeTerm, "*")) {
                                        ///
                                        $normal = "";
                                        $reverse = "";
                                        foreach (explode(" ", $likeTerm) as $word) {

                                            $str_json[] = str_replace(["*", "%"], "", $word);
                                            if (str_contains($word, "*")) {
                                                $word = strrev($word);
                                                $word = '"' . $word . '"';
                                                $reverse = $word . " " . $reverse;
                                            } else {
                                                $normal .= $word . " ";
                                            }
                                        }
                                        $normal = trim($normal);
                                        if ($normal) {
                                            if (count(explode(' ', $normal)) > 1) {
                                                $keyNormal = join(",", explode(" ", $normal));
                                                $where = 'contains(suutranb.texte,' . "'NEAR((" . $keyNormal . "),MAX)'" . ')';
                                                $data = $data->whereRaw($where);
                                            } else {
                                                if ($normal) {
                                                    $normal = '"' . str_replace('%', '*', $normal) . '"';
                                                    $where = 'contains(suutranb.texte_en,' . "'" . $normal . "'" . ')';
                                                    $data = $data->whereRaw($where);
                                                }
                                            }
                                        }
                                        $reverse = join(' OR ', explode(' ', trim($reverse)));
                                        $where = 'contains(suutranb.texte_reverse,' . "'" . $reverse . "'" . ')';
                                        $data = $data->whereRaw($where);
                                    } else {
                                        foreach (explode(" ", $likeTerm) as $word) {

                                            $str_json[] = str_replace(["*", "%"], "", $word);
                                        }
                                        //khong co *
                                        $string = join(",", explode(" ", trim($likeTerm)));

                                        //$key = join(",", $exactTerm);
                                        $whereExact = 'contains(suutranb.texte,' . "'NEAR((" . $key . "," . $string . ") ,MAX)'" . ')';
                                    }
                                } else {

                                    // $whereExact = 'contains(suutranb.texte,' . "'" . $key . "'" . ')';
                                    $string = str_replace(["*", "%"], "", $getNangCao);
                                    //$string = str_replace($str_json, "", $string);
                                    $string = str_replace(['"'], "", $string);
                                    foreach (explode(" ", $string) as $word) {
                                        $str_json[] = $word;
                                    }

                                    $string = join(",", explode(" ", trim($string)));
                                    $whereExact = 'contains(suutranb.texte,' . "'" . $key . "'" . ')';
                                }
                            } else {
                                //dd($lengh);
                                //khong dau
                                if (strlen($likeTerm) == 4) {
                                    $lengh = 5;
                                } elseif (strlen($likeTerm) == 9 || strlen($likeTerm) == 12) {
                                    $lengh = 500;
                                }
                                $lengh = 1000;

                                $key = join(",", $exactTerm);
                                //$whereExact='contains(suutranb.texte_en,' ."'". $key."'".')';
                                if ($likeTerm) {
                                    if (str_contains($likeTerm, "*")) {
                                        ///
                                        $normal = "";
                                        $reverse = "";
                                        foreach (explode(" ", $likeTerm) as $word) {

                                            $str_json[] = str_replace(["*", "%"], "", $word);
                                            if (str_contains($word, "*")) {
                                                $word = strrev($word);
                                                $word = '"' . $word . '"';
                                                $reverse = $word . " " . $reverse;
                                            } else {
                                                $normal .= $word . " ";
                                            }
                                        }

                                        $normal = trim($normal);

                                        if ($normal) {
                                            if (count(explode(' ', $normal)) > 1) {
                                                $keyNormal = join(",", explode(" ", $normal));
                                                $where = 'contains(suutranb.texte_en,' . "'NEAR((" . $keyNormal . "),MAX)'" . ')';
                                                $data = $data->whereRaw($where);
                                            } else {
                                                if ($normal) {
                                                    $normal = '"' . str_replace('%', '*', $normal) . '"';
                                                    $where = 'contains(suutranb.texte_en,' . "'" . $normal . "'" . ')';
                                                    $data = $data->whereRaw($where);
                                                }
                                            }
                                        }
                                        $reverse = join(' OR ', explode(' ', trim($reverse)));
                                        $where = 'contains(suutranb.texte_reverse,' . "'" . $reverse . "'" . ')';
                                        $data = $data->whereRaw($where);
                                    } else {
                                        $string = str_replace(["*", "%"], "", $getNangCao);
                                        $string = str_replace($str_json, "", $string);
                                        $string = str_replace(['"'], "", $string);
                                        foreach (explode(" ", $string) as $word) {
                                            $str_json[] = $word;
                                        }

                                        $string = join(",", explode(" ", trim($string)));
                                        $whereExact = 'contains(suutranb.texte_en,' . "'NEAR((" . $key . "," . $string . ") ,MAX)'" . ')';
                                    }
                                } else {
                                    $key = join(" AND ", $exactTerm);
                                    $whereExact = 'contains(suutranb.texte_en,' . "'" . $key . "'" . ')';
                                }
                            }


                            if ($whereExact) {
                                $data = $data->whereRaw($whereExact)->where($whereLike);
                            } else {
                                $data = $data->where($whereLike);
                            }
                            //here
                            //$data = $data->select($array)->orderByDesc("suutranb.st_id")->get();;
                            //$count=$data->count();
                        } else {
                            //khong trong nhay
                            if (str_contains($getNangCao, "*")) {

                                //co dau * khong trong nhay
                                $normal = "";
                                $reverse = "";
                                foreach (explode(" ", $getNangCao) as $word) {
                                    $str_json[] = str_replace(["*", "%"], "", $word);
                                    if (str_contains($word, "*")) {
                                        $word = strrev($word);
                                        $word = '"' . $word . '"';
                                        $reverse = $word . " " . $reverse;
                                    } else {
                                        $normal .= $word . " ";
                                    }
                                }

                                $normal = trim($normal);

                                if (strlen($getNangCao) != mb_strlen($getNangCao, 'utf-8')) {
                                    if (count(explode(' ', $normal)) > 1) {
                                        $key = join(",", explode(" ", $normal));
                                        $where = 'contains(suutranb.texte,' . "'NEAR((" . $key . "),MAX)'" . ')';
                                        $data = $data->whereRaw($where);
                                    } else {
                                        if ($normal) {
                                            $where = 'contains(suutranb.texte,' . "'" . $normal . "'" . ')';

                                            $data = $data->whereRaw($where);
                                        }
                                    }
                                } else {
                                    if (count(explode(' ', $normal)) > 1) {
                                        $key = join(",", explode(" ", $normal));
                                        $where = 'contains(suutranb.texte_en,' . "'NEAR((" . $key . "),MAX)'" . ')';
                                        $data = $data->whereRaw($where);
                                    } else {
                                        if ($normal) {
                                            $normal = '"' . str_replace('%', '*', $normal) . '"';
                                            $where = 'contains(suutranb.texte_en,' . "'" . $normal . "'" . ')';
                                            $data = $data->whereRaw($where);
                                        }
                                    }
                                }

                                $reverse = join(' OR ', explode(' ', trim($reverse)));
                                $where = 'contains(suutranb.texte_reverse,' . "'" . $reverse . "'" . ')';
                                $data = $data->whereRaw($where);
                                // $data = $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->get();

                            } else {
                                $keyArray = [];
                                foreach (explode(" ", str_replace("%", "*", $getNangCao)) as $item) {
                                    $str_json[] = str_replace(["*", "%"], "", $item);
                                    $keyArray[] = '"' . $item . '"';
                                }
                                $key = join(",", $keyArray);
                                $lengh = 200;
                                if (strlen($getNangCao) != mb_strlen($getNangCao, 'utf-8')) {
                                    $where = 'contains(suutranb.texte,' . "'NEAR((" . $key . "),MAX)'" . ')';
                                    $data = $data->whereRaw($where);
                                } else {
                                    $where = 'contains(suutranb.texte_en,' . "'NEAR((" . $key . "),MAX)'" . ')';

                                    $data = $data->whereRaw($where);
                                }
                                // $data = $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->get();;

                            }
                        }
                    } else {

                        //tim 1 word
                        $str_json[] = str_replace(["*", "%"], "", $getNangCao);
                        if (str_contains($getNangCao, "*")) {
                            $str_json[] = str_replace("*", "", $getNangCao);
                            $key = "'" . '"' . strrev($getNangCao) . '"' . "'";

                            $where = 'contains(suutranb.texte_reverse,' . $key . ')';
                            $data = $data->whereraw($where);
                            //$data = $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->get();;

                        } else {
                            $key = str_replace("%", "*", $getNangCao);
                            if (strlen($getNangCao) != mb_strlen($getNangCao, 'utf-8')) {
                                //co dau
                                $where = 'contains(suutranb.texte,' . "'" . $key . "'" . ')';
                                $data = $data->whereraw($where);

                                $data = $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id");;
                            } else {
                                $key = '"' . $key . '"';
                                $where = 'contains(suutranb.texte_en,' . "'" . $key . "'" . ')';
                                $data = $data->whereraw($where);
                                //$data = $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->get();;
                            }
                        }
                    }
                    if (str_contains($getcoban, ' ')) {
                        $status = preg_match_all('/\"([^\"]*?)\"/', $getcoban, $matches);

                        if ($status > 0) {
                            $lengh = 8;
                            $whereLike = [];
                            $whereExact = "";
                            $exactTerm = array_unique($matches[0]);
                            foreach ($exactTerm as $word) {
                                if (is_int(self::multi_strpos($word, $findMe))) {
                                    $str_json2_symbol[] = str_replace(['"', '*', '%'], "", $word);
                                } else {
                                    $str_json2[] = str_replace(['"', '*', '%'], "", $word);
                                }
                            }
                            $likeTerm = (trim(str_replace($exactTerm, '', $getcoban)));
                            foreach (explode(" ", $likeTerm) as $word) {
                                if (is_int(self::multi_strpos($word, $findMe))) {
                                    $str_json2_symbol[] = str_replace(['"', '*', '%'], "", $word);
                                } else {
                                    $str_json2[] = str_replace(['"', '*', '%'], "", $word);
                                }
                            }
                            $termIndex = "";
                            if ($likeTerm) {
                                foreach (explode(" ", $likeTerm) as $term) {
                                    if (str_contains($term, "*")) {
                                        $likeTerm = (trim(str_replace($term, '', $likeTerm)));
                                        $term = str_replace('*', "", $term);
                                        $termIndex .= '"' . $term . '"';
                                        //$whereLike[] = ['duong_su', 'LIKE', '%' . $term . '%'];

                                    }
                                }
                                if ($termIndex) {
                                    $data = $data->whereRaw('contains(suutranb.duong_su_index,' . "'" . $termIndex . "'" . ')');
                                }
                            }
                            //check dau
                            if (strlen($getcoban) != mb_strlen($getcoban, 'utf-8')) {
                                //co dau

                                //$key=join("",$exactTerm);
                                //$key=str_replace('"',"",$key);
                                //$keyUpper=mb_strtoupper($key);
                                //$keyLower=mb_strtolower($key);
                                //$capitalKey=ucwords($key);
                                //$whereLike[] = ['duong_su', 'LIKE', '%' . $keyUpper . '%'];
                                //$whereLike[] = ['duong_su', 'LIKE', '%' . $keyLower . '%'];
                                //$whereLike[] = ['duong_su', 'LIKE', '%' . $capitalKey . '%'];
                                $key = join(",", $exactTerm);
                                if ($likeTerm) {
                                    if (strlen($likeTerm) == 4) {
                                        $lengh = 5;
                                    } elseif (strlen($likeTerm) == 9 || strlen($likeTerm) == 12) {
                                        $lengh = 500;
                                    }
                                    $likeTerm = str_replace(" ", ",", $likeTerm);
                                    $whereExact = 'contains(suutranb.duong_su,' . "'NEAR((" . $key . "," . $likeTerm . "), " . $lengh . ",TRUE)'" . ')';
                                } else {
                                    $key = join(" AND ", $exactTerm);
                                    $whereExact = 'contains(suutranb.duong_su,' . "'" . $key . "'" . ')';
                                }
                            } else {
                                if (strlen($likeTerm) == 4) {
                                    $lengh = 10;
                                } elseif (strlen($likeTerm) == 9 || strlen($likeTerm) == 12) {
                                    $lengh = 500;
                                }
                                //khong dau
                                $key = join(" , ", $exactTerm);
                                //$whereExact='contains(suutranb.duong_su_en,' ."'". $key."'".')';
                                if ($likeTerm) {
                                    $likeTerm = str_replace(" ", ",", $likeTerm);
                                    $whereExact = 'contains(suutranb.duong_su_en,' . "'NEAR((" . $key . "," . $likeTerm . "), " . $lengh . ",TRUE)'" . ')';
                                } else {
                                    $key = join(" AND ", $exactTerm);
                                    $whereExact = 'contains(suutranb.duong_su_en,' . "'" . $key . "'" . ')';
                                }
                            }


                            if ($whereExact) {
                                $data = $data->whereraw($whereExact)->where($whereLike);
                            } else {
                                $data = $data->where($whereLike);
                            }
                            //$data= $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->get();;
                            //$count=$data->count();
                        } else {

                            $key = join(",", explode(" ", $getcoban));
                            $key = join(",", explode(" ", $getcoban));
                            $whereLike = [];
                            $whereExact = "";
                            $exactTerm = array_unique($matches[0]);
                            foreach ($exactTerm as $word) {
                                if (is_int(self::multi_strpos($word, $findMe))) {
                                    $str_json2_symbol[] = str_replace(['"', '*', '%'], "", $word);
                                } else {
                                    $str_json2[] = str_replace(['"', '*', '%'], "", $word);
                                }
                            }
                            $likeTerm = (trim(str_replace($exactTerm, '', $getcoban)));
                            foreach (explode(" ", $likeTerm) as $word) {
                                if (is_int(self::multi_strpos($word, $findMe))) {
                                    $str_json2_symbol[] = str_replace(['"', '*', '%'], "", $word);
                                } else {
                                    $str_json2[] = str_replace(['"', '*', '%'], "", $word);
                                }
                            }
                            $termIndex = "";
                            if ($likeTerm) {
                                foreach (explode(" ", $likeTerm) as $term) {
                                    if (str_contains($term, "*")) {
                                        $likeTerm = (trim(str_replace($term, '', $likeTerm)));
                                        $term = str_replace('*', "", $term);
                                        $termIndex .= '"' . $term . '"';
                                        //$whereLike[] = ['duong_su', 'LIKE', '%' . $term . '%'];

                                    }
                                }
                                if ($termIndex) {
                                    $data = $data->whereRaw('contains(suutranb.duong_su_index,' . "'" . $termIndex . "'" . ')');
                                }
                            }

                            if (strlen($getcoban) != mb_strlen($getcoban, 'utf-8')) {
                                //$whereExact='contains(suutranb.duong_su_en,' ."'". $key."'".')';
                                if ($likeTerm) {
                                    if (count(explode(' ', $likeTerm)) > 1) {

                                        $likeTerm = str_replace(" ", ",", $likeTerm);
                                        $whereExact = 'contains(suutranb.duong_su,' . "'NEAR((" . $likeTerm . "), " . $lengh . ",TRUE)'" . ')';
                                    } else {
                                        $whereExact = 'contains(suutranb.duong_su,' . "'" . $likeTerm . "'" . ')';
                                    }
                                }

                                $data = $data->whereRaw($whereExact);
                            } else {
                                $lengh = 5;
                                if ($likeTerm) {
                                    if (count(explode(' ', $likeTerm)) > 1) {

                                        $likeTerm = str_replace(" ", ",", $likeTerm);
                                        $whereExact = 'contains(suutranb.duong_su_en,' . "'NEAR((" . $likeTerm . "), " . $lengh . ",TRUE)'" . ')';
                                    } else {
                                        $whereExact = 'contains(suutranb.duong_su_en,' . "'" . $likeTerm . "'" . ')';
                                    }
                                }

                                $data = $data->whereRaw($whereExact);
                            }
                            //$data= $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->get();;

                        }
                    } else {
                        foreach (explode(" ", $getcoban) as $word) {
                            if (is_int(self::multi_strpos($word, $findMe))) {
                                $str_json2_symbol[] = str_replace(['"', '*', '%'], "", $word);
                            } else {
                                $str_json2[] = str_replace(['"', '*', '%'], "", $word);
                            }
                        }
                        //tim 1 word
                        if (strlen($getcoban) != mb_strlen($getcoban, 'utf-8')) {
                            //co dau
                            $key = "'" . $getcoban . "'";
                            $where = 'contains(suutranb.duong_su,' . $key . ')';
                            $data = $data->whereraw($where);

                            //$data = $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->get();;
                        } else {
                            $key = "'" . $getcoban . "'";
                            $where = 'contains(suutranb.duong_su_en,' . $key . ')';
                            $data = $data->whereraw($where);
                            //$data=$data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->get();;
                        }
                    }

                    $count = $data->count();
                    $data = $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->get();;
                } else {
                    if ($priority) {
                        if ($request->exactly) {
                            $data = SuuTraModel::searchByQuery(
                                [
                                    'bool' => [
                                        'should' => [
                                            0 => [
                                                'match_phrase' => [
                                                    'duong_su' => [
                                                        'query' => $getNangCao,
                                                        'analyzer' => 'my_analyzer',
                                                    ],
                                                ],
                                            ],
                                            1 => [
                                                'match' => [
                                                    'ngan_chan' => [
                                                        'query' => '3',
                                                        'boost' => 10
                                                    ],

                                                ],
                                            ],
                                        ],
                                    ],
                                ]
                            )->get();;
                        } else {
                            $data = SuuTraModel::searchByQuery(
                                [
                                    'bool' => [
                                        'must' => [
                                            0 => [
                                                'match' => [
                                                    'duong_su' => [
                                                        'query' => $getcoban,
                                                        'analyzer' => 'my_analyzer',
                                                    ],
                                                ],
                                            ],
                                            1 => [
                                                'match' => [
                                                    'texte' => [
                                                        'query' => $getNangCao,
                                                        'analyzer' => 'my_analyzer',
                                                    ],
                                                ],
                                            ],

                                        ],
                                        'should' => [
                                            0 => [
                                                'match' => [
                                                    'ngan_chan' => [
                                                        'query' => '3',
                                                        'boost' => 10
                                                    ],

                                                ],
                                            ],
                                        ]

                                    ]
                                ],
                                '',
                                '',
                                '20',
                                ''

                            )->get();;
                        }
                    } else {
                        if ($request->exactly) {
                            $data = SuuTraModel::searchByQuery(
                                [
                                    'bool' => [
                                        'must' => [
                                            0 => [
                                                'match_phrase' => [
                                                    'duong_su' => [
                                                        'query' => $getcoban,
                                                        'analyzer' => 'my_analyzer',
                                                    ],
                                                ],
                                            ],


                                        ],
                                        //                                'should'=>[
                                        //                                    0 => [
                                        //                                        'match' => [
                                        //                                            'ngan_chan' => '1',
                                        //                                        ],
                                        //                                    ],
                                        //                                ]

                                    ]
                                ],
                                '',
                                '',
                                '20',
                                ''

                            )->get();;
                        } else {
                            $data = SuuTraModel::searchByQuery(
                                [
                                    'bool' => [
                                        'must' => [
                                            0 => [
                                                'match' => [
                                                    'duong_su' => [
                                                        'query' => $getcoban,
                                                        'analyzer' => 'my_analyzer',
                                                    ],
                                                ],
                                            ],
                                            1 => [
                                                'match' => [
                                                    'texte' => [
                                                        'query' => $getNangCao,
                                                        'analyzer' => 'my_analyzer',
                                                    ],
                                                ],
                                            ],

                                        ],
                                        //                                'should'=>[
                                        //                                    0 => [
                                        //                                        'match' => [
                                        //                                            'ngan_chan' => '1',
                                        //                                        ],
                                        //                                    ],
                                        //                                ]

                                    ]
                                ],
                                '',
                                '',
                                '20',
                                ''

                            )->get();;
                        }
                    }
                }


                //$count = $data->count();
            } else {
                if ($getNangCao) {

                    $key = '"' . $getNangCao . '"';
                    if (true) {
                        if (str_contains($getNangCao, ' ')) {

                            $status = preg_match_all('/\"([^\"]*?)\"/', $getNangCao, $matches);
                            if ($status > 0) {
                                //trong nhay
                                $whereLike = [];
                                $whereExact = "";
                                $exactTerm = array_unique($matches[0]);
                                foreach ($exactTerm as $word) {
                                    $str_json[] = str_replace('"', "", $word);
                                }

                                $likeTerm = (trim(str_replace($exactTerm, '', $getNangCao)));

                                $termIndex = "";
                                //check dau
                                if (strlen($getNangCao) != mb_strlen($getNangCao, 'utf-8')) {


                                    //co dau

                                    //$key=join("",$exactTerm);
                                    //$key=str_replace('"',"",$key);
                                    //$keyUpper=mb_strtoupper($key);
                                    //$keyLower=mb_strtolower($key);
                                    //$capitalKey=ucwords($key);
                                    //$whereLike[] = ['texte', 'LIKE', '%' . $keyUpper . '%'];
                                    //$whereLike[] = ['texte', 'LIKE', '%' . $keyLower . '%'];
                                    //$whereLike[] = ['texte', 'LIKE', '%' . $capitalKey . '%'];
                                    $key = join(",", $exactTerm);
                                    if ($likeTerm) {
                                        if (strlen($likeTerm) == 4) {
                                            $lengh = 5;
                                        } elseif (strlen($likeTerm) == 9 || strlen($likeTerm) == 12) {
                                            $lengh = 500;
                                        }

                                        if (str_contains($likeTerm, "*")) {
                                            ///
                                            $normal = "";
                                            $reverse = "";
                                            foreach (explode(" ", $likeTerm) as $word) {

                                                $str_json[] = str_replace(["*", "%"], "", $word);
                                                if (str_contains($word, "*")) {
                                                    $word = strrev($word);
                                                    $word = '"' . $word . '"';
                                                    $reverse = $word . " " . $reverse;
                                                } else {
                                                    $normal .= $word . " ";
                                                }
                                            }
                                            $normal = trim($normal);
                                            if ($normal) {
                                                if (count(explode(' ', $normal)) > 1) {
                                                    $keyNormal = join(",", explode(" ", $normal));
                                                    $where = 'contains(suutranb.texte,' . "'NEAR((" . $keyNormal . "),MAX)'" . ')';
                                                    $data = $data->whereRaw($where);
                                                } else {
                                                    if ($normal) {
                                                        $normal = '"' . str_replace('%', '*', $normal) . '"';
                                                        $where = 'contains(suutranb.texte_en,' . "'" . $normal . "'" . ')';
                                                        $data = $data->whereRaw($where);
                                                    }
                                                }
                                            }
                                            $reverse = join(' OR ', explode(' ', trim($reverse)));
                                            $where = 'contains(suutranb.texte_reverse,' . "'" . $reverse . "'" . ')';
                                            $data = $data->whereRaw($where);
                                        } else {
                                            foreach (explode(" ", $likeTerm) as $word) {

                                                $str_json[] = str_replace(["*", "%"], "", $word);
                                            }
                                            //khong co *
                                            $string = join(",", explode(" ", trim($likeTerm)));
                                            $whereExact = 'contains(suutranb.texte,' . "'NEAR((" . $key . "," . $string . ") ,MAX)'" . ')';
                                        }
                                    } else {

                                        // $whereExact = 'contains(suutranb.texte,' . "'" . $key . "'" . ')';
                                        $string = str_replace(["*", "%"], "", $getNangCao);
                                        //$string = str_replace($str_json, "", $string);
                                        $string = str_replace(['"'], "", $string);
                                        foreach (explode(" ", $string) as $word) {
                                            $str_json[] = $word;
                                        }

                                        $string = join(",", explode(" ", trim($string)));
                                        $key = join(" AND ", $exactTerm);
                                        $whereExact = 'contains(suutranb.texte,' . "'" . $key . "'" . ')';
                                    }
                                } else {
                                    //dd($lengh);
                                    //khong dau
                                    if (strlen($likeTerm) == 4) {
                                        $lengh = 5;
                                    } elseif (strlen($likeTerm) == 9 || strlen($likeTerm) == 12) {
                                        $lengh = 500;
                                    }
                                    $lengh = 1000;

                                    $key = join(" ", $exactTerm);
                                    //$whereExact='contains(suutranb.texte_en,' ."'". $key."'".')';
                                    if ($likeTerm) {
                                        if (str_contains($likeTerm, "*")) {
                                            ///
                                            $normal = "";
                                            $reverse = "";
                                            foreach (explode(" ", $likeTerm) as $word) {

                                                $str_json[] = str_replace(["*", "%"], "", $word);
                                                if (str_contains($word, "*")) {
                                                    $word = strrev($word);
                                                    $word = '"' . $word . '"';
                                                    $reverse = $word . " " . $reverse;
                                                } else {
                                                    $normal .= $word . " ";
                                                }
                                            }

                                            $normal = trim($normal);

                                            if ($normal) {
                                                if (count(explode(' ', $normal)) > 1) {
                                                    $keyNormal = join(",", explode(" ", $normal));
                                                    $where = 'contains(suutranb.texte_en,' . "'NEAR((" . $keyNormal . "),MAX)'" . ')';
                                                    $data = $data->whereRaw($where);
                                                } else {
                                                    if ($normal) {
                                                        $normal = '"' . str_replace('%', '*', $normal) . '"';
                                                        $where = 'contains(suutranb.texte_en,' . "'" . $normal . "'" . ')';
                                                        $data = $data->whereRaw($where);
                                                    }
                                                }
                                            }
                                            $reverse = join(' OR ', explode(' ', trim($reverse)));
                                            $where = 'contains(suutranb.texte_reverse,' . "'" . $reverse . "'" . ')';
                                            $data = $data->whereRaw($where);
                                        } else {
                                            $string = str_replace(["*", "%"], "", $getNangCao);
                                            $string = str_replace($str_json, "", $string);
                                            $string = str_replace(['"'], "", $string);
                                            foreach (explode(" ", $string) as $word) {
                                                $str_json[] = $word;
                                            }

                                            $string = join(",", explode(" ", trim($string)));
                                            $whereExact = 'contains(suutranb.texte_en,' . "'NEAR((" . $key . "," . $string . ") ,MAX)'" . ')';
                                        }
                                    } else {
                                        $whereExact = 'contains(suutranb.texte_en,' . "'" . $key . "'" . ')';
                                    }
                                }


                                if ($whereExact) {
                                    $data = $data->whereRaw($whereExact)->where($whereLike);
                                } else {
                                    $data = $data->where($whereLike);
                                }
                                //here
                                $data = $data->select($array)->orderByDesc("suutranb.st_id")->get();;
                                //$count=$data->count();
                            } else {
                                //khong trong nhay
                                if (str_contains($getNangCao, "*")) {

                                    //co dau * khong trong nhay
                                    $normal = "";
                                    $reverse = "";
                                    foreach (explode(" ", $getNangCao) as $word) {
                                        $str_json[] = str_replace(["*", "%"], "", $word);
                                        if (str_contains($word, "*")) {
                                            $word = strrev($word);
                                            $word = '"' . $word . '"';
                                            $reverse = $word . " " . $reverse;
                                        } else {
                                            $normal .= $word . " ";
                                        }
                                    }

                                    $normal = trim($normal);

                                    if (strlen($getNangCao) != mb_strlen($getNangCao, 'utf-8')) {
                                        if (count(explode(' ', $normal)) > 1) {
                                            $key = join(",", explode(" ", $normal));
                                            $where = 'contains(suutranb.texte,' . "'NEAR((" . $key . "),MAX)'" . ')';
                                            $data = $data->whereRaw($where);
                                        } else {
                                            if ($normal) {
                                                $where = 'contains(suutranb.texte,' . "'" . $normal . "'" . ')';

                                                $data = $data->whereRaw($where);
                                            }
                                        }
                                    } else {
                                        if (count(explode(' ', $normal)) > 1) {
                                            $key = join(",", explode(" ", $normal));
                                            $where = 'contains(suutranb.texte_en,' . "'NEAR((" . $key . "),MAX)'" . ')';
                                            $data = $data->whereRaw($where);
                                        } else {
                                            if ($normal) {
                                                $normal = '"' . str_replace('%', '*', $normal) . '"';
                                                $where = 'contains(suutranb.texte_en,' . "'" . $normal . "'" . ')';
                                                $data = $data->whereRaw($where);
                                            }
                                        }
                                    }

                                    $reverse = join(' OR ', explode(' ', trim($reverse)));
                                    $where = 'contains(suutranb.texte_reverse,' . "'" . $reverse . "'" . ')';
                                    $data = $data->whereRaw($where);
                                    $data = $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->get();
                                } else {
                                    $keyArray = [];
                                    foreach (explode(" ", str_replace("%", "*", $getNangCao)) as $item) {
                                        $str_json[] = str_replace(["*", "%"], "", $item);
                                        $keyArray[] = '"' . $item . '"';
                                    }
                                    $key = join(",", $keyArray);
                                    $lengh = 200;
                                    if (strlen($getNangCao) != mb_strlen($getNangCao, 'utf-8')) {
                                        $where = 'contains(suutranb.texte,' . "'NEAR((" . $key . "),MAX)'" . ')';
                                        $data = $data->whereRaw($where);
                                    } else {
                                        $where = 'contains(suutranb.texte_en,' . "'NEAR((" . $key . "),MAX)'" . ')';

                                        $data = $data->whereRaw($where);
                                    }
                                    $data = $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->get();;
                                }
                            }
                        } else {

                            //tim 1 word
                            $str_json[] = str_replace(["*", "%"], "", $getNangCao);
                            if (str_contains($getNangCao, "*")) {
                                $str_json[] = str_replace("*", "", $getNangCao);
                                $key = "'" . '"' . strrev($getNangCao) . '"' . "'";

                                $where = 'contains(suutranb.texte_reverse,' . $key . ')';
                                $data = $data->whereraw($where);
                                $data = $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->get();;
                            } else {
                                $key = str_replace("%", "*", $getNangCao);
                                if (strlen($getNangCao) != mb_strlen($getNangCao, 'utf-8')) {
                                    //co dau
                                    $where = 'contains(suutranb.texte,' . "'" . $key . "'" . ')';
                                    $data = $data->whereraw($where);

                                    $data = $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->get();;
                                } else {
                                    $key = '"' . $key . '"';
                                    $where = 'contains(suutranb.texte_en,' . "'" . $key . "'" . ')';
                                    $data = $data->whereraw($where);
                                    $data = $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->get();;
                                }
                            }
                        }
                    } else {
                        if ($priority) {
                            if ($request->exactly) {
                                $data = SuuTraModel::searchByQuery(
                                    [
                                        'bool' => [
                                            'should' => [
                                                0 => [
                                                    'match_phrase' => [
                                                        'texte' => [
                                                            'query' => $getNangCao,
                                                            'analyzer' => 'my_analyzer',
                                                        ],
                                                    ],
                                                ],
                                                1 => [
                                                    'match' => [
                                                        'ngan_chan' => [
                                                            'query' => '3',
                                                            'boost' => 10
                                                        ],

                                                    ],
                                                ],
                                            ],
                                        ],
                                    ]
                                )->get();;
                            } else {
                                $data = SuuTraModel::searchByQuery(
                                    [
                                        'bool' => [
                                            'should' => [
                                                0 => [
                                                    'match' => [
                                                        'texte' => [
                                                            'query' => $getNangCao,
                                                            'analyzer' => 'my_analyzer',
                                                        ],
                                                    ],
                                                ],
                                                1 => [
                                                    'match' => [
                                                        'ngan_chan' => [
                                                            'query' => '3',
                                                            'boost' => 10
                                                        ],

                                                    ],
                                                ],
                                            ],
                                        ],
                                    ]
                                )->get();;
                            }
                        } else {
                            if ($request->exactly) {
                                $data = SuuTraModel::searchByQuery(
                                    [
                                        'bool' => [
                                            'should' => [
                                                0 => [
                                                    'match_phrase' => [
                                                        'texte' => [
                                                            'query' => $getNangCao,
                                                            'analyzer' => 'my_analyzer',
                                                        ],
                                                    ],
                                                ],
                                                //                                        1 => [
                                                //                                            'match' => [
                                                //                                                'ngan_chan' => '1',
                                                //                                            ],
                                                //                                        ],
                                            ],
                                        ],
                                    ],
                                    '',
                                    '',
                                    '',
                                    ''
                                )->get();;
                            } else {
                                $data = SuuTraModel::searchByQuery(
                                    [
                                        'bool' => [
                                            'should' => [
                                                0 => [
                                                    'match' => [
                                                        'texte' => [
                                                            'query' => $getNangCao,
                                                            'analyzer' => 'my_analyzer',
                                                        ],
                                                    ],
                                                ],
                                                //                                        1 => [
                                                //                                            'match' => [
                                                //                                                'ngan_chan' => '1',
                                                //                                            ],
                                                //                                        ],
                                            ],
                                        ],
                                    ],
                                    '',
                                    '',
                                    '',
                                    ''
                                )->get();;
                            }
                        }
                    }

                    //  $count = $data->count();

                } elseif ($getcoban) {

                    //                dd($status);
                    $where = "";
                    if (true) {
                        if (str_contains($getcoban, ' ')) {
                            $status = preg_match_all('/\"([^\"]*?)\"/', $getcoban, $matches);


                            if ($status > 0) {

                                $whereLike = [];
                                $whereExact = "";
                                $exactTerm = array_unique($matches[0]);
                                foreach ($exactTerm as $word) {
                                    $word = trim($word);
                                    if (is_int(self::multi_strpos($word, $findMe))) {
                                        $str_json2_symbol[] = str_replace(['"', '*', '%'], "", $word);
                                    } else {
                                        $str_json2[] = str_replace(['"', '*', '%'], "", $word);
                                    }
                                }
                                $likeTerm = (trim(str_replace($exactTerm, '', $getcoban)));
                                $termIndex = "";
                                if ($likeTerm) {

                                    foreach (explode(" ", $likeTerm) as $term) {
                                        if (str_contains($term, "*")) {
                                            $likeTerm = (trim(str_replace($term, '', $likeTerm)));
                                            $term = str_replace('*', "", $term);
                                            $str_json2[] = $term;
                                            $termIndex .= '"' . $term . '"';
                                            //$whereLike[] = ['duong_su', 'LIKE', '%' . $term . '%'];

                                        } else {
                                            $str_json2[] = $likeTerm;
                                        }
                                    }
                                    if ($termIndex) {
                                        $data = $data->whereRaw('contains(suutranb.duong_su_index,' . "'" . $termIndex . "'" . ')');
                                    }
                                }
                                //check dau
                                if (strlen($getcoban) != mb_strlen($getcoban, 'utf-8')) {
                                    //co dau

                                    //$key=join("",$exactTerm);
                                    //$key=str_replace('"',"",$key);
                                    //$keyUpper=mb_strtoupper($key);
                                    //$keyLower=mb_strtolower($key);
                                    //$capitalKey=ucwords($key);
                                    //$whereLike[] = ['duong_su', 'LIKE', '%' . $keyUpper . '%'];
                                    //$whereLike[] = ['duong_su', 'LIKE', '%' . $keyLower . '%'];
                                    //$whereLike[] = ['duong_su', 'LIKE', '%' . $capitalKey . '%'];
                                    $key = join(",", $exactTerm);
                                    if ($likeTerm) {
                                        if (strlen($likeTerm) == 4) {
                                            $lengh = 5;
                                        } elseif (strlen($likeTerm) == 9 || strlen($likeTerm) == 12) {
                                            $lengh = 500;
                                        }

                                        $likeTerm = join(",", explode(" ", $likeTerm));
                                        $whereExact = 'contains(suutranb.duong_su,' . "'NEAR((" . $key . "," . $likeTerm . "), " . $lengh . ",TRUE)'" . ')';
                                    } else {
                                        $key = join(" AND ", $exactTerm);
                                        $whereExact = 'contains(suutranb.duong_su,' . "'" . $key . "'" . ')';
                                    }
                                } else {
                                    //dd($lengh);
                                    //khong dau
                                    if (strlen($likeTerm) == 4) {
                                        $lengh = 5;
                                    } elseif (strlen($likeTerm) == 9 || strlen($likeTerm) == 12) {
                                        $lengh = 500;
                                    }

                                    $key = join(",", $exactTerm);
                                    //$whereExact='contains(suutranb.duong_su_en,' ."'". $key."'".')';
                                    if ($likeTerm) {
                                        $likeTerm = join(",", explode(" ", $likeTerm));
                                        $whereExact = 'contains(suutranb.duong_su_en,' . "'NEAR((" . $key . "," . $likeTerm . "), " . $lengh . ",TRUE)'" . ')';
                                    } else {
                                        $key = join(" AND ", $exactTerm);
                                        $whereExact = 'contains(suutranb.duong_su_en,' . "'" . $key . "'" . ')';
                                    }
                                }

                                if ($whereExact) {
                                    $data = $data->whereRaw($whereExact)->where($whereLike);
                                } else {
                                    $data = $data->where($whereLike);
                                }
                                $data = $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->get();;
                                //$count=$data->count();
                            } else {

                                $key = join(",", explode(" ", $getcoban));
                                foreach (explode(",", $key) as $word) {
                                    if (is_int(self::multi_strpos($word, $findMe))) {
                                        $str_json2_symbol[] = str_replace(['"', '*', '%'], "", $word);
                                    } else {
                                        $str_json2[] = str_replace(['"', '*', '%'], "", $word);
                                    }
                                    //$str_json2[]=$word;
                                }
                                $whereLike = [];
                                $whereExact = "";
                                $exactTerm = array_unique($matches[0]);
                                $likeTerm = (trim(str_replace($exactTerm, '', $getcoban)));
                                $termIndex = "";
                                if ($likeTerm) {
                                    foreach (explode(" ", $likeTerm) as $term) {
                                        if (str_contains($term, "*")) {
                                            $likeTerm = (trim(str_replace($term, '', $likeTerm)));
                                            $term = str_replace('*', "", $term);
                                            $termIndex .= '"' . $term . '"';
                                            //$whereLike[] = ['duong_su', 'LIKE', '%' . $term . '%'];

                                        } else {
                                        }
                                    }
                                    if ($termIndex) {
                                        $data = $data->whereRaw('contains(suutranb.duong_su_index,' . "'" . $termIndex . "'" . ')');
                                    }
                                }
                                if (strlen($getcoban) != mb_strlen($getcoban, 'utf-8')) {
                                    if ($likeTerm) {
                                        if (strlen($likeTerm) == 4) {
                                            $lengh = 10;
                                        } elseif (strlen($likeTerm) == 9 || strlen($likeTerm) == 12) {
                                            $lengh = 500;
                                        }
                                        if (count(explode(' ', $likeTerm)) > 1) {

                                            $likeTerm = str_replace(" ", ",", $likeTerm);
                                            $whereExact = 'contains(suutranb.duong_su,' . "'NEAR((" . $likeTerm . "), " . $lengh . ",TRUE)'" . ')';
                                        } else {
                                            $whereExact = 'contains(suutranb.duong_su,' . "'" . $likeTerm . "'" . ')';
                                        }
                                    } else {
                                        $whereExact = 'contains(suutranb.duong_su,' . "'" . $key . "'" . ')';
                                    }
                                    $data = $data->whereRaw($whereExact);
                                } else {
                                    if ($likeTerm) {
                                        if (strlen($likeTerm) == 4) {
                                            $lengh = 5;
                                        } elseif (strlen($likeTerm) == 9 || strlen($likeTerm) == 12) {
                                            $lengh = 500;
                                        }
                                        if (count(explode(' ', $likeTerm)) > 1) {

                                            $likeTerm = str_replace(" ", ",", $likeTerm);
                                            $whereExact = 'contains(suutranb.duong_su_en,' . "'NEAR((" . $likeTerm . "), " . $lengh . ",TRUE)'" . ')';
                                        } else {
                                            $whereExact = 'contains(suutranb.duong_su_en,' . "'" . $likeTerm . "'" . ')';
                                        }
                                    } else {
                                        $whereExact = 'contains(suutranb.duong_su_en,' . "'" . $key . "'" . ')';
                                    }
                                    $data = $data->whereRaw($whereExact);
                                }
                                $data = $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->get();;
                            }
                        } else {
                            //tim 1 word
                            $str_json2[] = $getcoban;
                            if (strlen($getcoban) != mb_strlen($getcoban, 'utf-8')) {
                                //co dau
                                $key = "'" . $getcoban . "'";
                                $where = 'contains(suutranb.duong_su,' . $key . ')';
                                $data = $data->whereraw($where);

                                $data = $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->get();;
                            } else {
                                $key = "'" . $getcoban . "'";
                                $where = 'contains(suutranb.duong_su_en,' . $key . ')';
                                $data = $data->whereraw($where);
                                $data = $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->get();;
                            }
                        }
                    } else {
                        if ($priority) {

                            if ($request->exactly) {
                                $data = SuuTraModel::searchByQuery(
                                    [
                                        'bool' => [
                                            'should' => [
                                                0 => [
                                                    'match_phrase' => [
                                                        'duong_su' => [
                                                            'query' => $getNangCao,
                                                            'analyzer' => 'my_analyzer',
                                                        ],
                                                    ],
                                                ],
                                                1 => [
                                                    'match' => [
                                                        'ngan_chan' => [
                                                            'query' => '3',
                                                            'boost' => 10
                                                        ],

                                                    ],
                                                ],
                                            ],
                                        ],
                                    ]
                                )->get();;
                            } else {
                                $data = SuuTraModel::searchByQuery(
                                    [
                                        'bool' => [
                                            'should' => [
                                                0 => [
                                                    'match' => [
                                                        'duong_su' => [
                                                            'query' => $getcoban,
                                                            'analyzer' => 'my_analyzer',
                                                        ],
                                                    ],
                                                ],
                                                1 => [
                                                    'match' => [
                                                        'ngan_chan' => [
                                                            'query' => '3',
                                                            'boost' => 10
                                                        ],

                                                    ],
                                                ],
                                            ],
                                        ],
                                    ]
                                )->get();;
                            }
                        } else {
                            if ($request->exactly) {
                                $data = SuuTraModel::searchByQuery(
                                    [
                                        'bool' => [
                                            'should' => [
                                                0 => [
                                                    'match_phrase' => [
                                                        'duong_su' => [
                                                            'query' => $getcoban,
                                                            'analyzer' => 'my_analyzer',
                                                        ],
                                                    ],
                                                ],
                                                //                                        1 => [
                                                //                                            'match' => [
                                                //                                                'ngan_chan' => '1',
                                                //                                            ],
                                                //                                        ],
                                            ],
                                        ],
                                    ],
                                    '',
                                    '',
                                    '',
                                    ''
                                )->get();;
                            } else {
                                $data = SuuTraModel::searchByQuery(
                                    [
                                        'bool' => [
                                            'should' => [
                                                0 => [
                                                    'match' => [
                                                        'duong_su' => [
                                                            'query' => $getcoban,
                                                            'analyzer' => 'my_analyzer',
                                                        ],
                                                    ],
                                                ],
                                                //                                        1 => [
                                                //                                            'match' => [
                                                //                                                'ngan_chan' => '1',
                                                //                                            ],
                                                //                                        ],
                                            ],
                                        ],
                                    ],
                                    '',
                                    '',
                                    '',
                                    ''
                                )->get();;
                            }
                        }
                    }
                } else {
                }
            }
            if ($getcoban || $getNangCao) {
                //        $data=collect($data->toArray());
                //dd($data);
            } else {
                $data = $data->orderByDesc("suutranb.st_id")->get();;
            }
            $count = $data->count();
            $loadTaiSan = false;
            $option = [];
            $option['accuracy'] = "complementary";
            $option['diacritics'] = true;
            $option['separateWordSearch'] = true;
            $status = preg_match_all('/\"([^\"]*?)\"/', $getcoban, $matches);
            //$str_json2=json_encode(["Thanh Nhuan","109"]);
            //dd($str_json2);
            $str_json2 = json_encode($str_json2, JSON_UNESCAPED_UNICODE);
            //$str_json2 = json_encode(array_filter(array(str_replace('"', "", $getcoban))));
            $str_json = json_encode($str_json, JSON_UNESCAPED_UNICODE);
            $str_json_symbol = json_encode($str_json_symbol, JSON_UNESCAPED_UNICODE);
            $str_json2_symbol = json_encode($str_json2_symbol, JSON_UNESCAPED_UNICODE);
            //dd($str_json2,$option);
            //            $str_json = json_encode(array_filter(array(str_replace('"', "", $getNangCao))));
            $exactly = $request->exactly;
        } catch (Exception $e) {
            return redirect(route('searchSolr'))->with('error', 'Vui lòng nhập lại đúng cú pháp tìm kiếm hoặc liên hệ hỗ trợ để được hướng dẫn!');
        }


        $loadTaiSan = false;
        $isPrevent = $request->prevent ?? false;
        return view('admin.suutra.printNew', compact('str_json2', 'count', 'data', 'getcoban', 'getNangCao', 'str_json'));
    }

    public function indexAdvanced(Request $request)
    {

        //        $index=SuuTraModel::reindex();
        $index = SuuTraModel::select('texte', 'duong_su')->first();

        ////dd(SuuTraModel::first()->basicInfo());
        //dd($index);
        $str_json = json_encode([]);
        $getcoban = $request->get('coban');
        $getNangCao = $request->get('nangcao');
        $array = [
            'suutranb.ccv',
            'suutranb.vp',
            'suutranb.texte',
            'suutranb.ngay_nhap',
            'suutranb.ngay_cc',
            'suutranb.so_hd',
            'suutranb.ten_hd',
            'suutranb.st_id',
            'suutranb.duong_su',
            'suutranb.picture',
            'users.first_name',
            'chinhanh.cn_ten',
            'suutranb.ngan_chan',
            'suutranb.chu_y',
            'suutranb.status',
            'suutranb.ccv_master',
            'suutranb.vp_master',
            'suutranb.cancel_status',
            'suutranb.cancel_description',
            'suutranb.ma_phan_biet',
            'suutranb.ma_dong_bo',
            'suutranb.uchi_id_ngan_chan',
            'suutranb.updated_at',
            'suutranb.created_at',
            'suutranb.note',
            'suutranb.real_name'


        ];
        $status = false;
        $priority = $request->priority;
        $advanced = $request->advanced;
        $isAdvanced = true;
        $count = 0;
        $data = SuuTraModel::query()->leftjoin('users', 'users.id', '=', 'suutranb.ccv')
            ->leftjoin('chinhanh', 'cn_id', '=', 'suutranb.vp')
            ->select($array);

        $keyNC = '"' . $getNangCao . '"';
        $keyCB = '"' . $getcoban . '"';
        $searchParam = $request->searchParam;
        if ($getcoban && $getNangCao) {
            $data = SuuTraModel::searchByQuery(
                [
                    'bool' => [
                        'should' => [
                            0 => [
                                'match' => [
                                    'duong_su' => [
                                        'query' => $getcoban,
                                        'analyzer' => 'my_analyzer',
                                    ],
                                ],
                            ],
                            1 => [
                                'match' => [
                                    'texte' => [
                                        'query' => $getNangCao,
                                        'analyzer' => 'my_analyzer',
                                    ],
                                ],
                            ],

                        ],
                    ]
                ],
                '',
                '',
                '20',
                ''

            )->paginate(20)->onEachSide(2);;
        } elseif ($getcoban) {
            $data = SuuTraModel::searchByQuery(
                [
                    'bool' => [
                        'should' => [
                            0 => [
                                'match' => [
                                    'duong_su' => [
                                        'query' => $getcoban,
                                        'analyzer' => 'my_analyzer',
                                    ],
                                ],
                            ],


                        ],
                    ]
                ],
                '',
                '',
                '20',
                ''

            )->paginate(20)->onEachSide(2);;
        } elseif ($getNangCao) {
            $data = SuuTraModel::searchByQuery(
                [
                    'bool' => [
                        'must' => [

                            0 => [
                                'match' => [
                                    'texte' => [
                                        'query' => $getNangCao,
                                        'analyzer' => 'my_analyzer',
                                    ],
                                ],
                            ],

                        ],
                    ]
                ],
                '',
                '',
                '20',
                ''

            )->paginate(20)->onEachSide(2);;
        } else {
            $data = $data->paginate(20)->onEachSide(2);;
        }
        $count = $data->count();


        $loadTaiSan = false;
        $str_json2 = json_encode(array_filter(array(str_replace('"', "", $getcoban))));
        $str_json = json_encode(array_filter(array(str_replace('"', "", $getNangCao))));
        $exactly = $request->exactly;
        //$count=$data->total();
        return view('admin.suutra.index', compact('searchParam', 'isAdvanced', 'exactly', 'loadTaiSan', 'str_json2', 'count', 'data', 'getcoban', 'getNangCao', 'str_json', 'priority'));
    }

    public static function checkEdit($createdDate)
    {
        $now = Carbon::now();
        $allowEdit = Carbon::parse($createdDate)->addHours(1);
        $allow = $allowEdit->greaterThan($now);
        if ($allow) {
            return true;
        } else {
            return false;
        }
    }


    public function listKieuVanBan(Request $request)
    {
        return VanBanModel::where('vb_kieuhd', $request->id ?? '')->where('vb_nhan', 'like', 'M%')->get();
    }

    public function listVanban(Request $request)
    {
        $id = $request->get('id');
        $id_vp = User::join('nhanvien', 'nhanvien.nv_id', '=', 'id')
            ->join('chinhanh', 'chinhanh.cn_id', '=', 'nhanvien.nv_vanphong')
            ->where('users.id', Sentinel::getUser()->id)->first()->cn_id;

        $kieuhd = VanBanModel::where('vb_kieuhd', $id)
            ->where('id_vp', 2020)
            ->get();
        return $kieuhd;
    }

    public function listKhachHang(Request $request)
    {
        $where = [];
        if ($request->tk_khachhang) {
            $where[] = ['first_name', 'LIKE', '%' . $request->tk_khachhang . '%'];
        }
        $role_kh = Sentinel::findRoleBySlug('khach-hang');
        $khachhang = $role_kh->users()->select('id as kh_id', 'first_name', 'phone', 'address', 'k_id')
            ->where($where)
            ->whereNull('deleted_at')
            ->orderby('id', 'desc')->get();
        return $khachhang;
    }

    public function get_tieumuc_kieu(Request $request, $id)
    {
        $kieu = $id;
        if ($request->kieu) {
            $kieu = $request->kieu;
        }
        $tm = KieuModel::select('k_tieumuc')->where('k_id', $kieu)->first();

        $tm_arr = $tm != null ? explode(' ', $tm->k_tieumuc) : [];
        return $tm_arr;
    }

    public function list_tieumuc_form($tm_arr, $k_id)
    {
        $tieumuc = TieuMucModel::select('tieumuc.tm_id', 'tm_nhan', 'tm_loai', 'tm_keywords', 'tm_batbuoc')
            ->leftjoin('tieumuc_sapxep', 'tieumuc_sapxep.tm_id', '=', 'tieumuc.tm_id')
            ->whereIn('tieumuc.tm_id', $tm_arr)
            ->where('k_id', $k_id)
            ->orderBy('tm_sort', 'asc');
        return $tieumuc;
    }

    public function createSuutra(Request $request)
    {
        // dd(1);
        $id_vp = User::join('nhanvien', 'nhanvien.nv_id', '=', 'id')
            ->join('chinhanh', 'chinhanh.cn_id', '=', 'nhanvien.nv_vanphong')
            ->where('users.id', Sentinel::getUser()->id)->first()->cn_id;
        $kieuhd = Kieuhopdong::where('id_vp', 2020)->pluck('kieu_hd', 'lien_ket_id')->prepend(
            '---  Chọn kiểu hợp đồng  ---',
            ''
        );
        $ccv = User::join('role_users', 'role_users.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_users.role_id')
            ->join('nhanvien', 'nhanvien.nv_id', '=', 'users.id')
            ->where('roles.slug', '=', 'cong-chung-vien')
            ->where('nhanvien.nv_vanphong', '=', $id_vp)
            ->pluck('users.first_name', 'users.id')->prepend('---  Chọn công chứng viên  ---', '');
        $cv = User::join('role_users', 'role_users.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_users.role_id')
            ->join('nhanvien', 'nhanvien.nv_id', '=', 'users.id')
            ->where('roles.slug', '=', 'chuyen-vien')
            ->where('nhanvien.nv_vanphong', '=', $id_vp)
            ->where('nhanvien.is_active',null)
            ->pluck('users.first_name', 'users.id')->prepend('---  Chọn chuyên viên  ---', '');
        $bank = DB::table('bank')->pluck('name', 'id')->prepend(
            '---  Chọn ngân hàng  ---',
            ''
        );
        $id_keyDS = KieuModel::where('k_keywords', '=', 'duong-su')->first()->k_id;
        $kieuDS = KieuModel::where('k_parent', $id_keyDS)->get();
        return view('admin.suutra.createSuutra', compact('kieuhd', 'ccv', 'kieuDS', 'cv', 'bank'));
    }
    public function createSuutraSTP(Request $request)
    {
        // dd(1);
        $id_vp = User::join('nhanvien', 'nhanvien.nv_id', '=', 'id')
            ->join('chinhanh', 'chinhanh.cn_id', '=', 'nhanvien.nv_vanphong')
            ->where('users.id', Sentinel::getUser()->id)->first()->cn_id;
        $kieuhd = Kieuhopdong::where('id_vp', 2020)->pluck('kieu_hd', 'lien_ket_id')->prepend(
            '---  Chọn kiểu hợp đồng  ---',
            ''
        );
        $ccv = User::join('role_users', 'role_users.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_users.role_id')
            ->join('nhanvien', 'nhanvien.nv_id', '=', 'users.id')
            ->where('roles.slug', '=', 'cong-chung-vien')
            ->where('nhanvien.nv_vanphong', '=', $id_vp)
            ->pluck('users.first_name', 'users.id')->prepend('---  Chọn công chứng viên  ---', '');
        $id_keyDS = KieuModel::where('k_keywords', '=', 'duong-su')->first()->k_id;
        $kieuDS = KieuModel::where('k_parent', $id_keyDS)->get();
        return view('admin.suutra.createSuutraSTP', compact('kieuhd', 'ccv', 'kieuDS'));
    }

    public function edit($id)
    {
        $data = SuuTraModel::find($id);
        $duongsu = explode(',/.', $data->duong_su);
        $data['duongsua'] = $duongsu[0] ?? null;
        $data['duongsub'] = $duongsu[1] ?? null;
        $data['duongsuc'] = $duongsu[2] ?? null;
        $texte = explode(',/.', $data->texte);
        $data['noidung'] = $texte[0] ?? null;
        $data['taisan'] = $texte[1] ?? null;
        $id_vp = User::join('nhanvien', 'nhanvien.nv_id', '=', 'id')
            ->join('chinhanh', 'chinhanh.cn_id', '=', 'nhanvien.nv_vanphong')
            ->where('users.id', Sentinel::getUser()->id)->first()->cn_id;
        $kieuhd = Kieuhopdong::where('id_vp', 2020)->pluck('kieu_hd', 'lien_ket_id')->prepend(
            '---  Chọn kiểu hợp đồng  ---',
            ''
        );
        $vanban = VanBanModel::where('vb_kieuhd', $data->loai)->where('id_vp', 2020)->pluck('vb_nhan', 'vb_id')->prepend(
            '---  Chọn tên văn bản  ---',
            ''
        );
        $ccv = User::join('role_users', 'role_users.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_users.role_id')
            ->join('nhanvien', 'nhanvien.nv_id', '=', 'users.id')
            ->where('roles.slug', '=', 'cong-chung-vien')
            ->where('nhanvien.nv_vanphong', '=', $id_vp)
            ->pluck('users.first_name', 'users.id')->prepend('---  Chọn công chứng viên  ---', '');
        $cv = User::join('role_users', 'role_users.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_users.role_id')
            ->join('nhanvien', 'nhanvien.nv_id', '=', 'users.id')
            ->where('roles.slug', '=', 'chuyen-vien')
            ->where('nhanvien.nv_vanphong', '=', $id_vp)
            ->pluck('users.first_name', 'users.id')->prepend('---  Chọn chuyên viên  ---', '');
        //        dd($vanban);

        $bank = DB::table('bank')->pluck('name', 'id')->prepend(
            '---  Chọn ngân hàng  ---',
            ''
        );
        $id_keyDS = KieuModel::where('k_keywords', '=', 'duong-su')->first()->k_id;
        $kieuDS = KieuModel::where('k_parent', $id_keyDS)->get();
        return view('admin.suutra.editSuutra', compact('data', 'bank', 'kieuhd', 'vanban', 'ccv', 'cv', 'kieuDS'));
    }
    public function createAppendix($id, Request $request)
    {

        $data = SuuTraModel::find($id);
        $kieu = $request->kieu ?? $data->loai;
        $duongsu = explode(',/.', $data->duong_su);
        $data['duongsua'] = $duongsu[0] ?? null;
        $data['duongsub'] = $duongsu[1] ?? null;
        $data['duongsuc'] = $duongsu[2] ?? null;
        $texte = explode(',/.', $data->texte);
        $data['noidung'] = $texte[0] ?? null;
        $data['taisan'] = $texte[1] ?? null;
        $id_vp = User::join('nhanvien', 'nhanvien.nv_id', '=', 'id')
            ->join('chinhanh', 'chinhanh.cn_id', '=', 'nhanvien.nv_vanphong')
            ->where('users.id', Sentinel::getUser()->id)->first()->cn_id;
        $kieuhd = Kieuhopdong::where('id_vp', 2020)->pluck('kieu_hd', 'lien_ket_id')->prepend(
            '---  Chọn kiểu hợp đồng  ---',
            ''
        );
        $vanban = VanBanModel::where('vb_kieuhd', $kieu)->where('id_vp', 2020)->pluck('vb_nhan', 'vb_id')->prepend(
            '---  Chọn tên văn bản  ---',
            ''
        );
        $ccv = User::join('role_users', 'role_users.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_users.role_id')
            ->join('nhanvien', 'nhanvien.nv_id', '=', 'users.id')
            ->where('roles.slug', '=', 'cong-chung-vien')
            ->where('nhanvien.nv_vanphong', '=', $id_vp)
            ->pluck('users.first_name', 'users.id')->prepend('---  Chọn công chứng viên  ---', '');
        //        dd($vanban);
        $cv = User::join('role_users', 'role_users.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_users.role_id')
            ->join('nhanvien', 'nhanvien.nv_id', '=', 'users.id')
            ->where('roles.slug', '=', 'chuyen-vien')
            ->where('nhanvien.nv_vanphong', '=', $id_vp)
            ->pluck('users.first_name', 'users.id')->prepend('---  Chọn chuyên viên  ---', '');
        $bank = DB::table('bank')->pluck('name', 'id')->prepend(
            '---  Chọn ngân hàng  ---',
            ''
        );

        $id_keyDS = KieuModel::where('k_keywords', '=', 'duong-su')->first()->k_id;
        $kieuDS = KieuModel::where('k_parent', $id_keyDS)->get();
        return view('admin.suutra.createAppendix', compact('data', 'bank', 'cv', 'kieu', 'kieuhd', 'vanban', 'ccv', 'kieuDS'));
    }
    public function doCancelEdit($id)
    {
        $data = SuuTraModel::find($id);
        // dd($data);
        if (!$data) {
            
    abort(404, 'Không tìm thấy hồ sơ');
}
        $duongsu = explode(',/.', $data->duong_su);
        $data['duongsua'] = $duongsu[0] ?? null;
        $data['duongsub'] = $duongsu[1] ?? null;
        $data['duongsuc'] = $duongsu[2] ?? null;
        $texte = explode(',/.', $data->texte);
        $data['noidung'] = $texte[0] ?? null;
        $data['taisan'] = $texte[1] ?? null;
        $id_vp = User::join('nhanvien', 'nhanvien.nv_id', '=', 'id')
            ->join('chinhanh', 'chinhanh.cn_id', '=', 'nhanvien.nv_vanphong')
            ->where('users.id', Sentinel::getUser()->id)->first()->cn_id;
        $kieuhd = Kieuhopdong::where('id_vp', 2020)->pluck('kieu_hd', 'lien_ket_id')->prepend(
            '---  Chọn kiểu hợp đồng  ---',
            ''
        );
        $vanban = VanBanModel::where('id_vp', 2020)->pluck('vb_nhan', 'vb_id')->prepend(
            '---  Chọn tên văn bản  ---',
            ''
        );
        $ccv = User::join('role_users', 'role_users.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_users.role_id')
            ->join('nhanvien', 'nhanvien.nv_id', '=', 'users.id')
            ->where('roles.slug', '=', 'cong-chung-vien')
            ->where('nhanvien.nv_vanphong', '=', $id_vp)
            ->pluck('users.first_name', 'users.id')->prepend('---  Chọn công chứng viên  ---', '');
        //        dd($vanban);
        $cv = User::join('role_users', 'role_users.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_users.role_id')
            ->join('nhanvien', 'nhanvien.nv_id', '=', 'users.id')
            ->where('roles.slug', '=', 'chuyen-vien')
            ->where('nhanvien.nv_vanphong', '=', $id_vp)
            ->pluck('users.first_name', 'users.id')->prepend('---  Chọn chuyên viên  ---', '');
        $bank = DB::table('bank')->pluck('name', 'id')->prepend(
            '---  Chọn ngân hàng  ---',
            ''
        );
        $id_keyDS = KieuModel::where('k_keywords', '=', 'duong-su')->first()->k_id;
        $kieuDS = KieuModel::where('k_parent', $id_keyDS)->get();
        return view('admin.suutra.doCancelEditSuutra', compact('cv', 'bank', 'data', 'kieuhd', 'vanban', 'ccv', 'kieuDS'));
    }
    public function giaiChapSuutra($id)
    {
        $data = SuuTraModel::find($id);
        $duongsu = explode(',/.', $data->duong_su);
        $data['duongsua'] = $duongsu[0] ?? null;
        $data['duongsub'] = $duongsu[1] ?? null;
        $data['duongsuc'] = $duongsu[2] ?? null;
        $texte = explode(',/.', $data->texte);
        $data['noidung'] = $texte[0] ?? null;
        $data['taisan'] = $texte[1] ?? null;
        $id_vp = User::join('nhanvien', 'nhanvien.nv_id', '=', 'id')
            ->join('chinhanh', 'chinhanh.cn_id', '=', 'nhanvien.nv_vanphong')
            ->where('users.id', Sentinel::getUser()->id)->first()->cn_id;
        $kieuhd = Kieuhopdong::where('id_vp', 2020)->pluck('kieu_hd', 'lien_ket_id')->prepend(
            '---  Chọn kiểu hợp đồng  ---',
            ''
        );
        $vanban = VanBanModel::where('id_vp', 2020)->pluck('vb_nhan', 'vb_id')->prepend(
            '---  Chọn tên văn bản  ---',
            ''
        );
        $ccv = User::join('role_users', 'role_users.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_users.role_id')
            ->join('nhanvien', 'nhanvien.nv_id', '=', 'users.id')
            ->where('roles.slug', '=', 'cong-chung-vien')
            ->where('nhanvien.nv_vanphong', '=', $id_vp)
            ->pluck('users.first_name', 'users.id')->prepend('---  Chọn công chứng viên  ---', '');
        $cv = User::join('role_users', 'role_users.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_users.role_id')
            ->join('nhanvien', 'nhanvien.nv_id', '=', 'users.id')
            ->where('roles.slug', '=', 'chuyen-vien')
            ->where('nhanvien.nv_vanphong', '=', $id_vp)
            ->pluck('users.first_name', 'users.id')->prepend('---  Chọn chuyên viên  ---', '');
        $bank = DB::table('bank')->pluck('name', 'id')->prepend(
            '---  Chọn ngân hàng  ---',
            ''
        );
        //        dd($vanban);
        $id_keyDS = KieuModel::where('k_keywords', '=', 'duong-su')->first()->k_id;
        $kieuDS = KieuModel::where('k_parent', $id_keyDS)->get();
        return view('admin.suutra.giaiChapSuutra', compact('data', 'cv', 'bank', 'kieuhd', 'vanban', 'ccv', 'kieuDS'));
    }
    public function editSTP($id)
    {
        $data = SuuTraModel::find($id);
        //dd($data);
        $duongsu = explode(',/.', $data->duong_su);
        $data['duongsua'] = $duongsu[0] ?? null;
        $data['duongsub'] = $duongsu[1] ?? null;
        $data['duongsuc'] = $duongsu[2] ?? null;
        $texte = explode(',/.', $data->texte);
        $data['noidung'] = $texte[0] ?? null;
        $data['taisan'] = $texte[1] ?? null;
        $id_vp = User::join('nhanvien', 'nhanvien.nv_id', '=', 'id')
            ->join('chinhanh', 'chinhanh.cn_id', '=', 'nhanvien.nv_vanphong')
            ->where('users.id', Sentinel::getUser()->id)->first()->cn_id;
        $kieuhd = Kieuhopdong::where('id_vp', $id_vp)->pluck('kieu_hd', 'id')->prepend(
            '---  Chọn kiểu hợp đồng  ---',
            ''
        );
        $vanban = VanBanModel::where('id_vp', $id_vp)->pluck('vb_nhan', 'vb_id')->prepend(
            '---  Chọn tên văn bản  ---',
            ''
        );
        $ccv = User::join('role_users', 'role_users.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_users.role_id')
            ->join('nhanvien', 'nhanvien.nv_id', '=', 'users.id')
            ->where('roles.slug', '=', 'cong-chung-vien')
            ->where('nhanvien.nv_vanphong', '=', $id_vp)
            ->pluck('users.first_name', 'users.id')->prepend('---  Chọn công chứng viên  ---', '');
        //        dd($vanban);
        $id_keyDS = KieuModel::where('k_keywords', '=', 'duong-su')->first()->k_id;
        $kieuDS = KieuModel::where('k_parent', $id_keyDS)->get();
        return view('admin.suutra.editSuutraSTP', compact('data', 'kieuhd', 'vanban', 'ccv', 'kieuDS'));
    }

    public function themduongsua(Request $request)
    {
        $data = User::where('id', $request->user_id)->get();
        return $data;
    }


    public static function cleanSpaces($string)
    {
        while (substr($string, 0, 1) == " ") {
            $string = substr($string, 1);
            SuuTraController::cleanSpaces($string);
        }
        while (substr($string, -1) == " ") {
            $string = substr($string, 0, -1);
            SuuTraController::cleanSpaces($string);
        }
        return $string;
    }

    public function store(Request $request)
    {
        $data = $request->toArray();
        $time = date("Y", strtotime($request->ngay_cc));
        $sohd = $request->so_hd . '/' . $time;


        $vp = NhanVienModel::find($request->id_ccv)->nv_vanphong;
        $data = AppController::convert_unicode($data);
        $request->replace($data->toArray());

        $now = Carbon::now()->format('Y-m-d');
        $role = Sentinel::check()->user_roles()->first()->slug;
        $pic = null;
        $file_name = '';
        $file_path = '';
  
        $ma_dong_bo = ChiNhanhModel::find($vp)->code_cn;
        $ten_vanphong = ChiNhanhModel::find($vp)->cn_ten;
        $ten_ccv = NhanVienModel::find($request->id_ccv)->nv_hoten;
        $id_lienket = NhanVienModel::find($request->id_ccv)->id_lienket;

        //        $texte = SuuTraController::cleanSpaces($request->noidung) . ',/.' . SuuTraController::cleanSpaces($request->taisan);
        //        $texte_en = $this->convert_vi_to_en($texte);
        //        $duong_su = SuuTraController::cleanSpaces($request->duongsua) . ',/.' . SuuTraController::cleanSpaces($request->duongsub) . ',/.' . SuuTraController::cleanSpaces($request->duongsuc);
        //        $duong_su_en = $this->convert_vi_to_en($duong_su);

        $texte = SuuTraController::cleanSpaces($request->noidung);
        $texte_en = $this->convert_vi_to_en($texte);
        ///
        $duong_su_index = "";
        $duong_su_draw = preg_replace('/[^\p{L}\p{N}\s]/u', '', $request->duongsu);
        $duong_su_draw = str_replace("\n", ' ', $duong_su_draw);
        $duong_su_draw = explode(' ', SuuTraController::convert_vi_to_en($duong_su_draw));

        $texte_reverse = strrev($texte_en);
        $texte_reverse = mb_convert_encoding($texte_reverse, 'UTF-8');
        $texte_reverse = preg_replace('/[^\p{L}\p{N}\s]/u', '', $texte_reverse);
        $texte_reverse = str_replace("\n", ' ', $texte_reverse);
        $texteArray = explode('/.', $texte);
        $property_info = '';
        $transaction_content = '';
        if (count(collect($texteArray)) > 1) {
            $property_info = $texteArray[0];
            $transaction_content = $texteArray[1];
        }

        foreach ($duong_su_draw as $item) {
            switch (strlen($item)) {
                case 3:
                    $duong_su_index .= $item . " ";
                    break;
                case 9:
                    $sub1 = substr($item, 0, 3);
                    $sub2 = substr($item, 3, 3);
                    $sub3 = substr($item, 6, 3);
                    $duong_su_index .= $sub1 . " " . $sub2 . " " . $sub3 . " ";
                    break;
                case 12:
                    $sub1 = substr($item, 0, 3);
                    $sub2 = substr($item, 3, 3);
                    $sub3 = substr($item, 6, 3);
                    $sub4 = substr($item, 9, 3);

                    $duong_su_index .= $sub1 . " " . $sub2 . " " . $sub3 . " " . $sub4 . " ";
                    break;
                default:
                    break;
            }
        }
        $duong_su_index = mb_convert_encoding($duong_su_index, 'UTF-8');

        //
        $duong_su = SuuTraController::cleanSpaces($request->duongsu);
        $duong_su_en = $this->convert_vi_to_en($duong_su);

        $ten_ccv_master = NhanVienModel::find($request->id_ccv)->nv_hoten;
        $ten_vp_master = ChiNhanhModel::find($vp)->cn_ten;
        $cn_code = ChiNhanhModel::find($vp)->cn_code;
        $vpcc = \App\Models\NhanVienModel::find(Sentinel::getUser()->id)->nv_vanphong;
        $realName = "";
        if ($role == 'phong-khac') {
            //bắt trùng
            if (!Sentinel::inRole('admin') && SuuTraModel::where('so_hd', $sohd)->whereNull('deleted_note')->first() != null && SuuTraModel::where(
                'so_hd',
                $sohd
            )->first()->vp_master == $ten_vp_master) {
                return back()->with('error', 'Mã hợp đồng đã tồn tại');
            } else {
                if ($request->hasFile('pic')) {
                    $picn = $request->so_hd . '.png';
                    $pic = json_encode($this->addImage($request, 'images/suutra', 'pic'));
                    $realName = json_encode($this->getRealName($request, 'images/suutra', 'pic'));
                }
                if ($request->hasFile('release_file_name')) {
                    //$picn = $request->so_hd . '.png';
                    $file_path = json_encode($this->addImage($request, 'images/suutra', 'release_file_name'));
                    $file_name = json_encode($this->getRealName($request, 'images/suutra', 'release_file_name'));
                }
                if (strstr($request->ten, "chấm dứt") || strstr($request->ten, "hủy bỏ")) {
                    $type_cancel = 1;
                } elseif (strstr($request->ten, "Phụ lục")) {
                    $type_cancel = 2;
                } else {
                    $type_cancel = 0;
                }
                $cancel_status = 0;
                $cancel_description = null;
                $cancel_description_other = null;
                if ($request->get('description')) {
                    $cancel_status = 0;
                    if ($type_cancel == 1) {
                        $cancel_status = 1;
                        $cancel_description = "Hủy hợp đồng số " . $request->get('description');
                    } elseif ($type_cancel == 2) {
                        $cancel_description = "Hợp đồng phụ lục của hợp đồng số " . $request->get('description');
                    } else {
                        $cancel_description = "Sửa đổi, bổ sung của hợp đồng số " . $request->get('description');
                    }
                }
                $vanban = explode('.$.', $request->ten);
                $suutra = SuuTraModel::create([
                    'ccv' => $request->id_ccv,
                    'ngay_cc' => $request->ngay_cc,
                    'so_hd' => $sohd,
                    'ten_hd' => $vanban[1] ?? null ?? $request->ten,
                    'texte' => $texte,
                    'texte_en' => $texte_en,
                    'duong_su' => $duong_su,
                    'duong_su_en' => $duong_su_en,
                    'ngan_chan' => $request->loai ?? 0,
                    'vp' => $vp,
                    'ccv_master' => $request->ccv_master,
                    'vp_master' => $ten_vp_master,
                    'ngay_nhap' => $now,
                    'picture' => $pic,
                    'chu_y' => $request->chu_y,
                    'status' => 1,
                    'ma_phan_biet' => 'D',
                    'cancel_status' => $cancel_status,
                    'cancel_description' => $cancel_description,
                    'nguoinhap' => Sentinel::getUser()->id,
                    'vanban' => $vanban[0] ?? null,
                    'type_cancel' => $type_cancel,
                    'so_cc_cu' => $request->get('description'),
                    'thu_lao' => str_replace(',', '', $request->thu_lao),
                    'phi_cong_chung' => str_replace(',', '', $request->phi_cong_chung),
                    'real_name' => $realName,
                    'duong_su_index' => $duong_su_index,
                    'sync_code' => $ma_dong_bo,
                    'texte_reverse' => $texte_reverse,
                    'duong_su_index' => $duong_su_index,
                    'property_info' => $property_info,
                    'transaction_content' => $transaction_content,
                    'contract_period' => $request->contract_period,
                    'merged' => 1,
                    'merge_content' => $duong_su_en  . ' ' . $texte_en,
                    'trans_val' => str_replace('.00', '', str_replace(',', '', $request->trans_val)) ?? '',
                ]);
                      //06092023
            $this->delete_solr($suutra->st_id);
                $this->insert_solr(SuutraModel::where('st_id', $suutra->st_id)->first(), 1);

                $suutra_log = SuuTraLogModel::create([
                    'suutra_id' => $suutra->st_id,
                    'log_content' => json_encode($suutra),
                    'user_id' => Sentinel::getUser()->id,
                    'so_hd' => $suutra->so_hd,
                    'flag_des'=> 1
                ]);
                $update = SuutraModel::where('st_id', $suutra->st_id)->first()->update([
                    'ma_dong_bo' => $ma_dong_bo . "_J_" . $suutra->st_id
                ]);
                //                $suutra->addIndex();
                return redirect(route('searchSolr'))->with('success', 'Thêm ngăn chặn thành công !');
            }
        }
        if ($request->loai == SuuTraModel::PREVENT) {
            if (!Sentinel::inRole('admin') && $vpcc !== "2190"  && SuuTraModel::where('so_hd', $sohd)->first() != null && SuuTraModel::where(
                'so_hd',
                $sohd
            )->first()->vp == $vp) {
                return back()->with('error', 'Mã hợp đồng đã tồn tại');
            } else {
                if ($request->hasFile('pic')) {
                    $picn = $request->so_hd . '.png';
                    $pic = json_encode($this->addImage($request, 'images/suutra', 'pic'));
                    $realName = json_encode($this->getRealName($request, 'images/suutra', 'pic'));
                }
                if ($request->hasFile('release_file_name')) {
                    //$picn = $request->so_hd . '.png';
                    $file_path = json_encode($this->addImage($request, 'images/suutra', 'release_file_name'));
                    $file_name = json_encode($this->getRealName($request, 'images/suutra', 'release_file_name'));
                }
                if (strstr($request->ten, "chấm dứt") || strstr($request->ten, "hủy bỏ")) {
                    $type_cancel = 1;
                } elseif (strstr($request->ten, "phụ lục")) {
                    $type_cancel = 2;
                } else {
                    $type_cancel = 0;
                }
                $cancel_status = null;
                $cancel_description = null;
                if ($request->get('description')) {
                    $cancel_status = 0;
                    if ($type_cancel == 1) {
                        $cancel_status = 1;
                        $cancel_description = "Hợp đồng hủy của hợp đồng số " . $request->get('description');
                    } elseif ($type_cancel == 2) {
                        $cancel_description = "Hợp đồng phụ lục của hợp đồng số " . $request->get('description');
                    } else {
                        $cancel_description = "Hợp đồng sửa đổi, bổ sung của hợp đồng số " . $request->get('description');
                    }
                }
                $vanban = explode('.$.', $request->ten);
                $suutra = SuuTraModel::create([
                    'ccv' => $request->id_ccv,
                    'ngay_cc' => $request->ngay_cc,
                    'so_hd' => $sohd,
                    //'ten_hd' => $vanban[1] ?? null,
                    'texte' => $texte,
                    'texte_en' => $texte_en,
                    'duong_su' => $duong_su,
                    'duong_su_en' => $duong_su_en,
                    'ngan_chan' => $request->loai ?? 0,
                    'vp' => $vp,
                    'ccv_master' => $request->ccv_master,
                    'vp_master' => $ten_vp_master,
                    'ngay_nhap' => $now,
                    'picture' => $pic,
                    'chu_y' => $request->chu_y,
                    'ma_phan_biet' => 'D',
                    'cancel_status' => $cancel_status,
                    'cancel_description' => $cancel_description,
                    'nguoinhap' => Sentinel::getUser()->id,
                    'ten_hd' => $vanban[0] ?? null,
                    'type_cancel' => $type_cancel,
                    'so_cc_cu' => $request->get('description'),
                    'thu_lao' => str_replace(',', '', $request->thu_lao),
                    'phi_cong_chung' => str_replace(',', '', $request->phi_cong_chung),
                    'complete' => 2,
                    'real_name' => $realName,
                    'duong_su_index' => $duong_su_index,
                    'sync_code' => $ma_dong_bo,
                    'release_file_name' => $file_name,
                    'release_file_path' => $file_name,
                    'texte_reverse' => $texte_reverse,
                    'property_info' => $property_info,
                    'transaction_content' => $transaction_content,
                    'duong_su_index' => $duong_su_index,
                    'contract_period' => $request->contract_period,
                    'prevent_doc_receive_date' => $request->prevent_doc_receive_date,
                    'merged' => 1,
                    'merge_content' => $duong_su_en  . ' ' . $texte_en,
                    'trans_val' => str_replace('.00', '', str_replace(',', '', $request->trans_val)) ?? '',

                ]);
                $ar_solr = [
                    'ccv' => $request->id_ccv,
                    'ngay_cc' => $request->ngay_cc,
                    'so_hd' => $sohd,
                    //'ten_hd' => $vanban[1] ?? null,
                    'texte' => $texte,
                    'texte_en' => $texte_en,
                    'duong_su' => $duong_su,
                    'duong_su_en' => $duong_su_en,
                    'ngan_chan' => $request->loai ?? 0,
                    'vp' => $vp,
                    'ccv_master' => $request->ccv_master,
                    'vp_master' => $ten_vp_master,
                    'ngay_nhap' => $now,
                    'picture' => $pic,
                    'chu_y' => $request->chu_y,
                    'ma_phan_biet' => 'D',
                    'cancel_status' => $cancel_status,
                    'cancel_description' => $cancel_description,
                    'nguoinhap' => Sentinel::getUser()->id,
                    'ten_hd' => $vanban[0] ?? null,
                    'type_cancel' => $type_cancel,
                    'so_cc_cu' => $request->get('description'),
                    'thu_lao' => str_replace(',', '', $request->thu_lao),
                    'phi_cong_chung' => str_replace(',', '', $request->phi_cong_chung),
                    'complete' => 2,
                    'real_name' => $realName,
                    'duong_su_index' => $duong_su_index,
                    'sync_code' => $ma_dong_bo,
                    'release_file_name' => $file_name,
                    'release_file_path' => $file_name,
                    'texte_reverse' => $texte_reverse,
                    'property_info' => $property_info,
                    'transaction_content' => $transaction_content,
                    'duong_su_index' => $duong_su_index,
                    'contract_period' => $request->contract_period,
                    'prevent_doc_receive_date' => $request->prevent_doc_receive_date,
                    'merged' => 1,
                    'merge_content' => $duong_su_en  . ' ' . $texte_en,
                ];
                      //06092023
            $this->delete_solr($suutra->st_id);
                $this->insert_solr(SuutraModel::where('st_id', $suutra->st_id)->first(), 2);

                $id = $suutra->st_id;

                $suutra_log = SuuTraLogModel::create([
                    'suutra_id' => $suutra->st_id,
                    'log_content' => json_encode($suutra),
                    'user_id' => Sentinel::getUser()->id,
                    'so_hd' => $suutra->so_hd,
                    'execute_content' => "Đăng ký",
                    'flag_des'=> 2
                ]);
                $ngay_cc = Carbon::parse($request->ngay_cc)->format('Y-m-d 00:00:000');
                //                $stp = Curl::to('http://127.0.0.1:8000/api/push-data-ngan-chan-stp')
                //                $stp = Curl::to('http://dev-k.dotary.net/api/push-data-ngan-chan-stp')
                //    ->withData([
                //       'property_info' => $texte, // noi dungs
                //       'owner_info' => $duong_su, // duong su
                //       'synchronize_id' => $ma_dong_bo . $id . '_D', // ma dong bo
                //   'prevent_person_info' => $ten_vanphong, // văn phòng
                //   'prevent_doc_number' => $sohd, //so hp
                //   'prevent_doc_date' => $ngay_cc, //ngay ngan chan
                //   'prevent_doc_summary' => $request->get('ten'), //ngay ngan chan
                //   'entry_user_name' => $ten_ccv,
                //	'update_user_name' => $ten_ccv,
                //	'user_entry_id'=>$id_lienket
                //  ])->post();
                $update = SuutraModel::where('st_id', $suutra->st_id)->first()->update([
                    'ma_dong_bo' => $ma_dong_bo . "_J_" . $suutra->st_id
                ]);
                if ($request->loai == "3" && $request->important == "1") {
                    $user = Sentinel::getUser();
                    $thong_bao_chung = new ThongBaoChung;
                    $thong_bao_chung->tieu_de = "Ngăn chặn số " . " " . $sohd . " " . " của " . ' ' . $request->ccv_master . "" . " liên quan đến " . " " . $duong_su;
                    $thong_bao_chung->noi_dung =  $texte;
                    $thong_bao_chung->nv_id = $user->id ?? 0;
                    $thong_bao_chung->vp_id = $vp ?? 0;
                    $thong_bao_chung->type = 1;
                    $thong_bao_chung->push = 3;
                    $thong_bao_chung->pic = $pic;
                    $thong_bao_chung->realname = $realName;
                    $thong_bao_chung->file = $pic;
                    $thong_bao_chung->created_indirect = 1;
                    $thong_bao_chung->save();
                }
                return redirect(route('searchSolr'))->with('success', 'Thêm ngăn chặn thành công !');
            }
        }
        // nhap giao dich thuong
        if (!Sentinel::inRole('admin') && SuuTraModel::where('so_hd', $sohd)->whereNull('deleted_note')->first() != null && SuuTraModel::where(
            'so_hd',
            $sohd
        )->first()->vp_master == $ten_vp_master) {
            return back()->with('error', 'Mã hợp đồng đã tồn tại');
        } else {
            if ($request->hasFile('pic')) {
                $picn = $request->so_hd . '.png';
                $pic = json_encode($this->addImage($request, 'images/suutra', 'pic'));
                $realName = json_encode($this->getRealName($request, 'images/suutra', 'pic'));
            }

            $vanban = VanBanModel::find($request->ten);
            $tenVB = $vanban->vb_nhan ?? '';
            if (strstr($tenVB, "dứt")) {
                $type_cancel = 1;
            } elseif (strstr($tenVB, "hủy")) {
                $type_cancel = 1;
            } elseif (strstr($tenVB, "Hủy")) {
                $type_cancel = 1;
            } elseif (strstr($tenVB, "huỷ")) {
                $type_cancel = 1;
            } elseif (strstr($tenVB, "Huỷ")) {
                $type_cancel = 1;
            } elseif (strstr($tenVB, "bỏ")) {
                $type_cancel = 1;
            } elseif (strstr($tenVB, "Thanh lý")) {
                $type_cancel = 1;
            } elseif (strstr($tenVB, "thanh lý")) {
                $type_cancel = 1;
            } elseif (strstr($tenVB, "phụ lục")) {
                $type_cancel = 2;
            } elseif (strstr($tenVB, "Phụ lục")) {
                $type_cancel = 2;
            } else {
                $type_cancel = 0;
            }
            $cancel_status = null;
            $cancel_description = null;
            $cancel_description_other = null;
            if ($request->get('description')) {
                $cancel_status = 0;
                if ($type_cancel == 1) {
                    $cancel_status = 1;
                    $cancel_description = "Hủy hợp đồng số " . $request->get('description');
                } elseif ($type_cancel == 2) {
                    $cancel_description = "Hợp đồng phụ lục của hợp đồng số " . $request->get('description');
                } else {
                    $cancel_description = "Sửa đổi, bổ sung hợp đồng số " . $request->get('description');
                }
                if ($type_cancel == 1) {
                    $cancel_status = 1;
                    $cancel_description_other = "Đã bị hủy bởi hợp đồng số " . $sohd;
                } elseif ($type_cancel == 2) {
                    $cancel_description_other = "Hợp đồng có phụ lục là đồng số " . $sohd;
                } else {
                    $cancel_description_other = "Đã bị sửa đổi, bổ sung bởi hợp đồng số " . $sohd;
                }
            }

            $cv_name = '';
            if ($request->cv_id && $nhanvien = NhanVienModel::find($request->cv_id)) {
                $cv_name = $nhanvien->nv_hoten;
            }

            $suutra = SuuTraModel::create([
                'ccv' => $request->id_ccv,
                'ngay_cc' => $request->ngay_cc,
                'so_hd' => $sohd,
                'ten_hd' => $tenVB,
                'loai' => $request->vb_kieuhd,
                'van_ban_id' => $request->ten,
                'texte' => $texte,
                'texte_en' => $texte_en,
                'duong_su' => $duong_su,
                'duong_su_en' => $duong_su_en,
                'ngan_chan' => $request->loai ?? 0,
                'vp' => $vp,
                'ccv_master' => $ten_ccv_master,
                'vp_master' => $ten_vp_master,
                'ngay_nhap' => $now,
                'picture' => $pic,
                'chu_y' => $request->chu_y,
                'ma_phan_biet' => 'D',
                'cancel_status' => $cancel_status,
                'cancel_description' => $cancel_description,
                'nguoinhap' => Sentinel::getUser()->id,
                'vanban' => $vanban[0] ?? null,
                'type_cancel' => $type_cancel,
                'so_cc_cu' => $request->get('description'),
                'thu_lao' => str_replace(',', '', $request->thu_lao),
                'phi_cong_chung' => str_replace(',', '', $request->phi_cong_chung),
                'real_name' => $realName,
                'duong_su_index' => $duong_su_index,
                'sync_code' => $ma_dong_bo,
                'texte_reverse' => $texte_reverse,
                'property_info' => $property_info,
                'transaction_content' => $transaction_content,
                'contract_period' => $request->contract_period,
                'cv_id' => $request->cv_id,
                'cv_name' => $cv_name,
                'bank_id' => $request->bank,
                'merged' => 1,
                'merge_content' => $duong_su_en  . ' ' . $texte_en,
                'trans_val' => str_replace('.00', '', str_replace(',', '', $request->trans_val)) ?? '',

            ]);
            //06092023
            $this->delete_solr($suutra->st_id);
            
            $this->insert_solr(SuutraModel::where('st_id', $suutra->st_id)->first(), 3);
            $relatedSuutra = SuuTraModel::where('so_hd', $request->get('description'))->where('sync_code', $ma_dong_bo)->first();

            if ($relatedSuutra) {
                $relatedSuutra->update([
                    'type_cancel' => $type_cancel,
                    'cancel_description' => $relatedSuutra->cancel_description . PHP_EOL . $cancel_description_other,
                    'so_cc_cu' => $request->get('so_hd'),
                ]);
                $this->delete_solr($relatedSuutra->st_id);
                $this->insert_solr(SuutraModel::where('st_id', $relatedSuutra->st_id)->first(), 4);
            }

            $update = SuutraModel::where('st_id', $suutra->st_id)->first()->update([
                'ma_dong_bo' => $ma_dong_bo . "_J_" . $suutra->st_id
            ]);
            $property_info = explode("/.", $texte);
            $transaction_content = "";
            if (count($property_info) > 1) {
                $transaction_content = $property_info[0];
                $property_info = $property_info[1];
            } else {
                $transaction_content = $texte;
            }
            $ngay_cc = Carbon::parse($request->ngay_cc)->format('Y-m-d 00:00:000');

            // $stp = Curl::to('http://127.0.0.1:8000/api/push-data-suutra-stp')
            //                $stp = Curl::to('http://dev-k.dotary.net/api/push-data-ngan-chan-stp')
            //         ->withData([
            //             'property_info' => $property_info, // noi dungs
            //				'transaction_content'=>$transaction_content,
            //              'relation_object' => $duong_su, // duong su
            //             'synchronize_id' => $ma_dong_bo . $suutra->st_id . '_D', // ma dong bo
            //               'notary_place' => $ten_vanphong, // văn phòng
            //            'contract_number' => $sohd, //so hp
            //            'notary_date' => $ngay_cc, //ngay ngan chan
            //            'contract_name' => $vanban[1] ?? null ?? $request->ten, //ngay ngan chan
            //          'entry_user_name' => $ten_ccv,
            //			'notary_person' => $ten_ccv,
            //			'user_entry_id'=>$id_lienket
            //        ])->post();
            $suutra_log = SuuTraLogModel::create([
                'suutra_id' => $suutra->st_id,
                'log_content' => json_encode($suutra),
                'user_id' => Sentinel::getUser()->id,
                'so_hd' => $suutra->so_hd,
                'execute_content' => "Đăng ký",
                'flag_des'=> 3
            ]);
            return redirect(route('createSuutra'))->with('success', 'Thêm hồ sơ thành công !');
        }
    }

    // cap nhat lai ngan chan
    public function getIdNganChan(Request $request, $ma_dong_bo)
    {
        $data = SuuTraModel::where('ma_dong_bo', '=', $ma_dong_bo)->first();
        $data->update([
            'uchi_id_ngan_chan' => $request->id
        ]);
        $this->delete_solr($data->st_id);
        $this->insert_solr(SuutraModel::where('st_id', $data->st_id)->first(), 5);
    }


    public function duyetSuutra($id)
    {
        $data = SuuTraModel::find($id);
        $id = $data->st_id;
        $ma_dong_bo = ChiNhanhModel::find($data->vp)->code_cn;
        $ngay_cc = Carbon::parse($data->ngay_cc)->format('Y-m-d 00:00:000');
        //            $stp = Curl::to('http://127.0.0.1:8000/api/push-data-suutra-stp')
        $stp = Curl::to('http://dev-k.dotary.net/api/push-data-suutra-stp')
            ->withData([
                'synchronize_id' => $ma_dong_bo . $id . '_D',
                'type' => '01',
                'property_info' => $data->texte,
                'transaction_content' => $data->texte,
                'notary_date' => $ngay_cc,
                'notary_office_name' => $data->vp_master,
                'contract_number' => $data->sohd . '_D',
                'contract_name' => $data->ten_hd,
                'relation_object' => $data->duong_su,
                'notary_person' => $data->ccv_master,
                'notary_place' => $data->vp_master,
                'mortage_cancel_flag' => '0',
                'cancel_status' => $data->cancel_status,
                'cancel_description' => $data->cancel_description,
                'entry_user_name' => $data->ccv_master . '_D',
                'update_user_name' => $data->ccv_master . '_D',
            ])->post();
        SuuTraModel::where('st_id', $id)->update([
            'ma_dong_bo' => $ma_dong_bo . $id . '_D',
            'status' => null,
        ]);
        $this->delete_solr($id);
        $this->insert_solr(SuuTraModel::where('st_id', $id)->first(), 7);
        return redirect(route('searchSolr'))->with('success', 'Duyệt thành công !');
    }


    public function update(Request $request, $id)
    {
        $data = $request->toArray();
        $data = AppController::convert_unicode($data);
        $request->replace($data->toArray());
        $vpcc = \App\Models\NhanVienModel::find(Sentinel::getUser()->id)->nv_vanphong;

        //        dd($request->all());
        $data = SuuTraModel::find($id);
        $role = Sentinel::check()->user_roles()->first()->slug;
        $thu_lao = str_replace(',', '', $request->thu_lao);
        if ($thu_lao == '') {
            $thu_lao = abs($data->thu_lao);
        } else {
            $thu_lao = abs($thu_lao);
        }
        $phi_cong_chung = str_replace(',', '', $request->phi_cong_chung) ?? $data->phi_cong_chung;
       if ($phi_cong_chung === '' || $phi_cong_chung === null) {
    $phi_cong_chung = abs((float) str_replace([',', '.'], '', $data->phi_cong_chung));
} else {
    $phi_cong_chung = abs((float) str_replace([',', '.'], '', $phi_cong_chung));
}

        $dataOld = SuuTraModel::where("st_id", "=", $id)->first();


        ///
        $duong_su_index = "";
        $duong_su_draw = preg_replace('/[^\p{L}\p{N}\s]/u', '', $dataOld->duong_su);
        $duong_su_draw = str_replace("\n", ' ', $duong_su_draw);
        $duong_su_draw = explode(' ', SuuTraController::convert_vi_to_en($duong_su_draw));
        foreach ($duong_su_draw as $item) {
            switch (strlen($item)) {
                case 3:
                    $duong_su_index .= $item . " ";
                    break;
                case 9:
                    $sub1 = substr($item, 0, 3);
                    $sub2 = substr($item, 3, 3);
                    $sub3 = substr($item, 6, 3);
                    $duong_su_index .= $sub1 . " " . $sub2 . " " . $sub3 . " ";
                    break;
                case 12:
                    $sub1 = substr($item, 0, 3);
                    $sub2 = substr($item, 3, 3);
                    $sub3 = substr($item, 6, 3);
                    $sub4 = substr($item, 9, 3);

                    $duong_su_index .= $sub1 . " " . $sub2 . " " . $sub3 . " " . $sub4 . " ";
                    break;
                default:
                    break;
            }
        }

        //

        $realName = $dataOld->real_name;
        $file_name = $dataOld->release_file_name;
        $file_path = $dataOld->release_file_path;
        if ($role != "ke-toan") {
            $data = SuuTraModel::find($id);
            //$vp = NhanVienModel::find($data->ccv)->nv_vanphong;
            $time = Carbon::today()->format('Y');
            $picture = SuuTraModel::where('st_id', $id)->first()->picture;
            $save_path = 'images/suutra';
            if ($request->hasFile('release_file_name')) {
                //$picn = $request->so_hd . '.png';
                $file_path = json_encode($this->addImage($request, 'images/suutra', 'release_file_name'));
                $file_name = json_encode($this->getRealName($request, 'images/suutra', 'release_file_name'));
            }
            if ($request->hasFile('pic')) {
                $picn = $request->so_hd . '.png';
                $pic = json_encode($this->addImage($request, 'images/suutra', 'pic'));
                $realName = json_encode($this->getRealName($request, 'images/suutra', 'pic'));
            } else {
                $pic = $picture;
            }
            if ($request->loai == 1) {
                $cancel = '1';
            } else {
                $cancel = '0';
            }
            $sohd = $request->so_hd;
            $texte = SuuTraController::cleanSpaces($request->noidung);
            $texte_en = $this->convert_vi_to_en($texte);
            $duong_su = SuuTraController::cleanSpaces($request->duongsu);
            $duong_su_en = $this->convert_vi_to_en($duong_su);

            //            $texte = SuuTraController::cleanSpaces($request->noidung) . ',/.' . SuuTraController::cleanSpaces($request->taisan);
            //            $texte_en = $this->convert_vi_to_en($texte);
            //            $duong_su = SuuTraController::cleanSpaces($request->duongsua) . ',/.' . SuuTraController::cleanSpaces($request->duongsub) . ',/.' . SuuTraController::cleanSpaces($request->duongsuc);
            //            $duong_su_en = $this->convert_vi_to_en($duong_su);

            //$ten_vp_master = ChiNhanhModel::find($vp)->cn_ten;
            $suutra = SuuTraModel::where('st_id', $id)->first();

            $ten_ccv_master = $suutra->ccv_master;
            $suutra_log = SuuTraLogModel::create([
                'suutra_id' => $id,
                'log_content' => json_encode($suutra),
                'user_id' => Sentinel::getUser()->id,
                'so_hd' => $suutra->so_hd,
                'flag_des'=> 4
            ]);
            $texte_reverse = strrev($texte_en);
            $texte_reverse = mb_convert_encoding($texte_reverse, 'UTF-8');
            $texte_reverse = preg_replace('/[^\p{L}\p{N}\s]/u', '', $texte_reverse);
            $texte_reverse = str_replace("\n", ' ', $texte_reverse);
            $cv_name = '';
            if ($request->cv_id && $nhanvien = NhanVienModel::find($request->cv_id)) {
                $cv_name = $nhanvien->nv_hoten;
            }

            if (!Sentinel::inRole('admin')) {
                if ($vpcc === "2190") {
                    $ten_ccv_master = $request->ccv_master;
                } else {
                    if ($request->id_ccv) {
                        $ten_ccv_master = NhanVienModel::find($request->id_ccv)->nv_hoten;
                    }
                }
            } else {
                $ten_ccv_master = $request->ccv_master;
            }
            ///

            if (!Sentinel::inRole('admin')) {
                if ($vpcc === "2190") {
                    $tenVB = $request->ten;
                    $van_ban_id = null;
                } else {
                    $vanban = VanBanModel::find($request->ten);
                    $tenVB = $vanban->vb_nhan ?? '';
                    $van_ban_id = $request->ten;
                }
            } else {
                $tenVB = $request->ten;
                $van_ban_id = null;
            }


            if (strstr($tenVB, "dứt")) {
                $type_cancel = 1;
            } elseif (strstr($tenVB, "hủy")) {
                $type_cancel = 1;
            } elseif (strstr($tenVB, "Hủy")) {
                $type_cancel = 1;
            } elseif (strstr($tenVB, "huỷ")) {
                $type_cancel = 1;
            } elseif (strstr($tenVB, "Huỷ")) {
                $type_cancel = 1;
            } elseif (strstr($tenVB, "bỏ")) {
                $type_cancel = 1;
            } elseif (strstr($tenVB, "Thanh lý")) {
                $type_cancel = 1;
            } elseif (strstr($tenVB, "thanh lý")) {
                $type_cancel = 1;
            } elseif (strstr($tenVB, "phụ lục")) {
                $type_cancel = 2;
            } elseif (strstr($tenVB, "Phụ lục")) {
                $type_cancel = 2;
            } else {
                $type_cancel = 0;
            }
            $cancel_status = null;
            $cancel_description = null;
            $cancel_description_other = null;
            if ($request->get('description')) {
                $cancel_status = 0;
                if ($type_cancel == 1) {
                    $cancel_status = 1;
                    $cancel_description = "Hủy hợp đồng số " . $request->get('description');
                } elseif ($type_cancel == 2) {
                    $cancel_description = "Hợp đồng phụ lục của hợp đồng số " . $request->get('description');
                } else {
                    $cancel_description = "Sửa đổi, bổ sung hợp đồng số " . $request->get('description');
                }
                if ($type_cancel == 1) {
                    $cancel_status = 1;
                    $cancel_description_other = "Đã bị hủy bởi hợp đồng số " . $sohd;
                } elseif ($type_cancel == 2) {
                    $cancel_description_other = "Hợp đồng có phụ lục là hợp đồng số " . $sohd;
                } else {
                    $cancel_description_other = "Đã bị sửa đổi, bổ sung bởi hợp đồng số " . $sohd;
                }
            }

            $suutra = SuuTraModel::where('st_id', $id)->update([
                'ngay_cc' => $request->ngay_cc,
                'so_hd' => $request->so_hd,
                'so_cc_cu' => $request->get('description'),
                'ten_hd' => $tenVB,
                'loai' => $request->vb_kieuhd,
                'van_ban_id' => $van_ban_id,
                'texte' => $texte,
                'texte_en' => $texte_en,
                'duong_su' => $duong_su,
                'duong_su_en' => $duong_su_en,
                'ngan_chan' => $request->loai,
                'picture' => $pic,
                'ccv' => $request->id_ccv,
                'ccv_master' => $ten_ccv_master,
                //'vp_master' => $ten_vp_master,
                'cancel_status' => $cancel,
                'cancel_description' => $cancel_description,
                'vanban' => $request->ten,
                'thu_lao' => $thu_lao,
                'phi_cong_chung' => $phi_cong_chung,
                'note' => "Chỉnh sửa lần cuối bởi " . NhanVienModel::find(Sentinel::check()->id)->nv_hoten,
                'real_name' => $realName,
                'duong_su_index' => $duong_su_index,
                'is_update' => 1,
                'release_doc_number' => $request->release_doc_number,
                'release_doc_date' => $request->release_doc_date,
                'texte_reverse' => $texte_reverse,
                'release_file_name' => $file_name,
                'release_file_path' => $file_path,
                'duong_su_index' => $duong_su_index,
                'contract_period' => $request->contract_period,
                'prevent_doc_receive_date' => $request->prevent_doc_receive_date,
                'undisputed_date' => $request->undisputed_date,
                'undisputed_note' => $request->undisputed_note,
                'cv_id' => $request->cv_id,
                'cv_name' => $cv_name,
                'bank_id' => $request->bank,
                'property_info' => null,
                'transaction_content' => null,
                'release_doc_receive_date' => $request->release_doc_receive_date,
                'merged' => '1',
                'merge_content' => $duong_su_en  . ' ' . $texte_en,
            ]);
            // dd(SuuTraModel::where('st_id', $id)->first());
            $this->delete_solr($id);
            $this->insert_solr(SuuTraModel::where('st_id', $id)->first(), 8);
            $vp = NhanVienModel::find(Sentinel::check()->id)->nv_vanphong;
            $ma_dong_bo = ChiNhanhModel::find($vp)->code_cn;
            $relatedSuutra = SuuTraModel::where('so_hd', $request->get('description'))->where('sync_code', $ma_dong_bo)->first();
            if ($relatedSuutra) {

                $relatedSuutra->update([
                    'type_cancel' => $type_cancel,
                    'cancel_description' => $relatedSuutra->cancel_description . PHP_EOL . $cancel_description_other,
                    'so_cc_cu' => $request->get('so_hd'),
                ]);
                $this->delete_solr($relatedSuutra->st_id);
                $this->insert_solr(SuutraModel::where('st_id', $relatedSuutra->st_id)->first(), 9);
            }
            $id = SuuTraModel::where('st_id', $id)->first()->ma_dong_bo;
            $duong_su = SuuTraController::cleanSpaces($request->duongsu);
            $texte = SuuTraController::cleanSpaces($request->noidung);
            //$ten_vanphong = ChiNhanhModel::find($vp)->cn_ten;
            $ten_ccv = $request->ccv_master;
            $ngay_cc = Carbon::parse($request->ngay_cc)->format('Y-m-d');
            //        $stp = Curl::to('http://127.0.0.1:8000/api/update-data-suutra-stp/' . $id . '')
            //            $stp = Curl::to('http://dev-k.dotary.net/api/update-data-suutra-stp/' . $id . '')
            //                ->withData([
            //                    'property_info' => $texte,
            //                    'transaction_content' => $texte,
            //                    'notary_date' => $ngay_cc,
            //                    'notary_office_name' => $ten_vanphong,
            //                    'contract_number' => $sohd . '_J',
            //                    'contract_name' => $request->get('ten'),
            //                    'relation_object' => $duong_su,
            //                    'notary_person' => $ten_ccv,
            //                    'notary_place' => $ten_vanphong,
            //                    'cancel_status' => $cancel,
            //                    'cancel_description' => $request->get('description'),
            //                ])->post();
        } else {
            $suutra = SuuTraModel::where('st_id', $id)->update([
                'thu_lao' => $thu_lao,
                'phi_cong_chung' => $phi_cong_chung
            ]);
            $this->delete_solr($id);
            $this->insert_solr(SuuTraModel::where('st_id', $id)->first(), 10);
        }

        return redirect(route('searchSolr'))->with('success', 'Cập nhật thành công !');
    }
    public function updateGiaiChap(Request $request, $id)
    {
        //        dd($request->all());
        $data = SuuTraModel::find($id);

        $suutra = SuuTraModel::where('st_id', $id)->update([
            'undisputed_date' => $request->undisputed_date,
            'undisputed_note' => $request->undisputed_note,
            'bank_id' => $request->bank
        ]);
        // $so_hd = is_object($suutra) ? $suutra->so_hd : null;
        $suutra_log = SuuTraLogModel::create([
            'suutra_id' => $id,
            'log_content' => json_encode($suutra),
            'user_id' => Sentinel::getUser()->id,
            'so_hd' => is_object($suutra) ? $suutra->so_hd : null,
            'flag_des'=> 5
        ]);
        $this->delete_solr($id);
        $this->insert_solr(SuuTraModel::where('st_id', $id)->first(), 11);



        return redirect(route('searchSolr'))->with('success', 'Cập nhật thành công !');
    }

    public function import(Request $request)
    {
        $file = $request->file('import');
        $ext = strtoupper($file->getClientOriginalExtension());
        if ($ext == 'XLSX') {
            Excel::import(new SuuTraImport(), $file, 's3', \Maatwebsite\Excel\Excel::XLSX);
        } elseif ($ext == 'XLS') {
            Excel::import(new SuuTraImport(), $file, 's3', \Maatwebsite\Excel\Excel::XLS);
        } else {
            return back()->with('error', 'File không đúng định dạng xls hoặc xlsx');
        }
        return back()->with('success', 'Đã import file thành công');
    }

    public function exportExample()
    {
        return Excel::download(new SuuTraExampleExport(), 'filemau-suutra.xlsx');
    }

    public static function addDuongSu(Request $request)
    {
        $id = $request->id;
        $id_vanphong = $request->id_vanphong;
        $data = User::find($id)->info($id_vanphong);
        $thongTinStr = $data['thong_tin_str'];
        $thongTinArr = $data['thong_tin_arr'];
        $lichSuHonNhanArr = $data['lich_su_hon_nhan'];
        return json_encode([
            'thong_tin_str' => $thongTinStr,
            'thong_tin_arr' => $thongTinArr,
            'lich_su_hon_nhan_arr' => $lichSuHonNhanArr,
        ]);
    }

    public static function thongTinDuongSu(Request $request)
    {
        $id = $request->id;
        $data = User::find($id)->info();
        $thongTinStr = $data['thong_tin_str'];
        $thongTinArr = $data['thong_tin_arr'];
        $lichSuHonNhanArr = $data['lich_su_hon_nhan'];

        return json_encode([
            'thong_tin_str' => $thongTinStr,
            'thong_tin_arr' => $thongTinArr,
            'lich_su_hon_nhan_arr' => $lichSuHonNhanArr,
        ]);
    }

    public static function thongTinTaiSan(Request $request)
    {
        $id = $request->id;
        $id_vanphong = $request->id_vanphong;
        $data = TaiSanModel::find($id)->info($id_vanphong);
        return json_encode([
            'thong_tin_arr' => $data['thong_tin_arr'],
            'thong_tin_str' => $data['thong_tin_str']
        ]);
    }

    public static function listDuongSu(Request $request)
    {
        $where = [];
        if ($request->search_str) {
            $where[] = ['first_name', 'LIKE', '%' . $request->search_str . '%'];
        }
        $role_kh = Sentinel::findRoleBySlug('khach-hang');
        $listDuongSu = $role_kh->users()->where($where)->get()->map(function ($item) {
            $data = $item->info();
            $item->thong_tin_str = $data['thong_tin_str'];
            return $item;
        });
        return ['status' => true, 'data' => $listDuongSu];
    }


    public function kiemtraid($id)
    {
        $data = SuuTraModel::where('uchi_id', $id)->first();
        return $data;
    }

    public function kiemtraid_nganchan($id)
    {
        $data = SuuTraModel::where('uchi_id_ngan_chan', $id)->first();
        return $data;
    }


    public function postDataSTP(Request $request)
    {
        $ngay_cc = Carbon::parse($request->ngay_cc)->format('Y-m-d');
        ///
        $duong_su_index = "";
        $duong_su_draw = preg_replace('/[^\p{L}\p{N}\s]/u', '', $request->duong_su);
        $duong_su_draw = str_replace("\n", ' ', $duong_su_draw);
        $duong_su_draw = explode(' ', SuuTraController::convert_vi_to_en($duong_su_draw));
        foreach ($duong_su_draw as $item) {
            switch (strlen($item)) {
                case 3:
                    $duong_su_index .= $item . " ";
                    break;
                case 9:
                    $sub1 = substr($item, 0, 3);
                    $sub2 = substr($item, 3, 3);
                    $sub3 = substr($item, 6, 3);
                    $duong_su_index .= $sub1 . " " . $sub2 . " " . $sub3 . " ";
                    break;
                case 12:
                    $sub1 = substr($item, 0, 3);
                    $sub2 = substr($item, 3, 3);
                    $sub3 = substr($item, 6, 3);
                    $sub4 = substr($item, 9, 3);

                    $duong_su_index .= $sub1 . " " . $sub2 . " " . $sub3 . " " . $sub4 . " ";
                    break;
                default:
                    break;
            }
        }
        $syncKey = "000";
        if (!is_null($request->ma_dong_bo)) {
            $syncKey = explode("_", $request->ma_dong_bo)[0];
        }
        $texte_reverse = preg_replace('/[^\p{L}\p{N}\s]/u', '', $request->texte_reverse);
        $texte_reverse = str_replace("\n", ' ', $texte_reverse);
        $texte_reverse = preg_replace("/(?![.=$'€%-])\p{P}/u", '', $texte_reverse);
        $duong_su = str_replace("\n", ';', $request->duong_su);
        $duong_su_en = str_replace("\n", ' ;', $request->duong_su_en);
        //
        $suutra = SuuTraModel::create([
            'ma_dong_bo' => $request->ma_dong_bo,
            'uchi_id' => $request->uchi_id,
            'texte' => $request->texte,
            'duong_su' => $duong_su,
            'ngan_chan' => $request->ngan_chan,
            'ngay_nhap' => $request->ngay_nhap,
            'ngay_cc' => $ngay_cc,
            'so_hd' => $request->so_hd,
            'ten_hd' => $request->ten_hd,
            'ccv_master' => $request->ccv_master,
            'vp_master' => $request->vp_master,
            'duong_su_en' => $duong_su_en,
            'texte_en' => $request->texte_en,
            'ma_phan_biet' => $request->ma_phan_biet,
            'cancel_status' => $request->cancel_status,
            'cancel_description' => $request->cancel_description,
            'uchi_id_ngan_chan' => $request->uchi_id_ngan_chan,
            'duong_su_index' => $duong_su_index,
            'sync_code' => $syncKey,
            'contract_period' => $request->contract_period,
            'property_info' => $request->property_info,
            'transaction_content' => $request->transaction_content,
            'texte_reverse' => $texte_reverse,
            'release_in_book_number' => $request->release_in_book_number,
            'release_doc_date' => $request->release_doc_date,
            'release_file_name' => $request->release_file_name,
            'release_file_path' => $request->release_file_path,
            'release_regist_agency' => $request->release_regist_agency,
            'release_person_info' => $request->release_person_info,
            'release_doc_number' => $request->release_doc_number,
            'release_doc_summary' => $request->release_doc_summary,
            'merged' => 1,
            'merge_content' => $duong_su_en  . ' ' . $request->texte_en,
            'trans_val' => str_replace('.00', '', str_replace(',', '', $request->trans_val)) ?? '',

        ]);
              //06092023
              $this->delete_solr($suutra->st_id);
        $this->insert_solr(SuutraModel::where('st_id', $suutra->st_id)->first(), 12);
        if ($request->first) {
            $contract_content = "- Tên hợp đồng : " . $request->ten_hd . "\n" . "- Số hợp đồng : " . $request->so_hd . "\n" . "- Ngày công chứng " . $ngay_cc . "\n" . "- Địa điểm công chứng : " . $request->vp_master;
            SuuTraLogModel::create([
                'uchi_id' => $request->uchi_id,
                'office_code' => $request->ma_dong_bo,
                'execute_person' => $request->entry_user_name,
                'execute_content' => "Đăng ký",
                'contract_content' => $contract_content,
                'so_hd' => $request->so_hd,
                'created_at' => $request->ngay_nhap,
                'flag_des'=> 6,
                'user_id' => Sentinel::getUser()->id,
            ]);
        }
        //SuuTraModel::where('uchi_id',$request->uchi_id)->first()->addIndex();
        //SuuTraModel::where('uchi_id',$request->uchi_id)->first()->update(['complete'=>2]);

    }

    public function postDataSTPFuture(Request $request, $id)
    {
        $tpid = substr($id, -1);
        ///
        $duong_su_index = "";
        $duong_su_draw = preg_replace('/[^\p{L}\p{N}\s]/u', '', $request->duong_su);
        $duong_su_draw = str_replace("\n", ' ', $duong_su_draw);
        $duong_su_draw = explode(' ', SuuTraController::convert_vi_to_en($duong_su_draw));
        foreach ($duong_su_draw as $item) {
            switch (strlen($item)) {
                case 3:
                    $duong_su_index .= $item . " ";
                    break;
                case 9:
                    $sub1 = substr($item, 0, 3);
                    $sub2 = substr($item, 3, 3);
                    $sub3 = substr($item, 6, 3);
                    $duong_su_index .= $sub1 . " " . $sub2 . " " . $sub3 . " ";
                    break;
                case 12:
                    $sub1 = substr($item, 0, 3);
                    $sub2 = substr($item, 3, 3);
                    $sub3 = substr($item, 6, 3);
                    $sub4 = substr($item, 9, 3);

                    $duong_su_index .= $sub1 . " " . $sub2 . " " . $sub3 . " " . $sub4 . " ";
                    break;
                default:
                    break;
            }
        }
        $syncKey = "000";
        if (!is_null($request->ma_dong_bo)) {
            $syncKey = explode("_", $request->ma_dong_bo)[0];
        }
        //
        $ngay_cc = Carbon::parse($request->ngay_cc)->format('Y-m-d');
        if ($tpid == "D") {
        } else {
            $texte_reverse = preg_replace('/[^\p{L}\p{N}\s]/u', '', $request->texte_reverse);
            $texte_reverse = str_replace("\n", ' ', $texte_reverse);
            $texte_reverse = preg_replace("/(?![.=$'€%-])\p{P}/u", '', $texte_reverse);
            //$texte_reverse=conf
            $duong_su_index = mb_convert_encoding($duong_su_index, 'UTF-8');
            $suutra = SuuTraModel::create([
                'ma_dong_bo' => $request->ma_dong_bo,
                'uchi_id' => $request->uchi_id,
                'texte' => $request->texte,
                'duong_su' => $request->duong_su,
                'ngan_chan' => $request->ngan_chan,
                'ngay_nhap' => $request->ngay_nhap,
                'ngay_cc' => $request->ngay_cc,
                'so_hd' => $request->so_hd,
                'ten_hd' => $request->ten_hd,
                'ccv_master' => $request->ccv_master,
                'vp_master' => $request->vp_master,
                'duong_su_en' => $request->duong_su_en,
                'texte_en' => $request->texte_en,
                'ma_phan_biet' => $request->ma_phan_biet,
                'cancel_status' => $request->cancel_status,
                'cancel_description' => $request->cancel_description,
                'uchi_id_ngan_chan' => $request->uchi_id_ngan_chan,
                'duong_su_index' => $duong_su_index,
                'sync_code' => $syncKey,
                'contract_period' => $request->contract_period,
                'property_info' => $request->property_info,
                'transaction_content' => $request->transaction_content,
                'texte_reverse' => $texte_reverse,
                'merged' => 1,
                'merge_content' => $request->duong_su_en ?? $request->duong_su . ' ' . $request->texte_en ?? $$request->texte,
                'trans_val' => str_replace('.00', '', str_replace(',', '', $request->trans_val)) ?? '',

            ]);
                  //06092023
                  $this->delete_solr($suutra->st_id);
            $this->insert_solr(SuutraModel::where('st_id', $suutra->st_id)->first(), 13);
            if ($request->first) {
                $contract_content = "- Tên hợp đồng : " . $request->ten_hd . "\n" . "- Số hợp đồng : " . $request->so_hd . "\n" . "- Ngày công chứng " . $ngay_cc . "\n" . "- Địa điểm công chứng : " . $request->vp_master;
                SuuTraLogModel::create([
                    'uchi_id' => $request->uchi_id,
                    'office_code' => $request->ma_dong_bo,
                    'execute_person' => $request->entry_user_name,
                    'execute_content' => "Đăng ký",
                    'contract_content' => $contract_content,
                    'so_hd' => $request->so_hd,
                    'created_at' => $request->ngay_nhap,
                    'flag_des'=> 7,
                    'user_id' => Sentinel::getUser()->id,
                ]);
            }
            return $suutra;
        }
    }

    public function updateDataSTP(Request $request, $id)
    {
        $ngay_cc = Carbon::parse($request->ngay_cc)->format('Y-m-d');
        ///
        $duong_su_index = "";
        $duong_su_draw = preg_replace('/[^\p{L}\p{N}\s]/u', '', $request->duong_su);
        $duong_su_draw = str_replace("\n", ' ', $duong_su_draw);
        $duong_su_draw = explode(' ', SuuTraController::convert_vi_to_en($duong_su_draw));
        foreach ($duong_su_draw as $item) {
            switch (strlen($item)) {
                case 3:
                    $duong_su_index .= $item . " ";
                    break;
                case 9:
                    $sub1 = substr($item, 0, 3);
                    $sub2 = substr($item, 3, 3);
                    $sub3 = substr($item, 6, 3);
                    $duong_su_index .= $sub1 . " " . $sub2 . " " . $sub3 . " ";
                    break;
                case 12:
                    $sub1 = substr($item, 0, 3);
                    $sub2 = substr($item, 3, 3);
                    $sub3 = substr($item, 6, 3);
                    $sub4 = substr($item, 9, 3);

                    $duong_su_index .= $sub1 . " " . $sub2 . " " . $sub3 . " " . $sub4 . " ";
                    break;
                default:
                    break;
            }
        }
        $texte_reverse = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $request->texte_reverse);
        $texte_reverse = str_replace("\n", ' ', $texte_reverse);
        $texte_reverse = preg_replace("/(?![.=$'€%-])\p{P}/u", ' ', $texte_reverse);
        $syncKey = "000";
        if (!is_null($request->ma_dong_bo)) {
            $syncKey = explode("_", $request->ma_dong_bo)[0];
        }
        //
        if (!SuuTraModel::where('uchi_id', $id)->first()) {
            return "0";
        }
        $duong_su = str_replace("\n", ';', $request->duong_su);
        $duong_su_en = str_replace("\n", ' ;', $request->duong_su_en);
        $data = SuuTraModel::where('uchi_id', $id)->first()
            ->update(
                [
                    'ma_dong_bo' => $request->ma_dong_bo,
                    'texte' => $request->texte,
                    'duong_su' => $duong_su,
                    'ngay_nhap' => $request->ngay_nhap,
                    'ngay_cc' => $ngay_cc,
                    'so_hd' => $request->so_hd,
                    'ten_hd' => $request->ten_hd,
                    'ccv_master' => $request->ccv_master,
                    'vp_master' => $request->vp_master,
                    'duong_su_en' => $duong_su_en,
                    'texte_en' => $request->texte_en,
                    'cancel_status' => $request->cancel_status,
                    'cancel_description' => $request->cancel_description,
                    'duong_su_index' => $duong_su_index,
                    'sync_code' => $syncKey,
                    'contract_period' => $request->contract_period,
                    'property_info' => $request->property_info,
                    'transaction_content' => $request->transaction_content,
                    'texte_reverse' => $texte_reverse,


                ]
            );
        if ($request->first) {
            $contract_content = "- Tên hợp đồng : " . $request->ten_hd . "\n" . "- Số hợp đồng : " . $request->so_hd . "\n" . "- Ngày công chứng " . $ngay_cc . "\n" . "- Địa điểm công chứng : " . $request->vp_master . "\n" . "- Bên liên quan" . $duong_su . "\n" . "- Nội dung : " . $request->transaction_content . "\n" . "- Tài sản : " . $request->property_info;
            SuuTraLogModel::create([
                'uchi_id' => $request->uchi_id,
                'office_code' => $request->ma_dong_bo,
                'execute_person' => $request->update_user_name,
                'execute_content' => "Chỉnh sửa",
                'contract_content' => $contract_content,
                'so_hd' => $request->so_hd,
                'created_at' => $request->ngay_nhap,
                'flag_des'=> 8,
                'user_id' => Sentinel::getUser()->id,
            ]);
        }
        //if(SuuTraModel::where('uchi_id', $id)->first()){
        //	SuuTraModel::where('uchi_id', $id)->first()->addIndex()();

        //}
        return $data;
    }

    public function updateDataSTP_nganchan(Request $request, $id)
    {
        $ngay_cc = Carbon::parse($request->ngay_cc)->format('Y-m-d');
        ///
        $duong_su_index = "";
        $duong_su_draw = preg_replace('/[^\p{L}\p{N}\s]/u', '', $request->duong_su);
        $duong_su_draw = str_replace("\n", ' ', $duong_su_draw);
        $duong_su_draw = explode(' ', SuuTraController::convert_vi_to_en($duong_su_draw));
        foreach ($duong_su_draw as $item) {
            switch (strlen($item)) {
                case 3:
                    $duong_su_index .= $item . " ";
                    break;
                case 9:
                    $sub1 = substr($item, 0, 3);
                    $sub2 = substr($item, 3, 3);
                    $sub3 = substr($item, 6, 3);
                    $duong_su_index .= $sub1 . " " . $sub2 . " " . $sub3 . " ";
                    break;
                case 12:
                    $sub1 = substr($item, 0, 3);
                    $sub2 = substr($item, 3, 3);
                    $sub3 = substr($item, 6, 3);
                    $sub4 = substr($item, 9, 3);

                    $duong_su_index .= $sub1 . " " . $sub2 . " " . $sub3 . " " . $sub4 . " ";
                    break;
                default:
                    break;
            }
        }
        $syncKey = "000";
        if (!is_null($request->ma_dong_bo)) {
            $syncKey = explode("_", $request->ma_dong_bo)[0];
        }
        $ngan_chan = SuuTraModel::PREVENT;
        if ($request->release_flg == 1) {
            $ngan_chan = SuuTraModel::WARNING;
        }
        try {
            //
            $data = SuuTraModel::where('uchi_id_ngan_chan', $id)
                ->update(
                    [
                        'ma_dong_bo' => $request->ma_dong_bo,
                        'texte' => $request->texte,
                        'duong_su' => $request->duong_su,
                        'ngay_nhap' => $request->ngay_nhap,
                        'ngay_cc' => $ngay_cc,
                        'so_hd' => $request->so_hd,
                        'ten_hd' => $request->ten_hd,
                        'ccv_master' => $request->ccv_master,
                        'vp_master' => $request->vp_master,
                        'cancel_status' => $request->cancel_status,
                        'cancel_description' => $request->cancel_description,
                        'duong_su_index' => $duong_su_index,
                        'sync_code' => $syncKey,
                        'texte_reverse' => $request->texte_reverse,
                        'release_in_book_number' => $request->release_in_book_number,
                        'release_doc_date' => $request->release_doc_date,
                        'release_file_name' => $request->release_file_name,
                        'release_file_path' => $request->release_file_path,
                        'release_regist_agency' => $request->release_regist_agency,
                        'release_person_info' => $request->release_person_info,
                        'release_doc_number' => $request->release_doc_number,
                        'release_doc_summary' => $request->release_doc_summary,
                        'ngan_chan' => $ngan_chan
                    ]
                );
            $this->delete_solr($id);
            $this->insert_solr(SuuTraModel::where('st_id', $id)->first(), 14);
        } catch (QueryException $e) {
            return "0";
        }
        return $data;
    }

    public function logUchi(Request $request)
    {
        $data = new LogUchiModel();
        $data->run_date_past = $request->run_date_past;
        $data->run_date_now = $request->run_date_now;
        $data->row_count = $request->row_count;
        $data->save();
    }

    public function getDataSuuTra(Request $request)
    {
        $vp_id = $request->vp_id;
        $code = $request->code;
        $path = storage_path() . "/app/" . $code . ".json";
        $json = json_decode(file_get_contents($path), true);
        $this->config = $json;
        $data = SuuTraModel::join('nhanvien', 'nhanvien.nv_id', '=', 'suutranb.ccv')
            ->where('suutranb.vp', '=', $vp_id)
            ->where('suutranb.ma_phan_biet', '=', 'D')
            ->whereIn('suutranb.ngan_chan', [0, 2])
            ->whereBetween('suutranb.created_at', [$this->config['runpast'], $this->config['runtime']])
            ->orderby('st_id', 'desc')
            ->get();
        foreach ($data as $item) {
            $ccv_lienket = NhanVienModel::where('nv_id', '=', $item->ccv)->first();
            $nguoinhap = NhanVienModel::join('role_users', 'role_users.user_id', '=', 'nv_id')
                ->join('roles', 'roles.id', '=', 'role_users.role_id')
                ->where('nv_id', '=', $item->nguoinhap)->first();
            if ($nguoinhap->slug == 'chuyen-vien') {
                $item['cv_lienket'] = $nguoinhap->id_lienket;
            }
            $item['ccv_lienket'] = $ccv_lienket->id_lienket;
        }
        $myfile = fopen("local2.txt", "w");
        fwrite($myfile, json_encode($data));
        fclose($myfile);
        return $data;
    }

    public function readConfig(Request $request)
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');
        $code = $request->code;
        $path = storage_path() . "/app/" . $code . ".json";
        if (!Storage::exists($code . ".json")) {
            $json = json_decode(Storage::get("template.json"), true);
        } else {
            $json = json_decode(file_get_contents($path), true);
        }
        $this->config = $json;
        if ($this->config['flag'] == false) {
            $this->config['flag'] = true;
            $this->config['page'] = 1;
            $this->config['runtime'] = "2021-05-01 00:00:00";
            $this->config['runpast'] = $now;
            $this->config['totalRow'] = $this->countPastData();
            $this->config['limitPage'] = $this->limitPage;
            if (0 == $this->config['totalRow'] % $this->limitPage) {
                $this->config['totalPage'] = $this->config['totalRow'] / $this->limitPage;
            } else {
                $this->config['totalPage'] = $this->config['totalRow'] / $this->limitPage;
                $this->config['totalPage'] = floor($this->config['totalPage']) + 1;
            }
        } else {
            $this->config['page'] = $this->config['page'] + 1;
            $this->page = $this->config['page'];
            $this->config['runpast'] = $this->config['runtime'];
            $this->config['runtime'] = $now;
        }
        $this->totalRow = $this->config['totalRow'];
        $this->runpast = $this->config['runtime'];
        $this->runtime = Carbon::now()->format('Y-m-d H:i:s');
        $handle = fopen($path, 'w+');
        fputs($handle, json_encode($this->config));
        fclose($handle);
    }

    public function updateSYNC(Request $request, $id)
    {
        SuuTraModel::where('st_id', $id)->first()
            ->update([
                'ma_dong_bo' => $request->ma_dong_bo
            ]);
    }

    public function downloadImg($img, $name)
    {
        try {
            $user = Sentinel::getUser();
            if ($user) {
                $path = storage_path() . '/' . 'app' . '/' . 'suutra' . '/' . $img;
                if (file_exists($path)) {
                    return response()->download($path, $name);
                }
            } else {
                return view('admin.404');
            }
        } catch (\Exception $exception) {
            return $exception;
        }
    }
    public function returnDataToNotaryOffice(Request $request)
    {
        $timePast = $request->timePast;
        $timeRun = $request->timeRun;
        $sync_code = $request->code;
        $offfice = ChiNhanhModel::where('code_cn', $sync_code)->first();
        if ($offfice->token != $request->token) {
            return ['status' => false, 'message' => 'token not found'];
        }
        if (!$sync_code) {
            return ['status' => false, 'message' => 'code not found'];
        }
        $data = SuuTraModel::query()
            ->where('sync_code', $sync_code);
        if ($timeRun != SuuTraModel::EMPTY && $timePast != SuuTraModel::EMPTY) {
            $data = $data->whereBetween('suutranb.created_at', [$timePast, $timeRun]);
        } else {
            return ['status' => false, 'message' => 'Thieu parameter'];
        }
        return ['status' => true, 'data' => $data->get(), 'message' => "Thanh cong"];
    }
    public function returnDataToBackup(Request $request)
    {
        $timePast = $request->timePast;
        $timeRun = $request
            ->timeRun;
        if ($request->token != "aboqor") {
            return ['status' => false, 'message' => 'token not found'];
        }
        // dd(1);
        $data = SuuTraModel::query();

        if ($timeRun != '' && $timePast != '') {
            $data = $data->whereBetween('suutranb.updated_at', [$timePast, $timeRun]);
        } else {
            return ['status' => false, 'message' => 'Thieu parameter'];
        }
        return ['status' => true, 'data' => $data->get(), 'message' => "Thanh cong"];
    }
    public function deleteSuutra($id)
    {
        //xóa suutra
    try{
        $nhanvien = NhanVienModel::find(Sentinel::check()->id);
        $now = Carbon::now()->format('d/m/Y');
        SuutraModel::find($id)->update([
            'deleted_note' => 'Đã bị xoá bởi ' . $nhanvien->nv_hoten . '. Ngày xoá: ' . $now
        ]);
        $relatedSuutra =  SuutraModel::find($id);
        $this->delete_solr($relatedSuutra->st_id);
        $this->insert_solr(SuutraModel::where('st_id', $relatedSuutra->st_id)->first(), 40);
        return redirect(route('searchSolr'))->with('success', 'Đã xoá thành công !');
    } catch (Exception $e) {
        return redirect(route('searchSolr'))->with('error', 'Vui lòng nhập lại đúng cú pháp tìm kiếm hoặc liên hệ hỗ trợ để được hướng dẫn!');
    }
    }
    public function editSuutraSolr(Request $request)
    {
        $id = $request->id;
        $suutra = SuutraModel::find($id);
        $chinhanh = ChiNhanhModel::all();
        $nhanvien = User::all();
    return view('admin.suutra.editSuutraSolr', compact('suutra', 'chinhanh', 'nhanvien'));
    }
    public function updateSuutraSolr(Request $request, $id)
    {
        $suutra = SuuTraModel::find($id);
        if($suutra){
            $log = SolrEditLogModel::create([
                'data_before' => $suutra->toJson(),
                'user_id' =>  Sentinel::getUser()->id,
            ]);
            $suutra->update($request->all());
            $log->update([
                'data_after'=> $suutra->toJson()
            ]);
            $this->delete_solr($id);
            $this->insert_solr(SuutraModel::where('st_id', $id)->first(), 100);
            $user_exec = Sentinel::getUser()->id;
            $description = "Cập nhật thông tin từ solr : ".$log;
            $this->api_create_log($user_exec, $description);
                return redirect()->back()->with('success', 'Done'); 
        }
        else{
            return redirect()->back()->with('error', 'Fail'); 
        }
        // return redirect(route('searchSolr'))->with('success', 'Đã cập nhật thành công !');
  
        
    }
    public function viewsolr(){
        return view('admin.suutra.viewsolr');
    }
    // app/Http/Controllers/SuutraController.php

    public function updateSuutraSolrByDate(Request $request)
    {
        $from = $request->input('from_date');
        $to = $request->input('to_date');

        $records = SuuTraModel::whereBetween('ngay_cc', [$from, $to])->get();
        $total = count($records);

        if ($total == 0) {
            return response()->json(['status' => 'EMPTY']);
        }

        session(['progress' => 0]);
        $logs = [];
        foreach ($records as $index => $suutra1) {
            $this->delete_solr($suutra1->st_id);

            try {
                $response = $this->insert_solr_data($suutra1, 500);
                session()->push('live_logs', "✅ Insert ST_ID {$suutra1->st_id} thành công:");
            } catch (\Exception $e) {
                session()->push('live_logs', "❌ Insert ST_ID {$suutra1->st_id} lỗi: " . $e->getMessage());
            }

            $percent = intval((($index + 1) / $total) * 100);
            session(['progress' => $percent]);

            usleep(200000); // 200ms
        }


        session([
            'progress' => 100,
            'sync_logs' => $logs,
        ]);
        $user_exec = Sentinel::getUser()->id;
        $description = "đã đồng bộ từ ngày " . $from . ' đến ngày ' . $to. ', tổng số hồ sơ '. $total;
        $this->api_create_log($user_exec, $description);
        return response()->json(['status' => 'OK']);
    }
public function importSolr()
{
    $url = 'http://localhost:8983/solr/timkiemsuutra/dataimport?command=full-import&clean=true&commit=true';
    $response = Http::get($url);

    return response()->json([
        'solr_response' => $response->body()
    ]);
}

}
