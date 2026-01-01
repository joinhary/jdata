@extends('admin/layouts/default')

@section('title')
    DTN    @parent
@stop

@section('header_styles')
    <meta name="_token" content="{{ csrf_token() }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/pages/tables.css') }}"/>
    <link rel="stylesheet" type="text/css"
          href="{{ asset('assets/vendors/datatables/css/dataTables.bootstrap.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/fancybox/jquery.fancybox.css') }}"
          media="screen"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/fancybox/jquery.fancybox-thumbs.css') }}"/>
{{--    <style type="text/css">--}}
{{--        table, th, td{--}}
{{--            border:1px solid #868585;--}}
{{--        }--}}
{{--        table{--}}
{{--            border-collapse:collapse;--}}
{{--            width:100%;--}}
{{--            margin: 3px;--}}
{{--        }--}}
{{--        th, td{--}}
{{--            text-align:left;--}}
{{--            padding:10px;--}}
{{--            font-size: 13px;--}}
{{--        }--}}
{{--        table tr:nth-child(odd){--}}
{{--            background-color:#eee;--}}
{{--            font-size: 13px;--}}
{{--            margin: 3px;--}}
{{--            padding:40px;--}}
{{--            height: 50px;--}}
{{--        }--}}
{{--        table tr:nth-child(even){--}}
{{--            background-color:white;--}}
{{--            font-size: 13px;--}}
{{--            margin: 3px;--}}
{{--            padding:40px;--}}
{{--            height: 50px;--}}
{{--        }--}}
{{--        table th{--}}
{{--            background-color:#138165;--}}
{{--            font-size: 12px;--}}
{{--            color: rgb(255, 251, 251)--}}
{{--        }--}}
{{--        table tr:hover{--}}
{{--            background: #1bb38d;--}}
{{--            color:white;--}}
{{--        }--}}
{{--    </style>--}}

@stop

@section('content')


    <section class="content">

                <h5 class="panel-title">Lịch sử giao dịch  <b>{{$ten}}</b></h5>
                <span class="pull-right">
                            <i class="glyphicon glyphicon-chevron-up showhide clickable"></i>
                        </span>
                <div class="panel-body">
            <div class="p-1">
                <div class="row scrollable-list">

                    <table id="noi-bo-table" class="table table-bordered table-hover mb-0">
                        <thead>
                        <tr class="text-center " style="background-color:#eeeeee">
                            <th style="width: 80px;font-size: 12px">Ngày ký</th>
                            <th style="width: 80px;font-size: 12px">Số công chứng</th>
                            <th style="width: 100px;font-size: 12px">Tên hợp đồng</th>
                            <th style="width: 100px;font-size: 12px">Công chứng viên</th>
                            <th style="width: 100px;font-size: 12px">Chuyên viên</th>
                            <th style="width: 100px;font-size: 12px">Văn Phòng</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $val)
                                    <tr>

                                        <td style="font-size: 13px;text-align: center;">{{ $val->ngayky }}</td>
                                        <td style="font-size: 13px;text-align: center;">{{ $val->so_cc }}</td>
                                        <td class="p-44"
                                            style="font-size: 13px;text-align: center;">@if(strlen($val->vb_nhan) > 150)


                                                <div class="modal fade bd-example-modal-sm"
                                                     id="more-content-md2-{{$val->st_id}}" tabindex="-1" role="dialog"
                                                     aria-labelledby="mySmallModalLabel" aria-hidden="false">
                                                    <div class="modal-dialog modal-sm"
                                                         style="margin-top: 20%;margin-right:29%">

                                                        <div class="modal-content">
                                                            <div class="modal-header"
                                                                 style="background-color: #138165;">
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                        aria-hidden="true">×
                                                                </button>
                                                                <h4 class="modal-title"
                                                                    style=" text-align:center;color: white">
                                                                    Tên hợp đồng chi
                                                                    tiết</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p style="font-size: 13px;text-align: justify;color: black">
                                                                    {!!$val->vb_nhan !!}

                                                                </p>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                                <span class="content-disp"
                                                      id="more-content2-{{$val->st_id}}">{!! substr($val->ten_hd, 150) !!}</span>
                                                <span id="three-dot2-{{$val->st_id}}">...</span>
                                                <span id="{{$val->st_id}}" onclick="showinfo2('{{$val->st_id}}')">
                                                                            <i id="search-icon2-{{$val->st_id}}"
                                                                               class="fa fa-search-plus text-primary"></i>
                                                                        </span>
                                            @else
                                                <a href="{{route('xemNoiDung',$val->id)}}" target='_blank'>  {{$val->vb_nhan}}</a>


                                            @endif</td>
                                        <td style="font-size: 13px;text-align: center;">{{ $val->first_name }}</td>
                                        <td style="font-size: 13px;text-align: center;">{{ $val->nvnv_id }}</td>

                                        <td style="font-size: 13px;text-align: center;">{{ $val->cn_ten }}</td>


                                    </tr>
                        @endforeach
                        </tbody>
                    </table>


                </div>
                <div
                        class="col-sm-12">
                    <div class="col-sm-6">
                        {{$data->appends(request()->input())->links()}}
                    </div>
                    <div class="col-sm-6" style="padding-top: 30px">
                        <p class="pull-right">Tổng số: <b><span
                                        style="color: red">{{$count}}</span> </b></p>

                    </div>
                </div>
                <div class="col-md-12" style="padding: 0px;">
                    <div class="panel panel-default"
                         style="border: 1px solid blue;border-top-left-radius: 14px;border-top-right-radius: 14px;">


                    </div>
                </div>
            </div>
                </div>

    </section>
@stop
@section('footer_scripts')
    <script type="text/javascript" src="{{ asset('assets/vendors/fancybox/jquery.fancybox.pack.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/pages/gallery.js') }}"></script>
    <script>
        function getDetailKH(ele) {
            var kh_id = ele.id;
            var urlGet = '{{url('admin/khachhang/show')}}' + '/' + kh_id;
            $.ajax({
                url: urlGet,
                type: 'GET',
                success: function (res) {
                    $('#placeToFillLabelDS').text(res.data['nhan']);
                    $.each(res.data['khachhang'], function (k, v) {
                        var giatri = '';
                        if (v.tm_loai !== 'file') {
                            if (v.kh_giatri) {
                                giatri = v.kh_giatri;
                            }
                            $('#kh-table').append(
                                '<tr>' +
                                '<td class="fit-column-kh">' + v.tm_nhan +
                                '</td>' +
                                '<td class="text-left">' + giatri +
                                '</td>' +
                                '</tr>'
                            );
                        }
                    });
                    $('#detail-kh').modal('show');
                }
            })
        }
    </script>
@stop

