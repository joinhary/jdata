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
        <div class="col-md-6">
            {!! \App\Helpers\Form::model($kieuhopdong, ['route' => ['admin.kieuhopdongs.update', collect($kieuhopdong)->first() ], 'method' => 'patch']) !!}
            @include('admin.kieuhopdongs.fields')
            {!! \App\Helpers\Form::close() !!}
        </div>
    </section>
@stop
