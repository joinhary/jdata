@extends('admin/layouts/default')
@section('title')
    Quản lý ngân hàng @parent
@stop
@section('header_styles')
    <meta name="_token" content="{{ csrf_token() }}">
    <link href="{{ asset('assets/vendors/jasny-bootstrap/css/jasny-bootstrap.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/iCheck/css/all.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/iCheck/css/line/line.css') }}"/>
    <link rel="stylesheet" type="text/css"
          href="{{ asset('assets/vendors/bootstrap-switch/css/bootstrap-switch.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/switchery/css/switchery.css') }}"/>
    <link rel="stylesheet" type="text/css"
          href="{{ asset('assets/vendors/awesomeBootstrapCheckbox/awesome-bootstrap-checkbox.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/pages/formelements.css') }}"/>
    <link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/vendors/select2/css/select2-bootstrap.css') }}" rel="stylesheet"/>
    <style>
        label {
            font-size: 14px !important;
        }

        .qksao {
            font-weight: bold;
        }

        .qkbtn {
            font-weight: bold;
            font-size: 14px !important;
        }

        .form-control {
            font-size: 13px !important;
        }
    </style>
@stop
@section('content')
    <section class="content">
        <form method="POST" action="{{route('storeBank')}}" class="form-create" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-8 border-right-custom">
                    <div class="row">
                        <label for="name">Tên ngân hàng: (<span class="text-danger qksao">*</span>)</label>
                        <input type="text" id="name" class="form-control" name="name" value="{{old('name')}}"
                                required TABINDEX="1">
                    </div>
                    <div class="row">
                        <label for="order_number">Mã: </label>
                        <input type="text" id="order_number" class="form-control" name="order_number" value="{{old('order_number')}}"
                               TABINDEX="2">
                    </div>
                    <br>
                    <div class="row">
                        <div class="row">
                            <a href="javascript:history.back()" type="cancel" class="btn btn-secondary qkbtn">Hủy</a>
                            <button type="submit" class="btn btn-primary qkbtn">Lưu</button>
                        </div>
                    </div>

                </div>
                </div>
            </div>
        </form>
    </section>
@stop
@section('footer_scripts')
    <script src="{{ asset('assets/vendors/jasny-bootstrap/js/jasny-bootstrap.js') }}" type="text/javascript"></script>
    <script language="javascript" type="text/javascript"
            src="{{ asset('assets/vendors/iCheck/js/icheck.js') }}"></script>
    <script language="javascript" type="text/javascript"
            src="{{ asset('assets/vendors/bootstrap-switch/js/bootstrap-switch.js') }}"></script>
    <script language="javascript" type="text/javascript"
            src="{{ asset('assets/vendors/switchery/js/switchery.js') }}"></script>
    <script language="javascript" type="text/javascript"
            src="{{ asset('assets/js/pages/radio_checkbox.js') }}"></script>
    <script language="javascript" type="text/javascript"
            src="{{ asset('assets/vendors/select2/js/select2.js') }}"></script>
    <script>
        var geometryURL = "{{route('getGeometry')}}";
        $('.select2').select2();

        $(".toggle-password").click(function () {
            $(this).toggleClass("fa-eye fa-eye-slash");
            $(this).attr("toggle");
            var input = $('#password');
            if (input.attr("type") === "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
        });

        $('#email').focusout(function () {
            $('#password').val(Math.floor(Math.random() * 999999) + 100000);
        })
    </script>
    <script src="{{ asset('assets/js/getGeometry.js') }}"></script>
@stop

