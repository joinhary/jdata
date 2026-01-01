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
use App\Http\Controllers\SolrThongBaoController;
use Solarium\Client;

class SolariumController extends Controller
{
    protected $client;
    protected $count_total;
    protected $count_ngan_chan;

    public function __construct(\Solarium\Client $client)
    {
        $this->client = $client;
        $this->client->getEndpoint()->setCore('timkiemsuutra');
        $query = $this->client->createSelect();
        $query2 = $this->client->createSelect();
        $query->setQuery("*:*")->setQueryDefaultOperator('OR')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(0)->setRows(20);
        $query2->setQuery("ngan_chan: 3")->setQueryDefaultOperator('OR')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(0)->setRows(20);
        $resultset = $this->client->select($query);
        $resultset2 = $this->client->select($query2);
        $count_ngan_chan = $resultset2->getNumFound();
        $count_total = $resultset->getNumFound();
        $this->count_total = $count_total;
        $this->count_ngan_chan = $count_ngan_chan;
    }
    public function delete($id)
    {
        $update = $this->client->createUpdate();
        $update->addDeleteQuery('st_id:' . $id);
        $update->addCommit();
        $result = $this->client->update($update);
        return $result;
    }
    public function paginate($items, $perPage = 100, $page = null, $options = [], $total)
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);

        $options = array_merge($options, ['pageName' => 'page']);
        // dd($pageT->links());
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
        if (!$role) {
    // 👉 Chưa đăng nhập → quay về trang login
    return redirect()->route('login'); 
    // hoặc: return redirect('/login');
}
        // dd($role);
        if ($role) {
            $user_id =  $role->getAttributes();
            $user_id = $user_id['id'];
        } else {
           $this->fetchData_checkSolr($resultset);
        }
        $vpcc_id = NhanVienModel::where('nv_id', $user_id)->first()->nv_vanphong;
        $cn_ndd = ChiNhanhModel::where('cn_id', (int)$vpcc_id)->first()->code_cn;
        $code_cn = ChiNhanhModel::where('cn_id', (int)$vpcc_id)->first()->code_cn;
        $test_list = '';
        foreach ($resultset as $document) {
            $test_list .= $document->st_id . ', ';
            $id_string = rtrim($test_list, ', ');
            // $data = $document->getFields();
            //thu nghien worldbank
            
            if ($document->st_id == "14088900" && $code_cn == "HTH") {
                continue;// 
            }
            //
                   
            
            $data[] = [
                'duong_su' => $document->duong_su,
                'texte' => $document->texte,
                'duong_su_en' => $document->duong_su_en,
                'texte_en' => $document->texte_en,
                'ngay_nhap' =>  $document->ngay_nhap,
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
                'is_update' => $document->is_update,
                'note' => $document->note,
                'ma_phan_biet' => $document->ma_phan_biet,
                'release_file_name' => $document->release_file_name,
                'undisputed_date' => $document->undisputed_date,
                'undisputed_note' => $document->undisputed_note,
                'ccv' => $document->ccv,
            ];
        }
        //orderby ngay_nhap
        // dd($id_string);
        $data = collect($data);
        return $data;
    }
    public function fetchData_checkSolr($resultset)
    {
        $data = [];
        foreach ($resultset as $document) {
            $data[] = [
                'duong_su' => $document->duong_su,
                'texte' => $document->texte,
                'duong_su_en' => $document->duong_su_en,
                'texte_en' => $document->texte_en,
                'ngay_nhap' =>  $document->ngay_nhap,
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
                'is_update' => $document->is_update,
                'note' => $document->note,
                'ma_phan_biet' => $document->ma_phan_biet,
                'release_file_name' => $document->release_file_name,
                'undisputed_date' => $document->undisputed_date,
                'undisputed_note' => $document->undisputed_note,
                'ccv' => $document->ccv,
            ];
        }
        //orderby ngay_nhap
        // dd($id_string);
        $data = collect($data);
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
            $query->setQuery($string_query)->setQueryDefaultOperator('AND')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
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
            // $string_query =  ' {!complexphrase} duong_su:' . '(' . '"' . $search1 . '"' . ') AND duong_su:' . '"' . $search1  . ' *' . $val . '*' . '"' . '~1000';
            // $string_query=preg_replace('/\*+/', '*', $string_query);
            $page = $request->page ?? 1;
            $numberInPage = 20;
            $query->setQuery($string_query)->setQueryDefaultOperator('AND')->addSort('ngan_chan', $query::SORT_DESC)->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
            $resultset = $this->client->select($query);
            $count = $resultset->getNumFound();
            $data = $this->fetchData($resultset);
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
            $query->setQuery($string_query)->setQueryDefaultOperator('AND')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
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
            $query->setQuery($string_query)->setQueryDefaultOperator('AND')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
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
                    $sub_string_query =  ' AND merge_content:' . '(' . '"' . $search1 . '"' . '*' . $val . '*' . ')';
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
            $query->setQuery($string_query)->setQueryDefaultOperator('AND')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
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
                $string_query = 'duong_su_en:' . '(' . '"' . $search1 . '"' . ') OR texte_en:' . '(' . '"' . $search1 . '"' . ')  OR so_hd:' . '(' . '"' . $search1 . '"' . ')';
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
            $query->setQuery($string_query)->setQueryDefaultOperator('AND')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
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
                $string_query = 'texte_en:' . '(' . '"' . $texte . '"' . ') AND duong_su_en:' . '(' . '"' . $duong_su . '"' . ')';
            } else {
                $string_query = 'texte_en:' . '(' . '"' . $texte . '"' . ') AND duong_su_en:' . '(' . '"' . $duong_su . '"' . ')';
            }
            $search1 = $duong_su;
            $string_out[] = $texte;
            foreach ($search2 as $key => $val) {
                //append to query string
                if ($val != "" && $option == 1) {
                    $sub_string_query =  ' AND texte_en:' . '(' . '"' . $texte . '"' . '*' . $val . '*' . ') AND duong_su_en:' . '(' . '"' . $duong_su . '"' . '*' . $val . '*' . ')';
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
            $query->setQuery($string_query)->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
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
                $string_query = 'texte_en:' . '(' . '"' . $texte . '"' . ') AND duong_su:' . '(' . '"' . $duong_su . '"' . ')';
            } else {
                $string_query = 'texte:' . '(' . '"' . $texte . '"' . ') AND duong_su_en:' . '(' . '"' . $duong_su . '"' . ')';
            }
            foreach ($search2 as $key => $val) {
                //append to query string
                if ($val != "" && $option == 1) {
                    $sub_string_query =  ' AND texte_en:' . '(' . '"' . $texte . '"' . '*' . $val . '*' . ') AND duong_su:' . '(' . '"' . $duong_su . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } elseif ($val != "" && $option == 2) {
                    $sub_string_query =  ' AND texte:' . '(' . '"' . $texte . '"' . '*' . $val . '*' . ') AND duong_su_en:' . '(' . '"' . $duong_su . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } else {
                    continue;
                }
                $string_out[] = $val;
            }
            $page = $request->page ?? 1;
            $numberInPage = 20;
            $query->setQuery($string_query)->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);

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
                $string_query = 'duong_su:' . '(' . '"' . $duong_su1 . '"' . ') AND texte_en:' . '(' . $texte  . ')';
            } else {
                $string_query = 'duong_su_en:' . '(' . '"' . $duong_su1 . '"' . ') AND texte:' . '(' .  $texte  . ')';
            }
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
                $string_query = 'texte_en:' . '(' . '"' . $texte1 . '"' . ') AND duong_su_en:' . '(' . $duong_su  . ')';
            } else {
                $string_query = 'texte_en:' . '(' . '"' . $texte . '"' . ') AND duong_su_en:' . '(' .  $duong_su  . ')';
            }
            foreach ($search2 as $key => $val) {
                //append to query string
                if ($val !== ""  && $option == 1) {
                    $sub_string_query =  ' AND texte_en:' . '(' . '"' . $texte1 . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } elseif ($val !== ""  && $option == 2) {
                    $sub_string_query =  ' AND texte_en:' . '(' . '"' . $texte1 . '"' . '*' . $val . '*' . ')';
                    $string_query .= $sub_string_query;
                } else {
                    continue;
                }
                $string_out[] = $val;
            }
            $page = $request->page ?? 1;
            $numberInPage = 20;
            $query->setQuery($string_query)->setQueryDefaultOperator('AND')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
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
    public function XuLyTimKiem_so_hd($data, $option, $request)
    {
        // $role = Sentinel::check();
        // $user_id =  $role->getAttributes();
        // $user_id = $user_id['id'];
        // $vpcc_id = NhanVienModel::where('nv_id', $user_id)->first()->nv_vanphong;
        // $cn_ndd = ChiNhanhModel::where('cn_id', (int)$vpcc_id)->first()->code_cn;

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
                $string_out[] = $val;
            }
            // $string_query =  ' {!complexphrase} duong_su:' . '(' . '"' . $search1 . '"' . ') AND duong_su:' . '"' . $search1  . ' *' . $val . '*' . '"' . '~1000';
            // $string_query=preg_replace('/\*+/', '*', $string_query);
            $page = $request->page ?? 1;
            $numberInPage = 20;
            $query->setQuery($string_query)->setQueryDefaultOperator('AND')->addSort('st_id', $query::SORT_DESC)->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
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

    public function XuLyTimKiem_so_hd2($data, $option, $request)
    {
        // $role = Sentinel::check();
        // $user_id =  $role->getAttributes();
        // $user_id = $user_id['id'];
        // $vpcc_id = NhanVienModel::where('nv_id', $user_id)->first()->nv_vanphong;
        // $cn_ndd = ChiNhanhModel::where('cn_id', (int)$vpcc_id)->first()->code_cn;
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
            $string_out = [];
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
        //080923
        $role = Sentinel::check();
        if (!$role) {
    // 👉 Chưa đăng nhập → quay về trang login
    return redirect()->route('login'); 
    // hoặc: return redirect('/login');
}
        $data = $request->toArray();
        $data = AppController::convert_unicode($data);
        $request->replace($data->toArray());
        try {
            //Thông báo chung-----------------------------------------------
            if ($request->all() !== []) {
                if ($request->duong_su == null && $request->tai_san == null && $request->tat_ca == null) {
                    $Tbc = [];
                } else {
                    $solrTbc = new SolrThongBaoController($this->client);
                    $Tbc = $solrTbc->searchApi($request);
                }
            } else {
                $Tbc = [];
            }
            if ($Tbc && $Tbc['total'] == 0) {
                $Tbc = [];
            }
            //---------------------------------------------------------------
            $count_bs = $this->count_total;
            $type = $request->type ?? 'basic';
            $count_ngan_chan = $this->count_ngan_chan;
            $role = Sentinel::check();
            $Solr_Prevent = new SolariumController_nganchan($this->client);
            $ngan_chan = $Solr_Prevent->search($request);
            if ($ngan_chan['data']) {
                $count_ngan_chan_result = $ngan_chan['data']->toArray();
                $count_ngan_chan_result = $count_ngan_chan_result['total'];
            } else {
                $request->session()->flash('error', 'Mời đăng nhập lại');
                return view('admin.login');
            }

            if ($role) {
                $user_id =  $role->getAttributes();
                $user_id = $user_id['id'];
            } else {
                $request->session()->flash('error', 'Mời đăng nhập lại');
                return view('admin.login');
            }
            $vpcc_id = NhanVienModel::where('nv_id', $user_id)->first()->nv_vanphong;
            $cn_ndd = ChiNhanhModel::where('cn_id', (int)$vpcc_id)->first()->code_cn;
            $code_cn = ChiNhanhModel::where('cn_id', (int)$vpcc_id)->first()->code_cn;
            $query5 = $this->client->createSelect();
            $query5->setQuery('sync_code:' . '(' . $cn_ndd . ')')->setQueryDefaultOperator('OR')->addSort('ngan_chan', $query5::SORT_DESC)->addSort('st_id', $query5::SORT_DESC)->setStart(0)->setRows(20);
            $resultset5 = $this->client->select($query5);
            $count_office = $resultset5->getNumFound();
            $option = $request->all();
            if ($option == []) {
                // dd(1);
          $page = $request->page ?? 1;
$numberInPage = 20;

$query = $this->client->createSelect();
$query->setQuery('*:*')
      ->addSort('st_id', $query::SORT_DESC) // 🔥 sort duy nhất theo st_id (lớn → nhỏ)
      ->setStart(($page - 1) * $numberInPage)
      ->setRows($numberInPage);

$resultset = $this->client->select($query);

                $data = $this->fetchData($resultset);
                $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                $count = $resultset->getNumFound();
                $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                // dd($data);
                $string_out = [];
                return view('search', compact('data', 'Tbc', 'option', 'type', 'count', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out', 'count_ngan_chan_result'));
            }
            if ($request->duong_su == null && $request->tai_san == null && $request->tat_ca == null  && $request->so_hd == null) {
                $page = $request->page ?? 1;
                $numberInPage = 20;
                $query = $this->client->createSelect();
                $query2 = $this->client->createSelect();
                $query->setQuery("*:*")->setQueryDefaultOperator('OR')->addSort('st_id', $query::SORT_DESC)
                    ->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                $resultset = $this->client->select($query);
                $data = $this->fetchData($resultset);
                $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                $count = $resultset->getNumFound();
                $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                $string_out = [];
                return view('search', compact('data', 'Tbc', 'option', 'type', 'count', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out', 'count_ngan_chan_result'));
            }
            //Tìm 2 ô đương sự và tài sản
            if ($request->duong_su != null && $request->tai_san != null && $request->tat_ca == null  && $request->so_hd == null) {
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
                        return view('search', compact('data', 'Tbc', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out', 'count_ngan_chan_result'));
                    } elseif (strpos($duong_su, '"') !== false && strpos($tai_san, '"') === false) {
                        $data = $this->XuLyTimKiem_2_o_duong_su($tai_san, $duong_su, 1, $request);
                        $count = $data[1];
                        $data = $data[0];
                        return view('search', compact('data', 'Tbc', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out', 'count_ngan_chan_result'));
                    } elseif (strpos($duong_su, '"') === false && strpos($tai_san, '"') !== false) {
                        $data = $this->XuLyTimKiem_2_o_tai_san($tai_san, $duong_su, 1, $request);
                        $count = $data[1];
                        $data = $data[0];
                        return view('search', compact('data', 'Tbc', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out', 'count_ngan_chan_result'));
                    } else {
                        $query = $this->client->createSelect();
                        $query2 = $this->client->createSelect();
                        $query->setQuery('duong_su:' . '(' . $duong_su . ') AND texte:' . '(' . $tai_san . ')')->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                        $resultset = $this->client->select($query);
                        $data = $this->fetchData($resultset);
                        $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                        $count = $resultset->getNumFound();
                        $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                        return view('search', compact('data', 'Tbc', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out', 'count_ngan_chan_result'));
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
                        return view('search', compact('data', 'Tbc', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out', 'count_ngan_chan_result'));
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
                        return view('search', compact('data', 'Tbc', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out', 'count_ngan_chan_result'));
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
                        return view('search', compact('data', 'Tbc', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out', 'count_ngan_chan_result'));
                    } else {
                        $query = $this->client->createSelect();
                        $query2 = $this->client->createSelect();
                        $query->setQuery('duong_su:' . '(' . $duong_su . ') AND texte:' . '(' . $tai_san . ')')->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                        $resultset = $this->client->select($query);
                        $data = $this->fetchData($resultset);
                        $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                        $count = $resultset->getNumFound();
                        $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                        $string_out = explode(" ", $search);
                        $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                        return view('search', compact('data', 'Tbc', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out', 'count_ngan_chan_result'));
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
                        return view('search', compact('data', 'Tbc', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out', 'count_ngan_chan_result'));
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
                        return view('search', compact('data', 'Tbc', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out', 'count_ngan_chan_result'));
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
                        return view('search', compact('data', 'Tbc', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out', 'count_ngan_chan_result'));
                    } else {
                        $query = $this->client->createSelect();
                        $query2 = $this->client->createSelect();
                        $query->setQuery('duong_su:' . '(' . $duong_su . ') AND texte:' . '(' . $tai_san . ')')->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                        $resultset = $this->client->select($query);
                        $data = $this->fetchData($resultset);
                        $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                        $count = $resultset->getNumFound();
                        $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                        $string_out = explode(" ", $search);
                        $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                        return view('search', compact('data', 'Tbc', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out', 'count_ngan_chan_result'));
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
                        return view('search', compact('data', 'Tbc', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out', 'count_ngan_chan_result'));
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
                        return view('search', compact('data', 'Tbc', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out', 'count_ngan_chan_result'));
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
                        return view('search', compact('data', 'Tbc', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out', 'count_ngan_chan_result'));
                    } else {
                        $query = $this->client->createSelect();
                        $query2 = $this->client->createSelect();
                        $query->setQuery('duong_su_en:' . '(' . $duong_su . ') AND texte_en:' . '(' . $tai_san . ')')->setQueryDefaultOperator('AND')->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                        $resultset = $this->client->select($query);
                        $data = $this->fetchData($resultset);
                        $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                        $count = $resultset->getNumFound();
                        $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                        $string_out = explode(" ", $search . ' ' . $tai_san);
                        $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                        return view('search', compact('data', 'Tbc', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out', 'count_ngan_chan_result'));
                    }
                }
            }
            //Tìm theo đương sự
            if ($request->duong_su != null) {
                $search = $request->duong_su;
                // dd($option);
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
                        return view('search', compact('data', 'Tbc', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out', 'count_ngan_chan_result'));
                    } else {
                        $page = $request->page ?? 1;
                        $numberInPage = 20;
                        $query = $this->client->createSelect();
                        $query2 = $this->client->createSelect();
                        $query->setQuery('duong_su:' . '(' . $search . ')')->setQueryDefaultOperator('OR')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                        $resultset = $this->client->select($query);
                        $data = $this->fetchData($resultset);
                        $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                        $count = $resultset->getNumFound();
                        $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                        $string_out = explode(" ", $search);
                        $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                        return view('search', compact('data', 'Tbc', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out', 'count_ngan_chan_result'));
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
                        return view('search', compact('data', 'Tbc', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out', 'count_ngan_chan_result'));
                    } else {
                        $page = $request->page ?? 1;
                        $numberInPage = 20;
                        $query = $this->client->createSelect();
                        $query->setQuery('duong_su_en:' . $search);
                        $query2 = $this->client->createSelect();
                        $query->setQuery('duong_su_en:' . '(' . $search . ')')->setQueryDefaultOperator('AND')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                        $resultset = $this->client->select($query);
                        $data = $this->fetchData($resultset);
                        $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                        $count = $resultset->getNumFound();
                        $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                        $string_out = explode(" ", $search);
                        $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                        return view('search', compact('data', 'Tbc', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out', 'count_ngan_chan_result'));
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
                        return view('search', compact('data', 'Tbc', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out', 'count_ngan_chan_result'));
                    } else {
                        $page = $request->page ?? 1;
                        $numberInPage = 20;
                        $query = $this->client->createSelect();
                        $query2 = $this->client->createSelect();
                        $query->setQuery('texte:' . '(' . $search . ')')->setQueryDefaultOperator('AND')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                        $resultset = $this->client->select($query);
                        $data = $this->fetchData($resultset);
                        $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                        $count = $resultset->getNumFound();
                        $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                        $string_out = explode(" ", $search);
                        $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                        return view('search', compact('data', 'Tbc', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out', 'count_ngan_chan_result'));
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
                        return view('search', compact('data', 'Tbc', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out', 'count_ngan_chan_result'));
                    } else {
                        $page = $request->page ?? 1;
                        $numberInPage = 20;
                        $query = $this->client->createSelect();
                        $query2 = $this->client->createSelect();
                        $query->setQuery('texte_en:' . '(' . $search . ')')->setQueryDefaultOperator('AND')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                        $resultset = $this->client->select($query);
                        $data = $this->fetchData($resultset);
                        $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                        $count = $resultset->getNumFound();
                        $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                        $string_out = explode(" ", $search);
                        $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                        return view('search', compact('data', 'Tbc', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out', 'count_ngan_chan_result'));
                    }
                }
            }

            //Tim theo So_hd
            elseif ($request->so_hd != null) {
                $search = $request->so_hd;
                if (preg_match('/"/', $search)) {
                    $search = $request->so_hd;
                } else {
                    $search = '"' . $search . '"';
                }

                //check is request vietnamese string ?
                if (preg_match('/[\x80-\xFF]/', $search)) {
                    //Chứa dấu " "
                    if (strpos($search, '"') !== false) {
                        $data = $this->XuLyTimKiem_so_hd($search, 1, $request);
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
                        return view('search', compact('data', 'Tbc', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out', 'count_ngan_chan_result'));
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
                        $string_out = explode(" ", $search);
                        $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                        return view('search', compact('data', 'Tbc', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out', 'count_ngan_chan_result'));
                    }
                }
                //Tìm không dấu
                else {
                    //Chứa dấu " "
                    if (strpos($search, '"') !== false) {
                        $data = $this->XuLyTimKiem_so_hd($search, 2, $request);
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
                        return view('search', compact('data', 'Tbc', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out', 'count_ngan_chan_result'));
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
                        $string_out = explode(" ", $search);
                        $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                        return view('search', compact('data', 'Tbc', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out', 'count_ngan_chan_result'));
                    }
                }
            }
            //Tìm tất cả
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
                        return view('search', compact('data', 'Tbc', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out', 'count_ngan_chan_result'));
                    } else {
                        $page = $request->page ?? 1;
                        $numberInPage = 20;
                        $query = $this->client->createSelect();
                        $query->setQuery('texte:' . '(' . $search . ') duong_su:' . '(' . $search . ')')->setQueryDefaultOperator('AND')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                        $query2 = $this->client->createSelect();
                        $query->setQueryDefaultOperator('OR')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                        $resultset = $this->client->select($query);
                        $data = $this->fetchData($resultset);
                        $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                        $count = $resultset->getNumFound();
                        $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                        $string_out = explode(" ", $search);
                        $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                        return view('search', compact('data', 'Tbc', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out', 'count_ngan_chan_result'));
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
                        return view('search', compact('data', 'Tbc', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out', 'count_ngan_chan_result'));
                    } else {
                        
                        $page = $request->page ?? 1;
                        $numberInPage = 20;
                        $query = $this->client->createSelect();
                        $query->setQuery('merge_content:' . '(' . $search . ') ')->setQueryDefaultOperator('AND')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                        $query2 = $this->client->createSelect();
                        $resultset = $this->client->select($query);
                        $data = $this->fetchData($resultset);
                        $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                        $count = $resultset->getNumFound();
                        $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                        $string_out = explode(" ", $search);
                        $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                        return view('search', compact('data', 'Tbc', 'option', 'type', 'count', 'search', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office', 'string_out', 'count_ngan_chan_result'));
                    }
                }
            }
        } catch (\Solarium\Exception\HttpException $e) {
            $page = $request->page ?? 1;
            $numberInPage = 20;
            $query = $this->client->createSelect();
            $query2 = $this->client->createSelect();
            $query->setQuery("*:*")->setQueryDefaultOperator('OR')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)
                ->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
            $resultset = $this->client->select($query);
            $data = $this->fetchData($resultset);
            $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
            $count = $resultset->getNumFound();
            $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
            return view('search', compact('data', 'Tbc', 'option', 'type', 'count', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office'));
        } catch (\Exception $e) {
            $page = $request->page ?? 1;
            $numberInPage = 20;
            $query = $this->client->createSelect();
            $query2 = $this->client->createSelect();
            $query->setQuery("*:*")->setQueryDefaultOperator('OR')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)
                ->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
            $resultset = $this->client->select($query);
            $data = $this->fetchData($resultset);
            $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
            $count = $resultset->getNumFound();
            $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
            return view('search', compact('data', 'Tbc', 'option', 'type', 'count', 'code_cn', 'count_bs', 'count_ngan_chan', 'count_office'));
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
        $Solr_Basic_Print = new Solr_Basic_Print($this->client);
        //filter item not null
        $search = array_filter($search);
        $search = implode(' ', $search);
        $search = str_replace('"', '', $search);
        $data =   $data = $Solr_Basic_Print->search($request);
        $item = $data['data']->toArray();
        $count = count($item) ?? 0;
        //save pdf view to file utf-8
        // $pdf = PDF::loadView('admin.suutra.printSolr', compact('item', 'count', 'search'))->setPaper('a4', 'landscape');
        // $pdf->setOptions(['dpi' => 200]);
        // //random code and number
        // $code = Str::random(5);
        // $number = rand(1, 999);
        // $rdCode = $code . $number;
        // $nameFile =  'LichSuTraCuu_' . date('d_m_Y_H_i_s') . '_' . $rdCode . '.pdf';
        // $pdf->save(public_path('lichsu_tracuu/' . $nameFile));
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
    public function saveHistoryPdf(Request $request)
    {
        $role = Sentinel::check();
        $user_id =  $role->getAttributes();
        $user_id = $user_id['id'];
        $vpcc_id = NhanVienModel::where('nv_id', $user_id)->first()->nv_vanphong;
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

        $history = HistorySearchModel::create([
            'user_id' => $user_id,
            'url' => $request->ipaddress ?? '',
            'client_ip' => $ipaddress,
            'vp_id' => $vpcc_id,
        ]);
        $dompdf = new Dompdf();
        $dompdf->loadHtml($request->html);
        $dompdf->setPaper('A4', 'portrait');
        $options = $dompdf->getOptions();
        $options->setDpi(200);
        $dompdf->setOptions($options);
        $dompdf->render();
        $output = $dompdf->output();
        $path = public_path('lichsu_tracuu/');
        $code = Str::random(5);
        $number = rand(1, 999);
        $rdCode = $code . $number;
        $nameFile =  '/LichSuTraCuu_' . date('d_m_Y_H_i_s') . '_' . $rdCode . '.pdf';
        file_put_contents($path . $nameFile, $output);
        $history->file = $nameFile;
        $history->save();

        return response()->json(['success' => 'done']);
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
    public function delete_solr($st_id)
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
    // public function merge_content()
    // {
    //     set_time_limit(0);
    //     //remember to set up the beginning of loop (in txt file) the number which u want to start  at the first time it loaded.
    //     $end_of_loop = (int)file_get_contents("C:/xampp/htdocs/aemSql/public/solr_error/end_of_solr.txt");
    //     $query = $this->client->createSelect();
    //     $data = SuuTraModel::where('st_id', '>=', $end_of_loop)->whereNull('deleted_at')->orderBy('st_id', 'asc')->limit(10)->get();
    //     foreach ($data as $item) {
    //         try {
    //             $a = $item;
    //             $query->setQuery('st_id: (' . $item->st_id . ')')->setQueryDefaultOperator('AND')->setStart(0)->setRows(100);
    //             $resultset = $this->client->select($query);
    //             $data_solr = $this->fetchData($resultset);
    //             $data_solr = $data_solr->toArray();
    //             if (!$data_solr) {
    //                 $this->insert_solr($a);
    //                 $file = fopen("C:/xampp/htdocs/aemSql/public/solr_error/end_of_solr.txt", "w") or die("Unable to open file!");
    //                 //write json to file
    //                 $id = $item->getAttributes();
    //                 fwrite($file,  $id . "\n");
    //                 //close file
    //                 fclose($file);
    //             } else {
    //                 continue;
    //             }
    //         } catch (\Solarium\Exception\HttpException $e) {
    //             //open file text
    //             $file = fopen("C:/xampp/htdocs/aemSql/public/solr_error/error.txt", "a") or die("Unable to open file!");
    //             //write json to file
    //             $id = $item->getAttributes();
    //             fwrite($file, "*" . $id['st_id'] . "\n");
    //             //close file
    //             fclose($file);
    //             $file = fopen("C:/xampp/htdocs/aemSql/public/solr_error/end_of_solr.txt", "w") or die("Unable to open file!");
    //             //write json to file
    //             $id = $item->getAttributes();
    //             fwrite($file,  $id['st_id'] . "\n");
    //             //close file
    //             fclose($file);
    //             continue;
    //         }
    //     }
    //     // //open file text
    //     $file = fopen("C:/xampp/htdocs/aemSql/public/solr_error/end_of_solr.txt", "w") or die("Unable to open file!");
    //     //write json to file
    //     $id = $item->getAttributes();
    //     fwrite($file,  $id['st_id'] . "\n");
    //     //close file
    //     fclose($file);
    //     return view('admin.suutra.reload');
    // }
    public function check_solr()
    {
        set_time_limit(0);
        try {
            $query = $this->client->createSelect();
            $data = SolrCheckModel::orderBy('st_id', 'desc')->limit(200)->get();
            foreach ($data as $item) {
                $query->setQuery('st_id: (' . $item->st_id . ')')->setQueryDefaultOperator('AND')->setStart(0)->setRows(100);
                $resultset = $this->client->select($query);
                $data_solr = $this->fetchData_checkSolr($resultset);
                $data_solr = $data_solr->toArray();

                if (!$data_solr) {
                    $this->insert_solr(SuutraModel::where('st_id', $item->st_id)->first());
                    $delete = SolrCheckModel::where('st_id', '=', $item->st_id)->first();
                    $delete->delete();
                } else {
                    if (count($data_solr) > 1) {
                        // $this->sendMessageToTelegram('[⚠️]_[STP2(240)]_[SuuTra]_Phat hien Hồ sơ bị trùng: ' . $item->so_hd . '(' . $item->st_id . ')');
                        $this->delete_solr($item->st_id);
                        $this->insert_solr(SuutraModel::where('st_id', $item->st_id)->first());
                    }
                    $delete = SolrCheckModel::where('st_id', '=', $item->st_id)->first();
                    $delete->delete();
                    continue;
                }
            }
        } catch (\Solarium\Exception\HttpException $e) {
            $create = SolrCheckModel::where('st_id', '=', $item->st_id)->first();
            $create->status = 1;
            $create->save();
        }
        return 'Cap nhat thanh cong!';
        // return view('admin.suutra.reload');

    }
    public function deleteSolr($id)
    {
        try {
            $this->delete_solr($id);
            $data = SuuTraModel::where('st_id', '=', $id)->first();
            if ($data) {
                $data->deleted_at = Carbon::now();
                $data->save();
            }
            return redirect(route('searchSolr'))->with('success', 'Xóa thành công !');
        } catch (\Solarium\Exception\HttpException $e) {
            return redirect(route('searchSolr'))->with('error', $e);
        }
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
    public function success($data, $message)
    {
        return response()->json([
            'status' => 200,
            'message' => $message,
            'data' => $data
        ]);
    }
    public function error($message)
    {
        return response()->json([
            'status' => 500,
            'message' => $message,
            'data' => ''
        ]);
    }
    public function searchApi(Request $request)
    {
        $data = $request->toArray();
        $data = AppController::convert_unicode($data);
        $request->replace($data->toArray());
        try {
            //Thông báo chung-----------------------------------------------
            if ($request->all() !== []) {
                if ($request->duong_su == null && $request->tai_san == null && $request->tat_ca == null) {
                    $Tbc = [];
                } else {
                    $solrTbc = new SolrThongBaoController($this->client);
                    $Tbc = $solrTbc->searchApi($request);
                }
            } else {
                $Tbc = [];
            }
            if ($Tbc && $Tbc['total'] == 0) {
                $Tbc = [];
            }
            //---------------------------------------------------------------
            $count_bs = $this->count_total;
            $type = $request->type ?? 'basic';
            $count_ngan_chan = $this->count_ngan_chan;
            $Solr_Prevent = new SolariumController_nganchan($this->client);
            $count_ngan_chan_result = 0;
            $count_ngan_chan_result = 0;


            $option = $request->all();
            if ($option == []) {
                $page = $request->page ?? 1;
                $numberInPage = 20;
                $query = $this->client->createSelect();
                $query2 = $this->client->createSelect();
                $query->setQuery("*:*")->setQueryDefaultOperator('OR')->addSort('st_id', $query::SORT_DESC)->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                $resultset = $this->client->select($query);
                $data = $this->fetchData_checkSolr($resultset);
                $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                $count = $resultset->getNumFound();
                $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                $string_out = [];
                $reponse = [
                    'data' => $data,
                    'Tbc' => $Tbc,
                    'option' => $option,
                    'type' => $type,
                    'count' => $count,
                    'count_bs' => $count_bs,
                    'count_ngan_chan' => $count_ngan_chan,
                    'string_out' => $string_out,
                    'count_ngan_chan_result' => $count_ngan_chan_result
                ];
                return $this->success($reponse, 'Thành công');
            }
            if ($request->duong_su == null && $request->tai_san == null && $request->tat_ca == null  && $request->so_hd == null) {
                $page = $request->page ?? 1;
                $numberInPage = 20;
                $query = $this->client->createSelect();
                $query2 = $this->client->createSelect();
                $query->setQuery("*:*")->setQueryDefaultOperator('OR')->addSort('st_id', $query::SORT_DESC)
                    ->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                $resultset = $this->client->select($query);
                $data = $this->fetchData_checkSolr($resultset);
                $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                $count = $resultset->getNumFound();
                $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                $string_out = [];
                $reponse = [
                    'data' => $data,
                    'Tbc' => $Tbc,
                    'option' => $option,
                    'type' => $type,
                    'count' => $count,
                    'count_bs' => $count_bs,
                    'count_ngan_chan' => $count_ngan_chan,
                    'string_out' => $string_out,
                    'count_ngan_chan_result' => $count_ngan_chan_result
                ];
                return $this->success($reponse, 'Thành công');
            }
            //Tìm 2 ô đương sự và tài sản
            if ($request->duong_su != null && $request->tai_san != null && $request->tat_ca == null  && $request->so_hd == null) {
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
                        $reponse = [
                            'data' => $data,
                            'Tbc' => $Tbc,
                            'option' => $option,
                            'type' => $type,
                            'count' => $count,
                            'count_bs' => $count_bs,
                            'count_ngan_chan' => $count_ngan_chan,
                            'count_ngan_chan_result' => $count_ngan_chan_result
                        ];
                        return $this->success($reponse, 'Thành công');
                    } elseif (strpos($duong_su, '"') !== false && strpos($tai_san, '"') === false) {
                        $data = $this->XuLyTimKiem_2_o_duong_su($tai_san, $duong_su, 1, $request);
                        $count = $data[1];
                        $data = $data[0];
                        $reponse = [
                            'data' => $data,
                            'Tbc' => $Tbc,
                            'option' => $option,
                            'type' => $type,
                            'count' => $count,
                            'count_bs' => $count_bs,
                            'count_ngan_chan' => $count_ngan_chan,
                            'count_ngan_chan_result' => $count_ngan_chan_result
                        ];
                        return $this->success($reponse, 'Thành công');
                    } elseif (strpos($duong_su, '"') === false && strpos($tai_san, '"') !== false) {
                        $data = $this->XuLyTimKiem_2_o_tai_san($tai_san, $duong_su, 1, $request);
                        $count = $data[1];
                        $data = $data[0];
                        $reponse = [
                            'data' => $data,
                            'Tbc' => $Tbc,
                            'option' => $option,
                            'type' => $type,
                            'count' => $count,
                            'count_bs' => $count_bs,
                            'count_ngan_chan' => $count_ngan_chan,
                            'count_ngan_chan_result' => $count_ngan_chan_result
                        ];
                        return $this->success($reponse, 'Thành công');
                    } else {
                        $query = $this->client->createSelect();
                        $query2 = $this->client->createSelect();
                        $query->setQuery('duong_su:' . '(' . $duong_su . ') AND texte:' . '(' . $tai_san . ')')->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                        $resultset = $this->client->select($query);
                        $data = $this->fetchData_checkSolr($resultset);
                        $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                        $count = $resultset->getNumFound();
                        $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                        $reponse = [
                            'data' => $data,
                            'Tbc' => $Tbc,
                            'option' => $option,
                            'type' => $type,
                            'count' => $count,
                            'count_bs' => $count_bs,
                            'count_ngan_chan' => $count_ngan_chan,
                            'count_ngan_chan_result' => $count_ngan_chan_result
                        ];
                        return $this->success($reponse, 'Thành công');
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
                        $reponse = [
                            'data' => $data,
                            'Tbc' => $Tbc,
                            'option' => $option,
                            'type' => $type,
                            'count' => $count,
                            'count_bs' => $count_bs,
                            'count_ngan_chan' => $count_ngan_chan,
                            'count_ngan_chan_result' => $count_ngan_chan_result
                        ];
                        return $this->success($reponse, 'Thành công');
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
                        $reponse = [
                            'data' => $data,
                            'Tbc' => $Tbc,
                            'option' => $option,
                            'type' => $type,
                            'count' => $count,
                            'count_bs' => $count_bs,
                            'count_ngan_chan' => $count_ngan_chan,
                            'count_ngan_chan_result' => $count_ngan_chan_result
                        ];
                        return $this->success($reponse, 'Thành công');
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
                        $reponse = [
                            'data' => $data,
                            'Tbc' => $Tbc,
                            'option' => $option,
                            'type' => $type,
                            'count' => $count,
                            'count_bs' => $count_bs,
                            'count_ngan_chan' => $count_ngan_chan,
                            'count_ngan_chan_result' => $count_ngan_chan_result
                        ];
                        return $this->success($reponse, 'Thành công');
                    } else {
                        $query = $this->client->createSelect();
                        $query2 = $this->client->createSelect();
                        $query->setQuery('duong_su:' . '(' . $duong_su . ') AND texte:' . '(' . $tai_san . ')')->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                        $resultset = $this->client->select($query);
                        $data = $this->fetchData_checkSolr($resultset);
                        $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                        $count = $resultset->getNumFound();
                        $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                        $string_out = explode(" ", $search);
                        $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                        $reponse = [
                            'data' => $data,
                            'Tbc' => $Tbc,
                            'option' => $option,
                            'type' => $type,
                            'count' => $count,
                            'count_bs' => $count_bs,
                            'count_ngan_chan' => $count_ngan_chan,
                            'count_ngan_chan_result' => $count_ngan_chan_result
                        ];
                        return $this->success($reponse, 'Thành công');
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
                        $reponse = [
                            'data' => $data,
                            'Tbc' => $Tbc,
                            'option' => $option,
                            'type' => $type,
                            'count' => $count,
                            'count_bs' => $count_bs,
                            'count_ngan_chan' => $count_ngan_chan,
                            'count_ngan_chan_result' => $count_ngan_chan_result
                        ];
                        return $this->success($reponse, 'Thành công');
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
                        $reponse = [
                            'data' => $data,
                            'Tbc' => $Tbc,
                            'option' => $option,
                            'type' => $type,
                            'count' => $count,
                            'count_bs' => $count_bs,
                            'count_ngan_chan' => $count_ngan_chan,
                            'count_ngan_chan_result' => $count_ngan_chan_result
                        ];
                        return $this->success($reponse, 'Thành công');
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
                        $reponse = [
                            'data' => $data,
                            'Tbc' => $Tbc,
                            'option' => $option,
                            'type' => $type,
                            'count' => $count,
                            'count_bs' => $count_bs,
                            'count_ngan_chan' => $count_ngan_chan,
                            'count_ngan_chan_result' => $count_ngan_chan_result
                        ];
                        return $this->success($reponse, 'Thành công');
                    } else {
                        $query = $this->client->createSelect();
                        $query2 = $this->client->createSelect();
                        $query->setQuery('duong_su:' . '(' . $duong_su . ') AND texte:' . '(' . $tai_san . ')')->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                        $resultset = $this->client->select($query);
                        $data = $this->fetchData_checkSolr($resultset);
                        $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                        $count = $resultset->getNumFound();
                        $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                        $string_out = explode(" ", $search);
                        $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                        $reponse = [
                            'data' => $data,
                            'Tbc' => $Tbc,
                            'option' => $option,
                            'type' => $type,
                            'count' => $count,
                            'count_bs' => $count_bs,
                            'count_ngan_chan' => $count_ngan_chan,
                            'count_ngan_chan_result' => $count_ngan_chan_result
                        ];
                        return $this->success($reponse, 'Thành công');
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
                        $reponse = [
                            'data' => $data,
                            'Tbc' => $Tbc,
                            'option' => $option,
                            'type' => $type,
                            'count' => $count,
                            'count_bs' => $count_bs,
                            'count_ngan_chan' => $count_ngan_chan,
                            'count_ngan_chan_result' => $count_ngan_chan_result
                        ];
                        return $this->success($reponse, 'Thành công');
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
                        $reponse = [
                            'data' => $data,
                            'Tbc' => $Tbc,
                            'option' => $option,
                            'type' => $type,
                            'count' => $count,
                            'count_bs' => $count_bs,
                            'count_ngan_chan' => $count_ngan_chan,
                            'count_ngan_chan_result' => $count_ngan_chan_result
                        ];
                        return $this->success($reponse, 'Thành công');
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
                        $reponse = [
                            'data' => $data,
                            'Tbc' => $Tbc,
                            'option' => $option,
                            'type' => $type,
                            'count' => $count,
                            'count_bs' => $count_bs,
                            'count_ngan_chan' => $count_ngan_chan,
                            'count_ngan_chan_result' => $count_ngan_chan_result
                        ];
                        return $this->success($reponse, 'Thành công');
                    } else {
                        $query = $this->client->createSelect();
                        $query2 = $this->client->createSelect();
                        $query->setQuery('duong_su_en:' . '(' . $duong_su . ') AND texte_en:' . '(' . $tai_san . ')')->setQueryDefaultOperator('AND')->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                        $resultset = $this->client->select($query);
                        $data = $this->fetchData_checkSolr($resultset);
                        $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                        $count = $resultset->getNumFound();
                        $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                        $string_out = explode(" ", $search . ' ' . $tai_san);
                        $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                        $reponse = [
                            'data' => $data,
                            'Tbc' => $Tbc,
                            'option' => $option,
                            'type' => $type,
                            'count' => $count,
                            'count_bs' => $count_bs,
                            'count_ngan_chan' => $count_ngan_chan,
                            'count_ngan_chan_result' => $count_ngan_chan_result
                        ];
                        return $this->success($reponse, 'Thành công');
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
                        $reponse = [
                            'data' => $data,
                            'Tbc' => $Tbc,
                            'option' => $option,
                            'type' => $type,
                            'count' => $count,
                            'count_bs' => $count_bs,
                            'count_ngan_chan' => $count_ngan_chan,
                            'count_ngan_chan_result' => $count_ngan_chan_result
                        ];
                        return $this->success($reponse, 'Thành công');
                    } else {
                        $page = $request->page ?? 1;
                        $numberInPage = 20;
                        $query = $this->client->createSelect();
                        $query2 = $this->client->createSelect();
                        $query->setQuery('duong_su:' . '(' . $search . ')')->setQueryDefaultOperator('OR')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                        $resultset = $this->client->select($query);
                        $data = $this->fetchData_checkSolr($resultset);
                        $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                        $count = $resultset->getNumFound();
                        $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                        $string_out = explode(" ", $search);
                        $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                        $reponse = [
                            'data' => $data,
                            'Tbc' => $Tbc,
                            'option' => $option,
                            'type' => $type,
                            'count' => $count,
                            'count_bs' => $count_bs,
                            'count_ngan_chan' => $count_ngan_chan,
                            'count_ngan_chan_result' => $count_ngan_chan_result
                        ];
                        return $this->success($reponse, 'Thành công');
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
                        $reponse = [
                            'data' => $data,
                            'Tbc' => $Tbc,
                            'option' => $option,
                            'type' => $type,
                            'count' => $count,
                            'count_bs' => $count_bs,
                            'count_ngan_chan' => $count_ngan_chan,
                            'count_ngan_chan_result' => $count_ngan_chan_result
                        ];
                        return $this->success($reponse, 'Thành công');
                    } else {
                        $page = $request->page ?? 1;
                        $numberInPage = 20;
                        $query = $this->client->createSelect();
                        $query->setQuery('duong_su_en:' . $search);
                        $query2 = $this->client->createSelect();
                        $query->setQuery('duong_su_en:' . '(' . $search . ')')->setQueryDefaultOperator('AND')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                        $resultset = $this->client->select($query);
                        $data = $this->fetchData_checkSolr($resultset);
                        $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                        $count = $resultset->getNumFound();
                        $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                        $string_out = explode(" ", $search);
                        $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                        $reponse = [
                            'data' => $data,
                            'Tbc' => $Tbc,
                            'option' => $option,
                            'type' => $type,
                            'count' => $count,
                            'count_bs' => $count_bs,
                            'count_ngan_chan' => $count_ngan_chan,
                            'count_ngan_chan_result' => $count_ngan_chan_result
                        ];
                        return $this->success($reponse, 'Thành công');
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
                        $reponse = [
                            'data' => $data,
                            'Tbc' => $Tbc,
                            'option' => $option,
                            'type' => $type,
                            'count' => $count,
                            'count_bs' => $count_bs,
                            'count_ngan_chan' => $count_ngan_chan,
                            'count_ngan_chan_result' => $count_ngan_chan_result
                        ];
                        return $this->success($reponse, 'Thành công');
                    } else {
                        $page = $request->page ?? 1;
                        $numberInPage = 20;
                        $query = $this->client->createSelect();
                        $query2 = $this->client->createSelect();
                        $query->setQuery('texte:' . '(' . $search . ')')->setQueryDefaultOperator('AND')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                        $resultset = $this->client->select($query);
                        $data = $this->fetchData_checkSolr($resultset);
                        $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                        $count = $resultset->getNumFound();
                        $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                        $string_out = explode(" ", $search);
                        $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                        $reponse = [
                            'data' => $data,
                            'Tbc' => $Tbc,
                            'option' => $option,
                            'type' => $type,
                            'count' => $count,
                            'count_bs' => $count_bs,
                            'count_ngan_chan' => $count_ngan_chan,
                            'count_ngan_chan_result' => $count_ngan_chan_result
                        ];
                        return $this->success($reponse, 'Thành công');
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
                        $reponse = [
                            'data' => $data,
                            'Tbc' => $Tbc,
                            'option' => $option,
                            'type' => $type,
                            'count' => $count,
                            'count_bs' => $count_bs,
                            'count_ngan_chan' => $count_ngan_chan,
                            'count_ngan_chan_result' => $count_ngan_chan_result
                        ];
                        return $this->success($reponse, 'Thành công');
                    } else {
                        $page = $request->page ?? 1;
                        $numberInPage = 20;
                        $query = $this->client->createSelect();
                        $query2 = $this->client->createSelect();
                        $query->setQuery('texte_en:' . '(' . $search . ')')->setQueryDefaultOperator('AND')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                        $resultset = $this->client->select($query);
                        $data = $this->fetchData_checkSolr($resultset);
                        $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                        $count = $resultset->getNumFound();
                        $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                        $string_out = explode(" ", $search);
                        $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                        $reponse = [
                            'data' => $data,
                            'Tbc' => $Tbc,
                            'option' => $option,
                            'type' => $type,
                            'count' => $count,
                            'count_bs' => $count_bs,
                            'count_ngan_chan' => $count_ngan_chan,
                            'count_ngan_chan_result' => $count_ngan_chan_result
                        ];
                        return $this->success($reponse, 'Thành công');
                    }
                }
            }

            //Tim theo So_hd
            elseif ($request->so_hd != null) {
                $search = $request->so_hd;
                if (preg_match('/"/', $search)) {
                    $search = $request->so_hd;
                } else {
                    $search = '"' . $search . '"';
                }

                //check is request vietnamese string ?
                if (preg_match('/[\x80-\xFF]/', $search)) {
                    //Chứa dấu " "
                    if (strpos($search, '"') !== false) {
                        $data = $this->XuLyTimKiem_so_hd($search, 1, $request);
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
                        $reponse = [
                            'data' => $data,
                            'Tbc' => $Tbc,
                            'option' => $option,
                            'type' => $type,
                            'count' => $count,
                            'count_bs' => $count_bs,
                            'count_ngan_chan' => $count_ngan_chan,
                            'count_ngan_chan_result' => $count_ngan_chan_result
                        ];
                        return $this->success($reponse, 'Thành công');
                    } else {
                        $page = $request->page ?? 1;
                        $numberInPage = 20;
                        $query = $this->client->createSelect();
                        $query2 = $this->client->createSelect();
                        $query->setQuery('so_hd:' . '(' . $search . ')')->addSort('st_id', $query::SORT_DESC)->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                        $resultset = $this->client->select($query);
                        $data = $this->fetchData_checkSolr($resultset);
                        $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                        $count = $resultset->getNumFound();
                        $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                        $string_out = explode(" ", $search);
                        $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                        $reponse = [
                            'data' => $data,
                            'Tbc' => $Tbc,
                            'option' => $option,
                            'type' => $type,
                            'count' => $count,
                            'count_bs' => $count_bs,
                            'count_ngan_chan' => $count_ngan_chan,
                            'count_ngan_chan_result' => $count_ngan_chan_result
                        ];
                        return $this->success($reponse, 'Thành công');
                    }
                }
                //Tìm không dấu
                else {
                    //Chứa dấu " "
                    if (strpos($search, '"') !== false) {
                        $data = $this->XuLyTimKiem_so_hd($search, 2, $request);
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
                        $reponse = [
                            'data' => $data,
                            'Tbc' => $Tbc,
                            'option' => $option,
                            'type' => $type,
                            'count' => $count,
                            'count_bs' => $count_bs,
                            'count_ngan_chan' => $count_ngan_chan,
                            'count_ngan_chan_result' => $count_ngan_chan_result
                        ];
                        return $this->success($reponse, 'Thành công');
                    } else {
                        $page = $request->page ?? 1;
                        $numberInPage = 20;
                        $query = $this->client->createSelect();
                        $query2 = $this->client->createSelect();
                        $query->setQuery('so_hd:' . '(' . $search . ')')->addSort('st_id', $query::SORT_DESC)->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                        $resultset = $this->client->select($query);
                        $data = $this->fetchData_checkSolr($resultset);
                        $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                        $count = $resultset->getNumFound();
                        $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                        $string_out = explode(" ", $search);
                        $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                        $reponse = [
                            'data' => $data,
                            'Tbc' => $Tbc,
                            'option' => $option,
                            'type' => $type,
                            'count' => $count,
                            'count_bs' => $count_bs,
                            'count_ngan_chan' => $count_ngan_chan,
                            'count_ngan_chan_result' => $count_ngan_chan_result
                        ];
                        return $this->success($reponse, 'Thành công');
                    }
                }
            }
            //Tìm tất cả
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
                        $reponse = [
                            'data' => $data,
                            'Tbc' => $Tbc,
                            'option' => $option,
                            'type' => $type,
                            'count' => $count,
                            'count_bs' => $count_bs,
                            'count_ngan_chan' => $count_ngan_chan,
                            'count_ngan_chan_result' => $count_ngan_chan_result
                        ];
                        return $this->success($reponse, 'Thành công');
                    } else {
                        $page = $request->page ?? 1;
                        $numberInPage = 20;
                        $query = $this->client->createSelect();
                        $query->setQuery('texte:' . '(' . $search . ') duong_su:' . '(' . $search . ')')->setQueryDefaultOperator('AND')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                        $query2 = $this->client->createSelect();
                        $query->setQueryDefaultOperator('OR')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                        $resultset = $this->client->select($query);
                        $data = $this->fetchData_checkSolr($resultset);
                        $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                        $count = $resultset->getNumFound();
                        $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                        $string_out = explode(" ", $search);
                        $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                        $reponse = [
                            'data' => $data,
                            'Tbc' => $Tbc,
                            'option' => $option,
                            'type' => $type,
                            'count' => $count,
                            'count_bs' => $count_bs,
                            'count_ngan_chan' => $count_ngan_chan,
                            'count_ngan_chan_result' => $count_ngan_chan_result
                        ];
                        return $this->success($reponse, 'Thành công');
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
                        $reponse = [
                            'data' => $data,
                            'Tbc' => $Tbc,
                            'option' => $option,
                            'type' => $type,
                            'count' => $count,
                            'count_bs' => $count_bs,
                            'count_ngan_chan' => $count_ngan_chan,
                            'count_ngan_chan_result' => $count_ngan_chan_result
                        ];
                        return $this->success($reponse, 'Thành công');
                    } else {
                        $page = $request->page ?? 1;
                        $numberInPage = 20;
                        $query = $this->client->createSelect();
                        $query->setQuery('merge_content:' . '(' . $search . ') ')->setQueryDefaultOperator('AND')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
                        $query2 = $this->client->createSelect();
                        $resultset = $this->client->select($query);
                        $data = $this->fetchData_checkSolr($resultset);
                        $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
                        $count = $resultset->getNumFound();
                        $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
                        $string_out = explode(" ", $search);
                        $string_out = json_encode($string_out, JSON_UNESCAPED_UNICODE);
                        $reponse = [
                            'data' => $data,
                            'Tbc' => $Tbc,
                            'option' => $option,
                            'type' => $type,
                            'count' => $count,
                            'count_bs' => $count_bs,
                            'count_ngan_chan' => $count_ngan_chan,
                            'count_ngan_chan_result' => $count_ngan_chan_result
                        ];
                        return $this->success($reponse, 'Thành công');
                    }
                }
            }
        } catch (\Solarium\Exception\HttpException $e) {
            $page = $request->page ?? 1;
            $numberInPage = 20;
            $query = $this->client->createSelect();
            $query2 = $this->client->createSelect();
            $query->setQuery("*:*")->setQueryDefaultOperator('OR')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)
                ->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
            $resultset = $this->client->select($query);
            $data = $this->fetchData_checkSolr($resultset);
            $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
            $count = $resultset->getNumFound();
            $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
            $reponse = [
                'data' => $data,
                'Tbc' => $Tbc,
                'option' => $option,
                'type' => $type,
                'count' => $count,
                'count_bs' => $count_bs,
                'count_ngan_chan' => $count_ngan_chan,
                'count_ngan_chan_result' => $count_ngan_chan_result
            ];
            return $this->success($reponse, 'Thành công');
        } catch (\Exception $e) {
            $page = $request->page ?? 1;
            $numberInPage = 20;
            $query = $this->client->createSelect();
            $query2 = $this->client->createSelect();
            $query->setQuery("*:*")->setQueryDefaultOperator('OR')->addSort('ngan_chan', $query::SORT_DESC)->addSort('st_id', $query::SORT_DESC)
                ->setStart(($page - 1) * $numberInPage)->setRows(($page) * $numberInPage);
            $resultset = $this->client->select($query);
            $data = $this->fetchData_checkSolr($resultset);
            $optionPaginate = ['path' => route('searchSolr'), 'query' => $request->query()];
            $count = $resultset->getNumFound();
            $data = $this->paginate($data, $numberInPage, $page, $optionPaginate, $count);
            $reponse = [
                'data' => $data,
                'Tbc' => $Tbc,
                'option' => $option,
                'type' => $type,
                'count' => $count,
                'count_bs' => $count_bs,
                'count_ngan_chan' => $count_ngan_chan,
                'count_ngan_chan_result' => $count_ngan_chan_result
            ];
            return $this->success($reponse, 'Thành công');
        }
    }
}
