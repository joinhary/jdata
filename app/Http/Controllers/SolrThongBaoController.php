<?php

namespace App\Http\Controllers;

use App\Models\SolrCheckModel;
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
use App\Http\Controllers\Solr_Basic_Print;
use App\Http\Controllers\SolariumController_nganchan;
use Carbon\Carbon;
use App\Models\HistorySearchModel;
use PDF;
use Str;
use Dompdf\Dompdf;


class SolrThongBaoController extends Controller
{
    protected $client;
    protected $count_total;
    protected $count_ngan_chan;

    public function __construct(\Solarium\Client $client)
    {
        $this->client = $client;
        $this->client->getEndpoint()->setCore('thongbaochung');
        $query = $this->client->createSelect();
        $query->setQuery("*:*")->setQueryDefaultOperator('OR')->setStart(0)->setRows(20);
        $resultset = $this->client->select($query);
    }
    public function paginate($items, $perPage = 100, $page = null, $options = [], $total)
    {
        // $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        // $items = $items instanceof Collection ? $items : Collection::make($items);

        // $options = array_merge($options, ['pageName' => 'page']);
        // // dd($pageT->links());
        // return new LengthAwarePaginator($items, $total, $perPage, $page, $options);
        $data  = [
            'total' => $total,
            'data' => $items,
        ];
        return $data;
    }
    public function fetchData($resultset)
    {
        $data = [];
        // $role = Sentinel::check();
        // if($role){
        //     $user_id =  $role->getAttributes();
        //     $user_id = $user_id['id'];
        // }else{
        //     return view('admin.login');
        // }
        // $vpcc_id = NhanVienModel::where('nv_id', $user_id)->first()->nv_vanphong;
        // $cn_ndd = ChiNhanhModel::where('cn_id', (int)$vpcc_id)->first()->code_cn;
        // $code_cn = ChiNhanhModel::where('cn_id', (int)$vpcc_id)->first()->code_cn;
        foreach ($resultset as $document) {
            // $data = $document->getFields();
            //Le cam lanh
            // if($document->st_id[0] == 1139110 && $code_cn != "TMH" ){
            //     continue;
            // }
            $data[] = [
                'id' => $document->id,
                'tieu_de' => $document->tieu_de,
                'noi_dung' => $document->noi_dung,
                'nv_id' => $document->nv_id,
                'vp_id' => $document->vp_id,
                'created_at' => $document->created_at,
                'updated_at' => $document->updated_at,
                'type' => $document->type,
                'duong_su' => $document->duong_su,
                'duong_su_en' => $document->duong_su_en,
                'texte' => $document->texte,
                'texte_en' => $document->texte_en,
                'file' => $document->file,

            ];
        }
        //orderby ngay_nhap
        $data = collect($data);
        return $data;
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
            $string_out = [];
            foreach ($search2 as $key => $val) {
                //append to query string
                if ($val != "" && $option == 1) {
                    $sub_string_query =  ' OR duong_su:' . '(' . '"' . $search1 . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } elseif ($val != "" && $option == 2) {
                    $sub_string_query =  ' OR duong_su_en:' . '(' . '"' . $search1 . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } else {
                    continue;
                }
                $string_out[] = $val;
            }
            $page = $request->page ?? 1;
            $numberInPage = 20;
            $query->setQuery($string_query)->setQueryDefaultOperator('OR');
            $resultset = $this->client->select($query);
            $count = $resultset->getNumFound();

            $data = $this->fetchData($resultset);
            //oday
            $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];

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
                $string_query = '{!complexphrase} duong_su:' . '(' . '"' . $search1 . '"' . ')';
            } else {
                $string_query =  '{!complexphrase} duong_su_en:' . '(' . '"' . $search1 . '"' . ')';
            }
            $string_out = [];
            foreach ($search2 as $key => $val) {
                if ($val && $option == 1) {
                    $sub_string_query =   ' OR duong_su:' . '"' . $search1  . ' *' . $val . '*' . '"~10000';
                    $string_query .= $sub_string_query;
                } elseif ($val && $option == 2) {
                    $sub_string_query =   ' OR duong_su_en:' . '"' . $search1  . ' *' . $val . '*' . '"~10000';
                    $string_query .= $sub_string_query;
                } else {
                    continue;
                }
                $string_out[] = $val;
            }
            // $string_query =  ' {!complexphrase} duong_su:' . '(' . '"' . $search1 . '"' . ') OR duong_su:' . '"' . $search1  . ' *' . $val . '*' . '"' . '~1000';
            // $string_query=preg_replace('/\*+/', '*', $string_query);
            $page = $request->page ?? 1;
            $numberInPage = 20;
            $query->setQuery($string_query)->setQueryDefaultOperator('OR');
            $resultset = $this->client->select($query);
            $count = $resultset->getNumFound();
            $data = $this->fetchData($resultset);
            //oday
            $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];

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
                $string_query = 'texte:' . '(' . '"' . $search1 . '"' . ')';
            } else {
                $string_query = 'texte_en:' . '(' . '"' . $search1 . '"' . ')';
            }
            $string_out = [];
            foreach ($search2 as $key => $val) {
                //append to query string
                if ($val != "" && $option == 1) {
                    $sub_string_query =  ' OR texte:' . '(' . '"' . $search1 . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } elseif ($val != "" && $option == 2) {
                    $sub_string_query =  ' OR texte_en:' . '(' . '"' . $search1 . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } else {
                    continue;
                }
                $string_out[] = $val;
            }
            $page = $request->page ?? 1;
            $numberInPage = 20;
            $query->setQuery($string_query)->setQueryDefaultOperator('OR');
            $resultset = $this->client->select($query);
            $data = $this->fetchData($resultset);
            $count = $resultset->getNumFound();
            //oday
            $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
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
                $string_query = '{!complexphrase} texte:' . '(' . '"' . $search1 . '"' . ')';
            } else {
                $string_query =  '{!complexphrase} texte_en:' . '(' . '"' . $search1 . '"' . ')';
            }
            $string_out = [];
            foreach ($search2 as $key => $val) {
                if ($val && $option == 1) {
                    $sub_string_query =   ' OR texte:' . '"' . $search1  . ' *' . $val . '*' . '"~10000';
                    $string_query .= $sub_string_query;
                } elseif ($val && $option == 2) {
                    $sub_string_query =   ' OR texte_en:' . '"' . $search1  . ' *' . $val . '*' . '"~10000';
                    $string_query .= $sub_string_query;
                } else {
                    continue;
                }
                $string_out[] = $val;
            }
            $page = $request->page ?? 1;
            $numberInPage = 20;
            $query->setQuery($string_query)->setQueryDefaultOperator('OR');
            $resultset = $this->client->select($query);
            $data = $this->fetchData($resultset);
            $count = $resultset->getNumFound();
            //oday
            $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
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
                $string_query = 'duong_su_en:' . '(' . '"' . $search1 . '"' . ') OR texte_en:' . '(' . '"' . $search1 . '"' . ')';
            } else {
                $string_query = 'merge_content:' . '(' . '"' . $search1 . '"' . ')';
            }
            $string_out = [];
            foreach ($search2 as $key => $val) {
                if ($val && $option == 1) {
                    $sub_string_query =  ' OR merge_content:' . '(' . '"' . $search1 . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } elseif ($val && $option == 2) {
                    $val = str_replace('-', " ", $val);
                    $sub_string_query =  ' OR merge_content:' . '(' . '"' . $search1 . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } else {
                    continue;
                }
                $string_out[] = $val;
            }
            $page = $request->page ?? 1;
            $numberInPage = 20;
            $query->setQuery($string_query)->setQueryDefaultOperator('OR');
            $resultset = $this->client->select($query);
            $data = $this->fetchData($resultset);
            $count = $resultset->getNumFound();
            //oday
            $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
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
                $string_query = 'duong_su_en:' . '(' . '"' . $search1 . '"' . ') OR texte_en:' . '(' . '"' . $search1 . '"' . ')';
            } else {
                $string_query = 'duong_su_en:' . '(' . '"' . $search1 . '"' . ') OR texte_en:' . '(' . '"' . $search1 . '"' . ')  OR so_cv:' . '(' . '"' . $search1 . '"' . ')';
            }
            $string_out = [];
            foreach ($search2 as $key => $val) {
                //append to query string
                if ($val != "" && $option == 1) {
                    $sub_string_query =  ' OR duong_su_en:' . '(' . '"' . $search1 . '"' . '*' . $val . '*' . ') OR texte_en:' . '(' . '"' . $search1 . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } elseif ($val != "" && $option == 2) {
                    $sub_string_query =  ' OR duong_su_en:' . '(' . '"' . $search1 . '"' . '*' . $val . '*' . ') OR texte_en:' . '(' . '"' . $search1 . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } else {
                    continue;
                }
                $string_out[] = $val;
            }
            $page = $request->page ?? 1;
            $numberInPage = 20;
            $query->setQuery($string_query)->setQueryDefaultOperator('AND')->addSort('id', $query::SORT_DESC);
            $resultset = $this->client->select($query);
            $data = $this->fetchData($resultset);
            $count = $resultset->getNumFound();
            //oday
            $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
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
                $string_query = 'texte_en:' . '(' . '"' . $texte . '"' . ') OR duong_su_en:' . '(' . '"' . $duong_su . '"' . ')';
            } else {
                $string_query = 'texte_en:' . '(' . '"' . $texte . '"' . ') OR duong_su_en:' . '(' . '"' . $duong_su . '"' . ')';
            }
            $search1 = $duong_su;
            $string_out[] = $texte;
            foreach ($search2 as $key => $val) {
                //append to query string
                if ($val != "" && $option == 1) {
                    $sub_string_query =  ' OR texte_en:' . '(' . '"' . $texte . '"' . '*' . $val . '*' . ') OR duong_su_en:' . '(' . '"' . $duong_su . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } elseif ($val != "" && $option == 2) {
                    $sub_string_query =  ' OR texte_en:' . '(' . '"' . $texte . '"' . '*' . $val . '*' . ') OR duong_su_en:' . '(' . '"' . $duong_su . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } else {
                    continue;
                }
                $string_out[] = $val;
            }
            $page = $request->page ?? 1;
            $numberInPage = 20;
            $query->setQuery($string_query)->setQueryDefaultOperator('AND')->addSort('id', $query::SORT_DESC);
            $resultset = $this->client->select($query);
            $data = $this->fetchData($resultset);
            $count = $resultset->getNumFound();
            //oday
            $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
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
            $texte_hl = explode(' ', $texte);
            $string_out = [];
            foreach ($texte_hl as $item) {
                $string_out[] = $item;
            }
            $search1 = $duong_su1;
            $query = $this->client->createSelect();
            if ($option === 1) {
                $string_query = 'duong_su_en:' . '(' . '"' . $duong_su1 . '"' . ') OR texte_en:' . '(' . $texte  . ')';
            } else {
                $string_query = 'duong_su_en:' . '(' . '"' . $duong_su1 . '"' . ') OR texte_en:' . '(' .  $texte  . ')';
            }
            foreach ($search2 as $key => $val) {
                //append to query string
                if ($val != "" && $option == 1) {
                    $sub_string_query =  ' OR duong_su_en:' . '(' . '"' . $duong_su1 . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } elseif ($val != "" && $option == 2) {
                    $sub_string_query =  ' OR duong_su_en:' . '(' . '"' . $duong_su1 . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } else {
                    continue;
                }
                $string_out[] = $val;
            }
            $page = $request->page ?? 1;
            $numberInPage = 20;
            $query->setQuery($string_query)->setQueryDefaultOperator('AND');
            $resultset = $this->client->select($query);
            $data = $this->fetchData($resultset);
            $count = $resultset->getNumFound();
            //oday
            $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
            $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
        } else {
            $data = [];
        }
        return [$data, $count, $search1, $string_out];
    }
    public function XuLyTimKiem_2_o_1_0($texte, $duongsu, $option, $request)
    {
        if (preg_match_all('/\"([^\"]*?)\"/', $duongsu, $matches1) && preg_match_all('/\"([^\"]*?)\"/', $texte, $matches2)) {
            //phần trong ""
            $duong_su = implode('', $matches1[0]);
            $duong_su = str_replace('"', '', $duongsu);
            $texte = implode('', $matches2[0]);
            $texte = str_replace('"', '', $texte);
            //Lọc lấy phần phía sau
            $data = str_replace($duong_su, '', $duongsu);
            //phần sau ""
            $search2 = explode(' ', $data);
            $search1[] = $duong_su;
            $search1[] = $texte;
            $string_out = [];
            $query = $this->client->createSelect();
            if ($option === 1) {
                $string_query = 'texte_en:' . '(' . '"' . $texte . '"' . ') OR duong_su:' . '(' . '"' . $duong_su . '"' . ')';
            } else {
                $string_query = 'texte:' . '(' . '"' . $texte . '"' . ') OR duong_su_en:' . '(' . '"' . $duong_su . '"' . ')';
            }
            foreach ($search2 as $key => $val) {
                //append to query string
                if ($val != "" && $option == 1) {
                    $sub_string_query =  ' OR texte_en:' . '(' . '"' . $texte . '"' . '*' . $val . '*' . ') OR duong_su:' . '(' . '"' . $duong_su . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } elseif ($val != "" && $option == 2) {
                    $sub_string_query =  ' OR texte:' . '(' . '"' . $texte . '"' . '*' . $val . '*' . ') OR duong_su_en:' . '(' . '"' . $duong_su . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } else {
                    continue;
                }
                $string_out[] = $val;
            }
            $page = $request->page ?? 1;
            $numberInPage = 20;
            $query->setQuery($string_query);

            $resultset = $this->client->select($query);
            $data = $this->fetchData($resultset);
            $count = $resultset->getNumFound();
            //oday
            $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
            $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
        } else {
            $data = [];
        }
        return [$data, $count, $search1, $string_out];
    }
    public function XuLyTimKiem_2_o_duong_su_1_0($texte, $duongsu, $option, $request)
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
            $search1[] = $duong_su1;
            $search1[] = $texte;
            $string_out = [];
            $query = $this->client->createSelect();
            if ($option === 1) {
                $string_query = 'duong_su:' . '(' . '"' . $duong_su1 . '"' . ') OR texte_en:' . '(' . $texte  . ')';
            } else {
                $string_query = 'duong_su_en:' . '(' . '"' . $duong_su1 . '"' . ') OR texte:' . '(' .  $texte  . ')';
            }
            foreach ($search2 as $key => $val) {
                //append to query string
                if ($val != "" && $option == 1) {
                    $sub_string_query =  ' OR duong_su:' . '(' . '"' . $duong_su1 . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } elseif ($val != "" && $option == 2) {
                    $sub_string_query =  ' OR duong_su_en:' . '(' . '"' . $duong_su1 . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } else {
                    continue;
                }
                $string_out[] = $val;
            }
            $page = $request->page ?? 1;
            $numberInPage = 20;
            $query->setQuery($string_query)->setQueryDefaultOperator('AND');
            $resultset = $this->client->select($query);
            $data = $this->fetchData($resultset);
            $count = $resultset->getNumFound();
            //oday
            $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
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
            $string_out = [];
            $search1 = $duong_su;
            $string_out[] = $texte1;
            //replace " in search2
            $search2 = str_replace('"', '', $search2);
            $query = $this->client->createSelect();
            if ($option === 1) {
                $string_query = 'texte_en:' . '(' . '"' . $texte1 . '"' . ') OR duong_su_en:' . '(' . $duong_su  . ')';
            } else {
                $string_query = 'texte_en:' . '(' . '"' . $texte . '"' . ') OR duong_su_en:' . '(' .  $duong_su  . ')';
            }
            foreach ($search2 as $key => $val) {
                //append to query string
                if ($val !== ""  && $option == 1) {
                    $sub_string_query =  ' OR texte_en:' . '(' . '"' . $texte1 . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } elseif ($val !== ""  && $option == 2) {
                    $sub_string_query =  ' OR texte_en:' . '(' . '"' . $texte1 . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } else {
                    continue;
                }
                $string_out[] = $val;
            }
            $page = $request->page ?? 1;
            $numberInPage = 20;
            $query->setQuery($string_query)->setQueryDefaultOperator('AND');
            //sort
            $resultset = $this->client->select($query);
            $data = $this->fetchData($resultset);
            $count = $resultset->getNumFound();
            //oday
            $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
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
    public function searchApi(Request $request)
    {
        try {
            $option = $request->all();
            if ($option == []) {
                $page = $request->page ?? 1;
                $numberInPage = 20;
                $query = $this->client->createSelect();

                $query->setQuery("*:*")->setQueryDefaultOperator('OR');
                $resultset = $this->client->select($query);
                $data = $this->fetchData($resultset);
                $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                $count = $resultset->getNumFound();
                $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                $string_out = [];
                return $data;
            }
            if ($request->duong_su == null && $request->tai_san == null && $request->tat_ca == null) {
                $page = $request->page ?? 1;
                $numberInPage = 20;
                $query = $this->client->createSelect();
                $query->setQuery("*:*")->setQueryDefaultOperator('OR')->addSort('id', $query::SORT_DESC);
                $resultset = $this->client->select($query);
                $data = $this->fetchData($resultset);
                $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                $count = $resultset->getNumFound();
                $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                $string_out = [];
                return $data;
            }
            //Tìm 2 ô đương sự và tài sản
            if ($request->duong_su != null && $request->tai_san != null && $request->tat_ca == null) {
                $duong_su = $this->vn_to_str($request->duong_su);
                $tai_san = $this->vn_to_str($request->tai_san);
                $search = $duong_su;
                $page = $request->page ?? 1;
                $numberInPage = 20;
                if (preg_match('/[\x80-\xFF]/', $duong_su) && preg_match('/[\x80-\xFF]/', $tai_san)) {
                    //Chứa dấu " "
                    if (strpos($duong_su, '"') !== false && strpos($tai_san, '"') !== false) {
                        // cả 2 đều có ""
                        $data = $this->XuLyTimKiem_2_o($tai_san, $duong_su, 1, $request);
                        $count = $data[1];
                        $data = $data[0];
                        return $data;
                    } elseif (strpos($duong_su, '"') !== false && strpos($tai_san, '"') === false) {
                        $data = $this->XuLyTimKiem_2_o_duong_su($tai_san, $duong_su, 1, $request);
                        $count = $data[1];
                        $data = $data[0];
                        return $data;
                    } elseif (strpos($duong_su, '"') === false && strpos($tai_san, '"') !== false) {
                        $data = $this->XuLyTimKiem_2_o_tai_san($tai_san, $duong_su, 1, $request);
                        $count = $data[1];
                        $data = $data[0];
                        return $data;
                    } else {
                        $query = $this->client->createSelect();

                        $query->setQuery('duong_su:' . '(' . $duong_su . ') OR texte:' . '(' . $tai_san . ')');
                        $resultset = $this->client->select($query);
                        $data = $this->fetchData($resultset);
                        $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                        $count = $resultset->getNumFound();
                        $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                        return $data;
                    }
                } elseif (preg_match('/[\x80-\xFF]/', $duong_su) && preg_match('/[\x80-\xFF]/', $tai_san) == false) {
                    //dayne 
                    //Chứa dấu " "
                    if (strpos($duong_su, '"') !== false && strpos($tai_san, '"') !== false) {
                        // cả 2 đều có ""
                        $data = $this->XuLyTimKiem_2_o_1_0($tai_san, $duong_su, 1, $request);
                        $string_in = $data[2] ?? '';
                        $string_in = str_replace('"', '', $string_in);
                        $string_out = $data[3] ?? [];
                        //add string_in to string_out
                        $string_out[] = $string_in;
                        $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                        $count = $data[1];
                        $data = $data[0];
                        return $data;
                    } elseif (strpos($duong_su, '"') !== false && strpos($tai_san, '"') === false) {
                        $data = $this->XuLyTimKiem_2_o_duong_su($tai_san, $duong_su, 1, $request);
                        $string_in = $data[2] ?? '';
                        $string_in = str_replace('"', '', $string_in);
                        $string_out = $data[3] ?? [];
                        //add string_in to string_out
                        $string_out[] = $string_in;
                        $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                        $count = $data[1];
                        $data = $data[0];
                        return $data;
                    } elseif (strpos($duong_su, '"') === false && strpos($tai_san, '"') !== false) {
                        $data = $this->XuLyTimKiem_2_o_tai_san($tai_san, $duong_su, 1, $request);
                        $string_in = $data[2] ?? '';
                        $string_in = str_replace('"', '', $string_in);
                        $string_out = $data[3] ?? [];
                        //add string_in to string_out
                        $string_out[] = $string_in;
                        $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                        $count = $data[1];
                        $data = $data[0];
                        return $data;
                    } else {
                        $query = $this->client->createSelect();

                        $query->setQuery('duong_su:' . '(' . $duong_su . ') OR texte:' . '(' . $tai_san . ')');
                        $resultset = $this->client->select($query);
                        $data = $this->fetchData($resultset);
                        $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                        $count = $resultset->getNumFound();
                        $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                        $string_out = explode(" ", $search);
                        $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                        return $data;
                    }
                } elseif (preg_match('/[\x80-\xFF]/', $duong_su) == false && preg_match('/[\x80-\xFF]/', $tai_san)) {
                    if (strpos($duong_su, '"') !== false && strpos($tai_san, '"') !== false) {
                        // cả 2 đều có ""
                        $data = $this->XuLyTimKiem_2_o_1_0($tai_san, $duong_su, 2, $request);
                        $string_in = $data[2] ?? '';
                        $string_in = str_replace('"', '', $string_in);
                        $string_out = $data[3] ?? [];
                        //add string_in to string_out
                        $string_out[] = $string_in;
                        $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                        $count = $data[1];
                        $data = $data[0];
                        return $data;
                    } elseif (strpos($duong_su, '"') !== false && strpos($tai_san, '"') === false) {
                        $data = $this->XuLyTimKiem_2_o_duong_su_1_0($tai_san, $duong_su, 2, $request);
                        $string_in = $data[2] ?? '';
                        $string_in = str_replace('"', '', $string_in);
                        $string_out = $data[3] ?? [];
                        //add string_in to string_out
                        $string_out[] = $string_in;
                        $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                        $count = $data[1];
                        $data = $data[0];
                        return $data;
                    } elseif (strpos($duong_su, '"') === false && strpos($tai_san, '"') !== false) {
                        $data = $this->XuLyTimKiem_2_o_tai_san($tai_san, $duong_su, 1, $request);
                        $string_in = $data[2] ?? '';
                        $string_in = str_replace('"', '', $string_in);
                        $string_out = $data[3] ?? [];
                        //add string_in to string_out
                        $string_out[] = $string_in;
                        $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                        $count = $data[1];
                        $data = $data[0];
                        return $data;
                    } else {
                        $query = $this->client->createSelect();

                        $query->setQuery('duong_su:' . '(' . $duong_su . ') OR texte:' . '(' . $tai_san . ')');
                        $resultset = $this->client->select($query);
                        $data = $this->fetchData($resultset);
                        $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                        $count = $resultset->getNumFound();
                        $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                        $string_out = explode(" ", $search);
                        $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                        return $data;
                    }
                } else {
                    if (strpos($duong_su, '"') !== false && strpos($tai_san, '"') !== false) {
                        // cả 2 đều có ""
                        $data = $this->XuLyTimKiem_2_o($tai_san, $duong_su, 2, $request);
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
                        return $data;
                    } elseif (strpos($duong_su, '"') !== false && strpos($tai_san, '"') === false) {
                        //here
                        $data = $this->XuLyTimKiem_2_o_duong_su($tai_san, $duong_su, 2, $request);
                        $string_in = $data[2] ?? '';
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
                        return $data;
                    } elseif (strpos($duong_su, '"') === false && strpos($tai_san, '"') !== false) {
                        $data = $this->XuLyTimKiem_2_o_tai_san($tai_san, $duong_su, 2, $request);
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
                        return $data;
                    } else {
                        $query = $this->client->createSelect();

                        $query->setQuery('duong_su_en:' . '(' . $duong_su . ') OR texte_en:' . '(' . $tai_san . ')')->setQueryDefaultOperator('OR');
                        $resultset = $this->client->select($query);
                        $data = $this->fetchData($resultset);
                        $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                        $count = $resultset->getNumFound();
                        $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                        $string_out = explode(" ", $search . ' ' . $tai_san);
                        $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                        return $data;
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
                        return $data;
                    } else {
                        $page = $request->page ?? 1;
                        $numberInPage = 20;
                        $query = $this->client->createSelect();

                        $query->setQuery('duong_su:' . '(' . $search . ')')->setQueryDefaultOperator('OR');
                        $resultset = $this->client->select($query);
                        $data = $this->fetchData($resultset);
                        $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                        $count = $resultset->getNumFound();
                        $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                        $string_out = explode(" ", $search);
                        $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                        return $data;
                    }
                }
                //Tìm không dấu
                else {
                    if (strpos($search, '"') !== false) {
                        $data = $this->XuLyTimKiem_duong_su($search, 2, $request);
                        $string_in = $data[2] ?? '';
                        $string_in = str_replace('"', '', $string_in);
                        $string_out = $data[3] ?? [];
                        //add string_in to string_out
                        $string_out[] = $string_in;
                        $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                        $count = $data[1];
                        $data = $data[0];
                        return $data;
                    } else {
                        $page = $request->page ?? 1;
                        $numberInPage = 20;
                        $query = $this->client->createSelect();
                        $query->setQuery('duong_su_en:' . $search);

                        $query->setQuery('duong_su_en:' . '(' . $search . ')')->setQueryDefaultOperator('OR');
                        $resultset = $this->client->select($query);
                        $data = $this->fetchData($resultset);
                        $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                        $count = $resultset->getNumFound();
                        $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                        $string_out = explode(" ", $search);
                        $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                        return $data;
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
                        $string_in = $data[2] ?? '';
                        $string_in = str_replace('"', '', $string_in);
                        $string_out = $data[3] ?? [];
                        $string_out = array_map(function ($item) {
                            return str_replace('""', '', $item);
                        }, $string_out);
                        $string_out = array_filter($string_out);
                        $count = $data[1];
                        $data = $data[0];
                        //here
                        //add string_in to string_out
                        $string_out[] = $string_in;
                        $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                        return $data;
                    } else {
                        $page = $request->page ?? 1;
                        $numberInPage = 20;
                        $query = $this->client->createSelect();

                        $query->setQuery('texte:' . '(' . $search . ')')->setQueryDefaultOperator('OR');
                        $resultset = $this->client->select($query);
                        $data = $this->fetchData($resultset);
                        $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                        $count = $resultset->getNumFound();
                        $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                        $string_out = explode(" ", $search);
                        $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                        return $data;
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
                        return $data;
                    } else {
                        $page = $request->page ?? 1;
                        $numberInPage = 20;
                        $query = $this->client->createSelect();

                        $query->setQuery('texte_en:' . '(' . $search . ')')->setQueryDefaultOperator('OR');
                        $resultset = $this->client->select($query);
                        $data = $this->fetchData($resultset);
                        $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                        $count = $resultset->getNumFound();
                        $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                        $string_out = explode(" ", $search);
                        $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                        return $data;
                    }
                }
            } //Tìm tất cả
            else {
                $search = $request->tat_ca;
                $search = str_replace('-', " ", $search);
                $search = $this->vn_to_str($search);
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
                        return $data;
                    } else {
                        $page = $request->page ?? 1;
                        $numberInPage = 20;
                        $query = $this->client->createSelect();
                        $query->setQuery('texte:' . '(' . $search . ') duong_su:' . '(' . $search . ')')->setQueryDefaultOperator('OR');

                        $query->setQueryDefaultOperator('OR');
                        $resultset = $this->client->select($query);
                        $data = $this->fetchData($resultset);
                        $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                        $count = $resultset->getNumFound();
                        $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                        $string_out = explode(" ", $search);
                        $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                        return $data;
                    }
                }
                //Tìm không dấu
                else {
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
                        return $data;
                    } else {
                        $page = $request->page ?? 1;
                        $numberInPage = 20;
                        $query = $this->client->createSelect();
                        $query->setQuery('merge_content:' . '(' . $search . ') ')->setQueryDefaultOperator('OR');

                        $resultset = $this->client->select($query);
                        $data = $this->fetchData($resultset);
                        $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                        $count = $resultset->getNumFound();
                        $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                        $string_out = explode(" ", $search);
                        $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                        return $data;
                    }
                }
            }
        } catch (\Solarium\Exception\HttpException $e) {
            $page = $request->page ?? 1;
            $numberInPage = 20;
            $query = $this->client->createSelect();

            $query->setQuery("*:*")->setQueryDefaultOperator('OR')->addSort('id', $query::SORT_DESC);
            $resultset = $this->client->select($query);
            $data = $this->fetchData($resultset);
            $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
            $count = $resultset->getNumFound();
            $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
            dd($e->getMessage());

            return $data;
        } catch (\Exception $e) {
            $page = $request->page ?? 1;
            $numberInPage = 20;
            $query = $this->client->createSelect();

            $query->setQuery("*:*")->setQueryDefaultOperator('OR')->addSort('id', $query::SORT_DESC);
            $resultset = $this->client->select($query);
            $data = $this->fetchData($resultset);
            $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
            $count = $resultset->getNumFound();
            $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
            return $data;
        }
    }

    public static function insert_solr($data)
    {
        // dd(json_encode($data1, JSON_UNESCAPED_UNICODE));
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
}
