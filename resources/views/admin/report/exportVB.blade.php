<html>
<table class="table-bordered">
    <thead>
    <tr>
        <th colspan="4" style="text-align: center;font-size: 16px;vertical-align: center">
            <b>Cộng Hòa Xã Hội Chủ Nghĩa Việt Nam</b>
        </th>
    </tr>
    <tr>
        <th colspan="4" style="text-align: center;font-size: 14px;vertical-align: center">
            <b>Độc Lập - Tự Do - Hạnh Phúc</b>
        </th>
    </tr>
    <tr>
        <th colspan="4" style="text-align: center;font-size: 14px;vertical-align: center">
            <b>***************</b>
        </th>
    </tr>
    <tr>
        <th colspan="4" style="text-align: center;font-size: 16px;vertical-align: center">
            <b>Báo cáo</b>
        </th>
    </tr>
    <tr></tr>
    <tr>
        <th style="font-weight:bold;text-align: center;font-size: 13px;vertical-align: center">
            STT
        </th>
        <th style="font-weight:bold;text-align: center;font-size: 13px;vertical-align: center">
            Loại
        </th>
        <th style="font-weight:bold;text-align: center;font-size: 13px;vertical-align: center">
            Tổng hồ sơ
        </th>
        <th style="font-weight:bold;text-align: center;font-size: 13px;vertical-align: center">
            Tổng tiền (phí công chứng)
        </th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $key => $val)
        <tr>
            <td style="text-align: center;vertical-align: center">{{ $loop->iteration }}</td>
            <th style="text-align: center;vertical-align: center">{{ $val->ten_hd }}</th>
            <th style="text-align: center;vertical-align: center">{{ $val->total }}</th>
            <td style="text-align: center;vertical-align: top">{{$val->phi_cong_chung}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</html>
