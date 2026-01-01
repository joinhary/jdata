<?php

namespace App\Http\Controllers;

use App\Models\HistorySearchModel;
use App\Models\SuuTraLogModel;
use App\Models\SuuTraModel;
use App\Models\User;
use Carbon\Carbon;
use App\Models\HistoryUchiModel;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use App\Models\ChiNhanhModel;
use App\Models\NhanVienModel;

class SuuTraLogController extends Controller
{
    public function index(Request $request)
    {
        
        $date = explode('-', $request->date);
        if ($date) {
            $created_from = $date[0] ?? null;
            $created_to = $date[1] ?? null;
        }
        $logs = SuuTraLogModel::orderByDesc('created_at');

       $tong = $logs->count();
        if ($created_from != '') {
            $logs->whereDate('created_at', '>=', Carbon::parse($created_from)->format('Y-m-d'));
        }

        if ($created_to != '') {
            $logs->whereDate('created_at', '<=', Carbon::parse($created_to)->format('Y-m-d'));
        }
        if ($request->so_hd) {
            $logs->where('so_hd', 'like', '%' . $request->so_hd . '%');
        }

        if ($request->suutra_id) {
            $logs->where('suutra_id', $request->suutra_id);
        }
        if($request->user_id){
            $logs->where('user_id', $request->user_id);  
        }
        $datasuutra = SuuTraModel::find($request->suutra_id);
        if ($datasuutra) {
            if ($datasuutra->uchi_id) {
                $logs->orwhere('uchi_id', $datasuutra->uchi_id);
            }
            if ($request->nv_id) {
                $logs->where('user_id', $request->nv_id);
            }
        }

        $count = $logs->count();
        $logs = $logs->paginate(20);
        $logs->map(function ($user) {
            return $user->user;
        });
        //        dd($logs->toArray());
        $creators = User::leftjoin('role_users', 'role_users.user_id', '=', 'users.id')
            ->leftjoin('roles', 'roles.id', '=', 'role_users.role_id')
            ->where('roles.slug', '!=', 'khach-hang')
            ->pluck('users.first_name', 'users.id');
        return view(
            'admin.suutra_log.index',
            compact('tong', 'creators', 'logs', 'count', 'created_from', 'created_to')
        );
    }

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

    function multi_strpos($haystack, $needles, $offset = 0)
    {

        foreach ($needles as $n) {
            if (strpos($haystack, $n, $offset) !== false)
                return strpos($haystack, $n, $offset);
        }
        return false;
    }
    public function show($id)
    {
        $log = SuuTraLogModel::find($id);
        $hoso = SuutraModel::find($log->suutra_id);
        $arr = [];
        return view('admin.suutra_log.show', compact('log','hoso'));
    }

    public function indexNew(Request $request)
    {
        try {

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
                HistorySearchModel::create(
                    [
                        'user_id' => Sentinel::getUser()->id,
                        'url' => urldecode($_SERVER['HTTP_REFERER']),
                        'client_ip' => $ipaddress,
                        'vp_id' => $id_vp,
                    ]
                );
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
                'suutranb.uchi_id'


            ];
            $status = self::check_status_server();
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
            if ($request->isOffice == "true") {

                $data = $data->where('sync_code', $code->code_cn);
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

                                if (is_int(self::multi_strpos($word, $findMe))) {
                                    $str_json_symbol[] = str_replace(['"', '*', '%'], "", $word);
                                } else {
                                    $str_json[] = str_replace(['"', '*', '%'], "", $word);
                                }
                            }
                            $likeTerm = (trim(str_replace($exactTerm, '', $getNangCao)));

                            foreach (explode(" ", $likeTerm) as $word) {

                                if (is_int(self::multi_strpos($word, $findMe))) {
                                    $str_json_symbol[] = str_replace(['"', '*', '%'], "", $word);
                                } else {
                                    $str_json[] = str_replace(['"', '*', '%'], "", $word);
                                }
                            }
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
                                $key = join(" ", $exactTerm);
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
                                                $where = 'contains(suutranb.texte,' . "'NEAR((" . $keyNormal . "), " . $lengh . ", MAX)'" . ')';
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

                                        //khong co *
                                        $whereExact = 'contains(suutranb.texte,' . "'NEAR((" . $key . "," . $likeTerm . "), MAX)'" . ')';
                                    }
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
                                    if (str_contains($likeTerm, "*")) {
                                        ///
                                        $normal = "";
                                        $reverse = "";
                                        foreach (explode(" ", $likeTerm) as $word) {
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
                                                $where = 'contains(suutranb.texte_en,' . "'NEAR((" . $keyNormal . "), MAX)'" . ')';
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

                                        $whereExact = 'contains(suutranb.texte_en,' . "'" . $key . "'" . ')';
                                    }
                                }
                            }


                            if ($whereExact) {
                                $data = $data->whereRaw($whereExact)->where($whereLike);
                            } else {
                                $data = $data->where($whereLike);
                            }
                            //$count=$data->total();
                        } else {
                            foreach (explode(" ", $getNangCao) as $word) {
                                if (is_int(self::multi_strpos($word, $findMe))) {
                                    $str_json_symbol[] = str_replace(['"', '*', '%'], "", $word);
                                } else {
                                    $str_json[] = str_replace(['"', '*', '%'], "", $word);
                                }
                            }
                            //khong trong nhay
                            if (str_contains($getNangCao, "*")) {
                                //co dau * khong trong nhay
                                $normal = "";
                                $reverse = "";
                                foreach (explode(" ", $getNangCao) as $word) {

                                    // $str_json[]=str_replace(['"','%','*'],'',$word);

                                    if (is_int(self::multi_strpos($word, $findMe))) {
                                        $str_json_symbol[] = str_replace(['"', '*', '%'], "", $word);
                                    } else {
                                        $str_json[] = str_replace(['"', '*', '%'], "", $word);
                                    }
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
                                        $where = 'contains(suutranb.texte,' . "'NEAR((" . $key . "), MAX,TRUE)'" . ')';
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
                                        $where = 'contains(suutranb.texte_en,' . "'NEAR((" . $key . "), MAX,TRUE)'" . ')';
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
                                $keyArray = [];
                                foreach (explode(" ", str_replace("%", "*", $getNangCao)) as $item) {
                                    $keyArray[] = '"' . $item . '"';
                                }
                                $key = join(",", $keyArray);
                                $lengh = 200;
                                if (strlen($getNangCao) != mb_strlen($getNangCao, 'utf-8')) {
                                    $where = 'contains(suutranb.texte,' . "'NEAR((" . $key . "), MAX,TRUE)'" . ')';
                                    $data = $data->whereRaw($where);
                                } else {
                                    $where = 'contains(suutranb.texte_en,' . "'NEAR((" . $key . "), MAX,TRUE)'" . ')';

                                    $data = $data->whereRaw($where);
                                }
                            }
                        }
                    } else {
                        foreach (explode(" ", $getNangCao) as $word) {

                            if (is_int(self::multi_strpos($word, $findMe))) {
                                $str_json_symbol[] = str_replace(['"', '*', '%'], "", $word);
                            } else {
                                $str_json[] = str_replace(['"', '*', '%'], "", $word);
                            }
                        }
                        //tim 1 word
                        if (str_contains($getNangCao, "*")) {

                            $key = "'" . '"' . strrev($getNangCao) . '"' . "'";

                            $where = 'contains(suutranb.texte_reverse,' . $key . ')';
                            $data = $data->whereraw($where);
                        } else {
                            $key = str_replace("%", "*", $getNangCao);
                            if (strlen($getNangCao) != mb_strlen($getNangCao, 'utf-8')) {
                                //co dau
                                $where = 'contains(suutranb.texte,' . "'" . $key . "'" . ')';
                                $data = $data->whereraw($where);
                            } else {
                                $key = '"' . $key . '"';
                                $where = 'contains(suutranb.texte_en,' . "'" . $key . "'" . ')';
                                $data = $data->whereraw($where);
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
                                $key = join(" ", $exactTerm);
                                if ($likeTerm) {
                                    if (strlen($likeTerm) == 4) {
                                        $lengh = 5;
                                    } elseif (strlen($likeTerm) == 9 || strlen($likeTerm) == 12) {
                                        $lengh = 500;
                                    }
                                    $whereExact = 'contains(suutranb.duong_su,' . "'NEAR((" . $key . "," . $likeTerm . "), " . $lengh . ",TRUE)'" . ')';
                                } else {
                                    $whereExact = 'contains(suutranb.duong_su,' . "'" . $key . "'" . ')';
                                }
                            } else {
                                if (strlen($likeTerm) == 4) {
                                    $lengh = 5;
                                } elseif (strlen($likeTerm) == 9 || strlen($likeTerm) == 12) {
                                    $lengh = 500;
                                }
                                //khong dau
                                $key = join(" AND ", $exactTerm);
                                //$whereExact='contains(suutranb.duong_su_en,' ."'". $key."'".')';
                                if ($likeTerm) {
                                    $whereExact = 'contains(suutranb.duong_su_en,' . "'NEAR((" . $key . "," . $likeTerm . "), " . $lengh . ",TRUE)'" . ')';
                                } else {
                                    $whereExact = 'contains(suutranb.duong_su_en,' . "'" . $key . "'" . ')';
                                }
                            }


                            if ($whereExact) {
                                $data = $data->whereRaw($whereExact)->where($whereLike);
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

                            $data = $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->paginate(20)->onEachSide(2);;
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
                                    $key = join(" ", $exactTerm);
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
                                            $whereExact = 'contains(suutranb.texte,' . "'NEAR((" . $key . "," . $likeTerm . ") ,MAX)'" . ')';
                                        }
                                    } else {

                                        // $whereExact = 'contains(suutranb.texte,' . "'" . $key . "'" . ')';


                                        $string = str_replace(["*", "%"], "", $getNangCao);
                                        $string = str_replace($str_json, "", $string);
                                        $string = str_replace(['"'], "", $string);
                                        foreach (explode(" ", $string) as $word) {
                                            $str_json[] = $word;
                                        }

                                        $string = join(",", explode(" ", trim($string)));
                                        $whereExact = 'contains(suutranb.texte,' . "'NEAR((" . $key . "," . $string . ") ,MAX)'" . ')';
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
                                $data = $data->select($array)->orderByDesc("suutranb.st_id")->paginate(20)->onEachSide(2);;
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
                                    $key = join(" ", $exactTerm);
                                    if ($likeTerm) {
                                        if (strlen($likeTerm) == 4) {
                                            $lengh = 5;
                                        } elseif (strlen($likeTerm) == 9 || strlen($likeTerm) == 12) {
                                            $lengh = 500;
                                        }
                                        $whereExact = 'contains(suutranb.duong_su,' . "'NEAR((" . $key . "," . $likeTerm . "), " . $lengh . ",TRUE)'" . ')';
                                    } else {
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

                                    $key = join(" AND ", $exactTerm);
                                    //$whereExact='contains(suutranb.duong_su_en,' ."'". $key."'".')';
                                    if ($likeTerm) {

                                        $whereExact = 'contains(suutranb.duong_su_en,' . "'NEAR((" . $key . "," . $likeTerm . "), " . $lengh . ",TRUE)'" . ')';
                                    } else {
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
            return redirect(route('indexSuutraNew'))->with('success', 'Vui lòng nhập lại đúng cú pháp tìm kiếm hoặc liên hệ hỗ trợ để được hướng dẫn!');
        }

        $loadTaiSan = false;
        $isPrevent = $request->prevent ?? false;
        return view('admin.suutra.indexNew', compact('isOffice', 'countOffice', 'str_json_symbol', 'str_json2_symbol', 'countPrevent', 'isPrevent', 'str_json2', 'count', 'data', 'getcoban', 'getNangCao', 'str_json', 'loadTaiSan'));
    }

    public function indexNewOther(Request $request)
    {
        $loadTaiSan = true;
        //        $index=SuuTraModel::reindex();
        $index = SuuTraModel::select('texte', 'duong_su')->first();

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
        $status = self::check_status_server();
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
                            $key = join(" ", $exactTerm);
                            if ($likeTerm) {
                                if (strlen($likeTerm) == 4) {
                                    $lengh = 5;
                                } elseif (strlen($likeTerm) == 9 || strlen($likeTerm) == 12) {
                                    $lengh = 500;
                                }
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
                                if (count(explode(' ', $likeTerm)) > 1) {

                                    $likeTerm = str_replace(" ", ",", $likeTerm);
                                    $whereExact = 'contains(suutranb.duong_su,' . "'NEAR((" . $likeTerm . "), " . $lengh . ",TRUE)'" . ')';
                                } else {
                                    $whereExact = 'contains(suutranb.duong_su,' . "'" . $likeTerm . "'" . ')';
                                }
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
        return view('admin.suutra.indexNew', compact('str_json2', 'count', 'data', 'getcoban', 'getNangCao', 'str_json', 'priority', 'loadTaiSan'));
    }

    public function printNew(Request $request)
    {
        try {

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
                HistorySearchModel::create(
                    [
                        'user_id' => Sentinel::getUser()->id,
                        'url' => urldecode($_SERVER['HTTP_REFERER']),
                        'client_ip' => $ipaddress,
                        'vp_id' => $id_vp,
                    ]
                );
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
                'suutranb.uchi_id'


            ];
            $status = self::check_status_server();
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
            if ($request->isOffice == "true") {

                $data = $data->where('sync_code', $code->code_cn);
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

                                if (is_int(self::multi_strpos($word, $findMe))) {
                                    $str_json_symbol[] = str_replace(['"', '*', '%'], "", $word);
                                } else {
                                    $str_json[] = str_replace(['"', '*', '%'], "", $word);
                                }
                            }
                            $likeTerm = (trim(str_replace($exactTerm, '', $getNangCao)));

                            foreach (explode(" ", $likeTerm) as $word) {

                                if (is_int(self::multi_strpos($word, $findMe))) {
                                    $str_json_symbol[] = str_replace(['"', '*', '%'], "", $word);
                                } else {
                                    $str_json[] = str_replace(['"', '*', '%'], "", $word);
                                }
                            }
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
                                $key = join(" ", $exactTerm);
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
                                                $where = 'contains(suutranb.texte,' . "'NEAR((" . $keyNormal . "), " . $lengh . ", MAX)'" . ')';
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

                                        //khong co *
                                        $whereExact = 'contains(suutranb.texte,' . "'NEAR((" . $key . "," . $likeTerm . "), MAX)'" . ')';
                                    }
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
                                    if (str_contains($likeTerm, "*")) {
                                        ///
                                        $normal = "";
                                        $reverse = "";
                                        foreach (explode(" ", $likeTerm) as $word) {
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
                                                $where = 'contains(suutranb.texte_en,' . "'NEAR((" . $keyNormal . "), MAX)'" . ')';
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

                                        $whereExact = 'contains(suutranb.texte_en,' . "'" . $key . "'" . ')';
                                    }
                                }
                            }


                            if ($whereExact) {
                                $data = $data->whereRaw($whereExact)->where($whereLike);
                            } else {
                                $data = $data->where($whereLike);
                            }
                            //$count=$data->total();
                        } else {
                            foreach (explode(" ", $getNangCao) as $word) {
                                if (is_int(self::multi_strpos($word, $findMe))) {
                                    $str_json_symbol[] = str_replace(['"', '*', '%'], "", $word);
                                } else {
                                    $str_json[] = str_replace(['"', '*', '%'], "", $word);
                                }
                            }
                            //khong trong nhay
                            if (str_contains($getNangCao, "*")) {
                                //co dau * khong trong nhay
                                $normal = "";
                                $reverse = "";
                                foreach (explode(" ", $getNangCao) as $word) {

                                    // $str_json[]=str_replace(['"','%','*'],'',$word);

                                    if (is_int(self::multi_strpos($word, $findMe))) {
                                        $str_json_symbol[] = str_replace(['"', '*', '%'], "", $word);
                                    } else {
                                        $str_json[] = str_replace(['"', '*', '%'], "", $word);
                                    }
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
                                        $where = 'contains(suutranb.texte,' . "'NEAR((" . $key . "), MAX,TRUE)'" . ')';
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
                                        $where = 'contains(suutranb.texte_en,' . "'NEAR((" . $key . "), MAX,TRUE)'" . ')';
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
                                $keyArray = [];
                                foreach (explode(" ", str_replace("%", "*", $getNangCao)) as $item) {
                                    $keyArray[] = '"' . $item . '"';
                                }
                                $key = join(",", $keyArray);
                                $lengh = 200;
                                if (strlen($getNangCao) != mb_strlen($getNangCao, 'utf-8')) {
                                    $where = 'contains(suutranb.texte,' . "'NEAR((" . $key . "), MAX,TRUE)'" . ')';
                                    $data = $data->whereRaw($where);
                                } else {
                                    $where = 'contains(suutranb.texte_en,' . "'NEAR((" . $key . "), MAX,TRUE)'" . ')';

                                    $data = $data->whereRaw($where);
                                }
                            }
                        }
                    } else {
                        foreach (explode(" ", $getNangCao) as $word) {

                            if (is_int(self::multi_strpos($word, $findMe))) {
                                $str_json_symbol[] = str_replace(['"', '*', '%'], "", $word);
                            } else {
                                $str_json[] = str_replace(['"', '*', '%'], "", $word);
                            }
                        }
                        //tim 1 word
                        if (str_contains($getNangCao, "*")) {

                            $key = "'" . '"' . strrev($getNangCao) . '"' . "'";

                            $where = 'contains(suutranb.texte_reverse,' . $key . ')';
                            $data = $data->whereraw($where);
                        } else {
                            $key = str_replace("%", "*", $getNangCao);
                            if (strlen($getNangCao) != mb_strlen($getNangCao, 'utf-8')) {
                                //co dau
                                $where = 'contains(suutranb.texte,' . "'" . $key . "'" . ')';
                                $data = $data->whereraw($where);
                            } else {
                                $key = '"' . $key . '"';
                                $where = 'contains(suutranb.texte_en,' . "'" . $key . "'" . ')';
                                $data = $data->whereraw($where);
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
                                $key = join(" ", $exactTerm);
                                if ($likeTerm) {
                                    if (strlen($likeTerm) == 4) {
                                        $lengh = 5;
                                    } elseif (strlen($likeTerm) == 9 || strlen($likeTerm) == 12) {
                                        $lengh = 500;
                                    }
                                    $whereExact = 'contains(suutranb.duong_su,' . "'NEAR((" . $key . "," . $likeTerm . "), " . $lengh . ",TRUE)'" . ')';
                                } else {
                                    $whereExact = 'contains(suutranb.duong_su,' . "'" . $key . "'" . ')';
                                }
                            } else {
                                if (strlen($likeTerm) == 4) {
                                    $lengh = 5;
                                } elseif (strlen($likeTerm) == 9 || strlen($likeTerm) == 12) {
                                    $lengh = 500;
                                }
                                //khong dau
                                $key = join(" AND ", $exactTerm);
                                //$whereExact='contains(suutranb.duong_su_en,' ."'". $key."'".')';
                                if ($likeTerm) {
                                    $whereExact = 'contains(suutranb.duong_su_en,' . "'NEAR((" . $key . "," . $likeTerm . "), " . $lengh . ",TRUE)'" . ')';
                                } else {
                                    $whereExact = 'contains(suutranb.duong_su_en,' . "'" . $key . "'" . ')';
                                }
                            }


                            if ($whereExact) {
                                $data = $data->whereRaw($whereExact)->where($whereLike);
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

                            $data = $data->select($array)->orderByDesc("ngan_chan")->orderByDesc("suutranb.st_id")->paginate(20)->onEachSide(2);;
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
                                    $key = join(" ", $exactTerm);
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
                                            $whereExact = 'contains(suutranb.texte,' . "'NEAR((" . $key . "," . $likeTerm . ") ,MAX)'" . ')';
                                        }
                                    } else {

                                        // $whereExact = 'contains(suutranb.texte,' . "'" . $key . "'" . ')';


                                        $string = str_replace(["*", "%"], "", $getNangCao);
                                        $string = str_replace($str_json, "", $string);
                                        $string = str_replace(['"'], "", $string);
                                        foreach (explode(" ", $string) as $word) {
                                            $str_json[] = $word;
                                        }

                                        $string = join(",", explode(" ", trim($string)));
                                        $whereExact = 'contains(suutranb.texte,' . "'NEAR((" . $key . "," . $string . ") ,MAX)'" . ')';
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
                                $data = $data->select($array)->orderByDesc("suutranb.st_id")->paginate(20)->onEachSide(2);;
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
                                    $key = join(" ", $exactTerm);
                                    if ($likeTerm) {
                                        if (strlen($likeTerm) == 4) {
                                            $lengh = 5;
                                        } elseif (strlen($likeTerm) == 9 || strlen($likeTerm) == 12) {
                                            $lengh = 500;
                                        }
                                        $whereExact = 'contains(suutranb.duong_su,' . "'NEAR((" . $key . "," . $likeTerm . "), " . $lengh . ",TRUE)'" . ')';
                                    } else {
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

                                    $key = join(" AND ", $exactTerm);
                                    //$whereExact='contains(suutranb.duong_su_en,' ."'". $key."'".')';
                                    if ($likeTerm) {

                                        $whereExact = 'contains(suutranb.duong_su_en,' . "'NEAR((" . $key . "," . $likeTerm . "), " . $lengh . ",TRUE)'" . ')';
                                    } else {
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
            return redirect(route('indexSuutraNew'))->with('success', 'Vui lòng nhập lại đúng cú pháp tìm kiếm hoặc liên hệ hỗ trợ để được hướng dẫn!');
        }

        $loadTaiSan = false;
        $isPrevent = $request->prevent ?? false;
        return view('admin.suutra.printNew', compact('str_json2', 'count', 'data', 'getcoban', 'getNangCao', 'str_json'));
    }
    function sync_history()
    {
        $data = HistoryUchiModel::whereNull('done')->limit(400);

        foreach ($data->get() as $i) {
            SuuTraLogModel::create([
                'uchi_id' => $i->tpid,
                'office_code' => $i->client_info,
                'execute_person' => $i->execute_person,
                'execute_content' => $i->execute_content,
                'contract_content' => $i->contract_content,
                'so_hd' => $i->contract_number,
                'created_at' => $i->execute_date_time
            ]);
            HistoryUchiModel::where('hid', $i->hid)->update(['done' => 1]);
        }

        //bat dau
        return view('admin.suutra.reload');
    }
}
