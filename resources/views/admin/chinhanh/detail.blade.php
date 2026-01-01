@extends('admin/layouts/default')
@section('title')
   Quản lý văn phòng @parent
@stop
@section('header_styles')
    <meta name="_token" content="{{ csrf_token() }}">
    <link href="{{ asset('assets/css/pages/toastr.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/vendors/toastr/css/toastr.css') }}" rel="stylesheet" type="text/css"/>
    <style>
        .qkth {
            text-align: left !important;
            background: #f8fbfd !important;
            color: black;
            font-weight: normal;
            font-family: "Lato", "Lucida Grande", Helvetica, Arial, sans-serif;
        }
        .qkbtn {
            font-weight: bold;
            font-size: 14px !important;
        }
    </style>
@stop
@section('content')
    <section class="content">
        <div class="row bctk-scrollable-list" style="overflow-x: hidden;">
            <div class="col-md-6 col-xs-12">
                <table class="table table-bordered table-hover">
                    <tr>
                        <th class="qkth">
                            Mã văn phòng
                        </th>
                        <th class="qkth">
                            {{$chinhanh->code_cn}}
                        </th>
                    </tr>
                    <tr>
                        <th class="qkth">
                            Tên văn phòng
                        </th>
                        <th class="qkth">
                            {{$chinhanh->cn_ten}}
                        </th>
                    </tr>
                    <tr>
                        <th class="qkth">
                            Số điện thoại văn phòng
                        </th>
                        <th class="qkth">
                            {{$chinhanh->cn_sdt}}
                        </th>
                    </tr>
                    <tr>
                        <th class="qkth">
                            Địa chỉ
                        </th>
                        <th class="qkth">
                            Số {{$chinhanh->cn_diachi.', '.lcfirst($chinhanh->cn_tenap).', '.lcfirst($chinhanh->cn_tenphuong).', '.lcfirst($chinhanh->cn_tenquan).', '.lcfirst($chinhanh->cn_tentinh)}}
                        </th>
                    </tr>
                    <tr>
                        <th class="qkth">
                            Người đại diện
                        </th>
                        <th class="qkth">
                            {{$chinhanh->cn_ndd}}
                        </th>
                    </tr>
                </table>
                <input type="text" id="lat" name="lat" value="{{$chinhanh->lat}}" hidden>
                <input type="text" id="lng" name="lng" value="{{$chinhanh->lng}}" hidden>
                <div class="col-xs-12" align="center"><br>
                    <a href="javascript:history.back()" class="btn btn-secondary qkbtn">Hủy</a>
                    <a href="{{route('editChiNhanh', ['id'=>$chinhanh->cn_id])}}" type="submit" class="btn btn-success qkbtn">Sửa</a>

                </div>
            </div>
            <div class="col-md-6 col-xs-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <span>Bản đồ (tùy chỉnh lại vị trí nếu có sai lệch)</span>
                    </div>
                    <div class="panel-body">
                        <div id="map" style="width: 100%; height: 485px"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
@section('footer_scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key=&callback=initMap" async defer></script>
    <script src="{{asset('assets/js/map_handling.js')}}"></script>
@stop
