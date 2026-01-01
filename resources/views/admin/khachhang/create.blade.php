@extends('admin/layouts/default')
@section('title')
    Quản lý đương sự    @parent
@stop
@section('header_styles')
    <meta name="_token" content="{{ csrf_token() }}">
    <link href="{{ asset('assets/css/pages/toastr.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/vendors/toastr/css/toastr.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/vendors/jasny-bootstrap/css/jasny-bootstrap.css') }}" rel="stylesheet"
          type="text/css"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/fancybox/jquery.fancybox.css') }}"
          media="screen"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/fancybox/jquery.fancybox-thumbs.css') }}"/>
    <link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/vendors/select2/css/select2-bootstrap.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/vendors/selectize/css/selectize.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/vendors/selectize/css/selectize.bootstrap3.css') }}" rel="stylesheet"/>
    <script src="{{asset('assets/js/jquery-3.3.1.min.js')}}"></script>
    <script src="{{asset('assets/js/jquery.fancybox.js')}}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/pages/gallery.js') }}"></script>
    <link rel="stylesheet" href="{{asset('assets/css/jquery.fancybox.css')}} "/>
    <style>
        .text-bold {
            font-weight: bold;
        }

        .form-group {
            margin-bottom: .5em;
        }

        .row-image {
            height: 100px !important;
        }

        .qksao {
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
                            <i class="fa fa-user"></i> Đương sự mới
                        </h4>
                    </div>
                    <form action="{{route('storeKhachHang')}}" enctype="multipart/form-data" method="POST">
                        @csrf
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-10 border-right-custom">
                                    <?php
                                    $id_hinh = [];
                                    ?>
                                    @foreach($tieumuc as $tm)
                                        <div class="form-group row">
                                            <div class="col-md-3 fit-column-height text-right">
                                                <label class="text-bold">{{$tm->tm_nhan}}:</label>
                                                <input type="text" name="ds_tm[]" value="tm-{{$tm->tm_id}}" hidden>
                                            </div>
                                            <div class="{{ ($tm->tm_keywords == 'tinh-trang-hon-nhan') ? 'col-md-4' : 'col-md-9'}}">
                                                @if($tm->tm_loai == "text")
                                                    <input id="{{$tm->tm_keywords}}" type="text" name="tm-{{$tm->tm_id}}" class="form-control" @if($tm->tm_batbuoc == 1) required @endif>
                                                @elseif($tm->tm_loai == "select")
                                                    <?php
                                                    $select = \App\Models\KieuTieuMucModel::where('tm_id', $tm->tm_id)
                                                        ->where('ktm_status', 1)
                                                        ->pluck('ktm_traloi', 'ktm_id');
                                                    ?>
                                                    {!! \App\Helpers\Form::select('tm-'.$tm->tm_id,$select,'',['class'=>'form-control','id'=>$tm->tm_keywords,'onchange'=>'change_tm(this,\'normal\')']) !!}
                                                @elseif($tm->tm_loai == 'file')
                                                    <input id="{{$tm->tm_keywords}}" name="tm-{{$tm->tm_id}}[]" type="file" accept="image/*" class="form-control" onchange="loadImgKH1(this)" @if($tm->tm_batbuoc == 1) required @endif multiple/>
                                                    <div class="row text-center row-image" data-provides="fileinput" id="img-{{$tm->tm_keywords}}"></div>
                                                @else
                                                    <input type="text" id="{{$tm->tm_keywords}}" class="form-control" name="tm-{{$tm->tm_id}}" data-mask="99/99/9999" placeholder="Ngày / tháng / năm" @if($tm->tm_batbuoc == 1) required @endif>
                                                @endif
                                            </div>
                                            @if($tm->tm_keywords == 'tinh-trang-hon-nhan')
                                                <a href="javascript:void(0)" id="btn-honphoi" class="btn btn-primary" onclick="addRelation()" disabled>Thêm Chồng/Vợ</a>
                                            @endif
                                        </div>
                                    @endforeach
                                    <div class="col-md-3 fit-column-height text-right">
                                        <button type="submit" id="sub-kh" class="btn btn-primary qkbtn">Lưu</button>
                                        <a href="{{route('indexKhachHang')}}" class="btn btn-secondary qkbtn">Hủy</a>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div hidden class="form-group row">
                                        <label class="text-bold" for="pic">Ảnh đại diện:</label><br>
                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                            <a data-fancybox="images" href="{{url('/images/new-user.png')}}">
                                                <div class="fileinput-new thumbnail"
                                                     style="width: 225px; height: 225px;">
                                                    <img src="{{url('/images/new-user.png')}}" alt="profile pic">
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
                                    <div class="row">
                                        <div class="form-group col-12">
                                            <label hidden class="text-bold" for="username">Tài khoản:</label>
                                            <input hidden type="text" id="username" class="form-control" name="username"
                                                   required>
                                        </div>
                                        <div class="form-group col-6">
                                            <label hidden class="text-bold" for="password">Mật khẩu:</label>
                                            <input hidden type="text" id="password"
                                                   class="form-control password-validate"
                                                   name="password" required>
                                        </div>
                                        <div class="form-group col-6">
                                            <label hidden class="text-bold" for="first_name">Nhãn:</label>
                                            <input hidden type="text" id="first_name" class="form-control"
                                                   name="first_name"
                                                   required>
                                        </div>
                                        <div class="form-group col-12">
                                            <label hidden class="text-bold">
                                                <input hidden type="checkbox" name="activate" class="square"/> Kích hoạt
                                                tài khoản
                                            </label>
                                        </div>
                                        <input type="text" id="full-addr" name="address" hidden>
                                        <input type="text" id="contact" name="contact" hidden>
                                        <input type="text" id="kieu" name="kieu" value="{{$k_id}}" hidden>
                                        <input type="text" id="loai" name="loai" value="{{$loai}}" hidden>
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
                                    <div class="col-md-6 col-xs-12 {{$tm->tm_keywords == 'tinh-trang-hon-nhan' ? 'hidden' : ''}}">
                                        <div class="form-group">
                                            <label class="text-bold" for="modal-ele-{{$tm->tm_keywords}}">{{$tm->tm_nhan}}:</label>
                                            <input type="text" name="ds_tm[]" value="tm-{{$tm->tm_id}}" hidden>
                                            @if($tm->tm_loai == "text")
                                                <input id="modal-ele-{{$tm->tm_keywords}}" type="text"name="tm-{{$tm->tm_id}}" class="form-control" @if($tm->tm_batbuoc == 1) required @endif>
                                            @elseif($tm->tm_loai == "select")
                                                <?php
                                                $select = \App\Models\KieuTieuMucModel::where('tm_id', $tm->tm_id)
                                                    ->where('ktm_status', 1)
                                                    ->pluck('ktm_traloi', 'ktm_id');
                                                ?>
                                                {!! \App\Helpers\Form::select('tm-'.$tm->tm_id,$select,'',['class'=>'form-control','id'=>'modal-ele-'.$tm->tm_keywords,'onchange'=>'change_tm(this,\'in-modal\')']) !!}
                                            @elseif($tm->tm_loai == 'file')
                                                <input id="{{$tm->tm_keywords}}" name="tm-{{$tm->tm_id}}[]"type="file" accept="image/*" class="form-control" onchange="loadImgKH2(this)" @if($tm->tm_batbuoc == 1) required @endif multiple/>
                                                <div class="row text-center row-image" id="img2-{{$tm->tm_keywords}}"></div>
                                            @else
                                                <input type="text" id="modal-ele-{{$tm->tm_keywords}}" class="form-control" name="tm-{{$tm->tm_id}}" data-mask="99/99/9999" placeholder="Ngày / tháng / năm" @if($tm->tm_batbuoc == 1) required @endif>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="row">
                                <hr>
                            </div>
                            <div class="row">
                                <div hidden class="col-md-4 col-xs-12">
                                    <div class="form-group row">
                                        <label class="text-bold" for="pic">Ảnh đại diện:</label><br>
                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                            <div class="fileinput-new thumbnail" style="width: 220px; height: 220px;">
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
                                <div hidden class="col-md-8 col-xs-12">
                                    {{--                                    <div class="form-group row">--}}
                                    <div class="form-group row">
                                        <label class="text-bold" for="modal-ele-username">Tài khoản:</label>
                                        <input type="text" id="modal-ele-username" class="form-control"
                                               name="username" required>
                                    </div>
                                    <div class="form-group row">
                                        <label class="text-bold" for="modal-ele-password">Mật khẩu:</label>
                                        <input type="text" id="modal-ele-password"
                                               class="form-control password-validate" name="password"
                                               required>
                                        <span id="modal-ele-valid-password"
                                              class="text-small text-danger pl-1 pt-1"></span>
                                    </div>
                                    <div class="form-group row">
                                        <label class="text-bold" for="modal-ele-first_name">Nhãn:</label>
                                        <input type="text" id="modal-ele-first_name" class="form-control"
                                               name="first_name" required>
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
                        <button type="button" data-dismiss="modal" class="btn btn-warning qkbtn">Hủy</button>
                        <a href="javascript:void(0)" id="submit-honphoi" class="btn btn-primary qkbtn" onclick="submitHonPhoi()">Lưu</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
@section('footer_scripts')
    <script type="text/javascript" src="{{ asset('assets/js/imgPreview.js') }}"></script>
    <script src="{{ asset('assets/vendors/select2/js/select2.js') }}"></script>
    <script src="{{ asset('assets/vendors/moment/js/moment.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/vendors/datetimepicker/js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/daterangepicker/js/daterangepicker.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/vendors/jasny-bootstrap/js/jasny-bootstrap.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/vendors/iCheck/js/icheck.js') }}"></script>
    <script src="{{ asset('assets/vendors/bootstrap-switch/js/bootstrap-switch.js') }}"></script>
    <script src="{{ asset('assets/vendors/bootstrap-maxlength/js/bootstrap-maxlength.js') }}"></script>
    <script src="{{ asset('assets/vendors/card/lib/js/jquery.card.js') }}"></script>
    
    <script>
        var select_element = {};
        $.each($('select'), function (k, v) {
            select_element[v.id] = v.value;
        });
        function change_tm(value, type) {
            var curr_val = select_element[value.id];
            var id_selected = '#' + value.id;
            var selected_val = $(id_selected).val();
            var fill_id_prefix = '';
            var fill_id = '';
            $.ajax({
                url: "{{route('getTMKHSelect')}}",
                method: "GET",
                data: 'ktm_id=' + selected_val + '&current_val=' + curr_val,
                success: function (tieumuc) {
                    console.log(tieumuc);
                    if (select_element[value.id] != 1) {
                        $('#btn-honphoi').removeAttr('disabled');

                    } else {
                        $('#btn-honphoi').attr('disabled', 'disabled');

                    }
                    $.each(tieumuc.data['tm_rmv'], function (k, v) {
                        $('#row-' + v.tm_keywords).remove();
                    });
                    var current_ele = $(id_selected).parent().parent();
                    tieumuc.data['list_tm'].map(function (val) {
                        var batbuoc = '';
                        if (val.tm_batbuoc === "1") {
                            batbuoc = 'required';
                        }
                        if (type === 'normal') {
                            fill_id_prefix = 'fill_';
                            fill_id = '#' + fill_id_prefix + val.tm_id;
                        } else {
                            fill_id_prefix = 'fill_modal_';
                            fill_id = '#' + fill_id_prefix + val.tm_id;
                        }
                        $('<div class="form-group row add-sub" id="row-' + val.tm_keywords + '">' +
                            '<div class="col-md-3 fit-column-height text-right">' +
                            '<label class="text-bold"  for="' + val.tm_keywords + '">' + val.tm_nhan + ':</label>' +
                            '<input type="text" name="ds_tm[]" value="tm-' + val.tm_id + '" hidden>' +
                            '</div>' +
                            '<div class="col-md-9" id="fill_' + val.tm_id + '">' +
                            '</div>'
                        ).insertAfter($(current_ele));
                        if (val.tm_loai === "text") {
                            if (value.id === "tinh-trang-hon-nhan" && val.tm_keywords === "noi-cu-tru-vo-chong") {
                                $(fill_id).append('<input id="' + val.tm_keywords + '" type="text" name="tm-' + val.tm_id + '" value="' + $('#dia-chi-lien-he').val() + '" class="form-control"' + batbuoc + '>')
                            } else {
                                $(fill_id).append('<input id="' + val.tm_keywords + '" type="text" name="tm-' + val.tm_id + '" class="form-control" ' + batbuoc + '>')
                            }
                        } else if (val.tm_loai === "select") {
                            $(fill_id).append(
                                '<select id="' + val.tm_keywords + '" required  class="form-control" name="tm-' + val.tm_id + '">' +
                                '</select>'
                            );
                            if (val.tm_keywords !== 'hon-phoi') {
                                var sel_id = '#' + val.tm_keywords;
                                $.ajax({
                                    url: "{{route('getTMKHOptions')}}",
                                    type: "GET",
                                    data: 'tm_id=' + val.tm_id,
                                    success: function (options) {
                                        options.data.map(function (opt) {
                                            $(sel_id).append(
                                                '<option value="' + opt.ktm_id + '">' + opt.ktm_traloi + '</option>'
                                            )
                                        })
                                    }
                                })
                            } else {
                                $('#hon-phoi').select2({
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
                        } else if (val.tm_loai === "file") {
                            $(fill_id).append(
                                '<input id="' + val.tm_keywords + '" name="tm-' + val.tm_id + '[]" type="file" accept="image/*" class="form-control" onchange="loadImgKH3(this,\'normal\')" multiple ' + batbuoc + '/>' +
                                '<div class="row text-center row-image" id="img3-' + val.tm_keywords + '"></div>'
                            );
                        } else {
                            $(fill_id).append(
                                '<input type="text" id="' + val.tm_keywords + '" class="form-control" name="tm-' + val.tm_id + '" data-mask="99/99/9999" placeholder="Ngày / tháng / năm" ' + batbuoc + '>'
                            )
                        }
                        current_ele = '#row-' + val.tm_keywords;
                    });
                }
            });
            select_element[value.id] = selected_val;
        }
        function addRelation() {
            $('#main-ds').text($('#first_name').val());
            $('#modal-honphoi').modal('show');
        }
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
                        $('#modal-honphoi').modal('hide');
                        $('#hon-phoi').val(res.data);
                        $('#hon-phoi').append(
                            '<option value="' + res.data + '">' + $('#modal-ele-first_name').val() + '</option>'
                        )
                    } else {
                        $.each(res.message, function (k, v) {
                            msgError(v);
                        })
                    }
                }
            })
        }

        $("#tinh_trang_hon_nhan option:selected").val()
        $('#giay-to-tuy-than-so').focusout(function () {
            var so_dinh_danh = $(this).val();
            if (so_dinh_danh) {
                $.ajax({
                    url: "{{route('validCMND')}}",
                    type: "GET",
                    data: 'kh_giatri=' + so_dinh_danh,
                    success: function (err) {
                        $('#giay-to-tuy-than-so').tooltip({
                            title: err.message,
                            placement: 'bottom',
                            trigger: 'manual'
                        });
                        if (err.status === 'error') {
                            $('#giay-to-tuy-than-so').css('border', '1px solid red');
                            $('#giay-to-tuy-than-so').tooltip('show');
                        } else {
                            $('#giay-to-tuy-than-so').removeAttr('style', 'border');
                            $('#giay-to-tuy-than-so').tooltip('hide');
                        }
                    }
                });
            }
            if ($('#ho-duong-su').val() !== undefined && $('#ten-duong-su').val() !== undefined) {
                var first_name = $('#ho-duong-su').val() + ' ' + $('#ten-duong-su').val() + ' ' + $(this).val();

                $('#first_name').val(first_name);
            }
            if ($('#ten-goi-doanh-nghiep').val() !== undefined) {
                var first_name = $('#ten-goi-doanh-nghiep').val();

                $('#first_name').val(first_name);
            }
            $('#username').val('dotary' + Math.floor(Math.random() * 999999) + 100000);
            $('#password').val(123456);
        });
        $('#ho-duong-su').val("{{Request::get('label')}}")
        $("#ten-goi-doanh-nghiep").val("{{Request::get('label')}}")
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
            $('#modal-ele-username').val($(this).val());
            $('#modal-ele-password').val(Math.floor(Math.random() * 999999) + 100000);
        });

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

        function closeSelf() {
            self.close();
            return true;
        }

        $('#modal-ele-dien-thoai').focusout(function () {
            $('#modal-ele-contact').val($(this).val());
        });

    </script>
    <script src="{{asset('assets/js/cleave.min.js')}}"></script>
    <script>
        new Cleave('#ngay-cap', {
    date: true,
    delimiter: '/',
    datePattern: ['d', 'm', 'Y']
});
</script>
@stop

