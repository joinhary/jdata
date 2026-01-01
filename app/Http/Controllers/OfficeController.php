<?php

namespace App\Http\Controllers;

use App\Models\ChiNhanhModel;
use App\Models\DistrictModel;
use App\Http\Requests\ChiNhanhRequest;
use App\Models\NhanVienModel;
use App\Models\ProvinceModel;
use App\Models\VillageModel;
use App\Models\WardModel;
use Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Foundation\Validation\ValidatesRequests;
class OfficeController extends Controller
{
    /**
     * using Chinhanh.php file
     * using users.php file
     * using Nhanvien.php file
     */
    use users;
    use Chinhanh;
    use Nhanvien;
    use ValidatesRequests;
    public function index(Request $request)
    {
        $where = [];
        $where[] = ['cn_trangthai', '=', 1];
        if ($request->cn_ten) {
            $where[] = ['cn_ten', 'LIKE', '%' . $request->cn_ten . '%'];
        }
        $tong = Count(
            ChiNhanhModel::select('chinhanh.code_cn', 'cn_id', 'cn_ten', 'cn_diachi', 'province.name as cn_tinh', 'district.name as cn_quan', 'ward.name as cn_phuong', 'village.name as cn_ap', 'lat', 'lng')
                ->join('province', 'provinceid', '=', 'cn_tinh')
                ->join('district', 'districtid', '=', 'cn_quan')
                ->join('ward', 'wardid', '=', 'cn_phuong')
                ->join('village', 'villageid', '=', 'cn_ap')
                ->get()
        );

        $count = Count($this->listChinhanh($request)->get());
        $chinhanh = $this->listChinhanh($request)
->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.chinhanh.index', compact('chinhanh', 'count', 'tong'));
    }

    public function create()
    {
        $request = new Request();
        $tinhthanh = ProvinceModel::orderBy('name', 'asc')->pluck('name', 'provinceid');
        $tinhthanh->prepend('---Chọn tỉnh/thành phố---', '');
        $quanhuyen = ['' => '---Chọn quận/huyện---'];
        $phuongxa = ['' => '---Chọn phường/xã---'];
        $ap = ['' => '---Chọn ấp/khu vực---'];
        return view('admin.chinhanh.create', compact('tinhthanh', 'quanhuyen', 'phuongxa', 'ap'));
    }

    public function store(ChiNhanhRequest $request)
    {
        $cn_ten = $request->cn_ten;
        $code_cn = $request->code_cn;
        $cn_sdt = $request->cn_sdt;
        $cn_code_uchi = $request->cn_code_uchi;
        $cn_tenvp_uchi = $request->cn_tenvp_uchi;
        $cn_ndd = $request->cn_ndd;
        $cn_tinh = $request->cn_tinh;
        $cn_quan = $request->cn_quan;
        $cn_phuong = $request->cn_phuong;
        $cn_ap = $request->cn_ap;
        $cn_diachi = $request->cn_diachi;
        if ($request->get('lat') == null && $request->get('lng') == null) {
            $lat = "1";
            $lng = "1";
        } else {
            $lat = $request->lat;
            $lng = $request->lng;
        }
        $cn_trangthai = 1;
        ChiNhanhModel::create([
            'cn_ten' => $cn_ten,
            'cn_sdt' => $cn_sdt,
            'cn_ndd' => $cn_ndd,
            'cn_tinh' => $cn_tinh,
            'cn_quan' => $cn_quan,
            'cn_phuong' => $cn_phuong,
            'cn_ap' => $cn_ap,
            'cn_diachi' => $cn_diachi,
            'lat' => $lat,
            'lng' => $lng,
            'cn_trangthai' => $cn_trangthai,
            'code_cn' => $code_cn,
            'login_code'=>123456

        ]);
        /*Ghi log*/
        $user_exec = Sentinel::getUser()->id;
        $description = "Tạo chi nhánh " . $cn_ten;
        $this->api_create_log($user_exec, $description);
        return Redirect::route('indexChiNhanh')->with('success', 'Tạo văn phòng thành công!');
    }

    public function show($id)
    {
        $chinhanh = ChiNhanhModel::select('*', 'province.name as cn_tentinh', 'district.name as cn_tenquan', 'ward.name as cn_tenphuong', 'village.name as cn_tenap')
            ->join('province', 'provinceid', '=', 'cn_tinh')
            ->join('district', 'districtid', '=', 'cn_quan')
            ->join('ward', 'wardid', '=', 'cn_phuong')
            ->join('village', 'villageid', '=', 'cn_ap')
            ->where('cn_id', $id)
            ->first();
        return view('admin.chinhanh.detail', compact('chinhanh'));
    }

    public function edit($id)
    {
        $request = new Request();
        $nhanvien = $this->listNhanVien($request)->pluck('nv_hoten', 'nv_id');
        $chinhanh = ChiNhanhModel::find($id);
        $tinhthanh = ProvinceModel::orderBy('name', 'asc')->pluck('name', 'provinceid');
        $quanhuyen = DistrictModel::where('provinceid', $chinhanh->cn_tinh)->orderBy('name', 'asc')->pluck('name', 'districtid');
        $phuongxa = WardModel::where('districtid', $chinhanh->cn_quan)->orderBy('name', 'asc')->pluck('name', 'wardid');
        $ap = VillageModel::where('wardid', $chinhanh->cn_phuong)->orderBy('name', 'asc')->pluck('name', 'villageid');
        return view('admin.chinhanh.edit', compact('chinhanh', 'tinhthanh', 'quanhuyen', 'phuongxa', 'ap', 'nhanvien'));
    }

    public function update(Request $request, $id)
    {

        $cn_ten = $request->cn_ten;
        $code_cn = $request->code_cn;
        $cn_sdt = $request->cn_sdt;
        $cn_code_uchi = $request->cn_code_uchi;
        $cn_tenvp_uchi = $request->cn_tenvp_uchi;
        $cn_ndd = $request->cn_ndd;
        $cn_tinh = $request->cn_tinh;
        $cn_quan = $request->cn_quan;
        $cn_phuong = $request->cn_phuong;
        $cn_ap = $request->cn_ap;
        $cn_diachi = $request->cn_diachi;
        $this->validate($request, [
            'cn_ten' => 'required',
            'code_cn' => 'required',
            'cn_sdt' => 'required',
            'cn_diachi' => 'required',
            'cn_tinh' => 'required',
            'cn_quan' => 'required',
            'cn_phuong' => 'required',
            'cn_ap' => 'required',
        ], [
            'cn_ten.required' => 'Vui lòng nhập tên văn phòng!',
            'code_cn.required' => 'Vui lòng nhập mã văn phòng!',
            'cn_sdt.required' => 'Vui lòng nhập số điện thoại văn phòng!',
            'cn_diachi.required' => 'Vui lòng nhập địa chỉ văn phòng!',
            'cn_tinh.required' => 'Vui lòng chọn tỉnh/thành phố!',
            'cn_quan.required' => 'Vui lòng chọn quận/huyện!',
            'cn_phuong.required' => 'Vui lòng chọn phường/xã!',
            'cn_ap.required' => 'Vui lòng chọn ấp/khu vực!',
        ]);
        if ($request->get('lat') == null && $request->get('lng') == null) {
            $lat = "1";
            $lng = "1";
        } else {
            $lat = $request->lat;
            $lng = $request->lng;
        }
        $cn_trangthai = 1;
        ChiNhanhModel::where('cn_id', $id)
            ->update([
                'cn_ten' => $cn_ten,
                'cn_sdt' => $cn_sdt,
                'cn_code_uchi' => $cn_code_uchi,
                'cn_tenvp_uchi' => $cn_tenvp_uchi,
                'cn_ndd' => $cn_ndd,
                'cn_tinh' => $cn_tinh,
                'cn_quan' => $cn_quan,
                'cn_phuong' => $cn_phuong,
                'cn_ap' => $cn_ap,
                'cn_diachi' => $cn_diachi,
                'lat' => $lat,
                'lng' => $lng,
                'cn_trangthai' => $cn_trangthai,
                'code_cn' => $code_cn,
				'login_code'=>$request->login_code
            ]);
        /*Ghi log*/
        $user_exec = Sentinel::getUser()->id;
        $description = "Cập nhật thông tin chi nhánh " . $cn_ten;
        $this->api_create_log($user_exec, $description);
        if(Sentinel::check()->isTruongVP()){
            return Redirect::route('admin')->with('success', 'Cập nhật văn phòng thành công!');

        }else{
            return Redirect::route('indexChiNhanh')->with('success', 'Cập nhật văn phòng thành công!');

        }
    }

    public function destroy($id)
    {
        $chinhanh = ChiNhanhModel::find($id);
		$chinhanh->update([
		'status'=>1,
		'login_code'=>uniqid()
		]);
       // if(NhanVienModel::where('nv_vanphong','=',$id)->first()){
      //      return Redirect::route('indexChiNhanh')->with('error', 'Văn phòng đã có dữ liệu không thể xóa!');
      //  }
        //$chinhanh->delete();
        /*Ghi log*/
        $user_exec = Sentinel::getUser()->id;
        $description = "Ẩn văn phòng " . $chinhanh->cn_ten;
        $this->api_create_log($user_exec, $description);
        return Redirect::route('indexChiNhanh')->with('success', 'Đã ẩn văn phòng thành công!');
    }
	public function restore($id)
    {
        $chinhanh = ChiNhanhModel::find($id);
		$chinhanh->update([
		'status'=>null,
		'login_code'=>123456
		]);
       // if(NhanVienModel::where('nv_vanphong','=',$id)->first()){
      //      return Redirect::route('indexChiNhanh')->with('error', 'Văn phòng đã có dữ liệu không thể xóa!');
      //  }
        //$chinhanh->delete();
        /*Ghi log*/
        $user_exec = Sentinel::getUser()->id;
        $description = "Hiện văn phòng " . $chinhanh->cn_ten;
        $this->api_create_log($user_exec, $description);
        return Redirect::route('indexChiNhanh')->with('success', 'Đã Hiện văn phòng thành công!');
    }
    public function getOfficeCode(){
        $user_exec = Sentinel::getUser()->id;
        $nhanvien=NhanVienModel::find($user_exec);
        $chinhanh=ChiNhanhModel::find($nhanvien->nv_vanphong);
        return view('admin.office.detail',compact('chinhanh'));
    }
    public function setOfficeCode(Request $request){
        $user_exec = Sentinel::getUser()->id;
        $nhanvien=NhanVienModel::find($user_exec);
        $chinhanh=ChiNhanhModel::find($nhanvien->nv_vanphong)->update([
            'login_code'=>$request->login_code
        ]);
        return redirect(route("getLoginCode"))->with("success","Cài đặt thành công");
    }
}
