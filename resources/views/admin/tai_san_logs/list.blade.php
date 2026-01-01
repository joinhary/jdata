@extends('admin/layouts/default')

{{-- Page title --}}
@section('title')
    Lịch sử tài sản
    @parent
@stop

{{-- page level styles --}}
@section('header_styles')
    <link rel="stylesheet" type="text/css"
          href="{{ asset('assets/vendors/datatables/css/dataTables.bootstrap.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/datatables/css/scroller.bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/pages/tables.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/vendors/Buttons/css/buttons.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/css/pages/advbuttons.css') }}"/>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/css/select2.min.css" rel="stylesheet"/>

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

        th, td {
            text-align: center;
            font-size: 13px;
        }

        input, select {
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


@php
    function before ($a, $inthat)
    {
        return substr($inthat, 0, strpos($inthat, $a));
    };  
    $first = true;
@endphp

{{-- Page content --}}
@section('content')
    <section class="content">
        <div class="col-md-12">
            <div class="row bctk-scrollable-list" style="overflow-x: hidden;height: calc(85vh - 100px);">
                <table class="table table-bordered table-hover">
                    <thead>
                    <tr class="bg-default">
                        <th>Người cập nhật cuối cùng</th>
                        <th>Ghi chú chỉnh sửa</th>
                        <th>Thời gian cập nhật cuối cùng</th>
                        <th>Dữ liệu cũ</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($logs as $log)
                        <tr @if($first) style="color:green" ; @endif>
                            <td style="text-align: center">{{$log['creator_name'] ?? 'Chưa cập nhật'}}</td>
                            <td style="text-align: center">{{$log['note'] ?? 'Không có ghi chú'}}</td>
                            <td style="text-align: center">{{$log['created_at']}}</td>
                            <td style="text-align: center">
                                <a class="btn btn-primary" href="{{route('showShowTaiSan',$log['id'])}}">Xem</a>
                            </td>
                        </tr>
                        @php $first = false; @endphp
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@stop
@section('footer_scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/js/select2.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/datatables/js/dataTables.bootstrap.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/datatables/js/dataTables.colReorder.js') }}"></script>
    <script type="text/javascript"
            src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script>
        $('.select2-single').select2();
        $('.select2-no-search').select2({
            minimumResultsForSearch: Infinity
        });
        $(document).ready(function () {

        });


        $(document).ready(function () {
            $('#hopdong-table').DataTable({
                    'searching': false,
                    "order": [[2, "desc"]]
                }
            );
        });


    </script>

@stop

