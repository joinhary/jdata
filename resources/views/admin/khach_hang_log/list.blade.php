@extends('admin/layouts/default')
@section('title')
    Lịch sử khách hàng @parent
@stop
@php
    function before ($a, $inthat)
    {
        return substr($inthat, 0, strpos($inthat, $a));
    };  
    $first = true;
@endphp

{{-- Page content --}}
@section('content')
    <section class="content">
        <div class="row bctk-scrollable-list" style="overflow-x: hidden;height: calc(85vh - 100px);">
            <table class="table table-bordered table-hover">
                <thead>
                <tr class="bg-default">
                    <th>Người cập nhật cuối cùng</th>
                    <th>Ghi chú chỉnh sửa</th>
                    <th>Thời gian cập nhật cuối cùng</th>
                    <th>Dữ liệu cũ</th>
                </tr>
                </thead>
                <tbody style="text-align: center">
                @foreach($logs as $log)
                    <tr @if($first) style="color:green; text-align: center" @endif >
                        <td style="text-align: center">{{$log['creator_name'] ?? 'Chưa cập nhật'}}</td>
                        <td style="text-align: center">{{$log['note'] ?? 'Không có ghi chú'}}</td>
                        <td style="text-align: center">{{$log['created_at']}}</td>
                        <td style="text-align: center">
                            <a class="btn btn-primary" href="{{route('showKhachHang',$log['id'])}}">Xem</a>
                        </td>
                    </tr>
                    @php $first = false; @endphp
                @endforeach
                </tbody>
            </table>
        </div>
    </section>
@stop

