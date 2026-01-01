@extends('admin/layouts/default')
@section('title')
    Quản lý văn phòng @parent
@stop
@section('header_styles')
    <link rel="stylesheet" href="{{ asset('assets/vendors/animate/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/only_dashboard.css') }}"/>
    <meta name="_token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/morrisjs/morris.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/dashboard2.css') }}"/>
    <style>
        label {
            font-size: 13px !important;
        }

        .form-control {
            font-size: 13px !important;
        }

        .qk {
            background: #f2f1f5;
        }

        .qkbtn {
            font-weight: 500;
            background-color: white !important;
            color: #1a67a3 !important;
            font-size: 14px !important;
        }

        .qkbtn2 {
            font-weight: 500;
            background-color: white !important;
            color: #01bc8c !important;
            font-size: 14px !important;
        }

        .bg-danger {
            background: #e74040 !important;
        }
    </style>
@stop
@section('content')
    <section class="content">
        <form>
            <div class="row">
                <div class="col-md-5 search">
                    <input type="text" value="{{request()->input('cn_ten')}}" class="form-control" id="searchbox"
                           name="cn_ten" placeholder="Nhập tên văn phòng cần tìm..." autofocus>
                    <span class="fa fa-search fa-search-custom"></span>
                </div>
                <div class="col-md-7">
                    <button class="btn btn-success qkbtn2" type="submit">
                        <i class="fa fa-search"></i>
                        Tìm kiếm
                    </button>
                    <a href="{{route('createChiNhanh')}}" class="btn btn-primary qkbtn">
                        <i class="fa fa-plus"></i>
                        Thêm mới
                    </a>
                </div>
            </div>
        </form>
        <div class="row">
            @if(request()->input('cn_ten') == null)
                <a></a>
            @else
                <a style="margin-left: 10px;">Có <span style="color : red; font-weight: bold;">{{$count}}</span> kết quả
                    được tìm thấy.</a>
            @endif
        </div>
        <div class="scrollable-list-custom" style="height: calc(90vh - 100px);">
            @foreach($chinhanh as $cn)
                <div class="row row-branches row-custom qk">
                    <div class="col-md-9 col-branches">
                        <a style="font-weight: 500;color: blue" title="Xem thông tin chi tiết"
                           href="{{route('showChiNhanh',['id'=>$cn->cn_id])}}">{{$cn->cn_ten}}</a>
                        <br> Số {{$cn->cn_diachi}}, {{$cn->cn_ap}}, {{$cn->cn_phuong}}, {{$cn->cn_quan}}
                        , {{$cn->cn_tinh}}.
                    </div>
                    <div class="col-md-3 text-center col-actions">
                        <a title="Xem thông tin văn phòng" href="{{route('showChiNhanh',['id'=>$cn->cn_id])}}"
                           class="btn btn-primary">Xem</a>
                        <a title="Cập nhật thông tin văn phòng" href="{{route('editChiNhanh',['id'=>$cn->cn_id])}}"
                           class="btn btn-success">Sửa</a>
                        @if($cn->cn_id == Sentinel::getUser()->id)
                        @else
							@if($cn->status==null)
                            <a title="Ẩn văn phòng" href="#" data-toggle="modal"
                               data-target="#confirm-delbranch-{{$cn->cn_id}}" class="btn btn-danger">Xóa</a>
							   @else
							   <a title="Hiện văn phòng" href="#" data-toggle="modal"
                               data-target="#confirm-restorebranch-{{$cn->cn_id}}" class="btn btn-primary">Khôi phục</a>
                        @endif
						@endif
                    </div>
                </div>
                <div class="modal fade" id="confirm-delbranch-{{$cn->cn_id}}" role="dialog"
                     aria-labelledby="modalLabeldanger">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-danger">
                                <h4 class="modal-title" id="modalLabeldanger">Chú ý!</h4>
                            </div>
                            <div class="modal-body">
                                <p>Bạn có thực sự muốn xóa văn phòng "{{$cn->cn_ten}}"?</p>
                            </div>
                            <div class="modal-footer">
                                <form action="{{route('destroyChiNhanh',['id' => $cn->cn_id])}}" method="get">
                                    <div class="form-inline">
                                        <button type="submit" class="btn btn-danger">Có, xóa!</button>
                                        <a href="#" data-dismiss="modal" class="btn btn-warning">Không</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
				
				<div class="modal fade" id="confirm-restorebranch-{{$cn->cn_id}}" role="dialog"
                     aria-labelledby="modalLabeldanger">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-danger">
                                <h4 class="modal-title" id="modalLabeldanger">Chú ý!</h4>
                            </div>
                            <div class="modal-body">
                                <p>Bạn có thực sự muốn hiện văn phòng "{{$cn->cn_ten}}"?</p>
                            </div>
                            <div class="modal-footer">
                                <form action="{{route('restoreChiNhanh',['id' => $cn->cn_id])}}" method="get">
                                    <div class="form-inline">
                                        <button type="submit" class="btn btn-danger">Có, khôi phục!</button>
                                        <a href="#" data-dismiss="modal" class="btn btn-warning">Không</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="col-sm-12">
            <div class="col-sm-6">
                {{$chinhanh->appends(request()->input())->links()}}
            </div>
            <div class="col-sm-6">
                <p class="pull-right" style="font-size: 16px;">Tổng số: <b><span style="color: red">{{$tong}}</span></b>
                </p>
            </div>
    </section>
@stop



