<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use Sentinel;
use Carbon\Carbon;
use App\Models\SuuTraModel;
use Illuminate\Http\Request;
use App\Models\ChiNhanhModel;
use App\Models\NhanVienModel;
use Ixudra\Curl\Facades\Curl;
use App\Exports\Export_BDS_sum;
use App\Exports\Export_BDS_moth;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AppController;

class ApiController extends Controller
{
    public static function insert_solr($data)
    {
        // $create = new SolrCheckModel;
        // $create->st_id = $data->st_id;
        // $create->save();
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
            <query> ma_dong_bo:' . $st_id . '</query>   
         </delete>  ',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/xml'
            ),
        ));
        $response = curl_exec($curl);
        // dd($response);
        curl_close($curl);
    }

    public function merge_content()
    {
        set_time_limit(0);
        $end_of_loop = (int)file_get_contents("C:/laragon6/www/jdata2025/public/solr_error/end_of_solr.txt");
        $data = SuuTraModel::where('st_id', '>=', $end_of_loop)->whereNull('deleted_at')->orderBy('st_id', 'asc')->limit(500)->get();
        foreach ($data as $item) {
            $item->merge_content = $item->duong_su_en . ' ' . $item->texte_en;
            $item->save();
            $file = fopen("C:/laragon6/www/jdata2025/public/solr_error/end_of_solr.txt", "w") or die("Unable to open file!");
            //write json to file
            $id = $item->getAttributes();
            fwrite($file,  $id['st_id'] . "\n");
            //close file
            fclose($file);
        }
        return view('admin.suutra.reload');
    }
    public function sys_lost()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://stp2.hoicongchungviencantho.org/api/get_dump_lost',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));
        $response = curl_exec($curl);
        curl_close($curl);
    }

    public function dump_lost()
    {

        $file = (string)file_get_contents("C:/laragon6/www/jdata2025/public/lost1.txt");
        // $file = explode(' ', $file);
        $file = json_decode($file);
        // $file = array_chunk($file, 2000);
        $result = [];
        foreach ($file as $item) {
            $data = SuuTraModel::where('ma_dong_bo', $item)->get();
            $data = $data->toArray();
            array_push($result, $data);
        }
        $myfile = fopen("file240.txt", "a") or die("Unable to open file!");
        $result = json_encode($result);
        fwrite($myfile, $result);
        fclose($myfile);
        return $result;
    }

    public function remove_2cham()
    {
        try {
            set_time_limit(0);
            $end_of_loop = (int)file_get_contents("C:/laragon6/www/jdata2025/public/solr_error/end_of_solr.txt");
            $data = SuuTraModel::where('st_id', '>=', $end_of_loop)->whereNull('deleted_at')->orderBy('st_id', 'asc')->limit(500)->get();
            foreach ($data as $item) {
                $item->duong_su = str_replace(':', ' ', $item->duong_su);
                $item->duong_su_en = str_replace(':', ' ', $item->duong_su_en);
                $item->texte = str_replace(':', ' ', $item->texte);
                $item->texte_en = str_replace(':', ' ', $item->texte_en);
                $item->save();
                $id = $item->getAttributes();
                $file = fopen("C:/laragon6/www/jdata2025/public/solr_error/end_of_solr.txt", "w") or die("Unable to open file!");
                fwrite($file, $id['st_id'] . "\n");
                fclose($file);
            }
            return view('admin.suutra.reload');
        } catch (QueryException $e) {
            $file = fopen("C:/laragon6/www/jdata2025/public/solr_error/error.txt", "a") or die("Unable to open file!");
            $id = $item->getAttributes();
            fwrite($file, "*" . $id['st_id'] . "\n");
            fclose($file);
        }
    }
    public function getLostFile()
    {
        $this->supportLostFile240();
        $file59 = file_get_contents(storage_path() . "/json/ma_dong_bo_59.txt", true);
        $file240 = file_get_contents(storage_path() . "/json/ma_dong_bo_240.txt", true);
        //return 
        $file59 = explode(" ", $file59);
        $file240 = explode(" ", $file240);
        $diff = array_diff($file240, $file59);
        $txt = json_encode($diff);
        $myfile = fopen("lost1.txt", "a") or die("Unable to open file!");
        fwrite($myfile, $txt);
        fclose($myfile);
        echo count($diff);
    }
    public function get_dump_lost()
    {
        set_time_limit(0);
        $link = "http://stp2.hoicongchungviencantho.org/dump_lost";
        $stp = Curl::to($link)->get();
        $stp = json_decode($stp);
        // dd($stp);
        // $file = (string)file_get_contents("C:/xampp/htdocs/aemSql/public/file59.txt");
        // $file = json_decode($file);
        // $stp =$file;
        if ($stp) {
            foreach ($stp as $data) {
                if ($data) {
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
                                'release_file_name'      => $item->release_file_name ?? '',
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
                            ]);
                            $this->insert_solr($suutra);
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
                                'release_file_name'      => $item->release_file_name ?? '',
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
                                'merge_content' => $item->duong_su_en . ' ' . $item->texte_en
                            ]);
                            $this->delete_solr($suutra_old->ma_dong_bo);
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
                                'release_file_name'      => $item->release_file_name ?? '',
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
                            $this->insert_solr((object)$ar_solr);
                        }
                    }
                }
            }

            return response()->json([
                'status'  => true,
                'message' => 'Hoàn thành',
            ]);
        }
        return response()->json([
            'status'  => false,
            'message' => 'Không tìm thấy dữ liệu stp',
        ]);
    }
    public function supportLostFile240()
    {
        $path = storage_path() . "/json/local_support_240to59.json";
        $json = json_decode(file_get_contents($path), true);
        $this->config = $json;
        $from = Carbon::parse($this->config['timeTo'])->subMinutes(10)->format('Y-m-d H:i:s');
        $to = Carbon::parse($this->config['timeTo'])->format('Y-m-d H:i:s');
        $this->config['timeFrom'] =  $to;
        $this->config['timeTo'] = Carbon::now()->format('Y-m-d H:i:s');
        $handle = fopen($path, 'w+');
        fputs($handle, json_encode($this->config));
        fclose($handle);
        //ghi 2 file
        return $this->returnDataToSupport($from, $to);
    }

    public function returnDataToSupport($timeFrom, $timeTo)
    {
        // $timePast = $request->timePast;
        // $timeRun = $request
        //     ->timeRun;
        // if ($request->token != "aboqor") {
        //     return ['status' => false, 'message' => 'token not found'];
        // }
        // // dd(1);
        $data = SuuTraModel::query();

        if ($timeFrom != '' && $timeFrom != '') {
            //file 240----------------------
            $timeFrom = "2023-03-17 05:00:00";
            $timeTo = "2023-03-19 23:30:00";
            $data = $data->whereBetween('suutranb.updated_at', [$timeFrom, $timeTo])->pluck('ma_dong_bo');
            // $data = $data->where('ngan_chan',3)->whereNull('deleted_at')->pluck('ma_dong_bo');
            $path = storage_path() . "/json/ma_dong_bo_240.txt";
            $data = implode(" ", $data->toArray());
            $handle = fopen($path, 'w+');
            fputs($handle, $data);
            fclose($handle);
            //file 59----------------------
            $this->getDataStealFrom59($timeFrom, $timeTo);
        } else {
            return false;
        }
        return true;
    }
    public function getDataStealFrom59($timeFrom, $timeTo)
    {
        set_time_limit(0);
        $timeFrom = "2023-03-17 05:00:00";
        $timeTo = "2023-03-19 23:30:00";
        $timeFrom = Carbon::createFromFormat('Y-m-d H:i:s', $timeFrom)->format('Y-m-d H:i:s');
        $timeTo = Carbon::createFromFormat('Y-m-d H:i:s', $timeTo)->format('Y-m-d H:i:s');
        $link = "http://stp2.hoicongchungviencantho.org/api/support-laets-59";
        $response = Curl::to($link)
            ->withData(array('timeFrom' => $timeFrom, 'timeTo' => $timeTo))
            ->get();
        if ($response) {
            echo 'data' . $response;
            $path = storage_path() . "/json/ma_dong_bo_59.txt";
            $handle = fopen($path, 'w+');
            fputs($handle, $response);
            fclose($handle);
        } else {
            return 'Không có data từ 59';
        }
    }
    public function checkMaDongBo(Request $request)
    {
        // Fetch 100 rows from table1 in db1
        $rowsDb1 = DB::connection('db1')->table('suutranb')
            ->select('ma_dong_bo')
            ->limit(10)
            ->get();

        // Extract `ma_dong_bo` values as an array
        $maDongBoDb1 = $rowsDb1->pluck('ma_dong_bo')->toArray();

        // Fetch matching rows from table2 in db2
        $rowsDb2 = DB::connection('db2')->table('suutranb')
            ->whereIn('ma_dong_bo', $maDongBoDb1)
            ->select('ma_dong_bo')
            ->get();

        // Extract `ma_dong_bo` from db2
        $maDongBoDb2 = $rowsDb2->pluck('ma_dong_bo')->toArray();

        // Identify missing `ma_dong_bo` values
        $missingMaDongBo = array_diff($maDongBoDb1, $maDongBoDb2);

        // Format response
        return response()->json([
            'db1_count' => count($rowsDb1),
            'db2_count' => count($rowsDb2),
            'missing_count' => count($missingMaDongBo),
            'missing_ma_dong_bo' => array_values($missingMaDongBo), // reindex array for JSON response
        ]);
    }
}