@extends('admin/layouts/default')
@section('title')
    Kiểu hợp đồng @parent
@stop
@section('header_styles')
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
@section('content')
    <section class="content">
        <div class="row bctk-scrollable-list" style="overflow-x: hidden;">
            <div class="col-md-6 col-xs-12">
                <table class="table table-bordered table-hover">
                    @include('admin.kieuhopdongs.show_fields')
                </table>
                <div class="col-xs-12" align="center"><br>
                    <a href="{{ route('admin.kieuhopdongs.index') }}" class="btn btn-secondary qkbtn">Hủy</a>
                    <a href="{{ route('admin.kieuhopdongs.edit', $kieuhopdong->id) }}" type="submit"
                       class="btn btn-success qkbtn">
                        Sửa
                    </a>
                </div>
            </div>
        </div>
    </section>
@stop
