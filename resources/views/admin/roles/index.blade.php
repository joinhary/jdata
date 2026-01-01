@extends('admin.layouts.default')
@section('header_styles')
    <link rel="stylesheet" href="{{ asset('assets/vendors/Buttons/css/buttons.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/css/pages/advbuttons.css') }}"/>
    <style>
        .spannew {
            float: right;
            margin-bottom: 2px;
        }

        .btnnew {
            padding: 0 16px;
        }
    </style>
@endsection
@section('content')
    <section class="content-header" style="margin-bottom: 0px">
        <h1>Phân Quyền</h1>
        <ol class="breadcrumb">
            <li>
                <a href="{{ route('admin.dashboard') }}">
                    <i class="livicon" data-name="home" data-size="14" data-loop="true"></i>
                    Trang chủ
                </a>
            </li>
            <li class="active">
                Phân quyền
            </li>
        </ol>
    </section>
    <br>
    <div class="row">
        <div class="col-lg-12">
            <span class="spannew pull-right">
                <a href="{{ route('admin.roles.create') }}"
                   class="button button-glow button-rounded button-primary button-3d btnnew">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i> Thêm mới
                </a>
            </span>
            <div class="portlet box primary">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="livicon" data-name="users" data-size="16" data-loop="true" data-c="#fff"
                           data-hc="white"></i> Danh sách phân quyền
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>
                                    #
                                </th>
                                <th width="20%">
                                    Tên
                                </th>
                                <th width="20%">
                                    Tên hiển thị
                                </th>
                                <th>
                                    Phân quyền
                                </th>
                                <th width="12%"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($role)>0)
                                @foreach ($role as $key => $val)
                                    <tr>
                                        <td style="vertical-align: middle;"> {{$num++}}</td>
                                        <td style="vertical-align: middle;">
                                            {{ $val->slug }}
                                        </td>
                                        <td style="vertical-align: middle;">
                                            {{ $val->name }}
                                        </td>
                                        <td style="vertical-align: middle;">
                                                @if(count(json_decode($val->permissions)))
                                                    @foreach(json_decode($val->permissions) as $key => $item)
                                                        @if($item)
                                                            {{ $key }},
                                                        @endif
                                                    @endforeach
                                                @endif
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <a class="btn btn-success"
                                               href="{{ route('admin.roles.edit',$val->id) }}">Sửa</a>

                                            <a class="btn btn-danger"
                                               href="{{ route('admin.roles.delete',$val->id) }}">Xóa</a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td>Not found role</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                        <div class="text-center">
                            {{ $role->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('footer_scripts')

@stop