@extends('admin/layouts/default')

{{-- Page title --}}
@section('title')
    Quản lý đương sự    @parent
@stop

{{-- page level styles --}}
@section('header_styles')
    <link rel="stylesheet" type="text/css"
          href="{{ asset('assets/vendors/datatables/css/dataTables.bootstrap.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/datatables/css/scroller.bootstrap.css') }}">
    <link href="{{ asset('assets/vendors/jstree/css/style.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/vendors/iCheck/css/all.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/vendors/treeview/css/bootstrap-treeview.min.css') }}" rel="stylesheet"
          type="text/css"/>
    <link href="{{ asset('assets/css/pages/treeview_jstree.css') }}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{ asset('assets/vendors/Buttons/css/buttons.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/css/pages/advbuttons.css') }}"/>

    <style type="text/css">
        .btn1 {
            font-weight: 500;
            background-color: white !important;
            color: #01bc8c !important;
            font-size: 14px !important;
        }

        .qktd {
            text-align: center;
        }

        .btn2 {
            font-weight: 500;
            background-color: white !important;
            color: #1a67a3 !important;
            font-size: 14px !important;
        }

        .bg-danger {
            background: #e74040 !important;
        }

        .qkmodel {
            background-color: #1a67a3 !important;
        }
    </style>
@stop
@section('content')
    <section class="content">
        <form action="{{route('indexKhachHang')}}">
            <div class="row">
                <div class="col-md-5 search">
                    <input type="text" class="form-control" id="searchbox" name="tk_khachhang"
                           placeholder="Tìm kiếm tên tên đương sự ..." value="{{$search}}" autofocus>
                    <span class="fa fa-search fa-search-custom"></span>
                </div>
                <div class="col-md-7">
                    <button class="btn btn-success btn1" type="submit">
                        <i class="fa fa-search"></i>
                        Tìm kiếm
                    </button>
                    <a href="javascript:void(0)" data-toggle="modal" data-target="#create-customer"
                       class="btn btn-primary btn2">
                        <i class="fa fa-plus"></i>
                        Thêm mới
                    </a>
                </div>
            </div>
        </form>
        <div class="row">
            @if(request()->input('tk_khachhang') == null)
                <a></a>
            @else
                <a>Có <span style="color : red; font-weight: bold;">{{$count}}</span> kết quả được tìm thấy.</a>
            @endif
        </div>
        <div class="row bctk-scrollable-list" style="overflow-x: hidden;">
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>STT</th>
                    <th>Nhãn đương sự</th>
                    <th>Hôn phối</th>
                    <th>
                        <i class="fa fa-cog"></i>
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($khachhang as $key => $kh)
                    <tr @if ($nganchan && in_array($kh->id,$nganchan)) class="text-red" title="Bị ngăn chặn" @endif>
                        <td class="column-align">{{$kh->kh_id}}</td>
                        @if($nganchan && in_array($kh->kh_id,$nganchan))
                            <td class="col-align-left column-align text-red">
                                {{$kh->first_name}}
                            </td>
                        @else
                            <td class="col-align-left column-align ">
                                {{$kh->first_name}}
                            </td>
                        @endif
                        <td class="col-align-left column-align">{{$vochong[$key]}}
                        <td class="column-align" style="text-align: center">
                            <a title="Chi tiết khách hàng" href="{{route('showKhachHang',['id'=>$kh->kh_id])}}"
                               class="btn btn-primary">
                                Xem
                            </a>
                            @if($kh->id_vp == \App\Models\NhanVienModel::where('nv_id','=',Sentinel::getUser()->id)->first()->nv_vanphong)
                                <a title="Chi tiết khách hàng" href="{{route('editKhachHang',['id' =>$kh->kh_id])}}"
                                   class="btn btn-success" style="margin-left: 0px; !important;">
                                    Sửa
                                </a>
                                <a href="{{route('destroyKhachHang',['id' => $kh->kh_id])}}" id="{{$kh->kh_id}}"
                                   title="Xóa khách hàng"
                                   onclick="return confirm('Bạn có chắc muốn xóa khách hàng \'{{$kh->first_name}}\'?')"
                                   class="btn button-danger">
                                    Xóa
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="col-sm-12">
            <div class="col-sm-6">
                {{$khachhang->appends(request()->input())->links()}}
            </div>
            <div class="col-sm-6">
                <p class="pull-right" style="font-size: 16px;">Tổng số:
                    <b><span style="color: red">{{$tong}}</span></b>
                </p>
            </div>
        </div>
        <div class="modal fade" id="create-customer" role="dialog" aria-labelledby="modalLabelinfo">
            <div class="modal-dialog" role="document">
                <div class="modal-content modal-middle">
                    <div class="modal-header qkmodel">
                        <h4 class="modal-title qkmodel" id="modalLabelinfo">Chọn kiểu đương sự</h4>
                    </div>
                    <div class="modal-body">
                        <div id="treeview-expandible" class="">
                            <div id="tree"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="#" id="create-KH" class="btn btn-primary mb-0 disabled">Tiếp tục</a>
                        <button class="btn btn-default" data-dismiss="modal">Hủy</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="change-type" role="dialog" aria-labelledby="modalLabelinfo">
            <div class="modal-dialog" role="document">
                <div class="modal-content modal-middle">
                    <div class="modal-header bg-info">
                        <h4 class="modal-title" id="modalLabelinfo">Chọn kiểu cần đổi cho đương sự "<u><span
                                        id="nhan-ds"></span></u>"</h4>
                    </div>
                    <form action="#" method="post">
                        {{csrf_field()}}
                        <div class="modal-body">
                            <div id="treeview-expandible" class="">
                                <div id="tree-change"></div>
                                <input type="text" name="kh_id" hidden>
                                <input type="text" name="k_old" hidden>
                                <input type="text" name="k_new" hidden>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="" id="change-type-KH" class="btn btn-primary mb-0 disabled"
                               onclick="return confirm('Bạn có chắc muốn đổi kiểu khách hàng?')">Tiếp tục</a>
                            <button class="btn btn-default" data-dismiss="modal">Hủy</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@stop
@section('footer_scripts')
    <script type="text/javascript" src="{{ asset('assets/vendors/datatables/js/jquery.dataTables.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/datatables/js/dataTables.bootstrap.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/datatables/js/dataTables.colReorder.js') }}"></script>
    <script src="{{ asset('assets/vendors/jstree/js/jstree.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/vendors/treeview/js/bootstrap-treeview.min.js') }}" type="text/javascript"></script>
    <script>
        function delete_customer(value) {
            var info = $('#nhan' + value.id).text();
            $('#confirm-info').html('<b>"' + info + '"</b>');
            $('#confirm-delcustomer').modal('show');
            $('#submit-delete').click(function () {
                var url = "{{url('admin/customer/delete').'/'}}" + value.id;
                $.ajax({
                    url: url,
                    type: 'POST',
                    success: function (res) {
                        $('#confirm-delcustomer').modal('hide');
                        if (res.status === 'success') {
                            toastr.success(res.message,
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
                        } else {
                            toastr.error(res.message,
                                toastr.options = {
                                    "closeButton": false,
                                    "debug": true,
                                    "newestOnTop": false,
                                    "progressBar": true,
                                    "positionClass": "toast-bottom-right",
                                    "preventDuplicates": true,
                                    "showDuration": "300",
                                    "hideDuration": "1000",
                                    "timeOut": "5000",
                                    "extendedTimeOut": "1000",
                                    "showEasing": "swing",
                                    "hideEasing": "linear",
                                    "showMethod": "fadeIn",
                                    "hideMethod": "fadeOut"
                                }
                            );
                        }
                    }
                })
            })
        }

        var urlChange = '';
        var khID = '';
        var k_new = '';
        $.ajax({
            url: "{{route('getKieu')}}",
            type: "GET",
            data: "keyword=duong-su",
            success: function (kieu) {
                var treedata = [];
                $.each(kieu.data, function (k, v) {
                    if (v.state.expanded === true) {
                        v.selectable = false;
                    }
                    treedata.push(v);
                });
                $('#tree').treeview({
                    color: "#418bca",
                    expandIcon: 'fa fa-plus',
                    collapseIcon: 'fa fa-minus',
                    nodeIcon: 'fa fa-book',
                    data: treedata,
                    onNodeSelected: function (event, data) {
                        var id = data.k_id;
                        var action = '{{url('admin/customer/create')}}?kieu=' + id + '&label=' + $('#searchbox').val();
                        $('#create-KH').removeClass('disabled');
                        $('#current').val(data.k_parent);
                        $('#child').val(id);
                        $('#create-KH').attr('href', action);
                        document.getElementById("create-KH").click();

                        // window.open(action);
                    },
                    onNodeUnselected: function () {
                        $('#create-KH').addClass('disabled');
                    }
                });
                $('#tree-change').treeview({
                    color: "#418bca",
                    expandIcon: 'fa fa-plus',
                    collapseIcon: 'fa fa-minus',
                    nodeIcon: 'fa fa-book',
                    data: treedata,
                    onNodeSelected: function (event, data) {
                        var id = data.k_id;
                        k_new = id;
                        $('#current').val(data.k_parent);
                        $('#child').val(id);
                        urlChange = '{{url('admin/customer/change_type_kh')}}/' + khID + '/' + k_new;
                        $('#change-type-KH').attr('href', urlChange);
                        $('#change-type-KH').removeClass('disabled');
                    },
                    onNodeUnselected: function () {
                        $('#change-type-KH').addClass('disabled');
                    }
                });
            }
        });

        function change_type_kh(kh, nhanKH) {
            khID = kh.id;
            $('#nhan-ds').html(nhanKH);
            $('#change-type').modal('show');
        }

    </script>
@stop

