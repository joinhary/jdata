@extends('admin/layouts/default')
@section('title')
    Thống kê    @parent
@stop
@section('header_styles')
    <link href="{{ asset('assets/css/pages/tables.css') }}"/>
    <link href="{{ asset('assets/vendors/datatables/css/dataTables.bootstrap.css') }}"/>
    <link href="{{ asset('assets/vendors/datatables/css/scroller.bootstrap.css') }}">
    <link href="{{ asset('assets/vendors/fancybox/jquery.fancybox.css') }}"/>
    <link href="{{ asset('assets/vendors/fancybox/jquery.fancybox-thumbs.css') }}"/>
    <link href="{{ asset('assets/vendors/Buttons/css/buttons.css') }}"/>
    <link href="{{ asset('assets/css/pages/advbuttons.css') }}"/>
    <style>
        .sotuphap html {
            display: none;
        }

        .content-disp {
            display: none;
        }


        .nqkradio {
            width: 17px;
            height: 17px;
            margin: 0;
        }

        mark {
            padding: 0;
            background-color: #ffe456 !important;
        }

        .sotuphap html {
            display: none;
        }

        table, th, td {
            border: 1px solid #868585;
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

        table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
        }

        th, td {
            text-align: left;
            padding: 10px;
            font-size: 11px;
        }

        tr, td {
            text-align: left;
            padding: 5px !important;
            font-size: 11px;
        }

        table th {
            background-color: #0e5965c2;
            font-size: 11px;

            color: rgb(255, 251, 251)
        }

        .table td, .table th {
            vertical-align: middle !important;
        }

        .content-disp {
            display: none;
        }

        mark {
            padding: 0;
            background-color: #ffe456 !important;
        }

        table {
            table-layout: fixed;
            width: 100%;
        }

        table td {
            word-wrap: break-word;
            overflow-wrap: break-word;
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
            font-size: 11px !important;
        }

    </style>
@stop
@section('content')
    <section class="content">
        <div class="bs-example">
            <ul class="nav nav-tabs">
                <li id="tab-ds" class="active">
                    <a href="#ds-f" id="tab-ds-a" data-toggle="tab">Đương sự</a>
                </li>
                <li id="tab-ts">
                    <a href="#ts-f" id="tab-ts-a" data-toggle="tab">Tài sản</a>
                </li>


            </ul>
            <div id="myTabContent" class="tab-content">
                <!--Tab hồ sơ con-->
                <div class="tab-pane fade active in border-custom" id="ds-f">

                    <div class="panel-body">
                        <div class="row" id="rwdss" style="overflow:scroll; height:250px;">
                            <table id="customers-table" class="table table-bordered table-hover mb-0">
                                <thead>
                                <tr>
                                    <th class="col-md-6 ">Nhãn khách hàng</th>
                                    <th class="col-md-6">Hôn phối</th>
                                </tr>
                                </thead>
                                <tbody id="rwds">
                                </tbody>
                            </table>

                        </div>

                    </div>
                    <div class="btn-group" id="filterDay">
                        <label>Số lượng hiển thị:</label>
                        <label>
                            <input type="radio" name="loai" value="10" onclick="changesl(10)" checked>10
                        </label>
                        <label>
                            <input type="radio" name="loai" value="30" onclick="changesl(30)"> 30
                        </label>
                        <label>
                            <input type="radio" name="loai" value="50" onclick="changesl(50)"> 50
                        </label>
                    </div>
                </div>

                <!--Tab hồ tài sản-->
                <div class="tab-pane fade border-custom  in" id="ts-f">

                    <div class="panel-body">
                        <div class="row" id="rwts" style="overflow:scroll; height:250px;">


                        </div>

                    </div>
                    <div class="btn-group" id="filterDayts">
                        <label>Số lượng hiển thị:</label>
                        <label>
                            <input type="radio" name="loaits" value="10" onclick="changesl_ts(10)" checked>10
                        </label>
                        <label>
                            <input type="radio" name="loaits" value="20" onclick="changesl_ts(20)"> 20
                        </label>
                        <label>
                            <input type="radio" name="loaits" value="30" onclick="changesl_ts(30)"> 30
                        </label>
                    </div>
                </div>
            </div>
        </div>



        <form action="{{ route('indexSuutra') }}" method="get" id="formSreach">
            <div class="row">
                <div class="col-md-4 search">
                    <span class="fa fa-search fa-search-custom "></span>
                    <input type="text" class="form-control" id="searchbox" name="coban"
                           placeholder="Tìm kiếm đương sự"
                           value="{{ $getcoban }}" autofocus>
                </div>
                <div class="col-md-4 search">
                    <span class="fa fa-search fa-search-custom"></span>
                    <input type="text" class="form-control" id="searchbox" name="nangcao"
                           placeholder="Nhập vào thông tin tài sản" value="{{ $getNangCao }}" autofocus>
                </div>
                <div class="col-md-4 search">
                    <button class="btn btn-success btn1" type="button" id="btnsearch">
                        <i class="fa fa-search"></i>
                        Tìm kiếm
                    </button>
                    <a class="btn btn-primary btn2" onclick="import_file()">
                        <i class="fa fa-plus"></i>
                        IMPORT
                    </a>
                    <a id="btnprint" class="btn btn-warning">
                        <i class="fa fa-print"></i>
                        PRINT
                    </a>
                </div>
            </div>
        </form>
        <div class="row">
            @if(request()->input('coban') == null && request()->input('nangcao') == null)
                <a></a>
            @else
                <a>Có <span style="color : red; font-weight: bold;">{{$count}}</span> kết quả được tìm thấy.</a>
            @endif
        </div>
        <div class="row bctk-scrollable-list" style="overflow-x: hidden;">
            <table id="noi-bo-table" class="table-bordered  ">
                <thead>
                <tr class="text-center" style="background-color:#eeeeee">
                    <th style="width: 8%;font-size: 11px">Ngày nhập<br> hệ thống</th>
                    <th style="width: 8%;font-size: 11px">Ngày CC/<br>ngăn chặn</th>
                    <th style="width: 20%;font-size: 11px">Các bên liên quan</th>
                    <th style="width: 20%;font-size: 11px">Nội dung tóm tắt/<br> công văn</th>
                    <th style="width: 8%;font-size: 11px">Số hợp đồng/<br> CV NC</th>
                    <th style="width: 10%;font-size: 11px">Tên hợp đồng/<br> công văn</th>
                    <th style="width: 10%;font-size: 11px">Công chứng viên/<br> Người nhập</th>
                    <th style="width: 8%;font-size: 11px">Văn Phòng</th>
                    <th style="width: 8%;font-size: 11px">Chặn/Giải tỏa</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $val)
                    @if($val["ngan_chan"] == \App\Models\SuuTraModel::NORMAL)
                        <tr class="khong_ngan_chan_mau_den">
                            <td class="qktd">{{ \Carbon\Carbon::parse($val["ngay_nhap"])->format('d/m/Y') }}</td>
                            <td class="qktd">{{ \Carbon\Carbon::parse($val["ngay_cc"])->format('d/m/Y') }}</td>
                            <td class="p-22 qktd" style="text-align: justify;">
                                @if (strlen($val["duong_su"]) > 150)
                                    {{ mb_substr($val["duong_su"], 0, 150, 'UTF-8') }}
                                    <div class="modal fade bd-example-modal-sm"
                                         id="more-content-md3-{{ $val["st_id"] }}" tabindex="-1" role="dialog"
                                         aria-labelledby="mySmallModalLabel" aria-hidden="false">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header qkmodel">
                                                    <h5 class="modal-title qkmodel">
                                                        Thông tin chi tiết</h5>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="qktrang">
                                                        {!! $val["duong_su"] !!}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <span id="three-dot2-{{ $val["st_id"] }}">...</span>
                                    <span id="{{ $val["st_id"] }}" onclick="showinfo3('{{ $val["st_id"] }}')">
                                            <i id="search-icon2-{{ $val["st_id"] }}"
                                               class="fa fa-search-plus text-primary"></i>
                                        </span>

                                @else

                                    {{ $val["duong_su"] }}
                                @endif
                            </td>
                            <td class="p-33 qktd" style="text-align: justify;">
                                @if(strlen($val["texte"]) > 150)
                                    {{mb_substr($val["texte"], 0, 150, 'UTF-8')}}
                                    <div class="modal fade bd-example-modal-sm" id="more-content-md-{{$val["st_id"]}}"
                                         tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
                                         aria-hidden="false">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header qkmodel">
                                                    <h5 class="modal-title qkmodel">
                                                        Thông tin chi tiết</h5>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="qktrang">{!!$val["texte"] !!}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <span id="three-dot-{{$val["st_id"]}}">...</span>
                                    <span id="{{$val["st_id"]}}" onclick="showinfo('{{$val["st_id"]}}')">
                                            <i id="search-icon-{{$val["st_id"]}}"
                                               class="fa fa-search-plus text-primary"></i>
                                        </span>
                                @else
                                    {{$val["texte"]}}
                                @endif
                                <br>
                                <span style="color: red">{{ $val["cancel_description"] }}</span>
                            </td>
                            <td class="qktd">{{ $val["so_hd"] }}</td>
                            @if(is_array(json_decode($val["ten_hd"])))
                                <td class="p-44 qktd" style="text-align: justify;">
                                    @foreach(json_decode($val["ten_hd"]) as $value)
                                        <span>{{ \App\VanBanModel::where('vb_id',$value)->first()->vb_nhan }}.</span>
                                        <br>
                                    @endforeach
                                </td>
                            @else
                                <td>
                                    <span>{{ ($val["ten_hd"]) }}</span>
                                </td>
                            @endif
                            @if($val["ma_phan_biet"] == 'D')
                                <td class="qktd">{{ $val["first_name"] }}</td>
                                <td class="qktd">{{ $val["cn_ten"] }}</td>
                            @else
                                <td class="qktd">{{ $val["ccv_master"] }}</td>
                                <td class="qktd">{{ $val["vp_master"] }}</td>
                            @endif
                            <td class="qktd">
                                <div class="row">
                                    @if($val["ma_phan_biet"] == 'D')
                                        <span>Jdata</span>
                                    @else
                                        <span>Dữ liệu khác</span>
                                    @endif
                                </div>
                                <div class="row">
                                    @if($val["ngan_chan"] == \App\Models\SuuTraModel::NORMAL)

                                    @endif
                                </div>
                                @php
                                    $role = Sentinel::check()->user_roles()->first()->slug;
                                @endphp
                                @if($role=='phong-khac')
                                    @if($val["ma_phan_biet"] == 'D' && $val["status"] == 1)
                                        <div class="row">chưa duyệt</div>
                                    @else
                                    @endif
                                @else
                                @endif
                                <div class="row">
                                    @if(isset($val["picture"]))
                                        <?php
                                        $imgs = json_decode($val["picture"]);
                                        ?>
                                        @if(is_array($imgs))
                                            @foreach($imgs as $img)
                                                @if(substr($img, -3)=='jpg' || substr($img, -3)=='png')
                                                    <div class="col-md-2 mb-2 mt-1" style="padding-left: 0px;">
                                                        <a class="fancybox-effects-a" target="_blank"
                                                           href="{{url('images/suutra').'/'.$img}}">
                                                            <img src="{{url('images/suutra').'/'.$img}}"
                                                                 width="20" height="20">
                                                        </a>
                                                    </div>
                                                    <br>
                                                @else
                                                    <a href="{{url('images/suutra').'/'.$img}}"><span>{{ $img }}</span></a>
                                                @endif
                                            @endforeach
                                        @endif
                                    @else
                                    @endif
                                </div>
                                @php
                                    $role = Sentinel::check()->user_roles()->first()->slug;
                                    $id_vp=\App\Models\NhanVienModel::find(Sentinel::check()->id)->nv_vanphong;
                                @endphp
                                @if($val["ma_phan_biet"] == 'D')
                                    @if($role=='admin'|| $role=='chuyen-vien-so'||$role=='truong-van-phong'||$role=='cong-chung-vien' || $role=='ke-toan')
                                        <div class="row">
                                            @if($role == 'ke-toan' && $val->vp == $id_vp)
                                                <a href="{{ route('editSuutra',['id' => $val["st_id"]]) }}"
                                                   class="btn btn-secondary">
                                                    <i class="fa fa-pencil-square-o"></i>
                                                </a>
                                            @endif
                                            @if($val->ccv==Sentinel::getUser()->id)
                                                <a href="{{ route('editSuutra',['id' => $val["st_id"]]) }}"
                                                   class="btn btn-secondary">
                                                    <i class="fa fa-pencil-square-o"></i>
                                                </a>
                                            @endif
                                            @if($val["status"] == 1)
                                                <a data-toggle="modal"
                                                   data-target="#confirm-{{$val["st_id"]}}"
                                                   class="btn btn-success">
                                                    <i class="fa fa-check"></i>
                                                </a>
                                                <div class="modal fade" id="confirm-{{$val["st_id"]}}" role="dialog"
                                                     aria-labelledby="modalLabeldanger">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-danger">
                                                                <h4 class="modal-title" id="modalLabeldanger">Chú
                                                                    ý!</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Duyệt hợp đồng: {{$val["ten_hd"]}}</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <form
                                                                    action="{{ route('duyetSuutra',['id' => $val["st_id"]]) }}"
                                                                    method="get">
                                                                    <div class="form-inline">
                                                                        <button type="submit" class="btn btn-danger">Có,
                                                                            duyệt!
                                                                        </button>
                                                                        <a href="#" data-dismiss="modal"
                                                                           class="btn btn-warning">Không</a>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                            @endif
                                        </div>
                                    @endif
                                @else
                                @endif
                            </td>
                        </tr>
                    @elseif($val["ngan_chan"] == \App\Models\SuuTraModel::PREVENT)
                        <tr class="ngan_chan_mau_do text-danger">
                            <td class="qktd">{{ \Carbon\Carbon::parse($val["ngay_nhap"])->format('d/m/Y') }}</td>
                            <td class="qktd">{{ \Carbon\Carbon::parse($val["ngay_cc"])->format('d/m/Y') }}</td>
                            <td class="p-22 qktd" style=" text-align: justify;">
                                @if (strlen($val["duong_su"]) > 150)
                                    {{ mb_substr($val["duong_su"], 0, 150, 'UTF-8') }}
                                    <div class="modal fade bd-example-modal-sm"
                                         id="more-content-md3-{{ $val["st_id"] }}" tabindex="-1" role="dialog"
                                         aria-labelledby="mySmallModalLabel" aria-hidden="false">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header qkmodel">
                                                    <h5 class="modal-title qkmodel">
                                                        Thông tin chi tiết</h5>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="qkdo">
                                                        {!! $val["duong_su"] !!}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <span id="three-dot2-{{ $val["st_id"] }}">...</span>
                                    <span id="{{ $val["st_id"] }}" onclick="showinfo3('{{ $val["st_id"] }}')">
                                            <i id="search-icon2-{{ $val["st_id"] }}"
                                               class="fa fa-search-plus text-primary"></i>
                                        </span>

                                @else

                                    {{ $val["duong_su"] }}
                                @endif
                            </td>
                            <td class="p-33 qktd" style=" text-align: justify;">
                                @if(strlen($val["texte"]) > 150){{mb_substr($val["texte"],0,150, "UTF-8")}}
                                <div class="modal fade bd-example-modal-sm" id="more-content-md-{{$val["st_id"]}}"
                                     tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
                                     aria-hidden="false">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header qkmodel">
                                                <h5 class="modal-title qkmodel">
                                                    Thông tin chi tiết
                                                </h5>
                                            </div>
                                            <div class="modal-body">
                                                <p class="qkdo">{!!$val["texte"] !!}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <span id="three-dot-{{$val["st_id"]}}">...</span>
                                <span id="{{$val["st_id"]}}" onclick="showinfo('{{$val["st_id"]}}')">
                                        <i id="search-icon-{{$val["st_id"]}}" class="fa fa-search-plus text-primary"></i>
                                    </span>
                                @else
                                    {{$val["texte"]}}
                                @endif
                                <br>
                                <span style="color: red">{{ $val["cancel_description"] }}</span>
                            </td>
                            <td class="qktd">{{ $val["so_hd"] }}</td>
                            @if(is_array(json_decode($val["ten_hd"])))
                                <td class="p-44 qktd" style="text-align: justify;">
                                    @foreach(json_decode($val["ten_hd"]) as $value)
                                        <span>{{ \App\VanBanModel::where('vb_id',$value)->first()->vb_nhan }}.</span>
                                        <br>
                                    @endforeach
                                </td>
                            @else
                                <td class="p-44 qktd" style="text-align: justify;">
                                    <span>{{ ($val["ten_hd"]) }}</span>
                                </td>
                            @endif
                            @if($val["ma_phan_biet"] == 'D')
                                <td class="qktd">{{ $val["first_name"] }}</td>
                                <td class="qktd">{{ $val["cn_ten"] }}</td>
                            @else
                                <td class="qktd">{{ $val["ccv_master"] }}</td>
                                <td class="qktd">{{ $val["vp_master"] }}</td>
                            @endif
                            <td class="qktd">
                                <div class="row">
                                    @if($val["ma_phan_biet"] == 'D')
                                        <span>Jdata</span>
                                    @else
                                        <span>Dữ liệu khác</span>
                                    @endif
                                </div>
                                <div class="row">Bị chặn</div>
                                @php
                                    $role = Sentinel::check()->user_roles()->first()->slug;
                                @endphp
                                @if($role=='phong-khac')
                                    @if($val["ma_phan_biet"] == 'D' && $val["status"] == 1)
                                        <div class="row">chưa duyệt</div>
                                    @else
                                    @endif
                                @else
                                @endif
                                <div class="row">
                                    @if($val["picture"])
                                        <?php
                                        $imgs = json_decode($val["picture"]);
                                        ?>
                                        @if(is_array($imgs))
                                            @foreach($imgs as $img)
                                                @if(substr($img, -3)=='jpg' || substr($img, -3)=='png')
                                                    <div class="col-md-2 mb-2 mt-1" style="padding-left: 0px;">
                                                        <a class="fancybox-effects-a" target="_blank"
                                                           href="{{url('images/suutra').'/'.$img}}">
                                                            <img src="{{url('images/suutra').'/'.$img}}"
                                                                 width="20" height="20">
                                                        </a>
                                                    </div>
                                                    <br>
                                                @else
                                                    <a href="{{url('images/suutra').'/'.$img}}"><span>{{ $img }}</span></a>
                                                @endif
                                            @endforeach
                                        @endif
                                    @endif
                                </div>
                                @if($val["ma_phan_biet"] == 'D')
                                    @php
                                        $role = Sentinel::check()->user_roles()->first()->slug;
                                    @endphp
                                    @if($role=='admin'|| $role=='chuyen-vien-so')
                                        <div class="row">
                                            {{--                                            @if($val->ccv==Sentinel::check()->id||Sentinel::inRole('admin'))--}}
                                            <a href="{{ route('editSuutra',['id' => $val["st_id"]]) }}"
                                               class="btn btn-danger">
                                                <i class="fa fa-pencil-square-o"></i>
                                            </a>
                                            {{--                                            @endif--}}
                                            @if($val["status"] == 1)
                                                <a data-toggle="modal"
                                                   data-target="#confirm-{{$val["st_id"]}}"
                                                   class="btn btn-success">
                                                    <i class="fa fa-check"></i>
                                                </a>
                                                <div class="modal fade" id="confirm-{{$val["st_id"]}}" role="dialog"
                                                     aria-labelledby="modalLabeldanger">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-danger">
                                                                <h4 class="modal-title" id="modalLabeldanger">Chú
                                                                    ý!</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Duyệt hợp đồng: {{$val["ten_hd"]}}</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <form
                                                                    action="{{ route('duyetSuutra',['id' => $val["st_id"]]) }}"
                                                                    method="get">
                                                                    <div class="form-inline">
                                                                        <button type="submit" class="btn btn-danger">Có,
                                                                            duyệt!
                                                                        </button>
                                                                        <a href="#" data-dismiss="modal"
                                                                           class="btn btn-warning">Không</a>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                            @endif
                                        </div>
                                    @endif
                                @else
                                @endif
                            </td>
                        </tr>
                    @else
                        <tr class="canh_bao_mau_xanh text-primary">
                            <td class="qktd">{{ \Carbon\Carbon::parse($val["ngay_nhap"])->format('d/m/Y') }}</td>
                            <td class="qktd">{{ \Carbon\Carbon::parse($val["ngay_cc"])->format('d/m/Y') }}</td>
                            <td class="p-22 qktd" style="text-align: justify;">
                                @if (strlen($val["duong_su"]) > 150)
                                    {{ mb_substr($val["duong_su"], 0, 150, 'UTF-8') }}
                                    <div class="modal fade bd-example-modal-sm"
                                         id="more-content-md3-{{ $val["st_id"] }}" tabindex="-1" role="dialog"
                                         aria-labelledby="mySmallModalLabel" aria-hidden="false">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header qkmodel">
                                                    <h5 class="modal-title qkmodel">
                                                        Thông tin chi tiết</h5>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="qkxanh">
                                                        {!! $val["duong_su"] !!}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <span id="three-dot2-{{ $val["st_id"] }}">...</span>
                                    <span id="{{ $val["st_id"] }}" onclick="showinfo3('{{ $val["st_id"] }}')">
                                            <i id="search-icon2-{{ $val["st_id"] }}"
                                               class="fa fa-search-plus text-primary"></i>
                                        </span>

                                @else

                                    {{ $val["duong_su"] }}
                                @endif
                            </td>
                            <td class="p-33 qktd" style="text-align: justify ;">
                                @if(strlen($val["texte"]) > 150)
                                    {{mb_substr($val["texte"], 0, 150, 'UTF-8')}}
                                    <div class="modal fade bd-example-modal-sm" id="more-content-md-{{$val["st_id"]}}"
                                         tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
                                         aria-hidden="false">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header qkmodel">
                                                    <h5 class="modal-title qkmodel">
                                                        Thông tin chi tiết
                                                    </h5>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="qkxanh">{!!$val["texte"] !!}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <span id="three-dot-{{$val["st_id"]}}">...</span>
                                    <span id="{{$val["st_id"]}}" onclick="showinfo('{{$val["st_id"]}}')">
                                            <i id="search-icon-{{$val["st_id"]}}"
                                               class="fa fa-search-plus text-primary"></i>
                                        </span>
                                @else
                                    {{$val["texte"]}}
                                @endif
                                <br>
                                <span style="color: red">{{ $val["cancel_description"] }}</span>
                            </td>
                            <td style="font-size: 14px">{{ $val["so_hd"] }}</td>
                            @if(is_array(json_decode($val["ten_hd"])))
                                <td class="p-44 qktd" style="text-align: justify;">
                                    @foreach(json_decode($val["ten_hd"]) as $value)
                                        <span>{{ \App\VanBanModel::where('vb_id',$value)->first()->vb_nhan }}.</span>
                                        <br>
                                    @endforeach
                                </td>
                            @else
                                <td>
                                    <span>{{ ($val["ten_hd"]) }}</span>
                                </td>
                            @endif
                            @if($val["ma_phan_biet"] == "D")
                                <td class="qktd">{{ $val["first_name"] }}</td>
                                <td class="qktd">{{ $val["cn_ten"] }}</td>
                            @else
                                <td class="qktd">{{ $val["ccv_master"] }}</td>
                                <td class="qktd">{{ $val["vp_master"] }}</td>
                            @endif
                            <td class="qktd">
                                <div class="row">
                                    @if($val["ma_phan_biet"] == 'D')
                                        <span>Jdata</span>
                                    @else
                                        <span>Dữ liệu khác</span>
                                    @endif
                                </div>
                                <div class="row">
                                    @if($val["ngan_chan"] == 2)
                                        Cảnh báo
                                    @endif
                                </div>
                                @php
                                    $role = Sentinel::check()->user_roles()->first()->slug;
                                @endphp
                                @if($role=='phong-khac')
                                    @if($val["ma_phan_biet"] == 'D' && $val["status"] == 1)
                                        <div class="row">chưa duyệt</div>
                                    @else
                                    @endif
                                @else
                                @endif
                                <div class="row">
                                    @if($val["picture"])
                                        <?php
                                        $imgs = json_decode($val["picture"]);
                                        ?>
                                        @if(is_array($imgs))
                                            @foreach($imgs as $img)
                                                @if(substr($img, -3)=='jpg' || substr($img, -3)=='png')
                                                    <div class="col-md-2 mb-2 mt-1" style="padding-left: 0px;">
                                                        <a class="fancybox-effects-a" target="_blank"
                                                           href="{{url('images/suutra').'/'.$img}}">
                                                            <img src="{{url('images/suutra').'/'.$img}}"
                                                                 width="20" height="20">
                                                        </a>
                                                    </div>
                                                    <br>
                                                @else
                                                    <a href="{{url('images/suutra').'/'.$img}}"><span>{{ $img }}</span></a>
                                                @endif
                                            @endforeach
                                        @endif
                                    @endif
                                </div>
                                @php
                                    $role = Sentinel::check()->user_roles()->first()->slug;
                                @endphp
                                @if($val["ma_phan_biet"] == 'D')
                                    @if($role=='admin'|| $role=='chuyen-vien-so')
                                        <div class="row">
                                            <a href="{{ route('editSuutra',['id' => $val["st_id"]]) }}"
                                               class="btn btn-primary">
                                                <i class="fa fa-pencil-square-o"></i>
                                            </a>
                                            @if($val["status"] == 1)
                                                <a data-toggle="modal"
                                                   data-target="#confirm-{{$val["st_id"]}}"
                                                   class="btn btn-success">
                                                    <i class="fa fa-check"></i>
                                                </a>
                                                <div class="modal fade" id="confirm-{{$val["st_id"]}}" role="dialog"
                                                     aria-labelledby="modalLabeldanger">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-danger">
                                                                <h4 class="modal-title" id="modalLabeldanger">Chú
                                                                    ý!</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Duyệt hợp đồng: {{$val["ten_hd"]}}</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <form
                                                                    action="{{ route('duyetSuutra',['id' => $val["st_id"]]) }}"
                                                                    method="get">
                                                                    <div class="form-inline">
                                                                        <button type="submit" class="btn btn-danger">Có,
                                                                            duyệt!
                                                                        </button>
                                                                        <a href="#" data-dismiss="modal"
                                                                           class="btn btn-warning">Không</a>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                            @endif
                                        </div>
                                    @endif
                                @else
                                @endif
                            </td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="col-ms-12">
            <div class="col-sm-6">
                {{$data->onEachSide(1)->appends(request()->input())->links()}}
            </div>
            <div class="col-sm-6">
                <p class="pull-right" style="font-size: 16px;">Tổng số:
                    <b>
                        <span style="color: red">{{count(\App\Models\SuuTraModel::all())}}</span>
                    </b>
                </p>
            </div>
        </div>
    </section>

    {{--    modal inport--}}
    <div class="modal fade in" id="modal-import" tabindex="-1" role="dialog" aria-hidden="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header qkmodel">
                    <h5 class="modal-title qkmodel">IMPORT</h5>
                </div>
                <form action="{{ route('importSuutra') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <p style="background-color: red; padding: 10px; color: white; border-radius: 10px;">
                            Trước tiên vui lòng tải file mẫu về và import dữ liệu đúng định dạng
                            <a style="color: yellow;" title="File Mẫu"
                               href="{{ route('exportExample') }}"
                               target="_blank">File Mẫu</a>.
                            Vì một số file có thể có định dạng khác và vì lý do bảo mật nên chúng tôi khuyến khích copy
                            dữ
                            liệu từ file của bạnnsang file mẫu và up file mẫu lên. Hạn chế up file của bạn lên mặc dù
                            cấu
                            trúc các cột đúng với file mẫu.
                        </p>
                        <input name="import" type="file" class="form-control">
                    </div>
                    <div class="modal-footer">
                        <div>
                            <button type="button" data-dismiss="modal" class="btn btn-secondary">Hủy</button>
                            <button type="submit" class="btn btn-primary">Import</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{--    modal print--}}
    <div id="printThis">
        <div id="print" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
             aria-hidden="true">

            <div class="modal-dialog modal-lg">

                <!-- Modal Content: begins -->
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="gridSystemModalLabel">Your Headings</h4>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <div class="body-message">
                            <h4>Any Heading</h4>
                            <p>And a paragraph with a full sentence or something else...</p>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
                        <button id="btnPrint" type="button" class="btn btn-default">Print</button>
                    </div>

                </div>
                <!-- Modal Content: ends -->

            </div>
        </div>
    </div>

    {{--    modal thêm hợp đồng--}}


    {{--    modal sửa hợp đồng--}}

    {{--    modal thêm mới công văn--}}

    {{-- modal cập nhật ngăn chặn --}}
    <div class="modal fade in" id="edit-modal-nganchan" tabindex="-1" role="dialog" aria-hidden="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header qkmodel">
                    <h5 class="modal-title qkmodel">Cập nhật công văn ngăn chặn</h5>
                </div>
                <div class="modal-body">
                    <form action="#" method="post" enctype="multipart/form-data">
                        @csrf
                        <input id="id_ccv" name="id_ccv" value="{{Sentinel::getUser()->id}}" hidden>
                        <input id="st_id_nc" name="st_id" hidden>
                        <div class="modal-body mx-1">
                            <div class="md-form mb-1">
                                <label data-error="wrong" data-success="right">
                                    Tên công văn (<span class="text-danger qksao">*</span>):
                                </label>
                                <input type="text" id="edit-ten-hd-nc" name="ten_hd" class="form-control" required>
                            </div>
                            <div class="md-form mb-1">
                                <label data-error="wrong" data-success="right">
                                    Số công văn (<span class="text-danger qksao">*</span>):
                                </label>
                                <input type="text" id="edit-so_hd-nc" name="so_hd" class="form-control" required>
                            </div>
                            <div class="md-form mb-1">
                                <label data-error="wrong" data-success="right">
                                    Ngày ngăn chặn (<span class="text-danger qksao">*</span>):
                                </label>
                                <input name="ngayapdung" id="ngayapdung-nc" type="date" class="form-control"
                                       placeholder="Ngày áp dụng" required>
                            </div>
                            <div class="md-form mb-1">
                                <label data-error="wrong" data-success="right">
                                    Các bên liên quan (<span class="text-danger qksao">*</span>):
                                </label>
                                <textarea type="text" id="edit-duongsu-nc" name="duongsu" class="form-control" rows="4"
                                          cols="50" required></textarea>
                            </div>
                            <div class="md-form mb-2">
                                <label data-error="wrong" data-success="right">
                                    Nội dung công văn (<span class="text-danger">*</span>):
                                </label>
                                <textarea type="text" id="edit-noidung-nc" name="noidung" class="form-control" rows="4"
                                          cols="50" required></textarea>
                            </div>
                            <div class="md-form mb-2" id="filterDay">
                                <label>Loại (<span class="text-danger qksao">*</span>):</label>
                                <label style="margin-left: 10px">
                                    <input type="radio" id="nc" name="loai" value="1">Ngăn chặn
                                </label>
                                <label style="margin-left: 10px">
                                    <input type="radio" id="gt" name="loai" value="0"> Giải tỏa
                                </label>
                            </div>
                            <div class="md-form mb-2">
                                <label for="pic">Ảnh đính kèm:</label><br>
                                <div class="form-group row">
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div class="fileinput-preview fileinput-exists thumbnail"
                                             style="max-width: 250px; max-height: 250px;">
                                            <img id="img2">
                                        </div>
                                        <span class="btn-file">
                                            <div class="col-md-9" style="padding: 0px">
                                            <input id="pic2" name="pic2[]" type="file" accept="image/*"
                                                   class="form-control" onchange="loadImgKH(this,'modal')" multiple/>
                                            </div>
                                            <div class="col-md-3">
                                                <a href="#" class="btn btn-danger" data-dismiss="fileinput">Gỡ bỏ</a>
                                            </div>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" data-dismiss="modal" class="btn btn-default qkbtn">Hủy</button>
                            <button type="submit" class="btn btn-primary qkbtn">Lưu</button>
                        </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('footer_scripts')
    <script type="text/javascript" src="{{ asset('assets/js/imgPreview.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/jquery.mark.js') }}"></script>
    {{--    <script src="{{ asset('assets/vendors/jasny-bootstrap/js/jasny-bootstrap.js') }}" type="text/javascript"></script>--}}
    {{--    <script type="text/javascript" src="{{ asset('assets/vendors/fancybox/jquery.fancybox.pack.js') }}"></script>--}}
    {{--    <script type="text/javascript" src="{{ asset('assets/js/pages/gallery.js') }}"></script>--}}
    <script type="text/javascript" src="http://johannburkard.de/resources/Johann/jquery.highlight-5.js"></script>
    {{--    <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>--}}

    <script type="text/javascript">
        $('#btnprint').click(function () {
            $('#formSreach').attr('target', "_blank");
            $('#formSreach').attr('action', "{{ route('PrintSuuTra') }}");
            $("#formSreach").submit();
        });
        $('#btnsearch').click(function () {
            $('#formSreach').removeAttr('target');
            $('#formSreach').attr('action', "{{ route('indexSuutra') }}");
            $("#formSreach").submit();
        });
    </script>
    <script>
        var duongsu = get('duongsu');
        var taisan = get('taisan');
        if (duongsu || taisan) {
            $("#inputOwnerInfo").val(duongsu);
            $("#inputPropertyInfo").val(taisan);
            setTimeout(function () {
                document.getElementById('submitsuutra').click();
            }, 500);
        }

        function get(name) {
            if (name = (new RegExp('[?&]' + encodeURIComponent(name) + '=([^&]*)')).exec(location.search))
                return decodeURIComponent(name[1]);
        }

        $(".toggle-password").click(function () {

            $(this).toggleClass("fa-eye fa-eye-slash");
            $(this).attr("toggle");
            var input = $('.password');
            if (input.attr("type") === "password") {
                $('.toggle-password').removeClass('text-muted');
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
                $('.toggle-password').addClass('text-muted');
            }
        });

    </script>
    <script>
        var i = 0;
        var dragging = false;
        $('#dragbar').mousedown(function (e) {
            e.preventDefault();

            dragging = true;
            var main = $('#main');
            var ghostbar = $('<div>', {
                id: 'ghostbar',
                css: {
                    height: main.outerHeight(),
                    top: main.offset().top,
                    left: main.offset().left
                }
            }).appendTo('body');

            $(document).mousemove(function (e) {
                ghostbar.css("left", e.pageX + 2);
            });

        });

        $(document).mouseup(function (e) {
            if (dragging) {
                var percentage = (e.pageX / window.innerWidth) * 100;
                var mainPercentage = 100 - percentage;

                $('#console').text("side:" + percentage + " main:" + mainPercentage);

                $('#sidebar').css("width", percentage + "%");
                $('#main').css("width", mainPercentage + "%");
                $('#ghostbar').remove();
                $(document).unbind('mousemove');
                dragging = false;
            }
        });
        var str_search_json2 = JSON.parse('{!! $str_json2 !!}');
        var str_search_json = JSON.parse('{!! $str_json !!}');
        var keywords2 = str_search_json2;
        var keywords = str_search_json;
        var getcoban = '{!! $getcoban !!}';
        var getnangcao = '{!! $getNangCao !!}';
        if (getcoban) {
            if (getcoban.search("%") == -1) {
                $('.p-22').unmark({
                    done: function () {
                        var str = "";
                        $.each(keywords2, function (k, v) {
                            str = " " + v
                        })
                        $('.p-22').mark(str, {
                            "separateWordSearch": false,
                            "diacritics": true
                        });

                    }
                });
            } else {
                $('.p-22').unmark({
                    done: function () {
                        $.each(keywords2, function (k, v) {
                            $('.p-22').mark(v, {
                                "separateWordSearch": false,
                                "diacritics": true
                            });
                        })
                    }
                });
            }

        }
        if (getnangcao) {
            if (getnangcao.search("%") == -1) {

                $('.p-33').unmark({
                    done: function () {
                        var str = "";

                        $.each(keywords, function (k, v) {
                            str = " " + v
                        })
                        $('.p-33').mark(str, {
                            "separateWordSearch": false,
                            "diacritics": true
                        });

                    }
                });
            } else {
                $('.p-33').unmark({
                    done: function () {
                        $.each(keywords, function (k, v) {
                            $('.p-33').mark(v, {
                                "separateWordSearch": false,
                                "diacritics": true
                            });
                        })
                    }
                });
            }

        }

        function readMore(element) {
            if ($('#more-content-' + element.id).is(":hidden")) {
                $('#three-dot-' + element.id).hide();

                $('#more-content-' + element.id).removeClass('content-disp');
                $('#search-icon-' + element.id).removeClass('fa-search-plus');
                $('#search-icon-' + element.id).addClass('fa-search-minus')

            } else {
                $('#three-dot-' + element.id).show();

                $('#more-content-' + element.id).addClass('content-disp');
                $('#search-icon-' + element.id).removeClass('fa-search-minus');
                $('#search-icon-' + element.id).addClass('fa-search-plus')
            }
        }

        function readMore2(element) {
            if ($('#more-content2-' + element.id).is(":hidden")) {
                $('#three-dot2-' + element.id).hide();

                $('#more-content2-' + element.id).removeClass('content-disp');
                $('#search-icon2-' + element.id).removeClass('fa-search-plus');
                $('#search-icon2-' + element.id).addClass('fa-search-minus')
            } else {
                $('#three-dot2-' + element.id).show();
                $('#more-content2-' + element.id).addClass('content-disp');
                $('#search-icon2-' + element.id).removeClass('fa-search-minus');
                $('#search-icon2-' + element.id).addClass('fa-search-plus')
            }
        }

        function import_file() {
            $('#modal-import').modal();
        }

        function add_nganchan() {
            $('#modal-nganchan').modal();
        }

        function add_suutra() {
            $('#modal-add').modal();
        }

        function showinfo(id) {
            $('#more-content-md-' + id).modal();

        }

        function showinfo2(id) {
            $('#more-content-md2-' + id).modal();

        }

        function showinfo3(id) {
            $('#more-content-md3-' + id).modal();

        }

        function acceptnganchan(id) {
            $.ajax({
                type: 'GET',
                url: '{{ route('acceptSuutra') }}',
                data: {
                    'id': id
                },
                success: function (result) {
                    location.reload();

                }
            });

        }


    </script>
@stop


{{--@extends('admin/layouts/default')--}}
{{--@section('title')--}}
{{--    Sưu tra    @parent--}}
{{--@stop--}}
{{--@section('header_styles')--}}
{{--    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/pages/tables.css') }}"/>--}}
{{--    <link rel="stylesheet" type="text/css"--}}
{{--          href="{{ asset('assets/vendors/datatables/css/dataTables.bootstrap.css') }}"/>--}}
{{--    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/datatables/css/scroller.bootstrap.css') }}">--}}
{{--    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/fancybox/jquery.fancybox.css') }}"--}}
{{--          media="screen"/>--}}
{{--    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/fancybox/jquery.fancybox-thumbs.css') }}"/>--}}
{{--    <link rel="stylesheet" href="{{ asset('assets/vendors/Buttons/css/buttons.css') }}"/>--}}
{{--    <link rel="stylesheet" href="{{ asset('assets/css/pages/advbuttons.css') }}"/>--}}

{{--    <style>--}}
{{--        .sotuphap html {--}}
{{--            display: none;--}}
{{--        }--}}

{{--        table, th, td {--}}
{{--            border: 1px solid #868585;--}}
{{--        }--}}

{{--        .p-33 {--}}
{{--            width: 20%;--}}
{{--        }--}}

{{--        .p-22 {--}}
{{--            width: 20%--}}
{{--        }--}}

{{--        .p-44 {--}}
{{--            width: 10%--}}
{{--        }--}}

{{--        table {--}}
{{--            width: 100%;--}}
{{--            margin-bottom: 1rem;--}}
{{--            color: #212529;--}}
{{--        }--}}

{{--        th, td {--}}
{{--            text-align: left;--}}
{{--            padding: 10px;--}}
{{--            font-size: 11px;--}}
{{--        }--}}

{{--        tr, td {--}}
{{--            text-align: left;--}}
{{--            padding: 5px !important;--}}
{{--            font-size: 11px;--}}
{{--        }--}}

{{--        table th {--}}
{{--            background-color: #0e5965c2;--}}
{{--            font-size: 11px;--}}

{{--            color: rgb(255, 251, 251)--}}
{{--        }--}}

{{--        .table td, .table th {--}}
{{--            vertical-align: middle !important;--}}
{{--        }--}}

{{--        .content-disp {--}}
{{--            display: none;--}}
{{--        }--}}

{{--        mark {--}}
{{--            padding: 0;--}}
{{--            background-color: #ffe456 !important;--}}
{{--        }--}}

{{--        table {--}}
{{--            table-layout: fixed;--}}
{{--            width: 100%;--}}
{{--        }--}}

{{--        table td {--}}
{{--            word-wrap: break-word;--}}
{{--            overflow-wrap: break-word;--}}
{{--        }--}}

{{--        .btn1 {--}}
{{--            font-weight: 500;--}}
{{--            background-color: white !important;--}}
{{--            color: #01bc8c !important;--}}
{{--            font-size: 14px !important;--}}
{{--        }--}}

{{--        .qkbtn {--}}
{{--            font-weight: bold;--}}
{{--            font-size: 14px !important;--}}
{{--        }--}}

{{--        .btn2 {--}}
{{--            font-weight: 500;--}}
{{--            background-color: white !important;--}}
{{--            color: #1a67a3 !important;--}}
{{--            font-size: 14px !important;--}}
{{--        }--}}

{{--        .qksao {--}}
{{--            font-weight: bold;--}}
{{--        }--}}

{{--        .qkmodel {--}}
{{--            background-color: #1a67a3 !important;--}}
{{--        }--}}

{{--        .qkdo {--}}
{{--            text-align: justify;--}}
{{--            font-size: 14px;--}}
{{--            color: red;--}}
{{--        }--}}

{{--        .qkxanh {--}}
{{--            text-align: justify;--}}
{{--            font-size: 14px;--}}
{{--            color: blue;--}}
{{--        }--}}

{{--        .qktrang {--}}
{{--            text-align: justify;--}}
{{--            font-size: 14px;--}}
{{--            color: white;--}}
{{--        }--}}

{{--        .qktd {--}}
{{--            font-size: 11px !important;--}}
{{--        }--}}
{{--    </style>--}}
{{--@stop--}}

{{--@section('footer_scripts')--}}
{{--    --}}{{-- <script> --}}
{{--    --}}{{-- function show(ngay_nhap) { --}}
{{--    --}}{{-- // alert('dd') --}}
{{--    --}}{{-- $('#show_'+ ngay_nhap).alert('đ'); --}}
{{--    --}}{{-- } --}}
{{--    --}}{{-- </script> --}}
{{--    <script type="text/javascript" src="{{ asset('assets/js/imgPreview.js') }}"></script>--}}

{{--    <script type="text/javascript" src="{{ asset('assets/js/jquery.mark.js') }}"></script>--}}
{{--    <script src="{{ asset('assets/vendors/jasny-bootstrap/js/jasny-bootstrap.js') }}" type="text/javascript"></script>--}}
{{--    <script type="text/javascript" src="{{ asset('assets/vendors/fancybox/jquery.fancybox.pack.js') }}"></script>--}}

{{--    <script type="text/javascript" src="{{ asset('assets/js/pages/gallery.js') }}"></script>--}}

{{--    <script type="text/javascript" src="http://johannburkard.de/resources/Johann/jquery.highlight-5.js"></script>--}}
{{--    <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>--}}

{{--    <style type="text/css">--}}
{{--        body {--}}
{{--            background-color: #E1E1E1--}}
{{--        }--}}

{{--        p {--}}
{{--            font-size: 16px--}}
{{--        }--}}

{{--        .highlight {--}}
{{--            background-color: yellow--}}
{{--        }--}}

{{--    </style>--}}
{{--    <script type="text/javascript">--}}
{{--        $('#btnprint').click(function () {--}}
{{--            $('#formSreach').attr('target', "_blank");--}}
{{--            $('#formSreach').attr('action', "{{ route('PrintSuuTra') }}");--}}
{{--            $("#formSreach").submit();--}}
{{--        });--}}
{{--        $('#btnsearch').click(function () {--}}
{{--            $('#formSreach').removeAttr('target');--}}
{{--            $('#formSreach').attr('action', "{{ route('indexSuutra') }}");--}}
{{--            $("#formSreach").submit();--}}
{{--        });--}}
{{--    </script>--}}
{{--    <script>--}}
{{--        var duongsu = get('duongsu');--}}
{{--        var taisan = get('taisan');--}}
{{--        if (duongsu || taisan) {--}}
{{--            $("#inputOwnerInfo").val(duongsu);--}}
{{--            $("#inputPropertyInfo").val(taisan);--}}
{{--            setTimeout(function () {--}}
{{--                document.getElementById('submitsuutra').click();--}}
{{--            }, 500);--}}
{{--        }--}}

{{--        function get(name) {--}}
{{--            if (name = (new RegExp('[?&]' + encodeURIComponent(name) + '=([^&]*)')).exec(location.search))--}}
{{--                return decodeURIComponent(name[1]);--}}
{{--        }--}}

{{--        $(".toggle-password").click(function () {--}}

{{--            $(this).toggleClass("fa-eye fa-eye-slash");--}}
{{--            $(this).attr("toggle");--}}
{{--            var input = $('.password');--}}
{{--            if (input.attr("type") === "password") {--}}
{{--                $('.toggle-password').removeClass('text-muted');--}}
{{--                input.attr("type", "text");--}}
{{--            } else {--}}
{{--                input.attr("type", "password");--}}
{{--                $('.toggle-password').addClass('text-muted');--}}
{{--            }--}}
{{--        });--}}

{{--    </script>--}}
{{--    <script>--}}
{{--        var i = 0;--}}
{{--        var dragging = false;--}}
{{--        $('#dragbar').mousedown(function (e) {--}}
{{--            e.preventDefault();--}}

{{--            dragging = true;--}}
{{--            var main = $('#main');--}}
{{--            var ghostbar = $('<div>', {--}}
{{--                id: 'ghostbar',--}}
{{--                css: {--}}
{{--                    height: main.outerHeight(),--}}
{{--                    top: main.offset().top,--}}
{{--                    left: main.offset().left--}}
{{--                }--}}
{{--            }).appendTo('body');--}}

{{--            $(document).mousemove(function (e) {--}}
{{--                ghostbar.css("left", e.pageX + 2);--}}
{{--            });--}}

{{--        });--}}

{{--        $(document).mouseup(function (e) {--}}
{{--            if (dragging) {--}}
{{--                var percentage = (e.pageX / window.innerWidth) * 100;--}}
{{--                var mainPercentage = 100 - percentage;--}}

{{--                $('#console').text("side:" + percentage + " main:" + mainPercentage);--}}

{{--                $('#sidebar').css("width", percentage + "%");--}}
{{--                $('#main').css("width", mainPercentage + "%");--}}
{{--                $('#ghostbar').remove();--}}
{{--                $(document).unbind('mousemove');--}}
{{--                dragging = false;--}}
{{--            }--}}
{{--        });--}}
{{--        var str_search_json2 = JSON.parse('{!! $str_json2 !!}');--}}
{{--        var str_search_json = JSON.parse('{!! $str_json !!}');--}}
{{--        var keywords2 = str_search_json2;--}}
{{--        var keywords = str_search_json;--}}
{{--        var getcoban = '{!! $getcoban !!}';--}}
{{--        var getnangcao = '{!! $getNangCao !!}';--}}
{{--        if (getcoban) {--}}
{{--            if (getcoban.search("%") == -1) {--}}
{{--                $('.p-22').unmark({--}}
{{--                    done: function () {--}}
{{--                        var str = "";--}}
{{--                        $.each(keywords2, function (k, v) {--}}
{{--                            str = " " + v--}}
{{--                        })--}}
{{--                        $('.p-22').mark(str, {--}}
{{--                            "separateWordSearch": false,--}}
{{--                            "diacritics": true--}}
{{--                        });--}}

{{--                    }--}}
{{--                });--}}
{{--            } else {--}}
{{--                $('.p-22').unmark({--}}
{{--                    done: function () {--}}
{{--                        $.each(keywords2, function (k, v) {--}}
{{--                            $('.p-22').mark(v, {--}}
{{--                                "separateWordSearch": false,--}}
{{--                                "diacritics": true--}}
{{--                            });--}}
{{--                        })--}}
{{--                    }--}}
{{--                });--}}
{{--            }--}}

{{--        }--}}
{{--        if (getnangcao) {--}}
{{--            if (getnangcao.search("%") == -1) {--}}

{{--                $('.p-33').unmark({--}}
{{--                    done: function () {--}}
{{--                        var str = "";--}}

{{--                        $.each(keywords, function (k, v) {--}}
{{--                            str = " " + v--}}
{{--                        })--}}
{{--                        $('.p-33').mark(str, {--}}
{{--                            "separateWordSearch": false,--}}
{{--                            "diacritics": true--}}
{{--                        });--}}

{{--                    }--}}
{{--                });--}}
{{--            } else {--}}
{{--                $('.p-33').unmark({--}}
{{--                    done: function () {--}}
{{--                        $.each(keywords, function (k, v) {--}}
{{--                            $('.p-33').mark(v, {--}}
{{--                                "separateWordSearch": false,--}}
{{--                                "diacritics": true--}}
{{--                            });--}}
{{--                        })--}}
{{--                    }--}}
{{--                });--}}
{{--            }--}}

{{--        }--}}

{{--        function readMore(element) {--}}
{{--            if ($('#more-content-' + element.id).is(":hidden")) {--}}
{{--                $('#three-dot-' + element.id).hide();--}}

{{--                $('#more-content-' + element.id).removeClass('content-disp');--}}
{{--                $('#search-icon-' + element.id).removeClass('fa-search-plus');--}}
{{--                $('#search-icon-' + element.id).addClass('fa-search-minus')--}}

{{--            } else {--}}
{{--                $('#three-dot-' + element.id).show();--}}

{{--                $('#more-content-' + element.id).addClass('content-disp');--}}
{{--                $('#search-icon-' + element.id).removeClass('fa-search-minus');--}}
{{--                $('#search-icon-' + element.id).addClass('fa-search-plus')--}}
{{--            }--}}
{{--        }--}}

{{--        function readMore2(element) {--}}
{{--            if ($('#more-content2-' + element.id).is(":hidden")) {--}}
{{--                $('#three-dot2-' + element.id).hide();--}}

{{--                $('#more-content2-' + element.id).removeClass('content-disp');--}}
{{--                $('#search-icon2-' + element.id).removeClass('fa-search-plus');--}}
{{--                $('#search-icon2-' + element.id).addClass('fa-search-minus')--}}
{{--            } else {--}}
{{--                $('#three-dot2-' + element.id).show();--}}
{{--                $('#more-content2-' + element.id).addClass('content-disp');--}}
{{--                $('#search-icon2-' + element.id).removeClass('fa-search-minus');--}}
{{--                $('#search-icon2-' + element.id).addClass('fa-search-plus')--}}
{{--            }--}}
{{--        }--}}

{{--        function import_file() {--}}
{{--            $('#modal-import').modal();--}}
{{--        }--}}

{{--        function add_nganchan() {--}}
{{--            $('#modal-nganchan').modal();--}}
{{--        }--}}

{{--        function add_suutra() {--}}
{{--            $('#modal-add').modal();--}}
{{--        }--}}

{{--        function showinfo(id) {--}}
{{--            $('#more-content-md-' + id).modal();--}}

{{--        }--}}

{{--        function showinfo2(id) {--}}
{{--            $('#more-content-md2-' + id).modal();--}}

{{--        }--}}

{{--        function showinfo3(id) {--}}
{{--            $('#more-content-md3-' + id).modal();--}}

{{--        }--}}

{{--        function acceptnganchan(id) {--}}
{{--            $.ajax({--}}
{{--                type: 'GET',--}}
{{--                url: '{{ route('acceptSuutra') }}',--}}
{{--                data: {--}}
{{--                    'id': id--}}
{{--                },--}}
{{--                success: function (result) {--}}
{{--                    location.reload();--}}

{{--                }--}}
{{--            });--}}

{{--        }--}}

{{--        $('#cac_ben_lien_quan').select2({--}}
{{--            placeholder: "Thêm các bên liên quan",--}}
{{--            minimumInputLength: 3,--}}
{{--            multiple: true,--}}
{{--            ajax: {--}}
{{--                url: "{{ url('account/kh') }}",--}}
{{--                quietMillis: 100,--}}
{{--                data: function (term, page) {--}}
{{--                    return {--}}
{{--                        q: term,--}}
{{--                        page_limit: 10,--}}
{{--                        page: page //you need to send page number or your script do not know witch results to skip--}}
{{--                    };--}}
{{--                },--}}
{{--                processResults: function (data) {--}}
{{--                    return {--}}
{{--                        results: $.map(data.data, function (obj) {--}}
{{--                            return {--}}
{{--                                id: obj.id,--}}
{{--                                text: obj.full_name--}}
{{--                            };--}}
{{--                        })--}}
{{--                    };--}}
{{--                },--}}
{{--                dropdownCssClass: "bigdrop"--}}
{{--            }--}}
{{--        });--}}

{{--    </script>--}}
{{--@stop--}}
{{--@section('footer_scripts')--}}
{{--    <script type="text/javascript" src="{{ asset('assets/js/imgPreview.js') }}"></script>--}}
{{--    <script type="text/javascript" src="{{ asset('assets/js/jquery.mark.js') }}"></script>--}}
{{--    <script src="{{ asset('assets/vendors/jasny-bootstrap/js/jasny-bootstrap.js') }}" type="text/javascript"></script>--}}
{{--    <script type="text/javascript" src="{{ asset('assets/vendors/fancybox/jquery.fancybox.pack.js') }}"></script>--}}
{{--    <script type="text/javascript" src="{{ asset('assets/js/pages/gallery.js') }}"></script>--}}
{{--    <script type="text/javascript" src="http://johannburkard.de/resources/Johann/jquery.highlight-5.js"></script>--}}
{{--    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>--}}
{{--    <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>--}}
{{--    <script type="text/javascript" src="http://johannburkard.de/resources/Johann/jquery.highlight-5.js"></script>--}}
{{--    <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>--}}


