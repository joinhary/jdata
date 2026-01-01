@extends('admin/layouts/default')
@section('title')
    Quản lý đương sự    @parent
@stop
@section('header_styles')
    <meta name="_token" content="{{ csrf_token() }}">
    <link href="{{ asset('assets/css/pages/toastr.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/vendors/toastr/css/toastr.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/vendors/datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet"
          type="text/css"/>
    <link href="{{ asset('assets/vendors/jasny-bootstrap/css/jasny-bootstrap.css') }}" rel="stylesheet"
          type="text/css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css"/>
    <link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/vendors/select2/css/select2-bootstrap.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/vendors/selectize/css/selectize.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/vendors/selectize/css/selectize.bootstrap3.css') }}" rel="stylesheet"/>
    <script src="{{asset('assets\js\jquery-3.3.1.min.js')}}"></script>
    <link rel="stylesheet" href="{{asset('assets\css\jquery.fancybox.css')}} "/>
    <script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
    <script type="text/javascript" src="{{ asset('assets/js/pages/gallery.js') }}"></script>

    <style>
        .text-bold {
            font-weight: bold;
        }

        .qkmodel {
            background-color: #1a67a3 !important;
        }

        .qkbtn {
            font-weight: bold;
            font-size: 14px !important;
        }
    </style>
@stop
@section('content')
    <section class="content">
        <div class="row bctk-scrollable-list" style="overflow-x: hidden;height: calc(100vh - 100px) !important;">
            <div class="col-md-12">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <i class="fa fa-user"></i> Cập nhật đương sự
                        </h4>
                    </div>
                    <form action="{{route('updateKhachHang',['id' => $account->id])}}" enctype="multipart/form-data"
                          method="POST">
                        <div class="panel-body">
                            {{csrf_field()}}
                            <input id="old-cmnd" name="old-cmnd" hidden>
                            <div class="row">
                                <div class="col-md-8 border-right-custom">
                                    <div class="form-group row">
                                        <div class="col-md-3 text-right">
                                            <label for="note" class="text-bold">Lý do cập nhật:</label>
                                        </div>
                                        <div class="col-md-9">
                                            <input class="form-control" type="text" id="note" name="note" required>
                                        </div>
                                    </div>
                                    @foreach($khachhang as $kh)
                                        <div id="row-{{$kh->tm_keywords}}" class="form-group row">
                                            <div class="col-md-3 text-right">
                                                <label for="{{$kh->tm_keywords}}" class="text-bold">{{$kh->tm_nhan}}
                                                    :</label>
                                                @if($kh->tm_loai == 'file')
                                                    <br><span class="text-warning"><i
                                                            class="fa fa-exclamation-triangle"></i> Chỉ chọn lại ảnh khi cần cập nhật!</span>
                                                @endif
                                                <input type="text" name="ds_tm[]" value="tm-{{$kh->tm_id}}" hidden>
                                            </div>

                                            <div
                                                class=" {{ ($kh->tm_keywords == 'tinh-trang-hon-nhan') ? 'col-md-4' : 'col-md-9'}}">
                                                @if($kh->tm_loai == "text")
                                                    <input id="{{$kh->tm_keywords}}" type="text"
                                                           name="tm-{{$kh->tm_id}}"
                                                           class="form-control"
                                                           value="{{$kh->kh_giatri}}"
                                                           @if($kh->tm_batbuoc == 1) required @endif>
                                                @elseif($kh->tm_loai == "select")
                                                    <?php
                                                    if ($kh->tm_keywords != 'hon-phoi') {
                                                        $select = \App\Models\KieuTieuMucModel::where('tm_id', $kh->tm_id)
                                                            ->where('ktm_status', 1)
                                                            ->pluck('ktm_traloi', 'ktm_id');
                                                        $readonly = '';
                                                    } else {
                                                        if ($honphoi) {
                                                            $select = $honphoi;
                                                        }
                                                        $readonly = 'readonly';
                                                    }

                                                    ?>
                                                    {!! \App\Helpers\Form::select('tm-'.$kh->tm_id,$select,$kh->kh_giatri,['class'=>'form-control'
                                                    ,
                                                    'id'=>$kh->tm_keywords,
                                                    'onchange'=>'change_tm(this,\'normal\')',
                                                    $readonly
                                                    ]) !!}
                                                @elseif($kh->tm_loai == 'file')
                                                    <input id="{{$kh->tm_keywords}}" name="tm-{{$kh->tm_id}}[]"
                                                           type="file"
                                                           accept="image/*" class="form-control"
                                                           onchange="loadImgKH1(this)"
                                                           @if($kh->tm_batbuoc == 1) required @endif multiple/>
                                                    <div class="row text-center row-image"
                                                         id="img-{{$kh->tm_keywords}}">
                                                        <?php
                                                        $images = json_decode($kh->kh_giatri);
                                                        ?>
                                                        @if($images)
                                                            @foreach($images as $img)
                                                                <div class="col-md-2 mb-2 mt-1">
                                                                    <a data-fancybox="images"
                                                                       href="{{url('images/khachhang/'.$img)}}">
                                                                        <img src="{{url('images/khachhang/'.$img)}}"
                                                                             width="50"
                                                                             height="50">
                                                                    </a>
                                                                </div>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                @else
                                                    <input type="text" id="{{$kh->tm_keywords}}" class="form-control"
                                                           name="tm-{{$kh->tm_id}}" data-mask="99/99/9999"
                                                           placeholder="Ngày / tháng / năm"
                                                           value="{{$kh->kh_giatri}}"
                                                           @if($kh->tm_batbuoc == 1) required @endif>
                                                @endif
                                            </div>
                                            @if($kh->tm_keywords == 'tinh-trang-hon-nhan')
                                                <a href="javascript:void(0)" id="btn-honphoi" class="btn btn-primary"
                                                   onclick="addRelation()" disabled>Thêm Chồng/Vợ</a>
                                            @endif
                                        </div>
                                    @endforeach
                                    @foreach($tieumuc as $tm)
                                        @if(!in_array($tm->tm_id, $kh_arr))
                                            <div class="form-group row">
                                                <div class="col-md-3 fit-column-height text-right">

                                                    <label for="{{$tm->tm_keywords}}">{{$tm->tm_nhan}}:</label>
                                                    <input type="text" name="ds_tm[]" class="ds_tm"
                                                           value="tm-{{$tm->tm_id}}"
                                                           hidden>
                                                </div>
                                                <div class="col-md-9">
                                                    @if($tm->tm_loai == "text")
                                                        <input id="{{$tm->tm_keywords}}" type="text"
                                                               name="tm-{{$tm->tm_id}}"
                                                               class="form-control"
                                                               @if($tm->tm_batbuoc == 1) required @endif>
                                                        <span id="valid-{{$tm->tm_keywords}}"
                                                              class="text-small text-danger pl-1 pt-1"></span>
                                                    @elseif($tm->tm_loai == "select")
                                                        <?php
                                                        $select = \App\Models\KieuTieuMucModel::where('tm_id', $tm->tm_id)
                                                            ->where('ktm_status', 1)
                                                            ->pluck('ktm_traloi', 'ktm_id');
                                                        ?>
                                                        {!! \App\Helpers\Form::select('tm-'.$tm->tm_id,$select,'',['class'=>'form-control'
                                                        ,
                                                        'id'=>$tm->tm_keywords,
                                                        'onchange'=>'change_tm(this,\'normal\')'
                                                        ]) !!}
                                                    @elseif($tm->tm_loai == 'file')

                                                    @else
                                                        <input type="text" id="{{$tm->tm_keywords}}"
                                                               class="form-control"
                                                               name="tm-{{$tm->tm_id}}" data-mask="99/99/9999"
                                                               placeholder="Ngày / tháng / năm"
                                                               @if($tm->tm_batbuoc == 1) required @endif>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                    <div class="form-group row text-center">
                                        <button type="submit" id="sub-kh" onclick="block()"
                                                class="btn btn-primary mr-2">Lưu
                                        </button>
                                        <a href="{{route('indexKhachHang')}}" class="btn btn-warning">Hủy</a>
                                    </div>
                                    <input type="text" id="full-addr" name="address" value="{{$account->address}}"
                                           hidden>
                                    <input type="text" id="kh-id" name="kh_id" value="{{$account->id}}" hidden>
                                    <input type="text" id="k-id" name="k_id" value="{{$account->k_id}}" hidden>
                                    <input type="text" id="ds2_nhan" name="ds2_nhan" hidden>
                                    <input type="text" id="contact" name="contact" value="{{$account->phone}}" hidden>
                                </div>
                                <div class="col-md-4 pr-0">
                                    <div class="panel panel-info">
                                        <div class="panel-heading">
                                            <a data-toggle="collapse" href="#lich-su-hon-nhan"><b>Lịch sử hôn nhân</b>
                                                <span class="pull-right"><i
                                                        class="glyphicon glyphicon-chevron-down panel-collapsed showhide clickable"></i></span>
                                            </a>
                                        </div>
                                        <div id="lich-su-hon-nhan" class="panel-body collapse show">
                                            @if($lichsuhonnhan->isNotEmpty())
                                                <table class="table table-bordered table-hover mb-0">
                                                    <thead>
                                                    <tr>
                                                        <th>Nhãn đương sự</th>
                                                        <th>Tình trạng</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($lichsuhonnhan as $ls)
                                                        <tr>
                                                            <td style="width: 70%; vertical-align: middle;">
                                                                <a href="javascript:void(0)" id="{{$ls->ds2_id}}"
                                                                   data-toggle="tooltip"
                                                                   data-placement="bottom" title="Nhấp để xem chi tiết"
                                                                   onclick="getDetailKH(this)">{{$ls->first_name}}</a>
                                                            </td>
                                                            <td style="vertical-align: middle;">{{$ls->tinhtrang}}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            @else
                                                Không có thông tin
                                            @endif
                                        </div>
                                    </div>
                                    <div hidden class="form-group row">
                                        <div class="form-group row">
                                            <label class="text-bold" for="pic">Ảnh đại diện:</label><br>
                                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                                <a data-fancybox="images"
                                                   href="{{url('assets/images/authors/'.$account->pic)}}">
                                                    <div class="fileinput-new thumbnail"
                                                         style="width: 225px; height: 225px;">
                                                        <img src="{{url('assets/images/authors/'.$account->pic)}}"
                                                             alt="profile pic">
                                                    </div>
                                                </a>
                                                <div class="fileinput-preview fileinput-exists thumbnail"
                                                     style="max-width: 250px; max-height: 250px;"></div>
                                                <div>
                                                <span class="btn btn-primary btn-file">
                                                    <span class="fileinput-new">Chọn ảnh</span>
                                                    <span class="fileinput-exists">Thay đổi</span>
                                                    <input id="pic" name="pic" type="file" class="form-control"/>
                                                </span>
                                                    <a href="#" class="btn btn-danger fileinput-exists"
                                                       data-dismiss="fileinput">Gỡ bỏ</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-12">
                                            <label class="text-bold" for="username">Tài khoản:</label>
                                            <input type="text" id="usernameChange" class="form-control"
                                                   name="usernameChange"
                                                   value="{{$account->username}}" >
                                        </div>
                                        <div class="form-group col-12">
                                            <label class="text-bold" for="password">Mật khẩu(Nếu không có thay đổi vui
                                                lòng để
                                                trống):</label>
                                            <input type="text" id="passwordChange"
                                                   class="form-control password-validate"
                                                   name="passwordChange">
                                        </div>
                                        <div class="form-group col-12">
                                            <label class="text-bold" for="first_name">Nhãn:</label>
                                            <input type="text" id="first_nameChange" class="form-control"
                                                   name="first_nameChange"
                                                   value="{{$account->nhan}}" >
                                        </div>
                                        <div class="form-group col-12">
                                            <label class="text-bold">
                                                @if($activation)
                                                    <input type="checkbox" name="activate" checked class="square"/> Kích
                                                    hoạt tài
                                                    khoản
                                                @else
                                                    <input type="checkbox" name="activate" class="square"/> Kích hoạt
                                                    tài khoản
                                                @endif
                                            </label>
                                        </div>


                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade in" id="modal-honphoi" tabindex="-1" role="dialog" aria-hidden="false">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header qkmodel">
                        <h4 class="modal-title qkmodel">Thông tin hôn phối của <span id="main-ds"></span></h4>
                    </div>
                    <div class="modal-body">
                        <form id="form-honphoi">
                            {{csrf_field()}}
                            <div class="row">
                                @foreach($tieumuc as $tm)
                                    <div
                                        class="col-md-6 col-xs-12 {{$tm->tm_keywords == 'tinh-trang-hon-nhan' ? 'hidden' : ''}}">
                                        <div class="form-group">
                                            <label class="text-bold"
                                                   for="modal-ele-{{$tm->tm_keywords}}">{{$tm->tm_nhan}}:</label>
                                            <input type="text" name="ds_tm[]" value="tm-{{$tm->tm_id}}" hidden>
                                            @if($tm->tm_loai == "text")
                                                <input id="modal-ele-{{$tm->tm_keywords}}" type="text"
                                                       name="tm-{{$tm->tm_id}}" class="form-control"
                                                       @if($tm->tm_batbuoc == 1) required @endif>
                                                {{--<span id="modal-ele-{{$tm->tm_keywords}}-valid" class="text-small text-danger pl-1 pt-1 hidden"></span>--}}
                                            @elseif($tm->tm_loai == "select")
                                                <?php
                                                $select = \App\Models\KieuTieuMucModel::where('tm_id', $tm->tm_id)
                                                    ->where('ktm_status', 1)
                                                    ->pluck('ktm_traloi', 'ktm_id');
                                                ?>
                                                {!! \App\Helpers\Form::select('tm-'.$tm->tm_id,$select,'',['class'=>'form-control'
                                                ,
                                                'id'=>'modal-ele-'.$tm->tm_keywords,
                                                'onchange'=>'change_tm(this,\'in-modal\')'
                                                ]) !!}
                                            @elseif($tm->tm_loai == 'file')
                                                <input id="modal-ele-{{$tm->tm_keywords}}" name="tm-{{$tm->tm_id}}[]"
                                                       type="file" accept="image/*" class="form-control"
                                                       onchange="loadImgKH(this,'modal')"
                                                       @if($tm->tm_batbuoc == 1) required @endif multiple/>
                                                <div class="row text-center row-image"
                                                     id="modal-images-{{$tm->tm_keywords}}"></div>

                                            @else
                                                <input type="text" id="modal-ele-{{$tm->tm_keywords}}"
                                                       class="form-control" name="tm-{{$tm->tm_id}}"
                                                       data-mask="99/99/9999"
                                                       placeholder="Ngày / tháng / năm"
                                                       @if($tm->tm_batbuoc == 1) required @endif>
                                            @endif

                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="row">
                                <hr>
                            </div>
                            <div hidden class="row">
                                <div class="col-md-4 col-xs-12">
                                    <div class="form-group row">
                                        <label class="text-bold" for="pic">Ảnh đại diện:</label><br>
                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                            <div class="fileinput-new thumbnail" style="width: 225px; height: 225px;">
                                                <img src="{{url('/images/new-user.png')}}" alt="profile pic">
                                            </div>
                                            <div class="fileinput-preview fileinput-exists thumbnail"
                                                 style="max-width: 250px; max-height: 250px;"></div>
                                            <div>
                                                <span class="btn btn-primary btn-file">
                                                    <span class="fileinput-new">Chọn ảnh</span>
                                                    <span class="fileinput-exists">Thay đổi</span>
                                                    <input id="modal-ele-pic" name="pic" type="file"
                                                           class="form-control"/>
                                                </span>
                                                <a href="#" class="btn btn-danger fileinput-exists"
                                                   data-dismiss="fileinput">Gỡ bỏ</a>
                                            </div>
                                        </div>
                                        <span class="help-block">{{ $errors->first('pic_file', ':message') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-8 col-xs-12">
                                    {{--                                    <div class="form-group row">--}}
                                    <div class="form-group row">
                                        <label class="text-bold" for="modal-ele-username">Tài khoản:</label>
                                        <input type="text" id="modal-ele-username" class="form-control"
                                               name="username" >
                                    </div>
                                    <div class="form-group row">
                                        <label class="text-bold" for="modal-ele-password">Mật khẩu:</label>
                                        <input type="text" id="modal-ele-password"
                                               class="form-control password-validate" name="password" >
                                        <span id="modal-ele-valid-password"
                                              class="text-small text-danger pl-1 pt-1"></span>
                                    </div>
                                    <div class="form-group row">
                                        <label class="text-bold" for="modal-ele-first_name">Nhãn:</label>
                                        <input type="text" id="modal-ele-first_name" class="form-control"
                                               name="first_name" >
                                        <span id="modal-ele-valid-first_name"
                                              class="text-small text-danger pl-1 pt-1"></span>
                                    </div>
                                    <input type="text" id="modal-ele-contact" name="contact" hidden>
                                    <input type="text" id="modal-ele-contact" name="kieu" value="{{$k_id}}" hidden>
                                    {{--                                    </div>--}}
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <a href="javascript:void(0)" id="submit-honphoi" class="btn btn-primary"
                           onclick="submitHonPhoi()">Tạo mới</a>
                        <button type="button" data-dismiss="modal" class="btn btn-default">Hủy bỏ</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade in" id="detail-kh" tabindex="-1" role="dialog" aria-hidden="false">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <a href="javascript:void(0)" class="text-danger pull-right" data-dismiss="modal"
                           aria-hidden="true"><i class="fa fa-times"></i></a>
                        <h4 class="modal-title">Thông tin đương sự "<b><span id="placeToFillLabelDS"></span></b>"</h4>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered table-hover mb-0" id="kh-table">
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
@section('footer_scripts')
    <script src="{{ asset('assets/vendors/moment/js/moment.min.js') }}" type="text/javascript"></script>
    <script language="javascript" type="text/javascript"
            src="{{ asset('assets/vendors/select2/js/select2.js') }}"></script>
    <script src="{{ asset('assets/vendors/jasny-bootstrap/js/jasny-bootstrap.js') }}" type="text/javascript"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/fancybox/jquery.fancybox.pack.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/pages/gallery.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/imgPreview.js') }}"></script>
    <script>

        var select_element = {};
        $.each($('select'), function (k, v) {
            select_element[v.id] = v.value;
        });
        var tt_honnhan_curr = $('#tinh-trang-hon-nhan').val();

        function change_tm(value, type) {

            var curr_val = select_element[value.id];
            var id_selected = '#' + value.id;
            var selected_val = $(id_selected).val();
            if (selected_val != 1) {
                $('#btn-honphoi').removeAttr('disabled');

            } else {
                $('#btn-honphoi').attr('disabled', 'disabled');

            }
            var kh_id = $('#kh-id').val();
            var k_id = $('#k-id').val();
            var fill_id = '';
            var fill_id_prefix = '';
            $.ajax({
                url: "{{route('getTMKHEdit')}}",
                method: "GET",
                data: 'ktm_id=' + selected_val + '&kh_id=' + kh_id + '&k_id=' + k_id + '&current_val=' + curr_val,
                success: function (tieumuc) {
                    $.each(tieumuc.data['tm_rmv'], function (k, v) {
                        $('#row-' + v.tm_keywords).remove();
                    });
                    var current_ele = $(id_selected).parent().parent();
                    tieumuc.data['list_tm'].map(function (val) {
                        // console.log(val);
                        if (type === 'normal') {
                            fill_id_prefix = 'fill_';
                            fill_id = '#' + fill_id_prefix + val.tm_id;
                        } else {
                            fill_id_prefix = 'fill_modal_';
                            fill_id = '#' + fill_id_prefix + val.tm_id;
                        }
                        var kh_giatri = '';
                        var batbuoc = '';
                        if (val.hasOwnProperty('kh_giatri')) {
                            console.log(1);
                            kh_giatri = val.kh_giatri;
                        } else {
                            console.log(0);
                        }
                        if (val.tm_batbuoc === "1") {
                            batbuoc = 'required';
                        }
                        $(fill_id).empty();
                        $('<div class="form-group row add-sub" id="row-' + val.tm_keywords + '">' +
                            '<div class="col-md-3 fit-column-height text-right">' +
                            '<label for="' + val.tm_keywords + '" class="text-bold">' + val.tm_nhan + ':</label>' +
                            '<input type="text" name="ds_tm[]" value="tm-' + val.tm_id + '" hidden>' +
                            '</div>' +
                            '<div class="col-md-9" id="' + fill_id_prefix + val.tm_id + '">' +
                            '</div>'
                        ).insertAfter($(current_ele));
                        if (val.tm_loai === "text") {
                            $(fill_id).append('<input id="' + val.tm_keywords + '" type="text" name="tm-' + val.tm_id + '" class="form-control" value="' + kh_giatri + '">')
                        } else if (val.tm_loai === "select") {
                            var sel_id = '#' + val.tm_keywords;
                            if (val.tm_keywords !== 'hon-phoi') {
                                $(fill_id).append(
                                    '<select id="' + val.tm_keywords + '"   class="form-control"  name="tm-' + val.tm_id + '">' +
                                    '</select>'
                                );
                                $.ajax({
                                    url: "{{route('getTMKHOptions')}}",
                                    type: "GET",
                                    data: 'tm_id=' + val.tm_id,
                                    success: function (options) {
                                        options.data.map(function (opt) {
                                            if (opt.ktm_id === kh_giatri) {
                                                $(sel_id).append(
                                                    '<option value="' + opt.ktm_id + '" selected>' + opt.ktm_traloi + '</option>'
                                                )
                                            } else {
                                                $(sel_id).append(
                                                    '<option value="' + opt.ktm_id + '">' + opt.ktm_traloi + '</option>'
                                                )
                                            }
                                        })
                                    }
                                });
                            } else {
                                if (tt_honnhan_curr === '{{$ktm_kethon}}') {
                                    $(fill_id).append(
                                        '<select id="' + val.tm_keywords + '"   class="form-control readonly" name="tm-' + val.tm_id + '">' +
                                        '</select>'
                                    );
                                    if (val.kh_giatri)
                                        $(sel_id).append(
                                            '<option value="' + Object.keys(val.kh_giatri)[0] + '" selected>' + Object.values(val.kh_giatri)[0] + '</option>'
                                        );

                                } else {
                                    $('#btn-honphoi').removeAttr('readonly');
                                    $(fill_id).append(
                                        '<select id="' + val.tm_keywords + '"  required class="form-control" name="tm-' + val.tm_id + '">' +
                                        '</select>'
                                    );
                                    $(sel_id).select2({
                                        placeholder: "Nhập nhãn đương sự...",
                                        language: {
                                            inputTooShort: function () {
                                                return "Nhập vào nhiều hơn 2 kí tự...";
                                            }
                                        },
                                        minimumInputLength: 2,
                                        ajax: {
                                            url: "{{route('getKHSelect')}}",
                                            dataType: 'json',
                                            data: function (params) {
                                                return {
                                                    q: $.trim(params.term)
                                                };
                                            },
                                            processResults: function (data) {
                                                return {
                                                    results: data
                                                };
                                            },
                                            cache: true
                                        }
                                    });
                                }
                            }
                        } else if (val.tm_loai === "file") {

                            $(fill_id).append(
                                '<input id="' + val.tm_keywords + '" name="tm-' + val.tm_id + '[]" type="file" ' +
                                'accept="image/*" class="form-control" onchange="loadImgKH(this,\'normal\')" multiple ' + batbuoc + '/>' +
                                '<div class="row text-center row-image" id="images-' + val.tm_keywords + '">' +
                                '</div>'
                            );
                            if (val.kh_giatri) {
                                var imgs = JSON.parse(val.kh_giatri);
                                if (imgs) {
                                    $.each(imgs, function (kimg, vimg) {
                                        var urlIMG = "{{url('images/khachhang')}}" + "/" + vimg;
                                        $('#images-' + val.tm_keywords).append(
                                            '<div class="col-md-2 mb-2 mt-1">' +
                                            '<a data-fancybox="images"    href="' + urlIMG + '">' +
                                            '<img src="' + urlIMG + '" width="50" height="50">' +
                                            '</a>' +
                                            '</div>'
                                        )
                                    });
                                }
                            }
                        } else {
                            $(fill_id).append(
                                '<input type="text" id="' + val.tm_keywords + '" class="form-control" name="tm-' + val.tm_id + '" data-mask="99/99/9999" placeholder="Ngày / tháng / năm" value=" ' + kh_giatri + '">'
                            )
                        }
                        current_ele = '#row-' + val.tm_keywords;
                    })
                }
            });
            select_element[value.id] = selected_val;
        }

        $('#old-cmnd').val($("#giay-to-tuy-than-so").val());

        function addRelation() {
            $('#main-ds').text($('#first_name').val());
            $('#modal-honphoi').modal('show');
        }

        function getDetailKH(ele) {
            var kh_id = ele.id;
            var urlGet = '{{url('admin/khachhang/show')}}' + '/' + kh_id;
            $.ajax({
                url: urlGet,
                type: 'GET',
                success: function (res) {
                    $('#placeToFillLabelDS').text(res.data['nhan']);
                    $.each(res.data['khachhang'], function (k, v) {
                        var giatri = '';
                        if (v.tm_loai !== 'file') {
                            if (v.kh_giatri) {
                                giatri = v.kh_giatri;
                            }
                            $('#kh-table').append(
                                '<tr>' +
                                '<td class="fit-column-kh">' + v.tm_nhan +
                                '</td>' +
                                '<td class="text-left">' + giatri +
                                '</td>' +
                                '</tr>'
                            );
                        }
                    });
                    $('#detail-kh').modal('show');
                }
            })
        }

        $('input:required').focusout(function () {
            $('#' + $(this).attr('id')).tooltip({
                title: 'Vui lòng không để trống!',
                placement: 'bottom',
                trigger: 'manual'
            });
            if (!$(this).val()) {
                $(this).css('border', '1px solid red');
                $('#' + $(this).attr('id')).tooltip("show");
            } else {
                $(this).removeAttr('style', 'border');
                $('#' + $(this).attr('id')).tooltip("hide");
            }
        });

        $('#dien-thoai').focusout(function () {
            $('#contact').val($(this).val());
        });

        $('#hon-phoi').select2({
            placeholder: "Nhập nhãn đương sự...",
            minimumInputLength: 2,
            ajax: {
                url: "{{route('getKHSelect')}}",
                dataType: 'json',
                data: function (params) {
                    return {
                        q: $.trim(params.term)
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: true
            }
        });

        $('#modal-ele-giay-to-tuy-than-so').focusout(function () {
            var so_dinh_danh = $(this).val();
            if (so_dinh_danh) {
                $.ajax({
                    url: "{{route('validCMND')}}",
                    type: "GET",
                    data: 'kh_giatri=' + so_dinh_danh,
                    success: function (err) {
                        $('#modal-ele-giay-to-tuy-than-so').tooltip({
                            title: err.message,
                            placement: 'bottom',
                            trigger: 'manual'
                        });
                        if (err.status === 'error') {
                            $('#modal-ele-giay-to-tuy-than-so').css('border', '1px solid red');
                            $('#modal-ele-giay-to-tuy-than-so').tooltip('show');
                        } else {
                            $('#modal-ele-giay-to-tuy-than-so').removeAttr('style', 'border');
                            $('#modal-ele-giay-to-tuy-than-so').tooltip('hide');
                        }
                    }
                });
            }
			 if ($('#modal-ele-ho-duong-su').val() !== '' && $('#modal-ele-ten-duong-su').val() !== '') {
                var first_name = $('#modal-ele-ho-duong-su').val() + ' ' + $('#modal-ele-ten-duong-su').val() + ' ' + $(this).val();
			 
			$('#modal-ele-first_name').val(first_name);
       }
            $('#modal-ele-username').val($(this).val()+Math.floor(Math.random() * 999999));
            $('#modal-ele-password').val(Math.floor(Math.random() * 999999) + 100000);
           
        });

        $('#modal-ele-dien-thoai').focusout(function () {
            $('#modal-ele-contact').val($(this).val());
        });

        function submitHonPhoi() {
            var dataForm = new FormData($('#form-honphoi')[0]);
            $.ajax({
                url: "{{route('storeKhachHang')}}",
                type: 'post',
                processData: false,
                contentType: false,
                data: dataForm,
                success: function (res) {
                    if (res.status === 'success') {
                        msgSuccess(res.message);
                        $('#hon-phoi').val(res.data);
                        $('#hon-phoi').append(
                            '<option value="' + res.data + '">' + $('#modal-ele-first_name').val() + '</option>'
                        )
                        $('#modal-honphoi').modal('hide');

                    } else {
                        $.each(res.message, function (k, v) {
                            msgError(v);
                        })
                    }
                }
            })
        }

    </script>
@stop

