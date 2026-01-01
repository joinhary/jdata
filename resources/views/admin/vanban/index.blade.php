@extends('admin/layouts/default')
@section('title')
    Quản lý Văn bản @parent
@stop
@section('header_styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/pages/tables.css') }}"/>
    <link rel="stylesheet" type="text/css"
          href="{{ asset('assets/vendors/datatables/css/dataTables.bootstrap.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/datatables/css/scroller.bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/fancybox/jquery.fancybox.css') }}"
          media="screen"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/fancybox/jquery.fancybox-thumbs.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/vendors/Buttons/css/buttons.css') }}"/>
    <style>
        mark {
            padding: 0;
            background-color: #ffe456 !important;
        }

        .btn1 {
            font-weight: 500 !important;
            background-color: white !important;
            color: #01bc8c !important;
            font-size: 14px !important;
        }

        .qktd {
            text-align: center;
        }

        .btn2 {
            font-weight: 500 !important;
            background-color: white !important;
            color: #1a67a3 !important;
            font-size: 14px !important;
        }

        .qkghichu {
            color: red;
        }

        .bg-danger {
            background: #e74040 !important;
        }
    </style>
@stop
@section('content')
    <section class="content">
        <form action="{{ route('indexVB') }}">
            <div class="row">
                <div class="col-md-4 search">
                    <input type="text" class="form-control" id="searchbox" name="nangcao"
                           placeholder="Nhập nội dung cần tìm" value="{{$getNangCao}}" autofocus>
                    <span class="fa fa-search fa-search-custom"></span>
                </div>
                <div class="col-md-4">
                    {!! \App\Helpers\Form::select('vb_kieuhd',$kieu_hd,request()->input('vb_kieuhd'),['class'=>'form-control','id'=>'vb_kieuhd']) !!}
                </div>
                <div class="col-md-4">
                    <button class="btn btn-success btn1" type="submit">
                        <i class="fa fa-search"></i>
                        Tìm kiếm
                    </button>
                    <a class="btn btn-primary btn2" href="{{ route('createVB') }}">
                        <i class="fa fa-plus"></i>
                        Thêm mới
                    </a>
                </div>
            </div>
        </form>
        <div class="row">
            @if(request()->input('nangcao') == null && request()->input('vb_kieuhd') == null)
                <a></a>
            @else
                <a>Có <span style="color : red; font-weight: bold;">{{$count}}</span> kết quả được tìm thấy.</a>
            @endif
        </div>
        <div class="row bctk-scrollable-list" style="overflow-x: hidden;">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>
                        Nhãn
                    </th>
                    <th>
                        Kiểu văn bản
                    </th>

                    <th>
                        <i class="fa fa-cog" data-name="edit" data-size="18" data-loop="true" data-c="#428BCA"
                           data-hc="#428BCA" title="edit vaitro"></i>
                    </th>
                </tr>
                </thead>
                <tbody class="p-1">
                @if($vanban->total()==0)
                    <tr>
                        <td colspan="99">
                            <b>Không có dữ liệu</b>
                        </td>
                    </tr>
                @endif
                @foreach($vanban as $item)
                    <tr>
                        <td>
                            {{ $item->vb_nhan }}
                        </td>
                        <td>
                            {{ $item->kieu_hd }}
                        </td>
                        <td style="text-align:center">
                            <a title="Cập nhật văn bản"
                               href="{{ route('editVB', $item->vb_id) }}"
                               class="btn btn-success">
                                Sửa
                            </a>
                            
                        </td>
                    </tr>
                    <div class="modal fade" id="confirm-delstaff-{{$item->vb_id}}" role="dialog"
                         aria-labelledby="modalLabeldanger">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-danger">
                                    <h4 class="modal-title" id="modalLabeldanger">Chú ý!</h4>
                                </div>
                                <div class="modal-body">
                                    <p>Bạn có thực sự muốn xóa văn bản có nhãn là <span
                                            class="qkghichu">{{$item->vb_nhan }}</span> ?</p>
                                </div>
                                <div class="modal-footer">
                                    <form action="{{ route('destroyVB', $item->vb_id) }}" method="get">
                                        <div class="form-inline">
                                            <button type="submit" class="btn btn-danger">Có, xóa!</button>
                                            <a href="#" data-dismiss="modal" class="btn btn-warning">Không</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="col-ms-12">
            <div class="col-sm-6">
                {{$vanban->appends(request()->input())->links()}}
            </div>
            <div class="col-sm-6">
                <p class="pull-right">Tổng số: <b><span
                            style="color: red">{{$tong}}</span></b>
                </p>
            </div>
        </div>
    </section>
@stop
