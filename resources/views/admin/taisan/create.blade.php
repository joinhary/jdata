@extends('admin/layouts/default')
@section('title')
    Tạo tài sản mới @parent
@stop
@section('header_styles')
    <link href="{{ asset('assets/vendors/treeview/css/bootstrap-treeview.min.css') }}"/>
    <link href="{{ asset('assets/css/pages/treeview_jstree.css') }}" rel="stylesheet" type="text/css"/>
@stop
@section('content')
    <section class="content">
        <form method="POST" action="#" class="form-create">
            @csrf
            <div class="row">
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h5 class="panel-title">
                                        Chọn kiểu tài sản
                                    </h5>
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
                                        <a href="" id="themKieuTaiSan" class="btn btn-block btn-primary disabled"
                                           onclick="return confirm('Bạn có chắc là thêm tài sản mới ?')">
                                            Thêm tài sản
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
    <script src="{{ asset('assets/vendors/jstree/js/jstree.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/vendors/treeview/js/bootstrap-treeview.min.js') }}" type="text/javascript"></script>
    <script>
        $.ajax({
            url: "{{route('getKieu')}}",
            type: "GET",
            data: "keyword=tai-san",
            success: function (kieu) {
                var treedata = [];
                $.each(kieu.data, function (k, v) {
                    treedata.push(v);
                });
                $('#tree').treeview({
                    color: "#000000",
                    expandIcon: 'fa fa-plus',
                    collapseIcon: 'fa fa-minus',
                    nodeIcon: 'fa fa-book',
                    data: treedata,
                    onNodeSelected: function (event, data) {
                        var id = data.k_id;
                        var action = '{{url('admin/assets/showcreate')}}/' + id + '?label=' + "{{\Request::get('label')}}";
                        $('#create-taisan').removeClass('disabled');
                        $('#current').val(data.k_parent);
                        $('#child').val(id);
                        $('#themKieuTaiSan').attr('href', action);
                        if (!data.nodes) {
                            document.getElementById("themKieuTaiSan").click();
                        } else {
                            document.getElementById("themKieuTaiSan").click();

                        }
                    },
                    onNodeUnselected: function () {
                        $('#themKieuTaiSan').addClass('disabled');
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