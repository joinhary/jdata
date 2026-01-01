<?php

namespace App\Http\Controllers;

use App\Models\BankModel;
use Illuminate\Http\Request;
use App\Models\SuuTraModel;
use App\Http\Controller\SuuTraController;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Factory;
use Sentinel;
use App\Models\NhanVienModel;
use App\Models\ChiNhanhModel;
use \Solarium\Client;
use App\Models\HistorySearchModel;



class Solr_Basic_Print extends Controller
{
    protected $client;

    public function __construct(\Solarium\Client $client)
    {
        //$client Solrium Client
        $this->client = $client;
        $count_ngan_chan = 0;
        $count_total = 0;
        $this->count_total = $count_total;
        $this->count_ngan_chan = $count_ngan_chan;
    }
    public static function curl_insert($data)
    {
        $getNumFounded = 0;
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
            CURLOPT_POSTFIELDS => $data->toJson(),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        if (json_decode($response)->responseHeader->status == 0) {
            $data->update(['added' => 1]);
        };
        curl_close($curl);
    }
    public function json_loop()
    {
        //read file from local
        $data = SuuTraModel::query()->limit(10)->get();
        //insert data to solr
        foreach ($data as $key => $value) {
            $this->curl_insert($value);
        }
    }
    public function paginate($items, $perPage = 100, $page = null, $options = [], $total)
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items, $total, $perPage, $page, $options);
    }
    public function ping()
    {
        // create a ping query
        // $ping = $this->client->createPing();

        // // execute the ping query
        // try {
        //     $this->client->ping($ping);
        //     return response()->json('OK');
        // } catch (\Exception $e) {
        //     return response()->json('ERROR', 500);
        // }
    }
    public function fetchData($resultset)
    {
        $data = [];
        $role = Sentinel::check();
        if($role){
            $user_id =  $role->getAttributes();
            $user_id = $user_id['id'];
        }else{
            return view('admin.login');
        }
        $vpcc_id = NhanVienModel::where('nv_id', $user_id)->first()->nv_vanphong;
        $cn_ndd = ChiNhanhModel::where('cn_id', (int)$vpcc_id)->first()->code_cn;
        $code_cn = ChiNhanhModel::where('cn_id', (int)$vpcc_id)->first()->code_cn;
        $test_list = '';
        foreach ($resultset as $document) {
            $test_list .= $document->st_id. ', ';
            $id_string = rtrim($test_list, ', ');
               // $data = $document->getFields();
          
            $data[] = [
                'duong_su' => $document->duong_su,
                'texte' => $document->texte,
                'duong_su_en' => $document->duong_su_en,
                'texte_en' => $document->texte_en,
                'ngay_nhap' => $document->ngay_nhap,
                'ngay_cc' => $document->ngay_cc,
                'so_hd' => $document->so_hd,
                'ten_hd' => $document->ten_hd,
                'ngay_chan' => $document->ngay_chan,
                'vp_master' => $document->vp_master,
                'ccv_master' => $document->ccv_master,
                'cancel_description' => $document->cancel_description,
                'contract_period' => $document->contract_period,
                'ngan_chan' => $document->ngan_chan,
                'sync_code' => $document->sync_code,
                'created_at' => $document->created_at,
                'updated_at' => $document->updated_at,
                'st_id' => $document->st_id,
                'release_doc_number' => $document->release_doc_number,
                'release_doc_date' => $document->release_doc_date,
                'deleted_note' => $document->deleted_note,
                'uchi_id' => $document->uchi_id,
                'picture' => $document->picture,
                'real_name' => $document->real_name,
                'release_file_path ' => $document->release_file_path,
                'is_update' => $document->is_update,
                'note' => $document->note,
                'ccv'=> $document->note,
                'undisputed_date'=> $document->undisputed_date,
                'undisputed_note'=> $document->undisputed_note,

            ];
        }
        //orderby ngay_nhap
        $data = collect($data);
        //count items
        return $data;
    }
    public function sortIndex($data, $a, $b)
    {

        $clause1 = strpos($data, $a);
        $clause2 = strpos($data, $b);
        if ($clause1 < $clause2) {
            return true;
        } else {
            return false;
        }
    }
    public function fetchData_test($resultset, $s1, $s2)
    {
        $data = [];
        foreach ($resultset as $document) {
            if ($this->sortIndex(implode(" ", $document->duong_su), $s1, $s2)) {
                $data[] = [
                    'duong_su' => $document->duong_su,
                    'texte' => $document->texte,
                    'duong_su_en' => $document->duong_su_en,
                    'texte_en' => $document->texte_en,
                    'ngay_nhap' => $document->ngay_nhap,
                    'ngay_cc' => $document->ngay_cc,
                    'so_hd' => $document->so_hd,
                    'ten_hd' => $document->ten_hd,
                    'ngay_chan' => $document->ngay_chan,
                    'vp_master' => $document->vp_master,
                    'ccv_master' => $document->ccv_master,
                    'is_update' => $document->is_update,
                    'note' => $document->note,
                    'undisputed_date'=> $document->undisputed_date,
                    'undisputed_note'=> $document->undisputed_note,

                ];
            } else {
                continue;
            }
        }
        //count items
        return $data;
    }

    public function loopData($timchinhxac, $search, $option)
    {
        foreach ($timchinhxac as $key => $val) {
            if ($option == 1) {
                if (strpos($val['duong_su'][0], (string)$search)) {
                    $datas[] = [
                        'duong_su' => $val['duong_su'],
                        'texte' => $val['texte'],
                    ];
                    // array_push($timchinhxac, $datas);
                    $timchinhxac = array_merge($datas);
                }
            } elseif ($option == 2) {
                if (strpos($val['texte'][0], (string)$search)) {
                    $datas[] = [
                        'duong_su' => $val['duong_su'],
                        'texte' => $val['texte'],
                    ];
                    // array_push($timchinhxac, $datas);
                    $timchinhxac = array_merge($datas);
                }
            }
        }
        return $timchinhxac;
    }
    public function XuLyTimKiem_duong_su2($data, $option, $request)
    {
        if (preg_match_all('/\"([^\"]*?)\"/', $data, $matches)) {
            $search1 = array_unique($matches[0]);
            $search1 = implode('', $matches[0]);
            $likeTerm = (trim(str_replace($search1, '', $data)));
            $search1 = str_replace('"', '', $search1);
            //Lọc lấy phần phía sau
            $data = str_replace($search1, '', $data);
            //phần sau ""
            $search2 = explode(' ', $likeTerm);
            $query = $this->client->createSelect();
            if ($option === 1) {
                $string_query = 'duong_su:' . '(' . '"' . $search1 . '"' . ')';
            } else {
                $string_query = 'duong_su_en:' . '(' . '"' . $search1 . '"' . ')';
            }
            foreach ($search2 as $key => $val) {
                //append to query string
                if ($val != "" && $option == 1) {
                    $sub_string_query =  ' AND duong_su:' . '(' . '"' . $search1 . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } elseif ($val != "" && $option == 2) {
                    $sub_string_query =  ' AND duong_su_en:' . '(' . '"' . $search1 . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } else {
                    continue;
                }
            }
            $page = $request->page ?? 1;
            $numberInPage = 20;
            $query->setQuery($string_query)->setQueryDefaultOperator('AND')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(0)->setRows(1000);
            $resultset = $this->client->select($query);
            $count = $resultset->getNumFound();

            $data = $this->fetchData($resultset);
            //oday
            $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
        } else {
            $data = [];
        }
        return [$data, $count];
    }

    public function XuLyTimKiem_duong_su($data, $option, $request)
    {

        if (preg_match_all('/\"([^\"]*?)\"/', $data, $matches)) {

            $search1 = array_unique($matches[0]);
            if (count($search1) > 1) {
                $replace_str = $search1[0];
                $search1 = implode("", $matches[0]);
                $likeTerm = (trim(str_replace($replace_str, '', $data)));
                $likeTerm = str_replace('"', '', $likeTerm);
                //còn cái case là nó còn có phần sau
                $search1 =  $replace_str;
            } else {
                $search1 = implode('', $matches[0]);
                $likeTerm = (trim(str_replace($search1, '', $data)));
            }
            if ($likeTerm == "") {
                return $this->XuLyTimKiem_duong_su2($data, $option, $request);
            }
            $search1 = str_replace('"', '', $search1);
            //Lọc lấy phần phía sau
            $data = str_replace($search1, '', $data);
            //phần sau ""
            $search2 = explode(' ', $likeTerm);
            $query = $this->client->createSelect();
            if ($option === 1) {
                $string_query = '{!complexphrase} duong_su:' . '(' . '"' . $search1 . '"' . ')';
            } else {
                $string_query =  '{!complexphrase} duong_su_en:' . '(' . '"' . $search1 . '"' . ')';
            }
            foreach ($search2 as $key => $val) {
                if ($val && $option == 1) {
                    $sub_string_query =   ' AND duong_su:' . '"' . $search1  . ' *' . $val . '*' . '"~10000';
                    $string_query .= $sub_string_query;
                } elseif ($val && $option == 2) {
                    $sub_string_query =   ' AND duong_su_en:' . '"' . $search1  . ' *' . $val . '*' . '"~10000';
                    $string_query .= $sub_string_query;
                } else {
                    continue;
                }
            }
            // $string_query =  ' {!complexphrase} duong_su:' . '(' . '"' . $search1 . '"' . ') AND duong_su:' . '"' . $search1  . ' *' . $val . '*' . '"' . '~1000';
            // $string_query=preg_replace('/\*+/', '*', $string_query);
            // dd($string_query);
            $page = $request->page;
            $numberInPage = 20;
            $query->setQuery($string_query)->setQueryDefaultOperator('AND')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(0)->setRows(1000);
            $resultset = $this->client->select($query);
            $count = $resultset->getNumFound();
            $data = $this->fetchData($resultset);
            //oday
            $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
        } else {
            $data = [];
        }
        return [$data, $count];
    }

    public function XuLyTimKiem_tai_san2($data, $option, $request)
    {
        if (preg_match_all('/\"([^\"]*?)\"/', $data, $matches)) {
            //phần trong ""
            $search1 = implode('', $matches[0]);
            $search1 = str_replace('"', '', $search1);
            //Lọc lấy phần phía sau
            $data = str_replace($search1, '', $data);
            //phần sau ""
            $search2 = explode(' ', $data);
            $query = $this->client->createSelect();
            if ($option === 1) {
                $string_query = 'texte:' . '(' . '"' . $search1 . '"' . ')';
            } else {
                $string_query = 'texte_en:' . '(' . '"' . $search1 . '"' . ')';
            }
            foreach ($search2 as $key => $val) {
                //append to query string
                if ($val != "" && $option == 1) {
                    $sub_string_query =  ' AND texte:' . '(' . '"' . $search1 . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } elseif ($val != "" && $option == 2) {
                    $sub_string_query =  ' AND texte_en:' . '(' . '"' . $search1 . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } else {
                    continue;
                }
            }
            $page = $request->page ?? 1;;
            $numberInPage = 20;
            $query->setQuery($string_query)->setQueryDefaultOperator('AND')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(0)->setRows(1000);
            $resultset = $this->client->select($query);
            $data = $this->fetchData($resultset);
            $count = $resultset->getNumFound();
            //oday
            $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
        } else {
            $data = [];
        }
        return [$data, $count];
    }

    public function XuLyTimKiem_tai_san($data, $option, $request)
    {
        if (preg_match_all('/\"([^\"]*?)\"/', $data, $matches)) {
            $search1 = array_unique($matches[0]);
            if (count($search1) > 1) {
                $replace_str = $search1[0];
                $search1 = implode("", $matches[0]);
                $likeTerm = (trim(str_replace($replace_str, '', $data)));
                $likeTerm = str_replace('"', '', $likeTerm);
                //còn cái case là nó còn có phần sau
                $search1 =  $replace_str;
            } else {
                $search1 = implode('', $matches[0]);
                $likeTerm = (trim(str_replace($search1, '', $data)));
            }
            if ($likeTerm == "") {
                return $this->XuLyTimKiem_tai_san2($data, $option, $request);
            }
            $search1 = str_replace('"', '', $search1);
            //Lọc lấy phần phía sau
            $data = str_replace($search1, '', $data);
            //phần sau ""
            $search2 = explode(' ', $likeTerm);
            $query = $this->client->createSelect();
            if ($option === 1) {
                $string_query = '{!complexphrase} texte:' . '(' . '"' . $search1 . '"' . ')';
            } else {
                $string_query =  '{!complexphrase} texte_en:' . '(' . '"' . $search1 . '"' . ')';
            }
            foreach ($search2 as $key => $val) {
                if ($val && $option == 1) {
                    $sub_string_query =   ' AND texte:' . '"' . $search1  . ' *' . $val . '*' . '"~10000';
                    $string_query .= $sub_string_query;
                } elseif ($val && $option == 2) {
                    $sub_string_query =   ' AND texte_en:' . '"' . $search1  . ' *' . $val . '*' . '"~10000';
                    $string_query .= $sub_string_query;
                } else {
                    continue;
                }
            }
            $page = $request->page ?? 1;;
            $numberInPage = 20;
            $query->setQuery($string_query)->setQueryDefaultOperator('AND')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(0)->setRows(1000);
            $resultset = $this->client->select($query);
            $data = $this->fetchData($resultset);
            $count = $resultset->getNumFound();
            //oday
            $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
        } else {
            $data = [];
        }
        return [$data, $count];
    }
    public  function XuLyTimKiem_tat_ca($data, $option, $request)
    {
        if (preg_match_all('/\"([^\"]*?)\"/', $data, $matches)) {
            $search1 = array_unique($matches[0]);
            if (count($search1) > 1) {
                $replace_str = $search1[0];
                $search1 = implode("", $matches[0]);
                $likeTerm = (trim(str_replace($replace_str, '', $data)));
                $likeTerm = str_replace('"', '', $likeTerm);
                //còn cái case là nó còn có phần sau
                $search1 =  $replace_str;
            } else {
                $search1 = implode('', $matches[0]);
                $likeTerm = (trim(str_replace($search1, '', $data)));
            }
            if ($likeTerm == "") {
                return $this->XuLyTimKiem_tat_ca2($data, $option, $request);
            }
            $search1 = str_replace('"', '', $search1);
            //Lọc lấy phần phía sau
            $data = str_replace($search1, '', $data);
            //phần sau ""
            $search2 = explode(' ', $likeTerm); //he
            $query = $this->client->createSelect();
            if ($option === 1) {
                $string_query = 'duong_su_en:' . '(' . '"' . $search1 . '"' . ') OR texte_en:' . '(' . '"' . $search1 . '"' . ')';
            } else {
                $string_query = 'merge_content:' . '(' . '"' . $search1 . '"' . ')';
            }
            foreach ($search2 as $key => $val) {
                if ($val && $option == 1) {
                    $sub_string_query =   ' AND texte:' . '"' . $search1  . ' *' . $val . '*' . '"' . '  OR duong_su:' . '"' . $search1  . ' *' . $val . '*' . '"~10000';
                    $string_query .= $sub_string_query;
                } elseif ($val && $option == 2) {
                    $val = str_replace('-'," ", $val);
                    $sub_string_query =  ' AND merge_content:' . '(' . '"' . $search1 . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } else {
                    continue;
                }
            }
            $page = $request->page ?? 1;;
            $numberInPage = 20;
            $query->setQuery($string_query)->setQueryDefaultOperator('AND')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(0)->setRows(1000);
            $resultset = $this->client->select($query);
            $data = $this->fetchData($resultset);
            $count = $resultset->getNumFound();
            //oday
            $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
        } else {
            $data = [];
        }
        return [$data, $count];
    }
    public function XuLyTimKiem_tat_ca2($data, $option, $request)
    {
        if (preg_match_all('/\"([^\"]*?)\"/', $data, $matches)) {
            //phần trong ""
            $search1 = implode('', $matches[0]);
            $search1 = str_replace('"', '', $search1);
            //Lọc lấy phần phía sau
            $data = str_replace($search1, '', $data);
            //phần sau ""
            $search2 = explode(' ', $data);
            $query = $this->client->createSelect();
            if ($option === 1) {
                $string_query = 'duong_su_en:' . '(' . '"' . $search1 . '"' . ') OR texte_en:' . '(' . '"' . $search1 . '"' . ')';
            } else {
                $string_query = 'merge_content:' . '(' . '"' . $search1 . '"' . ')';
            }
            foreach ($search2 as $key => $val) {
                //append to query string
                if ($val != "" && $option == 1) {
                    $sub_string_query =  ' OR duong_su_en:' . '(' . '"' . $search1 . '"' . '*' . $val . '*' . ') OR texte_en:' . '(' . '"' . $search1 . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } elseif ($val != "" && $option == 2) {
                    $sub_string_query =  '  AND merge_content:' . '(' . '"' . $search1 . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } else {
                    continue;
                }
            }
            $page = $request->page ?? 1;
            $numberInPage = 20;
            $query->setQuery($string_query)->setQueryDefaultOperator('OR')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(0)->setRows(1000);
            $resultset = $this->client->select($query);
            $data = $this->fetchData($resultset);
            $count = $resultset->getNumFound();
            //oday
            $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
        } else {
            $data = [];
        }
        return [$data, $count];
    }
    public function XuLyTimKiem_2_o($texte, $duongsu, $option, $request)
    {
        if (preg_match_all('/\"([^\"]*?)\"/', $duongsu, $matches1) && preg_match_all('/\"([^\"]*?)\"/', $texte, $matches2)) {
            //phần trong ""
            $duong_su = implode('', $matches1[0]);
            $duong_su = str_replace('"', '', $duong_su);
            $texte = implode('', $matches2[0]);
            $texte = str_replace('"', '', $texte);
            //Lọc lấy phần phía sau
            $data = str_replace($duong_su, '', $duongsu);
            //phần sau ""
            $search2 = explode(' ', $data);
            $query = $this->client->createSelect();
            if ($option === 1) {
                $string_query = 'texte:' . '(' . '"' . $texte . '"' . ') AND duong_su:' . '(' . '"' . $duong_su . '"' . ')';
            } else {
                $string_query = 'texte_en:' . '(' . '"' . $texte . '"' . ') AND duong_su_en:' . '(' . '"' . $duong_su . '"' . ')';
            }
            foreach ($search2 as $key => $val) {
                //append to query string
                if ($val != "" && $option == 1) {
                    $sub_string_query =  ' AND texte:' . '(' . '"' . $texte . '"' . '*' . $val . '*' . ') AND duong_su:' . '(' . '"' . $duong_su . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } elseif ($val != "" && $option == 2) {
                    $sub_string_query =  ' AND texte_en:' . '(' . '"' . $texte . '"' . '*' . $val . '*' . ') AND duong_su_en:' . '(' . '"' . $duong_su . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } else {
                    continue;
                }
            }
            $page = $request->page ?? 1;
            $numberInPage = 20;
            $query->setQuery($string_query)->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(0)->setRows(1000);

            $resultset = $this->client->select($query);
            $data = $this->fetchData($resultset);
            $count = $resultset->getNumFound();
            //oday
            $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
        } else {
            $data = [];
        }
        return [$data, $count];
    }
    public function XuLyTimKiem_2_o_duong_su($texte, $duongsu, $option, $request)
    {
        if (preg_match_all('/\"([^\"]*?)\"/', $duongsu, $matches1)) {
            //phần trong ""
            $duong_su1 = implode('', $matches1[0]);
            $duong_su1 = str_replace('"', '', $duong_su1);
            $texte = $texte;
            //Lọc lấy phần phía sau
            $data = str_replace($duong_su1, '', $duongsu);
            //phần sau ""
            $search2 = explode(' ', $data);
            $texte_hl = explode(' ', $texte);
            $string_out = [];
            foreach($texte_hl as $item){
                $string_out[]=$item;
            }
            $search1= $duong_su1;
            $query = $this->client->createSelect();
            if ($option === 1) {
                $string_query = 'duong_su_en:' . '(' . '"' . $duong_su1 . '"' . ') AND texte_en:' . '(' . $texte  . ')';
            } else {
                $string_query = 'duong_su_en:' . '(' . '"' . $duong_su1 . '"' . ') AND texte_en:' . '(' .  $texte  . ')';
            }
            foreach ($search2 as $key => $val) {
                //append to query string
                if ($val != "" && $option == 1) {
                    $sub_string_query =  ' AND duong_su_en:' . '(' . '"' . $duong_su1 . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } elseif ($val != "" && $option == 2) {
                    $sub_string_query =  ' AND duong_su_en:' . '(' . '"' . $duong_su1 . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } else {
                    continue;
                }
                $string_out[] = $val;
            }
            $page = $request->page ?? 1;
            $numberInPage = 20;
            $query->setQuery($string_query)->setQueryDefaultOperator('AND')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
            $resultset = $this->client->select($query);
            $data = $this->fetchData($resultset);
            $count = $resultset->getNumFound();
            //oday
            $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
        } else {
            $data = [];
        }
        return [$data, $count, $search1, $string_out];
    }
    public function XuLyTimKiem_2_o_tai_san($texte, $duongsu, $option, $request)
    {
        if (preg_match_all('/\"([^\"]*?)\"/', $texte, $matches1)) {
            //phần trong ""
            $texte1 = implode('', $matches1[0]);
            $texte1 = str_replace('"', '', $texte1);
            $duong_su = $duongsu;
            //Lọc lấy phần phía sau
            $data = str_replace($texte1, ' ', $texte);
            //phần sau ""
            $search2 = explode(' ', $data);
            //replace " in search2
            $search2 = str_replace('"', '', $search2);

            $query = $this->client->createSelect();
            if ($option === 1) {
                $string_query = 'texte:' . '(' . '"' . $texte1 . '"' . ') AND duong_su:' . '(' . $duong_su  . ')';
            } else {
                $string_query = 'texte_en:' . '(' . '"' . $texte . '"' . ') AND duong_su_en:' . '(' .  $duong_su  . ')';
            }
            foreach ($search2 as $key => $val) {
                //append to query string
                if ($val !== ""  && $option == 1) {
                    $sub_string_query =  ' AND texte:' . '(' . '"' . $texte1 . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } elseif ($val !== ""  && $option == 2) {
                    $sub_string_query =  ' AND texte_en:' . '(' . '"' . $texte1 . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } else {
                    continue;
                }
            }
            $page = $request->page ?? 1;
            $numberInPage = 20;
            $query->setQuery($string_query)->setQueryDefaultOperator('AND')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(0)->setRows(1000);
            //sort
            $resultset = $this->client->select($query);
            $data = $this->fetchData($resultset);
            $count = $resultset->getNumFound();
            //oday
            $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
        } else {
            $data = [];
        }
        return [$data, $count];
    }
    public function XuLyTimKiem_so_hd($data, $option, $request)
    {
        $role = Sentinel::check();
        $user_id =  $role->getAttributes();
        $user_id = $user_id['id'];
        $vpcc_id = NhanVienModel::where('nv_id', $user_id)->first()->nv_vanphong;
        $cn_ndd = ChiNhanhModel::where('cn_id', (int)$vpcc_id)->first()->code_cn;

        if (preg_match_all('/\"([^\"]*?)\"/', $data, $matches)) {

            $search1 = array_unique($matches[0]);
            if (count($search1) > 1) {
                $replace_str = $search1[0];
                $search1 = implode("", $matches[0]);
                $likeTerm = (trim(str_replace($replace_str, '', $data)));
                $likeTerm = str_replace('"', '', $likeTerm);
                //còn cái case là nó còn có phần sau
                $search1 =  $replace_str;
            } else {
                $search1 = implode('', $matches[0]);
                $likeTerm = (trim(str_replace($search1, '', $data)));
            }
            if ($likeTerm == "") {
                return $this->XuLyTimKiem_so_hd2($data, $option, $request);
            }
            $search1 = str_replace('"', '', $search1);
            //Lọc lấy phần phía sau
            $data = str_replace($search1, '', $data);
            //phần sau ""
            $search2 = explode(' ', $likeTerm);
            $query = $this->client->createSelect();
            if ($option === 1) {
                $string_query = '{!complexphrase} so_hd:' . '(' . '"' . $search1 . '"' . ')';
            } else {
                $string_query =  '{!complexphrase} so_hd:' . '(' . '"' . $search1 . '"' . ' )';
            }
            foreach ($search2 as $key => $val) {
                if ($val && $option == 1) {
                    $sub_string_query =   ' AND so_hd:' . '"' . $search1  . ' *' . $val . '*' . '"~10000';
                    $string_query .= $sub_string_query;
                } elseif ($val && $option == 2) {
                    $sub_string_query =   ' AND so_hd:' . '"' . $search1  . ' *' . $val . '*' . '"~10000';
                    $string_query .= $sub_string_query;
                } else {
                    continue;
                }
            }
            // $string_query =  ' {!complexphrase} duong_su:' . '(' . '"' . $search1 . '"' . ') AND duong_su:' . '"' . $search1  . ' *' . $val . '*' . '"' . '~1000';
            // $string_query=preg_replace('/\*+/', '*', $string_query);
            // dd($string_query);
            $page = $request->page ?? 1;
            $numberInPage = 20;
            $query->setQuery($string_query)->setQueryDefaultOperator('AND')->addSort('st_id', $query::SORT_DESC)->setStart(0)->setRows(1000);
            $resultset = $this->client->select($query);
            $count = $resultset->getNumFound();
            $data = $this->fetchData($resultset);
            //oday
            $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
        } else {
            $data = [];
        }
        return [$data, $count];
    }

    public function XuLyTimKiem_so_hd2($data, $option, $request)
    {
        $role = Sentinel::check();
        $user_id =  $role->getAttributes();
        $user_id = $user_id['id'];
        $vpcc_id = NhanVienModel::where('nv_id', $user_id)->first()->nv_vanphong;
        $cn_ndd = ChiNhanhModel::where('cn_id', (int)$vpcc_id)->first()->code_cn;
        if (preg_match_all('/\"([^\"]*?)\"/', $data, $matches)) {
            //phần trong ""
            $search1 = implode('', $matches[0]);
            $search1 = str_replace('"', '', $search1);
            //Lọc lấy phần phía sau
            $data = str_replace($search1, '', $data);
            //phần sau ""
            $search2 = explode(' ', $data);
            $query = $this->client->createSelect();
            if ($option === 1) {
                $string_query = 'so_hd:' . '(' . '"' . $search1 . '"' . ')';
            } else {
                $string_query = 'so_hd:' . '(' . '"' . $search1 . '"' . ')';
            }
            foreach ($search2 as $key => $val) {
                //append to query string
                if ($val != "" && $option == 1) {
                    $sub_string_query =  ' AND so_hd:' . '(' . '"' . $search1 . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } elseif ($val != "" && $option == 2) {
                    $sub_string_query =  ' AND so_hd:' . '(' . '"' . $search1 . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } else {
                    continue;
                }
            }
            $page = $request->page ?? 1;
            $numberInPage = 20;
            $query->setQuery($string_query)->setQueryDefaultOperator('AND')->addSort('st_id', $query::SORT_DESC)->setStart(0)->setRows(1000);
            $resultset = $this->client->select($query);
            $data = $this->fetchData($resultset);
            $count = $resultset->getNumFound();
            //oday
            $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
        } else {
            $data = [];
        }
        return [$data, $count];
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
    public function search(Request $request)
    {
        $count_bs = $this->count_total;
        $type = $request->type ?? 'basic';
        $count_ngan_chan = $this->count_ngan_chan;
        $role = Sentinel::check();
        $user_id =  $role->getAttributes();
        $user_id = $user_id['id'];
        $vpcc_id = NhanVienModel::where('nv_id', $user_id)->first()->nv_vanphong;
        $cn_ndd = ChiNhanhModel::where('cn_id', (int)$vpcc_id)->first()->code_cn;
        $code_cn = ChiNhanhModel::where('cn_id', (int)$vpcc_id)->first()->code_cn;
        $query5 = $this->client->createSelect();
        $query5->setQuery('sync_code:' . '(' . $cn_ndd . ')')->setQueryDefaultOperator('OR')->setStart(0)->setRows(1000);
        $resultset5 = $this->client->select($query5);
        $count_office = $resultset5->getNumFound();
        $option = $request->all();
        if ($option == []) {
            $page = $request->page ?? 1;
            $numberInPage = 20;
            $query = $this->client->createSelect();
            $query2 = $this->client->createSelect();
            $query->setQuery("*:*")->setQueryDefaultOperator('OR')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(0)->setRows(1000);
            $resultset = $this->client->select($query);
            $data = $this->fetchData($resultset);
            $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
            $count = $resultset->getNumFound();

            return view('search', compact('data', 'option', 'type', 'count', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office'));
        }
        //hightlight search request
        if ($request->duong_su == null && $request->tai_san == null && $request->tat_ca == null &&  $request->so_hd == null) {
            $page = $request->page ?? 1;
            $numberInPage = 20;
            $query = $this->client->createSelect();
            $query2 = $this->client->createSelect();
            $query->setQuery("*:*")->setQueryDefaultOperator('OR')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(0)->setRows(1000);
            $resultset = $this->client->select($query);
            $data = $this->fetchData($resultset);
            $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
            $count = $resultset->getNumFound();

            return view('search', compact('data', 'option', 'type', 'count', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office'));
        }
        //Tìm 2 ô đương sự và tài sản
        if ($request->duong_su!= null && $request->tai_san != null && $request->tat_ca == null &&  $request->so_hd == null) {
            $duong_su =$this->vn_to_str( $request->duong_su);
            $tai_san =$this->vn_to_str( $request->tai_san);
            $search = $duong_su;
            $page = $request->page ?? 1;
            $numberInPage = 20;
            if (preg_match('/[\x80-\xFF]/',$duong_su) && preg_match('/[\x80-\xFF]/', $tai_san)) {
                //Chứa dấu " "
                if (strpos($duong_su, '"') !== false && strpos($tai_san, '"') !== false) {
                    // cả 2 đều có ""
                    $data = $this->XuLyTimKiem_2_o($tai_san, $duong_su, 1, $request);
                    $count = $data[1];
                    $data = $data[0];
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office','string_out','count_ngan_chan_result'));
                } elseif (strpos($duong_su, '"') !== false && strpos($tai_san, '"') === false) {
                    $data = $this->XuLyTimKiem_2_o_duong_su($tai_san, $duong_su, 1, $request);
                    $count = $data[1];
                    $data = $data[0];
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office','string_out','count_ngan_chan_result'));
                } elseif (strpos($duong_su, '"') === false && strpos($tai_san, '"') !== false) {
                    $data = $this->XuLyTimKiem_2_o_tai_san($tai_san, $duong_su, 1, $request);
                    $count = $data[1];
                    $data = $data[0];
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office','string_out','count_ngan_chan_result'));
                } else {
                    $query = $this->client->createSelect();
                    $query2 = $this->client->createSelect();
                    $query->setQuery('duong_su:' . '(' . $duong_su . ') AND texte:' . '(' . $tai_san . ')')->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                    $resultset = $this->client->select($query);
                    $data = $this->fetchData($resultset);
                    $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                    $count = $resultset->getNumFound();
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office','string_out','count_ngan_chan_result'));
                }
            }elseif (preg_match('/[\x80-\xFF]/', $duong_su) && preg_match('/[\x80-\xFF]/', $tai_san) == false){
                //dayne 
                 //Chứa dấu " "
                 if (strpos($duong_su, '"') !== false && strpos($tai_san, '"') !== false) {
                    // cả 2 đều có ""
                    $data = $this->XuLyTimKiem_2_o_1_0($tai_san, $duong_su, 1, $request);
                  
                    $count = $data[1];
                    $data = $data[0];
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office','string_out','count_ngan_chan_result'));
                } elseif (strpos($duong_su, '"') !== false && strpos($tai_san, '"') === false) {
                    $data = $this->XuLyTimKiem_2_o_duong_su($tai_san, $duong_su, 1, $request);
                  
                    $count = $data[1];
                    $data = $data[0];
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office','string_out','count_ngan_chan_result'));
                } elseif (strpos($duong_su, '"') === false && strpos($tai_san, '"') !== false) {
                    $data = $this->XuLyTimKiem_2_o_tai_san($tai_san, $duong_su, 1, $request);
                  
                    $count = $data[1];
                    $data = $data[0];
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office','string_out','count_ngan_chan_result'));
                } else {
                    $query = $this->client->createSelect();
                    $query2 = $this->client->createSelect();
                    $query->setQuery('duong_su:' . '(' . $duong_su . ') AND texte:' . '(' . $tai_san . ')')->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                    $resultset = $this->client->select($query);
                    $data = $this->fetchData($resultset);
                    $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                    $count = $resultset->getNumFound();
                
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office','string_out','count_ngan_chan_result'));
                }
            }
            elseif (preg_match('/[\x80-\xFF]/', $duong_su) == false && preg_match('/[\x80-\xFF]/', $tai_san)){
                 if (strpos($duong_su, '"') !== false && strpos($tai_san, '"') !== false) {
                    // cả 2 đều có ""
                    $data = $this->XuLyTimKiem_2_o_1_0($tai_san, $duong_su, 2, $request);
                  
                    $count = $data[1];
                    $data = $data[0];
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office','string_out','count_ngan_chan_result'));
                } elseif (strpos($duong_su, '"') !== false && strpos($tai_san, '"') === false) {
                    $data = $this->XuLyTimKiem_2_o_duong_su_1_0($tai_san, $duong_su, 2, $request);
                  
                    $count = $data[1];
                    $data = $data[0];
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office','string_out','count_ngan_chan_result'));
                } elseif (strpos($duong_su, '"') === false && strpos($tai_san, '"') !== false) {
                    $data = $this->XuLyTimKiem_2_o_tai_san($tai_san, $duong_su, 1, $request);
                  
                    $count = $data[1];
                    $data = $data[0];
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office','string_out','count_ngan_chan_result'));
                } else {
                    $query = $this->client->createSelect();
                    $query2 = $this->client->createSelect();
                    $query->setQuery('duong_su:' . '(' . $duong_su . ') AND texte:' . '(' . $tai_san . ')')->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                    $resultset = $this->client->select($query);
                    $data = $this->fetchData($resultset);
                    $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                    $count = $resultset->getNumFound();
                
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office','string_out','count_ngan_chan_result'));
                }
            }
            else {
                if (strpos($duong_su, '"') !== false && strpos($tai_san, '"') !== false) {
                    // cả 2 đều có ""
                    $data = $this->XuLyTimKiem_2_o($tai_san, $duong_su, 2, $request);
                   
                    $count = $data[1];
                    $data = $data[0];
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office','string_out','count_ngan_chan_result'));
                } elseif (strpos($duong_su, '"') !== false && strpos($tai_san, '"') === false) {
                    //here
                    $data = $this->XuLyTimKiem_2_o_duong_su($tai_san, $duong_su, 2, $request);
                   
                    $count = $data[1];
                    $data = $data[0];
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office','string_out','count_ngan_chan_result'));
                } elseif (strpos($duong_su, '"') === false && strpos($tai_san, '"') !== false) {
                    $data = $this->XuLyTimKiem_2_o_tai_san($tai_san, $duong_su, 2, $request);
                   
                    $count = $data[1];
                    $data = $data[0];
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office','string_out','count_ngan_chan_result'));
                } else {
                    $query = $this->client->createSelect();
                    $query2 = $this->client->createSelect();
                    $query->setQuery('duong_su_en:' . '(' . $duong_su . ') AND texte_en:' . '(' . $tai_san . ')')->setQueryDefaultOperator('AND')->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                    $resultset = $this->client->select($query);
                    $data = $this->fetchData($resultset);
                    $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                    $count = $resultset->getNumFound();
                
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office','string_out','count_ngan_chan_result'));
                }
            }
        }
        //Tìm theo đương sự
        if ($request->duong_su != null) {
            $search = $request->duong_su;
            //check is request vietnamese string ?
            if (preg_match('/[\x80-\xFF]/', $search)) {
                //Chứa dấu " "
                if (strpos($search, '"') !== false) {
                    $data = $this->XuLyTimKiem_duong_su($search, 1, $request);
                    $count = $data[1];
                    $data = $data[0];
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office'));
                } else {
                    $page = $request->page ?? 1;
                    $numberInPage = 20;
                    $query = $this->client->createSelect();
                    $query2 = $this->client->createSelect();
                    $query->setQuery('duong_su:' . '(' . $search . ')')->setQueryDefaultOperator('AND')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(0)->setRows(1000);
                    $resultset = $this->client->select($query);
                    $data = $this->fetchData($resultset);
                    $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                    $count = $resultset->getNumFound();

                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office'));
                }
            }
            //Tìm không dấu
            else {
                if (strpos($search, '"') !== false) {
                    $data = $this->XuLyTimKiem_duong_su($search, 2, $request);
                    $count = $data[1];
                    $data = $data[0];
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office'));
                } else {
                    $page = $request->page ?? 1;
                    $numberInPage = 20;
                    $query = $this->client->createSelect();
                    $query->setQuery('duong_su_en:' . $search);
                    $query2 = $this->client->createSelect();
                    $query->setQuery('duong_su_en:' . '(' . $search . ')')->setQueryDefaultOperator('AND')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(0)->setRows(1000);
                    $resultset = $this->client->select($query);
                    $data = $this->fetchData($resultset);
                    $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                    $count = $resultset->getNumFound();

                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office'));
                }
            }
        } //Tìm theo tài sản
        elseif ($request->tai_san != null) {
            $search = $request->tai_san;
            //check is request vietnamese string ?
            if (preg_match('/[\x80-\xFF]/', $search)) {
                //Chứa dấu " "
                if (strpos($search, '"') !== false) {
                    $data = $this->XuLyTimKiem_tai_san($search, 1, $request);
                    $count = $data[1];
                    $data = $data[0];
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office'));
                } else {
                    $page = $request->page ?? 1;
                    $numberInPage = 20;
                    $query = $this->client->createSelect();
                    $query2 = $this->client->createSelect();
                    $query->setQuery('texte:' . '(' . $search . ')')->setQueryDefaultOperator('AND')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(0)->setRows(1000);
                    $resultset = $this->client->select($query);
                    $data = $this->fetchData($resultset);
                    $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                    $count = $resultset->getNumFound();

                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office'));
                }
            }
            //Tìm không dấu
            else {
                //Chứa dấu " "
                if (strpos($search, '"') !== false) {
                    $data = $this->XuLyTimKiem_tai_san($search, 2, $request);
                    $count = $data[1];
                    $data = $data[0];
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office'));
                } else {
                    $page = $request->page ?? 1;
                    $numberInPage = 20;
                    $query = $this->client->createSelect();
                    $query2 = $this->client->createSelect();
                    $query->setQuery('texte_en:' . '(' . $search . ')')->setQueryDefaultOperator('AND')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(0)->setRows(1000);
                    $resultset = $this->client->select($query);
                    $data = $this->fetchData($resultset);
                    $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                    $count = $resultset->getNumFound();

                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office'));
                }
            }
        } 
       //Tim theo So_hd
       elseif ($request->so_hd != null) {
        $search = $request->so_hd;
        if(preg_match('/"/', $search)){
            $search = $request->so_hd3;
        }else{
            $search = '"'.$search.'"';
        }
        //check is request vietnamese string ?
        if (preg_match('/[\x80-\xFF]/', $search)) {
            //Chứa dấu " "
            if (strpos($search, '"') !== false) {
                $data = $this->XuLyTimKiem_so_hd($search, 1, $request);
                $count = $data[1];
                $data = $data[0];
                return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office'));
            } else {
                $page = $request->page ?? 1;
                $numberInPage = 20;
                $query = $this->client->createSelect();
                $query2 = $this->client->createSelect();
                $query->setQuery('so_hd:' . '(' . $search . ')')->addSort('st_id', $query::SORT_DESC)->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                $resultset = $this->client->select($query);
                $data = $this->fetchData($resultset);
                $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                $count = $resultset->getNumFound();
                $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office'));
            }
        }
        //Tìm không dấu
        else {
            //Chứa dấu " "
            if (strpos($search, '"') !== false) {
                $data = $this->XuLyTimKiem_so_hd($search, 2, $request);
                $count = $data[1];
                $data = $data[0];
                return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office'));
            } else {
                $page = $request->page ?? 1;
                $numberInPage = 20;
                $query = $this->client->createSelect();
                $query2 = $this->client->createSelect();
                $query->setQuery('so_hd:' . '(' . $search . ')')->addSort('st_id', $query::SORT_DESC)->setStart(0)->setRows(1000);
                $resultset = $this->client->select($query);
                $data = $this->fetchData($resultset);
                $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                $count = $resultset->getNumFound();
                return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office'));
            }
        }
    }
        //Tìm tất cả
        else {
            $search = $request->tat_ca;
            $search = $this->vn_to_str($search);
            if (preg_match('/[\x80-\xFF]/', $search)) {
                //Chứa dấu " "
                if (strpos($search, '"') !== false) {
                    $data = $this->XuLyTimKiem_tat_ca($search, 1, $request);
                    $count = $data[1];
                    $data = $data[0];
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office'));
                } else {
                    $page = $request->page ?? 1;
                    $numberInPage = 20;
                    $query = $this->client->createSelect();
                    $query->setQuery('texte:' . '(' . $search . ') duong_su:' . '(' . $search . ')')->setQueryDefaultOperator('AND')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(0)->setRows(1000);
                    $query2 = $this->client->createSelect();
                    $query->setQueryDefaultOperator('OR')->setStart(0)->setRows(1000);
                    $resultset = $this->client->select($query);
                    $data = $this->fetchData($resultset);
                    $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                    $count = $resultset->getNumFound();

                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office'));
                }
            }
            //Tìm không dấu
            else {
                if (strpos($search, '"') !== false) {
                    $data = $this->XuLyTimKiem_tat_ca($search, 2, $request);
                    $count = $data[1];
                    $data = $data[0];
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office'));
                } else {
                    $page = $request->page ?? 1;
                    $numberInPage = 20;
                    $query = $this->client->createSelect();
                    $query->setQuery('merge_content:' . '(' . $search . ')')->setQueryDefaultOperator('OR')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(0)->setRows(1000);
                    $query2 = $this->client->createSelect();
                    $query->setQueryDefaultOperator('OR')->setStart(0)->setRows(1000);
                    $resultset = $this->client->select($query);
                    $data = $this->fetchData($resultset);
                    $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                    $count = $resultset->getNumFound();
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office'));
                }
            }
        }
    }
}