@extends('admin.layouts.default')
@section('header_styles')
    <link href="{{ asset('assets/vendors/jasny-bootstrap/css/jasny-bootstrap.css') }}" rel="stylesheet"
          type="text/css"/>
    <link href="{{ asset('assets/vendors/iCheck/css/all.css') }}" rel="stylesheet" type="text/css"/>

    <!-- Add fancyBox main CSS files -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/fancybox/jquery.fancybox.css') }}"
          media="screen"/>

@endsection
@section('content')
    <section class="content-header" style="margin-bottom: 0px">
        <h1>Tài sản</h1>
{{--        <ol class="breadcrumb">--}}
{{--            <li>--}}
{{--                <a href="{{ route('admin.dashboard') }}">--}}
{{--                    <i class="livicon" data-name="home" data-size="14" data-loop="true"></i>--}}
{{--                    Trang chủ--}}
{{--                </a>--}}
{{--            </li>--}}
{{--            <li>--}}
{{--                <a href="{{ route('indexTaiSan') }}">--}}
{{--                    Tài sản--}}
{{--                </a>--}}
{{--            </li>--}}
{{--            <li>--}}
{{--                <a href="{{ route('admin.taisan.lichsu.index',$id_ts) }}">--}}
{{--                    Lý lịch--}}
{{--                </a>--}}
{{--            </li>--}}
{{--            <li class="active">--}}
{{--                Sửa--}}
{{--            </li>--}}
{{--        </ol>--}}
    </section>
    <br>
    <div class="row">
        <div class="col-lg-12">
            <div class="portlet box">
                <!--form control starts-->
                <div class="panel panel-primary" id="hidepanel6">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <i class="fa fa-fw fa-save"></i> Lịch sử - sửa
                        </h3>
                        <span class="pull-right">
                                    <i class="glyphicon glyphicon-chevron-up clickable"></i>
                            {{--<i class="glyphicon glyphicon-remove removepanel clickable"></i>--}}
                                </span>
                    </div>
                    <div class="panel-body">
                        <form action="{{route('storeLyLich',['idKH'=>$idKH])}}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="row form-group">
                                <label class="col-md-2 p-0">Loại:</label>
                                <div class="col-md-10 p-0">
                                    <label class="radio-inline">
                                        &nbsp;<input type="radio" name="tinhtrang" class="custom-radio" value="1"
                                                     required {{ old('tinhtrang') == 1?'checked':'' }}>&nbsp;Ngăn
                                        chặn</label>
                                    <label class="radio-inline">
                                        <input type="radio" name="tinhtrang" class="custom-radio"
                                               value="2" {{ old('tinhtrang') == 2?'checked':'' }}>&nbsp;Giải
                                        chấp</label>
                                    <label class="radio-inline">
                                        <input type="radio" name="tinhtrang" class="custom-radio"
                                               value="3" {{ old('tinhtrang') == 3?'checked':'' }}>&nbsp;Giải
                                        tỏa</label>
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="control-label col-md-2 p-0" for="sohoso">Số HS/CV:</label>
                                <div class="col-md-10 p-0">
                                    <input type="text" class="form-control" id="sohoso" name="sohoso"
                                           placeholder="Số HS/CV"  maxlength="20" value="{{ old('sohoso') }}">
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="control-label col-md-2 p-0" for="ngayky">Ngày ký:</label>
                                <div class="col-md-10 p-0">
                                    <input type="date" class="form-control" id="ngayky" name="ngayky"
                                           placeholder="Ngày ký" required value="{{ old('ngayky') }}">
                                </div>
                            </div>
                            <div>
                                <label class="control-label" for="">Danh mục:</label>
                                <div class="row"
                                     style="border: 1px solid #dbd8d5; border-radius: 5px; padding: 10px 0px !important;">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="so_cc">Số CC:</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" id="so_cc" name="so_cc"
                                                       placeholder="Số CC" maxlength="20"
                                                       value="{{ old('so_cc') }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="so_vaoso">Số chính thức:</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" id="so_vaoso" name="so_vaoso"
                                                       placeholder="Số chính thức"  maxlength="20"
                                                       value="{{ old('so_vaoso') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-12 p-0">
                                <div class="col-md-8 p-0">
                                    <label>Nhãn:</label>
                                    <textarea class="form-control" name="mota"  rows="5"
                                              style="resize: vertical">{{ old('mota') }}</textarea>
                                </div>
                                <div class="col-md-4 p-0 pl-1">
                                    <label for="lylich_hinhanh" class="label-line"><b>Hình ảnh:</b></label>
                                    <div class="col-12 border-custom p-1 pt-2 text-center">
                                        <div class="row mb-1">
                                            <input id="lylich_hinhanh" name="lylich_hinhanh[]" type="file" accept="image/*" class="form-control" multiple onchange="loadImg(this)">
                                        </div>
                                        <div class="row text-center row-image" id="images"></div>
                                    </div>
                                </div>

                            </div>
                            <div>
                                <label class="control-label" for="">Người thụ lý:</label>
                                <div class="row"
                                     style="border: 1px solid #dbd8d5; border-radius: 5px; padding: 10px 0px !important;">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="form-group">
                                                <label>CCV:</label>
                                                {!! \App\Helpers\Form::select('ccv_id',$ccv,null,['id' => 'ccv_id','class'=>'form-control select2','required']) !!}

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="form-group">
                                                <label>Nhân viên NV:</label>

                                                {!! \App\Helpers\Form::select('nhanviennv_id',$cv,null,['id' => 'nhanviennv_id','class'=>'form-control select2']) !!}

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr width="50%">
                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-responsive btn-primary">Xác nhận</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('footer_scripts')
    <script src="{{ asset('assets/vendors/jasny-bootstrap/js/jasny-bootstrap.js') }}" type="text/javascript"></script>
    <script language="javascript" type="text/javascript" src="{{ asset('assets/vendors/iCheck/js/icheck.js') }}"></script>
    <script language="javascript" type="text/javascript" src="{{ asset('assets/vendors/bootstrap-switch/js/bootstrap-switch.js') }}"></script>
    <script language="javascript" type="text/javascript" src="{{ asset('assets/vendors/switchery/js/switchery.js') }}"></script>
    <script language="javascript" type="text/javascript" src="{{ asset('assets/vendors/bootstrap-maxlength/js/bootstrap-maxlength.js') }}"></script>
    <script language="javascript" type="text/javascript" src="{{ asset('assets/vendors/card/lib/js/jquery.card.js') }}"></script>
    <script language="javascript" type="text/javascript" src="{{ asset('assets/js/pages/radio_checkbox.js') }}"></script>
    <script src="{{ asset('assets/vendors/jasny-bootstrap/js/jasny-bootstrap.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/pages/form_examples.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/fancybox/jquery.fancybox.pack.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/pages/gallery.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/imgPreview.js') }}"></script>
    <script>
        $('#sohoso, #so_vaoso, #mota, #ngayky').focusout(function () {
            if (!$(this).val()) {
                var t = $("label[for='" + $(this).attr('id') + "']").text();
                var title = t.slice(0, t.indexOf(":"));
                title = title.toUpperCase();
                var message = 'Không để trống "' + title + '"!';
                $(this).css('border', '1px solid red');
                msgError(message);
                $('#sub-lylich').attr('disabled', true);
            } else {
                $(this).removeAttr('style', 'border');
                $('#sub-lylich').attr('disabled', false);
                $('#err_' + $(this).attr('id')).text('');
            }
        });
        $('#sohoso').focusout(function () {
            $.ajax({
                url: '{{route('validSoHoSo')}}',
                type: 'GET',
                data: 'sohoso=' + $('#sohoso').val() + '&type=create',
                success: function (res) {
                    if (res.status === 'error') {
                        $('#sohoso').css('border', '1px solid red');
                        $('#sub-lylich').attr('disabled', true);
                        msgError(message);
                    } else {
                        if ($(this).val()) {
                            $('#sohoso').removeAttr('style', 'border');
                            $('#sub-lylich').attr('disabled', false);
                        }
                    }
                }
            })
        });
        $('#so_vaoso').focusout(function () {
            $.ajax({
                url: '{{route('validSoVaoSo')}}',
                type: 'GET',
                data: 'so_vaoso=' + $('#so_vaoso').val() + '&type=create',
                success: function (res) {
                    if (res.status === 'error') {
                        $('#so_vaoso').css('border', '1px solid red');
                        $('#sub-lylich').attr('disabled', true);
                        msgError(message);
                    } else {
                        if ($(this).val()) {
                            $('#so_vaoso').removeAttr('style', 'border');
                            $('#sub-lylich').attr('disabled', false);
                        }
                    }
                }
            })
        });
    </script>
    <script>
        var i=1;
        function fancyboxRotation(){
            var n = 90 * ++i;
            $('.fancybox-content img').css('webkitTransform', 'rotate(-' + n + 'deg)');
            $('.fancybox-content img').css('mozTransform', 'rotate(-' + n + 'deg)');
        }
    </script>
@stop

