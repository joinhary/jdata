@extends('admin/layouts/default')
@section('title')
    Chỉnh sửa dữ liệu Bất động sản @parent
@stop
@section('header_styles')
    <meta name="_token" content="{{ csrf_token() }}">
    <link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/vendors/select2/css/select2-bootstrap.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/vendors/jasny-bootstrap/css/jasny-bootstrap.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/iCheck/css/all.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/iCheck/css/line/line.css') }}"/>
    <link rel="stylesheet" type="text/css"
          href="{{ asset('assets/vendors/bootstrap-switch/css/bootstrap-switch.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/switchery/css/switchery.css') }}"/>
    <link rel="stylesheet" type="text/css"
          href="{{ asset('assets/vendors/awesomeBootstrapCheckbox/awesome-bootstrap-checkbox.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/pages/formelements.css') }}"/>
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

        .qkimg111 {
            text-align: center;
        }

        .qkimg222 {
            width: 300px;
            height: 300px;
            border-radius: 50%;
        }
        .qkimg333 {
            width: 300px;
            height: 300px;
            border-radius: 50%;
            line-height: 0px !important;
        }
    </style>
@stop
@section('content')
    <section class="content">
        <form method="POST" action="{{route('updateBDS',['id'=>$bank->id])}}" class="form-create"
              enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <label for="name">Tên file: (<span class="text-danger qksao">*</span>)</label>
                        <input type="text" id="name" class="form-control" name="name"
                               value="{{$bank->name}}"
                               required autofocus TABINDEX="1">
                    </div>
                    <div class="row">
                        <label for="name">File hiện tại: </label>
                       <a style="color:blue;"
                               href={{ url('storage/upload_bds/' . $bank->file) }}> {{ $bank->file }} 
                            </a>
                    </div>
                    <div class="row">
                        <label for="files">Cập nhật File mới: </label>
                        <input type="file" id="file" class="form-control" name="file"
                               TABINDEX="2">
                    </div>
                    {{-- <div class="row">
                        <label for="nv_vanphong">Văn phòng công tác: (<span class="text-danger qksao">*</span>)</label>
                        {!! \App\Helpers\Form::select('nv_vanphong',$chinhanh,$bank->nv_vanphong,['id' => 'nv_vanphong','required' => 'required','class'=>'form-control']) !!}
                    </div>
                    <div class="row">
                        <label for="nv_chucvu">Chức vụ: (<span class="text-danger qksao">*</span>)</label>
                        {!! \App\Helpers\Form::select('nv_chucvu',$chucvu,$bank->nv_chucvu,['id' => 'nv_chucvu','required' => 'required','class'=>'form-control']) !!}
                    </div>
                    <div class="row">
                        <label for="address">Địa chỉ:</label>
                        <input type="text" id="address" class="form-control" name="address"
                               value="{{$bank->address}}"
                               placeholder="Nhập vào số nhà, tên đường (Vd: 1 đường Lý Tự Trọng)">
                    </div> --}}
                    <br>
                    <div class="row">
                        <a href="{{route('indexBds')}}" type="cancel" class="btn btn-secondary qkbtn">Hủy</a>
                        <button type="submit" class="btn btn-primary qkbtn">Cập nhật</button>
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
    {{-- <script>
        var geometryURL = "{{route('getGeometry')}}";
        $('#tinh, #quan, #phuong, #ap, #nv_vanphong, #nv_chucvu').select2();

        $(".toggle-username").click(function () {

            $(this).toggleClass("fa-pencil-square-o fa-times-circle");
            $(this).attr("toggle");
            var input = $('#nv_username');
            if (input.attr("readonly") === "readonly") {
                $('.toggle-username').removeClass('text-muted');
                input.prop("readonly", false);
            } else {
                input.prop("readonly", true);
                input.val('{{$user->username}}');
                $('.toggle-password').addClass('text-muted');
            }
        });
    </script> --}}
    <script src="{{asset('assets/js/getGeometry.js')}}"></script>
@stop

