@extends('admin/layouts/default')
@section('title')
    Quản lý nhân viên @parent
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
        <form method="POST" action="{{route('storeNhanVien')}}" class="form-create" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-8 border-right-custom">
                    <div class="row">
                        <label for="nv_ten">Họ và tên nhân viên: (<span class="text-danger qksao">*</span>)</label>
                        <input type="text" id="nv_ten" class="form-control" name="nv_hoten" value="{{old('nv_hoten')}}"
                                required TABINDEX="1">
                    </div>
                    <div class="row">
                        <label for="phone">Số điện thoại: </label>
                        <input type="text" id="phone" class="form-control" name="phone" value="{{old('phone')}}"
                               TABINDEX="2">
                    </div>
                    <div class="row">
                        <label for="nv_vanphong">Văn phòng công tác: (<span class="text-danger qksao">*</span>)</label>
                        @if(\App\Models\RoleUsersModel::where('user_id',Sentinel::getUser()->id)->first()->role_id==10)
                            {!! \App\Helpers\Form::select('nv_vanphong',$chinhanh,old('nv_vanphong'),['id' => 'nv_vanphong','required' => 'required','class'=>'form-control select2']) !!}
                        @else
                            <input hidden id="nv_vanphong" name="nv_vanphong"
                                   value="{{\App\Models\NhanVienModel::find(Sentinel::getUser()->id)->nv_vanphong}}">
                            {!! \App\Helpers\Form::select('vp',$chinhanh,\App\Models\NhanVienModel::find(Sentinel::getUser()->id)->nv_vanphong,['id' => 'vp','required' => 'required','class'=>'form-control select2','disabled'=>'disabled']) !!}
                        @endif
                    </div>
                    <div class="row">
                        <label for="nv_chucvu">Chức vụ: (<span class="text-danger qksao">*</span>)</label>
                        @if(\App\Models\RoleUsersModel::where('user_id',Sentinel::getUser()->id)->first()->role_id==10)
                            {!! \App\Helpers\Form::select('nv_chucvu',$chucvu,old('nv_chucvu'),['id' => 'nv_chucvu','required' => 'required','class'=>'form-control select2']) !!}
                        @else
                            {!! \App\Helpers\Form::select('nv_chucvu',$chuc,old('nv_chucvu'),['id' => 'nv_chucvu','required' => 'required','class'=>'form-control select2']) !!}
                        @endif
                    </div>
                    <div class="row">
                        <label for="address">Địa chỉ:</label>
                        <input type="text" id="address" class="form-control" name="address" value="{{old('address')}}"
                               placeholder="Nhập vào số nhà, tên đường (Vd: 1 đường Lý Tự Trọng)">
                    </div>
                    <br>

                </div>
                <div class="col-md-4">
                    <div class="col-md-12">
                        <label for="pic">Ảnh đại diện:</label><br>
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
                                    <input id="pic" name="pic" type="file" class="form-control"/>
                                </span>
                                <a href="#" class="btn btn-danger fileinput-exists"
                                   data-dismiss="fileinput">Gỡ bỏ</a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <label for="email">Tên đăng nhập: (<span class="text-danger qksao">*</span>)</label>
                        <input type="text" id="email" class="form-control" name="email" value="{{old('email')}}"
                               placeholder="mail@gmail.com" required TABINDEX="3">
                    </div>
                    <div class="row">
                        <label for="password">Mật khẩu: (<span class="text-danger qksao">*</span>)</label>
                        <input type="password" id="password" class="form-control" name="password"
                               value="{{old('password')}}" 
                               required TABINDEX="4">
                    </div>
                    {{-- <div class="row">
                        <label for="password">Id liên kết: (<span class="text-danger qksao">*</span>)</label>
                        <input type="text" name="id_lienket" class="form-control" name="password"
                               value="{{old('id_lienket')}}" required TABINDEX="4">
                    </div> --}}
                    <div class="row">
                        <label>
                            <input type="checkbox" name="activate" class="square" checked/> Kích hoạt tài khoản
                        </label>
                    </div>
                    <div class="row">
                        <div class="row">
                            <a href="javascript:history.back()" type="cancel" class="btn btn-secondary qkbtn">Hủy</a>
                            <button type="submit" class="btn btn-primary qkbtn">Lưu</button>
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

        // $('#email').focusout(function () {
        //     $('#password').val(Math.floor(Math.random() * 999999) + 100000);
        // })
    </script>
    <script src="{{ asset('assets/js/getGeometry.js') }}"></script>
@stop

