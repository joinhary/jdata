@extends('admin/layouts/default')

{{-- Page title --}}
@section('title')
    Thông báo
    @parent
@stop


{{-- page level styles --}}
@section('header_styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/datatables/css/dataTables.bootstrap.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/datatables/css/scroller.bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/pages/tables.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/Buttons/css/buttons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/pages/advbuttons.css') }}" />
    <link href="{{ asset('/assets/vendors/summernote/summernote.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">
    <style type="text/css">
        .btn1 {
            font-weight: 500;
            background-color: white !important;
            color: #01bc8c !important;
            font-size: 14px !important;
        }

        .qktd {
            text-align: center;
        }

        .btn2 {
            font-weight: 500;
            background-color: white !important;
            color: #1a67a3 !important;
            font-size: 14px !important;
        }

        .bg-danger {
            background: #e74040 !important;
        }
    </style>
    <style type="text/css">
        table.dataTable tfoot th {
            text-align: right;
        }

        #hopdong-table {
            border: 1px solid #868585;
        }

        #hopdong-table {
            border-collapse: collapse;
            width: 100%;
            margin: 3px;
        }

        textarea {
            resize: vertical;
        }

        th,
        td {
            text-align: center;
            font-size: 13px;
        }

        input,
        select {
            border-radius: 0 !important;
        }

        .select2 {
            width: 100% !important;
        }

        #hopdong-table tr:nth-child(odd) {
            background-color: #eee;
            font-size: 13px;
            height: 50px;
        }

        #hopdong-table tr:nth-child(even) {
            background-color: white;
            font-size: 13px;
            height: 50px;
        }

        #hopdong-table th {
            background-color: #138165;
            font-size: 12px;
            color: rgb(255, 251, 251);
        }

        #service-fees th {
            background-color: #138165;
            font-size: 12px;
            color: rgb(255, 251, 251);
        }

        #hopdong-table tr:hover {
            background: #1bb38d;
            color: white;
        }

        mark {
            padding: 0;
            background-color: #ffe456 !important;
        }

        #hopdong-table td {
            text-align: center;
            vertical-align: middle;
        }

        #hopdong-table th {
            text-align: center;
            vertical-align: middle;
        }
    </style>
@stop

{{-- Page content --}}
@section('content')
    <section class="content">
        <div class="row">
            <h3></h3>
        </div>
        {{-- <div class="row bctk-scrollable-list" style="overflow-x: hidden;height: calc(100vh - 100px);">
       
                        {!! $tbc->noi_dung !!}
                

        </div> --}}
        <div class="container">
            <div class="panel panel-primary">
                <div class="panel-heading">{{ $tbc->tieu_de }}</div>
                <div class="panel-body">
                    <b>Nội dung: </b> {!! $tbc->noi_dung !!} <br>
                    <b>Đương sự: </b> {!! $tbc->duong_su !!} <br>
                    <b>Tài sản: </b> {{ $tbc->texte }} <br>

                </div>
                <div class="panel-footer">
                    @php
                        $ymd = DateTime::createFromFormat('d-m-Y', '06-11-2022')->format('Y-m-d');
                    @endphp
                    @if ($tbc->created_at <= $ymd)
                        Đính kèm:
                        <a href="{{ url('storage/upload_thongbao/' . $tbc->file) }}" style="color:#1a67a3" target="blank">
                            <i class="fa fa-paperclip" aria-hidden="true"></i> {{ $tbc->file }}</a>
                    @else
                    @if($tbc->file)
                    @foreach (json_decode($tbc->file, true) as $key => $img)
                        <a href="{{ url('storage/upload_thongbao/' . $img) }}" style="color:#1a67a3" target="blank"><i
                                class="fa fa-paperclip" aria-hidden="true"></i> {{ $img }}</a>
                        <br>
                    @endforeach
                    @endif
                    @endif
                </div>
            </div>
        </div>
    @stop
    @section('footer_scripts')
        <script src="//cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
        <script>
            CKEDITOR.replace('noi_dung');
        </script>
    @stop
