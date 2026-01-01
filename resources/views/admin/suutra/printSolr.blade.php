
<!DOCTYPE html>

<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
        }
    </style>
</head>

<body>
    <div id="print">
        <h2 style="text-align: center; font-family: DejaVu Sans, sans-serif;charset: utf-8;"><b>KẾT QUẢ TRA CỨU</b></h2>
        <ul style="font-size: 18px; font-family: DejaVu Sans, sans-serif;">
            <li>
                <b style="color: black;font-family: DejaVu Sans, sans-serif">Thời gian:</b>
                {{ \Carbon\Carbon::now()->format('H:i:s d/m/Y') }}
            </li>
            <li>
                <span> <b style="color: black;font-family: DejaVu Sans, sans-serif">Từ khóa tìm kiếm :</b>
                    {{ $search }}</span>
            </li>
            <li>
                <span> <b style="color: black;font-family: DejaVu Sans, sans-serif">Người tạo :</b>
                    {{ $user_name }} - {{ $vpcc_name }}. </span>
            </li>
        </ul>
        <h3 style="color: black;font-family: DejaVu Sans, sans-serif">
            <b style="color: black;font-family: DejaVu Sans, sans-serif">1. Danh sách thông tin giao dịch</b>
            (Tổng số: <span
                style="color: black;font-family: DejaVu Sans, sans-serif">{{ \App\Models\SuuTraModel::count() }}</span> ,
            Có <span style="color: black; font-family: DejaVu Sans, sans-serif;">{{ $count }}</span> kết quả được
            tìm thấy.)
        </h3>
        <table style="font-size: 12px!important;border-collapse: collapse;border: 0.5px solid black;">
            <thead>
                <tr class="text-center">
                    <th
                        style="width: 8%;border: 0.5px solid black;border-collapse: collapse; font-family: DejaVu Sans, sans-serif;">
                        Ngày nhập<br> hệ thống</th>
                    <th
                        style="width: 8%;border: 0.5px solid black;border-collapse: collapse;font-family: DejaVu Sans, sans-serif;">
                        Ngày CC/<br>ngăn chặn</th>
                    <th
                        style="width: 45%;border: 0.5px solid black;border-collapse: collapse;font-family: DejaVu Sans, sans-serif;">
                        Các bên liên quan</th>
                    <th
                        style="width: 45%;border: 0.5px solid black;border-collapse: collapse;font-family: DejaVu Sans, sans-serif;">
                        Nội dung tóm tắt/<br> công văn</th>
                    <th
                        style=" width: 20px !important;border: 0.5px solid black;border-collapse: collapse;font-family: DejaVu Sans, sans-serif;">
                        Số HD<br> CV NC</th>
                    <th
                        style="width: 8%;border: 0.5px solid black;border-collapse: collapse;font-family: DejaVu Sans, sans-serif;">
                        Tên HD/<br> CV</th>
                    <th
                        style="width: 5%;border: 0.5px solid black;border-collapse: collapse;font-family: DejaVu Sans, sans-serif;">
                        Công chứng viên/<br> Người nhập</th>
                    <th
                        style="width: 5%;border: 0.5px solid black;border-collapse: collapse;font-family: DejaVu Sans, sans-serif;">
                        Văn Phòng</th>

                </tr>
            </thead>
            <tbody>
                @foreach ($item as $value)
                    @php
                        $duong_su = str_replace(['Bên B', 'bên b', 'BÊN b', 'BÊN B', 'bên B'], '<b>Bên B</b>', $value['duong_su'][0] ?? '');
                        $duong_su = str_replace(['Bên nhận chuyển nhượng', 'BÊN NHẬN CHUYỂN NHƯỢNG'], '</br><b>Bên nhận chuyển nhượng</b>', $duong_su);
                        $duong_su = str_replace(['Bên chuyển nhượng', 'BÊN CHUYỂN NHƯỢNG'], '<b>Bên chuyển nhượng</b>', $duong_su);
                        $duong_su = str_replace(['Bên thế chấp', 'BÊN THẾ CHẤP'], '</br><b>Bên thế chấp</b>', $duong_su);
                        
                        $duong_su = str_replace(['Bên đặt cọc', 'BÊN ĐẶT CỌC'], '<b>Bên đặt cọc</b>', $duong_su);
                        $duong_su = str_replace(['Bên nhận đặt cọc', 'BÊN NHẬN ĐẶT CỌC'], '</br><b>Bên nhận đặt cọc</b>', $duong_su);
                        $duong_su = str_replace(['Bên tặng cho', 'BÊN TẶNG CHO'], '<b>Bên tặng cho</b>', $duong_su);
                        $duong_su = str_replace(['Bên được tặng cho', 'BÊN ĐƯỢC TẶNG CHO'], '</br><b>Bên được tặng cho</b>', $duong_su);
                        
                        $duong_su = str_replace(['Bên cho thuê', 'BÊN CHO THUÊ'], '<b>Bên cho thuê</b>', $duong_su);
                        $duong_su = str_replace(['Bên thuê', 'BÊN THUÊ'], '</br><b>Bên thuê</b>', $duong_su);
                        
                        $duong_su = str_replace(['BÊN ỦY QUYỀN', 'Bên uỷ quyền', 'BÊN ỦY QUYỀN', 'BÊN UỶ QUYỀN', 'Bên ủy quyền'], '<b>Bên ủy quyền</b>', $duong_su);
                        $duong_su = str_replace(['BÊN ĐƯỢC ỦY QUYỀN', 'Bên được uỷ quyền', 'BÊN ĐƯỢC ỦY QUYỀN', 'BÊN ĐƯỢC UỶ QUYỀN', 'Bên được ủy quyền'], '</br><b>Bên được ủy quyền</b>', $duong_su);
                        $duong_su = str_replace(['NGƯỜI ỦY QUYỀN', 'Người uỷ quyền', 'NGƯỜI ỦY QUYỀN', 'NGƯỜI UỶ QUYỀN', 'Người ủy quyền'], '<b>Người ủy quyền</b>', $duong_su);
                        $duong_su = str_replace(['NGƯỜI ĐƯỢC ỦY QUYỀN', 'NGƯỜI được uỷ quyền', 'NGƯỜI ĐƯỢC ỦY QUYỀN', 'NGƯỜI ĐƯỢC UỶ QUYỀN', 'Người được ủy quyền'], '</br><b>Người được ủy quyền</b>', $duong_su);
                        $duong_su = str_replace(['Bên nhận thế chấp', 'BÊN NHẬN THẾ CHẤP'], '</br><b>Bên nhận thế chấp</b>', $duong_su);
                        $duong_su = str_replace(['Bên A', 'bên a', 'BÊN A', ' Bên A', 'BÊN a', 'bên A'], '<b>Bên A</b>', $duong_su);
                        $duong_su = str_replace(';', '<br>', $duong_su);
                        $duong_su = trim($duong_su, '"');
                        $duong_su_cut = mb_substr($value['duong_su'][0] ?? '', 0, 350, 'UTF-8');
                        $duong_su_cut = str_replace(['Bên A', 'bên a', 'BÊN A', 'BÊN A', 'bên A'], '<b>Bên A</b>', $duong_su_cut);
                        $duong_su_cut = str_replace(['Bên B', 'bên b', 'BÊN b', 'BÊN B', 'bên B'], '<b>Bên B</b>', $duong_su_cut);
                        
                        $duong_su_cut = str_replace(['Bên nhận chuyển nhượng', 'BÊN NHẬN CHUYỂN NHƯỢNG'], '</br><b>Bên nhận chuyển nhượng</b>', $duong_su_cut);
                        $duong_su_cut = str_replace(['Bên chuyển nhượng', 'BÊN CHUYỂN NHƯỢNG'], '<b>Bên chuyển nhượng</b>', $duong_su_cut);
                        $duong_su_cut = str_replace(['BÊN ỦY QUYỀN', 'Bên uỷ quyền', 'BÊN ỦY QUYỀN', 'BÊN UỶ QUYỀN', 'Bên ủy quyền'], '<b>Bên ủy quyền</b>', $duong_su_cut);
                        $duong_su_cut = str_replace(['BÊN ĐƯỢC ỦY QUYỀN', 'Bên được uỷ quyền', 'BÊN ĐƯỢC ỦY QUYỀN', 'BÊN ĐƯỢC UỶ QUYỀN', 'Bên được ủy quyền'], '</br><b>Bên được ủy quyền</b>', $duong_su_cut);
                        $duong_su_cut = str_replace(['Bên thế chấp', 'BÊN THẾ CHẤP'], '</br><b>Bên thế chấp</b>', $duong_su_cut);
                        $duong_su_cut = str_replace(['Bên tặng cho', 'BÊN TẶNG CHO'], '<b>Bên tặng cho</b>', $duong_su_cut);
                        $duong_su_cut = str_replace(['Bên được tặng cho', 'BÊN ĐƯỢC TẶNG CHO'], '</br><b> Bên được tặng cho</b>', $duong_su_cut);
                        $duong_su_cut = str_replace(['NGƯỜI ỦY QUYỀN', 'Người uỷ quyền', 'NGƯỜI ỦY QUYỀN', 'NGƯỜI UỶ QUYỀN', 'Người ủy quyền'], '<b>Người ủy quyền</b>', $duong_su_cut);
                        $duong_su_cut = str_replace(['NGƯỜI ĐƯỢC ỦY QUYỀN', 'NGƯỜI được uỷ quyền', 'NGƯỜI ĐƯỢC ỦY QUYỀN', 'NGƯỜI ĐƯỢC UỶ QUYỀN', 'Người được ủy quyền'], '</br><b>Người được ủy quyền</b>', $duong_su_cut);
                        $duong_su_cut = str_replace(['Bên đặt cọc', 'BÊN ĐẶT CỌC'], '<b>Bên đặt cọc</b>', $duong_su_cut);
                        $duong_su_cut = str_replace(['Bên nhận đặt cọc', 'BÊN NHẬN ĐẶT CỌC'], '</br><b>Bên nhận đặt cọc</b>', $duong_su_cut);
                        
                        $duong_su_cut = str_replace(['Bên cho thuê', 'BÊN CHO THUÊ'], '<b>Bên cho thuê</b>', $duong_su_cut);
                        $duong_su_cut = str_replace(['Bên thuê', 'BÊN THUÊ'], '</br><b>Bên thuê</b>', $duong_su_cut);
                        
                        $duong_su_cut = str_replace(['Bên nhận thế chấp', 'BÊN NHẬN THẾ CHẤP'], '</br><b>Bên nhận thế chấp</b>', $duong_su_cut);
                        $texte = str_replace(';', '<br>', $value['texte'][0] ?? '');
                        $texte = str_replace('-', '<br>-', $texte ?? '');
                        $texte = str_replace('+', '<br>+', $texte ?? '');
                        $texte = str_replace('1/.', '<br>1/.', $texte ?? '');
                        $texte = str_replace('2/.', '<br>2/.', $texte ?? '');
                        $texte = str_replace('3/.', '<br>3/.', $texte ?? '');
                        $texte = trim($texte, '"');
                        
                    @endphp
                    <tr>
                        @if (($value['ma_phan_biet'][0] ??'')== 'D')
                        <td style="text-align: justify;border: 0.5px solid black;border-collapse: collapse;font-family: DejaVu Sans, sans-serif;">
                            <b>{{ \Carbon\Carbon::parse($value['created_at'][0])->format('H:i:s d/m/Y') ?? '' }}</b>
                        </td>
                    @else
                    <td>
                                       
                        @if ($value['st_id'] > 1000
                        &&
                        \Carbon\Carbon::parse($value['created_at'][0])->format('H')  >= 0
                        && \Carbon\Carbon::parse($value['created_at'][0])->format('H')  <= 5
                        )
                        <b>{{ \Carbon\Carbon::parse($value['created_at'][0])->addHour(7)->format('H:i:s d/m/Y') ?? '' }}</b>
                        @else
                        <b> {{-- {{ \Carbon\Carbon::parse($value['created_at'][0])->format('H:i:s d/m/Y') ?? '' }} 090124--}} 
                            @if(($value['ma_phan_biet'][0] ??'')== 'U')
                            {{ \Carbon\Carbon::parse($value['ngay_nhap'][0])->format('d/m/Y') }}
                            @else
                            {{ \Carbon\Carbon::parse($value['created_at'][0])->format('H:i:s d/m/Y') }}
                            @endif</b>
                        @endif  
                    </td>
                    @endif
                        <td
                            style="text-align: justify;border: 0.5px solid black;border-collapse: collapse;font-family: DejaVu Sans, sans-serif;">
                            <b>
{{ optional(\Carbon\Carbon::parse($value['ngay_cc'][0] ?? null))->format('d/m/Y') }}
</b>
                        </td>

                        <td
                            style="text-align: justify;border: 0.5px solid black;border-collapse: collapse;font-family: DejaVu Sans, sans-serif;">
                            <div style="margin:0.5em">{!! $duong_su !!}</div>
                        </td>
                        <td
                            style="text-align: justify;border: 0.5px solid black;border-collapse: collapse;font-family: DejaVu Sans, sans-serif;">
                            <div style="margin:0.5em">{!! $texte !!}</div><br>
                            <span
                                style="color: black;font-family: DejaVu Sans, sans-serif;">{{ $value['cancel_description'][0] ?? '' }}</span>
                            <br>
                            @if ($value['contract_period'][0] ?? '' != null)
                                <b style="color: black;font-family: DejaVu Sans, sans-serif">Thời hạn: </b> <span
                                    style="color: black;font-family: DejaVu Sans, sans-serif;">{{ $value['contract_period'][0] ?? '' }}</span>
                            @else
                            @endif
                            @if ($value['deleted_note'][0] ?? '' != null)
                                <br>
                                <b> <span
                                        style="color: red;font-family: DejaVu Sans, sans-serif;">{{ $value['deleted_note'][0] ?? '' }}</span>

                                </b>
                            @endif
                        </td>
                        @if ($value['undisputed_date'])
                            <td
                                style="text-align: center;border: 0.5px solid black;border-collapse: collapse; width: 20px !important;font-family: DejaVu Sans, sans-serif;">
                                <b>{{ $value['so_hd'][0] ?? '' }}</b> </br> {{ $value['ten_hd'][0] ?? '' }}</br> <b
                                    style="color:#e74040"> Ngày giải chấp:
                                    {{ \Illuminate\Support\Carbon::parse($value['undisputed_date'][0])->format('d/m/Y') }}</b>
                                </br> <b> {{ $value['undisputed_note'][0] ?? '' }}</b>
                            </td>
                        @else
                            <td
                                style="text-align: center;border: 0.5px solid black;border-collapse: collapse; width: 20px !important;font-family: DejaVu Sans, sans-serif;">
                                <b>{{ $value['so_hd'][0] ?? '' }}</b> </br> {{ $value['ten_hd'][0] ?? '' }}</br>
                            </td>
                        @endif
                        <td
                            style="text-align: center;border: 0.5px solid black;border-collapse: collapse;font-family: DejaVu Sans, sans-serif;">
                            {{ $value['ten_hd'][0] ?? '' }}<br>

                            @if ($value['ngan_chan'][0] ?? '' == 3)
                                Bị chặn
                            @elseif($value['ngan_chan'][0] ?? '' == 1)
                                Cảnh báo
                            @endif
                            <br>
                            @if ($value['release_doc_number'][0] ?? '')
                                Đã được giải toả theo CV: <b>{{ $value['release_doc_number'][0] ?? '' }}</b>
                            @endif
                            @if ($value['release_doc_date'][0] ?? '')
                                |Ngày giải toả : {{ $value['release_doc_date'][0] ?? '' }}
                            @endif
                            
                        </td>
                        <td
                            style="text-align: center;border: 0.5px solid black;border-collapse: collapse;font-family: DejaVu Sans, sans-serif;">
                            {{ $value['ccv_master'][0] ?? '' }}</td>
                        <td
                            style="text-align: center;border: 0.5px solid black;border-collapse: collapse;font-family: DejaVu Sans, sans-serif;">
                            {{ $value['vp_master'][0] ?? '' }}</td>


                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <script src="js/jsPDF/dist/jspdf.umd.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.5/jspdf.min.js"></script>

    <script>
        window.print();
        window.onafterprint = function(event) {
            //get content of div print
            var source = window.document.getElementById("print");
            //ajax get to route saveHistoryPdf
            $.ajax({
                url: "{{ route('saveHistoryPdf') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    html: source.innerHTML,
                    ipaddress: "{{ $ipaddress }}"

                },
                success: function(data) {
                    console.log(data);
                }
            });
        };
    </script>
</body>

</html>
