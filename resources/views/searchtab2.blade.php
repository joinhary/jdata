<div class="tab-pane" id="tab2">
    <div class="row">
        <form action="{{ route('searchSolr_nganchan') }}" id="formSreachprevent">
            <input hidden name='type' value='prevent'>
            <div class="row">
                <div class="col-md-2">
                    <input type="text" class="form-control" id="duong_su2" name="duong_su2"
                        onchange="hide_inputAll2()"
                        value="{{ isset($option['duong_su2']) ? $option['duong_su2'] : '' }}"
                        placeholder="Tìm kiếm chủ thể" autofocus
                        style="  padding: 10px 10px;
                               margin: 8px 0;
                               autofocus;
                               width: 180px;
                               box-sizing: border-box;">
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control" id="tai_san2" name="tai_san2"
                        onchange="hide_inputAll2()"
                        value="{{ isset($option['tai_san2']) ? $option['tai_san2'] : '' }}"
                        placeholder="Tìm kiếm theo tài sản" autofocus
                        style=" padding: 10px 10px;
                               margin: 8px 0;
                               width: 180px;
                               box-sizing: border-box;">
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" id="tat_ca2" name="tat_ca2"
                        onchange="hide_input2()" value="{{ isset($option['tat_ca2']) ? $option['tat_ca2'] : '' }}"
                        placeholder="Tìm kiếm tất cả" autofocus
                        style="  padding: 10px 10px;
                               margin: 8px 0;
                                width: 180px;
                               box-sizing: border-box;">
                </div>
                {{-- <div class="col-md-6 search" >
                        <span class="fa fa-search"></span>
                        <input type="text" class="form-control" id="test" name="test"
                        value="{{isset($option['test'])?$option['test']:''}}"
                               placeholder="Tìm test" autofocus style="width: 30%;  padding: 10px 10px;
                               margin: 8px 0;
                               box-sizing: border-box;">
                    </div> --}}
        </form>
        <div class="col-md-4" style="margin-left:-180px;margin-top: 8px">
            <button class="btn btn-success btn1" id="search2" type="submit" onclick="highlight('a')">
                <i class="fa fa-search"></i>
                Tìm kiếm
            </button>
            <button id="btnclear" type="button" class="btn btn-danger" onclick="clearValue()"> <i
                    class="fa fa-trash"></i> Xóa</button>
            <button id="btnprint2" type="button" class="btn btn-warning" onclick="btnprint_click2()"> <i
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
            <div class="col-md-6">
                <a> <b>Gợi ý:</b> Nguyễn Văn A để tìm <b>hoặc</b> (kèm năm sinh hoặc CMND). </a>
            </div>
            <div class="col-md-12">
                @if ($option == [])
                @elseif (
    (!isset($option['duong_su']) || $option['duong_su'] == null) &&
    (!isset($option['tai_san']) || $option['tai_san'] == null) &&
    (!isset($option['tat_ca']) || $option['tat_ca'] == null) &&
    (!isset($option['so_hd1']) || $option['so_hd1'] == null)
)                    <!--
                                            <a>Có <span style="color : red; font-weight: bold; font-size:20px">{{ $count }}</span> kết quả được tìm thấy.                 <span> Nội dung có màu <span style="color:black;background-color:black">###</span> là dữ liệu bình thường, màu <span
                                                    style="color:#149B5E;background-color:#149B5E">###</span> là dữ liệu cảnh báo, màu <span
                                                    style="color:red;background-color:red">###</span>  là ngăn chặn</span></a> -->
                @else
                    <a>Có <span style="color : red; font-weight: bold; font-size:20px">{{ $count }}</span>
                        kết quả được tìm thấy.</a> <span> Nội dung có màu
                        <span style="color:red;font-weight:bold;font-size:20px">
    {{ $count_ngan_chan_result ?? 0 }}
</span>
                        màu <span style="color:red;background-color:red">###</span> là ngăn chặn</span> </a>
                @endif
            </div>
    </div>
    <div class="row">
        <div class="col-md-12">

            <table class="table table-borderd  table-striped table-hover" style="border: 1.5px solid grey" id="table_data2">
                <thead style=" border: 1px solid;background-color:#1a67a3; color:white !important;">
                    <tr style="border: 1px solid;color:white !important;">
                        <th style="border: 1px solid grey;color:white !important;"><b>Ngày nhập hệ thống </b> </th>
                        <th style="border: 1px solid grey;width: 8%;color:white !important;"><b>Ngày CC/</b></br> <b>ngăn chặn</b></th>
                        <th style="border: 1px solid grey;color:white !important;"><b>Các bên liên quan</b></th>
                        <th style="border: 1px solid grey;color:white !important;"><b>Nội dung tóm tắt/ giao dịch</b></th>
                        <th style="border: 1px solid grey;width: 10%;color:white !important;"><b>Số HD/CV NC/ </b> </br> <b>Tên HD/GD</b>
                        </th>
                        <th style="border: 1px solid grey;width: 10%;color:white !important;"><b>VP CCV/</b> </br> <b>Người nhập</b></th>
                        <th style="border: 1px solid grey;width: 9%;color:white !important;"><b>Ngăn chặn/</b></br> <b>Giải tỏa</b></th>
                        </th>
                        <th style="border: 1px solid grey;color:white !important;"><b>Chỉnh sửa</b></th>
                    </tr>
                </thead>
                <tbody>

                    {{-- @foreach ($data['data'] as $key => $value)
                            <tr>
                                  <td>{{ $value['duong_su'][0] ?? '' }}</td>
                                  <td>{{ $value['texte'][0] ?? '' }}</td>
                              </tr>
                          @endforeach --}}
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
                            $texte = str_replace('-', '<br>-', $texte ?? '');
                            $texte = str_replace('+', '<br>+', $texte ?? '');
                            $texte = str_replace('1/.', '<br>1/.', $texte ?? '');
                            $texte = str_replace('2/.', '<br>2/.', $texte ?? '');
                            $texte = str_replace('3/.', '<br>3/.', $texte ?? '');
                            $texte = trim($texte, '"');
                            
                        @endphp
                        <tr>
                            @if ($value['ngan_chan'] && $value['ngan_chan'][0] == 3)
                                @if ($value['ma_phan_biet'] && $value['ma_phan_biet'][0] == 'D')
                                    <td style="color:#e74040">
                                        <b>{{ \Carbon\Carbon::parse($value['created_at'][0])->format('H:i:s d/m/Y') ?? '' }}</b>
                                    </td>
                                @else
                                    <td style="color:#e74040">

                                        @if ($value >= 880749 &&
                                        \Carbon\Carbon::parse($value['created_at'][0])->format('H')  >= 0
                                        && \Carbon\Carbon::parse($value['created_at'][0])->format('H')  <= 6
                                        )
                                        <b>{{ \Carbon\Carbon::parse($value['created_at'][0])->addHour(7)->format('H:i:s d/m/Y') ?? '' }}</b>
                                        @else
                                        {{-- <b>{{ \Carbon\Carbon::parse($value['created_at'][0])->format('H:i:s d/m/Y') ?? '' }}</b> --}}
                                        <b>
                                            @if($value['ma_phan_biet'] && $value['ma_phan_biet'][0] == 'U')
                                            {{ \Carbon\Carbon::parse($value['ngay_nhap'][0])->format('d/m/Y') }}
                                            @else
                                            {{ \Carbon\Carbon::parse($value['ngay_cc'][0])->format('d/m/Y') }}
                                            @endif
                                        </b>   
                                        @endif                                             
                                    </td>
                                @endif
                                <td style="color:#e74040">
                                    {{ \Carbon\Carbon::parse($value['ngay_cc'][0])->format('d/m/Y') }}</td>
                                <td style="color:#e74040" class="duong_su_2">
                                    @if (strlen($value['duong_su'][0] ?? '') > 250)
                                        {{ mb_substr($value['duong_su'][0] ?? '', 0, 280, 'UTF-8') }}
                                        <div class="modal" tabindex="-1" role="dialog" name="modalinfor2"
                                            id="more-content-md2-{{ $value['st_id'] }}"
                                            aria-labelledby="modalLabeldanger">
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
                                        <span id="{{ $value['st_id'] }}"
                                            onclick="showinfo2('{{ $value['st_id'] }}')">
                                            <i id="search-icon2-{{ $value['st_id'] }}"
                                                class="fa fa-search-plus fa-2x  text-primary">
                                            </i>
                                        </span>
                                    @else
                                        {{ $value['duong_su'][0] ?? '' }}
                                    @endif
                                </td>
                                <td style="color:#e74040" class="tai_san_2">
                                    @if (strlen($value['texte'][0] ?? '') > 250)
                                        {{ mb_substr($value['texte'][0] ?? '', 0, 250, 'UTF-8') }} </br> <b
                                            style="color:#e74040">{{ $value['cancel_description'][0] ?? '' }}
                                        </b>
                                        </br> <b style="color:#e74040">{{ $value['contract_period'][0] ?? '' }}
                                        </b>
                                        <div class="modal" tabindex="-1" role="dialog"
                                            name="modalinfor_taisan2"
                                            id="more-content-md_taisan2-{{ $value['st_id'] }}"
                                            aria-labelledby="modalLabeldanger">
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
                                            onclick="showtaisan2('{{ $value['st_id'] }}')">
                                            <i id="search-icon2-{{ $value['st_id'] }}"
                                                class="fa fa-search-plus fa-2x  text-primary">
                                            </i>
                                        </span>
                                    @else
                                        {{ $value['texte'][0] ?? '' }} </br> <b
                                            style="color:#e74040">{{ $value['cancel_description'][0] ?? '' }}
                                        </b>
                                        </br> <b style="color:#e74040">{{ $value['contract_period'][0] ?? '' }}
                                        </b>
                                    @endif
                                </td>
                                @if ($value['st_id'] ?? '')
                                    <td style="color:#e74040"> <b>{{ $value['so_hd'][0] ?? '' }}</b>
                                        {{ $value['ten_hd'][0] ?? '' }}</br> <b>
                                            {{ $value['ccv_master'][0] ?? '' }}</b> </td>
                                @else
                                    <td style="color:#e74040"> <b>{{ $value['so_hd'][0] ?? '' }}</b>
                                        {{ $value['ten_hd'][0] ?? '' }}</br> <b>
                                            {{ $value['ccv_master'][0] ?? '' }}</b> </td>
                                @endif
                                <td style="color:#e74040">{{ $value['vp_master'][0] ?? '' }}<br>
                                    <b>
                                        {{ \App\Models\NhanVienModel::find($value['ccv'][0]??'')->nv_hoten }}
                                    </b>
                                    </br> <b style="color:#e74040;text-align:center">
                                        {{ $value['deleted_note'][0] ?? '' }}</b>
                                </td>
                                <td style="color:#e74040">


                                    @if ($value['release_doc_number'])
                                        Đã giải toả theo CV: <b>{{ $value['release_doc_number'][0] }}</b>
                                    @endif
                                    @if ($value['release_doc_date'])
                                        <br>Ngày giải toả: {{ $value['release_doc_date'][0] }}
                                    @endif

                                    @if ($role == 'admin' || $role == 'chuyen-vien-so' || $vp === '2190')
                                        <div class="row">
                                            @if ($value['st_id'])
                                                <a href="{{ route('editSuutraSTP', ['id' => $value['st_id'] ?? '']) }}"
                                                    class="button button-circle button-mid button-primary">
                                                    <i class="fa fa-pencil-square-o"></i>
                                                </a>
                                            @endif
                                        @else
                                            @if ($value['st_id'])
                                            @if ($role == 'admin' || $role == 'chuyen-vien-so')
                                                <a href="{{ route('editSuutra', ['id' => $value['st_id'] ?? '']) }}"
                                                    class="button button-circle button-mid button-primary">
                                                    <i class="fa fa-pencil-square-o"></i>
                                                </a>
                                                @endif
                                            @endif
                                    @endif
                                    <div class="row">
                                        @if ($value['picture'])
                                            @if (is_array(json_decode($value['picture'][0], true)))
                                                <a data-toggle="modal"
                                                    onClick="showModal2({{ $value['st_id'] ?? '' }}) "
                                                    class="button button-circle button-mid button-primary">
                                                    <i class="fa fa-image"></i>
                                                </a>
                                                <div class="modal" tabindex="-1" role="dialog" name="modal2"
                                                    id="imgg-{{ $value['st_id'] ?? '' }}"
                                                    aria-labelledby="modalLabeldanger">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-danger qkmodel">
                                                                <h4 class="modal-title qkmodel">Danh sách tập tin
                                                                </h4>
                                                            </div>
                                                            <div class="modal-body"
                                                                style="background-color: white">
                                                                <table>
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Tên tập tin</th>
                                                                            <th><i class="fa fa-cog"></i></th>
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
                                                                                <th>Công văn giải toả</th>
                                                                                <th><i class="fa fa-cog"></i></th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @if ($value['release_file_path'])
                                                                                @foreach (collect(json_decode($value['release_file_path'][0])) as $key => $img)
                                                                                    <tr>
                                                                                        <td>
                                                                                            @php
    $files = json_decode($value['release_file_name'][0] ?? '[]', true);
    $name  = $files[$key] ?? 'FileGiaiToa';
@endphp

<span>{{ $name }}</span>

                                                                                        </td>
                                                                                        @php
    $name = data_get(
        json_decode($value['release_file_name'][0] ?? '[]', true),
        $key,
        'FileGiaiToa'
    );
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
                                                                    <p style="color:black">File công văn giải toả
                                                                        đang được cập nhật</p>
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
                                    <!-- @if ($value['contract_period'] && $value['contract_period'][0] != null)
<b>Thời hạn: </b> <span
                                                                                style="color: red">{{ $value['contract_period'][0] }}</span>
@else
@endif -->
                                    </br>
                                    @if ($value['is_update'] && $value['is_update'][0] == 1)
                                        {{ $value['note'][0] ?? '' }}
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
                                    @else
                                        {{ $value['note'][0] ?? '' }}
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
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