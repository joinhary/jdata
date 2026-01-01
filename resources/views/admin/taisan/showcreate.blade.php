@extends('admin/layouts/default')

{{-- Page title --}}
@section('title')
Tạo tài sản mới
@parent
@stop

{{-- page level styles --}}
@section('header_styles')
<meta name="_token" content="{{ csrf_token() }}">
<script src="{{asset('assets/js/jquery-3.3.1.min.js')}}"></script>
<link href="{{ asset('assets/css/pages/toastr.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/vendors/toastr/css/toastr.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/vendors/datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/vendors/jasny-bootstrap/css/jasny-bootstrap.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{asset('assets/css/jquery.fancybox.css')}} " />
    <style>
        .form-group {
            margin-bottom: 0px!important;
        }

        .form-control {
            font-size: 13px !important;
        }
        label {
            font-size: 13px !important;
        }

        .form-control {
            font-size: 13px !important;
        }

    </style>
@stop

{{-- Page content --}}
@section('content')

{{--
        <section class="content-header">
            <h1>Quản lý tài sản</h1>
            <a href="javascript:history.back()"><i class="fa fa-arrow-left"></i> Trở lại</a>
        </section>
    --}}
<section class="content p-2 pt-1">
    <div class="row scrollable-list-custom">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h5 class="panel-title">
           Thêm tài sản mới
                    </h5>
                </div>
               {!! \App\Helpers\Form::open([
    'url' => route('showStoreTaiSan', ['id' => $id]),
    'method' => 'POST',
    'role'=>'form',
    'enctype'=>'multipart/form-data'
]) !!}
@csrf
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="text" id="loai" name="loai" value="{{$id}}" hidden>

                            @foreach($tieumuc_nhan as $tm)
                            <div class="form-group row">
                                <div class="col-md-4 fit-column-height text-right">
                                    <label style="font-weight: bold" for="{{$tm->tm_id}}">{{$tm->tm_nhan}}
                                        @if($tm->tm_batbuoc == 1)
                                        <span style="color: red">( * ) </span>
                                        @else
                                        @endif
                                        :
                                    </label>
                                    <input type="text" name="ds_tm[]" value="tm-{{$tm->tm_id}}" hidden>
                                </div>
                                <div class="col-md-8">
                                    @if($tm->tm_loai == "text")
                                    <input id="{{$tm->tm_keywords}}" name="tm-{{$tm->tm_id}}" placeholder="Nhập {{ strtolower($tm->tm_nhan) }} ...." type="text" class="form-control" @if($tm->tm_batbuoc == 1) required
                                    @endif
                                    />
                                    <span id="valid-{{$tm->tm_keywords}}" class="text-small text-danger pl-1 pt-1">
                                    </span>
                                    @elseif($tm->tm_loai == "select")
                                    <?php
                                                $select = \App\Models\KieuTieuMucModel::where('tm_id', $tm->tm_id)
                                                    ->where('ktm_status', 1)
                                                    ->pluck('ktm_traloi', 'ktm_id');
                                                ?>
                                    {!! \App\Helpers\Form::select('tm-'.$tm->tm_id,$select,'',['class'=>'form-control'
                                    ,
                                    'id'=>$tm->tm_id,
                                    'onchange'=>'change_tm(this)'
                                    ]) !!}
                                        <span class="text-small text-danger pl-1 pt-1"></span>

                                    @elseif($tm->tm_loai == 'file')
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div class="fileinput-new thumbnail" style="width: 100px; height: 100px;">
                                            <img src="http://placehold.it/100x100" alt="profile pic">
                                        </div>
                                        <a data-fancybox="images" class="fancy-box" href="#">
                                            <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 100px; max-height: 100px;">
                                            </div>
                                        </a>
                                        <div>
                                            <span class="btn btn-primary btn-file">
                                                <span class="fileinput-new">Chọn ảnh</span>
                                                <span class="fileinput-exists">Thay đổi</span>
                                                <input id="pic{{$tm->tm_id}}" name="tm-{{$tm->tm_id}}" type="file" class="form-control" @if($tm->tm_batbuoc == 1) required
                                                @endif
                                                />
                                            </span>
                                            <a href="#" class="btn btn-danger fileinput-exists" data-dismiss="fileinput">Gỡ bỏ</a>
                                        </div>
                                    </div>
                                    @elseif($tm->tm_loai == 'date')
                                    <input type="text" id="{{$tm->tm_keywords}}" class="form-control" name="tm-{{$tm->tm_id}}" data-mask="99/99/9999" placeholder="Ngày / tháng / năm" @if($tm->tm_batbuoc == 1) required
                                    @endif
                                    />
                                        <span class="text-small text-danger pl-1 pt-1"></span>

                                    @else
                                    <input type="number" id="{{$tm->tm_keywords}}" step="0.01" class="form-control" name="tm-{{$tm->tm_id}}" placeholder="Nhập {{ strtolower($tm->tm_nhan) }} ...." @if($tm->tm_batbuoc == 1) required
                                    @endif
                                    />
                                        <span class="text-small text-danger pl-1 pt-1"></span>

                                    @endif
                                </div>
                            </div>
                            @endforeach
                            <div class="col-md-4">

                            </div>
                            <div class="col-md-3 col-md-offset-6">
                                <div class="form-group row">
                                    <button type="submit" id="sub-kh" onclick="block()" class="btn btn-success">Lưu
                                    </button>
                                    <a href="{{ route('createTaiSan') }}" class="btn btn-danger">Hủy</a>
                                </div>
                            </div>
                            <div class="col-md-5">

                            </div>
                        </div>
                    </div>
                </div>
                {!! \App\Helpers\Form::close() !!}
            </div>
        </div>
    </div>

</section>
@stop
@section('footer_scripts')
<script src="{{ asset('assets/vendors/moment/js/moment.min.js') }}" type="text/javascript"></script>
{{-- <script src="{{ asset('assets/vendors/daterangepicker/js/daterangepicker.js') }}" type="text/javascript"></script>--}}
{{-- <script src="{{ asset('assets/vendors/datetimepicker/js/bootstrap-datetimepicker.min.js') }}"--}}
{{--type="text/javascript"></script>--}}
<script src="{{ asset('assets/vendors/daterangepicker/js/daterangepicker.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/vendors/jasny-bootstrap/js/jasny-bootstrap.js') }}" type="text/javascript"></script>
{{-- <script src="{{ asset('assets/js/pages/datepicker.js') }}" type="text/javascript"></script>--}}
<script src="{{asset('assets/js/jquery.fancybox.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/pages/gallery.js') }}"></script>

<script>
    function block() {
        $('#sub-kh').addClass('disabled');
    }

        $('.fileinput-preview').bind('DOMNodeInserted', function() {
        $value = $(this).children().attr('src');
        $(this).children().closest('.fancy-box').attr('href', $value);
    });
    
    function change_tm(value) {
        var id_selected = '#' + value.id;
        var selected_val = $(id_selected).val();
        $.ajax({
            url: "{{route('getTMSelect')}}"
            , method: "GET"
            , data: 'ktm_id=' + selected_val
            , success: function(tieumuc) {
                if (tieumuc.data.length === 0) {
                    $('.add-sub').remove();
                } else {
                    var current_ele = $(id_selected).parent().parent();
                    tieumuc.data.map(function(val) {
                        var fill_id = '#fill_' + val.tm_id;
                        $('<div class="form-group row add-sub" id="add-' + val.tm_id + '">' +
                            '<div class="col-md-4 fit-column-height text-right">' +
                            '<label for="' + val.tm_id + '">' + val.tm_nhan + ':</label>' +
                            '<input type="text" name="ds_tm[]" value="tm-' + val.tm_id + '" hidden>' +
                            '</div>' +
                            '<div class="col-md-8" id="fill_' + val.tm_id + '">' +
                            '</div>'
                        ).insertAfter($(current_ele));
                        if (val.tm_loai === "text") {
                            $(fill_id).append('<input id="' + val.tm_id + '" type="text" name="tm-' + val.tm_id + '" class="form-control" placeholder="Nhập ' + val.tm_nhan.toLowerCase() + ' ...">')
                        } else if (val.tm_loai === "select") {
                            $(fill_id).append(
                                '<select id="' + val.tm_id + '"   class="form-control" name="tm-' + val.tm_id + '">' +
                                '</select>'
                            );
                            var sel_id = '#' + val.tm_id;
                            $.ajax({
                                url: "{{route('getTMOptions')}}"
                                , type: "GET"
                                , data: {
                                    'tm_id': val.tm_id
                                }
                                , success: function(options) {
                                    console.log(options.data);
                                    $.each(options.data, function(key, value) {
                                        $(sel_id).append(
                                            '<option value="' + key + '">' + value + '</option>'
                                        )
                                    })
                                }
                            })
                        } else if (val.tm_loai === "file") {
                            $(fill_id).append(
                                '<div class="fileinput fileinput-new" data-provides="fileinput">' +
                                '<div class="fileinput-new thumbnail" style="width: 100px; height: 100px;">' +
                                '<img src="http://placehold.it/100x100" alt="profile pic">' +
                                '</div>' +
                                '<div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 100px; max-height: 100px;"></div>' +
                                '<div>' +
                                '<span class="btn btn-primary btn-file">' +
                                '<span class="fileinput-new">Chọn ảnh</span>' +
                                '<span class="fileinput-exists">Thay đổi</span>' +
                                '<input id="pic' + val.tm_id + '" name="tm-' + val.tm_id + '" type="file" class="form-control"/>' +
                                '</span>' +
                                '<a href="#" class="btn btn-danger fileinput-exists" data-dismiss="fileinput">Gỡ bỏ</a>' +
                                '</div>' +
                                '<div>'
                            );
                        } else if (val.tm_loai === "date") {
                            $(fill_id).append(
                                '<input type="text" id="' + val.tm_keywords + '" class="form-control" name="tm-' + val.tm_id + '" data-mask="99/99/9999" placeholder="Ngày / tháng / năm">'
                            )
                        } else {

                            $(fill_id).append(
                                '<input type="number" id="' + val.tm_keywords + '" class="form-control" name="tm-' + val.tm_id + ''
                            )

                        }
                        current_ele = '#add-' + val.tm_id;
                    })
                }

            }
        })
    }
    $("#ten-phieu-tai-san").val("{{\Request::get('label')}}")

        $('#dien-tich').change(function () {
            $.ajax({
                url: "{{route('readArea')}}",
                type: "get",
                data: {
                    'number': $('#dien-tich').val()
                },
                success: function (res) {
                    if(res.data!=false){
                        $('#tong-dien-tich-bang-chu').val(res.data);

                    }else{
                        $('#tong-dien-tich-bang-chu').val('');

                    }
                }
            })
        });
    $('input:required').focusout(function() {
        if (!$(this).val()) {
            $(this).css('border', '1px solid red');
            $('#valid-' + $(this).attr('id')).text('Vui lòng không để trống!');
        } else {
            $(this).removeAttr('style', 'border');
            $('#valid-' + $(this).attr('id')).text('');
        }
    });

</script>
<script src="{{asset('assets/js/cleave.min.js')}}"></script>
    <script>
        new Cleave('#ngay-cap-giay-chung-nhan', {
    date: true,
    delimiter: '/',
    datePattern: ['d', 'm', 'Y']
});
</script>
@stop
