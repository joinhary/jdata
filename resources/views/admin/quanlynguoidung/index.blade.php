@extends('admin/layouts/default')
@section('title')
    Quản trị tài khoản
    @parent
@stop
@section('header_styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/select2/css/select2.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/select2/css/select2-bootstrap.css') }}"/>
    <link href="{{ asset('assets/css/pages/form2.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/css/pages/form3.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/vendors/jasny-bootstrap/css/jasny-bootstrap.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/vendors/bootstrapvalidator/css/bootstrapValidator.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/vendors/iCheck/css/all.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/vendors/modal/css/component.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/css/pages/advmodals.css') }}" rel="stylesheet"/>
    <link rel="stylesheet" href="{{ asset('assets/vendors/Buttons/css/buttons.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/css/pages/advbuttons.css') }}"/>
    <link rel="stylesheet" type="text/css"
          href="{{ asset('assets/vendors/datatables/css/dataTables.bootstrap.css') }}"/>
    <link href="{{ asset('assets/css/pages/tables.css') }}" rel="stylesheet" type="text/css"/>
    <style>
        .pagination > li > a, .pagination > li > span {
            float: initial !important;
        }

        .dataTables_paginate a {
        }

        .form-inline {
            display: block !important;
        }

        .btn2 {
            font-weight: 500;
            background-color: white !important;
            color: #1a67a3 !important;
            font-size: 14px !important;
            margin-top: 10px;
        }

        .btn1 {
            font-weight: 500;
            background-color: white !important;
            color: #01bc8c !important;
            font-size: 14px !important;
        }

        .qkmodel {
            background-color: #1a67a3 !important;
        }
    </style>
@stop
@section('content')
    <div class="row">
        <div class="col-md-3">
            <label style="padding-left: 10px">Khối dữ liệu</label>
        </div>
        <div class="col-md-3">
            <label style="padding-left: 10px">Khối dữ liệu</label>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="col-md-12">
                <select name="filter_khoi" id="filter_khoi" class="form-control" {{ count($khoi) == 1?'disabled':'' }}>
                    @foreach($khoi as $item)
                        <option value="{{ $item }}">{{ $item }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="col-md-12">
                <select id="filter_roles" class="form-control select2" name="filter_roles">
                    <option value="" selected="selected">Chọn phân quyền</option>
                    @foreach($roles as $item)
                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-2" style="color:black !important;">
           <select name="vanphong_id" id="vanphong_id" class="form-control">
    <option value="">-- Chọn văn phòng --</option>
    @foreach ($vanphong as $vp)
        <option value="{{ $vp->cn_id }}"
            {{ old('vanphong_id', $vanphong_sl ?? '') == $vp->cn_id ? 'selected' : '' }}>
            {{ $vp->cn_ten }}
        </option>
    @endforeach
</select>

    </div>
        

        <div class="col-md-4">
            <div class="col-md-6">
                <input maxlength="255" type="text" class="form-control" name="searching" id="searching"
                       placeholder="Tìm kiếm...">
            </div>
            <div class="col-md-6">
                <button type="button" class="btn btn-success btn1" id="btn_searching" onclick="searching()">
                    <i class="fa fa-search"></i>
                    Tìm kiếm
                </button>
                {{--                <a type="button" href="#" class="btn btn-primary btn2"--}}
                {{--                   data-toggle="modal" data-target="#modalRegister">--}}
                {{--                    <i class="fa fa-plus"></i>--}}
                {{--                    Thêm mới--}}
                {{--                </a>--}}
            </div>
        </div>
    </div>
    <div class="col-md-12 bctk-scrollable-list" style="overflow-x: hidden;">
        <table class="table table-bordered table-hover" id="table-users" style="width: 100%!important;">
            <thead>
            <tr>
                <th>ID</th>
                <th>Họ và tên</th>
                <th>E-mail</th>
                <th>Phân quyền</th>
                <th>Trạng thái</th>
                <th>VP</th>
                <th>Thời gian tạo</th>
                <th width="25%"><i class="fa fa-cog"></i></th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    <div class="modal fade" id="modalRegister" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header qkmodel">
                    <h5 class="modal-title qkmodel" id="myModalLabel">Đăng Ký</h5>
                </div>
                <div class="modal-body">
                    <form role="form" id="formRegister" action="{{ route('admin.manager.users.register') }}"
                          method="post">
                        @csrf
                        <div class="col-md-12">
                            <div class="form-group">
                                <input type="text" name="first_name" id="first_name" class="form-control input-md"
                                       placeholder="Họ và tên" tabindex="1" data-error="Username must be enetered"
                                       required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <input type="email" name="email" class="form-control input-md"
                                       placeholder="Email" tabindex="2"
                                       data-error="that email address is invalid" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-6">
                                <div class="form-group">
                                    <input type="password" name="password" id="password" class="form-control input-md"
                                           placeholder="Mật khẩu" tabindex="3" required minlength="6"
                                           data-bv-stringlength-message="Ít nhất 6 ký tự">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-6">
                                <div class="form-group">
                                    <input type="password" name="password_confirm" class="form-control input-md"
                                           placeholder="Nhập lại mật khẩu" data-match="#password"
                                           data-match-error="conform passwork should be same as password" tabindex="4"
                                           required minlength="6" data-bv-stringlength-message="Ít nhất 6 ký tự">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <input type="text" name="phone" id="phone" class="form-control input-md"
                                       placeholder="Số điện thoại" tabindex="5" data-error="Phone"
                                       required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <input type="text" name="address" id="address" class="form-control input-md"
                                       placeholder="Số nhà - đường, phường/xã, quận/huyện, tỉnh/thành phố" tabindex="6"
                                       data-error="Address"
                                       required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="roles" class="control-label">Phân quyền</label>
                                    <select id="roles" class="form-control select2" name="roles" tabindex="7">
                                        <option value="" selected="selected">Chọn phân quyền</option>
                                        @foreach($roles as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12" style="padding-left: 6%;">
                                <div class="form-group">
                                    <div class="checkbox">
                                        <label for="activate">
                                            <input type="checkbox" name="activate" id="activate"
                                                   class="custom-checkbox" tabindex="8"> Kích hoạt
                                        </label>
                                    </div>
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row marginTop">
                            <div class="col-xs-6 col-md-6" style="padding-top: 10px;">
                                <a id="reset"
                                   class="btn btn-danger btn-block btn-md btn-responsive resetModal"
                                   tabindex="10">Reset</a>
                            </div>
                            <div class="col-xs-6 col-md-6">
                                <input type="submit" id="btncheck" value="Đăng ký"
                                       class="btn btn-primary btn-block btn-md btn-responsive" tabindex="9">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    {{--end--}}
    {{--Modal change permission - password--}}


    <div class="modal fade" id="modal-change-password" tabindex="-1" role="dialog" aria-labelledby="modalChangePassword"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header qkmodel">
                    <h5 class="modal-title" id="myModalLabel">
                        Đổi mật khẩu & Phân quyền
                    </h5>
                </div>
                <div class="modal-body">
                    <form role="form" id="formChangePassword"
                          action="{{ route('admin.manager.users.change_password') }}" method="post">
                        @csrf
                        <input type="text" name="id_user_change" id="id_user_change" hidden>
                        <div class="col-md-12">
                            <p style="color: red;">Nếu không đổi mật khẩu thì vui lòng để trống</p>
                            <div class="form-group">
                                <input type="password" name="password_change" id="password-change"
                                       class="form-control input-md"
                                       placeholder="Mật khẩu" minlength="6">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <input type="password" name="password_change_confirm" class="form-control input-md"
                                       placeholder="Nhập lại mật khẩu" minlength="6">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <span>Phân quyền</span>
                                    <select id="roles-change" class="form-control select2" name="roles_change">
                                        <option value="" selected="selected">Chọn phân quyền</option>
                                        @foreach($roles as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        {{--                        <div hidden="" class="row">--}}
                        {{--                            <div class="col-md-12">--}}
                        {{--                                <div class="form-group">--}}
                        {{--                                    <span>Phân quyền cá nhân</span>--}}
                        {{--                                    <select id="roles-personal-change" style="width: 100%;" class="form-control select2"--}}
                        {{--                                            name="roles_personal_change[]" multiple="multiple">--}}
                        {{--                                        @foreach($permission as $item)--}}
                        {{--                                            <option value="{{ $item->permissions }}">{{ $item->permissions }}</option>--}}
                        {{--                                        @endforeach--}}
                        {{--                                    </select>--}}
                        {{--                                </div>--}}
                        {{--                            </div>--}}
                        {{--                        </div>--}}
                        <div class="row">
                            <div class="col-md-12" style="padding-left: 6%;">
                                <div class="form-group">
                                    <div class="checkbox">
                                        <label for="activate-changepassword">
                                            <input type="checkbox" name="activate_changepassword"
                                                   id="activate-changepassword"
                                                   class="custom-checkbox"> Xác nhận
                                        </label>
                                    </div>
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row marginTop">
                            <div class="col-md-12">
                                <input type="submit" value="Lưu"
                                       class="btn btn-primary btn-block btn-md btn-responsive form-control">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
    {{--end--}}
    <!-- fullwidth modal info-->
    <div class="modal fade in" id="modal-info" tabindex="-1" role="dialog" aria-hidden="false">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header qkmodel">
                    <h5 class="modal-title">
                        Thông tin người dùng <b id="title-info-name-user"></b>
                    </h5>
                </div>
                <div class="modal-body">
                    <div id="table-info-user"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-default">Đóng</button>
                </div>
            </div>
        </div>
    </div>
    <!-- END modal-->
@stop

{{-- page level scripts --}}
@section('footer_scripts')
    <script type="text/javascript" src="{{ asset('assets/vendors/select2/js/select2.js') }}"></script>

    <script type="text/javascript" src="{{ asset('assets/vendors/Buttons/js/scrollto.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/Buttons/js/buttons.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/modal/js/classie.js')}}"></script>
    <script src="{{ asset('assets/vendors/jasny-bootstrap/js/jasny-bootstrap.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('assets/vendors/bootstrapvalidator/js/bootstrapValidator.min.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('assets/vendors/iCheck/js/icheck.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/datatables/js/jquery.dataTables.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/datatables/js/dataTables.bootstrap.js') }}"></script>
    {{--<script src="{{ asset('assets/js/pages/validation.js') }}" type="text/javascript"></script>--}}
    <script>
        $('#roles-personal-change').select2();
        // $('.select2').select2();
        $('#activate_changepassword').on('ifChanged', function (event) {
            $('#formChangePassword').bootstrapValidator('revalidateField', $('#activate_changepassword'));
        });
        $("#formChangePassword").bootstrapValidator({
            fields: {
                activate_changepassword: {
                    validators: {
                        choice: {
                            min: 1,
                            message: 'Vui lòng xác nhận'
                        }
                    }
                },
            }
        });
        $('#formRegister').bootstrapValidator({
            excluded: [':disabled'],
            fields: {
                first_name: {
                    validators: {
                        notEmpty: {
                            message: 'Vui lòng nhập họ và tên'
                        }
                    }
                },
                phone: {
                    validators: {
                        notEmpty: {
                            message: 'Vui lòng nhập số điện thoại'
                        },
                        digits: {
                            message: 'Vui lòng nhập số'
                        },
                        phone: {
                            country: 'US',
                            message: 'Số điện thoại 10 số'
                        }
                    }
                },
                email: {
                    validators: {
                        notEmpty: {
                            message: 'Vui lòng nhập email'
                        },
                        emailAddress: {
                            message: 'Dữ liệu nhập không phải là địa chỉ email hợp lệ'
                        },
                    }
                },
                address: {
                    validators: {
                        notEmpty: {
                            message: 'Vui lòng nhập địa chỉ'
                        }
                    }
                },
                password: {
                    validators: {
                        notEmpty: {
                            message: 'Vui lòng nhập mật khẩu'
                        },
                        different: {
                            field: 'first_name',
                            message: 'Mật khẩu không được khớp với tên'
                        }
                    }
                },
                confirmpassword: {
                    validators: {
                        notEmpty: {
                            message: 'Yêu cầu mật khẩu xác nhận không được để trống'
                        },
                        identical: {
                            field: 'password'
                        },
                        different: {
                            field: 'first_name',
                            message: 'Mật khẩu không được khớp với tên'
                        }
                    }
                },
                roles: {
                    validators: {
                        notEmpty: {
                            message: 'Vui lòng chọn quyền'
                        }
                    }
                },
            }
        });
        $('#reset').on('click', function () {
            $('#formRegister').bootstrapValidator("resetForm", true);
            $("#formRegister").find(".icheckbox_minimal-blue").removeClass('checked');
        });
    </script>


    <script>
        var table = $('#table-users').DataTable({
            "bLengthChange": false, //thought this line could hide the LengthMenu
            // "lengthMenu": [[50, 100, -1], [50, 100, "All"]],
            "language": {
                "decimal": "",
                "emptyTable": "Không có dữ liệu trong bảng",
                "info": "Đang hiển thị _START_ tới _END_ của _TOTAL_ dòng",
                "infoEmpty": "Showing 0 to 0 of 0 entries",
                "infoFiltered": "(filtered from _MAX_ total entries)",
                "infoPostFix": "",
                "thousands": ",",
                "pagingType": "simple",
                // "lengthMenu": "Hiện _MENU_ mục",
                "loadingRecords": "Đang lấy dữ liệu...",
                "processing": "Đang tải...",
                "search": "Tìm kiếm:",
                "zeroRecords": "Không tìm thấy kết quả",
                "paginate": {
                    "first": "Trước nhất",
                    "last": "Sau cùng",
                    "next": "Sau",
                    "previous": "Trước"
                },
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending"
                }
            },
            // "responsive": true,
            "paging": true,
            "deferRender": true,
            "processing": true,
            // "deferLoading": [57, 100],
            "serverSide": true,
            // "autoWidth": true,
            "info": false,

            // "pageLength": 50,
            // "scrollY": 500,
            "stateSave": true,
            "searching": false,
            // "order": [[3, 'desc']],
            "ajax": {
                "url": '{!! route('admin.manager.users.data') !!}',
                "data": function (d) {
                    d.filter_khoi = $('#filter_khoi').val();
                    d.filter_roles = $('#filter_roles').val();
                    d.searching = $('#searching').val();
                    d.vanphong_id = $('#vanphong_id').val();
                    
                }
            },
   
            columns: [
                  { data: 'id', name: 'id' },
    { data: 'first_name', name: 'first_name' },
    { data: 'email', name: 'email' },
    { data: 'role_name', name: 'role_name' }, // ✅ ĐÚNG
    { data: 'status', name: 'status' },
    { data: 'cn_ten', name: 'cn_ten' },
    { data: 'created_at', name: 'created_at' },
                {
                    data: 'id',
                    "orderable": false,
                    "searchable": false,
                    "render": function (data, type, row, meta) {
                        var user = '{{ \Sentinel::getUser()->id }}';
                        var info = '<a class="btn btn-primary" href="javascript:void(0)" onclick="ModalInfo(' + data + ')" title="Thông tin người dùng"><i class="fa fa-fw fa-info-circle"></i></a>';
                        var changePassword = '<a class="btn btn-success" href="javascript:void(0)" onclick="ModalChangePassword(' + data + ')" title="Đổi mật khẩu hoặc phân quyền"><i class="fa fa-fw fa-pencil-square-o"></i></a>';
                        var history = '<a class="btn btn-warning" href="{{ url('admin/manager/users/diary') }}/' + data + '" title="Lịch sử hoạt động" target="_blank"><i class="fa fa-fw fa-list-alt"></i></a>';
                        var active = '<a class="btn btn-success" href="javascript:void(0)" onclick="Active(' + data + ')" title="Mở khoá người dùng"><i class="fa fa-fw fa-check-square-o"></i></a>';
                        var block = '<a class="btn btn-secondary" href="javascript:void(0)" onclick="Block(' + data + ')" title="Khoá người dùng"><i class="fa fa-fw fa-times-circle"></i></a>';
                      
        // Conditionally display the delete button
        var deleteUser = '';
        // alert(user);
        if (user == 1559 ) { 
            deleteUser = '<a class="btn btn-danger hh" onclick="delete_confirm(' + data + ')" title="Xoá người dùng"><i class="fa fa-fw fa-trash-o"></i></a>';
        }
                        return info + changePassword + history + active + block + deleteUser;
                    }
                }
            ]
        });

        function reset_table() {
            table.ajax.reload();
        }

        function searching() {
    if ($('#searching').val() !== '') {

        $('#filter_roles').val('').trigger('change');
        $('#vanphong_id').val('').trigger('change');

        reset_table();
    }
}


        $('#filter_khoi').on('change', function () {
            reset_table();
        });

        $('#filter_roles').on('change', function () {
            reset_table();
        });
         $('#vanphong_id').on('change', function () {
            reset_table();
        });

        table.on('draw', function () {
            // $('.livicon').each(function () {
            //     $(this).updateLivicon();
            // });
        });

        function success_message(message) {
            toastr.success(message,
                toastr.options = {
                    "closeButton": false,
                    "debug": true,
                    "newestOnTop": false,
                    "progressBar": true,
                    "positionClass": "toast-bottom-right",
                    "preventDuplicates": true,
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "3000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                }
            );
        }

        function warning_message(message) {
            toastr.warning(message,
                toastr.options = {
                    "closeButton": false,
                    "debug": true,
                    "newestOnTop": false,
                    "progressBar": true,
                    "positionClass": "toast-bottom-right",
                    "preventDuplicates": true,
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "3000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                }
            );
        }

        function Active(e) {
            $.ajax({
                type: 'GET',
                url: '{{ route('admin.manager.users.ajax_active') }}',
                data: {
                    'id': e
                },
                success: function (result) {
                    if (result.status == true) {
                        reset_table();
                        success_message(result.message);
                    } else {
                        warning_message(result.message)
                    }
                }
            });
        }

        function Block(e) {
            $.ajax({
                type: 'GET',
                url: '{{ route('admin.manager.users.ajax_block') }}',
                data: {
                    'id': e
                },
                success: function (result) {
                    if (result.status == true) {
                        reset_table();
                        success_message(result.message);
                    } else {
                        warning_message(result.message)
                    }
                }
            });
        }

        $("#modal-info").on('hide.bs.modal', function () {
            $('#title-info-name-user').empty();
            $('#table-info-user').empty();
        });

        function ModalInfo(e) {
            $.ajax({
                type: 'GET',
                url: '{{ route('admin.manager.users.info_user') }}',
                data: {'id': e},
                success: function (result) {
                    // $('#title-info-name-user').empty();
                    document.getElementById("title-info-name-user").innerHTML = result.first_name;
                    // $('#table-info-user').empty();
                    $('#table-info-user').append('<table class="table table-bordered table-striped">' +
                        '                           <tr>\n' +
                        '                                <td>Họ và tên</td>\n' +
                        '                                <td>\n' + result.first_name +
                        '                                </td>\n' +
                        '                            </tr>\n' +
                        '                            <tr>\n' +
                        '                                <td>@lang('users/title.email')</td>\n' +
                        '                                <td>\n' + result.email +
                        '                                </td>\n' +
                        '                            </tr>\n' +
                        '                            <tr>\n' +
                        '                                <td>\n' +
                        '                                    Giới tính' +
                        '                                </td>\n' +
                        '                                <td>\n' + result.gender +
                        '                                </td>\n' +
                        '                            </tr>\n' +
                        '                            <tr>\n' +
                        '                                <td>Năm sinh</td>\n' +
                        '                                <td>' + result.dob +
                        '                               </td>\n' +
                        '                            </tr>\n' +
                        '                            <tr>\n' +
                        '                                <td>Quốc gia</td>\n' +
                        '                                <td>\n' + result.country +
                        '                                </td>\n' +
                        '                            </tr>\n' +
                        '                            <tr>\n' +
                        '                                <td>Thành phố</td>\n' +
                        '                                <td>\n' + result.city +
                        '                                </td>\n' +
                        '                            </tr>\n' +
                        '                            <tr>\n' +
                        '                                <td>Địa chỉ</td>\n' +
                        '                                <td>\n' + result.address +
                        '                                </td>\n' +
                        '                            </tr>\n' +
                        {{--'                            <tr>\n' +--}}
                            {{--'                                <td>@lang('users/title.postal')</td>\n' +--}}
                            {{--'                                <td>\n' +result.postal +--}}
                            {{--'                                </td>\n' +--}}
                            {{--'                            </tr>\n' +--}}
                            '                            <tr>\n' +
                        '                                <td>Thời gian tạo</td>\n' +
                        '                                <td>\n' + result.created_at +
                        '                                </td>\n' +
                        '                            </tr></table>');
                }
            });
            $('#modal-info').modal();
        }

        function ModalChangePassword(e) {
            $('#modal-change-password').modal();
            $('#id_user_change').val(e);
        }

        function delete_confirm(e) {
            $('#delete_confirm').modal();
            $('#id_user').val(e);
        }
    </script>

    <div class="modal fade" id="delete_confirm" tabindex="-1" role="dialog" aria-labelledby="user_delete_confirm_title"
         aria-hidden="true">
        <div class="modal-dialog">
            <form method="post" action="{{route('admin.manager.users.destroy')}}">
                @csrf
                <input type="text" name="id_user" id="id_user" hidden>
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="user_delete_confirm_title">Xoá dữ liệu</h4>
                    </div>
                    <div class="modal-body">
                        <p style="color: white">Bạn có muốn xoá dữ liệu này không</p>
                    </div>
                    <div class="modal-footer ">

                        <a type="button" class="btn btn-default mt-1 text-black-50" data-dismiss="modal">Không</a>
                        <button type="submit" class="btn btn-danger mt-1">Có</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop
