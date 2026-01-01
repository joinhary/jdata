<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\ChiNhanhModel;
use App\Exports\BaoCaoExportSheet;
use App\Exports\export12b;
use App\Exports\exportBank;
use App\Exports\exportNotaryBook;
use App\Exports\exportNotaryView;
use App\Exports\SuuTraExport;
use App\Exports\ExportVanBan;
use App\Exports\ExportBDS;
use App\Exports\BCTKExportNhom;
use App\Models\NhanVienModel;
use App\Models\RoleUsersModel;
use App\Models\SuuTraModel;
use App\Models\VanBanModel;
use Carbon\Carbon;
use App\Http\Controllers\SuuTraController;
use App\Models\Kieuhopdong;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use DB;

class ReportController extends Controller
{

  use Nhanvien;

public function index(Request $request)
{
    $role = Sentinel::check()->user_roles()->first()->slug;

    if (in_array($role, ['admin', 'chuyen-vien-so'])) {
        return $this->indexAdmin($request);
    }

    return $this->indexUser($request);
}


  protected function indexAdmin(Request $request)
{
    $vars = $this->getSearchVariables();

    $code_cn = ChiNhanhModel::find(
        NhanVienModel::find(Sentinel::check()->id)->nv_vanphong
    )->code_cn;

    $query = SuuTraModel::query();

    if ($code_cn === 'THACT') {
        $query->where('suutranb.sync_code', $code_cn);
    }

    // ==== FILTER (y nguyên code cũ) ====
    $this->applyOldFilters($query, $request);

    $count = (clone $query)->count();

    $data = $query
        ->orderBy('st_id', 'desc')
        ->simplePaginate(20);

    return view(
        'admin.report.index',
        array_merge($vars, compact('data', 'count'))
    );
}



  protected function indexUser(Request $request)
{
    $vars = $this->getSearchVariables();

    $code_cn = ChiNhanhModel::find(
        NhanVienModel::find(Sentinel::check()->id)->nv_vanphong
    )->code_cn;

    $query = SuuTraModel::where('suutranb.sync_code', $code_cn);

    $this->applyOldFilters($query, $request);

    $count = (clone $query)->count();

    $data = $query
        ->orderBy('st_id', 'desc')
        ->simplePaginate(20);

    return view(
        'admin.report.index',
        array_merge($vars, compact('data', 'count'))
    );
}



 protected function applyOldFilters($data, Request $request)
{
    if ($request->filled('office')) {
        $data->where('suutranb.sync_code', $request->office);
    }

    if ($request->filled('contract')) {
        $contracts = array_filter((array)$request->contract);
        if (!empty($contracts)) {
            $data->whereIn('suutranb.loai', $contracts);
        }
    }

    if ($request->filled('notary')) {
        $data->where('suutranb.ccv_master', 'like', '%' . $request->notary . '%');
    }

    if ($request->filled('bank')) {
        $banks = array_filter((array)$request->bank);
        if (!empty($banks)) {
            if (in_array(0, $banks)) {
                $data->whereNotNull('bank_id');
            } else {
                $data->whereIn('bank_id', $banks);
            }
        }
    }

    if ($request->filled('dateFrom') && $request->filled('dateTo')) {
        $data->whereBetween('suutranb.ngay_cc', [
            $request->dateFrom,
            $request->dateTo
        ]);
    }

    if ($request->filled('status')) {
        $data->where('suutranb.ngan_chan', $request->status);
    }
}


protected function viewData(array $extra = [])
{
    return array_merge([
        'banks'    => Bank::pluck('name', 'id')->prepend('---Tất cả---', 0),
        'office'   => ChiNhanhModel::pluck('cn_ten', 'code_cn')->prepend('------', null),
        'contract' => Kieuhopdong::pluck('kieu_hd', 'lien_ket_id'),
        'notary'   => collect($this->get_ccv())->pluck('first_name', 'first_name')->prepend('------', ''),
        'prevent'  => collect([
            '' => '------',
            1  => 'Thường',
            2  => 'Cảnh báo',
            3  => 'Ngăn chặn',
        ]),
        'type'     => collect([
            1 => 'Sổ công chứng',
            4 => 'Báo cáo theo nhóm',
            5 => 'Báo cáo ngân hàng',
        ]),
        'sortExcel'=> collect([
            null => '------',
            1    => 'Số công chứng',
            2    => 'Ngày công chứng',
        ]),
        'str_json' => json_encode([]),
    ], $extra);
}
protected function getSearchVariables()
{
    $office   = ChiNhanhModel::pluck('cn_ten', 'code_cn')->prepend('------', null);

    $contract = Kieuhopdong::pluck('kieu_hd', 'lien_ket_id');

    $contract_key = request()->contract;
    if (empty($contract_key)) {
        $contract_key = Kieuhopdong::pluck('lien_ket_id')->toArray();
    }

    $banks = Bank::orderBy('name', 'asc')
        ->pluck('name', 'id')
        ->prepend('---Tất cả---', 0);

    $ccv_arr = $this->get_ccv();
    $notary  = collect($ccv_arr)
        ->pluck('first_name', 'first_name')
        ->prepend('------', '');

    $prevent = collect([
        '' => '------',
        1  => 'Thường',
        3  => 'Ngăn chặn',
        2  => 'Cảnh báo',
    ]);

    $sortExcel = collect([
        null => '------',
        1    => 'Số công chứng',
        2    => 'Ngày công chứng',
    ]);

    $type = collect([
        1 => 'Sổ công chứng',
        4 => 'Báo cáo theo nhóm',
        5 => 'Báo cáo ngân hàng',
    ]);

    return compact(
        'office',
        'contract',
        'contract_key',
        'banks',
        'notary',
        'prevent',
        'type',
        'sortExcel'
    );
}

  public function export(Request $request)
  {
    $dateFrom      = $request->dateFrom;
    $dateTo        = $request->dateTo;
    $sortExcel     = $request->sortExcel;
    $key           = "'" . '"' . "chữ ký" . '"' . "'";
    $user_id       = Sentinel::check()->id;
    $van_phong_id  = NhanVienModel::find($user_id)->nv_vanphong;
    $ten_van_phong = ChiNhanhModel::find($van_phong_id)->cn_ten;
    $role          = Sentinel::check()->user_roles()->first()->slug;
    $str_json      = json_encode([]);
    $code_cn       = ChiNhanhModel::find(NhanVienModel::find(Sentinel::check()->id)->nv_vanphong)->code_cn;
    if ($dateFrom == null && $dateTo == null) {
      $dateFrom = '2015-01-01';
      $dateTo = Carbon::now()->format('Y-m-d');
    } elseif ($dateFrom == null && $dateTo != null) {
      $dateFrom = '2015-01-01';
    } elseif ($dateFrom != null && $dateTo == null) {
      $dateTo = Carbon::now()->format('Y-m-d');
    }

    $array = [
      'suutranb.*',
      'bank.name',

    ];
    //        //sổ công chứng
    if ($request->type == SuuTraModel::TYPENORMAL) {
      if ($role == "admin" || $role == 'chuyen-vien-so') {
        if($code_cn !== "THACT"){
          $data = SuuTraModel::leftjoin('bank', 'id', '=', 'suutranb.bank_id')->select($array);
        }else{
          $data = SuuTraModel::where('suutranb.sync_code', $code_cn);
        }
      } else {
        $data = SuuTraModel::where('suutranb.sync_code', $code_cn);
      }
      /* thống kê theo văn phòng */
      if ($request->office) {
        $officeKey = '"' . $request->office . '"';
        $data      = $data->where('suutranb.sync_code', $request->get('office'));
      }
      $nameContract =    Kieuhopdong::pluck('kieu_hd', 'lien_ket_id');
      
      $selectedContract = $request->contract;
      $inListContract   = [];
      if (!empty($selectedContract) && count($selectedContract) == 1) {
        //dd($nhom_chuyen_nhuong);
              $selectedContract = $selectedContract[0];
              $data = $data->where('suutranb.loai', $selectedContract);

       
      } elseif (!empty($selectedContract)) {
      
        $data = $data->whereIn('suutranb.loai', $selectedContract);
      }
      /* thống kê theo công chứng viên */
      if ($request->notary) {
        $notaryKey = '"' . $request->notary . '"';

        $data->where('suutranb.ccv_master', 'like', '%' . $request->get('notary') . '%');
      }
      /* thông kế theo loại */
      if ($request->status == SuuTraModel::PREVENT) {
        $data->where('suutranb.ngan_chan', '=', SuuTraModel::PREVENT);
      }
      if ($request->status == SuuTraModel::WARNING) {
        $data->where('suutranb.ngan_chan', '=', SuuTraModel::WARNING);
      }
      if ($request->status == SuuTraModel::NORMAL && $request->status != NULL) {
        $data->where('suutranb.ngan_chan', '=', SuuTraModel::NORMAL);
      }
      if ($dateFrom != SuuTraModel::EMPTY && $dateTo == SuuTraModel::EMPTY) {
        $data = $data->whereDate('suutranb.ngay_cc', '>=', $dateFrom);
      }
      if ($dateFrom == SuuTraModel::EMPTY && $dateTo != SuuTraModel::EMPTY) {
        $data = $data->whereDate('suutranb.ngay_cc', '<=', $dateTo);
      }
      if ($dateFrom != SuuTraModel::EMPTY && $dateTo != SuuTraModel::EMPTY) {
        $data = $data->whereBetween('suutranb.ngay_cc', [$dateFrom, $dateTo]);
      }
      $data = $data->get();
      if ($request->sortExcel == 1) {
        $data = $data->sort(function ($a, $b) {
          $so_a = explode('/', $a->so_hd)[0];
          $so_a = preg_replace('/\D/', '', $so_a);
          $so_b = explode('/', $b->so_hd)[0];
          $so_b = preg_replace('/\D/', '', $so_b);
          return (int) $so_a - (int) $so_b;
        });
      } else {
        $data = $data->sortBy('ngay_cc');
      }
      return Excel::download(new exportNotaryBook($data), 'socongchung.xls');
    }
    //        // mẫu 12a
    if ($request->type == SuuTraModel::TYPE12A) {
      $so_luong_nhan_vien                 = NhanVienModel::where("nv_vanphong", '=', $van_phong_id)
        ->count();
      $so_chung_thuc_chu_ky               = SuuTraModel::where('sync_code', $code_cn)
        ->whereDate('suutranb.ngay_cc', '>=', $dateFrom)
        ->whereDate('suutranb.ngay_cc', '<=', $dateTo)
        ->whereRaw('contains(suutranb.ten_hd,' . $key . ')')
        ->count();
      $so_hop_dong_giao_dich              = SuuTraModel::where('sync_code', $code_cn)
        ->whereDate('suutranb.ngay_cc', '>=', $dateFrom)
        ->whereDate('suutranb.ngay_cc', '<=', $dateTo)
        ->count() - $so_chung_thuc_chu_ky;
      $tong_so                            = SuuTraModel::where('sync_code', $code_cn)
        ->whereDate('suutranb.ngay_cc', '>=', $dateFrom)
        ->whereDate('suutranb.ngay_cc', '<=', $dateTo)
        ->count() - $so_chung_thuc_chu_ky;
      $phi_chung_thuc_chu_ky              = SuuTraModel::where('sync_code', $code_cn)
        ->whereDate('suutranb.ngay_cc', '>=', $dateFrom)
        ->whereDate('suutranb.ngay_cc', '<=', $dateTo)
        ->whereRaw('contains(suutranb.ten_hd,' . $key . ')')
        ->sum('phi_cong_chung');
      $phi_hop_dong_giao_dich             = SuuTraModel::where('sync_code', $code_cn)
        ->whereDate('suutranb.ngay_cc', '>=', $dateFrom)
        ->whereDate('suutranb.ngay_cc', '<=', $dateTo)
        ->sum('phi_cong_chung') - $phi_chung_thuc_chu_ky;
      $thu_lao_hop_dong_giao_dich         = SuuTraModel::where('sync_code', $code_cn)
        ->whereDate('suutranb.ngay_cc', '>=', $dateFrom)
        ->whereDate('suutranb.ngay_cc', '<=', $dateTo)
        ->sum('thu_lao');
      $data['so_luong_nhan_vien']         = $so_luong_nhan_vien;
      $data['so_chung_thuc_chu_ky']       = $so_chung_thuc_chu_ky;
      $data['so_hop_dong_giao_dich']      = $so_hop_dong_giao_dich;
      $data['phi_chung_thuc_chu_ky']      = $phi_chung_thuc_chu_ky;
      $data['phi_hop_dong_giao_dich']     = $phi_hop_dong_giao_dich;
      $data['thu_lao_hop_dong_giao_dich'] = $thu_lao_hop_dong_giao_dich;
      $data['tong_so']                    = $tong_so;
      $data['ten_van_phong']              = $ten_van_phong;
      $tu_ngay                            = explode('-', $dateFrom);
      $den_ngay                           = explode('-', $dateTo);
      $data['Y_tungay']                   = $tu_ngay[0];
      $data['M_tungay']                   = $tu_ngay[1];
      $data['D_tungay']                   = $tu_ngay[2];
      $data['Y_denngay']                  = $den_ngay[0];
      $data['M_denngay']                  = $den_ngay[1];
      $data['D_denngay']                  = $den_ngay[2];
      //            return view('admin.report.export12a', compact('data'));
      return Excel::download(new BaoCaoExportSheet($data), '12A.xls');
    }
    //mẫu 12b
    if ($request->type == SuuTraModel::TYPE12B) {
      $data = SuuTraModel::select('sync_code');
      if ($dateFrom != SuuTraModel::EMPTY && $dateTo == SuuTraModel::EMPTY) {
        $data->whereDate('ngay_cc', '>=', $dateFrom);
      }
      if ($dateFrom == SuuTraModel::EMPTY && $dateTo != SuuTraModel::EMPTY) {
        $data->whereDate('ngay_cc', '<=', $dateTo);
      }
      if ($dateFrom != SuuTraModel::EMPTY && $dateTo != SuuTraModel::EMPTY) {
        $data->whereBetween('ngay_cc', [$dateFrom, $dateTo]);
      }
      $data = $data->groupBy('suutranb.sync_code')->get();

      $total = [
        'sumEmployee'     => 0,
        'sumAuth'         => 0,
        'sumDeal'         => 0,
        'totals'          => 0,
        'costAuth'        => 0,
        'costDeal'        => 0,
        'sumRemuneration' => 0,
      ];

      $idChiNhanh  = ChiNhanhModel::whereIn('code_cn', $data->pluck('sync_code'))->get()->pluck('cn_id');
      $sumEmployee = NhanVienModel::whereIn('nv_vanphong', $idChiNhanh)->count();
      //if has Date
      if ($dateFrom != SuuTraModel::EMPTY && $dateTo !== SuuTraModel::EMPTY) {
        $sumAuth = SuuTraModel::whereIn('sync_code', $data->pluck('sync_code'))->whereDate(
          'ngay_cc',
          '>=',
          $dateFrom
        )
          ->whereDate('ngay_cc', '<=', $dateTo)
          ->whereRaw('contains(suutranb.ten_hd,' . $key . ')')
          ->count();
        $sumDeal = SuuTraModel::whereIn('sync_code', $data->pluck('sync_code'))->whereDate(
          'ngay_cc',
          '>=',
          $dateFrom
        )
          ->whereDate('ngay_cc', '<=', $dateTo)
          ->count() - $sumAuth;
        $totals  = SuuTraModel::whereIn('sync_code', $data->pluck('sync_code'))->whereDate(
          'ngay_cc',
          '>=',
          $dateFrom
        )
          ->whereDate('ngay_cc', '<=', $dateTo)
          ->count() - $sumAuth;

        $costAuth                 = SuuTraModel::whereIn('sync_code', $data->pluck('sync_code'))->whereDate(
          'ngay_cc',
          '>=',
          $dateFrom
        )
          ->whereDate('ngay_cc', '<=', $dateTo)
          ->whereRaw('contains(suutranb.ten_hd,' . $key . ')')->sum('phi_cong_chung');
        $costDeal                 = SuuTraModel::whereIn('sync_code', $data->pluck('sync_code'))->whereDate(
          'ngay_cc',
          '>=',
          $dateFrom
        )
          ->whereDate('ngay_cc', '<=', $dateTo)
          ->sum('phi_cong_chung') - $costAuth;
        $sumRemuneration          = SuuTraModel::whereIn('sync_code', $data->pluck('sync_code'))->whereDate(
          'ngay_cc',
          '>=',
          $dateFrom
        )
          ->whereDate('ngay_cc', '<=', $dateTo)
          ->sum('thu_lao');
        $total['sumEmployee']     = $sumEmployee;
        $total['sumAuth']         = $sumAuth;
        $total['sumDeal']         = $sumDeal;
        $total['totals']          = $totals;
        $total['costAuth']        = $costAuth;
        $total['costDeal']        = $costDeal;
        $total['sumRemuneration'] = $sumRemuneration;
        //dd($data->pluck('sync_code'),$total);
        $from               = explode('-', $dateFrom);
        $to                 = explode('-', $dateTo);
        $total['yearFrom']  = $from[0];
        $total['monthFrom'] = $from[1];
        $total['dayFrom']   = $from[2];
        $total['yearTo']    = $to[0];
        $total['monthTo']   = $to[1];
        $total['dayTo']     = $to[2];
        $data = $data->sort(function ($a, $b) {
          $so_a = explode('/', $a->so_hd)[0];
          $so_a = preg_replace('/\D/', '', $so_a);
          if (!$so_a) {
            $so_a = 0;
          }
          $so_b = explode('/', $b->so_hd)[0];
          $so_b = preg_replace('/\D/', '', $so_b);
          if (!$so_b) {
            $so_b = 0;
          }
          return $so_a - $so_b;
        });
        return Excel::download(new export12b($data, $total, $dateFrom, $dateTo), '12b.xls');
      } else {
        $sumAuth = SuuTraModel::whereIn('sync_code', $data->pluck('sync_code'))->whereRaw('contains(suutranb.ten_hd,' . $key . ')')->count();
        $sumDeal = SuuTraModel::whereIn('sync_code', $data->pluck('sync_code'))->count() - $sumAuth;
        $totals  = SuuTraModel::whereIn('sync_code', $data->pluck('sync_code'))->count() - $sumAuth;
        $costAuth                 = SuuTraModel::whereIn('sync_code', $data->pluck('sync_code'))->whereRaw('contains(suutranb.ten_hd,' . $key . ')')->sum('phi_cong_chung');
        $costDeal                 = SuuTraModel::whereIn('sync_code', $data->pluck('sync_code'))->sum('phi_cong_chung') - $costAuth;
        $sumRemuneration          = SuuTraModel::whereIn('sync_code', $data->pluck('sync_code'))->sum('thu_lao');
        $total['sumEmployee']     = $sumEmployee;
        $total['sumAuth']         = $sumAuth;
        $total['sumDeal']         = $sumDeal;
        $total['totals']          = $totals;
        $total['costAuth']        = $costAuth;
        $total['costDeal']        = $costDeal;
        $total['sumRemuneration'] = $sumRemuneration;
        $total['dayFrom']         = '';
        $total['monthFrom']       = '';
        $total['yearFrom']        = '';
        $total['dayTo']           = '';
        $total['monthTo']         = '';
        $total['yearTo']          = '';
        //dd($data->pluck('sync_code'),$total);
        $dateFrom = '';
        $dateTo   = '';
        $data = $data->sort(function ($a, $b) {
          $so_a = explode('/', $a->so_hd)[0];
          $so_a = preg_replace('/\D/', '', $so_a);
          if (!$so_a) {
            $so_a = 0;
          }
          $so_b = explode('/', $b->so_hd)[0];
          $so_b = preg_replace('/\D/', '', $so_b);
          if (!$so_b) {
            $so_b = 0;
          }
          return $so_a - $so_b;
        });
        return Excel::download(new export12b($data, $total, $dateFrom, $dateTo), '12b.xls');
      }
    }
    if ($request->type == 4) {
      if ($role == "admin" || $role == 'chuyen-vien-so') {
        if($code_cn !== "THACT"){
          $data = SuuTraModel::
          //                    ->leftjoin('chinhanh', 'cn_id', '=', 'suutranb.vp')
          select($array);
        }else{
          $data = SuuTraModel::
          //   ->leftjoin('chinhanh', 'cn_id', '=', 'suutranb.vp')
          whereRaw('contains(suutranb.sync_code,' . "'" . $code_cn . "'" . ')')
          ->select($array);
        }

      } else {
        $data = SuuTraModel::
          //   ->leftjoin('chinhanh', 'cn_id', '=', 'suutranb.vp')
          whereRaw('contains(suutranb.sync_code,' . "'" . $code_cn . "'" . ')')
          ->select($array);
      }
      /* thống kê theo văn phòng */
      if ($request->office) {
        $officeKey = '"' . $request->office . '"';
        $data      = $data->where('suutranb.sync_code', $request->get('office'));
      }
      $nameContractPrevent = SuuTraModel::where('ngan_chan', 3)->groupBy('ten_hd')->pluck('ten_hd');
      $nameContract        = SuuTraModel::groupBy('ten_hd')->pluck('ten_hd');
      $nameContract        = array_diff($nameContract->toArray(), $nameContractPrevent->toArray());
      $nhom_chuyen_nhuong  = [];
      $nhom_the_chap       = [];
      $nhom_uy_quyen       = [];
      $nhom_thua_ke        = [];
      $nhom_thue_muon      = [];
      $nhom_tang_cho       = [];
      $nhom_vong_chong     = [];
      $nhom_di_chuc        = [];
      $nhom_chu_ky         = [];
      $nhom_vay            = [];
      $nhom_huy            = [];
      $nhom_sua_doi        = [];
      $nhom_khac = [];
      $nhom_gop_von        = [];

      foreach ($nameContract as $item) {
        if ((stristr(mb_strtolower($item), "chuyển nhượng") || stristr(mb_strtolower($item), "hđcn")
            || stristr(
              mb_strtolower($item),
              "đặt cọc"
            )
            || stristr(mb_strtolower($item), "mua")
            || stristr(mb_strtolower($item), "bán"))
          && !stristr(mb_strtolower($item), 'tặng cho')
        ) {
          $nhom_chuyen_nhuong[] = $item;
        } elseif (stristr(mb_strtolower($item), "tặng cho") || stristr(mb_strtolower($item), "tặng")) {
          $nhom_tang_cho[] = $item;
        } elseif (
          stristr(mb_strtolower($item), "thế chấp") || stristr(mb_strtolower($item), "cầm cố")
          || stristr(mb_strtolower($item), "hđtc")
        ) {
          $nhom_the_chap[] = $item;
        } elseif (stristr(mb_strtolower($item), "ủy quyền")) {
          $nhom_uy_quyen[] = $item;
        } elseif (stristr(mb_strtolower($item), "thuê") || stristr(mb_strtolower($item), "mượn")) {
          $nhom_thue_muon[] = $item;
        } elseif (stristr(mb_strtolower($item), "thừa kế")) {
          $nhom_thua_ke[] = $item;
        } elseif (stristr(mb_strtolower($item), "vợ chồng")) {
          $nhom_vong_chong[] = $item;
        } elseif (stristr(mb_strtolower($item), "di chúc")) {
          $nhom_di_chuc[] = $item;
        } elseif (stristr(mb_strtolower($item), "chữ ký")) {
          $nhom_chu_ky[] = $item;
        } elseif (stristr(mb_strtolower($item), "vay")) {
          $nhom_vay[] = $item;
        } elseif (stristr(mb_strtolower($item), "góp vốn")) {
          $nhom_gop_von[] = $item;
        } elseif (
          stristr(mb_strtolower($item), "thanh lý") || stristr(mb_strtolower($item), "huỷ")
          || stristr(
            mb_strtolower($item),
            "HỦY"
          )
          || stristr(mb_strtolower($item), "hủy")
          || stristr(mb_strtolower($item), "chấm dứt")
        ) {
          $nhom_huy[] = $item;
        } elseif (
          stristr(mb_strtolower($item), "sửa") || stristr(mb_strtolower($item), "bổ sung")
          || stristr(
            $item,
            "phụ lục"
          )
        ) {
          $nhom_sua_doi[] = $item;
        }
      }
      $temp = array_merge(
        $nhom_chu_ky,
        $nhom_di_chuc,
        $nhom_vong_chong,
        $nhom_thua_ke,
        $nhom_thue_muon,
        $nhom_uy_quyen,
        $nhom_the_chap,
        $nhom_tang_cho,
        $nhom_chuyen_nhuong,
        $nhom_huy,
        $nhom_sua_doi,
        $nhom_vay,
        $nhom_gop_von
      );

      $nhom_khac = array_diff($nameContract, $temp);

      /* thống kê theo công chứng viên */
      if ($request->notary) {
        $notaryKey = '"' . $request->notary . '"';

        $data->where('suutranb.ccv_master', 'like', '%' . $request->get('notary') . '%');
      }
      /* thông kế theo loại */
      if ($request->status == SuuTraModel::PREVENT) {
        $data->where('suutranb.ngan_chan', '=', SuuTraModel::PREVENT);
      }
      if ($request->status == SuuTraModel::WARNING) {
        $data->where('suutranb.ngan_chan', '=', SuuTraModel::WARNING);
      }
      if ($request->status == SuuTraModel::NORMAL && $request->status != NULL) {
        $data->where('suutranb.ngan_chan', '=', SuuTraModel::NORMAL);
      }
      if ($dateFrom != SuuTraModel::EMPTY && $dateTo == SuuTraModel::EMPTY) {
        $data = $data->whereDate('suutranb.ngay_cc', '>=', $dateFrom);
      }
      if ($dateFrom == SuuTraModel::EMPTY && $dateTo != SuuTraModel::EMPTY) {
        $data = $data->whereDate('suutranb.ngay_cc', '<=', $dateTo);
      }
      if ($dateFrom != SuuTraModel::EMPTY && $dateTo != SuuTraModel::EMPTY) {
        $data = $data->whereBetween('suutranb.ngay_cc', [$dateFrom, $dateTo]);
      }
      $filterBuilder = clone $data;

      $selectedContract = $request->contract;
      // $inListContract   = [];
      // if (!empty($selectedContract) && count($selectedContract) == 1) {
      //   //dd($nhom_chuyen_nhuong);
      //   $selectedContract = $selectedContract[0];
      //   switch ($selectedContract) {
      //     case 1:
      //       $data = $data->whereIn('suutranb.ten_hd', $nhom_chuyen_nhuong);
      //       break;
      //     case 2:
      //       $data = $data->whereIn('suutranb.ten_hd', $nhom_tang_cho);
      //       break;
      //     case 3:
      //       $data = $data->whereIn('suutranb.ten_hd', $nhom_the_chap);
      //       break;
      //     case 4:
      //       $data = $data->whereIn('suutranb.ten_hd', $nhom_uy_quyen);
      //       break;
      //     case 5:
      //       $data = $data->whereIn('suutranb.ten_hd', $nhom_thua_ke);
      //       break;
      //     case 6:
      //       $data = $data->whereIn('suutranb.ten_hd', $nhom_di_chuc);
      //       break;
      //     case 7:
      //       $data = $data->whereIn('suutranb.ten_hd', $nhom_thue_muon);
      //       break;
      //     case 8:
      //       $data = $data->whereIn('suutranb.ten_hd', $nhom_vong_chong);
      //       break;
      //     case 9:
      //       $data = $data->whereIn('suutranb.ten_hd', $nhom_chu_ky);
      //       break;
      //     case 10:
      //       $data = $data->whereIn('suutranb.ten_hd', $nhom_vay);
      //       break;
      //     case 11:
      //       $data = $data->whereIn('suutranb.ten_hd', $nhom_huy);
      //       break;
      //     case 12:
      //       $data = $data->whereIn('suutranb.ten_hd', $nhom_sua_doi);
      //       break;
      //     case 13:
      //       $data = $data->whereIn('suutranb.ten_hd', $nhom_gop_von);
      //       break;  
      //     case 14:
      //       $data = $data->whereIn('suutranb.ten_hd', $nhom_khac);
      //       break;
      //   }
      // } elseif (!empty($selectedContract)) {
      //   foreach ($selectedContract as $item) {
      //     switch ($item) {
      //       case 1:
      //         $inListContract = array_merge($inListContract, $nhom_chuyen_nhuong);
      //         break;
      //       case 2:
      //         $inListContract = array_merge($inListContract, $nhom_tang_cho);
      //         break;
      //       case 3:
      //         $inListContract = array_merge($inListContract, $nhom_the_chap);
      //         break;
      //       case 4:
      //         $inListContract = array_merge($inListContract, $nhom_uy_quyen);
      //         break;
      //       case 5:
      //         $inListContract = array_merge($inListContract, $nhom_thua_ke);
      //         break;
      //       case 6:
      //         $inListContract = array_merge($inListContract, $nhom_di_chuc);
      //         break;
      //       case 7:
      //         $inListContract = array_merge($inListContract, $nhom_thue_muon);
      //         break;
      //       case 8:
      //         $inListContract = array_merge($inListContract, $nhom_vong_chong);
      //         break;
      //       case 9:
      //         $inListContract = array_merge($inListContract, $nhom_chu_ky);
      //         break;
      //       case 10:
      //         $inListContract = array_merge($inListContract, $nhom_vay);
      //         break;
      //       case 11:
      //         $inListContract = array_merge($inListContract, $nhom_huy);
      //         break;
      //       case 12:
      //         $inListContract = array_merge($inListContract, $nhom_sua_doi);
      //         break;
      //       case 13:
      //         $inListContract = array_merge($inListContract, $nhom_gop_von);
      //         break;
      //       case 14:
      //         $inListContract = array_merge($inListContract, $nhom_khac);
      //         break;
      //     }
      //   }
      //   $data = $data->whereIn('suutranb.ten_hd', $inListContract);
      // }
      
    $inListContract   = [];
    if (!empty($selectedContract) && count($selectedContract) == 1) {
      $contract_key = $selectedContract;
      //dd($nhom_chuyen_nhuong);
      $selectedContract = $selectedContract[0];
      $data = $data->where('suutranb.loai', $selectedContract);
     
    } elseif (!empty($selectedContract)) {
      $data = $data->whereIn('suutranb.loai', $selectedContract);
    }
      $count = [];
      $total = $data->count();
      $nhom      = Kieuhopdong::pluck('kieu_hd', 'lien_ket_id');
      $nhom=array_values($nhom->toArray());
      $filterBuilder = clone $data;
      $count[] = $filterBuilder->whereIn('suutranb.loai',[1])->count();
      $filterBuilder = clone $data;
      $count[]       = $filterBuilder->whereIn('suutranb.loai',[2])->count();
      $filterBuilder = clone $data;

      $count[] = $filterBuilder->whereIn('suutranb.loai', [3])->count();

      $filterBuilder = clone $data;
      $count[]       = $filterBuilder->whereIn('suutranb.loai',[4])->count();

      $filterBuilder = clone $data;
      $count[]       = $filterBuilder->whereIn('suutranb.loai', [5])->count();
  
    
      $filterBuilder = clone $data;
      $count[]       = $filterBuilder->whereIn('suutranb.loai', [6])->count();
      $filterBuilder = clone $data;

      $count[]       = $filterBuilder->whereIn('suutranb.loai', [7])->count();
      $filterBuilder = clone $data;

      $count[] = $filterBuilder->whereIn('suutranb.loai', [8])->count();

      $filterBuilder = clone $data;

      $count[] = $filterBuilder->whereIn('suutranb.loai', [9])->count();
      $filterBuilder = clone $data;

      $count[]       = $filterBuilder->whereIn('suutranb.loai', [10])->count();
      $filterBuilder = clone $data;
      $count[]       = $filterBuilder->whereIn('suutranb.loai', [11])->count();
      $filterBuilder = clone $data;

      $count[]       = $filterBuilder->whereIn('suutranb.loai', [12])->count();
      $filterBuilder = clone $data;


      $count[]       = $filterBuilder->whereIn('suutranb.loai', [13])->count();
      $filterBuilder = clone $data;
      $count[]       = $filterBuilder->whereIn('suutranb.loai', [14])->count();


      return Excel::download(new BCTKExportNhom($count, $nhom, $total), 'baocao.xls');
    }
    if ($request->type == SuuTraModel::BANK) {
      if ($request->bank) {
        foreach ($request->bank as $item) {
          if ($item == 0) {
            $code_cn = ChiNhanhModel::find(NhanVienModel::find(Sentinel::check()->id)->nv_vanphong)->code_cn;
            //suutra.bank_id get all
            if ($role == "admin" || $role == 'chuyen-vien-so') {
              if($code_cn !== "THACT"){
                $data = SuuTraModel::whereNotNull('bank_id')->leftjoin('bank', 'bank.id', '=', 'suutranb.bank_id')
                ->whereBetween('suutranb.ngay_cc', [$dateFrom, $dateTo]);
              // ->leftjoin('chinhanh', 'cn_id', '=', 'suutranb.vp')
              }else{
                $data = SuuTraModel::whereNotNull('bank_id')->leftjoin('bank', 'bank.id', '=', 'suutranb.bank_id')
                ->whereBetween('suutranb.ngay_cc', [$dateFrom, $dateTo])
                //                whereRaw('contains(suutranb.sync_code,'."'".$code_cn."'".')')
                ->where('suutranb.sync_code', $code_cn);
              }


            } else {
              $data = SuuTraModel::whereNotNull('bank_id')->leftjoin('bank', 'bank.id', '=', 'suutranb.bank_id')
                ->whereBetween('suutranb.ngay_cc', [$dateFrom, $dateTo])
                //                whereRaw('contains(suutranb.sync_code,'."'".$code_cn."'".')')
                ->where('suutranb.sync_code', $code_cn);
            }

            if ($request->office) {
              $officeKey = '"' . $request->office . '"';
              $data      = $data->where('suutranb.sync_code', $request->get('office'));
            }
            $data = $data->get();
            if ($data->isEmpty()) {
              return redirect(route('indexReport'))->withErrors('Không có dữ liệu');
            }

            $banks = Bank::orderBy('name', 'asc')->get();
            $user_id = Sentinel::check()->id;
            $vp_id   = NhanVienModel::find($user_id)->nv_vanphong;
            $vp      = ChiNhanhModel::find($vp_id);
            $bank_arr = [];
            foreach ($data as $item) {
              $bank_arr[] = $item->bank_id;
            }
            $bank_arr = array_unique($bank_arr);
            //find $bank_arr in $banks
            $bank_arr = Bank::whereIn('id', $bank_arr)->orderBy('name', 'asc')->get();
            $banks = $bank_arr;
            return Excel::download(new exportBank($banks, $data, $vp, $dateTo, $dateFrom), 'ngan-hang.xls');
          }
        }
      }
      if ($role == "admin" || $role == 'chuyen-vien-so') {
        if($code_cn !== "THACT"){
          $data = SuuTraModel::leftjoin('bank', 'bank.id', '=', 'suutranb.bank_id')
          // ->leftjoin('chinhanh', 'cn_id', '=', 'suutranb.vp')
          ->select($array);
        }else{
          $data = SuuTraModel::leftjoin('bank', 'bank.id', '=', 'suutranb.bank_id')
          // ->leftjoin('chinhanh', 'cn_id', '=', 'suutranb.vp')
          ->select($array);
        }
 
      } else {
          $data = SuuTraModel::leftjoin('bank', 'bank.id', '=', 'suutranb.bank_id')
          // ->leftjoin('chinhanh', 'cn_id', '=', 'suutranb.vp')
          ->select($array);
      }
      /* thống kê theo văn phòng */
      if ($request->office) {
        $officeKey = '"' . $request->office . '"';
        $data      = $data->where('suutranb.sync_code', $request->get('office'));
      }
      $nameContractPrevent = SuuTraModel::where('ngan_chan', 3)->groupBy('ten_hd')->pluck('ten_hd');
      $nameContract        = SuuTraModel::groupBy('ten_hd')->pluck('ten_hd');
      $nameContract        = array_diff($nameContract->toArray(), $nameContractPrevent->toArray());
      $nhom_chuyen_nhuong  = [];
      $nhom_the_chap       = [];
      $nhom_uy_quyen       = [];
      $nhom_thua_ke        = [];
      $nhom_thue_muon      = [];
      $nhom_tang_cho       = [];
      $nhom_vong_chong     = [];
      $nhom_di_chuc        = [];
      $nhom_chu_ky         = [];
      $nhom_vay            = [];
      $nhom_huy            = [];
      $nhom_sua_doi        = [];
      $nhom_khac = [];
      $nhom_gop_von        = [];
      foreach ($nameContract as $item) {
        if ((stristr(mb_strtolower($item), "chuyển nhượng") || stristr(mb_strtolower($item), "hđcn")
            || stristr(
              mb_strtolower($item),
              "đặt cọc"
            )
            || stristr(mb_strtolower($item), "mua")
            || stristr(mb_strtolower($item), "bán"))
          && !stristr(mb_strtolower($item), 'tặng cho')
        ) {
          $nhom_chuyen_nhuong[] = $item;
        } elseif (stristr(mb_strtolower($item), "tặng cho") || stristr(mb_strtolower($item), "tặng")) {
          $nhom_tang_cho[] = $item;
        } elseif (
          stristr(mb_strtolower($item), "thế chấp") || stristr(mb_strtolower($item), "cầm cố")
          || stristr(mb_strtolower($item), "hđtc")
        ) {
          $nhom_the_chap[] = $item;
        } elseif (stristr(mb_strtolower($item), "ủy quyền")) {
          $nhom_uy_quyen[] = $item;
        } elseif (stristr(mb_strtolower($item), "thuê") || stristr(mb_strtolower($item), "mượn")) {
          $nhom_thue_muon[] = $item;
        } elseif (stristr(mb_strtolower($item), "thừa kế")) {
          $nhom_thua_ke[] = $item;
        } elseif (stristr(mb_strtolower($item), "vợ chồng")) {
          $nhom_vong_chong[] = $item;
        } elseif (stristr(mb_strtolower($item), "di chúc")) {
          $nhom_di_chuc[] = $item;
        } elseif (stristr(mb_strtolower($item), "chữ ký")) {
          $nhom_chu_ky[] = $item;
        } elseif (stristr(mb_strtolower($item), "vay")) {
          $nhom_vay[] = $item;
        } elseif (stristr(mb_strtolower($item), "góp vốn")) {
          $nhom_gop_von[] = $item;
        } elseif (
          stristr(mb_strtolower($item), "thanh lý") || stristr(mb_strtolower($item), "huỷ")
          || stristr(
            mb_strtolower($item),
            "HỦY"
          )
          || stristr(mb_strtolower($item), "hủy")
          || stristr(mb_strtolower($item), "chấm dứt")
        ) {
          $nhom_huy[] = $item;
        } elseif (
          stristr(mb_strtolower($item), "sửa") || stristr(mb_strtolower($item), "bổ sung")
          || stristr(
            $item,
            "phụ lục"
          )
        ) {
          $nhom_sua_doi[] = $item;
        }
      }
      $temp = array_merge(
        $nhom_chu_ky,
        $nhom_di_chuc,
        $nhom_vong_chong,
        $nhom_thua_ke,
        $nhom_thue_muon,
        $nhom_uy_quyen,
        $nhom_the_chap,
        $nhom_tang_cho,
        $nhom_chuyen_nhuong,
        $nhom_huy,
        $nhom_sua_doi,
        $nhom_vay,
        $nhom_gop_von
      );

      $nhom_khac = array_diff($nameContract, $temp);
      if ($request->contract) {
        //dd($nhom_chuyen_nhuong);
        switch ($request->contract) {
          case 1:
            $data = $data->whereIn('suutranb.ten_hd', $nhom_chuyen_nhuong);
            break;
          case 2:
            $data = $data->whereIn('suutranb.ten_hd', $nhom_tang_cho);
            break;
          case 3:
            $data = $data->whereIn('suutranb.ten_hd', $nhom_the_chap);
            break;
          case 4:
            $data = $data->whereIn('suutranb.ten_hd', $nhom_uy_quyen);
            break;
          case 5:
            $data = $data->whereIn('suutranb.ten_hd', $nhom_thua_ke);
            break;
          case 6:
            $data = $data->whereIn('suutranb.ten_hd', $nhom_thue_muon);
            break;
          case 7:
            $data = $data->whereIn('suutranb.ten_hd', $nhom_vong_chong);
            break;
          case 8:
            $data = $data->whereIn('suutranb.ten_hd', $nhom_chu_ky);
            break;
          case 9:
            $data = $data->whereIn('suutranb.ten_hd', $nhom_vay);
            break;
          case 10:
            $data = $data->whereIn('suutranb.ten_hd', $nhom_huy);
            break;
          case 11:
            $data = $data->whereIn('suutranb.ten_hd', $nhom_sua_doi);
            break;
          case 12:
            $data = $data->whereIn('suutranb.ten_hd', $nhom_khac);
            break;
        }
      }
      //            dd($request->bank)


      //            dd($request->bank);
      /* thống kê theo công chứng viên */
      if ($request->notary) {
        $notaryKey = '"' . $request->notary . '"';

        $data->where('suutranb.ccv_master', 'like', '%' . $request->get('notary') . '%');
      }
      /* thông kế theo loại */
      if ($request->status == SuuTraModel::PREVENT) {
        $data->where('suutranb.ngan_chan', '=', SuuTraModel::PREVENT);
      }
      if ($request->status == SuuTraModel::WARNING) {
        $data->where('suutranb.ngan_chan', '=', SuuTraModel::WARNING);
      }
      if ($request->status == SuuTraModel::NORMAL && $request->status != NULL) {
        $data->where('suutranb.ngan_chan', '=', SuuTraModel::NORMAL);
      }
      if ($dateFrom != SuuTraModel::EMPTY && $dateTo == SuuTraModel::EMPTY) {
        $data = $data->whereDate('suutranb.ngay_cc', '>=', $dateFrom);
      }
      if ($dateFrom == SuuTraModel::EMPTY && $dateTo != SuuTraModel::EMPTY) {
        $data = $data->whereDate('suutranb.ngay_cc', '<=', $dateTo);
      }
      if ($dateFrom != SuuTraModel::EMPTY && $dateTo != SuuTraModel::EMPTY) {
        $data = $data->whereBetween('suutranb.ngay_cc', [$dateFrom, $dateTo]);
      }
      if ($request->bank) {
        if (in_array('all', $request->bank)) {
          $data = $data->where('suutranb.bank_id', '<>', NULL);
          $b     = Bank::orderBy('name', 'asc')->get();
          $banks = [];
          if ($data->get() != NULL) {
            foreach ($data->orderBy('bank.name', 'asc')->get()->groupBy('bank_id') as $key => $item) {
              $b = Bank::where('id', $key)->first();
              if ($b) {
                array_push($banks, $b);
              }
            }
          }
        } else {
          $data  = $data->whereIn('suutranb.bank_id', $request->bank);
          $banks = $request->bank ? Bank::orderBy('name', 'asc')->whereIn('id', $request->bank)->get() : [];
        }
      } else {
        return redirect()->route('indexReport')->with('error', 'Chưa chọn Ngân hàng!');
      }
      $data = $data->get();
      if ($request->sortExcel == 2) {
        $data = $data->sortByDesc('suutranb.ngay_cc');
      } else {
        $data = $data->sort(function ($a, $b) {
          $so_a = explode('/', $a->so_hd)[0];
          $so_a = preg_replace('/\D/', '', $so_a);
          $so_b = explode('/', $b->so_hd)[0];
          $so_b = preg_replace('/\D/', '', $so_b);
          return (int) $so_a - (int) $so_b;
        });
      }

      $user_id = Sentinel::check()->id;
      $vp_id   = NhanVienModel::find($user_id)->nv_vanphong;
      $vp      = ChiNhanhModel::find($vp_id);
      if ($banks && $banks == []) {
        return redirect()->route('indexReport')->with('error', 'Không có dữ liệu!');
      }
      return Excel::download(new exportBank($banks, $data, $vp, $dateTo, $dateFrom), 'ngan-hang.xls');
    }

    return redirect()->route('indexReport')->with('error', 'Lỗi!');
  }

  public function exportView(Request $request)
  {
    $dateFrom      = $request->dateFrom ?? Carbon::now()->format('Y-m-d');
    $dateTo        = $request->dateTo ?? Carbon::now()->format('Y-m-d');
    $user_id       = Sentinel::check()->id;
    $van_phong_id  = NhanVienModel::find($user_id)->nv_vanphong;
    $ten_van_phong = ChiNhanhModel::find($van_phong_id)->cn_ten;
    $role          = Sentinel::check()->user_roles()->first()->slug;
    $str_json      = json_encode([]);
    $array         = [
      'suutranb.*',
    ];

    $code_cn = ChiNhanhModel::find(NhanVienModel::find(Sentinel::check()->id)->nv_vanphong)->code_cn;
    if ($role == "admin" || $role == 'chuyen-vien-so') {
      if($code_cn !== "THACT"){
        $data = SuuTraModel::select($array);

      }else{

        $data = SuuTraModel::whereRaw('contains(suutranb.sync_code,' . "'" . $code_cn . "'" . ')')
        ->select($array);
      }

    } else {
      $data = SuuTraModel::whereRaw('contains(suutranb.sync_code,' . "'" . $code_cn . "'" . ')')
        ->select($array);
    }
    /* thống kê theo ngày */
    if ($dateFrom != SuuTraModel::EMPTY && $dateTo == SuuTraModel::EMPTY) {
      $data->whereDate('suutranb.ngay_cc', '>=', $dateFrom);
    }
    if ($dateFrom == SuuTraModel::EMPTY && $dateTo != SuuTraModel::EMPTY) {
      $data->whereDate('suutranb.ngay_cc', '<=', $dateTo);
    }
    if ($dateFrom != SuuTraModel::EMPTY && $dateTo != SuuTraModel::EMPTY) {
      $data->whereBetween('suutranb.ngay_cc', [$dateFrom, $dateTo]);
    }
    $data = $data->orderby('st_id', 'desc')->get();
    //            return view('admin.report.exportNotaryBook', compact('data'));
    return Excel::download(new exportNotaryView($data), 'drawdata.xls');
  }

  function initials($str)
  {
    $ret = '';
    foreach (explode(' ', $str) as $word) {
      $ret .= strtoupper($word[0]);
    }
    return $ret;
  }

  public function exportVanBan(Request $request)
  {
    $type         = $request->type;
    $nameContract = SuuTraModel::groupBy('ten_hd')->pluck('ten_hd');
    $code_cn      = ChiNhanhModel::find(NhanVienModel::find(Sentinel::check()->id)->nv_vanphong)->code_cn;
    $role         = Sentinel::check()->user_roles()->first()->slug;
    //dd($nameContract);
    $nhom_chuyen_nhuong = [];
    $thue               = [];
    foreach ($nameContract as $item) {
      if (
        stristr($item, "tặng cho") || stristr($item, "chuyển nhượng") || stristr($item, "mua")
        || stristr($item, "bán")
      ) {
        $nhom_chuyen_nhuong[] = $item;
      }
    }
    foreach ($nameContract as $item) {
      if (stristr($item, "thuê") || stristr($item, "mượn")) {
        $thue[] = $item;
      }
    }
    $timeGet = '';
    switch ($type) {
      case 'week':
        $date     = Carbon::now();
        $dateFrom = $date->startOfWeek()->format("Y-m-d");
        $dateTo   = $date->endOfWeek()->format("Y-m-d");
        break;
      case 'month':
        $month        = $request->months ? $request->months . '-01' : Carbon::now();
        $date     = Carbon::parse($month);
        $dateFrom = $date->startOfMonth()->format("Y-m-d");
        $dateTo   = $date->endOfMonth()->format("Y-m-d");
        //        $date     = Carbon::now();
        //        $dateFrom = $date->startOfMonth()->format("Y-m-d");
        //        $dateTo   = $date->endOfMonth()->format("Y-m-d");
        $timeGet  = ucfirst(Carbon::parse($dateFrom)->translatedFormat('F \nă\m Y'));
        break;
      case 'quater':
        $date     = Carbon::now();
        $dateFrom = $date->startOfQuarter()->format("Y-m-d");
        $dateTo   = $date->endOfQuarter()->format("Y-m-d");
        $timeGet  = ucfirst(Carbon::parse($dateFrom)->translatedFormat('F \nă\m Y')) . ' đến ' . Carbon::parse(
          $dateTo
        )->translatedFormat('F \nă\m Y');
        break;
      case 'year':
        $date     = Carbon::now();
        $dateFrom = $date->startOfYear()->format("Y-m-d");
        $dateTo   = $date->endOfYear()->format("Y-m-d");
        $timeGet  = ucfirst(Carbon::parse($dateFrom)->translatedFormat('\nă\m Y'));
        break;
      case  'other':
        $dateFrom = $request->dateFromExportVB ?? Carbon::now()->format('Y-m-d');
        $dateTo   = $request->dateToExportVB ?? Carbon::now()->format('Y-m-d');
        break;
    }
    $user_id      = Sentinel::check()->id;
    $van_phong_id = NhanVienModel::find($user_id)->nv_vanphong;
    $vanban       = VanBanModel::where('vb_kieuhd', '15')
      ->where('id_vp', $van_phong_id);
    $vanban->where(
      function ($vanban) {
        return $vanban->where('vb_nhan', 'like', '%nhà%')
          ->orwhere('vb_nhan', 'like', '%đất%')
          ->orwhere('vb_nhan', 'like', '%căn hộ%');
      }
    );
    $vanban = $vanban->pluck('vb_nhan');
    //        dd($dateFrom, $dateTo, $data);
    Carbon::setLocale(config('app.locale'));

    $label     = mb_strtolower(Carbon::parse($dateFrom)->translatedFormat('jS F Y')) . 'den' . mb_strtolower(
      Carbon::parse($dateTo)->translatedFormat('jS F Y')
    );
    $label     = str_replace(' ', '', SuuTraController::convert_vi_to_en($label));
    $query     = SuuTraModel::whereIn('ten_hd', $nhom_chuyen_nhuong);
    $queryThue = SuuTraModel::whereIn('ten_hd', $thue);
    if ($role != "admin" && $role != 'chuyen-vien-so') {
      $query     = $query->where('suutranb.sync_code', $code_cn);
      $queryThue = $queryThue->where('suutranb.sync_code', $code_cn);
    }
    if ($dateFrom != SuuTraModel::EMPTY && $dateTo == SuuTraModel::EMPTY) {
      $query->whereDate('ngay_cc', '>=', $dateFrom);
    }
    if ($dateFrom == SuuTraModel::EMPTY && $dateTo != SuuTraModel::EMPTY) {
      $query->whereDate('ngay_cc', '<=', $dateTo);
    }
    if ($dateFrom != SuuTraModel::EMPTY && $dateTo != SuuTraModel::EMPTY) {
      $query->whereDate('ngay_cc', '>=', $dateFrom);
      $query->whereDate('ngay_cc', '<=', $dateTo);
    }
    ///
    if ($dateFrom != SuuTraModel::EMPTY && $dateTo == SuuTraModel::EMPTY) {
      $queryThue->whereDate('ngay_cc', '>=', $dateFrom);
    }
    if ($dateFrom == SuuTraModel::EMPTY && $dateTo != SuuTraModel::EMPTY) {
      $queryThue->whereDate('ngay_cc', '<=', $dateTo);
    }
    if ($dateFrom != SuuTraModel::EMPTY && $dateTo != SuuTraModel::EMPTY) {
      $queryThue->whereDate('ngay_cc', '>=', $dateFrom);
      $queryThue->whereDate('ngay_cc', '<=', $dateTo);
    }


    $data     = $query->count();
    $dataThue = $queryThue->count();

    //dd($data);
    $export[] = (int) round($data * 40 / 100, 0);
    $export[] = (int) round($data * 12 / 100, 0);
    $export[] = (int) round($data * 13 / 100, 0);
    $export[] = (int) round($data * 10 / 100, 0);
    $export[] = (int) round($data * 6 / 100, 0);
    $export[] = (int) round($data * 7 / 100, 0);
    $export[] = (int) round($data * 5 / 100, 0);
    $export[] = (int) round($data * 3 / 100, 0);
    $export[] = (int) round($data * 4 / 100, 0);
    //
    $exportThue[] = (int) round($dataThue * 40 / 100, 0);
    $exportThue[] = (int) round($dataThue * 12 / 100, 0);
    $exportThue[] = (int) round($dataThue * 13 / 100, 0);
    $exportThue[] = (int) round($dataThue * 10 / 100, 0);
    $exportThue[] = (int) round($dataThue * 6 / 100, 0);
    $exportThue[] = (int) round($dataThue * 7 / 100, 0);
    $exportThue[] = (int) round($dataThue * 5 / 100, 0);
    $exportThue[] = (int) round($dataThue * 3 / 100, 0);
    $exportThue[] = (int) round($dataThue * 4 / 100, 0);
    $listDistrict = [
      "Quận Ninh Kiều",
      "Quận Bình Thủy",
      "Quận Cái Răng",
      "Quận Ô Môn",
      "Quận Thốt Nốt",
      "Huyện Phong Điền",
      "Huyện Thới Lai",
      "Huyện Cờ Đỏ",
      "Huyện Vĩnh Thạnh",
    ];
    $key          = [];
    $data         = $listDistrict;
    //return view('admin.report.exportBDS',compact('data'));
    foreach ($listDistrict as $item) {
      $key[] = self::initials(SuuTraController::convert_vi_to_en($item));
    }

    $from               = explode('-', $dateFrom);
    $to                 = explode('-', $dateTo);
    $total['yearFrom']  = $from[0];
    $total['monthFrom'] = $from[1];
    $total['dayFrom']   = $from[2];
    $total['yearTo']    = $to[0];
    $total['monthTo']   = $to[1];
    $total['dayTo']     = $to[2];
    $dateTo             = $timeGet;
    return Excel::download(new ExportBDS($data, $dateFrom, $dateTo, $export, $exportThue), $label . '.xls');
  }
}