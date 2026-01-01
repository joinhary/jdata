<html>
<table class="table-bordered" style="border: 1px solid black">
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
                <b>BÁO CÁO VĂN PHÒNG KHÔNG NỘP BÁO CÁO BDS tháng {{ $month }}</b>
            </th>
        </tr>
        <tr>
            <th style="text-align: center;vertical-align: center; font-weight: bold">STT</th>
            <th style="text-align: center;vertical-align: center; width: 200px; font-weight: bold">Tên ngân hàng</th>
            <th style="text-align: center;vertical-align: center; width: 300px; font-weight: bold">Đại diện</th>
            <th style="text-align: center;vertical-align: center; width: 500px; font-weight: bold">Địa chỉ</th>

        </tr>
    </thead>
    <tbody>
        @foreach ($data as $key => $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td style="text-align: center;vertical-align: center; width: 200px">{{ $item->cn_ten }}</td>
                <td style="text-align: center;vertical-align: center; width: 300px">{{ $item->cn_ndd }}</td>
                <td style="text-align: center;vertical-align: center; width: 500px">{{ $item->cn_diachi }}</td>

            </tr>
        @endforeach
    </tbody>
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
