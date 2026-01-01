<html>
<table class="table-bordered">
    <thead>
    <tr>
        <th style="font-weight:bold;text-align: center;vertical-align: center">
            STT
        </th>
        <th style="font-weight:bold;text-align: center;font-size: 13px;vertical-align: center">
            Số công chứng
        </th>
        <th style="font-weight:bold;text-align: center;font-size: 13px;vertical-align: center">
            Ngày công chứng
        </th>
        <th style="font-weight:bold;text-align: center;font-size: 13px;vertical-align: center">
            Họ tên, CMND/Hộ chiếu/Căn cước công dân, nơi cư trú của người yêu cầu công chứng
        </th>
        <th style="font-weight:bold;text-align: center;font-size: 13px;vertical-align: center">
            Tên hợp đồng
        </th>
        <th style="font-weight:bold;text-align: center;font-size: 13px;vertical-align: center">
            Tóm tắt nội dung
        </th>
        <th style="font-weight:bold;text-align: center;font-size: 13px;vertical-align: center">
            Công chứng viên
        </th>
        <th style="font-weight:bold;text-align: center;font-size: 13px;vertical-align: center">
            Phí công chứng
        </th>
        <th style="font-weight:bold;text-align: center;font-size: 13px;vertical-align: center">
            Thù lao
        </th>
        <th style="font-weight:bold;text-align: center;font-size: 13px;vertical-align: center">
            Ghi chú
        </th>
    </tr>
    </thead>
    <tbody>{{$i=1}}
    @foreach($data as $val)
        <tr>
            <td style="text-align: center;vertical-align: center">{{$i++}}</td>
            <td style="text-align: center;vertical-align: center">{{ $val->so_hd }}</td>
            <th style="text-align: center;vertical-align: center">{{ $val->ngay_cc ? \Illuminate\Support\Carbon::createFromFormat('Y-m-d',$val->ngay_cc)->format('d/m/Y') : '' }}</th>
            <td style="text-align: justify;vertical-align: top">{{$val->duong_su}}</td>
            <td style="text-align: center;vertical-align: center">{{$val->ten_hd}}</td>
            <td style="text-align: justify;vertical-align: top">{{$val->texte}}</td>
            <td style="text-align: center;vertical-align: center">{{$val->ccv_master}}</td>
            <td style="text-align: center;vertical-align: center">{{ $val->phi_cong_chung }}</td>
            <td style="text-align: center;vertical-align: center">{{ $val->thu_lao }}</td>
            <td style="text-align: center;vertical-align: center"></td>
        </tr>
    @endforeach
    </tbody>
</table>
</html>
