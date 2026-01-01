@extends('admin/layouts/default')
@section('title')
    Lịch sử đăng nhập @parent
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
        <form action="#">
            <div class="row">
                <div class="col-md-3 search">
                    <span class="nqkspan">Tìm kiếm theo ngày</span>
                    <input type="text" class="form-control" name="date" id="date" value="{{$date}}">
                </div>
                <div class="col-md-3" style="margin-top: 23px">
                    <button class="btn btn-success btn1" type="submit">
                        <i class="fa fa-search"></i>
                        Tìm kiếm
                    </button>
                    <a class="btn btn-danger" href="{{route('historyLogin')}}">
                        <i class="fa fa-trash"></i> Xóa Tìm kiếm</a>
                </div>
            </div>
        </form>
        <div class="row">
            @if(request()->input('date') == null)
                <a></a>
            @else
                <a>Có <span style="color : red; font-weight: bold;">{{$count}}</span> kết quả được tìm thấy.</a>
            @endif
        </div>
        <div class="row scrollable-list" style="overflow-x: hidden;height: calc(80vh - 100px)">
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>STT</th>
                    <th>Tên đăng nhập</th>
					<th>Văn phòng</th>
                    <th>Thời gian đăng nhập cuối cùng</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $key => $user)
                    <tr>
					
                        <td class="column-align">{{$loop->iteration}}</td>
                        <td class="column-align">{{$user[0]['log_name']}}</td>
						<td>{{\App\Models\ChiNhanhModel::find(\App\Models\NhanVienModel::find($user[0]['subject_id'])->nv_vanphong)->cn_ten}}</td>
                        <td class="column-align">{{ \Illuminate\Support\Carbon::parse($user[0]['created_at'])->format('d/m/Y H:i:s')}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>    
        
    </section>
@stop
@section('footer_scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $('#date').daterangepicker({
            singleDatePicker: true,
            locale: {
                format: 'DD/MM/YYYY'
            }
        });

    </script>
@stop

