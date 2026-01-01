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
        <th colspan="3" style="text-align: center;font-size: 11px;vertical-align: top">Biểu số: 12a/BTP/BTTP/CC<br>
            Ban hành theo Thông tư số 03/2019/TT-BTP ngày 20/3/2019<br>
            Ngày nhận báo cáo (BC):<br>
            BC 6 tháng: Ngày 06 tháng 6 hàng năm<br>
            BC năm: Ngày 07 tháng 11 hàng năm<br>
            BC năm chính thức: Ngày 20 tháng 01 năm sau
        </th>
        <th colspan="5" style="text-align: center;font-size: 11px;vertical-align: top">TÌNH HÌNH TỔ CHỨC VÀ HOẠT ĐỘNG
            CÔNG CHỨNG<br>
            (6 tháng, năm)<br>
            Kỳ báo cáo:............<br>
            (Từ ngày {{$data['D_tungay']}},tháng {{ $data['M_tungay'] }},năm {{ $data['Y_tungay'] }}<br>
            đến ngày {{$data['D_denngay']}},tháng {{$data['M_denngay']}},năm {{$data['Y_denngay']}})
        </th>
        <th colspan="3" style="text-align: left;font-size: 11px;vertical-align: top">
            Đơn vị báo cáo:<br>
            - {{$data['ten_van_phong']}}<br>
            Đơn vị nhận báo cáo:<br>
            - Sở Tư pháp<br>
        </th>
    </tr>
    <tr>
        <td rowspan="4" style="text-align: center;font-size: 11px;vertical-align: center">Số công chứng viên
            (người)
        </td>
        <td colspan="5" style="text-align: center;font-size: 11px;vertical-align: center">Công chứng</td>

        <td colspan="4" style="text-align: center;font-size: 11px;vertical-align: center">Chứng thực</td>


        <td rowspan="4" style="text-align: center;font-size: 11px;vertical-align: center">Tổng số tiền nộp vào ngân
            sách/thuế
            (đồng)
        </td>


    </tr>
    <tr>
        <td colspan="3" style="text-align: center;font-size: 11px;vertical-align: center">Số việc công chứng
            (việc)
        </td>


        <td rowspan="3" style="text-align: center;font-size: 11px;vertical-align: center">Tổng số thù lao công chứng
            (đồng)


        </td>
        <td rowspan="3" style="text-align: center;font-size: 11px;vertical-align: center">Tổng số phí công chứng
            (đồng)
        </td>

        <td colspan="2" rowspan="2" style="text-align: center;font-size: 11px;vertical-align: center">Chứng thực bản sao

        </td>

        <td colspan="2" rowspan="2" style="text-align: center;font-size: 11px;vertical-align: center">Chứng thực chữ ký
            trong giấy tờ, văn bản
        </td>


    </tr>
    <tr>
        <td rowspan="2" style="text-align: center;font-size: 11px;vertical-align: center">Tổng số
        </td>
        <td colspan="2" style="text-align: center;font-size: 11px;vertical-align: center">Chia ra
        </td>

    </tr>
    <tr>

        <td style="text-align: center;font-size: 11px;vertical-align: center">Công chứng hợp đồng, giao dịch
        </td>

        <td style="text-align: center;font-size: 11px;vertical-align: center">Công chứng bản dịch và các loại việc khác
        </td>


        <td style="text-align: center;font-size: 11px;vertical-align: center">Số bản sao
            (bản sao)
        </td>

        <td style="text-align: center;font-size: 11px;vertical-align: center">Phí chứng thực bản sao
            (đồng)
        </td>
        <td style="text-align: center;font-size: 11px;vertical-align: center">Số việc
            (việc)
        </td>

        <td style="text-align: center;font-size: 11px;vertical-align: center">Phí
            chứng thực chữ ký
            (đồng)
        </td>


    </tr>
    <tr>
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
        <td style="text-align: center;font-size: 11px;vertical-align: center">{{ $data['so_luong_nhan_vien']}}</td>
        <td style="text-align: center;font-size: 11px;vertical-align: center">{{ $data['tong_so']}}</td>

        <td style="text-align: center;font-size: 11px;vertical-align: center">{{$data['so_hop_dong_giao_dich']}}</td>

        <td style="text-align: center;font-size: 11px;vertical-align: center">0</td>

        <td style="text-align: center;font-size: 11px;vertical-align: center">{{$data['thu_lao_hop_dong_giao_dich']}}</td>
        <td style="text-align: center;font-size: 11px;vertical-align: center">{{$data['phi_hop_dong_giao_dich']}}</td>

        <td style="text-align: center;font-size: 11px;vertical-align: center">0</td>

        <td style="text-align: center;font-size: 11px;vertical-align: center">0</td>
        <td style="text-align: center;font-size: 11px;vertical-align: center">{{$data['so_chung_thuc_chu_ky']}}</td>

        <td style="text-align: center;font-size: 11px;vertical-align: center">{{$data['phi_chung_thuc_chu_ky']}}</td>

        <td style="text-align: center;font-size: 11px;vertical-align: center">{{$data['phi_chung_thuc_chu_ky']+$data['phi_hop_dong_giao_dich']+$data['thu_lao_hop_dong_giao_dich']}}</td>


    </tr>
    </tbody>
</table>
<tr>

</tr>
<tr>
    <td colspan="2" style="text-align: center;font-size: 11px;vertical-align: center"><b>Người lập biểu</b></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="3" style="text-align: center;font-size: 11px;vertical-align: center">
        ... ,ngày ... ... ,tháng ... ... ,năm ... ....
    </td>
</tr>
<tr>
    <td colspan="2" style="text-align: center;font-size: 11px;vertical-align: center">(Ký,ghi rõ họ, tên)</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="5" style="text-align: center;font-size: 11px;vertical-align: center">
        <b>NGƯỜI ĐẠI DIỆN THEO PHÁP LUẬT</b>
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
    <td colspan="3" style="text-align: center;font-size: 11px;vertical-align: center">
        (Ký,đóng dấu,ghi rõ họ, tên)
    </td>
</tr>
</html>
