<html>
<table class="table-bordered">
    <thead>
    <tr>
        <th colspan="11">Đơn vị báo cáo</th>
    </tr>
    <tr>
        <th colspan="11">Địa chỉ</th>
    </tr>
    <tr>
        <th colspan="11">Nơi nhận báo cáo: SỞ TƯ PHÁP TP.CẦN THƠ</th>
    </tr>
    <tr></tr>
    <tr>
        <th colspan="11" style="text-align: center;font-size: 18px;vertical-align: center">
            <b>BÁO CÁO VỀ LƯỢNG GIAO DỊCH BẤT ĐỘNG SẢN</b>
        </th>
    </tr>
    <tr>
        <th colspan="11" style="text-align: center;font-size: 14px;vertical-align: center">
            <b>{{$dateTo}}</b>
        </th>
    </tr>
    <tr></tr>
    <tr>
        <th rowspan="3" style="width:30%;font-weight:bold;text-align: center;justify-content: center;font-size: 12px;vertical-align: center;">
            STT
        </th>
        <th rowspan="3" style="width:30%;font-weight:bold;text-align: center;justify-content: center;font-size: 12px;vertical-align: center;width: 150px">
            Địa điểm bất động sản
        </th>
        <th colspan="9" style="width:30%;font-weight:bold;text-align: center;justify-content: center;font-size: 12px;vertical-align: center;height: 30px">
            Số lượng giao dịch BĐS để bạn được tổng hợp từ số liệu công chứng/chứng thực, đấu giá tài sản trong tháng báo cáo
        </th>
    </tr>
    <tr>
        <th colspan="2" style="width:20%;font-weight:bold;text-align: center;justify-content: center;font-size: 12px;vertical-align: center;">
            Đất nền để ở (lô)
        </th>
        <th colspan="2" style="width:20%;font-weight:bold;text-align: center;justify-content: center;font-size: 12px;vertical-align: center;">
            Nhà ở riêng lẻ (căn)
        </th>
        <th colspan="3" style="width:30%;font-weight:bold;text-align: center;justify-content: center;font-size: 12px;vertical-align: center;">
            Căn hộ chung cư (căn)
        </th>
        <th rowspan="2" style="width:20%;font-weight:bold;text-align: center;justify-content: center;font-size: 12px;vertical-align: center;width: 100px">
            Văn phòng cho thuê (m2)
        </th>
        <th rowspan="2" style="width:20%;font-weight:bold;text-align: center;justify-content: center;font-size: 12px;vertical-align: center;width: 100px">
            Mặt bằng thương mại, dịch vụ (m2)
        </th>
    </tr>
    <tr>
        <th style="text-align: center;vertical-align: center; justify-content: center;width: 100px"><b>Phát triển theo dự án</b></th>
        <th style="text-align: center;vertical-align: center; justify-content: center;width: 100px"><b>Trong khu dân cư hiện hữu</b></th>
        <th style="text-align: center;vertical-align: center; justify-content: center;width: 100px"><b>Phát triển theo dự án</b></th>
        <th style="text-align: center;vertical-align: center; justify-content: center;width: 100px"><b>Trong khu dân cư hiện hữu</b></th>
        <th style="text-align: center;vertical-align: center; justify-content: center;width: 100px"><b>Diện tích &lt;= 70m2</b></th>
        <th style="text-align: center;vertical-align: center; justify-content: center;width: 100px"><b>70m2 &lt; Diện tích &lt;= 120m2</b></th>
        <th style="text-align: center;vertical-align: center; justify-content: center;width: 100px"><b>Diện tích &gt; 120m2</b></th>
    </tr>
    </thead>
    <tr>
        <td style="text-align: center;vertical-align: center; justify-content: center">(1)</td>
        <td style="text-align: center;vertical-align: center; justify-content: center">(2)</td>
        <td style="text-align: center;vertical-align: center; justify-content: center">(3)</td>
        <td style="text-align: center;vertical-align: center; justify-content: center">(4)</td>
        <td style="text-align: center;vertical-align: center; justify-content: center">(5)</td>
        <td style="text-align: center;vertical-align: center; justify-content: center">(6)</td>
        <td style="text-align: center;vertical-align: center; justify-content: center">(7)</td>
        <td style="text-align: center;vertical-align: center; justify-content: center">(8)</td>
        <td style="text-align: center;vertical-align: center; justify-content: center">(9)</td>
        <td style="text-align: center;vertical-align: center; justify-content: center">(10)</td>
        <td style="text-align: center;vertical-align: center; justify-content: center">(11)</td>
    </tr>
    @php
        $sum_1 = 0;
        $sum_2 = 0;
        $sum_3 = 0;
        $sum_4 = 0;
        $sum_5 = 0;
        $sum_6 = 0;
        $sum_7 = 0;
        $sum_8 = 0;
        $sum_9 = 0;
    @endphp
    @foreach($data as $key => $val)
        <tr>
            <td style="text-align: center;width: 50px">
                {{ $loop->iteration }}
            </td>
            <td style="text-align: left;">
                {{ $val }}
            </td>
            <td style="text-align: center;">
            0
            </td>
            <td style="text-align: center;">
              0
            </td>
            <td style="text-align: center;">
               0
            </td>
            <td style="text-align: center;">
             0
            </td>
            <td style="text-align: center;">
              0
            </td>
            <td style="text-align: center;">
              0
            </td>
            <td style="text-align: center;">
               0
            </td>
            <td style="text-align: center;">
               0
            </td>
            <td style="text-align: center;">
             0
            </td>
        </tr>
    @endforeach
    <tr>
        <td colspan="2" style="text-align: center">
            <b>Tổng cộng</b>
        </td>
        <td style="text-align: center;">
            {{ $sum_1 }}
        </td>
        <td style="text-align: center;">
            {{ $sum_2 }}
        </td>
        <td style="text-align: center;">
            {{ $sum_3 }}
        </td>
        <td style="text-align: center;">
            {{ $sum_4 }}
        </td>
        <td style="text-align: center;">
            {{ $sum_5}}
        </td>
        <td style="text-align: center;">
            {{ $sum_6 }}
        </td>
        <td style="text-align: center;">
            {{ $sum_7 }}
        </td>
        <td style="text-align: center;">
            {{ $sum_8 }}
        </td>
        <td style="text-align: center;">
            {{ $sum_9 }}
        </td>
    </tr>
</table>
<table>
    <tr></tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td colspan="3" style="text-align: center">
            Ngày.....tháng.....năm....
        </td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="2" style="text-align: center">
            NGƯỜI LẬP BIỂU
        </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td colspan="3" style="text-align: center">
            TRƯỞNG VĂN PHÒNG
        </td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="2" style="text-align: center;font-style: italic">
            (Ký, ghi rõ họ và tên)
        </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td colspan="3" style="text-align: center;font-style: italic">
            (Ký tên, đóng dấu)
        </td>
        <td></td>
    </tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr>
        <td></td>
        <td colspan="10">
            Email:
        </td>
    </tr>
</table>
</html>
