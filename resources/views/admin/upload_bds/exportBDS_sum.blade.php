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
            <b>Tháng {{$month}}/20..</b>
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
        <tr>
            <td style="text-align: center;width: 50px">
                1
            </td>
            <td style="text-align: left;">
                Quận Ninh kiều
            </td>
            <td style="text-align: center;">
              {{$data['detail'][0]['Ninh Kiều'] ?? 0}}
            </td>
            <td style="text-align: center;">
                {{$data['detail'][1]['Ninh Kiều'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][2]['Ninh Kiều'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][3]['Ninh Kiều'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][4]['Ninh Kiều'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][5]['Ninh Kiều'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][6]['Ninh Kiều'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][7]['Ninh Kiều'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][8]['Ninh Kiều'] ?? 0}}

            </td>
        </tr>
        <tr>
            <td style="text-align: center;width: 50px">
                2
            </td>
            <td style="text-align: left;">
                Quận Bình Thủy
            </td>
            <td style="text-align: center;">
              {{$data['detail'][0]['Bình Thủy'] ?? 0}}
            </td>
            <td style="text-align: center;">
                {{$data['detail'][1]['Bình Thủy'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][2]['Bình Thủy'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][3]['Bình Thủy'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][4]['Bình Thủy'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][5]['Bình Thủy'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][6]['Bình Thủy'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][7]['Bình Thủy'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][8]['Bình Thủy'] ?? 0}}

            </td>
        </tr>
        <tr>
            <td style="text-align: center;width: 50px">
                3
            </td>
            <td style="text-align: left;">
                Quận Cái Răng
            </td>
            <td style="text-align: center;">
              {{$data['detail'][0]['Cái Răng'] ?? 0}}
            </td>
            <td style="text-align: center;">
                {{$data['detail'][1]['Cái Răng'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][2]['Cái Răng'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][3]['Cái Răng'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][4]['Cái Răng'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][5]['Cái Răng'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][6]['Cái Răng'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][7]['Cái Răng'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][8]['Cái Răng'] ?? 0}}
            </td>
        </tr>
        <tr>
            <td style="text-align: center;width: 50px">
                4
            </td>
            <td style="text-align: left;">
                Quận Ô Môn
            </td>
            <td style="text-align: center;">
              {{$data['detail'][0]['Ô Môn'] ?? 0}}
            </td>
            <td style="text-align: center;">
                {{$data['detail'][1]['Ô Môn'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][2]['Ô Môn'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][3]['Ô Môn'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][4]['Ô Môn'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][5]['Ô Môn'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][6]['Ô Môn'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][7]['Ô Môn'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][8]['Ô Môn'] ?? 0}}
            </td>
        </tr>
        <tr>
            <td style="text-align: center;width: 50px">
                5
            </td>
            <td style="text-align: left;">
                Quận Thốt Nốt
            </td>
            <td style="text-align: center;">
              {{$data['detail'][0]['Thốt Nốt'] ?? 0}}
            </td>
            <td style="text-align: center;">
                {{$data['detail'][1]['Thốt Nốt'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][2]['Thốt Nốt'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][3]['Thốt Nốt'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][4]['Thốt Nốt'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][5]['Thốt Nốt'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][6]['Thốt Nốt'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][7]['Thốt Nốt'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][8]['Thốt Nốt'] ?? 0}}
            </td>
        </tr>
        <tr>
            <td style="text-align: center;width: 50px">
                6
            </td>
            <td style="text-align: left;">
                Quận Phong Điền
            </td>
            <td style="text-align: center;">
              {{$data['detail'][0]['Phong Điền'] ?? 0}}
            </td>
            <td style="text-align: center;">
                {{$data['detail'][1]['Phong Điền'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][2]['Phong Điền'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][3]['Phong Điền'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][4]['Phong Điền'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][5]['Phong Điền'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][6]['Phong Điền'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][7]['Phong Điền'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][8]['Phong Điền'] ?? 0}}
            </td>
        </tr>
        <tr>
            <td style="text-align: center;width: 50px">
                7
            </td>
            <td style="text-align: left;">
                Quận Thới Lai
            </td>
            <td style="text-align: center;">
              {{$data['detail'][0]['Thới Lai'] ?? 0}}
            </td>
            <td style="text-align: center;">
                {{$data['detail'][1]['Thới Lai'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][2]['Thới Lai'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][3]['Thới Lai'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][4]['Thới Lai'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][5]['Thới Lai'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][6]['Thới Lai'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][7]['Thới Lai'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][8]['Thới Lai'] ?? 0}}
            </td>
        </tr>
        <tr>
            <td style="text-align: center;width: 50px">
                8
            </td>
            <td style="text-align: left;">
                Quận Cờ Đỏ
            </td>
            <td style="text-align: center;">
              {{$data['detail'][0]['Cờ Đỏ'] ?? 0}}
            </td>
            <td style="text-align: center;">
                {{$data['detail'][1]['Cờ Đỏ'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][2]['Cờ Đỏ'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][3]['Cờ Đỏ'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][4]['Cờ Đỏ'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][5]['Cờ Đỏ'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][6]['Cờ Đỏ'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][7]['Cờ Đỏ'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][8]['Cờ Đỏ'] ?? 0}}
            </td>
        </tr>
        <tr>
            <td style="text-align: center;width: 50px">
                9
            </td>
            <td style="text-align: left;">
                Quận Vinh Thạnh
            </td>
            <td style="text-align: center;">
              {{$data['detail'][0]['Vĩnh Thạnh'] ?? 0}}
            </td>
            <td style="text-align: center;">
                {{$data['detail'][1]['Vĩnh Thạnh'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][2]['Vĩnh Thạnh'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][3]['Vĩnh Thạnh'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][4]['Vĩnh Thạnh'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][5]['Vĩnh Thạnh'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][6]['Vĩnh Thạnh'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][7]['Vĩnh Thạnh'] ?? 0}}

            </td>
            <td style="text-align: center;">
                {{$data['detail'][8]['Vĩnh Thạnh'] ?? 0}}
            </td>
        </tr>
    <tr>
        <td colspan="2" style="text-align: center">
            <b>Tổng cộng</b>
        </td>
        <td style="text-align: center;">
            {{$data['sum']['Phát triển theo dự án(đất nền)']}}
        </td>
        <td style="text-align: center;">
            {{$data['sum']['Trong khu dân cư hiện hữu(đất nền)']}}
        </td>
        <td style="text-align: center;">
            {{$data['sum']['Phát triển theo dự án(nhà ở)']}}
        </td>
        <td style="text-align: center;">
            {{$data['sum']['Trong khu dân cư hiện hữu(nhà ở)']}}
        </td>
        <td style="text-align: center;">
            {{$data['sum']['Diện tích <= 70m2(căn hộ)']}}
        </td>
        <td style="text-align: center;">
            {{$data['sum']['Diện tích 70m2 < Diện tích <= 120m2(căn hộ)']}}
        </td>
        <td style="text-align: center;">
            {{$data['sum']['Diện tích > 120m2(căn hộ)']}}
        </td>
        <td style="text-align: center;">
            {{$data['sum']['Văn phòng cho thuê (m2)']}}
        </td>
        <td style="text-align: center;">
            {{$data['sum']['Mặt bằng thương mại, dịch vụ (m2)']}}
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
