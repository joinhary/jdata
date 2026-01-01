<html>
<table class="table-bordered">
    <thead>
    <tr>
        <th colspan="4"></th>
        <th colspan="5" style="text-align: center;font-size: 11px;vertical-align: center">
            TP-CC-27 <br>
            (Ban hành kèm theo Thông tư số 01/2021/TT-BTP)
        </th>
    </tr>
    <tr>
        <th colspan="9" style="text-align: center;font-size: 14px;vertical-align: center">
            <b>Cộng Hòa Xã Hội Chủ Nghĩa Việt Nam</b>
        </th>
    </tr>
    <tr>
        <th colspan="9" style="text-align: center;font-size: 11px;vertical-align: center">
            <b>Độc Lập - Tự Do - Hạnh Phúc</b>
        </th>
    </tr>
    <tr></tr>
    <tr>
        <th colspan="9" style="text-align: center;font-size: 14px;vertical-align: center">
            <b>SỔ CÔNG CHỨNG HỢP ĐỒNG, GIAO DỊCH</b>
        </th>
    </tr>
    <tr>
        <th colspan="9" style="text-align: center;font-size: 11px;vertical-align: center">
            Tên tổ chức hành nghề công chứng:.................................<br>
            Tỉnh (thành phố):............................................................
        </th>
    </tr>
    <tr>
        <th colspan="9" style="font-size: 11px;text-align: center;vertical-align: center">
            Quyển số: …………………….. TP/CC-SCC/HĐGD<br>
            Mở Sổ ngày …… tháng …… năm ………………..<br>
            Khóa Sổ ngày …….. tháng ……. Năm …………...
        </th>
    </tr>
    <tr></tr>
    <tr>
       
        <th style="font-weight:bold;text-align: center;font-size: 13px;vertical-align: center">
           SỐ CÔNG CHỨNG
        </th>
        <th style="font-weight:bold;text-align: center;font-size: 13px;vertical-align: center">
            NGÀY, THÁNG, NĂM CÔNG CHỨNG
        </th>
        <th style="font-weight:bold;text-align: center;font-size: 13px;vertical-align: center">
            HỌ TÊN, CMND/HỘ CHIẾU/CĂN CƯỚC CÔNG DÂN, NƠI CƯ TRÚ CỦA NGƯỜI YÊU CẦU CÔNG CHỨNG
        </th>
        <th style="font-weight:bold;text-align: center;font-size: 13px;vertical-align: center">
            LOẠI HỢP ĐỒNG, GIAO DỊCH
        </th>
       
        <th style="font-weight:bold;text-align: center;font-size: 13px;vertical-align: center">
           HỌ TÊN CÔNG CHỨNG VIÊN KÝ VĂN BẢN CÔNG CHỨNG
        </th>
        <th style="font-weight:bold;text-align: center;font-size: 13px;vertical-align: center">
            PHÍ CÔNG CHỨNG
        </th>
		<th style="font-weight:bold;text-align: center;font-size: 13px;vertical-align: center">
THÙ LAO CÔNG  CHỨNG, CHI PHÍ KHÁC
        </th>
		 <th style="font-weight:bold;text-align: center;font-size: 13px;vertical-align: center">
TÀI SẢN LÀ ĐỐI TƯỢNG CỦA HỢP ĐỒNG, GIAO DỊCH
(nếu có)
        </th>

        <th style="font-weight:bold;text-align: center;font-size: 13px;vertical-align: center">
            Ghi chú
        </th>
    </tr>
    </thead>
    <tbody>{{$i=1}}
	<tr> <td style="text-align: center;vertical-align: center">(1)</td>
        <td style="text-align: center;vertical-align: center">(2)</td>
            <th style="text-align: center;vertical-align: center">(3)</th>
            <td style="text-align: justify;vertical-align: top">(4)</td>
            <td style="text-align: center;vertical-align: center">(5)</td>
            <td style="text-align: justify;vertical-align: top">(6)</td>
            <td style="text-align: center;vertical-align: center">(7)</td>
            <td style="text-align: center;vertical-align: center">(8)</td>
            <td style="text-align: center;vertical-align: center">(9)</td></tr>
    @foreach($data as $val)
    <tr>
        <td style="text-align: center;vertical-align: center">{{ $val->so_hd }}</td>
        <th style="text-align: center;vertical-align: center">{{ $val->ngay_cc ? \Illuminate\Support\Carbon::createFromFormat('Y-m-d',$val->ngay_cc)->format('d/m/Y') : '' }}</th>
        <td style="text-align: justify;vertical-align: top">{{$val->duong_su}}</td>
        <td style="text-align: center;vertical-align: center">{{$val->ten_hd}}</td>
        <td style="text-align: center;vertical-align: center">{{$val->ccv_master}}</td>
        <td style="text-align: center;vertical-align: center">{{ $val->phi_cong_chung }}</td>
        <td style="text-align: center;vertical-align: center">{{ $val->thu_lao }}</td>
        <td style="text-align: justify;vertical-align: top">{{$val->texte}} <p style="color:red">{{$val->contract_period}} </p> <p style="color:red">{{$val->cancel_description}}</p></td>

        <td style="text-align: center;vertical-align: center"></td>
    </tr>
    @endforeach
    </tbody>
    <tr></tr>
    <tr>
        <th colspan="9">
            HƯỚNG DẪN SỬ DỤNG:
        </th>
    </tr>
    <tr>
        <th colspan="9">
            1 - Chữ viết trong sổ phải rõ ràng, không tẩy xóa, phải viết cùng một thứ mực tốt, màu đen.
        </th>
    </tr>
    <tr>
        <th colspan="9">
            2 - Trước khi vào Sổ phải kiểm tra các dữ liệu sẽ ghi vào Sổ để tránh nhầm lẫn. Trường hợp viết nhầm, sửa
            lỗi kỹ thuật phải gạch đi viết lại, không được viết đè lên chữ cũ; khi viết lại phải ghi vào cột ghi chú
            những nội dung
        </th>
    </tr>
    <tr>
        <th colspan="9">
            sửa; họ và tên, chữ ký của người đã sửa và ngày, tháng, năm sửa và đóng dấu của tổ chức hành
            nghề công chứng vào chỗ sửa.
        </th>
    </tr>
    <tr>
        <th colspan="9">
            3 - Phải ghi đầy đủ các cột mục có trong Sổ và lưu ý:
        </th>
    </tr>
    <tr>
        <th colspan="9">
            Cột (1): Số công chứng trong cột này là số ghi trong lời chứng của công chứng viên; mỗi một yêu cầu công
            chứng phải ghi một số, không phụ thuộc vào số lượng văn bản công chứng của yêu cầu công chứng đó.
        </th>
    </tr>
    <tr>
        <th colspan="9">
            4 - Khi sử dụng phải ghi ngày mở Sổ, khi kết thúc phải ghi ngày khóa Sổ.
        </th>
    </tr>
    <tr>
        <th colspan="9">
            5 - Sổ phải được giữ sạch, không được để nhòe hoặc rách nát và phải được bảo quản chặt chẽ, lưu trữ lâu dài
            tại tổ chức hành nghề công chứng.
        </th>
    </tr>
    <tr></tr>
    <tr>
        <th colspan="4">Tổng số: {{$i-1    }} hợp đồng</th>
        <th></th>
        <th></th>
        <th colspan="3" style="text-align: center">
            Ngày {{ \Carbon\Carbon::now()->day}}
            tháng {{ \Carbon\Carbon::now()->month}}
            năm {{ \Carbon\Carbon::now()->year}}
        </th>
    </tr>
    <tr>
        <th colspan="4" style="text-align: left"></th>
        <th></th>
        <th></th>
        <th colspan="3" style="text-align: center"><b>Người báo cáo</b></th>
    </tr>
</table>
</html>
