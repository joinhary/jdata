<?php


use App\Http\Controllers\SuuTraController;
// use Exception;
use App\Models\User;

use Carbon\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Ixudra\Curl\Facades\Curl;
use App\Models\Bank;
use App\Http\Controllers\AppController;
use App\Http\Controllers\SolrThongBaoController;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Models\SuuTraModel;
use App\Models\SuuTraLogModel;
use App\Models\NhanVienModel;
use App\Models\ChiNhanhModel;
use App\Models\VanBanModel;


use App\Http\Controllers\SyncSuutranbController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

include('spp.php');
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'api'], function () {
    //    Route::get('api_create_log','UsersController@create_log');
});
Route::get('vi-to-en', function () {
    $data = SuutraModel::select('st_id', 'duong_su', 'texte')->get();
    foreach ($data as $item) {
        SuutraModel::where('st_id', $item->st_id)->update([
            'texte' => \App\Http\Controllers\SuutraController::convert_vi_to_en($item->texte),
            'duong_su' => \App\Http\Controllers\SuutraController::convert_vi_to_en($item->duong_su),

        ]);
    }
    return ['status' => true];
});
Route::post('input-data', function (Request $request) {
    $data = $request->toArray();
    $data = AppController::convert_unicode($data);
    $request = new Request();
    $request->replace($data->toArray());
    $now = Carbon::now()->format('Y-m-d');
    $vp = $request->vp;
    $pic = null;
    $file_name = '';
    $file_path = '';
    //$time = date("Y", strtotime($request->notary_date));
    $sohd = $request->contract_number;
    $sync_code = $request->code_cn;
    $id_lienket = $request->id_ccv;
    $van_ban_id = $request->van_ban_id;
    $tenVanBan = $request->contract_template;
    //        $texte = SuuTraController::cleanSpaces($request->noidung) . ',/.' . SuuTraController::cleanSpaces($request->taisan);
    //        $texte_en = $this->convert_vi_to_en($texte);
    //        $duong_su = SuuTraController::cleanSpaces($request->duongsua) . ',/.' . SuuTraController::cleanSpaces($request->duongsub) . ',/.' . SuuTraController::cleanSpaces($request->duongsuc);
    //        $duong_su_en = $this->convert_vi_to_en($duong_su);

    $texte = SuuTraController::cleanSpaces($request->noidung);
    $texte_en = SuuTraController::convert_vi_to_en($texte);
    ///
    $duong_su_index = "";
    $duong_su_draw = preg_replace('/[^\p{L}\p{N}\s]/u', '', $request->duongsu);
    $duong_su_draw = str_replace("\n", ' ', $duong_su_draw);
    $duong_su_draw = explode(' ', SuuTraController::convert_vi_to_en($duong_su_draw));

    $texte_reverse = strrev($texte_en);
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

    //
    $duong_su = SuuTraController::cleanSpaces($request->duongsu);
    $duong_su_en = SuuTraController::convert_vi_to_en($duong_su);

    $ten_ccv_master = $request->ccv;
    $ten_vp_master =  $request->vanphong;
    $realName = "";

    // nhap giao dich thuong
    if (SuuTraModel::where('so_hd', $sohd)->first() != null && SuuTraModel::where(
        'so_hd',
        $sohd
    )->first()->sync_code == $sync_code) {
        $mess = 'Ma hop dong da ton tai!' . '____Ma hd:' . $sohd . '____Code:' . $sync_code;
        return ['status' => 'failed', 'message' => $mess];
    } else {

        $vanban = VanBanModel::find($request->van_ban_id);
        $tenVB = $tenVanBan;
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
        if ($request->get('so-cc-cu')) {
            $cancel_status = 0;
            if ($type_cancel == 1) {
                $cancel_status = 1;
                $cancel_description = "Hủy hợp đồng số " . $request->get('so-cc-cu');
            } elseif ($type_cancel == 2) {
                $cancel_description = "Hợp đồng phụ lục của đồng số " . $request->get('so-cc-cu');
            } else {
                $cancel_description = "Sửa đổi, bổ sung hợp đồng số " . $request->get('so-cc-cu');
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

        $notary_date = Carbon::createFromFormat('d/m/Y', $request->notary_date)->format('Y-m-d');

        DB::beginTransaction();
        try {
            $cv_name = '';
            if ($request->id_cv && $nhanvien = NhanVienModel::find($request->id_cv)) {
                $cv_name = $nhanvien->nv_hoten;
            }

            $duong_su_index = mb_convert_encoding($duong_su_index, 'UTF-8');
            $suutra = SuuTraModel::create([
                'ccv' => $request->id_ccv,
                'ngay_cc' => $notary_date,
                'so_hd' => $sohd,
                'ten_hd' => $tenVB,
                'loai' => $vanban->vb_kieuhd,
                'van_ban_id' => $request->van_ban_id,
                'texte' => $texte,
                'texte_en' => $texte_en,
                'duong_su' => $duong_su,
                'duong_su_en' => $duong_su_en,
                'ngan_chan' => 0,
                'vp' => $vp,
                'ccv_master' => $ten_ccv_master,
                'vp_master' => $ten_vp_master,
                'ngay_nhap' => $now,
                'cancel_status' => $cancel_status,
                'cancel_description' => $cancel_description,
                'nguoinhap' => $request->input_person,
                'so_cc_cu' => $request->get('so-cc-cu'),
                'ma_phan_biet' => 'D',
                'duong_su_index' => $duong_su_index,
                'sync_code' => $sync_code,
                'texte_reverse' => $texte_reverse,
                'property_info' => $property_info,
                'transaction_content' => $transaction_content,
                'contract_period' => $request->contract_period,
                'bank_id' => $request->bank_id,
                'cv_id' => $request->id_cv,
                'cv_name' => $cv_name,
                'merged' => '1',
                'merge_content' => $duong_su_en  . ' ' . $texte_en,

            ]);
            $insert_solr = new SuuTraController();
            $insert_solr->insert_solr(SuutraModel::where('st_id', $suutra->st_id)->first(), 15);

            $update = SuutraModel::where('st_id', $suutra->st_id)->first()->update([
                'ma_dong_bo' => $sync_code . "_J_" . $suutra->st_id
            ]);
            if ($request->get('so-cc-cu')) {
                $relatedSuutra = SuuTraModel::where('so_hd', $request->get('so-cc-cu'))->where('sync_code', $sync_code)->first();
                if ($relatedSuutra) {
                    $relatedSuutra->update([
                        'type_cancel' => $type_cancel,
                        'cancel_description' => $relatedSuutra->cancel_description . PHP_EOL . $cancel_description_other,
                    ]);
                }
            }
            $suutra_log = SuuTraLogModel::create([
                'suutra_id' => $suutra->st_id,
                'log_content' => json_encode($suutra),
                'user_id' => $request->input_person,
                'so_hd' => $suutra->so_hd,
                'execute_content' => "Đăng ký"
            ]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            return $e;
        }
    }
    return ['status' => 'success'];
});
Route::get('check-database', function (Request $request) {
    //    if(!$request->token==env('TOKEN')){
    //        return ['status'=>false,'token'=>$request->token];
    //    }
    $servername = ENV("DB_MYSQL_HOST");
    $database = ENV("DB_MYSQL_DATABASE");
    $username = ENV("DB_MYSQL_USERNAME");
    $password = ENV("DB_MYSQL_PASSWORD");

    // Create connection
    try {
        $conn = new mysqli($servername, $username, $password, $database);
        // Check connection
        if ($conn->connect_error) {
            return ['status' => false, 'message' => "Connection failed: " . $conn->connect_error];
        }
        return ['status' => true, 'message' => "Connection success"];
    } catch (Exception $e) {
        return ['status' => false, 'message' => $e->getMessage()];
    }
});

Route::get('change-login-key', function (Request $request) {
    if (!$request->token == env('TOKEN')) {
        return ['status' => false, 'token' => $request->token];
    }
    $email = $request->email;
    $user = User::where("email", "=", $email)->first();
    $nhanvien = NhanVienModel::find($user->id);
    ChiNhanhModel::find($nhanvien->nv_vanphong)->update([
        'login_code' => $request->code
    ]);
    return ['status' => true];
});
Route::get('get-template', function (Request $request) {
  // dd(1);
    $vanphong = $request->vanphong;
    $vanban = VanBanModel::where('id_vp', '=', 2020)->pluck('vb_nhan', 'lien_ket');
    return $vanban;
});
Route::get('get-bank', function (Request $request) {
    $bank = Bank::pluck('name', 'id');
    return $bank;
});
Route::get('backup', function (Request $request) {
    $code = $request->code;
    $path = storage_path() . "/app/" . $code . ".json";
    $json = json_decode(file_get_contents($path), true);
    $this->config = $json;
    $data = SuuTraModel::where('backup', '=', 0)->whereBetween('suutranb.created_at', [$this->config['runpast'], $this->config['runtime']])
        ->orderby('st_id', 'desc')
        ->get();
    return $data;
});

Route::get('fix-path', function () {
    $data = SuuTraModel::whereNotNull('uchi_id_ngan_chan')->get();
    foreach ($data as $item) {
        $path = explode('\\', $item->picture);
        if (count($path) > 0) {
            //dd('["'.$path[count($path)-1].'"]');
            SuuTraModel::where("st_id", $item->st_id)->update([
                'picture' => '["' . $path[count($path) - 1] . '"]',
                'real_name' => '["' . $item->real_name . '"]'

            ]);
        }

        //$path = storage_path().'/'.'app'.'/'.'suutra'.'/'.$img;
        //      if (file_exists($path)) {
        //        return response()->download($path);
        //  }
    }
});
Route::get('count', function () {
    $array = file('uchi_data.txt');
    $code = trim(str_replace('\r\n', '', json_encode($array)));
    $code = json_decode($code);
    $notExist = [];
    $jdata = SuuTraModel::where('sync_code', 'like', 'TMH')->whereNull('deleted_at')->pluck('uchi_id')->toArray();
    dd(array_diff_assoc($jdata, array_unique($jdata)));

    try {
        $notExist = collect($code)->filter(function ($item) use ($jdata) {
            return !in_array($item, $jdata);
        });
        dd(json_encode(array_values($notExist->toArray())));
    } catch (Exception $e) {
        return $e;
    }
    return ['data' => $notExist];
});

Route::get('count/jdata', function () {
    $array = file('uchi_data.txt');
    $code = trim(str_replace('\r\n', '', json_encode($array)));
    $code = json_decode($code);

    $notExist = [];
    $jdata = SuuTraModel::where('sync_code', 'like', 'TMH')->whereNull('deleted_at')->pluck('uchi_id');
    //dd(array_diff_assoc($jdata, array_unique($jdata)));

    try {
        $notExist = $jdata->filter(function ($item) use ($code) {
            return !in_array($item, $code);
        });
        dd(json_encode(array_values($notExist->toArray())));
    } catch (Exception $e) {
        return $e;
    }
    return ['data' => $notExist];
});
Route::get('check-file', function () {
    $data = SuuTraModel::whereNotNull('uchi_id_ngan_chan')->get();
    foreach ($data as $item) {
        $img = json_decode($item->picture);
        //$name=json_decode($item->real_name);
        if ($img && count($img) > 0) {
            foreach ($img as $i) {
                $path = storage_path() . '/' . 'app' . '/' . 'suutra' . '/' . $i;
                if (!file_exists($path)) {
                    SuuTraModel::where('st_id', $item->st_id)->first()->update([
                        'file' => 1
                    ]);
                }
            }
        }
    }
});
Route::post('upload-file', function (Request $request) {
    if ($request->has('image')) {
        $file = $request->image;
        $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move('uploads/', $fileName);
        return ['status' => true, 'link' => asset('uploads/' . $fileName)];
    } else {
        return ['status' => false, 'link' => ''];
    }
})->name('uploadFile');



//----------Tìm file thiếu-------------------//
//tìm file khác từ 240 với 59
Route::get('file', function () {

    $file59 = file('ma_dong_bo_59.txt', FILE_IGNORE_NEW_LINES);
    $file240 = file('ma_dong_bo_240.txt', FILE_IGNORE_NEW_LINES);
    $diff = array_diff($file240, $file59);
    echo count($diff);
    $txt = json_encode($diff);
    $myfile = fopen("lost1.txt", "a") or die("Unable to open file!");
    fwrite($myfile, $txt);
    fclose($myfile);
});
Route::get('/dump_lost', [ApiController::class, 'dump_lost'])->name('dump_lost');
Route::post('/get_dump_lost', [ApiController::class, 'get_dump_lost'])->name('get_dump_lost');
Route::get('/support-lost-file', [ApiController::class, 'supportLostFile240'])->name('supportLostFile240');
Route::get('/support-file', [ApiController::class, 'getLostFile'])->name('getLostFile');
//------------------------------------------//
Route::get('/search-tbc', [SolrThongBaoController::class, 'searchApi']);
Route::get('/check-ma-dong-bo', [ApiController::class, 'checkMaDongBo']);
Route::post('/sync-suutranb', [SyncSuutranbController::class, 'sync']);