@extends('admin/layouts/default')
@section('title')
    Lịch sử đăng nhập @parent
@stop
@php
use App\Models\ChiNhanhModel;
@endphp   
@section('header_styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <style type="text/css">
        th, td {
            text-align: center;
            font-size: 14px !important;
        }

        td {
            word-break: break-word;
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
        <div class="row scrollable-list table-responsive" style="overflow-x: hidden;height: calc(80vh - 100px)"> 
            <table class="table table-bordered table-hover" style="table-layout: fixed">
                <thead>
                <tr>
                    <th style="width: 2%">STT</th>
                    <th style="width: 6%">Tên người dùng</th>
                    <th style="width: 6%">Văn phòng</th>
                    <th style="width: 10%">Url</th>
                    <th style="width: 10%">File In </th>
                    <th style="width: 4%">Ip</th>
                    <th style="width: 5%">Thời gian tìm kiếm</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $item)
                    <tr>
                        <td class="column-align">{{$loop->iteration}}</td>
                        <td class="column-align">{{$item->user->first_name}}</td>
                        <td class="column-align">
                            {{ChiNhanhModel::find($item->vp_id)->cn_ten}}

                        </td>

                        <td class="column-align">
                            <a href="{{$item->url}}" target="_blank">{{$item->url}}</a>
                        </td>
                        <td class="column-align" style="text-align: center"><a style="color:blue;"
                            href={{ url('lichsu_tracuu/'.$item->file) }} target="_blank">  
                                                       {{ $item->file }}
                        </a>
                         </td>
                        <td class="column-align">{{$item->client_ip}}</td>
                        <td class="column-align">{{ \Illuminate\Support\Carbon::parse($item->created_at)->format('d/m/Y H:i:s')}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
			 
        </div>
        <div class="col-sm-12">
            <div class="col-sm-6">
                {{$data->appends(request()->input())->links()}}
            </div>
                <div class="col-sm-6">
                <p class="pull-right" style="font-size: 16px;">Tổng số:
                    <b><span style="color: red">{{ $total }}</span></b>
                </p>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="col-sm-6">
            </div>
        
        </div>
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

