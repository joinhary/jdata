@extends('admin/layouts/default')
@section('title')
    Lịch sử đương sự @parent
@stop
@section('header_styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <style type="text/css">
        th, td {
            text-align: center;
            font-size: 14px !important;
        }

        .nqkspan {
            font-size: 14px !important;
            font-weight: bold;
            color: black;
        }

    </style>
@stop
@section('content')
    <section class="content">
        <form action="{{route('indexTaiSanLog')}}">
            <div class="row">
                <div class="col-md-3 search">
                    <span class="nqkspan">Tìm kiếm theo ngày</span>
                    <input type="text" class="form-control" name="date">
                </div>
                <div class="col-md-3 search">
                    <span class="nqkspan">Tìm kiếm theo tên đương sự</span>
                    <input type="text" class="form-control" id="searchbox" name="name"
                           placeholder="Tìm kiếm theo tên đương sự..." value="{{$search}}" autofocus>
                </div>
                <div class="col-md-3 search">
                    <span class="nqkspan">Tìm kiếm theo người chỉnh sửa</span>
                    {!! \App\Helpers\Form::select('nv_id',$creators,'',['class'=>'form-control select2']) !!}
                </div>
                <div class="col-md-3" style="margin-top: 23px">
                    <button class="btn btn-success btn1" type="submit">
                        <i class="fa fa-search"></i>
                        Tìm kiếm
                    </button>
                    <a class="btn btn-danger" href="{{ route('indexTaiSanLog')}}">
                        <i class="fa fa-trash"></i> Xóa Tìm kiếm</a>
                </div>
            </div>
        </form>
        <div class="row">
            @if(request()->input('date') == null
                && request()->input('nv_id') == null
                && request()->input('name') == null)
                <a></a>
            @else
                <a>Có <span style="color : red; font-weight: bold;">{{$count}}</span> kết quả được tìm thấy.</a>
            @endif
        </div>
        <div class="row bctk-scrollable-list" style="overflow-x: hidden;height: calc(85vh - 100px);">
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>Phiếu thay đổi</th>
                    <th>Người cập nhật cuối cùng</th>
                    <th>Thời gian cập nhật cuối cùng</th>
                    <th>Dữ liệu cũ</th>
                </tr>
                </thead>
                <tbody>
                @foreach($logs as $log)
                    <tr>
                        <td class="column-align">{{$log['customer_name']}}</td>
                        <td class="column-align">{{$log['creator_name'] ?? 'Chưa cập nhật'}}</td>
                        <td class="column-align">{{$log['created_at']}}</td>
                        <td class="column-align">
                            <a class="btn btn-primary" href="{{route('listTaiSanLog',$log['ts_id'])}}">Xem</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="col-sm-12">
            <div class="col-sm-6">
                {{$tai_san->appends(request()->input())->links()}}
            </div>
            <div class="col-sm-6">
                <p class="pull-right" style="font-size: 16px;">Tổng số:
                    <b><span style="color: red">{{ $tong }}</span></b>
                </p>
            </div>
        </div>
    </section>
@stop
@section('footer_scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $(function () {
            $('input[name="date"]').daterangepicker({
                opens: 'left'
            }, function (start, end, label) {
                console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
            });
        });
    </script>
@stop

