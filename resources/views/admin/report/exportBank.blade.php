<html>
<table class="table-bordered" style="font-size: 14px;">
    <thead>
    <tr>
        <td colspan="4" style="text-align: left;border: 0px solid white;font-size: 11px;">{{ $vp->cn_ten }}</td>
        <td colspan="3" style="text-align: center;border: 0px solid white;font-size: 11px;">Cộng Hòa Xã Hội Chủ Nghĩa Việt Nam</td>
    </tr>
    <tr>
        <td colspan="4" style="text-align: left;border: 0px solid white;font-size: 11px;">{{ $vp->cn_diachi }}</td>
        <td colspan="3" style="text-align: center;border: 0px solid white;font-size: 11px;">Độc Lập - Tự Do - Hạnh Phúc</td>
</tr>
    <tr>
        <th colspan="7" style="border: 0px solid white"></th>
    </tr>
    <tr>
        <th colspan="7" style="text-align: center; font-size: 14px;border: 0px solid white"><b>DANH SÁCH HỢP ĐỒNG</b></th>
    </tr>
    <tr>
        <th colspan="7" style="text-align: center;border: 0px solid white;font-size: 13px;">Của ngân hàng {{ $banks->name }} tại văn phòng công chứng {{ $vp->cn_ten }}</th>
    </tr>
    <tr>
        <th colspan="7" style="text-align: center; border: 0px solid white;font-size: 11px;">Từ
            ngày @if($dateFrom) {{ \Illuminate\Support\Carbon::createFromFormat('Y-m-d', $dateFrom)->format('d/m/Y') }} @else ... @endif
            đến ngày @if($dateTo) {{ \Illuminate\Support\Carbon::createFromFormat('Y-m-d', $dateTo)->format('d/m/Y') }} @else ... @endif
        </th>
    </tr>
    <tr>
        <th colspan="7" style="border: 0px solid white"></th>
    </tr>
    <tr>
        <th style="justify-content: center;text-align: center;width: 30px">
            <b>STT</b>
        </th>
        <th style="justify-content: center;text-align: center;width: 100px ">
            <b>Số hợp đồng</b>
        </th>
        <th style="justify-content: center;text-align: center ;width: 120px">
            <b>Ngày công chứng</b>
        </th>
        <th style="justify-content: center;text-align: center ;width: 200px">
            <b>Tên hợp đồng</b>
        </th>
        <th style="justify-content: center;text-align: center ;width: 200px">
            <b>Bên liên quan</b>
        </th>
        <th style="justify-content: center;text-align: center ;width: 200px">
            <b>Tóm tắt nội dung hợp đồng</b>
        </th>
        <th style="justify-content: center;text-align: center ;width: 120px">
            <b>Công chứng viên</b>
        </th>
    </tr>
    </thead>
    @foreach($data as $item)
        <tr>
            <td style="justify-content: normal;text-align: center; vertical-align: top;width: 30px">{{ $loop->iteration }}</td>
            <td style="justify-content: normal;text-align: center;vertical-align: top;width: 100px">{{ $item->so_hd }}</td>
            <td style="justify-content: normal;text-align: center;vertical-align: top;width: 120px">{{ \Illuminate\Support\Carbon::createFromFormat('Y-m-d',$item->ngay_cc)->format('d/m/Y') }}</td>
            <td style="justify-content: normal;text-align: left;vertical-align: top;width: 200px">{{ $item->ten_hd }}</td>
            <td style="justify-content: normal;text-align: left;vertical-align: top;width: 200px">{{ $item->duong_su }}</td>
            <td style="justify-content: normal;text-align: left;vertical-align: top;width: 200px">{{ $item->texte }}</td>
            <td style="justify-content: normal;text-align: left;vertical-align: top;width: 120px">{{ $item->ccv_master }}</td>
        </tr>
    @endforeach
</table>
</html>
