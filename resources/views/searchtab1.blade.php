<div class="tab-pane active" id="tab1">
    <div class="row">
        @include('modalTbc')
        <form action="{{ route('searchSolr') }}" id="formSreachbasic">
            <input hidden name='type' value='basic'>
            <div class="row">
                <div class="col-md-2 mt-2">
                    <input type="text" class="form-control" id="duong_su" name="duong_su" onchange="hide_inputAll1()"
                        value="{{ isset($option['duong_su']) ? $option['duong_su'] : '' }}"
                        placeholder="Tìm kiếm chủ thể" autofocus
                        style="
                       autofocus;
                       box-sizing: border-box;">
                </div>
                <div class="col-md-2 mt-2">
                    <input type="text" class="form-control" id="tai_san" name="tai_san" onchange="hide_inputAll1()"
                        value="{{ isset($option['tai_san']) ? $option['tai_san'] : '' }}"
                        placeholder="Tìm kiếm theo tài sản" autofocus
                        style="
                       
                       box-sizing: border-box;">
                </div>
                
                <div class="col-md-1 mt-2">
                    <input type="text" class="form-control" id="so_hd1" name="so_hd"
                        onchange="hide_inputAll1()"
                        value="{{ isset($option['so_hd']) ? $option['so_hd'] : '' }}"
                        placeholder="Số HĐ" autofocus
                        style="
                               
                               box-sizing: border-box;">
                </div>
                <div class="col-md-2 mt-2">
                    <input type="text" class="form-control" id="tat_ca" name="tat_ca" onchange="hide_input()"
                        value="{{ isset($option['tat_ca']) ? $option['tat_ca'] : '' }}" placeholder="Tìm kiếm tất cả"
                        autofocus
                        style="
                       
                       box-sizing: border-box;">
                </div>
            
        </form>
        <div class="col-md-4 mt-2">
            <button class="btn btn-info" id="search2" type="submit" onclick="highlight('a')">
                <i class="fa fa-search"></i>
                Tìm kiếm
            </button>
            <button id="btnclear" type="button" class="btn btn-danger" onclick="clearValue()"> <i
                    class="fa fa-trash"></i> Xóa</button>
            <button id="btnprint" type="button" class="btn btn-warning" onclick="btnprint_click()"> <i
                    class="fa fa-print"></i> In </button>

        </div>
        @if (isset($data))
            @if (isset($_GET['name']))
                <h2>Kết quả tìm kiếm cho từ khóa: <b>{{ $search = $_GET['name'] }}</b></h2>
            @endif


            @if ($data == [])
                <script>
                    alert("Không tìm thấy kết quả nào phù hợp");
                </script>
            @endif
            @if (isset($Tbc) && $Tbc !== [])
                <div class="col-md-6 mt-2">
                    <a> <i class="fa fa-warning"></i>
                        <b>Chú ý:</b> <span style="color : red; font-weight: bold; font-size:20px;">Có
                            {{ $Tbc['total'] }} thông báo từ kết
                            quả này.
                            <button type="button" class="btn btn-danger" onclick="$('#myModalTbc').modal('show');">Bấm
                                vào xem ngay</button>
                        </span>
                    </a>
                </div>
            @else
                <div class="col-md-6">
                    <a> <b>Gợi ý:</b> Nguyễn Văn B để tìm <b>hoặc</b> (kèm năm sinh hoặc CMND). </a>
                </div>
            @endif

            <div class="col-md-12 mt-2">
                <!-- Le Cam Lanh-->
                @if ($option == [])
                @elseif (
    (!isset($option['duong_su']) || $option['duong_su'] == null) &&
    (!isset($option['tai_san']) || $option['tai_san'] == null) &&
    (!isset($option['tat_ca']) || $option['tat_ca'] == null) &&
    (!isset($option['so_hd1']) || $option['so_hd1'] == null)
)
                    <!-- <a>Có <span style="color : red; font-weight: bold; font-size:20px">{{ $count }}</span> kết quả được tìm thấy.                 <span> Nội dung có màu <span style="color:black;background-color:black">###</span> là dữ liệu bình thường, màu <span
                                            style="color:#149B5E;background-color:#149B5E">###</span> là dữ liệu cảnh báo, màu <span
                                            style="color:red;background-color:red">###</span>  là ngăn chặn</span></a> -->
                @else
                    <a>Có <span style="color : red; font-weight: bold; font-size:20px">{{ $count }}</span>
                        kết quả được tìm thấy.</a> <span> Nội dung có màu <span
                            style="color:black;background-color:black">###</span> là dữ liệu bình thường,
                        màu <span style="color:#149B5E;background-color:#149B5E">###</span> là dữ liệu cảnh
                        báo,<span style="color:red;font-weight:bold;font-size:20px">
    {{ $count_ngan_chan_result ?? 0 }}
</span>
                        màu <span style="color:red;background-color:red">###</span> là ngăn chặn</span>
                    </a>
                @endif
            </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-borderd  table-striped table-hover" style="border: 1.5px solid grey" id="table_data">
                <thead style=" border: 1px solid; background-color:#1a67a3; color:white;">
                    <tr style="border: 1px solid; color:white !important;">
                        @if ($role == 'admin' || $role == 'chuyen-vien-so')
                            <th style="border: 1px solid grey; color:white !important;"><b>STT HS </b> </th>
                        @endif
                        <th style="border: 1px solid grey;color:white !important;"><b>Ngày nhập hệ thống </b> </th>
                        <th style="border: 1px solid grey;width: 8%;color:white !important;"><b>Ngày CC/</b></br> <b>ngăn chặn</b>
                        </th>
                        <th style="border: 1px solid grey;color:white !important;"><b>Các bên liên quan</b></th>
                        <th style="border: 1px solid grey;color:white !important;"><b>Nội dung tóm tắt/ giao dịch</b></th>
                        <th style="border: 1px solid grey;width: 10%;color:white !important;"><b>Số HD/CV NC/ </b> </br> <b>Tên
                                HD/GD</b></th>
                        <th style="border: 1px solid grey;width: 10%;color:white !important;"><b>VP CCV/</b> </br> <b>Người
                                nhập</b></th>
                        <th style="border: 1px solid grey;width: 9%;color:white !important;"><b>Ngăn chặn/</b></br> <b>Giải tỏa</b>
                        </th>
                        </th>
                        <th style="border: 1px solid grey;color:white !important;"><b>Chỉnh sửa</b></th>
                    </tr>
                </thead>
                @if ($vp !== '2190')
                    <tbody style="border: 1px solid;">

                        @foreach ($data as $key => $value)
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
                                $duong_su = str_replace(['BÊN ĐƯỢC ỦY QUYỀN', 'Bên được uỷ quyền', 'BÊN ĐƯỢC ỦY QUYỀN', 'BÊN ĐƯỢC UỶ QUYỀN', 'Bên được ủy quyền', 'Bên nhận ủy quyền'], '</br><b>Bên được ủy quyền</b>', $duong_su);
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
                                $duong_su_cut = str_replace(['BÊN ĐƯỢC ỦY QUYỀN', 'Bên được uỷ quyền', 'BÊN ĐƯỢC ỦY QUYỀN', 'BÊN ĐƯỢC UỶ QUYỀN', 'Bên được ủy quyền', 'Bên nhận ủy quyền'], '</br><b>Bên được ủy quyền</b>', $duong_su_cut);
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
                                $texte = str_replace('- ', '<br>-', $texte ?? '');
                                $texte = str_replace('+', '<br>+', $texte ?? '');
                                $texte = str_replace('1/.', '<br>1/.', $texte ?? '');
                                $texte = str_replace('2/.', '<br>2/.', $texte ?? '');
                                $texte = str_replace('3/.', '<br>3/.', $texte ?? '');
                                $texte = trim($texte, '"');
                                
                            @endphp
                            <tr>
                                @if ($value['ngan_chan'] && $value['ngan_chan'][0] == 3)
                                    @if ($role == 'admin')
                                        <td style="width: 8%; text-align:center !important;"> <b>{{ $value['st_id'] }} </b></td>
                                    @endif
                                    @if ($value['ma_phan_biet'] && $value['ma_phan_biet'][0] == 'D')
                                        <td style="color:#e74040;">
                                            <b>{{ \Carbon\Carbon::parse($value['created_at'][0])->format('H:i:s d/m/Y') ?? '' }}</b>
                                        </td>
                                    @else
                                        <td style="color:#e74040;">
                                       
                                            @if ($value['st_id'] > 1000
                                            &&
                                            \Carbon\Carbon::parse($value['created_at'][0])->format('H')  >= 0
                                            && \Carbon\Carbon::parse($value['created_at'][0])->format('H')  <= 5
                                            )
                                            <b>{{ \Carbon\Carbon::parse($value['created_at'][0])->addHour(7)->format('H:i:s d/m/Y') ?? '' }}</b>
                                            @else
                                            <b>
                                               {{-- {{ \Carbon\Carbon::parse($value['created_at'][0])->format('H:i:s d/m/Y') ?? '' }} 090124--}} 
                                               @if($value['ma_phan_biet'] && $value['ma_phan_biet'][0] == 'U')
                                               {{ \Carbon\Carbon::parse($value['ngay_nhap'][0])->format('d/m/Y') }}
                                               @else
                                               {{ \Carbon\Carbon::parse($value['ngay_cc'][0])->format('d/m/Y') }}
                                               @endif
                                            </b>
                                            @endif  
                                        </td>
                                    @endif
                                    <td style="color:#e74040;">
                                        {{ \Carbon\Carbon::parse($value['ngay_cc'][0])->format('d/m/Y') }}
                                    </td>
                                    <td style="color:#e74040" class="duong_su_1">
                                        @if (strlen($value['duong_su'][0] ?? '') > 250)
                                            {{ $duong_su_cut }}
                                            <div class="modal" tabindex="-1" role="dialog" name="modalinfo3"
                                                id="more-content-md3-{{ $value['st_id'] }}"
                                                aria-labelledby="modalLabeldanger">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header qkmodel">
                                                            <h5 class="modal-title qkmodel">Thông tin chi
                                                                tiết
                                                                giao dịch</h5>
                                                        </div>
                                                        <div class="modal-body" style="background-color: white">
                                                            <span class="qktrang" style="color: black">
                                                                {!! $duong_su !!}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <span id="three-dot2-{{ $value['st_id'] }}">...</span>
                                            <span id="{{ $value['st_id'] }}"
                                                onclick="showinfo3('{{ $value['st_id'] }}')">
                                                <i id="search-icon2-{{ $value['st_id'] }}"
                                                    class="fa fa-search-plus fa-1x  text-primary">
                                                </i>
                                            </span>
                                        @else
                                            {!! $duong_su !!}
                                        @endif
                                    </td>
                                    <td style="color:#e74040" class="tai_san_1">
                                        @if (strlen($value['texte'][0] ?? '') > 250)
                                            {{ mb_substr($value['texte'][0] ?? '', 0, 280, 'UTF-8') }}</br>
                                            <b style="color:#e74040">{{ $value['cancel_description'][0] ?? '' }}
                                            </b> </br> <b
                                                style="color:#e74040">{{ $value['contract_period'][0] ?? '' }}
                                            </b>
                                            <div class="modal" tabindex="-1" role="dialog"
                                                name="modalinfor_taisan3"
                                                id="more-content-md_taisan3-{{ $value['st_id'] }}"
                                                aria-labelledby="modalLabeldanger">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header qkmodel">
                                                            <h5 class="modal-title qkmodel">Thông tin chi
                                                                tiết
                                                                giao dịch</h5>
                                                        </div>
                                                        <div class="modal-body" style="background-color: white">
                                                            <span class="qktrang" style="color: black">
                                                                {!! $texte !!} <b
                                                                    style="color:#e74040">{{ $value['cancel_description'][0] ?? '' }}
                                                                </b> </br> <b
                                                                    style="color:#e74040">{{ $value['contract_period'][0] ?? '' }}
                                                                </b>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <span id="three-dot2-{{ $value['st_id'] }}">...</span>
                                            <span id="{{ $value['st_id'] }}"
                                                onclick="showtaisan3('{{ $value['st_id'] }}')">
                                                <i id="search-icon2-{{ $value['st_id'] }}"
                                                    class="fa fa-search-plus fa-2x  text-primary">
                                                </i>
                                            </span>
                                        @else
                                            {{ $value['texte'][0] ?? '' }}</br> <b
                                                style="color:#e74040">{{ $value['cancel_description'][0] ?? '' }}
                                            </b> </br> <b
                                                style="color:#e74040">{{ $value['contract_period'][0] ?? '' }}
                                            </b>
                                        @endif
                                    </td>
                                    @if ($value['undisputed_date'])
                                        <td style="color:#e74040" class="so_hd1"> <b>{{ $value['so_hd'][0] ?? '' }}</b>
                                            </br> {{ $value['ten_hd'][0] ?? '' }}</br> <b>
                                                {{ $value['ccv_master'][0] ?? '' }}</b> </br> <b
                                                style="color:#e74040"> Ngày giải chấp:
                                                {{ \Illuminate\Support\Carbon::parse($value['undisputed_date'][0])->format('d/m/Y') }}</b>
                                        </td>
                                    @else
                                        <td style="color:#e74040" class="so_hd1"> <b>{{ $value['so_hd'][0] ?? '' }}</b>
                                            </br> {{ $value['ten_hd'][0] ?? '' }} </br> <b>
                                                {{ $value['ccv_master'][0] ?? '' }}</b> </td>
                                    @endif
                                    <td style="color:#e74040">{{ $value['vp_master'][0] ?? '' }}<br>
                                        <b>
                                            {{ \App\Models\NhanVienModel::find($value['ccv'][0]??'')->nv_hoten }}
                                        </b>
                                    </td>
                                    <td style="color:#e74040">
                                        @if ($value['release_doc_number'])
                                            Đã giải toả theo CV:
                                            <b>{{ $value['release_doc_number'][0] }}</b>
                                        @endif
                                        @if ($value['release_doc_date'])
                                            <br>Ngày giải toả: {{ $value['release_doc_date'][0] }}
                                        @endif
                                        <div class="row">
                                            @if ($value['picture'])
                                                @if (is_array(json_decode($value['picture'][0], true)))
                                                    <a data-toggle="modal"
                                                        onClick="showModal({{ $value['st_id'] ?? '' }}) "
                                                        class="button button-circle button-mid button-primary">
                                                        <i class="fa fa-image"></i>
                                                    </a>
                                                    <div class="modal" tabindex="-1" role="dialog" name="modal2"
                                                        id="img-{{ $value['st_id'] ?? '' }}"
                                                        aria-labelledby="modalLabeldanger">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header bg-danger qkmodel">
                                                                    <h4 class="modal-title qkmodel">Danh
                                                                        sách tập tin
                                                                    </h4>
                                                                </div>
                                                                <div class="modal-body"
                                                                    style="background-color: white">
                                                                    <table>
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Tên tập tin</th>
                                                                                <th><i class="fa fa-cog"></i>
                                                                                </th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach (json_decode($value['picture'][0], true) as $key => $img)
                                                                                <tr>
                                                                                    <td>
                                                                                        <span>{{ json_decode($value['real_name'][0])[$key] }}</span></a>
                                                                                    </td>
                                                                                    @php
                                                                                        $name = json_decode($value['real_name'][0])[$key];
                                                                                    @endphp
                                                                                    @if ($name)
                                                                                        <td style="text-align: center">
                                                                                            <a
                                                                                                href="{{ route('downloadImg', ['img' => $img, 'name' => $name]) }}"><span><i
                                                                                                        class="fa fa-download"></i></span></a>
                                                                                        </td>
                                                                                    @endif
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                    @if ($value['release_doc_number'])
                                                                        <table>
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>Công văn giải toả
                                                                                    </th>
                                                                                    <th><i class="fa fa-cog"></i>
                                                                                    </th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @if ($value['release_file_path'])
                                                                                @foreach (collect(json_decode($value['release_file_path'][0])) as $key => $img)
                                                                                    <tr>
                                                                                        <td>
                                                                                            @php
    $files = json_decode($value['release_file_name'][0] ?? '[]', true);
@endphp

<span>{{ data_get($files, $key, 'FileGiaiToa') }}</span>

                                                                                        </td>
                                                                                       @php
    $files = json_decode($value['release_file_name'][0] ?? '[]', true);
    $name  = $files[$key] ?? 'FileGiaiToa';
@endphp
                                                                                        @if ($name)
                                                                                            <td
                                                                                                style="text-align: center">
                                                                                                <a
                                                                                                    href="{{ route('downloadImg', ['img' => $img, 'name' => $name]) }}"><span><i
                                                                                                            class="fa fa-download"></i></span></a>
                                                                                            </td>
                                                                                        @else
                                                                                        <td
                                                                                        style="text-align: center">
                                                                                        <a
                                                                                            href="{{ route('downloadImg', ['img' => $img, 'name' => 'FileGiaiToa.pdf']) }}"><span><i
                                                                                                    class="fa fa-download"></i></span></a>
                                                                                    </td>
                                                                                    @endif
                                                                                    </tr>
                                                                                @endforeach
                                                                            @endif
                                                                            </tbody>
                                                                        </table>
                                                                        <p style="color:black">File công
                                                                            văn giải toả đang được cập nhật
                                                                        </p>
                                                                    @endif
                                                                </div>
                                                                <div class="modal-footer"
                                                                    style="background-color: white">
                                                                    <div class="form-inline">
                                                                        <a href="#" data-dismiss="modal"
                                                                            class="btn btn-warning">Đóng</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @else
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <!-- @if ($value['contract_period'] != null)
<b>Thời hạn: </b> <span
                                                                        style="color: red">{{ $value['contract_period'][0] }}</span>
@else
@endif -->
                                        <br>

                                        @if ($value['is_update'] && $value['is_update'][0] == 1)
                                            {{ $value['note'][0] ?? ' ' }}
                                            @if (!empty($value['uchi_id'][0]))
                                                <a style="color: red"
                                                    href="{{ route('suutralogIndex', ['uchi_id' => $value['uchi_id'][0]]) }}">
                                                    Nhật ký chỉnh sửa
                                                </a>
                                            @else
                                                <a style="color: red"
                                                    href="{{ route('suutralogIndex', ['suutra_id' => $value['st_id']]) }}">
                                                    Nhật ký chỉnh sửa
                                                </a>
                                            @endif
                                        @endif
                                    </td>

                                    <!-- Cảnh báo -->
                                @elseif ($value['ngan_chan'] && $value['ngan_chan'][0] == 2)
                                    @if ($role == 'admin')
                                        <td style="width: 8%; text-align:center !important;"> <b>{{ $value['st_id'] }} </b></td>
                                    @endif
                                    @if ($value['ma_phan_biet'] && $value['ma_phan_biet'][0] == 'D')
                                        <td style="color:#149B5E;">
                                            <b>{{ \Carbon\Carbon::parse($value['created_at'][0])->format('H:i:s d/m/Y') ?? '' }}</b>
                                        </td>
                                    @else
                                        <td style="color:#149B5E;">
                                            @if ($value['st_id'] > 1000
                                            &&
                                            \Carbon\Carbon::parse($value['created_at'][0])->format('H')  >= 0
                                            && \Carbon\Carbon::parse($value['created_at'][0])->format('H')  <= 5
                                            )
                                                <b>{{ \Carbon\Carbon::parse($value['created_at'][0])->addHour(7)->format('H:i:s d/m/Y') ?? '' }}</b>
                                            @else
                                                <b>{{ \Carbon\Carbon::parse($value['created_at'][0])->format('H:i:s d/m/Y') ?? '' }}</b>
                                            @endif
                                        </td>
                                    @endif
                                    <td style="color:#149B5E;">
                                        {{ \Carbon\Carbon::parse($value['ngay_cc'][0])->format('d/m/Y') }}
                                    </td>
                                    <td style="color:#149B5E" class="duong_su_1">
                                        @if (strlen($value['duong_su'][0] ?? '') > 250)
                                            {!! $duong_su_cut !!}
                                            <div class="modal" tabindex="-1" role="dialog" name="modalinfo3"
                                                id="more-content-md3-{{ $value['st_id'] }}"
                                                aria-labelledby="modalLabeldanger">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header qkmodel">
                                                            <h5 class="modal-title qkmodel">Thông tin chi
                                                                tiết
                                                                giao dịch</h5>
                                                        </div>
                                                        <div class="modal-body" style="background-color: white">
                                                            <span class="qktrang" style="color: black">
                                                                {!! $duong_su !!}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <span id="three-dot2-{{ $value['st_id'] }}">...</span>
                                            <span id="{{ $value['st_id'] }}"
                                                onclick="showinfo3('{{ $value['st_id'] }}')">
                                                <i id="search-icon2-{{ $value['st_id'] }}"
                                                    class="fa fa-search-plus fa-2x  text-primary">
                                                </i>
                                            </span>
                                        @else
                                            {!! $duong_su !!}
                                        @endif
                                    </td>
                                    <td style="color:#149B5E" class="tai_san_1">
                                        @if (strlen($value['texte'][0] ?? '') > 250)
                                            {{ mb_substr($value['texte'][0] ?? '', 0, 280, 'UTF-8') }}</br>
                                            <b style="color:#149B5E">{{ $value['cancel_description'][0] ?? '' }}
                                            </b> </br> <b
                                                style="color:#149B5E">{{ $value['contract_period'][0] ?? '' }}
                                            </b>
                                            <div class="modal" tabindex="-1" role="dialog"
                                                name="modalinfor_taisan3"
                                                id="more-content-md_taisan3-{{ $value['st_id'] }}"
                                                aria-labelledby="modalLabeldanger">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header qkmodel">
                                                            <h5 class="modal-title qkmodel">Thông tin chi
                                                                tiết
                                                                giao dịch</h5>
                                                        </div>
                                                        <div class="modal-body" style="background-color: white">
                                                            <span class="qktrang" style="color: black">
                                                                {!! $texte !!} <b
                                                                    style="color:#e74040">{{ $value['cancel_description'][0] ?? '' }}
                                                                </b> </br> <b
                                                                    style="color:#e74040">{{ $value['contract_period'][0] ?? '' }}
                                                                </b>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <span id="three-dot2-{{ $value['st_id'] }}">...</span>
                                            <span id="{{ $value['st_id'] }}"
                                                onclick="showtaisan3('{{ $value['st_id'] }}')">
                                                <i id="search-icon2-{{ $value['st_id'] }}"
                                                    class="fa fa-search-plus fa-2x  text-primary">
                                                </i>
                                            </span>
                                        @else
                                            {{ $value['texte'][0] ?? '' }}</br> <b
                                                style="color:#149B5E">{{ $value['cancel_description'][0] ?? '' }}
                                            </b> </br> <b
                                                style="color:#149B5E">{{ $value['contract_period'][0] ?? '' }}
                                            </b>
                                        @endif
                                    </td>
                                    @if ($value['undisputed_date'])
                                        <td style="color:#149B5E" class="so_hd1"> <b>{{ $value['so_hd'][0] ?? '' }}</b>
                                            </br> {{ $value['ten_hd'][0] ?? '' }}</br> <b>
                                                {{ $value['ccv_master'][0] ?? '' }}</b> </br> <b
                                                style="color:#149B5E"> Ngày giải chấp:
                                                {{ \Illuminate\Support\Carbon::parse($value['undisputed_date'][0])->format('d/m/Y') }}</b>
                                        </td>
                                    @else
                                        <td style="color:#149B5E" class="so_hd1"> <b>{{ $value['so_hd'][0] ?? '' }}</b>
                                            </br> {{ $value['ten_hd'][0] ?? '' }} </br> <b>
                                                {{ $value['ccv_master'][0] ?? '' }}</b> </td>
                                    @endif
                                    <td style="color:#149B5E">{{ $value['vp_master'][0] ?? '' }}<br>
                                        <b>
                                            {{ \App\Models\NhanVienModel::find($value['ccv'][0])->nv_hoten }}
                                        </b>
                                    </td>
                                    <td style="color:#149b5e">
                                        @if ($value['release_doc_number'])
                                            Đã giải toả theo CV:
                                            <b>{{ $value['release_doc_number'][0] }}</b>
                                        @endif
                                        @if ($value['release_doc_date'])
                                            <br>Ngày giải toả: {{ $value['release_doc_date'][0] }}
                                        @endif
                                        <div class="row">
                                            @if ($value['picture'])
                                                @if (is_array(json_decode($value['picture'][0], true)))
                                                    <a data-toggle="modal"
                                                        onClick="showModal({{ $value['st_id'] ?? '' }}) "
                                                        class="button button-circle button-mid button-primary">
                                                        <i class="fa fa-image"></i>
                                                    </a>
                                                    <div class="modal" tabindex="-1" role="dialog" name="modal2"
                                                        id="img-{{ $value['st_id'] ?? '' }}"
                                                        aria-labelledby="modalLabeldanger">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header bg-danger qkmodel">
                                                                    <h4 class="modal-title qkmodel">Danh
                                                                        sách tập tin
                                                                    </h4>
                                                                </div>
                                                                <div class="modal-body"
                                                                    style="background-color: white">
                                                                    <table>
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Tên tập tin</th>
                                                                                <th><i class="fa fa-cog"></i>
                                                                                </th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach (json_decode($value['picture'][0], true) as $key => $img)
                                                                                <tr>
                                                                                    <td>
                                                                                        <span>{{ json_decode($value['real_name'][0])[$key] }}</span></a>
                                                                                    </td>
                                                                                    @php
                                                                                        $name = json_decode($value['real_name'][0])[$key];
                                                                                    @endphp
                                                                                    @if ($name)
                                                                                        <td style="text-align: center">
                                                                                            <a
                                                                                                href="{{ route('downloadImg', ['img' => $img, 'name' => $name]) }}"><span><i
                                                                                                        class="fa fa-download"></i></span></a>
                                                                                        </td>
                                                                                    @endif
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                    @if ($value['release_doc_number'])
                                                                        <table>
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>Công văn giải toả
                                                                                    </th>
                                                                                    <th><i class="fa fa-cog"></i>
                                                                                    </th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                {{-- @if ($value['release_file_path'])
                                                                                    @foreach (collect(json_decode($value['release_file_path'][0])) as $key => $img)
                                                                                        <tr>
                                                                                            <td>
                                                                                                <span>{{ json_decode($value['release_file_name'][0])[$key] }}</span></a>
                                                                                            </td>
                                                                                            @php
                                                                                                $name = json_decode($value['release_file_name'][0])[$key];
                                                                                                
                                                                                            @endphp
                                                                                            @if ($name)
                                                                                                <td
                                                                                                    style="text-align: center">
                                                                                                    <a
                                                                                                        href="{{ route('downloadImg', ['img' => $img, 'name' => $name]) }}"><span><i
                                                                                                                class="fa fa-download"></i></span></a>
                                                                                                </td>
                                                                                            @endif
                                                                                        </tr>
                                                                                    @endforeach
                                                                                @endif --}}


                                                                                @if ($value['release_file_path'])
                                                                                @foreach (collect(json_decode($value['release_file_path'][0])) as $key => $img)
                                                                                    <tr>
                                                                                        <td>
                                                                                            <span>{{ json_decode($value['release_file_name'][0])[$key] ?? 'FileGiaiToa' }}</span></a>
                                                                                        </td>
                                                                                        @php
                                                                                            $name = json_decode($value['release_file_name'][0])[$key];
                                                                                            
                                                                                        @endphp
                                                                                        @if ($name)
                                                                                            <td
                                                                                                style="text-align: center">
                                                                                                <a
                                                                                                    href="{{ route('downloadImg', ['img' => $img, 'name' => $name]) }}"><span><i
                                                                                                            class="fa fa-download"></i></span></a>
                                                                                            </td>
                                                                                        @else
                                                                                        <td
                                                                                        style="text-align: center">
                                                                                        <a
                                                                                            href="{{ route('downloadImg', ['img' => $img, 'name' => 'FileGiaiToa.pdf']) }}"><span><i
                                                                                                    class="fa fa-download"></i></span></a>
                                                                                    </td>
                                                                                    @endif
                                                                                    </tr>
                                                                                @endforeach
                                                                            @endif
                                                                            </tbody>
                                                                        </table>
                                                                        <p style="color:black">File công
                                                                            văn giải toả đang được cập nhật
                                                                        </p>
                                                                    @endif
                                                                </div>
                                                                <div class="modal-footer"
                                                                    style="background-color: white">
                                                                    <div class="form-inline">
                                                                        <a href="#" data-dismiss="modal"
                                                                            class="btn btn-warning">Đóng</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @else
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <!-- @if ($value['contract_period'] != null)
<b>Thời hạn: </b> <span
                                                                        style="color: red">{{ $value['contract_period'][0] }}</span>
@else
@endif -->
                                        <br>

                                        @if ($value['is_update'] && $value['is_update'][0] == 1)
                                            {{ $value['note'][0] ?? ' ' }}
                                            @if (!empty($value['uchi_id'][0]))
                                                <a style="color: #149B5E"
                                                    href="{{ route('suutralogIndex', ['uchi_id' => $value['uchi_id'][0]]) }}">
                                                    Nhật ký chỉnh sửa
                                                </a>
                                            @else
                                                <a style="color: #149B5E"
                                                    href="{{ route('suutralogIndex', ['suutra_id' => $value['st_id']]) }}">
                                                    Nhật ký chỉnh sửa
                                                </a>
                                            @endif
                                        @endif
                                    </td>
                                    <!-- End Cảnh báo -->
                                @else
                                    @if ($role == 'admin' || $role == 'chuyen-vien-so')
                                        <td style="width: 8%; text-align:center !important;"> <b>{{ $value['st_id'] }} </b></td>
                                    @endif
                                    @if ($value['ma_phan_biet'] && $value['ma_phan_biet'][0] == 'D')
                                        <td> <b>{{ \Carbon\Carbon::parse($value['created_at'][0])->format('H:i:s d/m/Y') ?? '' }}</b>
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
                                                <b>{{ \Carbon\Carbon::parse($value['created_at'][0])->format('H:i:s d/m/Y') ?? '' }}</b>
                                            @endif
                                        </td>
                                    @endif
                                
                                    @if ($value['ngay_chan'])
                                        @php
                                            $a = date('d/m/Y H:i:s', strtotime($value['ngay_chan'][0]) ?? '');
                                        @endphp
                                        @if ($a == '01/01/1970 08:00:00')
                                            <td style="width: 8%">
                                                <b>{{ \Carbon\Carbon::parse($value['ngay_cc'][0])->format('d/m/Y') }}
                                                </b>
                                            </td>
                                        @else
                                            <td style="width: 8%">
                                                <b>{{ \Carbon\Carbon::parse($value['ngay_cc'][0])->format('d/m/Y') }}
                                                </b>
                                            </td>
                                        @endif
                                    @else
                                        @php
    $a = isset($value['ngay_cc']['date'])
        ? \Carbon\Carbon::parse($value['ngay_cc']['date'])->format('d/m/Y')
        : '';
@endphp
                                        @if ($a == '01/01/1970')
                                            <td style="width: 8%">
                                                <b>{{ \Carbon\Carbon::parse($value['ngay_cc'][0])->format('d/m/Y') }}</b>
                                            </td>
                                        @else
                                            <td style="width: 8%">
    <b>
        @php
            $ngay = '';

            if (!empty($value['ngay_cc'])) {
                // Dữ liệu cũ: array [0 => 'yyyy-mm-dd']
                if (is_array($value['ngay_cc']) && isset($value['ngay_cc'][0])) {
                    $ngay = \Carbon\Carbon::parse($value['ngay_cc'][0])->format('d/m/Y');

                // Dữ liệu mới: Carbon toArray()
                } elseif (is_array($value['ngay_cc']) && isset($value['ngay_cc']['date'])) {
                    $ngay = \Carbon\Carbon::parse($value['ngay_cc']['date'])->format('d/m/Y');

                // Dữ liệu mới: string datetime
                } elseif (is_string($value['ngay_cc'])) {
                    $ngay = \Carbon\Carbon::parse($value['ngay_cc'])->format('d/m/Y');
                }
            }
        @endphp

        {{ $ngay }}
    </b>
</td>

                                        @endif
                                    @endif
                                    <td class="duong_su_1">
                                        @if ($value['duong_su'] && strlen($value['duong_su'][0] ?? '') > 250)
                                            {!! $duong_su_cut !!}
        </div>
        <div class="modal" tabindex="-1" role="dialog" name="modalinfo3"
            id="more-content-md3-{{ $value['st_id'] }}" aria-labelledby="modalLabeldanger">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header qkmodel">
                        <h5 class="modal-title qkmodel">Thông tin chi tiết
                            giao dịch</h5>
                    </div>
                    <div class="modal-body" style="background-color: white">
                        <span class="qktrang" style="color: black">
                            {!! $duong_su !!}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <span id="three-dot2-{{ $value['st_id'] }}">...</span>
        <span id="{{ $value['st_id'] }}" onclick="showinfo3('{{ $value['st_id'] }}')">
            <i id="search-icon2-{{ $value['st_id'] }}" class="fa fa-search-plus fa-2x  text-primary">
            </i>
        </span>
    @else
        {!! $duong_su !!}
        @endif
        </td>
        <td class="tai_san_1">
            @if (strlen($value['texte'][0] ?? '') > 250)
                {{ mb_substr($value['texte'][0] ?? '', 0, 280, 'UTF-8') }} </br> <b
                    style="color:#e74040">{{ $value['cancel_description'][0] ?? '' }} </b> </br> <b
                    style="color:#e74040">{{ $value['contract_period'][0] ?? '' }} </b>
                <div class="modal" tabindex="-1" role="dialog" name="modalinfor_taisan3"
                    id="more-content-md_taisan3-{{ $value['st_id'] }}" aria-labelledby="modalLabeldanger">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header qkmodel">
                                <h5 class="modal-title qkmodel">Thông tin chi tiết
                                    giao dịch</h5>
                            </div>
                            <div class="modal-body" style="background-color: white">
                                <span class="qktrang" style="color: black">
                                    {!! $texte !!} <b
                                        style="color:#e74040">{{ $value['cancel_description'][0] ?? '' }}
                                    </b> </br> <b style="color:#e74040">{{ $value['contract_period'][0] ?? '' }}
                                    </b>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <span id="three-dot2-{{ $value['st_id'] }}">...</span>
                <span id="{{ $value['st_id'] }}" onclick="showtaisan3('{{ $value['st_id'] }}')">
                    <i id="search-icon2-{{ $value['st_id'] }}" class="fa fa-search-plus fa-2x  text-primary">
                    </i>
                </span>
            @else
                {{ $value['texte'][0] ?? '' }} </br> <b
                    style="color:#e74040">{{ $value['cancel_description'][0] ?? '' }} </b> </br> <b
                    style="color:#e74040">{{ $value['contract_period'][0] ?? '' }} </b>
            @endif
        </td>
        @if ($value['undisputed_date'])
            <td class="so_hd1"> <b>{{ $value['so_hd'][0] ?? '' }}</b> </br> {{ $value['ten_hd'][0] ?? '' }}</br> <b
                    style="color:#e74040;text-align:center"> Ngày giải chấp:
                    {{ \Illuminate\Support\Carbon::parse($value['undisputed_date'][0])->format('d/m/Y') }}</b>
                </br> <b> {{ $value['undisputed_note'][0] ?? '' }}</b> <b style="color:#e74040;text-align:center">
                    {{ $value['deleted_note'][0] ?? '' }}
                </b></br>
            </td>
        @else
            <td class="so_hd1"> <b>{{ $value['so_hd'][0] ?? '' }}</b> </br> {{ $value['ten_hd'][0] ?? '' }}</br> <b
                    style="color:#e74040;text-align:center">
                    {{ $value['deleted_note'][0] ?? '' }}</b></br>
            </td>
        @endif
        <td>{{ $value['vp_master'][0] ?? '' }} </br> <b> {{ $value['ccv_master'][0] ?? '' }}</b></td>
        <td>
            <!-- @if ($value['contract_period'] && $value['contract_period'][0] != null)
<b>Thời hạn: </b> <span
                                                                        style="color: red">{{ $value['contract_period'][0] }}</span>
@else
@endif -->
            <br>
                       @if ($value['is_update'] && $value['is_update'][0] == 1)
                {{ $value['note'][0] ?? '' }}
                @if (!empty($value['uchi_id'][0]))
                    <a style="color: red" href="{{ route('suutralogIndex', ['uchi_id' => $value['uchi_id'][0]]) }}">
                        Nhật ký chỉnh sửa
                    </a>
                @else
                    <a style="color: red" href="{{ route('suutralogIndex', ['suutra_id' => $value['st_id']]) }}">
                        Nhật ký chỉnh sửa
                    </a>
                @endif
            @else
                {{ $value['note'][0] ?? '' }}
            @endif
        </td>
        <td>
            @if ($id_user == 2608 && $vp == 2047)
                <a style="color: red" href="{{ route('deleteSolr', ['id' => $value['st_id']]) }}">
                    <i class="fa fa-trash"></i>
                </a>
            @endif

          
        </td>
        @endif
        </tr>
        @endforeach
        </tbody>
    @else
        <p style="color: red"> <i class="fa fa-lock" aria-hidden="true"></i> Yêu cầu quyền truy cập! </p>

        @endif

        </table>
    </div>

</div>

@endif
</div>
<div class="col-sm-12">
    <div class="col-sm-6">
        @if ($data)
            {{ $data->appends(request()->input())->links() }}
        @endif
    </div>
</div>
</div>
