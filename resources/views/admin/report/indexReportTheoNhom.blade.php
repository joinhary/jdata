@extends('admin/layouts/default')
@section('title')
    Thống kê   @parent
@stop
@section('header_styles')
    <style>
        .content-disp {
            display: none;
        }

        .nqkradio {
            width: 17px;
            height: 17px;
            margin: 0;
        }

        .p-33 {
            width: 20%;
        }

        .p-22 {
            width: 20%
        }

        .p-44 {
            width: 10%
        }

        .qkmodel {
            background-color: #1a67a3 !important;
        }

        .qkdo {
            text-align: justify;
            font-size: 14px;
            color: red;
        }

        .qkxanh {
            text-align: justify;
            font-size: 14px;
            color: blue;
        }

        .qktrang {
            text-align: justify;
            font-size: 14px;
            color: white;
        }

        .qktd {
            font-size: 14px !important;
        }

    </style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>

@stop
@section('content')
    @php
        $role = Sentinel::check()->user_roles()->first()->slug;
        $id_vp=\App\Models\NhanVienModel::find(Sentinel::check()->id)->nv_vanphong;
    @endphp
    <div class="col-md-12">
        <div class="col-md-11">
            <div class="btn-group dropright">
                <button type="button" style="font-weight: bold" class="btn btn-outline-primary dropdown-toggle"
                        id="show-menu">
                    Thống kê hồ sơ >>>
                </button>
                <div class="dropdown-menu" id="menu" style="min-width: 34rem;z-index:1">
                    <form action="#" method="get" id="formBaoCao">
                        <div class="col-md-12 mb-1 mt-1">
                            <input type="checkbox" name="radio"
                                   @if(request()->input('office') == null
                                    && request()->input('status') == null
                                    && request()->input('contract') == null
                                    && request()->input('notary') == null
                                    && request()->input('type') == null
                                    && request()->input('dateFrom') == null
                                    && request()->input('dateTo') == null)
                                   checked
                                   @endif class="nqkradio" value="1" id="radio1" onclick="TatCa()"/>
                            <b>Tất Cả</b>
                        </div>
                        @if(Sentinel::inRole('admin'))
                            <div class="col-md-2">
                                <b>Văn phòng</b>
                            </div>
                            <div class="col-md-10 mb-1">
                                {!! \App\Helpers\Form::select('office',$office,request()->input('office'),['class'=>'form-control select2','id'=>'nvnv']) !!}
                            </div>
                        @endif
                        <div class="col-md-2" style="padding-right: 0px">
                            <b>Chặn/Giải tỏa</b>
                        </div>
                        <div class="col-md-10 mb-1">
                            {!! \App\Helpers\Form::select('status',$prevent,request()->input('status'),['class'=>'form-control select2','onchange'=>'changedieukien(this)','id'=>'status']) !!}
                        </div>
                        <div class="col-md-2">
                            <b>Tên HĐ</b>
                        </div>
                        <div class="col-md-10 mb-1">
                            {!! \App\Helpers\Form::select('contract[]',$contract,request()->input('contract'),['class'=>'form-control js-example-basic-multiple','multiple','id'=>'contract','style'=>'width: 50%']) !!}
                        </div>
                        <div class="col-md-2">
                            <b>Theo CCV</b>
                        </div>
                        <div class="col-md-10 mb-1">
                            {!! \App\Helpers\Form::select('notary',$notary,request()->input('notary'),['class'=>'form-control','id'=>'notary']) !!}
                        </div>
                        <div class="col-md-2">
                            <b>Ngân hàng</b>
                        </div>
                        <div class="col-md-10 mb-1">
                            {{-- {!! \App\Helpers\Form::select('bank[]',$banks,request()->input('bank'),['class'=>'form-control js-example-basic-multiple','multiple','id'=>'bank','style'=>'width: 50%']) !!}--}}
                            <select name="bank[]" class="form-control js-example-basic-multiple" id="bank" style="width: 50%" multiple>
                                @foreach($banks as $val)
                                    <option value="{{ $val->id }}">{{ $val->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2" style="padding-right: 0px;">
                            <b>Theo Loại</b>
                        </div>
                        <div class="col-md-10 mb-1">
                            {!! \App\Helpers\Form::select('type',$type,request()->input('type'),['class'=>'form-control','id'=>'type']) !!}
                        </div>
                        <div class="col-md-2">
                            <b>Thời gian</b>
                        </div>
                        <div class="col-md-5 mb-1">
                            <input data-date-format="dd-mm-yyyy" d class="form-control" value="{{ request()->input('dateFrom') }}" type="date"
                                   name="dateFrom" id="dateFrom">
                        </div>
                        <div class="col-md-5 mb-1">
                            <input class="form-control" data-date-format="dd-mm-yyyy" value="{{ request()->input('dateTo') }}" type="date"
                                   name="dateTo" id="dateTo">
                        </div>
                        <div class="col-md-2 mb-1"></div>
                        <div class="col-md-5 mb-1">
                            <a class="btn btn-sm btn-info" style="width:100%" id="xem" onclick="Xem()">Xem</a>
                        </div>
                        <div class="col-md-5 mb-1">
                            <button type="button" class="btn btn-sm btn-success" onclick="ExportReport()" style="width:100%" id="export"
                            >Export
                            </button>
                        </div>
                        <div class="col-md-2 mb-1"></div>
                        <div class="col-md-5 mb-1">
                            <button type="button" style="width:100%" class="btn btn-sm btn-success" onclick="ExportDraw()" id="export-draw">
                                Download Data
                            </button>
                        </div>
                        <div class="col-md-5 mb-1">
                            <a id="close-menu" class="btn btn-sm btn-danger" style="width:100%">Đóng</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-12">

        </div>
       
        <div class="row bctk-scrollable-list" style="overflow-x: hidden;">
            <table class="table-bordered  ">
                       <thead>
    <tr class="text-center " style="background-color:#eeeeee">
        <th >STT</th>
        <th >Nhóm hợp đồng</th>
        <th >Số lượng</th>
      

    </tr>
    </thead>
    <tbody>
    @foreach($count as $key=> $val)
        <tr>
            <td style="vertical-align: text-top!important;">
                {{$key}}
            </td>
            <td class="text-left"><strong>{{$nhom[$key]}}</strong></td>
            <td class="text-left"><strong>{{$val}}</strong></td>
        </tr>
  

    @endforeach
    <tr><td colspan="2"><strong>Tổng cộng</strong></td>

        <td><strong>{{$total}}</strong></td></tr>
    </tbody>
                    </table>
     
    </div>
@endsection
@section('footer_scripts')
    {{-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}
    <script src="{{ asset('assets/vendors/select2/js/select2.js') }}"></script>

    <script>
        $('.js-example-basic-multiple').select2();
        if ($("#radio1").is(':checked')) {
            document.getElementById("notary").disabled = true;
            document.getElementById("dateFrom").disabled = true;
            document.getElementById("dateTo").disabled = true;
            document.getElementById("contract").disabled = true;
            document.getElementById("status").disabled = true;
            if (document.getElementById("nvnv")) {
                document.getElementById("nvnv").disabled = true;

            }
        } else {
            document.getElementById("notary").disabled = false;
            document.getElementById("dateFrom").disabled = false;
            document.getElementById("dateTo").disabled = false;
            document.getElementById("contract").disabled = false;
            document.getElementById("status").disabled = false;
            if (document.getElementById("nvnv")) {
                document.getElementById("nvnv").disabled = false;
            }

        }

        function TatCa() {
            if ($("#radio1").is(':checked')) {
                document.getElementById("notary").disabled = true;
                document.getElementById("dateFrom").disabled = true;
                document.getElementById("dateTo").disabled = true;
                document.getElementById("contract").disabled = true;
                document.getElementById("status").disabled = true;
                if (document.getElementById("nvnv")) {
                    document.getElementById("nvnv").disabled = true;
                }
            } else {
                document.getElementById("notary").disabled = false;
                document.getElementById("dateFrom").disabled = false;
                document.getElementById("dateTo").disabled = false;
                document.getElementById("contract").disabled = false;
                document.getElementById("status").disabled = false;
                if (document.getElementById("nvnv")) {
                    document.getElementById("nvnv").disabled = false;
                }
            }
        }

        $("#show-menu").click(function () {
            $("#menu").addClass("show")
        })
        $("#close-menu").click(function () {
            $("#menu").removeClass("show")

        })
        $("#show-menu-export").click(function () {
            $("#menu-export").addClass("show")
        })
        $("#close-menu-export").click(function () {
            $("#menu-export").removeClass("show")

        })

        function showinfo(id) {
            $('#more-content-md-' + id).modal();

        }

        function showinfo2(id) {
            $('#more-content-md2-' + id).modal();

        }

        function showinfo3(id) {
            $('#more-content-md3-' + id).modal();

        }

        function Xem() {
            var xem = '{{ route('indexReport') }}';
            $('#formBaoCao').attr('action', xem);
            $('#formBaoCao').submit();
        }

        function ExportDraw() {
            var xem = '{{ route('exportReportView') }}';
            $("#formBaoCao").attr("action", xem);
            $("#formBaoCao").submit();
        }

        function ExportReport() {
            var xem = '{{ route('exportReport') }}';
            $("#formBaoCao").attr("action", xem);
            $("#formBaoCao").submit();
        }

    </script>
@stop

