@extends('admin/layouts/default')
@section('title')
    Kiểu hợp đồng @parent
@stop
@section('header_styles')
    <style type="text/css">
        .btn1 {
            font-weight: 500;
            background-color: white !important;
            color: #01bc8c !important;
            font-size: 14px !important;
        }

        .btn2 {
            font-weight: 500;
            background-color: white !important;
            color: #1a67a3 !important;
            font-size: 14px !important;
        }
    </style>
    <style>
        label {
            font-size: 13px !important;
        }

        .form-control {
            font-size: 13px !important;
        }

        .qk {
            background: #f2f1f5;
        }

        .qkbtn {
            font-weight: 500;
            background-color: white !important;
            color: #1a67a3 !important;
            font-size: 14px !important;
        }

        .qkbtn2 {
            font-weight: 500;
            background-color: white !important;
            color: #01bc8c !important;
            font-size: 14px !important;
        }

        .bg-danger {
            background: #e74040 !important;
        }
    </style>
@stop
@section('content')
    <section class="content paddingleft_right15">
        <form>
            <div class="row">
                @include('flash::message')
                <div class="col-md-4 search">
                    <input type="text" value="{{request()->input('kieu_hd')}}" class="form-control" id="searchbox"
                           name="kieu_hd" placeholder="Nhập kiểu hợp đồng cần tìm..." autofocus>
                    <span class="fa fa-search fa-search-custom"></span>
                </div>
                <div class="col-md-8">
                    <button class="btn btn-success btn1" type="submit">
                        <i class="fa fa-search"></i>Tìm kiếm
                    </button>
                    <a href="{{ route('admin.kieuhopdongs.create') }}" class="btn btn-primary btn2">
                        <i class="fa fa-plus"></i> Thêm mới
                    </a>
                    <a href="{{ route('admin.kieuhopdongs.syncKind') }}" class="btn btn-primary btn2">
                        <i class="fa fa-refresh"></i> Đồng bộ dữ liệu
                    </a>
                </div>
            </div>
        </form>
        @include('admin.kieuhopdongs.table')
    </section>
@stop
