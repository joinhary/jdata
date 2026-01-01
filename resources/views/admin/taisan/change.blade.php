@extends('admin/layouts/default')

{{-- Page title --}}
@section('title')
Đổi kiểu Tài sản    @parent
@stop

{{-- page level styles --}}
@section('header_styles')
    <meta name="_token" content="{{ csrf_token() }}">
    <link href="{{ asset('assets/css/pages/toastr.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/vendors/toastr/css/toastr.css') }}" rel="stylesheet" type="text/css"/>

    <link href="{{ asset('assets/vendors/jstree/css/style.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/vendors/iCheck/css/all.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/vendors/treeview/css/bootstrap-treeview.min.css') }}" rel="stylesheet"
          type="text/css"/>
    <link href="{{ asset('assets/css/pages/treeview_jstree.css') }}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/iCheck/css/all.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/iCheck/css/line/line.css') }}"/>
    <link rel="stylesheet" type="text/css"
          href="{{ asset('assets/vendors/awesomeBootstrapCheckbox/awesome-bootstrap-checkbox.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/pages/formelements.css') }}"/>
@stop

{{-- Page content --}}
@section('content')
{{--
    <section class="content-header">
        <h1>Quản lý tài sản</h1>
        <a href="{{ route('indexTaiSan') }}"><i class="fa fa-arrow-left"></i> Trở lại</a>
    </section>
--}}
    <section class="content">
        <form method="POST" action="#" class="form-create">
            {{csrf_field()}}
            <div class="row">
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h3 class="panel-title">
                                        <i class="fa fa-dollar"></i> Chọn kiểu tài sản mới cần đổi
                                    </h3>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div id="treeview-expandible" class="">
                                                <div id="tree"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-info">
                                <div class="panel-body">
                                    <div class="col-md-12">
                                        <a href="" id="doiKieuTaiSan" class="btn btn-block btn-primary disabled"
                                           onclick="return confirm('Bạn có chắc là đổi kiểu tài sản ?')">
                                            Đổi kiểu
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
@stop
@section('footer_scripts')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script src="{{ asset('assets/vendors/jstree/js/jstree.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/vendors/treeview/js/bootstrap-treeview.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/vendors/iCheck/js/icheck.js') }}" type="text/javascript"></script>
    <script language="javascript" type="text/javascript"
            src="{{ asset('assets/vendors/bootstrap-switch/js/bootstrap-switch.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.select2').select2();
        })
    </script>
    <script>
        $.ajax({
            url: "{{route('getKieuTaiSan')}}",
            type: "GET",
            success: function (kieu) {
                var treedata = [];
                $.each(kieu.data, function (k, v) {
                    treedata.push(v);
                });
                $('#tree').treeview({
                    color: "#418bca",
                    expandIcon: 'fa fa-plus',
                    collapseIcon: 'fa fa-minus',
                    nodeIcon: 'fa fa-book',
                    data: treedata,
                    onNodeSelected: function (event, data) {
                        var id = data.k_id
                        var linkCreate = '{{url('admin/assets/change')}}/{{ $id }}/' + id;
                        $('#doiKieuTaiSan').removeClass('disabled');
                        $('#current').val(data.k_parent);
                        $('#child').val(id);
                        $('#doiKieuTaiSan').attr('href', linkCreate);
                    },
                    onNodeUnselected: function () {
                        $('#doiKieuTaiSan').addClass('disabled');
                    }
                });
                if (kieu.data.length === 0) {
                    $('#themKieu').removeClass('disabled');
                    $('#current').val(0);
                    $('#child').prop('disabled', 'disabled');
                }
            }
        });
    </script>
@stop