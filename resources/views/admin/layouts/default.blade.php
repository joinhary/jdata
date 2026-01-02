<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('V3/images/logo.png') }}" type="image/ico"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
        @section('title')
            | Jdata
        @show
    </title>
    <link href="{{ asset('assets/css/custom2.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('V3/vendors/bootstrap/dist/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('V3/vendors/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet">
    <link href="{{ asset('assets/css/toastr.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('V3/build/css/custom.css')}}" rel="stylesheet">
    <link href="{{ asset('assets/css/old.css') }}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{ asset('assets/vendors/Buttons/css/buttons.css') }}"/>
    <style type="text/css">
        ::-webkit-scrollbar {
            height: 4px; /* height of horizontal scrollbar ← You're missing this */
            width: 4px; /* width of vertical scrollbar */
            border: 1px solid #d5d5d5;
        }

        /* Track */
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        /* Handle */
        ::-webkit-scrollbar-thumb {
            background: #888;
        }

        /* Handle on hover */
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        ::-webkit-scrollbar:horizontal {
            width: 2px;
        }

        body {
            margin: 0;
        }

        .main_container {
            height: auto;
        }

        .nav-item {
            vertical-align: middle;
        }

        .vp-profile img {
            width: 29px;
            height: 29px;
            border: none !important;
            margin-right: 10px;
            visibility: hidden;
        }

        .qknav {
            color: white !important;
            font-weight: 500;
        }

        .qkn {
            color: white;
        }

        .nav.child_menu li:hover, .nav.child_menu li.active {
            background-color: #1a67a3;
        }

        .nav-sm .main_container .top_nav {
            margin-left: 0px;
        }

        .main_container .top_nav {
            margin-left: 0px;
        }

        .nav-md .container.body .col-md-3.left_col {
            height: calc(100% - 54px) !important;
            min-height: 0px !important;
        }

        .qkimg {
            height: var(--header-height);
            position: absolute;
            left: calc(50% - 75px / 2);
        }

        .qkimg2 {
            background-color: #FFFFFF;
            width: 90px;
            height: 50px;
            margin-top: 2px;
        }

        .qkpnew {
            margin-bottom: 0px;
            margin-top: 0px
        }

        .nav.side-menu > li > a {
            margin: 0px;
        }

        .menu_section.active {
            overflow-x: hidden;
            height: calc(100vh - 100px);
        }
    </style>
    @yield('header_styles')
</head>
<body class="nav-md" id="idbody">
<div class="container body" style="width:100% !important;">
    <div class="" style="width:100% !important;">
        <!-- top navigation -->
        <div class="top_nav">
            <div class="nav_menu">
                <div class="nav toggle qkn">
                    <a id="menu_toggle"><i class="fa fa-bars"></i></a>
                </div>
                <nav class="nav navbar-nav">
                    <div class="qkimg">
                        <a href="{{ route('admin') }}" class="mt-2">
                            <img  src="{{ asset('images/logo-jdata-03.png') }}" width="100px" height="30px" style="margin-top:10px;">
                        </a>
                    </div>
                    <ul class="navbar-right">
                        <li class="nav-item dropdown open" style="padding-left: 15px;">
                            <a href="#" class="user-profile dropdown-toggle" aria-haspopup="true"
                               id="navbarDropdown" data-toggle="dropdown" aria-expanded="false">
                             @php
    $user = Sentinel::getUser();
@endphp

@if($user && $user->pic)
    <img src="{{ asset('assets/images/authors/' . $user->pic) }}" alt="">
@else
    <img src="{{ asset('/images/new-user.png') }}" alt="profile pic">
@endif

                                <span>
                                {{substr(Sentinel::getUser()->first_name, 0,30) . '...'}}

                                        {{ Sentinel::getUser()->last_name }}
                                    </span>
                            </a>
                            <div class="dropdown-menu dropdown-usermenu pull-right" aria-labelledby="navbarDropdown">
                               <a class="dropdown-item"
   href="{{ route('users.show', Sentinel::getUser()->id) }}">
   Thông tin cá nhân
</a>
                                <a class="dropdown-item" href="{{ URL::route('lockscreen',Sentinel::getUser()->id) }}">
                                    <span>Màn hình chờ</span>
                                </a>
                                <a class="dropdown-item" href="{{ URL::to('admin/logout') }}"><i
                                            class="fa fa-sign-out pull-right"></i> Đăng xuất</a>
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <span href="#" class="vp-profile qknav" style="font-size: 20px">
                                {{mb_strtoupper(\App\Models\ChiNhanhModel::find(\App\Models\NhanVienModel::find(Sentinel::getUser()->id)->nv_vanphong)->cn_ten, "UTF-8")}}
                            </span>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <div class="nav"></div>
        @include('admin.layouts._left_menu')
        <div class="right_col" style="">
            @yield('content')
        </div>
    </div>
</div>
<script src="{{ asset('assets/packages/popup/notiflix-3.2.5.min.js') }}"></script>

<script src="{{ asset('assets/packages/popup/notiflix-aio-3.2.5.min.js') }}"></script>

<script src="{{ asset('assets/packages/popup/notiflix-notify-aio-3.2.5.min.js') }}"></script>

<script src="{{ asset('assets/packages/popup/notiflix-report-aio-3.2.5.min.js') }}"></script>

<script src="{{ asset('assets/packages/popup/notiflix-confirm-aio-3.2.5.min.js') }}"></script>

<script src="{{ asset('assets/packages/popup/notiflix-loading-aio-3.2.5.min.js') }}"></script>

<script src="{{ asset('assets/packages/popup/notiflix-block-aio-3.2.5.min.js') }}"></script>
<script src="{{asset('V3/vendors/jquery/dist/jquery.min.js')}}"></script>
@yield('footer_scripts')
<script src="{{asset('V3/vendors/bootstrap/dist/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{ asset('assets/js/toastr.min.js') }}"></script>
<script src="{{asset('V3/build/js/custom.js')}}"></script>

<script>
    function loadingLeftMenu(event) {
        Notiflix.Loading.hourglass('Đang tải dữ liệu...');
        };
    </script>

@include('admin.inc.messages')

</body>
</html>
