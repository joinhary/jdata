@extends('admin.layouts.default')
@section('header_styles')
    <link rel="stylesheet" href="{{ asset('assets/vendors/Buttons/css/buttons.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/css/pages/advbuttons.css') }}"/>

@endsection
@section('content')
    {{--
        <section class="content-header" style="margin-bottom: 0px">
            <h1>Nhật ký hoạt động</h1>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="livicon" data-name="home" data-size="14" data-loop="true"></i>
                        Trang chủ
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.manager.users.index') }}">
                        Danh sách người dùng
                    </a>
                </li>
                <li class="active">
                    Nhật ký hoạt động
                </li>
            </ol>
        </section>
    --}}
    <br>
    <div class="row">
        <div class="col-md-12 bctk-scrollable-list" style="overflow-x: hidden;">
            <div class="caption">
                <i class="livicon" data-name="users" data-size="16" data-loop="true" data-c="#fff"
                   data-hc="white"></i> Nhật ký hoạt động: <b>{{ $user->first_name }}</b>
            </div>
            <table class="table table-bordered table-hover" style="width: 100%!important;">
                <thead>
                <tr>
                    <th>
                        #
                    </th>
                    <th width="20%">
                        Tên
                    </th>
                    <th>
                        Mô tả
                    </th>
                    <th>
                        Thời gian
                    </th>
                </tr>
                </thead>
                <tbody>
                @if(count($activity)>0)
                    @foreach ($activity as $key => $val)
                        <tr>
                            <td> {{$num++}}</td>
                            <td>
                                {{ $val->log_name }}
                            </td>
                            <td>
                                {{ $val->description }}
                            </td>
                            <td>
                                {{ $val->created_at }}
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td>Not found diary</td>
                    </tr>
                @endif
                </tbody>
            </table>
            <div class="text-center">
                {{ $activity->links() }}
            </div>
        </div>
    </div>
@stop
@section('footer_scripts')
@stop
