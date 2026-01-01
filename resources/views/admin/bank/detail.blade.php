@extends('admin/layouts/default')
@section('title')
    Quản lý nhân viên @parent
@stop
@section('header_styles')
    <meta name="_token" content="{{ csrf_token() }}">
    <link href="{{ asset('assets/css/pages/toastr.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/vendors/toastr/css/toastr.css') }}" rel="stylesheet" type="text/css"/>
    <style>
        label {
            font-size: 13px !important;
        }

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
{{-- Page content --}}
@section('content')
    <section class="content">
        <div class="row bctk-scrollable-list" style="overflow-x: hidden;">
            <div class="col-md-6 col-xs-12">
                <table class="table table-bordered table-hover">
                    <tr>
                        <th class="qkth">
                            Tên:
                        </th>
                        <th class="qkth">
                            {{$bank->name}}
                        </th>
                    </tr>
                    <tr>
                        <th class="qkth">
                            Mã số:
                        </th>
                        <th class="qkth">
                            {{$bank->order_number}}
                        </th>
                    </tr>
                </table>
                <div class="col-xs-12" align="center"><br>
                    <a href="{{ route('indexBank') }}" class="btn btn-secondary qkbtn">Hủy</a>
                    @if($bank->nv_id == Sentinel::getUser()->id)
                    {{-- @else
                        <a href="{{ route('editbank',$bank->id) }}" type="submit"
                           class="btn btn-success qkbtn">
                            Sửa
                        </a> --}}
                    @endif
                </div>
            </div>
            {{-- <div class="col-md-6 col-xs-12">
                <div class="img-file" style="text-align: center">
                    @if($pic)
                        @if((substr(Sentinel::getUser()->pic, 0,5)) == 'https')
                            <img src="{{ $pic }}" alt="img" class="img-responsive"/>
                        @else
                            <img src="{!!asset('assets/images/authors').'/' .$pic!!}" alt="img" class="img-responsive"
                                 width="244px" height="246px"/>
                        @endif
                    @elseif($bank->gender === "male")
                        <img src="{{ asset('assets/images/authors/avatar3.png') }}" alt="..." class="img-responsive"/>
                    @elseif($bank->gender === "female")
                        <img src="{{ asset('assets/images/authors/avatar5.png') }}" alt="..." class="img-responsive"/>
                    @else
                        <img src="{{ asset('assets/images/authors/no_avatar.jpg') }}" alt="..." class="img-responsive"/>
                    @endif
                </div>
            </div> --}}
        </div>
    </section>
@stop
@section('footer_scripts')
@stop
