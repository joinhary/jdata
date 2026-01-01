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
use App\Http\Controllers\Solr_Prevent_Print;




class SolariumController_nganchan extends Controller
{
    protected $client;

    public function __construct(\Solarium\Client $client)
    {
        $this->client = $client;
        $this->client->getEndpoint()->setCore('timkiemsuutra');

        $query = $this->client->createSelect();
        $query2 = $this->client->createSelect();
        $query->setQuery("*:*")->setQueryDefaultOperator('OR')->addSort('st_id', $query::SORT_DESC)->setStart(0)->setRows(20);
        $query2->setQuery("ngan_chan: (3)")->setQueryDefaultOperator('OR')->addSort('st_id', $query2::SORT_DESC)->setStart(0)->setRows(20);
        $resultset = $this->client->select($query);
        $resultset2 = $this->client->select($query2);
        $count_ngan_chan = $resultset2->getNumFound();
        $count_total = $resultset->getNumFound();
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
        foreach ($resultset as $document) {
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
                'release_file_path' => $document->release_file_path,
                'release_file_name' => $document->release_file_name,
                'is_update' => $document->is_update,
                'ma_phan_biet' => $document->ma_phan_biet,
                'note' => $document->note,
                'undisputed_date' => $document->undisputed_date,
                'undisputed_note' => $document->undisputed_note,
                'ccv' => $document->ccv,

            ];
        }
        //orderby ngay_nhap
        $data = collect($data);
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
                $string_query = 'duong_su:' . '(' . '"' . $search1 . '"' . ')AND ngan_chan:(3)';
            } else {
                $string_query = 'duong_su_en:' . '(' . '"' . $search1 . '"' . ')AND ngan_chan:(3)';
            }
            $string_out = [];
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
                $string_out[] = $val;
            }
            $page = $request->page ?? 1;
            $numberInPage = 20;
            $query->setQuery($string_query)->setQueryDefaultOperator('AND')->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
            $resultset = $this->client->select($query);
            $count = $resultset->getNumFound();
            $data = $this->fetchData($resultset);
            //oday
            $optionPaginate = ['path' => route('searchSolr_nganchan'), 'query' => $request->query()];

            $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
        } else {
            $data = [];
        }
        return [$data, $count, $search1, $string_out];
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
                $string_query = 'duong_su:' . '(' . '"' . $search1 . '"' . ')AND ngan_chan:(3)';
            } else {
                $string_query =  'duong_su_en:' . '(' . '"' . $search1 . '"' . ' )AND ngan_chan:(3)';
            }
            $string_out = [];
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
                $string_out[] = $val;
            }
            // $string_query= $string_query . ' AND ngan_chan:(3)';
            // $string_query =  ' {!complexphrase} duong_su:' . '(' . '"' . $search1 . '"' . ') AND duong_su:' . '"' . $search1  . ' *' . $val . '*' . '"' . '~1000';
            // $string_query=preg_replace('/\*+/', '*', $string_query);
            $page = $request->page ?? 1;
            $numberInPage = 20;
            $query->setQuery($string_query)->setQueryDefaultOperator('AND')->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
            $resultset = $this->client->select($query);
            $count = $resultset->getNumFound();
            $data = $this->fetchData($resultset);
            //oday
            $optionPaginate = ['path' => route('searchSolr_nganchan'), 'query' => $request->query()];
            $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
        } else {
            $data = [];
        }
        return [$data, $count, $search1, $string_out];
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
                $string_query = 'texte:' . '(' . '"' . $search1 . '"' . ')AND ngan_chan:(3)';
            } else {
                $string_query = 'texte_en:' . '(' . '"' . $search1 . '"' . ')AND ngan_chan:(3)';
            }
            $string_out = [];
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
                $string_out[] = $val;
            }
            $page = $request->page ?? 1;
            $numberInPage = 20;
            $query->setQuery($string_query)->setQueryDefaultOperator('AND')->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
            $resultset = $this->client->select($query);
            $data = $this->fetchData($resultset);
            $count = $resultset->getNumFound();
            //oday
            $optionPaginate = ['path' => route('searchSolr_nganchan'), 'query' => $request->query()];
            $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
        } else {
            $data = [];
        }
        return [$data, $count, $search1, $string_out];
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
                $string_query = 'texte:' . '(' . '"' . $search1 . '"' . ')AND ngan_chan:(3)';
            } else {
                $string_query =  'texte_en:' . '(' . '"' . $search1 . '"' . ')AND ngan_chan:(3)';
            }
            $string_out = [];
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
                $string_out[] = $val;
            }
            $page = $request->page ?? 1;
            $numberInPage = 20;
            $query->setQuery($string_query)->setQueryDefaultOperator('AND')->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
            $resultset = $this->client->select($query);
            $data = $this->fetchData($resultset);
            $count = $resultset->getNumFound();
            //oday
            $optionPaginate = ['path' => route('searchSolr_nganchan'), 'query' => $request->query()];
            $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
        } else {
            $data = [];
        }
        return [$data, $count, $search1, $string_out];
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
                $string_query = 'texte:' . '(' . '"' . $search1 . '"' . ') AND duong_su:' . '(' . '"' . $search1 . '"' . ')AND ngan_chan:(3)';
            } else {
                $string_query = 'merge_content:' . '(' . '"' . $search1 . '"' . ')AND ngan_chan:(3)';
            }
            $string_out = [];
            foreach ($search2 as $key => $val) {
                if ($val && $option == 1) {
                    $sub_string_query =   ' OR texte:' . '"' . $search1  . ' *' . $val . '*' . '"' . '  OR duong_su:' . '"' . $search1  . ' *' . $val . '*' . '"~10000';
                    $string_query .= $sub_string_query;
                } elseif ($val && $option == 2) {
                    $val = str_replace('-', " ", $val);
                    $sub_string_query =  ' AND merge_content:' . '(' . '"' . $search1 . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } else {
                    continue;
                }
                $string_out[] = $val;
            }
            $page = $request->page ?? 1;
            $numberInPage = 20;
            $query->setQuery($string_query)->setQueryDefaultOperator('AND')->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
            $resultset = $this->client->select($query);
            $data = $this->fetchData($resultset);
            $count = $resultset->getNumFound();
            //oday
            $optionPaginate = ['path' => route('searchSolr_nganchan'), 'query' => $request->query()];
            $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
        } else {
            $data = [];
        }
        return [$data, $count, $search1, $string_out];
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
                $string_query = 'texte:' . '(' . '"' . $search1 . '"' . ') OR duong_su:' . '(' . '"' . $search1 . '"' . ') AND ngan_chan:(3)';
            } else {
                $string_query = 'merge_content:' . '(' . '"' . $search1 . '"' . ') AND ngan_chan:(3)';
            }
            $string_out = [];
            foreach ($search2 as $key => $val) {
                //append to query string
                if ($val != "" && $option == 1) {
                    $sub_string_query =  ' AND texte:' . '(' . '"' . $search1 . '"' . '*' . $val . '*' . ') OR duong_su:' . '(' . '"' . $search1 . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } elseif ($val != "" && $option == 2) {
                    $sub_string_query =  ' AND merge_content:' . '(' . '"' . $search1 . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } else {
                    continue;
                }
                $string_out[] = $val;
            }
            $page = $request->page ?? 1;
            $numberInPage = 20;
            $query->setQuery($string_query)->setQueryDefaultOperator('AND')->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
            $resultset = $this->client->select($query);
            $data = $this->fetchData($resultset);
            $count = $resultset->getNumFound();
            //oday
            $optionPaginate = ['path' => route('searchSolr_nganchan'), 'query' => $request->query()];
            $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
        } else {
            $data = [];
        }
        return [$data, $count, $search1, $string_out];
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
                $string_query = 'texte:' . '(' . '"' . $texte . '"' . ') AND duong_su:' . '(' . '"' . $duong_su . '"' . ') AND ngan_chan:(3)';
            } else {
                $string_query = 'texte_en:' . '(' . '"' . $texte . '"' . ') AND duong_su_en:' . '(' . '"' . $duong_su . '"' . ') AND ngan_chan:(3)';
            }
            $search1 = $duong_su;
            $string_out[] = $texte;
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
                $string_out[] = $val;
            }
            $page = $request->page ?? 1;
            $numberInPage = 20;
            $query->setQuery($string_query)->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
            $resultset = $this->client->select($query);
            $data = $this->fetchData($resultset);
            $count = $resultset->getNumFound();
            //oday
            $optionPaginate = ['path' => route('searchSolr_nganchan'), 'query' => $request->query()];
            $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
        } else {
            $data = [];
        }
        return [$data, $count, $search1, $string_out];
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
            $query = $this->client->createSelect();
            if ($option === 1) {
                $string_query = 'duong_su:' . '(' . '"' . $duong_su1 . '"' . ') AND texte:' . '(' . $texte  . ') AND ngan_chan:(3)';
            } else {
                $string_query = 'duong_su_en:' . '(' . '"' . $duong_su1 . '"' . ') AND texte_en:' . '(' .  $texte  . ' ) AND ngan_chan:(3)';
            }
            $search2 = explode(' ', $data);
            $texte_hl = explode(' ', $texte);
            $string_out = [];
            foreach ($texte_hl as $item) {
                $string_out[] = $item;
            }
            $search1 = $duong_su1;
            foreach ($search2 as $key => $val) {
                //append to query string
                if ($val != "" && $option == 1) {
                    $sub_string_query =  ' AND duong_su:' . '(' . '"' . $duong_su1 . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } elseif ($val != "" && $option == 2) {
                    $sub_string_query =  ' AND duong_su_en:' . '(' . '"' . $duong_su1 . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } else {
                    continue;
                }
            }
            $page = $request->page ?? 1;
            $numberInPage = 20;
            $query->setQuery($string_query)->setQueryDefaultOperator('AND')->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
            $resultset = $this->client->select($query);
            $data = $this->fetchData($resultset);
            $count = $resultset->getNumFound();
            //oday
            $optionPaginate = ['path' => route('searchSolr_nganchan'), 'query' => $request->query()];
            $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
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
                $string_query = 'texte:' . '(' . '"' . $texte1 . '"' . ') AND duong_su:' . '(' . $duong_su  . ') AND ngan_chan:(3)';
            } else {
                $string_query = 'texte_en:' . '(' . '"' . $texte . '"' . ') AND duong_su_en:' . '(' .  $duong_su  . ') AND ngan_chan:(3)';
            }
            $search1[] = $duong_su;
            $search1[] = $texte;
            $string_out = [];
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
            $query->setQuery($string_query)->setQueryDefaultOperator('AND')->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
            //sort
            $resultset = $this->client->select($query);
            $data = $this->fetchData($resultset);
            $count = $resultset->getNumFound();
            //oday
            $optionPaginate = ['path' => route('searchSolr_nganchan'), 'query' => $request->query()];
            $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
        } else {
            $data = [];
        }
        return [$data, $count, $search1, $string_out];
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
        // dd($request->all());
             //080923
             $data = $request->toArray();
             $data = AppController::convert_unicode($data);
             $request->replace($data->toArray());
        $count_bs = $this->count_total;
        $type = $request->type ?? 'basic';
        $count_ngan_chan = $this->count_ngan_chan;
        $role = Sentinel::check();
        if ($role) {
            $user_id =  $role->getAttributes();
            $user_id = $user_id['id'];
        } else {
            return view('admin.login');
        }
        $vpcc_id = NhanVienModel::where('nv_id', $user_id)->first()->nv_vanphong;
        $code_cn = ChiNhanhModel::where('cn_id', (int)$vpcc_id)->first()->code_cn;
        $cn_ndd = ChiNhanhModel::where('cn_id', (int)$vpcc_id)->first()->code_cn;
        $query5 = $this->client->createSelect();
        $query5->setQuery('sync_code:' . '(' . $cn_ndd . ')')->setQueryDefaultOperator('OR')->addSort('st_id', $query5::SORT_DESC)->setStart(0)->setRows(20);
        $resultset5 = $this->client->select($query5);
        $count_office = $resultset5->getNumFound();
        $option = $request->all();
        if (isset($option['test'])) {
           
            $page = $request->page ?? 1;
            $numberInPage = 20;
            $search = $option['test'];
            $query = $this->client->createSelect();
            $query2 = $this->client->createSelect();
            // $query->setQuery("duong_su: $search " . "AND " . "duong_su: $search ")->setQueryDefaultOperator('AND')->setStart(0)->setRows(1000);
            $query->setQuery("duong_su: $search AND ngan_chan: (3)")->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
            $resultset = $this->client->select($query);
            $data = $this->fetchData($resultset);
            $optionPaginate = ['path' => route('searchSolr_nganchan'), 'query' => $request->query()];
            $count = $resultset->getNumFound();
            $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
            return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out'));
        }

        if ($option == []) {
          
            $page = $request->page ?? 1;
            $numberInPage = 20;
            $query = $this->client->createSelect();
            $query2 = $this->client->createSelect();
            $query->setQuery('ngan_chan:(3)')->setQueryDefaultOperator('OR')->addSort('st_id', $query::SORT_DESC)->setStart(0)->setRows(20);
            $resultset = $this->client->select($query);
            $data = $this->fetchData($resultset);
            $optionPaginate = ['path' => route('searchSolr_nganchan'), 'query' => $request->query()];
            $count = $resultset->getNumFound();
            $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
            $string_out = [];
            return view('search', compact('data', 'option', 'type', 'count', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office'));
        }
        //hightlight search request
        if ($request->duong_su2 == null && $request->tai_san2 == null && $request->tat_ca2 == null) {
            
            $page = $request->page ?? 1;
            $numberInPage = 20;
            $query = $this->client->createSelect();
            $query2 = $this->client->createSelect();
            $query->setQuery('ngan_chan:(3)')->setQueryDefaultOperator('OR')->addSort('st_id', $query::SORT_DESC)->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
            $resultset = $this->client->select($query);
            $data = $this->fetchData($resultset);
            $optionPaginate = ['path' => route('searchSolr_nganchan'), 'query' => $request->query()];
            $count = $resultset->getNumFound();
            $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
            $string_out = [];
            return view('search', compact('data', 'option', 'type', 'count', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office'));
        }
        //Tìm 2 ô đương sự và tài sản
        if ($request->duong_su2 != null && $request->tai_san2 != null && $request->tat_ca2 == null) {
            $duong_su2 = $this->vn_to_str($request->duong_su2);
            $tai_san2 = $this->vn_to_str($request->tai_san2);
            $search = $request->duong_su2;
            $page = $request->page ?? 1;
            $numberInPage = 20;
            if (preg_match('/[\x80-\xFF]/', $request->duong_su2) || preg_match('/[\x80-\xFF]/', $request->tai_san2)) {
                //Chứa dấu " "
                if (strpos($request->duong_su2, '"') !== false && strpos($request->tai_san2, '"') !== false) {
                    // cả 2 đều có ""
                    $data = $this->XuLyTimKiem_2_o($request->tai_san2, $request->duong_su2, 1, $request);
                    $string_in = $data[2] ?? '';
                    $string_in = str_replace('"', '', $string_in);
                    $string_out = $data[3] ?? [];
                    $string_out = array_map(function ($item) {
                        return str_replace('""', '', $item);
                    }, $string_out);
                    $string_out = array_filter($string_out);
                    //add string_in to string_out
                    $string_out[] = $string_in;
                    $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                    $count = $data[1];
                    $data = $data[0];
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out'));
                } elseif (strpos($request->duong_su2, '"') !== false && strpos($request->tai_san2, '"') === false) {
                    $data = $this->XuLyTimKiem_2_o_duong_su($request->tai_san2, $request->duong_su2, 1, $request);
                    $string_in = $data[2] ?? '';
                    $string_in = str_replace('"', '', $string_in);
                    $string_out = $data[3] ?? [];
                    $string_out = array_map(function ($item) {
                        return str_replace('""', '', $item);
                    }, $string_out);
                    $string_out = array_filter($string_out);
                    //add string_in to string_out
                    $string_out[] = $string_in;
                    $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                    $count = $data[1];
                    $data = $data[0];
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out'));
                } elseif (strpos($request->duong_su2, '"') === false && strpos($request->tai_san2, '"') !== false) {
                    $data = $this->XuLyTimKiem_2_o_tai_san($request->tai_san2, $request->duong_su2, 1, $request);
                    $string_in = $data[2] ?? '';
                    $string_in = str_replace('"', '', $string_in);
                    $string_out = $data[3] ?? [];
                    $string_out = array_map(function ($item) {
                        return str_replace('""', '', $item);
                    }, $string_out);
                    $string_out = array_filter($string_out);
                    //add string_in to string_out
                    $string_out[] = $string_in;
                    $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                    $count = $data[1];
                    $data = $data[0];
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out'));
                } else {
                    $query = $this->client->createSelect();
                    $query2 = $this->client->createSelect();
                    $query->setQuery('duong_su:' . '(' . $request->duong_su2 . ') AND texte:' . '(' . $request->tai_san2 . ') AND ngan_chan:(3)')->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                    $resultset = $this->client->select($query);
                    $data = $this->fetchData($resultset);
                    $optionPaginate = ['path' => route('searchSolr_nganchan'), 'query' => $request->query()];
                    $count = $resultset->getNumFound();
                    $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                    $string_out = explode(" ", $search);
                    $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out'));
                }
            } elseif (preg_match('/[\x80-\xFF]/', $duong_su2) && preg_match('/[\x80-\xFF]/', $tai_san2) == false) {
                if (strpos($duong_su2, '"') !== false && strpos($tai_san2, '"') !== false) {
                    // cả 2 đều có ""
                    dd('cc');
                    $data = $this->XuLyTimKiem_2_o_1_0($tai_san2, $duong_su2, 1, $request);
                    $string_in = $data[2] ?? '';
                    $string_in = str_replace('"', '', $string_in);
                    $string_out = $data[3] ?? [];
                    //add string_in to string_out
                    $string_out[] = $string_in;
                    $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                    $count = $data[1];
                    $data = $data[0];
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out'));
                } elseif (strpos($duong_su2, '"') !== false && strpos($tai_san2, '"') === false) {
                    $data = $this->XuLyTimKiem_2_o_duong_su($tai_san, $duong_su2, 1, $request);
                    $string_in = $data[2] ?? '';
                    $string_in = str_replace('"', '', $string_in);
                    $string_out = $data[3] ?? [];
                    //add string_in to string_out
                    $string_out[] = $string_in;
                    $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                    $count = $data[1];
                    $data = $data[0];
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out'));
                } elseif (strpos($duong_su2, '"') === false && strpos($tai_san2, '"') !== false) {
                    $data = $this->XuLyTimKiem_2_o_tai_san($tai_san2, $duong_su2, 1, $request);
                    $string_in = $data[2] ?? '';
                    $string_in = str_replace('"', '', $string_in);
                    $string_out = $data[3] ?? [];
                    //add string_in to string_out
                    $string_out[] = $string_in;
                    $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                    $count = $data[1];
                    $data = $data[0];
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out'));
                } else {
                    $query = $this->client->createSelect();
                    $query2 = $this->client->createSelect();
                    $query->setQuery('duong_su:' . '(' . $duong_su2 . ') AND texte:' . '(' . $tai_san2 . ') AND ngan_chan:(3)')->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                    $resultset = $this->client->select($query);
                    $data = $this->fetchData($resultset);
                    $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                    $count = $resultset->getNumFound();
                    $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                    $string_out = explode(" ", $search);
                    $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out'));
                }
            } else {
                if (strpos($request->duong_su2, '"') !== false && strpos($request->tai_san2, '"') !== false) {
                    // cả 2 đều có ""
                    $data = $this->XuLyTimKiem_2_o($request->tai_san2, $request->duong_su2, 2, $request);
                    $string_in = $data[2] ?? '';
                    $string_in = str_replace('"', '', $string_in);
                    $string_out = $data[3] ?? [];
                    $string_out = array_map(function ($item) {
                        return str_replace('""', '', $item);
                    }, $string_out);
                    $string_out = array_filter($string_out);
                    //add string_in to string_out
                    $string_out[] = $string_in;
                    $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                    $count = $data[1];
                    $data = $data[0];
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out'));
                } elseif (strpos($request->duong_su2, '"') !== false && strpos($request->tai_san2, '"') === false) {
                    $data = $this->XuLyTimKiem_2_o_duong_su($request->tai_san2, $request->duong_su2, 2, $request);
                    $string_in = $data[2] ?? '';
                    $string_in = str_replace('"', '', $string_in);
                    $string_out = $data[3] ?? [];
                    $string_out = array_map(function ($item) {
                        return str_replace('""', '', $item);
                    }, $string_out);
                    $string_out = array_filter($string_out);
                    //add string_in to string_out
                    $string_out[] = $string_in;
                    $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                    $count = $data[1];
                    $data = $data[0];
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out'));
                } elseif (strpos($request->duong_su2, '"') === false && strpos($request->tai_san2, '"') !== false) {
                    $data = $this->XuLyTimKiem_2_o_tai_san($request->tai_san2, $request->duong_su2, 2, $request);
                    $string_in = $data[2] ?? '';
                    $string_in = str_replace('"', '', $string_in);
                    $string_out = $data[3] ?? [];
                    $string_out = array_map(function ($item) {
                        return str_replace('""', '', $item);
                    }, $string_out);
                    $string_out = array_filter($string_out);
                    //add string_in to string_out
                    $string_out[] = $string_in;
                    $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                    $count = $data[1];
                    $data = $data[0];
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out'));
                } else {
                    $query = $this->client->createSelect();
                    $query2 = $this->client->createSelect();
                    $query->setQuery('duong_su_en:' . '(' . $request->duong_su2 . ') AND texte_en:' . '(' . $request->tai_san2 . ') AND ngan_chan:(3)')->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                    $resultset = $this->client->select($query);
                    $data = $this->fetchData($resultset);
                    $optionPaginate = ['path' => route('searchSolr_nganchan'), 'query' => $request->query()];
                    $count = $resultset->getNumFound();
                    $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                    $string_out = explode(" ", $search . ' ' . $tai_san);
                    $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out'));
                }
            }
        }
        //Tìm theo đương sự
        if ($request->duong_su2 != null) {
            $search = $request->duong_su2;
            //check is request vietnamese string ?
            if (preg_match('/[\x80-\xFF]/', $search)) {
                //Chứa dấu " "
                if (strpos($search, '"') !== false) {
                    $data = $this->XuLyTimKiem_duong_su($search, 1, $request);
                    $string_in = $data[2] ?? '';
                    $string_in = str_replace('"', '', $string_in);
                    $string_out = $data[3] ?? [];
                    $string_out = array_map(function ($item) {
                        return str_replace('""', '', $item);
                    }, $string_out);
                    $string_out = array_filter($string_out);
                    //add string_in to string_out
                    $string_out[] = $string_in;
                    $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                    $count = $data[1];
                    $data = $data[0];
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out'));
                } else {
                    $page = $request->page ?? 1;
                    $numberInPage = 20;
                    $query = $this->client->createSelect();
                    $query2 = $this->client->createSelect();
                    $query->setQuery('duong_su:' . '(' . $search . ') AND ngan_chan:(3)')->setQueryDefaultOperator('OR')->addSort('st_id', $query::SORT_DESC)->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                    $resultset = $this->client->select($query);
                    $data = $this->fetchData($resultset);
                    $optionPaginate = ['path' => route('searchSolr_nganchan'), 'query' => $request->query()];
                    $count = $resultset->getNumFound();
                    $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                    $string_out = explode(" ", $search);
                    $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out'));
                }
            }
            //Tìm không dấu
            else {
                if (strpos($search, '"') !== false) {
                    $data = $this->XuLyTimKiem_duong_su($search, 2, $request);
                    $string_in = $data[2] ?? '';
                    $string_in = str_replace('"', '', $string_in);
                    $string_out = $data[3] ?? [];
                    $string_out = array_map(function ($item) {
                        return str_replace('""', '', $item);
                    }, $string_out);
                    $string_out = array_filter($string_out);
                    //add string_in to string_out
                    $string_out[] = $string_in;
                    $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                    $count = $data[1];
                    $data = $data[0];
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out'));
                } else {
                    $page = $request->page ?? 1;
                    $numberInPage = 20;
                    $query = $this->client->createSelect();
                    $query->setQuery('duong_su_en:' . $search);
                    $query2 = $this->client->createSelect();
                    $query->setQuery('duong_su_en:' . '(' . $search . ') AND ngan_chan:(3)')->setQueryDefaultOperator('AND')->addSort('st_id', $query::SORT_DESC)->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                    $resultset = $this->client->select($query);
                    $data = $this->fetchData($resultset);
                    $optionPaginate = ['path' => route('searchSolr_nganchan'), 'query' => $request->query()];
                    $count = $resultset->getNumFound();
                    $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                    $string_out = explode(" ", $search);
                    $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out'));
                }
            }
        } //Tìm theo tài sản
        elseif ($request->tai_san2 != null) {
            $search = $request->tai_san2;
            //check is request vietnamese string ?
            if (preg_match('/[\x80-\xFF]/', $search)) {
                //Chứa dấu " "
                if (strpos($search, '"') !== false) {
                    $data = $this->XuLyTimKiem_tai_san($search, 1, $request);
                    $string_in = $data[2] ?? '';
                    $string_in = str_replace('"', '', $string_in);
                    $string_out = $data[3] ?? [];
                    $string_out = array_map(function ($item) {
                        return str_replace('""', '', $item);
                    }, $string_out);
                    $string_out = array_filter($string_out);
                    //add string_in to string_out
                    $string_out[] = $string_in;
                    $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                    $count = $data[1];
                    $data = $data[0];
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out'));
                } else {
                    $page = $request->page ?? 1;
                    $numberInPage = 20;
                    $query = $this->client->createSelect();
                    $query2 = $this->client->createSelect();
                    $query->setQuery('texte:' . '(' . $search . ') AND ngan_chan:(3)')->setQueryDefaultOperator('AND')->addSort('st_id', $query::SORT_DESC)->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                    $resultset = $this->client->select($query);
                    $data = $this->fetchData($resultset);
                    $optionPaginate = ['path' => route('searchSolr_nganchan'), 'query' => $request->query()];
                    $count = $resultset->getNumFound();
                    $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                    $string_out = explode(" ", $search);
                    $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out'));
                }
            }
            //Tìm không dấu
            else {
                //Chứa dấu " "
                if (strpos($search, '"') !== false) {
                    $data = $this->XuLyTimKiem_tai_san($search, 2, $request);
                    $string_in = $data[2] ?? '';
                    $string_in = str_replace('"', '', $string_in);
                    $string_out = $data[3] ?? [];
                    $string_out = array_map(function ($item) {
                        return str_replace('""', '', $item);
                    }, $string_out);
                    $string_out = array_filter($string_out);
                    //add string_in to string_out
                    $string_out[] = $string_in;
                    $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                    $count = $data[1];
                    $data = $data[0];
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out'));
                } else {
                    $page = $request->page ?? 1;
                    $numberInPage = 20;
                    $query = $this->client->createSelect();
                    $query2 = $this->client->createSelect();
                    $query->setQuery('texte_en:' . '(' . $search . ') AND ngan_chan:(3)')->setQueryDefaultOperator('AND')->addSort('st_id', $query::SORT_DESC)->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                    $resultset = $this->client->select($query);
                    $data = $this->fetchData($resultset);
                    $optionPaginate = ['path' => route('searchSolr_nganchan'), 'query' => $request->query()];
                    $count = $resultset->getNumFound();
                    $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                    $string_out = explode(" ", $search);
                    $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out'));
                }
            }
        } //Tìm tất cả
        else {
            // dd(1);
            $search = $request->tat_ca2;
            // dd($search);
            $search = $this->vn_to_str($search);
            // dd($option);
            if (preg_match('/[\x80-\xFF]/', $search)) {
                //Chứa dấu " "
                if (strpos($search, '"') !== false) {
                    $data = $this->XuLyTimKiem_tat_ca($search, 1, $request);
                    $string_in = $data[2] ?? '';
                    $string_in = str_replace('"', '', $string_in);
                    $string_out = $data[3] ?? [];
                    $string_out = array_map(function ($item) {
                        return str_replace('""', '', $item);
                    }, $string_out);
                    $string_out = array_filter($string_out);
                    //add string_in to string_out
                    $string_out[] = $string_in;
                    $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                    $count = $data[1];
                    $data = $data[0];
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out'));
                } else {
                    $page = $request->page ?? 1;
                    $numberInPage = 20;
                    $query = $this->client->createSelect();
                    $query->setQuery('texte:' . '(' . $search . ') duong_su:' . '(' . $search . ') AND ngan_chan:(3) ')->setQueryDefaultOperator('AND')->addSort('st_id', $query::SORT_DESC)->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                    $query2 = $this->client->createSelect();
                    $query->setQueryDefaultOperator('OR')->addSort('st_id', $query::SORT_DESC)->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                    $resultset = $this->client->select($query);
                    $data = $this->fetchData($resultset);
                    $optionPaginate = ['path' => route('searchSolr_nganchan'), 'query' => $request->query()];
                    $count = $resultset->getNumFound();
                    $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                    $string_out = explode(" ", $search);
                    $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out'));
                }
            }
            
            //Tìm không dấu
            else {
                // dd(1);
                if (strpos($search, '"') !== false) {
                    $data = $this->XuLyTimKiem_tat_ca($search, 2, $request);
                    $string_in = $data[2] ?? '';
                    $string_in = str_replace('"', '', $string_in);
                    $string_out = $data[3] ?? [];
                    $string_out = array_map(function ($item) {
                        return str_replace('""', '', $item);
                    }, $string_out);
                    $string_out = array_filter($string_out);
                    //add string_in to string_out
                    $string_out[] = $string_in;
                    $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                    $count = $data[1];
                    $data = $data[0];
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out'));
                } else {
                    // dd(1);
                    $page = $request->page ?? 1;
                    $numberInPage = 20;
                    $query = $this->client->createSelect();
                    $query->setQuery('merge_content:' . '(' . $search . ')  AND ngan_chan:(3)')->setQueryDefaultOperator('AND')->addSort('st_id', $query::SORT_DESC)->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                    $query2 = $this->client->createSelect();
                    $query->setQueryDefaultOperator('OR')->addSort('st_id', $query::SORT_DESC)->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                    $resultset = $this->client->select($query);
                    $data = $this->fetchData($resultset);
                    $optionPaginate = ['path' => route('searchSolr_nganchan'), 'query' => $request->query()];
                    $count = $resultset->getNumFound();
                    $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                    $string_out = explode(" ", $search);
                    $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                    return view('search', compact('data', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out'));
                }
            }
        }
    }
    //print data from solr
    public function printSolr(Request $request)
    {
        $role = Sentinel::check();
        $user_id =  $role->getAttributes();
        $user_id = $user_id['id'];
        $vpcc_id = NhanVienModel::where('nv_id', $user_id)->first()->nv_vanphong;
        $vpcc_name = ChinhanhModel::where('cn_id', $vpcc_id)->first()->cn_ten;
        $user_name = NhanVienModel::where('nv_id', $user_id)->first()->nv_hoten;
        $search = $request->all();
        $Solr_Prevent_Print = new Solr_Prevent_Print($this->client);
        //filter item not null
        $search = array_filter($search);
        $search = implode(' ', $search);
        $search = str_replace('"', '', $search);
        $data =   $data = $Solr_Prevent_Print->search($request);
        $item = $data['data']->toArray();
        $count = count($item) ?? 0;
        $ipaddress = $request->fullUrl();

        // $history = HistorySearchModel::create([
        //     'user_id' => $user_id,
        //     'url' => $request->fullUrl(),
        //     'client_ip' => $ipaddress,
        //     'vp_id' => $vpcc_id,
        //     'file' => $nameFile,
        // ]);

        return view('admin.suutra.printSolr', compact('item', 'count', 'search', 'ipaddress', 'user_name', 'vpcc_name'));
    }
    
}
