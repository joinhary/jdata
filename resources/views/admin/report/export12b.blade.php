<!DOCTYPE html>
<html>
<style>
    table, th, td {
        border: 1px solid black;
    }
</style>
<table style="border:2px solid black">
    <tbody>
    <tr>
        <td colspan="4" style="font-size: 11px;vertical-align: top;font-weight: inherit !important;"><b>Biểu số:
                12b/BTP/BTTP/CC</b><br>
            <span>Ban hành theo Thông tư số 03/2019/TT-BTP ngày 20/3/2019</span><br>
            <b>Ngày nhận báo cáo (BC):</b><br>
            BC 6 tháng: Ngày 25 tháng 6 hàng năm<br>
            BC năm: Ngày 28 tháng 11 hàng năm<br>
            BC năm chính thức: Ngày 20 tháng 02 năm sau
        </td>
        <td colspan="5" style="text-align: center;font-size: 11px;vertical-align: top;font-weight: inherit !important;">
            <b>TÌNH HÌNH TỔ CHỨC VÀ HOẠT ĐỘNG CÔNG CHỨNG TRÊN ĐỊA BÀN TỈNH</b><br>
            <b>(6 tháng, năm)</b><br>
            Kỳ báo cáo:............<br>
            (Từ ngày {{ $total['dayFrom'] }},tháng {{ $total['monthFrom'] }},năm {{ $total['yearFrom'] }}<br>
            đến ngày {{ $total['dayTo'] }},tháng {{ $total['monthTo'] }},năm {{ $total['yearTo'] }})
        </td>
        <td colspan="3" style="text-align: left;font-size: 11px;vertical-align: top;font-weight: inherit !important;">
            <b>Đơn vị báo cáo:</b><br>
            - /......<br>
            <b>Đơn vị nhận báo cáo:</b><br>
            - Sở Tư pháp<br>
        </td>
    </tr>
    <tr>
        <td rowspan="4" style="text-align: center;font-size: 11px;vertical-align: center">
        </td>
        <td rowspan="4" style="text-align: center;font-size: 11px;vertical-align: center">
            <b>Số công chứng viên</b> (người)
        </td>
        <td colspan="5" style="text-align: center;font-size: 11px;vertical-align: center"><b>Công chứng</b></td>
        <td colspan="4" style="text-align: center;font-size: 11px;vertical-align: center"><b>Chứng thực</b></td>
        <td rowspan="4" style="text-align: center;font-size: 11px;vertical-align: center">
            <b>Tổng số tiền nộp vào ngân sách/thuế</b> (đồng)
        </td>
    </tr>
    <tr>
        <td colspan="3" style="text-align: center;font-size: 11px;vertical-align: center">
            Số việc công chứng (việc)
        </td>
        <td rowspan="3" style="text-align: center;font-size: 11px;vertical-align: center">
            Tổng số thù lao công chứng (đồng)
        </td>
        <td rowspan="3" style="text-align: center;font-size: 11px;vertical-align: center">
            Tổng số phí công chứng (đồng)
        </td>
        <td colspan="2" rowspan="2" style="text-align: center;font-size: 11px;vertical-align: center">
            Chứng thực bản sao
        </td>
        <td colspan="2" rowspan="2" style="text-align: center;font-size: 11px;vertical-align: center">
            Chứng thực chữ ký trong giấy tờ, văn bản
        </td>
    </tr>
    <tr>
        <td rowspan="2" style="text-align: center;font-size: 11px;vertical-align: center">
            Tổng số
        </td>
        <td colspan="2" style="text-align: center;font-size: 11px;vertical-align: center">
            Chia ra
        </td>
    </tr>
    <tr>
        <td style="text-align: center;font-size: 11px;vertical-align: center">
            Công chứng hợp đồng, giao dịch
        </td>
        <td style="text-align: center;font-size: 11px;vertical-align: center">
            Công chứng bản dịch và các loại việc khác
        </td>
        <td style="text-align: center;font-size: 11px;vertical-align: center">
            Số bản sao (bản sao)
        </td>
        <td style="text-align: center;font-size: 11px;vertical-align: center">
            Phí chứng thực bản sao (đồng)
        </td>
        <td style="text-align: center;font-size: 11px;vertical-align: center">
            Số việc (việc)
        </td>
        <td style="text-align: center;font-size: 11px;vertical-align: center">
            Phí chứng thực chữ ký (đồng)
        </td>
    </tr>
    <tr>
        <td style="text-align: center;font-size: 11px;vertical-align: center">A</td>
        <td style="text-align: center;font-size: 11px;vertical-align: center">(1)</td>
        <td style="text-align: center;font-size: 11px;vertical-align: center">(2)</td>
        <td style="text-align: center;font-size: 11px;vertical-align: center">(3)</td>
        <td style="text-align: center;font-size: 11px;vertical-align: center">(4)</td>
        <td style="text-align: center;font-size: 11px;vertical-align: center">(5)</td>
        <td style="text-align: center;font-size: 11px;vertical-align: center">(6)</td>
        <td style="text-align: center;font-size: 11px;vertical-align: center">(7)</td>
        <td style="text-align: center;font-size: 11px;vertical-align: center">(8)</td>
        <td style="text-align: center;font-size: 11px;vertical-align: center">(9)</td>
        <td style="text-align: center;font-size: 11px;vertical-align: center">(10)</td>
        <td style="text-align: center;font-size: 11px;vertical-align: center">(11)</td>
    </tr>
    <tr>
        <td style="text-align: center;font-size: 11px;vertical-align: center"><b>Tổng số:</b></td>
        <td style="text-align: center;font-size: 11px;vertical-align: center">{{ $total['sumEmployee']}}</td>
        <td style="text-align: center;font-size: 11px;vertical-align: center">{{ $total['totals']}}</td>
        <td style="text-align: center;font-size: 11px;vertical-align: center">{{$total['sumDeal']}}</td>
        <td style="text-align: center;font-size: 11px;vertical-align: center">0</td>
        <td style="text-align: center;font-size: 11px;vertical-align: center">{{$total['sumRemuneration']}}</td>
        <td style="text-align: center;font-size: 11px;vertical-align: center">{{$total['costDeal']}}</td>
        <td style="text-align: center;font-size: 11px;vertical-align: center">0</td>
        <td style="text-align: center;font-size: 11px;vertical-align: center">0</td>
        <td style="text-align: center;font-size: 11px;vertical-align: center">{{$total['sumAuth']}}</td>
        <td style="text-align: center;font-size: 11px;vertical-align: center">{{$total['costAuth']}}</td>
        <td style="text-align: center;font-size: 11px;vertical-align: center">{{$total['costAuth'] + $total['costDeal'] + $total['sumRemuneration']}}</td>
    </tr>
    @foreach($data as $item)
        <tr>
            @php
				$nameOffice = "";
				$code_cn=$item->sync_code;
                $Office = \App\Models\ChiNhanhModel::where('chinhanh.code_cn',$code_cn)->first();
                if ($Office == false){
                    $nameOffice = "";
                }else{
                    $nameOffice = $Office->cn_ten;
                }
                if(!$Office)
                {
$sumEmployee=0;
                }else{
                    $sumEmployee = \App\Models\NhanVienModel::where('nv_vanphong', $Office->cn_id)->count();

                }

				$key="'".'"'."chữ ký".'"'."'";
                if($dateFrom !== '' && $dateTo !== ''){
                $sumAuth = \App\Models\SuuTraModel::whereRaw('contains(suutranb.sync_code,' ."'". $code_cn."'".')')
                    ->whereDate('ngay_cc', '>=', $dateFrom)
                    ->whereDate('ngay_cc', '<=', $dateTo)
					->whereRaw('contains(suutranb.ten_hd,' . $key.')')
                   ->count();
                $totals = \App\Models\SuuTraModel::whereRaw('contains(suutranb.sync_code,' ."'". $code_cn."'".')')
                        ->whereDate('ngay_cc', '>=', $dateFrom)
                        ->whereDate('ngay_cc', '<=', $dateTo)
                        ->count() - $sumAuth;
						
                $costAuth = \App\Models\SuuTraModel::whereRaw('contains(suutranb.sync_code,' ."'". $code_cn."'".')')
                    ->whereDate('ngay_cc', '>=', $dateFrom)
                    ->whereDate('ngay_cc', '<=', $dateTo)
					->whereRaw('contains(suutranb.ten_hd,' . $key.')')->sum('phi_cong_chung');
                $costDeal = \App\Models\SuuTraModel::whereRaw('contains(suutranb.sync_code,' ."'". $code_cn."'".')')
                        ->whereDate('ngay_cc', '>=', $dateFrom)
                        ->whereDate('ngay_cc', '<=', $dateTo)
                        ->sum('phi_cong_chung') - $costAuth;
                $sumRemuneration = \App\Models\SuuTraModel::whereRaw('contains(suutranb.sync_code,' ."'". $code_cn."'".')')
                    ->whereDate('ngay_cc', '>=', $dateFrom)
                    ->whereDate('ngay_cc', '<=', $dateTo)
                    ->sum('thu_lao');
                }else{
                    $sumAuth = \App\Models\SuuTraModel::whereRaw('contains(suutranb.sync_code,' ."'". $code_cn."'".')')
                        ->whereRaw('contains(suutranb.ten_hd,' . $key.')')
                        ->count();
                    $totals = \App\Models\SuuTraModel::whereRaw('contains(suutranb.sync_code,' ."'". $code_cn."'".')')
                        ->count() - $sumAuth;
                    $costAuth = \App\Models\SuuTraModel::whereRaw('contains(suutranb.sync_code,' ."'". $code_cn."'".')')
                        ->sum('phi_cong_chung');
                    $costDeal = \App\Models\SuuTraModel::whereRaw('contains(suutranb.sync_code,' ."'". $code_cn."'".')')
                        ->sum('phi_cong_chung') - $costAuth;
                    $sumRemuneration = \App\Models\SuuTraModel::whereRaw('contains(suutranb.sync_code,' ."'". $code_cn."'".')')
                        ->sum('thu_lao');
                }
            @endphp
            <td style="text-align: center;font-size: 11px;vertical-align: center"><b>{{ $nameOffice }}:</b></td>
            <td style="text-align: center;font-size: 11px;vertical-align: center">{{ $sumEmployee}}</td>
            <td style="text-align: center;font-size: 11px;vertical-align: center">{{ $totals }}</td>
            <td style="text-align: center;font-size: 11px;vertical-align: center">{{$totals}}</td>
            <td style="text-align: center;font-size: 11px;vertical-align: center">0</td>
            <td style="text-align: center;font-size: 11px;vertical-align: center">{{$sumRemuneration}}</td>
            <td style="text-align: center;font-size: 11px;vertical-align: center">{{$costDeal}}</td>
            <td style="text-align: center;font-size: 11px;vertical-align: center">0</td>
            <td style="text-align: center;font-size: 11px;vertical-align: center">0</td>
            <td style="text-align: center;font-size: 11px;vertical-align: center">{{$sumAuth}}</td>
            <td style="text-align: center;font-size: 11px;vertical-align: center">{{$costAuth}}</td>
            <td style="text-align: center;font-size: 11px;vertical-align: center">{{$costAuth + $costDeal + $sumRemuneration}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
<tr>
    <td colspan="12">+ Số liệu trong báo cáo này được tổng hợp từ báo cáo của …../…. tổ chức hành nghề công chứng đăng
        ký hoạt động tại Sở Tư pháp.
    </td>
</tr>
<tr>
    <td colspan="12">+ Theo quản lý của Sở Tư pháp, số công chứng viên đăng ký hành nghề là......……công chứng viên.</td>
</tr>
<tr>

</tr>
<tr>
    <td colspan="2" style="text-align: center;font-size: 11px;vertical-align: center"><b>Người lập biểu</b></td>
    <td></td>
    <td></td>
    <td colspan="3" style="text-align: center;font-size: 11px;vertical-align: center"><b>Người kiểm tra</b></td>
    <td></td>
    <td colspan="4" style="text-align: center;font-size: 11px;vertical-align: center">
        ... ,ngày ... ... ,tháng ... ... ,năm ... ....
    </td>
</tr>
<tr>
    <td colspan="2" style="text-align: center;font-size: 11px;vertical-align: center">(Ký, ghi rõ họ, tên, chức vụ)</td>
    <td></td>
    <td></td>
    <td colspan="3" style="text-align: center;font-size: 11px;vertical-align: center">(Ký,ghi rõ họ, tên)</td>
    <td></td>
    <td colspan="4" style="text-align: center;font-size: 11px;vertical-align: center">
        <b>GIÁM ĐỐC</b>
    </td>
</tr>
<tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="4" style="text-align: center;font-size: 11px;vertical-align: center">
        (Ký,đóng dấu,ghi rõ họ, tên)
    </td>
</tr>
</html>
