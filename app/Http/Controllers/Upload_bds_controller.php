<?php

namespace App\Http\Controllers;

use App\Models\Upload_bds_model;
use Illuminate\Http\Request;
use Sentinel;
use App\Models\NhanVienModel;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Models\ChiNhanhModel;
use App\Exports\Export_BDS_moth;
use App\Exports\Export_BDS_sum;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\SuuTraModel;
use App\Http\Controllers\MyReadFilter;
use App\Imports\SumBDSImport;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Foundation\Validation\ValidatesRequests;


class Upload_bds_controller extends Controller
{
    use ValidatesRequests;
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
            <query> st_id:' . $st_id . '</query>   
         </delete>  ',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/xml'
            ),
        ));
        $response = curl_exec($curl);
        // dd($response);
        curl_close($curl);
    }

    function index(Request $request)
    {
        $role = Sentinel::check();
        if($role){
            $user_id =  $role->getAttributes();
            $user_id = $user_id['id'];
        }else{
            return view('admin.login');
        }
     
        $vpcc_id = NhanVienModel::where('nv_id', $user_id)->first()->nv_vanphong;
        $search = $request->all();
        $name = isset($search['name']) ? $search['name'] : null;
        $month = isset($search['month']) ? $search['month'] : null;
        $vpcc = isset($search['vpcc']) ? (int)$search['vpcc'] : null;
        $roleUser = Sentinel::check()->user_roles()->first()->slug;
        if ($name != null || $month != null || $vpcc != null) {
            if ($name != null) {
                $bank = Upload_bds_model::where('name', 'like', '%' . $name . '%')->orderBy('id', 'desc');
            } else if ($month != null) {
                // $bank = Upload_bds_model::where('date', 'like', '%' . $month . '%');
                //whereDate
                $bank = Upload_bds_model::whereMonth('date', $month);
            } else if ($vpcc != null) {
                $bank = Upload_bds_model::where('vpcc_id', '=', $vpcc);
            }
            $bank = $bank->orderBy('created_at', 'desc')->paginate(10);
        } else if ($roleUser == 'admin') {
            $bank = Upload_bds_model::orderBy('created_at', 'desc')->paginate(10);
        } else {
            $bank = Upload_bds_model::where('vpcc_id', $vpcc_id)->orderBy('created_at', 'desc')->paginate(10);
        }
        $count = Count($bank);
        //total
        $tong = count($bank);
        return view('admin.upload_bds.index ', compact('bank', 'count', 'tong', 'search'));
    }
    public function store(Request $request)
    {
        try {
            if ($request->hasFile('file')) {
                $this->validate($request, [
                    'name' => 'required',
                    'file' => 'required|mimes:xls,xlsx|max:2048',
                ]);
                $upload_bds = new Upload_bds_model;
                $upload_bds->name = $request->name;
                //get id user logged i
                $upload_bds->date = Carbon::parse($request->date);
                //rename file name
                $role = Sentinel::check();
                $user_id =  $role->getAttributes();
                $user_id = $user_id['id'];
                $vpcc_id = NhanVienModel::where('nv_id', $user_id)->first()->nv_vanphong;
                $vpcc_name = ChiNhanhModel::where('cn_id', $vpcc_id)->first()->cn_ten;
                $file = $request->file('file');
                $ext = $file->extension();
                $file_name = $vpcc_id . '_' . 'BDS' . '_' . time() . '.' . $ext;
                $file->storeAs('public/upload_bds', $file_name);
                $upload_bds->file = $file_name;
                $upload_bds->vpcc_name = $vpcc_name;
                $upload_bds->vpcc_id = (int)$vpcc_id;
                $upload_bds->user_id = (int)$user_id;
                $upload_bds->accepted = 0;
                $upload_bds->save();
                $a = 'storage/upload_bds/' . $file_name;
                //preview excel file
                $request->session()->flash('success', 'Tải lên thành công');
                return redirect('admin/bds/index');
            } else {
                $request->session()->flash('error', 'Chưa chọn file');
                return redirect('admin/bds/index');
            }
        } catch (\Exception $e) {
            $request->session()->flash('error', $e->getMessage());
            return redirect('admin/bds/index');
        }
    }
    //export excel
    public function export(Request $request)
    {
        if ($request->date != null) {
            $Uploaded = Upload_bds_model::where('date', Carbon::parse($request->date)->format('m-Y'))->get();
            $Uploaded_ar = [];
            foreach ($Uploaded as $Uploaded) {
                //array of cn_id
                $Uploaded_ar[] = (int)$Uploaded->vpcc_id;
            }
            //những đứa không nộp vào tháng đó
            $Uploaded = ChiNhanhModel::whereNotIn('cn_id', $Uploaded_ar)->get();
            //export excel
            return Excel::download(new Export_BDS_moth($Uploaded, Carbon::parse($request->date)->format('m-Y')), 'Upload_bds_' . Carbon::parse($request->date)->format('m-Y') . '_' . time() . '.xls');
        } else {
            $request->session()->flash('error', 'Chưa chọn tháng');
            return redirect('/bds/index');
        }
    }
    public function destroy(Request $request, $id)
    {
        try {
            $upload_bds = Upload_bds_model::find($id);
            $upload_bds->delete();
            $request->session()->flash('success', 'Xóa thành công');
            return redirect('admin/bds/index');
        } catch (\Exception $e) {
            $request->session()->flash('error', $e->getMessage());
            return redirect('admin/bds/index');
        }
    }
    public function edit($id)
    {
        $bank = Upload_bds_model::find($id);
        return view('admin.upload_bds.edit', compact('bank'));
    }
    public function update(Request $request, $id)
    {
        $bank = Upload_bds_model::find($id);
        $bank->name = $request->name;
        if ($request->hasFile('file')) {
            $this->validate($request, [
                'name' => 'required',
                'file' => 'required|mimes:xls,xlsx|max:2048',
            ]);
            $file = $request->file('file');
            $ext = $file->extension();
            $file_name = $bank->vpcc_id . '_' . 'BDS' . '_' . time() . '.' . $ext;
            $file->storeAs('public/upload_bds', $file_name);
            $bank->file = $file_name;
        }
        $role = Sentinel::check();
        $user_id =  $role->getAttributes();
        $user_id = $user_id['id'];
        $user_name = NhanVienModel::where('nv_id', $user_id)->first();
        $description =  'Cập nhật bởi: ' . $user_name->nv_hoten . ' lúc ' . Carbon::now()->format('d-m-Y H:i:s');
        $bank->edit_description = $description;
        $bank->save();
        //notification
        $request->session()->flash('success', 'Sửa thành công');
        return redirect('admin/bds/index');
    }
    public function readCell($columnAddress, $row, $worksheetName = '')
    {
        // Read title row and rows 20 - 30
        if ($row == 1 || ($row >= 20 && $row <= 30)) {
            return true;
        }
        return false;
    }
    public function accepted(Request $request)
    {
        try {
            $data_id = $request->data;
            $data_id = json_decode($data_id);
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $file_name_arr = [];
            $sum_each_file = [];
            $district_arr = [];
            $dat_nen1 = 0;
            $dat_nen2 = 0;
            $nha_o1 = 0;
            $nha_o2 = 0;
            $can_ho1 = 0;
            $can_ho2 = 0;
            $can_ho3 = 0;
            $van_phong = 0;
            $thuong_mai = 0;
            ///
            $ninhkieu = 0;
            $binhthuy = 0;
            $cairang = 0;
            $omom = 0;
            $phong_dien = 0;
            $thotnot = 0;
            $thoilai = 0;
            $codo = 0;
            $vinhthanh = 0;
            //
            $ninhkieu1 = 0;
            $binhthuy1 = 0;
            $cairang1 = 0;
            $omom1 = 0;
            $phong_dien1 = 0;
            $thotnot1 = 0;
            $thoilai1 = 0;
            $codo1 = 0;
            $vinhthanh1 = 0;
            //
            $ninhkieu2 = 0;
            $binhthuy2 = 0;
            $cairang2 = 0;
            $omom2 = 0;
            $phong_dien2 = 0;
            $thotnot2 = 0;
            $thoilai2 = 0;
            $codo2 = 0;
            $vinhthanh2 = 0;
            //
            $ninhkieu3 = 0;
            $binhthuy3 = 0;
            $cairang3 = 0;
            $omom3 = 0;
            $phong_dien3 = 0;
            $thotnot3 = 0;
            $thoilai3 = 0;
            $codo3 = 0;
            $vinhthanh3 = 0;
            //
            $ninhkieu4 = 0;
            $binhthuy4 = 0;
            $cairang4 = 0;
            $omom4 = 0;
            $phong_dien4 = 0;
            $thotnot4 = 0;
            $thoilai4 = 0;
            $codo4 = 0;
            $vinhthanh4 = 0;
            //
            $ninhkieu5 = 0;
            $binhthuy5 = 0;
            $cairang5 = 0;
            $omom5 = 0;
            $phong_dien5 = 0;
            $thotnot5 = 0;
            $thoilai5 = 0;
            $codo5 = 0;
            $vinhthanh5 = 0;
            //
            $ninhkieu6 = 0;
            $binhthuy6 = 0;
            $cairang6 = 0;
            $omom6 = 0;
            $phong_dien6 = 0;
            $thotnot6 = 0;
            $thoilai6 = 0;
            $codo6 = 0;
            $vinhthanh6 = 0;
            //
            $ninhkieu7 = 0;
            $binhthuy7 = 0;
            $cairang7 = 0;
            $omom7 = 0;
            $phong_dien7 = 0;
            $thotnot7 = 0;
            $thoilai7 = 0;
            $codo7 = 0;
            $vinhthanh7 = 0;
            //
            $ninhkieu8 = 0;
            $binhthuy8 = 0;
            $cairang8 = 0;
            $omom8 = 0;
            $phong_dien8 = 0;
            $thotnot8 = 0;
            $thoilai8 = 0;
            $codo8 = 0;
            $vinhthanh8 = 0;
            //
            $data = [];
            foreach ($data_id as $data_id) {
                $upload_bds = Upload_bds_model::find($data_id);
                $file_name_arr[] = $upload_bds->file;
                $upload_bds->accepted = 1;
                $upload_bds->save();
            }
            //read excel file in $file_name_arr
            foreach ($file_name_arr as $file) {
                $file = 'storage/upload_bds/' . $file;
                $collection = Excel::toArray(new SumBDSImport, $file);
                $collection = $collection[0];
                $collection = array_slice($collection, 11, 19);
                $collection = array_slice($collection, 0, 9);
                $phat_trien_theo_du_an = [
                    "Ninh Kiều" => $ninhkieu += $collection[0][2],
                    "Bình Thủy" => $binhthuy += $collection[1][2],
                    "Cái Răng" => $cairang += $collection[2][2],
                    "Ô Môn" => $omom += $collection[3][2],
                    "Thốt Nốt" => $thotnot += $collection[5][2],
                    "Phong Điền" => $phong_dien += $collection[4][2],
                    "Thới Lai" => $thoilai += $collection[6][2],
                    "Cờ Đỏ" => $codo += $collection[7][2],
                    "Vĩnh Thạnh" => $vinhthanh += $collection[8][2],
                ];
                $trong_kdc_hien_huu = [
                    "Ninh Kiều" => $ninhkieu1 += $collection[0][3],
                    "Bình Thủy" => $binhthuy1 += $collection[1][3],
                    "Cái Răng" => $cairang1 += $collection[2][3],
                    "Ô Môn" => $omom1 += $collection[3][3],
                    "Thốt Nốt" => $thotnot1 += $collection[5][3],
                    "Phong Điền" => $phong_dien1 += $collection[4][3],
                    "Thới Lai" => $thoilai1 += $collection[6][3],
                    "Cờ Đỏ" => $codo1 += $collection[7][3],
                    "Vĩnh Thạnh" => $vinhthanh1 += $collection[8][3],
                ];
                $phat_trien_theo_du_an_nha_o = [
                    "Ninh Kiều" => $ninhkieu2 += $collection[0][4],
                    "Bình Thủy" => $binhthuy2 += $collection[1][4],
                    "Cái Răng" => $cairang2 += $collection[2][4],
                    "Ô Môn" => $omom2 += $collection[3][4],
                    "Thốt Nốt" => $thotnot2 += $collection[5][4],
                    "Phong Điền" => $phong_dien2 += $collection[4][4],
                    "Thới Lai" => $thoilai2 += $collection[6][4],
                    "Cờ Đỏ" => $codo2 += $collection[7][4],
                    "Vĩnh Thạnh" => $vinhthanh2 += $collection[8][4],
                ];
                $trong_kdc_hien_huu_nha_o = [
                    "Ninh Kiều" => $ninhkieu3 += $collection[0][5],
                    "Bình Thủy" => $binhthuy3 += $collection[1][5],
                    "Cái Răng" => $cairang3 += $collection[2][5],
                    "Ô Môn" => $omom3 += $collection[3][5],
                    "Thốt Nốt" => $thotnot3 += $collection[5][5],
                    "Phong Điền" => $phong_dien3 += $collection[4][5],
                    "Thới Lai" => $thoilai3 += $collection[6][5],
                    "Cờ Đỏ" => $codo3 += $collection[7][5],
                    "Vĩnh Thạnh" => $vinhthanh3 += $collection[8][5],
                ];
                $can_ho_70m = [
                    "Ninh Kiều" => $ninhkieu4 += $collection[0][6],
                    "Bình Thủy" => $binhthuy4 += $collection[1][6],
                    "Cái Răng" => $cairang4 += $collection[2][6],
                    "Ô Môn" => $omom4 += $collection[3][6],
                    "Thốt Nốt" => $thotnot4 += $collection[5][6],
                    "Phong Điền" => $phong_dien4 += $collection[4][6],
                    "Thới Lai" => $thoilai4 += $collection[6][6],
                    "Cờ Đỏ" => $codo4 += $collection[7][6],
                    "Vĩnh Thạnh" => $vinhthanh4 += $collection[8][6],
                ];
                $can_ho_70_120 = [
                    "Ninh Kiều" => $ninhkieu5 += $collection[0][7],
                    "Bình Thủy" => $binhthuy5 += $collection[1][7],
                    "Cái Răng" => $cairang5 += $collection[2][7],
                    "Ô Môn" => $omom5 += $collection[3][7],
                    "Thốt Nốt" => $thotnot5 += $collection[5][7],
                    "Phong Điền" => $phong_dien5 += $collection[4][7],
                    "Thới Lai" => $thoilai5 += $collection[6][7],
                    "Cờ Đỏ" => $codo5 += $collection[7][7],
                    "Vĩnh Thạnh" => $vinhthanh5 += $collection[8][7],
                ];
                $can_ho_120 = [
                    "Ninh Kiều" => $ninhkieu6 += $collection[0][8],
                    "Bình Thủy" => $binhthuy6 += $collection[1][8],
                    "Cái Răng" => $cairang6 += $collection[2][8],
                    "Ô Môn" => $omom6 += $collection[3][8],
                    "Thốt Nốt" => $thotnot6 += $collection[5][8],
                    "Phong Điền" => $phong_dien6 += $collection[4][8],
                    "Thới Lai" => $thoilai6 += $collection[6][8],
                    "Cờ Đỏ" => $codo6 += $collection[7][8],
                    "Vĩnh Thạnh" => $vinhthanh6 += $collection[8][8],
                ];
                $van_phong_cho_thue = [
                    "Ninh Kiều" => $ninhkieu7 += $collection[0][9],
                    "Bình Thủy" => $binhthuy7 += $collection[1][9],
                    "Cái Răng" => $cairang7 += $collection[2][9],
                    "Ô Môn" => $omom7 += $collection[3][9],
                    "Thốt Nốt" => $thotnot7 += $collection[5][9],
                    "Phong Điền" => $phong_dien7 += $collection[4][9],
                    "Thới Lai" => $thoilai7 += $collection[6][9],
                    "Cờ Đỏ" => $codo7 += $collection[7][9],
                    "Vĩnh Thạnh" => $vinhthanh7 += $collection[8][9],
                ];
                $mat_bang_thuong_mai = [
                    "Ninh Kiều" => $ninhkieu8 += $collection[0][10],
                    "Bình Thủy" => $binhthuy8 += $collection[1][10],
                    "Cái Răng" => $cairang8 += $collection[2][10],
                    "Ô Môn" => $omom8 += $collection[3][10],
                    "Thốt Nốt" => $thotnot8 += $collection[5][10],
                    "Phong Điền" => $phong_dien8 += $collection[4][10],
                    "Thới Lai" => $thoilai8 += $collection[6][10],
                    "Cờ Đỏ" => $codo8 += $collection[7][10],
                    "Vĩnh Thạnh" => $vinhthanh8 += $collection[8][10],
                ];
                foreach ($collection as $collection) {
                    //forach quận
                    $collection = array_slice($collection, 1, 11);
                    $sum_detail = [
                        "Phát triển theo dự án(đất nền)" => $dat_nen1 += $collection[1],
                        "Trong khu dân cư hiện hữu(đất nền)" => $dat_nen2 += $collection[2],
                        "Phát triển theo dự án(nhà ở)" => $nha_o1 += $collection[3],
                        "Trong khu dân cư hiện hữu(nhà ở)" => $nha_o2 += $collection[4],
                        "Diện tích <= 70m2(căn hộ)" => $can_ho1 += $collection[5],
                        "Diện tích 70m2 < Diện tích <= 120m2(căn hộ)" => $can_ho2 += $collection[6],
                        "Diện tích > 120m2(căn hộ)" => $can_ho3 += $collection[7],
                        "Văn phòng cho thuê (m2)" => $van_phong += $collection[8],
                        "Mặt bằng thương mại, dịch vụ (m2)" => $thuong_mai += $collection[9],
                    ];
                }
                $detail_dicst = [
                    "Phát triển theo dự án(đất nền)" => $phat_trien_theo_du_an,
                    "Trong khu dân cư hiện hữu(đất nền)" =>    $trong_kdc_hien_huu,
                    "Phát triển theo dự án(nhà ở)" =>  $phat_trien_theo_du_an_nha_o,
                    "Trong khu dân cư hiện hữu(nhà ở)" => $trong_kdc_hien_huu_nha_o,
                    "Diện tích <= 70m2(căn hộ)" =>  $can_ho_70m,
                    "Diện tích 70m2 < Diện tích <= 120m2(căn hộ)" => $can_ho_70_120,
                    "Diện tích > 120m2(căn hộ)" => $can_ho_120,
                    "Văn phòng cho thuê (m2)" => $van_phong_cho_thue,
                    "Mặt bằng thương mại, dịch vụ (m2)" =>    $mat_bang_thuong_mai,
                ];
            }
            $data = [
                "sum" => $sum_detail,
                "detail" => $detail_dicst,
            ];
            return response()->json(['success' => '200']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    //export excel
    public function export_Sum(Request $request)
    {
        $dat_nen1 = 0;
        $dat_nen2 = 0;
        $nha_o1 = 0;
        $nha_o2 = 0;
        $can_ho1 = 0;
        $can_ho2 = 0;
        $can_ho3 = 0;
        $van_phong = 0;
        $thuong_mai = 0;
        ///
        $ninhkieu = 0;
        $binhthuy = 0;
        $cairang = 0;
        $omom = 0;
        $phong_dien = 0;
        $thotnot = 0;
        $thoilai = 0;
        $codo = 0;
        $vinhthanh = 0;
        //
        $ninhkieu1 = 0;
        $binhthuy1 = 0;
        $cairang1 = 0;
        $omom1 = 0;
        $phong_dien1 = 0;
        $thotnot1 = 0;
        $thoilai1 = 0;
        $codo1 = 0;
        $vinhthanh1 = 0;
        //
        $ninhkieu2 = 0;
        $binhthuy2 = 0;
        $cairang2 = 0;
        $omom2 = 0;
        $phong_dien2 = 0;
        $thotnot2 = 0;
        $thoilai2 = 0;
        $codo2 = 0;
        $vinhthanh2 = 0;
        //
        $ninhkieu3 = 0;
        $binhthuy3 = 0;
        $cairang3 = 0;
        $omom3 = 0;
        $phong_dien3 = 0;
        $thotnot3 = 0;
        $thoilai3 = 0;
        $codo3 = 0;
        $vinhthanh3 = 0;
        //
        $ninhkieu4 = 0;
        $binhthuy4 = 0;
        $cairang4 = 0;
        $omom4 = 0;
        $phong_dien4 = 0;
        $thotnot4 = 0;
        $thoilai4 = 0;
        $codo4 = 0;
        $vinhthanh4 = 0;
        //
        $ninhkieu5 = 0;
        $binhthuy5 = 0;
        $cairang5 = 0;
        $omom5 = 0;
        $phong_dien5 = 0;
        $thotnot5 = 0;
        $thoilai5 = 0;
        $codo5 = 0;
        $vinhthanh5 = 0;
        //
        $ninhkieu6 = 0;
        $binhthuy6 = 0;
        $cairang6 = 0;
        $omom6 = 0;
        $phong_dien6 = 0;
        $thotnot6 = 0;
        $thoilai6 = 0;
        $codo6 = 0;
        $vinhthanh6 = 0;
        //
        $ninhkieu7 = 0;
        $binhthuy7 = 0;
        $cairang7 = 0;
        $omom7 = 0;
        $phong_dien7 = 0;
        $thotnot7 = 0;
        $thoilai7 = 0;
        $codo7 = 0;
        $vinhthanh7 = 0;
        //
        $ninhkieu8 = 0;
        $binhthuy8 = 0;
        $cairang8 = 0;
        $omom8 = 0;
        $phong_dien8 = 0;
        $thotnot8 = 0;
        $thoilai8 = 0;
        $codo8 = 0;
        $vinhthanh8 = 0;
        //
        if ($request->date != null) {
            $date = $request->date;
            $Uploaded =  Upload_bds_model::whereMonth('date', $date)->where('accepted', 1)->get();
            $Uploaded_ar = [];
            foreach ($Uploaded as $Uploaded) {
                //array of cn_id
                $Uploaded_ar[] = $Uploaded->file;
            }
            $file_name_arr = $Uploaded_ar;
            //read excel file in $file_name_arr
            foreach ($file_name_arr as $file) {
                $file = 'storage/upload_bds/' . $file;
                $collection = Excel::toArray(new SumBDSImport, $file);
                $collection = $collection[0];
                $collection = array_slice($collection, 11, 19);
                $collection = array_slice($collection, 0, 9);
                $phat_trien_theo_du_an = [
                    "Ninh Kiều" => $ninhkieu += $collection[0][2],
                    "Bình Thủy" => $binhthuy += $collection[1][2],
                    "Cái Răng" => $cairang += $collection[2][2],
                    "Ô Môn" => $omom += $collection[3][2],
                    "Thốt Nốt" => $thotnot += $collection[4][2],
                    "Phong Điền" => $phong_dien += $collection[5][2],
                    "Thới Lai" => $thoilai += $collection[6][2],
                    "Cờ Đỏ" => $codo += $collection[7][2],
                    "Vĩnh Thạnh" => $vinhthanh += $collection[8][2],
                ];
                $trong_kdc_hien_huu = [
                    "Ninh Kiều" => $ninhkieu1 += $collection[0][3],
                    "Bình Thủy" => $binhthuy1 += $collection[1][3],
                    "Cái Răng" => $cairang1 += $collection[2][3],
                    "Ô Môn" => $omom1 += $collection[3][3],
                    "Thốt Nốt" => $thotnot1 += $collection[4][3],
                    "Phong Điền" => $phong_dien1 += $collection[5][3],
                    "Thới Lai" => $thoilai1 += $collection[6][3],
                    "Cờ Đỏ" => $codo1 += $collection[7][3],
                    "Vĩnh Thạnh" => $vinhthanh1 += $collection[8][3],
                ];
                $phat_trien_theo_du_an_nha_o = [
                    "Ninh Kiều" => $ninhkieu2 += $collection[0][4],
                    "Bình Thủy" => $binhthuy2 += $collection[1][4],
                    "Cái Răng" => $cairang2 += $collection[2][4],
                    "Ô Môn" => $omom2 += $collection[3][4],
                    "Thốt Nốt" => $thotnot2 += $collection[4][4],
                    "Phong Điền" => $phong_dien2 += $collection[5][4],
                    "Thới Lai" => $thoilai2 += $collection[6][4],
                    "Cờ Đỏ" => $codo2 += $collection[7][4],
                    "Vĩnh Thạnh" => $vinhthanh2 += $collection[8][4],
                ];
                $trong_kdc_hien_huu_nha_o = [
                    "Ninh Kiều" => $ninhkieu3 += $collection[0][5],
                    "Bình Thủy" => $binhthuy3 += $collection[1][5],
                    "Cái Răng" => $cairang3 += $collection[2][5],
                    "Ô Môn" => $omom3 += $collection[3][5],
                    "Thốt Nốt" => $thotnot3 += $collection[4][5],
                    "Phong Điền" => $phong_dien3 += $collection[5][5],
                    "Thới Lai" => $thoilai3 += $collection[6][5],
                    "Cờ Đỏ" => $codo3 += $collection[7][5],
                    "Vĩnh Thạnh" => $vinhthanh3 += $collection[8][5],
                ];
                $can_ho_70m = [
                    "Ninh Kiều" => $ninhkieu4 += $collection[0][6],
                    "Bình Thủy" => $binhthuy4 += $collection[1][6],
                    "Cái Răng" => $cairang4 += $collection[2][6],
                    "Ô Môn" => $omom4 += $collection[3][6],
                    "Thốt Nốt" => $thotnot4 += $collection[4][6],
                    "Phong Điền" => $phong_dien4 += $collection[5][6],
                    "Thới Lai" => $thoilai4 += $collection[6][6],
                    "Cờ Đỏ" => $codo4 += $collection[7][6],
                    "Vĩnh Thạnh" => $vinhthanh4 += $collection[8][6],
                ];
                $can_ho_70_120 = [
                    "Ninh Kiều" => $ninhkieu5 += $collection[0][7],
                    "Bình Thủy" => $binhthuy5 += $collection[1][7],
                    "Cái Răng" => $cairang5 += $collection[2][7],
                    "Ô Môn" => $omom5 += $collection[3][7],
                    "Thốt Nốt" => $thotnot5 += $collection[4][7],
                    "Phong Điền" => $phong_dien5 += $collection[5][7],
                    "Thới Lai" => $thoilai5 += $collection[6][7],
                    "Cờ Đỏ" => $codo5 += $collection[7][7],
                    "Vĩnh Thạnh" => $vinhthanh5 += $collection[8][7],
                ];
                $can_ho_120 = [
                    "Ninh Kiều" => $ninhkieu6 += $collection[0][8],
                    "Bình Thủy" => $binhthuy6 += $collection[1][8],
                    "Cái Răng" => $cairang6 += $collection[2][8],
                    "Ô Môn" => $omom6 += $collection[3][8],
                    "Thốt Nốt" => $thotnot6 += $collection[4][8],
                    "Phong Điền" => $phong_dien6 += $collection[5][8],
                    "Thới Lai" => $thoilai6 += $collection[6][8],
                    "Cờ Đỏ" => $codo6 += $collection[7][8],
                    "Vĩnh Thạnh" => $vinhthanh6 += $collection[8][8],
                ];
                $van_phong_cho_thue = [
                    "Ninh Kiều" => $ninhkieu7 += $collection[0][9],
                    "Bình Thủy" => $binhthuy7 += $collection[1][9],
                    "Cái Răng" => $cairang7 += $collection[2][9],
                    "Ô Môn" => $omom7 += $collection[3][9],
                    "Thốt Nốt" => $thotnot7 += $collection[4][9],
                    "Phong Điền" => $phong_dien7 += $collection[5][9],
                    "Thới Lai" => $thoilai7 += $collection[6][9],
                    "Cờ Đỏ" => $codo7 += $collection[7][9],
                    "Vĩnh Thạnh" => $vinhthanh7 += $collection[8][9],
                ];
                $mat_bang_thuong_mai = [
                    "Ninh Kiều" => $ninhkieu8 += $collection[0][10],
                    "Bình Thủy" => $binhthuy8 += $collection[1][10],
                    "Cái Răng" => $cairang8 += $collection[2][10],
                    "Ô Môn" => $omom8 += $collection[3][10],
                    "Thốt Nốt" => $thotnot8 += $collection[4][10],
                    "Phong Điền" => $phong_dien8 += $collection[5][10],
                    "Thới Lai" => $thoilai8 += $collection[6][10],
                    "Cờ Đỏ" => $codo8 += $collection[7][10],
                    "Vĩnh Thạnh" => $vinhthanh8 += $collection[8][10],
                ];
                foreach ($collection as $collection) {
                    //forach quận
                    $collection = array_slice($collection, 1, 11);
                    $sum_detail = [
                        "Phát triển theo dự án(đất nền)" => $dat_nen1 += $collection[1],
                        "Trong khu dân cư hiện hữu(đất nền)" => $dat_nen2 += $collection[2],
                        "Phát triển theo dự án(nhà ở)" => $nha_o1 += $collection[3],
                        "Trong khu dân cư hiện hữu(nhà ở)" => $nha_o2 += $collection[4],
                        "Diện tích <= 70m2(căn hộ)" => $can_ho1 += $collection[5],
                        "Diện tích 70m2 < Diện tích <= 120m2(căn hộ)" => $can_ho2 += $collection[6],
                        "Diện tích > 120m2(căn hộ)" => $can_ho3 += $collection[7],
                        "Văn phòng cho thuê (m2)" => $van_phong += $collection[8],
                        "Mặt bằng thương mại, dịch vụ (m2)" => $thuong_mai += $collection[9],
                    ];
                }
                $detail_dicst = [
                    0 => $phat_trien_theo_du_an,
                    1 =>    $trong_kdc_hien_huu,
                    2 =>  $phat_trien_theo_du_an_nha_o,
                    3 => $trong_kdc_hien_huu_nha_o,
                    4 =>  $can_ho_70m,
                    5 => $can_ho_70_120,
                    6 => $can_ho_120,
                    7 => $van_phong_cho_thue,
                    8 =>    $mat_bang_thuong_mai,
                ];
            }
            $data = [
                "sum" => $sum_detail,
                "detail" => $detail_dicst,
            ];
            return Excel::download(new Export_BDS_sum($data, $request->date), 'BDS_sum.xls');
        } else {
            $request->session()->flash('error', 'Chưa chọn tháng');
            return redirect('/bds/index');
        }
    }
    public function merge_content()
    {
        set_time_limit(0);
        $end_of_loop = (int)file_get_contents("C:/xampp/htdocs/aemSql/public/solr_error/end_of_solr.txt");
        $data = SuuTraModel::where('st_id', '>=', $end_of_loop)->whereNull('deleted_at')->orderBy('st_id', 'asc')->limit(500)->get();
        foreach ($data as $item) {
            $item->merge_content = $item->duong_su_en . ' ' . $item->texte_en;
            $item->save();
            $file = fopen("C:/xampp/htdocs/aemSql/public/solr_error/end_of_solr.txt", "w") or die("Unable to open file!");
            //write json to file
            $id = $item->getAttributes();
            fwrite($file,  $id['st_id'] . "\n");
            //close file
            fclose($file);
        }    
        return view('admin.suutra.reload');
    }
       
    public function dump_lost(){
        
        $file = (string)file_get_contents("C:/xampp/htdocs/aemSql/public/lost1.txt");
        // $file = explode(' ', $file);
        $file = json_decode($file);
        // $file = array_chunk($file, 2000);
        $result =[];
        foreach ($file as $item) {
            $data = SuuTraModel::where('ma_dong_bo', $item)->get();
            $data = $data->toArray();
            array_push($result,$data);
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
            $end_of_loop = (int)file_get_contents("C:/xampp/htdocs/aemSql/public/solr_error/end_of_solr.txt");
            $data = SuuTraModel::where('st_id', '>=', $end_of_loop)->whereNull('deleted_at')->orderBy('st_id', 'asc')->limit(500)->get();
            foreach ($data as $item) {
                $item->duong_su = str_replace(':', ' ', $item->duong_su);
                $item->duong_su_en = str_replace(':', ' ', $item->duong_su_en);
                $item->texte = str_replace(':', ' ', $item->texte);
                $item->texte_en = str_replace(':', ' ', $item->texte_en);
                $item->save();
                $id = $item->getAttributes();
                $file = fopen("C:/xampp/htdocs/aemSql/public/solr_error/end_of_solr.txt", "w") or die("Unable to open file!");
                fwrite($file, $id['st_id'] . "\n");
                fclose($file);
            }
            return view('admin.suutra.reload');
        } catch (QueryException $e) {
            $file = fopen("C:/xampp/htdocs/aemSql/public/solr_error/error.txt", "a") or die("Unable to open file!");
            $id = $item->getAttributes();
            fwrite($file, "*" . $id['st_id'] . "\n");
            fclose($file);
        }
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
                if($data){
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

}