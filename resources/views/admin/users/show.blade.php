@extends('admin/layouts/default')
@section('title')
    Thông tin người dùng    @parent
@stop
@section('header_styles')
    <meta name="csrf_token" content="{{ csrf_token() }}">
    <link href="{{ asset('assets/vendors/jasny-bootstrap/css/jasny-bootstrap.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/vendors/x-editable/css/bootstrap-editable.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/css/pages/ucontent-headerser_profile.css') }}" rel="stylesheet"/>
    <style>
        .qkth {
            text-align: left !important;
            background: #f8fbfd !important;
            color: black;
            font-weight: normal;
            font-family: "Lato", "Lucida Grande", Helvetica, Arial, sans-serif;
        }
        .qkbtn {
            font-weight: bold;
            font-size: 14px !important;
        }
    </style>
@stop
@section('content')
    <section class="content">
        <div class="col-lg-12">
            <ul class="nav nav-tabs">
                <li>
                    <a href="#tab1" data-toggle="tab">
                        <i class="livicon" data-name="user" data-size="16" data-c="#000" data-hc="#000"
                           data-loop="true"></i>
                        Thông tin người dùng
                    </a>
                </li>
                <li>
                    <a href="#tab2" data-toggle="tab">
                        <i class="livicon" data-name="key" data-size="16" data-loop="true" data-c="#000"
                           data-hc="#000"></i>
                        Đổi mật khẩu
                    </a>
                </li>
            </ul>
            <div class="tab-content mar-top">
                <div class="tab-content active">
                    <div id="tab1" class="tab-pane fade in ">
                        <form method="POST" action="{{route('update1',['id'=>$user->id])}}" class="form-create"
                              enctype="multipart/form-data">
                            <div class="col-lg-12">
                                <div class="panel">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Thông tin người dùng </h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="col-sm-12">
                                            <div class="col-sm-4 pull-left">
                                                @csrf
                                                <div class="form-group row">
                                                    <label for="pic">Ảnh đại diện:</label><br>
                                                    <div class="fileinput fileinput-new"
                                                         data-provides="fileinput">
                                                        <div class="fileinput-new thumbnail"
                                                             style="width: 225px; height: 225px;">
                                                            @if($user->pic)
                                                                <img
                                                                    src="{!!asset('assets/images/authors').'/' .$user->pic!!}"
                                                                    alt="profile pic">
                                                            @else
                                                                <img src="{{url('/images/new-user.png')}}"
                                                                     alt="profile pic">
                                                            @endif
                                                        </div>
                                                        <div
                                                            class="fileinput-preview fileinput-exists thumbnail"
                                                            style="max-width: 250px; max-height: 250px;"></div>
                                                        <div>
                                                            <span class="btn btn-primary btn-file">
                                                                <span class="fileinput-new qkbtn">Chọn ảnh</span>
                                                                <span class="fileinput-exists qkbtn">Thay đổi</span>
                                                                <input id="pic" name="pic" type="file"
                                                                       class="form-control"/>
                                                            </span>
                                                            <a href="#" class="btn btn-danger qkbtn fileinput-exists"
                                                               data-dismiss="fileinput">
                                                                Gỡ bỏ
                                                            </a>
                                                            <button type="submit" class="btn btn-primary qkbtn">Lưu
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-8 col-xs-12">
                                                <table class="table table-bordered table-hover">
                                                    <tr>
                                                        <th class="qkth">
                                                            @lang('users/title.first_name')
                                                        </th>
                                                        <th class="qkth">
                                                            {{ $user->first_name }}
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <th class="qkth">
                                                            @lang('users/title.email')
                                                        </th>
                                                        <th class="qkth">
                                                            {{ $user->email }}
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <th class="qkth">
                                                            @lang('users/title.status')
                                                        </th>
                                                        <th class="qkth">
                                                            @if($user->deleted_at)
                                                                Đã bị xóa
                                                            @elseif($activation = Activation::completed($user))
                                                                Đã kích hoạt
                                                            @else
                                                                Chưa kích hoạt
                                                            @endif
                                                        </th>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </form>
                    </div>
                </div>
                <div id="tab2" class="tab-pane fade">
                    <div class="row">
                        <div class="col-md-12 pd-top">
                            <form class="form-horizontal">
                                <div class="form-body">
                                    <div class="form-group">
                                        {{ csrf_field() }}
                                        <label for="inputpassword" class="col-md-3 control-label">
                                            Nhập mật khẩu
                                            <span class='require' style="color: red">*</span>
                                        </label>
                                        <div class="col-md-9">
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="livicon" data-name="key" data-size="16"
                                                       data-loop="true" data-c="#000" data-hc="#000"></i>
                                                </span>
                                                <input type="password" id="password" placeholder="Nhập mật khẩu"
                                                       name="password" class="form-control"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputnumber" class="col-md-3 control-label">Nhập lại mật khẩu
                                            <span class='require'  style="color: red">*</span>
                                        </label>
                                        <div class="col-md-9">
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="livicon" data-name="key" data-size="16" data-loop="true"
                                                       data-c="#000" data-hc="#000"></i>
                                                </span>
                                                <input type="password" id="password-confirm" class="form-control"
                                                       placeholder="Nhập lại mật khẩu" name="confirm_password"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" class="btn btn-primary qkbtn" id="change-password">Lưu
                                        </button>
                                        <a href="#" type="reset" class="btn btn-warning qkbtn">Đặt lại</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
{{-- page level scripts --}}
@section('footer_scripts')
    <!-- Bootstrap WYSIHTML5 -->
    <script src="{{ asset('assets/vendors/jasny-bootstrap/js/jasny-bootstrap.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#tab1').tab('show');
            $('#change-password').click(function (e) {
                e.preventDefault();
                var check = false;
                if ($('#password').val() === "") {
                    alert('Please Enter password');
                } else if ($('#password').val() !== $('#password-confirm').val()) {
                    alert("confirm password should match with password");
                } else if ($('#password').val() === $('#password-confirm').val()) {
                    check = true;
                }

                if (check == true) {
                    var sendData = '_token=' + $("input[name='_token']").val() + '&password=' + $('#password').val() + '&id=' + {{ $user->id }};
                    var path = "passwordreset";
                    $.ajax({
                        url: path,
                        type: "post",
                        data: sendData,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                        },
                        success: function (data) {
                            $('#password, #password-confirm').val('');
                            alert('password reset successful');
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            alert('error in password reset');
                        }
                    });
                }
            });
        });
    </script>
@stop
