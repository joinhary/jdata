@extends('admin/layouts/default')
@section('title')
    Quản lý tài sản @parent
@stop
@section('header_styles')
    <link rel="stylesheet" href="{{ asset('assets/vendors/Buttons/css/buttons.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/css/pages/advbuttons.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/pages/tables.css') }}"/>
    <link href="{{ asset('assets/vendors/datatables/css/dataTables.bootstrap.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/datatables/css/scroller.bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/pages/tables.css') }}"/>
    <style>
        td {
            text-align: left;
            padding: 5px !important;
            font-size: 13px;
        }

        .btn1 {
            font-weight: 500;
            background-color: white !important;
            color: #01bc8c !important;
            font-size: 14px !important;
        }

        .btn2 {
            font-weight: 500;
            background-color: white !important;
            color: #1a67a3 !important;
            font-size: 14px !important;
        }
    </style>
@stop
@section('content')
    <section class="content">
        <form action="{{route('indexTaiSan')}}">
            <div class="row">
                <div class="col-md-5 search">
                    <input type="text" class="form-control" id="searchbox" name="ts_nhan"
                           placeholder="Tìm kiếm theo nhãn tài sản ..." value="{{$search}}" autofocus>
                    <span class="fa fa-search fa-search-custom"></span>
                </div>
                <div class="col-md-7">
                    <button class="btn btn-success btn1" type="submit">
                        <i class="fa fa-search"></i>
                        Tìm kiếm
                    </button>
                    <a href="{{route('createTaiSan')}}?label={{$search}}"
                       class="btn btn-primary btn2">
                        <i class="fa fa-plus"></i>
                        Thêm mới
                    </a>
                </div>
            </div>
        </form>
        <div class="row">
            @if(request()->input('ts_nhan') == null)
                <a></a>
            @else
                <a>Có <span style="color : red; font-weight: bold;">{{ $count }}</span> kết quả được tìm thấy.</a>
            @endif
        </div>
        <div class="row bctk-scrollable-list" style="overflow-x: hidden;">
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>STT</th>
                    <th style="width: 35%">Nhãn tài sản</th>
                    <th style="width: 25%">Loại tài sản</th>
                    <th><i class="fa fa-cog"></i></th>
                </tr>
                </thead>
                <tbody>
                @foreach($taisan as $key=> $val)
                    <tr>
                        <td class="column-align">{{$val->stt}}</td>
                        @if($bichan[$key]!=null)
                            <td class="text-red column-align">{{$val->ts_nhan}}</td>
                        @else
                            <td class="column-align">{{$val->ts_nhan}}</td>
                        @endif
                        <td class="column-align text-center ">
                            {{ $val->k_nhan }}
                        </td>
                        <td align="center" class="column-align text-center">
                            <a style="font-size: 12px" href="{{ route('showShowTaiSan',$val->ts_id) }}"
                               class="btn btn-primary" title="xem chi tiết">
                                <span style="text-align: center!important;font-size: 12px;">
                                    Xem
                                </span>
                            </a>
                            @if( $val->id_vp == \App\Models\NhanVienModel::where('nv_id', '=', Sentinel::getUser()->id)->first()->nv_vanphong)
                                <a style="font-size: 12px" href="{{ route('showEditTaiSan',$val->ts_id) }}"
                                   class="btn btn-success" title="sửa tài sản">
                                <span style="text-align: center!important;font-size: 12px;">
                                    Sửa
                                </span>
                                </a>
                                <a style="font-size: 12px" href="{{ route('changeCreate',$val->ts_id) }}"
                                   class="btn btn-warning" title="đổi kiểu">
                                <span style="text-align: center!important;font-size: 12px;">
                                    Đổi kiểu
                                </span>
                                </a>
                                <a style="font-size: 12px" href="{{ route('destroysTaiSan',$val->ts_id) }}"
                                   class="btn btn-danger" title="xóa tài sản"
                                   onclick="return confirm('Bạn có chắc là xóa tài sản ?')">
                                <span style="text-align: center!important;font-size: 12px;">
                                    Xóa
                                </span>
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
                {{$taisan->appends(request()->input())->links()}}
            </div>
            <div class="col-sm-6">
                <p class="pull-right" style="font-size: 16px;">Tổng số:
                    <b><span style="color: red">{{$tong->count()}}</span></b>
                </p>
            </div>
        </div>
    </section>
@stop
@section('footer_scripts')
    <script>
        $(".toggle-password").click(function () {
            $(this).toggleClass("fa-eye fa-eye-slash");
            $(this).attr("toggle");
            var input = $('.password');
            if (input.attr("type") === "password") {
                $('.toggle-password').removeClass('text-muted');
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
                $('.toggle-password').addClass('text-muted');
            }
        });
    </script>
@stop

