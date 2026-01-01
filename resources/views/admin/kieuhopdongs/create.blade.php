@extends('admin/layouts/default')
@section('title')
    Kiểu hợp đồng @parent
@stop
@section('header_styles')
    <style>
        label {
            font-size: 14px !important;
        }

        .qksao {
            font-weight: bold;
        }

        .qkbtn {
            font-weight: bold;
            font-size: 14px !important;
        }

    </style>
@stop
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-md-6">
                {!! \App\Helpers\Form::open(['route' => 'admin.kieuhopdongs.store']) !!}
                @include('admin.kieuhopdongs.crefields')
                {!! \App\Helpers\Form::close() !!}
            </div>
        </div>
    </section>
@stop
