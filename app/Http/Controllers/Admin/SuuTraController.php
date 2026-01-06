<?php

namespace App\Http\Controllers\Admin;

use App\Models\SolrCheckModel;
use App\Http\Controllers\Controller;
use App\Models\KieuModel;
use App\Models\Kieuhopdong;
use App\Models\User;
use App\Models\SuuTraModel;
use App\Models\VanBanModel;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Ixudra\Curl\Facades\Curl;
use App\Http\Controllers\AppController;
use stdClass;

class SuuTraController extends Controller
{
  protected array $config = [];
    public static function insert_solr($data, $note)
    {
        $create = new SolrCheckModel;
        $create->st_id = $data->st_id;
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
        // dd($response);
        curl_close($curl);
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
         </delete>',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/xml'
            ),
        ));
        $response = curl_exec($curl);
        // dd($response);
        curl_close($curl);
    }
    public function import()
    {

        $path = storage_path() . "/json/local.json";
        $json = json_decode(file_get_contents($path), true);
        $this->config = $json;
        $run = Carbon::now()->format('Y-m-d H:i:s');
        // $run  = $this->config['timeRun'];
        $past = $this->config['timePast'];
        // if ($past < Carbon::now()->subMinutes(30)) {
        //     $this->sendMessageToTelegram('[⚠️]_[STP2(240)]_[SYNC]'.$past.'-'.$run.' không kết nối đến hội được');
        // }
        //        dd($run, $past);
        $link = 'http://stp2.hoicongchungviencantho.org/get-data-for-backup?timePast=' . urlencode($past) . '&timeRun=' . urlencode($run) . '&token='
            . "aboqor";
            $stp = Curl::to($link)->get();
        if ($stp) {
            $data = json_decode($stp)->data;
            foreach ($data as $item) {
                echo $item->ma_dong_bo . " ";
                $item = AppController::convert_unicode_object($item);
                $suutra_old = SuuTraModel::where('ma_dong_bo', $item->ma_dong_bo)->first();
                if (!$suutra_old) {
                    echo "create";
                    $suutra = SuuTraModel::create([
                        'ma_dong_bo'             => $item->ma_dong_bo,
                        'uchi_id'                => $item->uchi_id,
                        'texte'                  => $item->texte,
                        'loai'                   => $item->loai,
                        'duong_su'               => $item->duong_su,
                        'tai_san'                => $item->tai_san,
                        'ngan_chan'              => $item->ngan_chan,
                        'ngay_nhap'              => $item->ngay_nhap,
                        'ngay_cc'                => $item->ngay_cc,
                        'so_hd'                  => $item->so_hd,
                        'ten_hd'                 => $item->ten_hd,
                        'ccv'                    => $item->ccv,
                        'vp'                     => $item->vp,
                        'chu_y'                  => $item->chu_y,
                        'ccv_master'             => $item->ccv_master,
                        'vp_master'              => $item->vp_master,
                        'picture'                => $item->picture,
                        'duong_su_en'            => $item->duong_su_en,
                        'texte_en'               => $item->texte_en,
                        'status'                 => $item->status,
                        'ma_phan_biet'           => $item->ma_phan_biet,
                        'cancel_status'          => $item->cancel_status,
                        'cancel_description'     => $item->cancel_description,
                        'uchi_id_ngan_chan'      => $item->uchi_id_ngan_chan,
                        'nguoinhap'              => $item->nguoinhap,
                        'vanban'                 => $item->vanban,
                        'type_cancel'            => $item->type_cancel,
                        'so_cc_cu'               => $item->so_cc_cu,
                        'phi_cong_chung'         => $item->phi_cong_chung,
                        'thu_lao'                => $item->thu_lao,
                        'note'                   => $item->note,
                        'real_name'              => $item->real_name,
                        'duong_su_index'         => $item->duong_su_index,
                        'file'                   => $item->file,
                        'sync_code'              => $item->sync_code,
                        'is_update'              => $item->is_update,
                        'property_info'          => $item->property_info,
                        'transaction_content'    => $item->transaction_content,
                        'contract_period'        => $item->contract_period,
                        'complete'               => $item->complete,
                        'texte_reverse'          => $item->texte_reverse,
                        'release_in_book_number' => $item->release_in_book_number,
                        'release_doc_date'       => $item->release_doc_date,
                        'release_file_name'      => $item->release_file_name,
                        'release_file_path'      => $item->release_file_path,
                        'release_regist_agency'  => $item->release_regist_agency,
                        'release_person_info'    => $item->release_person_info,
                        'release_doc_number'     => $item->release_doc_number,
                        'release_doc_summary'    => $item->release_doc_summary,
                        'undisputed_date' => $item->undisputed_date,
                        'undisputed_note' => $item->undisputed_note,
                        'bank_id' => $item->bank_id,
                        'cv_id' => $item->cv_id,
                        'cv_name' => $item->cv_name,
                        'van_ban_id' => $item->van_ban_id,
                        'merged' => 1,
                        'merge_content' => $item->duong_su_en . ' ' . $item->texte_en,
                        'created_at'    => $item->created_at,
                        'updated_at'    => $item->updated_at,
                    ]);
                    $this->insert_solr($suutra, 99);
                } else {
                    echo "update";
                    $suutra_old->update([
                        'ma_dong_bo'             => $item->ma_dong_bo,
                        'uchi_id'                => $item->uchi_id,
                        'texte'                  => $item->texte,
                        'loai'                   => $item->loai,
                        'duong_su'               => $item->duong_su,
                        'tai_san'                => $item->tai_san,
                        'ngan_chan'              => $item->ngan_chan,
                        'ngay_nhap'              => $item->ngay_nhap,
                        'ngay_cc'                => $item->ngay_cc,
                        'so_hd'                  => $item->so_hd,
                        'ten_hd'                 => $item->ten_hd,
                        'ccv'                    => $item->ccv,
                        'vp'                     => $item->vp,
                        'chu_y'                  => $item->chu_y,
                        'ccv_master'             => $item->ccv_master,
                        'vp_master'              => $item->vp_master,
                        'picture'                => $item->picture,
                        'duong_su_en'            => $item->duong_su_en,
                        'texte_en'               => $item->texte_en,
                        'status'                 => $item->status,
                        'ma_phan_biet'           => $item->ma_phan_biet,
                        'cancel_status'          => $item->cancel_status,
                        'cancel_description'     => $item->cancel_description,
                        'uchi_id_ngan_chan'      => $item->uchi_id_ngan_chan,
                        'nguoinhap'              => $item->nguoinhap,
                        'vanban'                 => $item->vanban,
                        'type_cancel'            => $item->type_cancel,
                        'so_cc_cu'               => $item->so_cc_cu,
                        'phi_cong_chung'         => $item->phi_cong_chung,
                        'thu_lao'                => $item->thu_lao,
                        'note'                   => $item->note,
                        'real_name'              => $item->real_name,
                        'duong_su_index'         => $item->duong_su_index,
                        'file'                   => $item->file,
                        'sync_code'              => $item->sync_code,
                        'is_update'              => $item->is_update,
                        'property_info'          => $item->property_info,
                        'transaction_content'    => $item->transaction_content,
                        'contract_period'        => $item->contract_period,
                        'complete'               => $item->complete,
                        'texte_reverse'          => $item->texte_reverse,
                        'release_in_book_number' => $item->release_in_book_number,
                        'release_doc_date'       => $item->release_doc_date,
                        'release_file_name'      => $item->release_file_name,
                        'release_file_path'      => $item->release_file_path,
                        'release_regist_agency'  => $item->release_regist_agency,
                        'release_person_info'    => $item->release_person_info,
                        'release_doc_number'     => $item->release_doc_number,
                        'release_doc_summary'    => $item->release_doc_summary,
                        'undisputed_date' => $item->undisputed_date,
                        'undisputed_note' => $item->undisputed_note,
                        'bank_id' => $item->bank_id,
                        'van_ban_id' => $item->van_ban_id,
                        'cv_id' => $item->cv_id,
                        'cv_name' => $item->cv_name,
                        'deleted_note' => $item->deleted_note,
                        'merged' => 1,
                        'merge_content' => $item->duong_su_en . ' ' . $item->texte_en,
                        'created_at'    => $item->created_at,
                        'updated_at'    => $item->updated_at,
                    ]);
                    $this->delete_solr($suutra_old->st_id);
                    $ar_solr = [
                        'st_id' => $suutra_old->st_id,
                        'ma_dong_bo'             => $item->ma_dong_bo,
                        'uchi_id'                => $item->uchi_id,
                        'texte'                  => $item->texte,
                        'loai'                   => $item->loai,
                        'duong_su'               => $item->duong_su,
                        'tai_san'                => $item->tai_san,
                        'ngan_chan'              => $item->ngan_chan,
                        'ngay_nhap'              => $item->ngay_nhap,
                        'ngay_cc'                => $item->ngay_cc,
                        'so_hd'                  => $item->so_hd,
                        'ten_hd'                 => $item->ten_hd,
                        'ccv'                    => $item->ccv,
                        'vp'                     => $item->vp,
                        'chu_y'                  => $item->chu_y,
                        'ccv_master'             => $item->ccv_master,
                        'vp_master'              => $item->vp_master,
                        'picture'                => $item->picture,
                        'duong_su_en'            => $item->duong_su_en,
                        'texte_en'               => $item->texte_en,
                        'status'                 => $item->status,
                        'ma_phan_biet'           => $item->ma_phan_biet,
                        'cancel_status'          => $item->cancel_status,
                        'cancel_description'     => $item->cancel_description,
                        'uchi_id_ngan_chan'      => $item->uchi_id_ngan_chan,
                        'nguoinhap'              => $item->nguoinhap,
                        'vanban'                 => $item->vanban,
                        'type_cancel'            => $item->type_cancel,
                        'so_cc_cu'               => $item->so_cc_cu,
                        'phi_cong_chung'         => $item->phi_cong_chung,
                        'thu_lao'                => $item->thu_lao,
                        'note'                   => $item->note,
                        'real_name'              => $item->real_name,
                        'duong_su_index'         => $item->duong_su_index,
                        'file'                   => $item->file,
                        'sync_code'              => $item->sync_code,
                        'is_update'              => $item->is_update,
                        'property_info'          => $item->property_info,
                        'transaction_content'    => $item->transaction_content,
                        'contract_period'        => $item->contract_period,
                        'complete'               => $item->complete,
                        'texte_reverse'          => $item->texte_reverse,
                        'release_in_book_number' => $item->release_in_book_number,
                        'release_doc_date'       => $item->release_doc_date,
                        'release_file_name'      => $item->release_file_name,
                        'release_file_path'      => $item->release_file_path,
                        'release_regist_agency'  => $item->release_regist_agency,
                        'release_person_info'    => $item->release_person_info,
                        'release_doc_number'     => $item->release_doc_number,
                        'release_doc_summary'    => $item->release_doc_summary,
                        'undisputed_date' => $item->undisputed_date,
                        'undisputed_note' => $item->undisputed_note,
                        'bank_id' => $item->bank_id,
                        'van_ban_id' => $item->van_ban_id,
                        'cv_id' => $item->cv_id,
                        'cv_name' => $item->cv_name,
                        'created_at' => $item->created_at,
                        'updated_at' => $item->updated_at,
                        'deleted_note' => $item->deleted_note,
                        'merged' => '1',
                        'merge_content' => $item->duong_su_en  . ' ' . $item->texte_en,
                    ];
                    $this->insert_solr((object)$ar_solr, 99);
                }
            }
            $now = Carbon::now();
            $this->config['timeRun'] = Carbon::parse($now)->format('Y-m-d H:i:s');
            $this->config['timePast'] = Carbon::parse($now)->subMinutes(10)->format('Y-m-d H:i:s');
            $handle = fopen($path, 'w+');
            fputs($handle, json_encode($this->config));
            fclose($handle);

            return response()->json([
                'status'  => true,
                'message' => 'Hoàn thành',
            ]);
        }
        return response()->json([
            'status'  => false,
            'message' => 'Không tìm thấy dữ liệu stp',
        ]);
        // $this->sendMessageToTelegram('[⚠️]_[STP2(240)]_[SYNC]'.$past.'-'.$run.' đang có vấn đề!');
    }


    public function create(Request $request)
    {
        //        $id_vp = User::join('nhanvien', 'nhanvien.nv_id', '=', 'id')
        //            ->join('chinhanh', 'chinhanh.cn_id', '=', 'nhanvien.nv_vanphong')
        //            ->where('users.id', Sentinel::getUser()->id)->first()->cn_id;
        $id_vp = '2046';
        $kieuhd = Kieuhopdong::where('id_vp', $id_vp)->pluck('kieu_hd', 'id')->prepend(
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
        return view('page.suutra.create', compact('kieuhd', 'ccv', 'kieuDS'));
    }

    public function store(Request $request)
    {
    }
    public function sendMessageToTelegram($text)
    {
        $url = "https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN') . "/sendMessage?chat_id=" . env('TELEGRAM_CHAT_ID') . "&text=" . $text;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
    }
}