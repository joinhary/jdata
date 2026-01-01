@extends('admin/layouts/default')
@section('title')
    Quản lý mã đăng nhập @parent
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
                <form action="{{route("setLoginCode")}}" method="post">
                    @csrf
                    <table class="table table-bordered table-hover">
                        <tr>
                            <th class="qkth">
                                Mã đăng nhập
                            </th>
                            <th class="qkth">
                                <input name="login_code" value="{{$chinhanh->login_code}}" required>
                            </th>
                        </tr>

                    </table>
                    <button class="btn btn-success">Cài đặt</button>
                </form>


            </div>
        </div>
    </section>
@stop
@section('footer_scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key=&callback=initMap" async defer></script>
    <script src="{{asset('assets/js/map_handling.js')}}"></script>
@stop
