@extends('admin/layouts/default')
@section('title')
    Quản lý chi nhánh ngân hàng @parent
@stop
@section('header_styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/pages/tables.css') }}"/>
    <link rel="stylesheet" type="text/css"
          href="{{ asset('assets/vendors/datatables/css/dataTables.bootstrap.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/datatables/css/scroller.bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/pages/tables.css') }}"/>
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

    </style>
@stop
@section('content')
    <section class="content">
        <form action="{{route('indexBank')}}">
            <div class="row">
                <div class="col-md-5 search">
                    <input type="text" class="form-control" id="searchbox" name="name"
                           placeholder="Tìm kiếm theo mã hoặc tên ngân hàng..." value="{{$search}}" autofocus>
                    <span class="fa fa-search fa-search-custom"></span>
                </div>
                <div class="col-md-7">
                    <button class="btn btn-success btn1" type="submit">
                        <i class="fa fa-search"></i>
                        Tìm kiếm
                    </button>
                    <a href="create" class="btn btn-primary btn2">
                        <i class="fa fa-plus"></i>
                        Thêm mới
                    </a>
                </div>
            </div>
        </form>
        <div class="row">
            @if(request()->input('name') == null)
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
                    <th>Mã</th>
                    <th>Tên ngân hàng</th>
                    <th>Hành động</th>
    
        
                    {{-- <th><i class="fa fa-cog"></i></th> --}}
                </tr>
                </thead>
                <tbody>
                @foreach($bank as $nv)
                    <tr>
                        <td align="center" class="column-align" style="text-align: center ">{{$loop->iteration}}</td>
                        <td class="column-align" style="text-align: center ">{{$nv->order_number}}</td>
                        <td class="column-align"> {{$nv->name}}</td>
                        <td class="column-align qktd">
                            <a title="Chi tiết ngân hàng" href="{{route('showBank',['id' => $nv->id])}}"
                               class="btn btn-primary">
                                Xem
                            </a>
                            @if($nv->id == Sentinel::getUser()->id)
                            @else
                                <a title="Cập nhật thông tin ngân hàng"
                                   href="{{route('editBank',['id' => $nv->id])}}"
                                   class="btn btn-success">
                                    Sửa
                                </a>
                                <a title="Xóa" href="#" data-toggle="modal"
                                   data-target="#confirm-delstaff-{{$nv->id}}"
                                   class="btn btn-danger">
                                    Xóa
                                </a>
                            @endif
                        </td>
                    </tr>
                    <div class="modal fade" id="confirm-delstaff-{{$nv->id}}" role="dialog"
                         aria-labelledby="modalLabeldanger">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-danger">
                                    <h4 class="modal-title" id="modalLabeldanger">Chú ý!</h4>
                                </div>
                                <div class="modal-body">
                                    <p>Bạn có thực sự muốn xóa [{{$nv->id.'] - ['.$nv->name.']'}}?</p>
                                </div>
                                <div class="modal-footer">
                                    <form action="{{route('destroyBank',['id' => $nv->id])}}" method="get">
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
        <div class="col-sm-12">
            <div class="col-sm-6">
                {{$bank->appends(request()->input())->links()}}
            </div>
            <div class="col-sm-6">
                <p class="pull-right" style="font-size: 16px;">Tổng số:
                    <b><span style="color: red">{{$tong}}</span></b>
                </p>
            </div>
        </div>
    </section>
@stop
@section('footer_scripts')
@stop
