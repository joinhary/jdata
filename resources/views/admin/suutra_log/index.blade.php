@extends('admin/layouts/default')
@section('title')
    Lịch sử sưu tra @parent
@stop
@section('header_styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    {{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> --}}
    <link rel="stylesheet" href="{{ asset('assets/vendors/select2/css/select2.min.css') }}">

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
        {!! \App\Helpers\Form::open(['method'=>'GET','id'=>'form-submit']) !!}
        <div class="row">
            <div class="col-md-3 search">
                <span class="nqkspan">Tìm kiếm theo ngày</span>
                <input type="text" class="form-control" name="date"
                value="{{request()->input('date')}}"
                >
            </div>
            <div class="col-md-3 search">
                <span class="nqkspan">Tìm kiếm theo số công chứng</span>
                <input type="text" class="form-control" id="searchbox" name="so_hd"
                       placeholder="Tìm kiếm theo số công chứng..." value="{{request()->input('so_hd')}}" autofocus>
            </div>
            <div class="col-md-3 search">
                <span class="nqkspan">Tìm kiếm theo người chỉnh sửa</span>
                {!! \App\Helpers\Form::select('user_id',$creators,request()->input('user_id'),['class'=>'form-control select2','id'=>'user_id','name'=>'user_id','placeholder'=>'Chọn người chỉnh sửa']) !!}
            </div>
            <div class="col-md-3" style="margin-top: 23px">
                <button class="btn btn-success btn1" type="submit">
                    <i class="fa fa-search"></i>
                    Tìm kiếm
                </button>
                <a class="btn btn-danger" href="{{ route('suutralogIndex')}}">
                    <i class="fa fa-trash"></i> Xóa Tìm kiếm</a>
            </div>
        </div>
        {!! \App\Helpers\Form::close() !!}
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
                    <th>STT</th>
                    <th>Số công chứng</th>
                    <th>Người cập nhật</th>
										<th>Loại</th>

                    <th>Thời gian cập nhật</th>
                    <th>Dữ liệu cũ</th>
                </tr>
                </thead>
                <tbody>
                  
                @foreach($logs as $log)
              
                    <tr>
                        <td class="column-align">{{$loop->iteration}}</td>
                        <td class="column-align">{{$log->so_hd}}</td>
                        <td class="column-align">{{$log->execute_person ? $log->execute_person : \App\Models\NhanVienModel::find($log->user_id)->nv_hoten}}</td>
						<td class="column-align">{{$log->execute_content}}</td>
                        <td class="column-align">{{\Illuminate\Support\Carbon::parse($log->created_at)->format('d/m/Y H:i:')}}</td>
                        <td class="column-align qktd">
                            <a title="Chi tiết nhân viên" href="{{ route('suutralogShow',['id' =>$log->id]) }}"
                               class="btn btn-primary">
                                Xem
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="col-sm-12">
            <div class="col-sm-6">
                {{$logs->appends(request()->input())->links()}}
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
    {{-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}
    <script src="{{ asset('assets/vendors/select2/js/select2.js') }}"></script>
    <script>
           $('.select2').select2();
        $(function () {
            $('input[name="date"]').daterangepicker({
                opens: 'left',
                               
            }, function (start, end, label) {
                console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
            });
        });
    </script>
@stop

