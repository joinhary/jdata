

<div class="tab-pane" id="tab3">
    <div class="row">
        @include('modalTbc2')

        <form action="{{ route('searchSolr_vp') }}" id="formSreachoffice">
            <input hidden name='type' value='office'>
            <div class="row">
                <div class="col-md-2">
                    <input type="text" class="form-control" id="duong_su3" name="duong_su3"
                        onchange="hide_inputAll3()"
                        value="{{ isset($option['duong_su3']) ? $option['duong_su3'] : '' }}"
                        placeholder="Tìm kiếm chủ thể" autofocus
                        style="  padding: 10px 10px;
                               margin: 8px 0;
                               autofocus;
                               width: 180px;
                               box-sizing: border-box;">
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control" id="tai_san3" name="tai_san3"
                        onchange="hide_inputAll3()"
                        value="{{ isset($option['tai_san3']) ? $option['tai_san3'] : '' }}"
                        placeholder="Tìm kiếm theo tài sản" autofocus
                        style=" padding: 10px 10px;
                               margin: 8px 0;
                               width: 180px;
                               box-sizing: border-box;">
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control" id="so_hd3" name="so_hd3"
                        onchange="hide_inputAll3()"
                        value="{{ isset($option['so_hd3']) ? $option['so_hd3'] : '' }}"
                        placeholder="Tìm kiếm theo số HĐ" autofocus
                        style="  padding: 10px 10px;
                               margin: 8px 0;
                               width: 180px;
                               box-sizing: border-box;">
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" id="tat_ca3" name="tat_ca3"
                        onchange="hide_input3()" value="{{ isset($option['tat_ca3']) ? $option['tat_ca3'] : '' }}"
                        placeholder="Tìm kiếm tất cả" autofocus
                        style="  padding: 10px 10px;
                               margin: 8px 0;
                               width: 180px;
                               box-sizing: border-box;">
                </div>
        </form>
        <div class="col-md-3" style="margin-left:-180px;margin-top: 8px">
            <button class="btn btn-info" id="search2" type="submit" onclick="highlight('a')">
                <i class="fa fa-search"></i>
                Tìm kiếm
            </button>
            <button id="btnclear" type="button" class="btn btn-danger" onclick="clearValue()"> <i
                    class="fa fa-trash"></i> Xóa</button>
            <button id="btnprint3" type="button" class="btn btn-warning" onclick="btnprint_click3()"> <i
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
        @if (!empty($tbc) && ($tbc['total'] ?? 0) > 0)
    <div class="col-md-6">
        <a>
            <i class="fa fa-warning"></i>
            <b>Chú ý:</b>
            <span style="color:red; font-weight:bold; font-size:20px;">
                Có {{ $tbc['total'] }} thông báo từ kết quả này.
                <button type="button" class="btn btn-danger" onclick="$('#myModalTbc2').modal('show');">
                    Bấm vào xem ngay
                </button>
            </span>
        </a>
    </div>
@else
    <div class="col-md-6">
        <a><b>Gợi ý:</b> Nguyễn Văn B để tìm <b>hoặc</b> (kèm năm sinh hoặc CMND).</a>
    </div>
@endif

            <div class="col-md-12">
                @if ($option == [])
                @elseif (
    (!isset($option['duong_su']) || $option['duong_su'] == null) &&
    (!isset($option['tai_san']) || $option['tai_san'] == null) &&
    (!isset($option['tat_ca']) || $option['tat_ca'] == null) &&
    (!isset($option['so_hd1']) || $option['so_hd1'] == null)
)        <!-- <a>Có <span style="color : red; font-weight: bold; font-size:20px">{{ $count }}</span> kết quả được tìm thấy.                 <span> Nội dung có màu <span style="color:black;background-color:black">###</span> là dữ liệu bình thường, màu <span
                                                    style="color:#149B5E;background-color:#149B5E">###</span> là dữ liệu cảnh báo, màu <span
                                                    style="color:red;background-color:red">###</span>  là ngăn chặn</span></a> -->
                @else
                    <a>Có <span style="color : red; font-weight: bold; font-size:20px">{{ $count }}</span>
                        kết quả được tìm thấy.</a> <span> Nội dung có màu <span
                            style="color:black;background-color:black">###</span> là dữ liệu bình thường, màu <span
                            style="color:#149B5E;background-color:#149B5E">###</span> là dữ liệu cảnh báo,<span
                            style="color : red; font-weight: bold; font-size:20px"></span> màu <span
                            style="color:red;background-color:red">###</span> là ngăn chặn</span> </a>
                @endif
            </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-borderd  table-striped table-hover" style="border: 1.5px solid grey" id="table_data3">
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
                            $texte = str_replace('- ', '<br>-', $texte ?? '');
                            $texte = str_replace('+', '<br>+', $texte ?? '');
                            $texte = str_replace('1/.', '<br>1/.', $texte ?? '');
                            $texte = str_replace('2/.', '<br>2/.', $texte ?? '');
                            $texte = str_replace('3/.', '<br>3/.', $texte ?? '');
                            $texte = trim($texte, '"');
                            
                        @endphp
                        @if ($value['ngan_chan'] && $value['ngan_chan'][0] == 3)
                            <tr>
                                @if ($value['sync_code'][0] ?? '' == $code_cn)
                                    @if ($value['ma_phan_biet'] && $value['ma_phan_biet'][0] == 'D')
                                        <td> <b>{{ \Carbon\Carbon::parse($value['created_at'][0])->format('H:i:s d/m/Y') ?? '' }}</b>
                                        </td>
                                    @else
                                        <td> 
                                            @if ($value['st_id'] > 1000)
                                            <b>{{ \Carbon\Carbon::parse($value['created_at'][0])->addHour(7)->format('H:i:s d/m/Y') ?? '' }}</b>
                                            @else
                                            <b>{{ \Carbon\Carbon::parse($value['created_at'][0])->format('H:i:s d/m/Y') ?? '' }}</b>
                                            @endif
                                        </td>
                                    @endif
                                    <td> <b>{{ \Carbon\Carbon::parse($value['ngay_cc'][0])->format('d/m/Y') }}
                                        </b></td>
                                    <td class="duong_su_3">

                                        @if (strlen($value['duong_su'][0] ?? '') > 250)
                                            {!! $duong_su_cut !!}
                                            <div class="modal" tabindex="-1" role="dialog" name="modalinfor1"
                                                id="more-content-md1-{{ $value['st_id'] }}"
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
                                                onclick="showinfo1('{{ $value['st_id'] }}')">
                                                <i id="search-icon2-{{ $value['st_id'] }}"
                                                    class="fa fa-search-plus fa-2x  text-primary">
                                                </i>
                                            </span>
                                        @else
                                            {!! $duong_su !!}
                                        @endif
                                    </td>
                                    <td class="tai_san_3">
                                        @if (strlen($value['texte'][0] ?? '') > 250)
                                            {{ mb_substr($value['texte'][0] ?? '', 0, 250, 'UTF-8') }} </br> <b
                                                style="color:#e74040">{{ $value['cancel_description'][0] ?? '' }}
                                            </b> </br> <b
                                                style="color:#e74040">{{ $value['contract_period'][0] ?? '' }}
                                            </b>
                                            <div class="modal" tabindex="-1" role="dialog"
                                                name="modalinfor_taisan1"
                                                id="more-content-md_taisan1-{{ $value['st_id']}}"
                                                aria-labelledby="modalLabeldanger">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header qkmodel">
                                                            <h5 class="modal-title qkmodel">Thông tin chi tiết
                                                                giao dịch</h5>
                                                        </div>
                                                        <div class="modal-body" style="background-color: white">
                                                            <span class="qktrang" style="color: black">
                                                                {!! $texte !!} </br> <b
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
                                                onclick="showtaisan1('{{ $value['st_id'] }}')">
                                                <i id="search-icon2-{{ $value['st_id'] }}"
                                                    class="fa fa-search-plus fa-2x  text-primary">
                                                </i>
                                            </span>
                                        @else
                                            {{ $value['texte'][0] ?? '' }} </br> <b
                                                style="color:#e74040">{{ $value['cancel_description'][0] ?? '' }}
                                            </b> </br> <b
                                                style="color:#e74040">{{ $value['contract_period'][0] ?? '' }}
                                            </b>
                                        @endif
                                    </td>
                                    @if ($value[0]['st_id'] ?? '')
                                        <td class="so_hd3"> 
                                            <!-- <a class="btn btn-link"
       href="{{ route('doCancelEdit', ['id' => $value['st_id'] ?? '']) }}">
        <b>{{ $value['so_hd'][0] ?? '' }}</b>
    </a> -->

    <a class="btn btn-link f"
       href="{{ route('doCancelEdit', ['id' => $value['st_id'] ?? '' ]) }}">
        <b>{{ $value['so_hd'][0] ?? '' }}</b>
    </a>


</br> {{ $value['ten_hd'][0] ?? '' }} </br> <b
                                                style="color:#e74040;text-align:center;">
                                                {{ $value['deleted_note'][0] ?? '' }} </b>
                                            @if ($value['undisputed_date'])
                                                </br> <b style="color:#e74040"> Ngày giải
                                                    chấp:{{ \Illuminate\Support\Carbon::parse($value['undisputed_date'][0])->format('d/m/Y') }}</b>
                                                </br> <b style="color:#e74040;text-align:center">
                                                    {{ $value['deleted_note'][0] ?? '' }} </b>
                                            @else
                                            @endif
                                        </td>
                                    @else
                                        <td class="so_hd3"> <b>{{ $value['so_hd'][0] ?? '' }}</b> </br>
                                            {{ $value['ten_hd'][0] ?? '' }} </br><b
                                                style="color:#e74040;text-align:center">
                                                {{ $value['deleted_note'][0] ?? '' }} </b></td>
                                    @endif
                                    <td>{{ $value['vp_master'][0] ?? '' }} </br> <b>
                                            {{ $value['ccv_master'][0] ?? '' }}</b></td>
                                    <td>
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
                                        <!-- @if ($role == 'admin' || $role == 'chuyen-vien-so')
<div class="row">
                                                                        @if ($value['st_id'])
<a href="{{ route('editSuutraSTP', ['id' => $value['st_id'] ?? '']) }}"
                                                                           class="button button-circle button-mid button-primary">
                                                                            <i class="fa fa-pencil-square-o"></i>
                                                                        </a>
@endif
@else
@if ($value['st_id'])
<a href="{{ route('editSuutra', ['id' => $value['st_id'] ?? '']) }}"
                                                                           class="button button-circle button-mid button-primary">
                                                                            <i class="fa fa-pencil-square-o"></i>
                                                                        </a>
@endif
@endif
                                                                    </div> -->
                                    </td>
                                    <td></td>
                                @endif
                            </tr>
                            <!-- Cảnh báo tab3 -->
                        @elseif ($value['ngan_chan'] && $value['ngan_chan'][0] == 2)
                            <tr>
                                @if ($value['sync_code'][0] ?? '' == $code_cn)
                                    @if ($value['ma_phan_biet'] && $value['ma_phan_biet'][0] == 'D')
                                        <td style="color: #149B5E">
                                            <b>{{ \Carbon\Carbon::parse($value['created_at'][0])->format('H:i:s d/m/Y') ?? '' }}</b>
                                        </td>
                                    @else
                                        <td style="color: #149B5E">
                                            @if ($value['st_id'] > 1000)
                                            <b>{{ \Carbon\Carbon::parse($value['created_at'][0])->addHour(7)->format('H:i:s d/m/Y') ?? '' }}</b>
                                            @else
                                            <b>{{ \Carbon\Carbon::parse($value['created_at'][0])->format('H:i:s d/m/Y') ?? '' }}</b>
                                            @endif
                                        </td>
                                    @endif
                                    <td style="color: #149B5E">
                                        <b>{{ \Carbon\Carbon::parse($value['ngay_cc'][0])->format('d/m/Y') }}
                                        </b>
                                    </td>
                                    <td class="duong_su_3" style="color: #149B5E">

                                        @if (strlen($value['duong_su'][0] ?? '') > 250)
                                            {!! $duong_su_cut !!}
                                            <div class="modal" tabindex="-1" role="dialog" name="modalinfor1"
                                                id="more-content-md1-{{ $value['st_id'] }}"
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
                                                onclick="showinfo1('{{ $value['st_id'] }}')">
                                                <i id="search-icon2-{{ $value['st_id'] }}"
                                                    class="fa fa-search-plus fa-2x  text-primary">
                                                </i>
                                            </span>
                                        @else
                                            {!! $duong_su !!}
                                        @endif
                                    </td>
                                    <td class="tai_san_3" style="color: #149B5E">
                                        @if (strlen($value['texte'][0] ?? '') > 250)
                                            {{ mb_substr($value['texte'][0] ?? '', 0, 250, 'UTF-8') }} </br>
                                            <b style="color:#e74040">{{ $value['cancel_description'][0] ?? '' }}
                                            </b> </br> <b
                                                style="color:#e74040">{{ $value['contract_period'][0] ?? '' }}
                                            </b>
                                            <div class="modal" tabindex="-1" role="dialog"
                                                name="modalinfor_taisan1"
                                                id="more-content-md_taisan1-{{ $value['st_id'] }}"
                                                aria-labelledby="modalLabeldanger">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header qkmodel">
                                                            <h5 class="modal-title qkmodel">Thông tin chi tiết
                                                                giao dịch</h5>
                                                        </div>
                                                        <div class="modal-body" style="background-color: white">
                                                            <span class="qktrang" style="color: black">
                                                                {!! $texte !!} </br> <b
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
                                                onclick="showtaisan1('{{ $value['st_id'] }}')">
                                                <i id="search-icon2-{{ $value['st_id'] }}"
                                                    class="fa fa-search-plus fa-2x  text-primary">
                                                </i>
                                            </span>
                                        @else
                                            {{ $value['texte'][0] ?? '' }} </br> <b
                                                style="color:#e74040">{{ $value['cancel_description'][0] ?? '' }}
                                            </b> </br> <b
                                                style="color:#e74040">{{ $value['contract_period'][0] ?? '' }}
                                            </b>
                                        @endif
                                    </td>
                                    @if ($value['st_id'] ?? '')
                                        <td class="so_hd3" style="color: #149B5E"> <a class='btn btn-link'
                                                href="{{ route('doCanCelEdit', ['id' => $value['st_id'] ?? '']) }}">
                                                <b>{{ $value['so_hd'][0] ?? '' }}</b>
                                            </a> </br> {{ $value['ten_hd'][0] ?? '' }} </br> <b
                                                style="color:#e74040;text-align:center;">
                                                {{ $value['deleted_note'][0] ?? '' }} </b>
                                            @if ($value['undisputed_date'])
                                                </br> <b style="color:#e74040"> Ngày giải
                                                    chấp:{{ \Illuminate\Support\Carbon::parse($value['undisputed_date'][0])->format('d/m/Y') }}</b>
                                                </br> <b style="color:#e74040;text-align:center">
                                                    {{ $value['deleted_note'][0] ?? '' }} </b>
                                            @else
                                            @endif
                                        </td>
                                    @else
                                        <td class="so_hd3" style="color: #149B5E">
                                            <b>{{ $value['so_hd'][0] ?? '' }}</b> </br>
                                            {{ $value['ten_hd'][0] ?? '' }} </br><b
                                                style="color:#e74040;text-align:center">
                                                {{ $value['deleted_note'][0] ?? '' }} </b></td>
                                    @endif
                                    <td style="color: #149B5E"> {{ $value['vp_master'][0] ?? '' }} </br> <b>
                                            {{ $value['ccv_master'][0] ?? '' }}</b></td>
                                    <td style="color: #149B5E">
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
                                        <!-- @if ($role == 'admin' || $role == 'chuyen-vien-so')
<div class="row">
                                                                @if ($value['st_id'])
<a href="{{ route('editSuutraSTP', ['id' => $value['st_id'] ?? '']) }}"
                                                                   class="button button-circle button-mid button-primary">
                                                                    <i class="fa fa-pencil-square-o"></i>
                                                                </a>
@endif
@else
@if ($value['st_id'])
<a href="{{ route('editSuutra', ['id' => $value['st_id'] ?? '']) }}"
                                                                   class="button button-circle button-mid button-primary">
                                                                    <i class="fa fa-pencil-square-o"></i>
                                                                </a>
@endif
@endif
                                                            </div> -->
                                    </td>
                                    <td></td>
                                @endif
                            </tr>
                            <!-- End cảnh báo tab3 -->
                        @else
                            <tr>
                                @if ($value['sync_code'][0] ?? '' == $code_cn)
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
                                    <td>
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

                                    <td class="duong_su_3">

                                        @if (strlen($value['duong_su'][0] ?? '') > 250)
                                            {!! $duong_su_cut !!}
                                            <div class="modal" tabindex="-1" role="dialog" name="modalinfor1"
                                                id="more-content-md1-{{ $value['st_id'] }}"
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
                                                onclick="showinfo1('{{ $value['st_id'] }}')">
                                                <i id="search-icon2-{{ $value['st_id'] }}"
                                                    class="fa fa-search-plus fa-2x  text-primary">
                                                </i>
                                            </span>
                                        @else
                                            {!! $duong_su !!}
                                        @endif
                                    </td>
                                    <td class="tai_san_3">
                                        @if (strlen($value['texte'][0] ?? '') > 250)
                                            {{ mb_substr($value['texte'][0] ?? '', 0, 250, 'UTF-8') }} </br>
                                            <b style="color:#e74040">{{ $value['cancel_description'][0] ?? '' }}
                                            </b> </br> <b
                                                style="color:#e74040">{{ $value['contract_period'][0] ?? '' }}
                                            </b>
                                            <div class="modal" tabindex="-1" role="dialog"
                                                name="modalinfor_taisan1"
                                                id="more-content-md_taisan1-{{ $value['st_id'] }}"
                                                aria-labelledby="modalLabeldanger">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header qkmodel">
                                                            <h5 class="modal-title qkmodel">Thông tin chi tiết
                                                                giao dịch</h5>
                                                        </div>
                                                        <div class="modal-body" style="background-color: white">
                                                            <span class="qktrang" style="color: black">
                                                                {!! $texte !!} </br> <b
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
                                                onclick="showtaisan1('{{ $value['st_id'] }}')">
                                                <i id="search-icon2-{{ $value['st_id'] }}"
                                                    class="fa fa-search-plus fa-2x  text-primary">
                                                </i>
                                            </span>
                                        @else
                                            {{ $value['texte'][0] ?? '' }} </br> <b
                                                style="color:#e74040">{{ $value['cancel_description'][0] ?? '' }}
                                            </b> </br> <b
                                                style="color:#e74040">{{ $value['contract_period'][0] ?? '' }}
                                            </b>
                                        @endif
                                    </td>
                                    @if ($value['st_id'] ?? '')
                                        <td class="so_hd3"> 
                                           
                                          <a class="btn btn-link"
       href="{{ route('doCanCelEdit', ['id' => $value['st_id'] ?? '' ]) }}">
        <b>{{ $value['so_hd'][0] ?? '' }}</b>
    </a>
                                        </br> {{ $value['ten_hd'][0] ?? '' }} </br> <b
                                                style="color:#e74040;text-align:center;">
                                                {{ $value['deleted_note'][0] ?? '' }} </b>
                                            @if ($value['undisputed_date'])
                                                </br> <b style="color:#e74040"> Ngày giải
                                                    chấp:{{ \Illuminate\Support\Carbon::parse($value['undisputed_date'][0])->format('d/m/Y') }}</b>
                                                </br> <b style="color:#e74040;text-align:center">
                                                    {{ $value['deleted_note'][0] ?? '' }} </b>
                                            @else
                                            @endif
                                        </td>
                                    @else
                                        <td class="so_hd3"> <b>{{ $value['so_hd'][0] ?? '' }}</b> </br>
                                            {{ $value['ten_hd'][0] ?? '' }} </br><b
                                                style="color:#e74040;text-align:center">
                                                {{ $value['deleted_note'][0] ?? '' }} </b></td>
                                    @endif
                                    <td>{{ $value['vp_master'][0] ?? '' }} </br> <b>
                                            {{ $value['ccv_master'][0] ?? '' }}
                                        
                                        </b></td>
                                    <td>
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
                                        <!-- @if ($role == 'admin' || $role == 'chuyen-vien-so')
<div class="row">
                                                                            @if ($value['st_id'])
<a href="{{ route('editSuutraSTP', ['id' => $value['st_id'] ?? '']) }}"
                                                                               class="button button-circle button-mid button-primary">
                                                                                <i class="fa fa-pencil-square-o"></i>
                                                                            </a>
@endif
@else
@if ($value['st_id'])
<a href="{{ route('editSuutra', ['id' => $value['st_id'] ?? '']) }}"
                                                                               class="button button-circle button-mid button-primary">
                                                                                <i class="fa fa-pencil-square-o"></i>
                                                                            </a>
@endif
@endif
                                                                        </div> -->
                                    </td>
                                    <td></td>
                                @endif
                            </tr>
                        @endif
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
</div>
</div>