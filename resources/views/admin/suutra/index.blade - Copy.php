@extends('admin/layouts/default')
@section('title')
    Sưu tra    @parent
@stop
@section('header_styles')
    {{--    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/pages/tables.css') }}"/>--}}
    {{--    <link rel="stylesheet" type="text/css"--}}
    {{--          href="{{ asset('assets/vendors/datatables/css/dataTables.bootstrap.css') }}"/>--}}
    {{--    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/datatables/css/scroller.bootstrap.css') }}">--}}
    {{--    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/fancybox/jquery.fancybox.css') }}"--}}
    {{--          media="screen"/>--}}
    {{--    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/fancybox/jquery.fancybox-thumbs.css') }}"/>--}}
    {{--    <link rel="stylesheet" href="{{ asset('assets/vendors/Buttons/css/buttons.css') }}"/>--}}
    {{--    <link rel="stylesheet" href="{{ asset('assets/css/pages/advbuttons.css') }}"/>--}}

    <style>
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
            font-size: 14px;
        }

        tr, td {
            text-align: left;
            padding: 5px !important;
            font-size: 14px;
        }

        table th {
            background-color: #0e5965c2;
            font-size: 14px;

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

        .btn1 {
            font-weight: 500;
            background-color: white !important;
            color: #01bc8c !important;
            font-size: 14px !important;
        }

        .qkbtn {
            font-weight: bold;
            font-size: 14px !important;
        }

        .btn2 {
            font-weight: 500;
            background-color: white !important;
            color: #1a67a3 !important;
            font-size: 14px !important;
        }

        .qksao {
            font-weight: bold;
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
            color: #016639;
        }

        .qktrang {
            text-align: justify;
            font-size: 14px;
            color: black;
        }

        .qktd {
            font-size: 14px !important;
            text-align: justify;
			 
        }

        li .active {
            color: #0b67cd !important;
        }
    </style>
@stop
@section('content')
    <section class="content">
        <div class="row">
            <ul class="nav nav-tabs">
                <li id="tab-ds" class="active">
                    <a href="#ds-f" id="tab-ds-a" class="active" data-toggle="tab">Tìm kiếm cơ bản</a>
                </li>
                <li id="tab-ts">
                    <a href="#ts-f" id="tab-ts-a" onclick="submit()" data-toggle="tab">Truy vết giao dịch</a>
                </li>
			
            </ul>
            <div id="myTabContent" class="tab-content">
                <!--Tab hồ sơ con-->
                <div class="tab-pane fade active show in bctk-scrollable-list" id="ds-f">
                    <div id="rwdss" style="">
                        <div class="row">
                            <div class="col-md-7">
                                <form action="{{ route('indexSuutra') }}" method="get" id="formSreach">
                                    <div class="col-md-4 search">
                                        <span class="fa fa-search fa-search-custom "></span>
                                        <input type="text" class="form-control" id="searchbox" name="coban"
                                               placeholder="Tìm kiếm các bên liên quan"
                                               value="{{ $getcoban }}" autofocus>
                                      <span style="font-size: 12px;color: black"><b style="color: red">Gợi ý:</b> Nguyễn Văn A để tìm<span
                                                style="color: red"> hoặc</span> (kèm năm sinh hoặc CMND)</span>
                                    </div>
                                    <div class="col-md-4 search">
                                        <span class="fa fa-search fa-search-custom"></span>
                                        <input type="text" class="form-control" id="searchbox1" name="nangcao"
                                               placeholder="Tìm kiếm nội dung giao dịch" value="{{ $getNangCao }}"
                                               autofocus>
                                      
                                    </div>
                                    <div class="col-md-3 search">
                                        <button class="btn btn-success btn1" type="submit" id="btnsearch">
                                            <i class="fa fa-search"></i>
                                            Tìm kiếm
                                        </button>
                                    </div>
                                

                                </form>
                            </div>
                            <div class="col-md-5">
                                <a class="btn btn-primary btn2" onclick="import_file()">
                                    <i class="fa fa-plus"></i>
                                    IMPORT
                                </a>
                                <a id="btnprint" class="btn btn-warning">
                                    <i class="fa fa-print"></i>
                                    PRINT
                                </a>
								 <a id="" class="btn btn-warning" href={{route("indexSuutraNew")}}>
                                    <i class="fa fa-certificate"></i>
                                    Dùng thử giao diện tra cứu mới
                                </a>
                            </div>
							
                        </div>
                        <div class="row">
                            @if(request()->input('coban') == null && request()->input('nangcao') == null)
                                <a></a>
                            @else
                                <a>Có <span style="color : red; font-weight: bold;">{{$count}}</span> kết quả được tìm
                                    thấy. </a<span> Nội dung có màu <span style="color:black;background-color:black">###</span> là dữ liệu bình thường, màu <span style="color:#016639;background-color:#016639">###</span> là dữ liệu cảnh báo, màu <span style="color:red;background-color:red">###</span>  là ngăn chặn</span>
                            @endif
                        </div>
                        <div class="row" style="overflow-x: hidden;">
                            @php
                                $role = Sentinel::check()->user_roles()->first()->slug;
                                $id_vp=\App\Models\NhanVienModel::find(Sentinel::check()->id)->nv_vanphong;
                            @endphp
                            <table id="noi-bo-table" class="table-bordered  ">
                                <thead>
                                <tr class="text-center" style="background-color:#eeeeee">
                                     <th style="width: 8%;font-size: 14px !important;">Ngày nhập<br> hệ thống</th>
                                    <th style="width: 8%;font-size: 14px !important;">Ngày CC/<br>ngăn chặn</th>
                                    <th style="width: 25%;font-size: 14px !important;">Các bên liên quan</th>
                                    <th style="width: 25%;font-size: 14px !important;">Nội dung tóm tắt/<br> giao dịch
                                    </th>
                                    <th style="width: 8%;font-size: 14px !important;">Số hợp đồng/<br> CV NC</th>
                                    <th style="width: 10%;font-size: 14px !important;">Tên hợp đồng/<br> giao dịch</th>
                                    <th style="width: 6%;font-size: 14px !important;">CCV/<br> Người nhập
                                    </th>
                                    <th style="width: 6%;font-size: 14px !important;">Văn Phòng</th>
                                    <th style="width: 6%;font-size: 14px !important;">Chặn/Giải tỏa</th>
                                    <th style="width: 6%;font-size: 14px !important;">Ghi chú</th>

                                </tr>
                                </thead>
                                <tbody>
                                @foreach($data as $val)
                                    @php
                                        $imgs = json_decode($val["picture"]);
                                    @endphp
                                    @if($val["ngan_chan"] == \App\Models\SuuTraModel::NORMAL)
                                        <tr class="khong_ngan_chan_mau_den">
                                            <td class="qktd"><b>{{ \Carbon\Carbon::parse($val["ngay_nhap"])->format('d/m/Y') }}</b></td>
                                            <td class="qktd">{{ \Carbon\Carbon::parse($val["ngay_cc"])->format('d/m/Y') }}</td>
                                            <td class="p-22 qktd">
                                                @if (strlen($val["duong_su"]) > 280)
                                                    {{ mb_substr($val["duong_su"], 0, 280, 'UTF-8') }}
                                                    <div class="modal fade bd-example-modal-sm"
                                                         id="more-content-md3-{{ $val["st_id"] }}" tabindex="-1"
                                                         role="dialog"
                                                         aria-labelledby="mySmallModalLabel" aria-hidden="false">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header qkmodel">
                                                                    <h5 class="modal-title qkmodel">Thông tin chi tiết
                                                                        giao dịch</h5>
                                                                </div>
                                                                <div class="modal-body" style="background-color: white">
                                                                    <p class="qktrang">
                                                                        {!!str_replace(';',"</br>",$val["duong_su"])!!}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span id="three-dot2-{{ $val["st_id"] }}">...</span>
                                                    <span id="{{ $val["st_id"] }}"
                                                          onclick="showinfo3('{{ $val["st_id"] }}')">
                                        <i id="search-icon2-{{ $val["st_id"] }}" class="fa fa-search-plus fa-2x  text-primary">
                                        </i>
                                    </span>
                                                @else
                                                    {{ $val["duong_su"] }}
                                                @endif
                                            </td>
                                            <td class="p-33 qktd">
                                                @if(strlen($val["texte"]) > 280)
                                                    {{mb_substr($val["texte"], 0, 280, 'UTF-8')}}
                                                    <div class="modal fade bd-example-modal-sm"
                                                         id="more-content-md-{{$val["st_id"]}}"
                                                         tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
                                                         aria-hidden="false">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header qkmodel">
                                                                    <h5 class="modal-title qkmodel">Thông tin chi tiết
                                                                        giao dịch</h5>
                                                                </div>
                                                                <div class="modal-body" style="background-color: white">
                                                                    <p class="qktrang">{!!str_replace(';',"</br>",$val["texte"])!!}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span id="three-dot-{{$val["st_id"]}}">...</span>
                                                    <span id="{{$val["st_id"]}}"
                                                          onclick="showinfo('{{$val["st_id"]}}')">
                                        <i id="search-icon-{{$val["st_id"]}}"
                                           class="fa fa-search-plus fa-2x  text-primary"></i>
                                    </span>
                                                @else
                                                    {{ $val["texte"] }}
                                                @endif
                                                <br>
                                                <span style="color: red">{{ $val["cancel_description"] }}</span>
                                            </td>
                                            <td class="qktd">{{ $val["so_hd"] }}</td>
                                            @if(is_array(json_decode($val["ten_hd"])))
                                                <td class="p-44 qktd">
                                                    @foreach(json_decode($val["ten_hd"]) as $value)
                                                        <span>{{ \App\VanBanModel::where('vb_id',$value)->first()->vb_nhan }}.</span>
                                                        <br>
                                                    @endforeach
                                                </td>
                                            @else
                                                <td class="qktd">
                                                    <span>{{ ($val["ten_hd"]) }}</span>
                                                </td>
                                            @endif
                                            @if($val["ma_phan_biet"])
{{--                                                <td class="qktd">{{ $val["first_name"] }}</td>--}}
{{--                                                <td class="qktd">{{ $val["cn_ten"] }}</td>--}}
{{--                                            @else--}}
                                                <td class="qktd">{{ $val["ccv_master"] }}</td>
                                                <td class="qktd">{{ $val["vp_master"] }}</td>
                                            @endif
                                            <td class="qktd">
                                                <div class="row">
                                                    @if($val["ma_phan_biet"]==\App\Models\SuuTraModel::CODE)
                                                        <span>Jdata</span>
                                                    @else
                                                        <span>DL khác</span>
                                                    @endif
                                                </div>
                                                @if($role=='phong-khac')
                                                    @if($val["ma_phan_biet"] == 'D' && $val["status"] == \App\Models\SuuTraModel::PREVENT)
                                                        <div class="row">chưa duyệt</div>
                                                    @else
                                                    @endif
                                                @else
                                                @endif
                                                <div class="row">
                                                    @if($val["picture"])
                                                        @if(is_array($imgs))
                                                            <a data-toggle="modal" data-target="#img-{{$val->st_id}}"
                                                   class="button button-circle button-mid button-primary">
                                                    <i class="fa fa-image"></i>
                                                </a>
                                                <div class="modal fade bd-example-modal-sm" id="img-{{$val->st_id}}" role="dialog"
                                                     aria-labelledby="modalLabeldanger">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-danger qkmodel">
                                                                <h4 class="modal-title qkmodel">Danh sách tập tin
                                                                </h4>
                                                            </div>
                                                            <div class="modal-body" style="background-color: white">
                                                                <table>
                                                                    <thead>
                                                                    <tr>
                                                                        <th>Tên tập tin</th>
                                                                        <th><i class="fa fa-cog"></i></th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                @foreach($imgs as $key=>$img)
                                                                        <tr>
                                                                            <td>
                                                                                <span>{{ json_decode($val["real_name"])[$key] }}</span></a>
                                                                            </td>
																			@php
																			$name=json_decode($val["real_name"])[$key];
																			@endphp
                                                                           @if($name)
                                                                            <td style="text-align: center">
                                                                                <a href="{{route('downloadImg',['img'=>$img,'name'=>$name])}}"><span><i class="fa fa-download"></i></span></a>
                                                                            </td>
																			@endif
                                                                        </tr>
                                                                @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="modal-footer" style="background-color: white">
                                                                <div class="form-inline">
                                                                    <a href="#" data-dismiss="modal"
                                                                       class="btn btn-warning">Đóng</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                        @endif
                                                    @else
                                                    @endif
                                                </div>
                                                @if($val["ma_phan_biet"])
                                                    @if($role=='admin'|| $role=='chuyen-vien-so'||$role=='truong-van-phong'||$role=='cong-chung-vien' || $role=='ke-toan')
                                                        <div class="row">
                                                            @if($role == 'ke-toan' && $val["vp"] == $id_vp)
                                                                <a href="{{ route('editSuutra',['id' => $val["st_id"]]) }}"
                                                                   class="button button-circle button-mid button-primary">
                                                                    <i class="fa fa-pencil-square-o"></i>
                                                                </a>
                                                            @endif
                                                            @if(\App\Http\Controllers\SuuTraController::checkEdit($val["created_at"]))
                                                                @if($role=='truong-van-phong'&& $val["vp"] == $id_vp||$val["ccv"]==Sentinel::getUser()->id)
                                                                    <a href="{{ route('editSuutra',['id' => $val["st_id"]]) }}"
                                                                       class="button button-circle button-mid button-primary">
                                                                        <i class="fa fa-pencil-square-o"></i>
                                                                    </a>
                                                                @endif
                                                            @endif
                                                            @if($val["status"] == \App\Models\SuuTraModel::PREVENT)
                                                                <a data-toggle="modal"
                                                                   data-target="#confirm-{{$val["st_id"]}}"
                                                                   class="btn btn-success">
                                                                    <i class="fa fa-check"></i>
                                                                </a>
                                                                <div class="modal fade" id="confirm-{{$val["st_id"]}}"
                                                                     role="dialog"
                                                                     aria-labelledby="modalLabeldanger">
                                                                    <div class="modal-dialog" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header bg-danger">
                                                                                <h4 class="modal-title"
                                                                                    id="modalLabeldanger">Chú ý!
                                                                                </h4>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <p>Duyệt hợp
                                                                                    đồng: {{$val["ten_hd"]}}</p>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <form
                                                                                    action="{{ route('duyetSuutra',['id' => $val["st_id"]]) }}"
                                                                                    method="get">
                                                                                    <div class="form-inline">
                                                                                        <button type="submit"
                                                                                                class="btn btn-danger">
                                                                                            Có,
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
											 @if($val->is_update == 1)
												 <td class="qktd">{{$val["note"]}}  
											 <a style="color: red" href="{{ route('suutralogIndex',['so_hd'=>$val->so_hd]) }}">
                                            => Xem chỉnh sửa
                                        </a> </td>

                                      
                                    @else
										<td></td>
                                    @endif

                                        </tr>
                                    @elseif($val["ngan_chan"] == \App\Models\SuuTraModel::PREVENT)
                                        <tr class="ngan_chan_mau_do text-danger">
                                            <td class="qktd"><b>{{ \Carbon\Carbon::parse($val["ngay_nhap"])->format('d/m/Y') }}</b></td>
                                            <td class="qktd">{{ \Carbon\Carbon::parse($val["ngay_cc"])->format('d/m/Y') }}</td>
                                            <td class="p-22 qktd">
                                                @if (strlen($val["duong_su"]) > 280)
                                                    {{ mb_substr($val["duong_su"], 0, 280, 'UTF-8') }}
                                                    <div class="modal fade bd-example-modal-sm"
                                                         id="more-content-md3-{{ $val["st_id"] }}" tabindex="-1"
                                                         role="dialog"
                                                         aria-labelledby="mySmallModalLabel" aria-hidden="false">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header qkmodel">
                                                                    <h5 class="modal-title qkmodel">Thông tin chi tiết
                                                                        ngăn chặn</h5>
                                                                </div>
                                                                <div class="modal-body" style="background-color: white">
                                                                    <p class="qkdo">
                                                                        {!!str_replace(';',"</br>",$val["duong_su"])!!}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span id="three-dot2-{{ $val["st_id"] }}">...</span>
                                                    <span id="{{ $val["st_id"] }}"
                                                          onclick="showinfo3('{{ $val["st_id"] }}')">
                                        <i id="search-icon2-{{ $val["st_id"] }}"
                                           class="fa fa-search-plus fa-2x  text-primary"></i>
                                    </span>
                                                @else
                                                    {{ $val["duong_su"] }}
                                                @endif
                                            </td>
                                            <td class="p-33 qktd">
                                                @if(strlen($val["texte"]) > 280){{mb_substr($val["texte"],0,280, "UTF-8")}}
                                                <div class="modal fade bd-example-modal-sm"
                                                     id="more-content-md-{{$val["st_id"]}}"
                                                     tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
                                                     aria-hidden="false">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header qkmodel">
                                                                <h5 class="modal-title qkmodel">
                                                                    Thông tin chi tiết ngăn chặn
                                                                </h5>
                                                            </div>
                                                            <div class="modal-body" style="background-color: white">
                                                                <p class="qkdo">{!!str_replace(';',"</br>",$val["texte"])!!}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <span id="three-dot-{{$val["st_id"]}}">...</span>
                                                <span id="{{$val["st_id"]}}" onclick="showinfo('{{$val["st_id"]}}')">
                                    <i id="search-icon-{{$val["st_id"]}}" class="fa fa-search-plus fa-2x  text-primary"></i>
                                </span>
                                                @else
                                                    {{$val["texte"]}}
                                                @endif
                                                <br>
                                                <span style="color: red">{{ $val["cancel_description"] }}</span>
                                            </td>
                                            <td class="qktd">{{ $val["so_hd"] }}</td>
                                            @if(is_array(json_decode($val["ten_hd"])))
                                                <td class="p-44 qktd">
                                                    @foreach(json_decode($val["ten_hd"]) as $value)
                                                        <span>{{ \App\VanBanModel::where('vb_id',$value)->first()->vb_nhan }}.</span>
                                                        <br>
                                                    @endforeach
                                                </td>
                                            @else
                                                <td class="p-44 qktd">
                                                    <span>{{ ($val["ten_hd"]) }}</span>
                                                </td>
                                            @endif
                                            @if($val["ma_phan_biet"])
{{--                                                <td class="qktd">{{ $val["first_name"] }}</td>--}}
{{--                                                <td class="qktd">{{ $val["cn_ten"] }}</td>--}}
{{--                                            @else--}}
                                                <td class="qktd">{{ $val["ccv_master"] }}</td>
                                                <td class="qktd">{{ $val["vp_master"] }}</td>
                                            @endif
                                            <td class="qktd">
                                                <div class="row">
                                                    @if($val["ma_phan_biet"]==\App\Models\SuuTraModel::CODE)
                                                        <span>Jdata</span>
                                                    @else
                                                        <span>DL khác</span>
                                                    @endif
                                                </div>
                                                <div class="row">Bị chặn</div>
                                                @if($role=='phong-khac')
                                                    @if($val["ma_phan_biet"] == 'D' && $val["status"] == \App\Models\SuuTraModel::PREVENT)
                                                        <div class="row">chưa duyệt</div>
                                                    @else
                                                    @endif
                                                @else
                                                @endif
                                                <div class="row">
                                                    @if($val["picture"])
                                                        @if(is_array($imgs))
                                                            <a data-toggle="modal" data-target="#img-{{$val->st_id}}"
                                                   class="button button-circle button-mid button-primary">
                                                    <i class="fa fa-image"></i>
                                                </a>
                                                <div class="modal fade bd-example-modal-sm" id="img-{{$val->st_id}}" role="dialog"
                                                     aria-labelledby="modalLabeldanger">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-danger qkmodel">
                                                                <h4 class="modal-title qkmodel">Danh sách tập tin
                                                                </h4>
                                                            </div>
                                                            <div class="modal-body" style="background-color: white">
                                                                <table>
                                                                    <thead>
                                                                    <tr>
                                                                        <th>Tên tập tin</th>
                                                                        <th><i class="fa fa-cog"></i></th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                     @foreach($imgs as $key=>$img)
                                                                        <tr>
                                                                            <td>
                                                                                <span>{{ json_decode($val["real_name"])[$key] }}</span></a>
                                                                            </td>
																			@php
																			$name=json_decode($val["real_name"])[$key];
																			@endphp
                                                                           @if($name)
                                                                            <td style="text-align: center">
                                                                                <a href="{{route('downloadImg',['img'=>$img,'name'=>$name])}}"><span><i class="fa fa-download"></i></span></a>
                                                                            </td>
																			@endif
                                                                        </tr>
                                                                @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="modal-footer" style="background-color: white">
                                                                <div class="form-inline">
                                                                    <a href="#" data-dismiss="modal"
                                                                       class="btn btn-warning">Đóng</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                        @endif
                                                    @endif
                                                </div>
                                                @if($val["ma_phan_biet"] == 'D')
                                                    @if($role=='admin'|| $role=='chuyen-vien-so')
                                                        <div class="row">
                                                            <a href="{{ route('editSuutra',['id' => $val["st_id"]]) }}"
                                                               class="button button-circle button-mid button-primary">
                                                                <i class="fa fa-pencil-square-o"></i>
                                                            </a>
                                                            @if($val["status"] == \App\Models\SuuTraModel::PREVENT)
                                                                <a data-toggle="modal"
                                                                   data-target="#confirm-{{$val["st_id"]}}"
                                                                   class="btn btn-success">
                                                                    <i class="fa fa-check"></i>
                                                                </a>
                                                                <div class="modal fade" id="confirm-{{$val["st_id"]}}"
                                                                     role="dialog"
                                                                     aria-labelledby="modalLabeldanger">
                                                                    <div class="modal-dialog" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header bg-danger">
                                                                                <h4 class="modal-title"
                                                                                    id="modalLabeldanger">Chú
                                                                                    ý!</h4>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <p>Duyệt hợp
                                                                                    đồng: {{$val["ten_hd"]}}</p>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <form
                                                                                    action="{{ route('duyetSuutra',['id' => $val["st_id"]]) }}"
                                                                                    method="get">
                                                                                    <div class="form-inline">
                                                                                        <button type="submit"
                                                                                                class="btn btn-danger">
                                                                                            Có,
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
                                        <tr class="canh_bao_mau_xanh qkxanh">
                                            <td class="qktd"><b>{{ \Carbon\Carbon::parse($val["ngay_nhap"])->format('d/m/Y') }}</b></td>
                                            <td class="qktd">{{ \Carbon\Carbon::parse($val["ngay_cc"])->format('d/m/Y') }}</td>
                                            <td class="p-22 qktd">
                                                @if (strlen($val["duong_su"]) > 280)
                                                    {{ mb_substr($val["duong_su"], 0, 280, 'UTF-8') }}
                                                    <div class="modal fade bd-example-modal-sm"
                                                         id="more-content-md3-{{ $val["st_id"] }}" tabindex="-1"
                                                         role="dialog"
                                                         aria-labelledby="mySmallModalLabel" aria-hidden="false">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header qkmodel">
                                                                    <h5 class="modal-title qkmodel">Thông tin chi tiết
                                                                        cảnh báo</h5>
                                                                </div>
                                                                <div class="modal-body" style="background-color: white">
                                                                    <p class="qkxanh">
                                                                        {!!str_replace(';',"</br>",$val["duong_su"])!!}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span id="three-dot2-{{ $val["st_id"] }}">...</span>
                                                    <span id="{{ $val["st_id"] }}"
                                                          onclick="showinfo3('{{ $val["st_id"] }}')">
                                        <i id="search-icon2-{{ $val["st_id"] }}" class="fa fa-search-plus fa-2x  text-primary">
                                        </i>
                                    </span>
                                                @else
                                                    {{ $val["duong_su"] }}
                                                @endif
                                            </td>
                                            <td class="p-33 qktd">
                                                @if(strlen($val["texte"]) > 280)
                                                    {{mb_substr($val["texte"], 0, 280, 'UTF-8')}}
                                                    <div class="modal fade bd-example-modal-sm"
                                                         id="more-content-md-{{$val["st_id"]}}"
                                                         tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
                                                         aria-hidden="false">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header qkmodel">
                                                                    <h5 class="modal-title qkmodel">Thông tin chi tiết
                                                                        cảnh báo</h5>
                                                                </div>
                                                                <div class="modal-body" style="background-color: white">
                                                                    <p class="qkxanh">{!!str_replace(';',"</br>",$val["texte"])!!}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span id="three-dot-{{$val["st_id"]}}">...</span>
                                                    <span id="{{$val["st_id"]}}"
                                                          onclick="showinfo('{{$val["st_id"]}}')">
                                        <i id="search-icon-{{$val["st_id"]}}"
                                           class="fa fa-search-plus fa-2x  text-primary"></i>
                                    </span>
                                                @else
                                                    {{$val["texte"]}}
                                                @endif
                                                <br>
                                                <span style="color: red">{{ $val["cancel_description"] }}</span>
                                            </td>
                                            <td class="qktd" style="font-size: 14px">{{ $val["so_hd"] }}</td>
                                            @if(is_array(json_decode($val["ten_hd"])))
                                                <td class="p-44 qktd">
                                                    @foreach(json_decode($val["ten_hd"]) as $value)
                                                        <span>{{ \App\VanBanModel::where('vb_id',$value)->first()->vb_nhan }}.</span>
                                                        <br>
                                                    @endforeach
                                                </td>
                                            @else
                                                <td class="qktd">
                                                    <span>{{ ($val["ten_hd"]) }}</span>
                                                </td>
                                            @endif
                                            @if($val["ma_phan_biet"])
{{--                                                <td class="qktd">{{ $val["first_name"] }}</td>--}}
{{--                                                <td class="qktd">{{ $val["cn_ten"] }}</td>--}}
{{--                                            @else--}}
                                                <td class="qktd">{{ $val["ccv_master"] }}</td>
                                                <td class="qktd">{{ $val["vp_master"] }}</td>
                                            @endif
                                            <td class="qktd">
                                                <div class="row">
                                                    @if($val["ma_phan_biet"]==\App\Models\SuuTraModel::CODE)
                                                        <span>Jdata</span>
                                                    @else
                                                        <span>DL khác</span>
                                                    @endif
                                                </div>
                                                <div class="row">
                                                    @if($val["ngan_chan"] == \App\Models\SuuTraModel::WARNING)
                                                        Cảnh báo
                                                    @endif
                                                </div>
                                                @if($role=='phong-khac')
                                                    @if($val["ma_phan_biet"] == 'D' && $val["status"] == \App\Models\SuuTraModel::PREVENT)
                                                        <div class="row">chưa duyệt</div>
                                                    @else
                                                    @endif
                                                @else
                                                @endif
                                                <div class="row">
                                                    @if($val["picture"])
                                                        @if(is_array($imgs))
                                                            <a data-toggle="modal" data-target="#img-{{$val->st_id}}"
                                                   class="button button-circle button-mid button-primary">
                                                    <i class="fa fa-image"></i>
                                                </a>
                                                <div class="modal fade bd-example-modal-sm" id="img-{{$val->st_id}}" role="dialog"
                                                     aria-labelledby="modalLabeldanger">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-danger qkmodel">
                                                                <h4 class="modal-title qkmodel">Danh sách tập tin
                                                                </h4>
                                                            </div>
                                                            <div class="modal-body" style="background-color: white">
                                                                <table>
                                                                    <thead>
                                                                    <tr>
                                                                        <th>Tên tập tin</th>
                                                                        <th><i class="fa fa-cog"></i></th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                      @foreach($imgs as $key=>$img)
                                                                        <tr>
                                                                            <td>
                                                                                <span>{{ json_decode($val["real_name"])[$key] }}</span></a>
                                                                            </td>
																			@php
																			$name=json_decode($val["real_name"])[$key];
																			@endphp
                                                                           @if($name)
                                                                            <td style="text-align: center">
                                                                                <a href="{{route('downloadImg',['img'=>$img,'name'=>$name])}}"><span><i class="fa fa-download"></i></span></a>
                                                                            </td>
																			@endif
                                                                        </tr>
                                                                @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="modal-footer" style="background-color: white">
                                                                <div class="form-inline">
                                                                    <a href="#" data-dismiss="modal"
                                                                       class="btn btn-warning">Đóng</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                        @endif
                                                    @endif
                                                </div>
                                                @if($val["ma_phan_biet"])
                                                    @if($role=='admin'|| $role=='chuyen-vien-so')
                                                        <div class="row">
                                                            <a href="{{ route('editSuutra',['id' => $val["st_id"]]) }}"
                                                               class="button button-circle button-mid button-primary">
                                                                <i class="fa fa-pencil-square-o"></i>
                                                            </a>
                                                            @if($val["status"] == \App\Models\SuuTraModel::PREVENT)
                                                                <a data-toggle="modal"
                                                                   data-target="#confirm-{{$val["st_id"]}}"
                                                                   class="btn btn-success">
                                                                    <i class="fa fa-check"></i>
                                                                </a>
                                                                <div class="modal fade" id="confirm-{{$val["st_id"]}}"
                                                                     role="dialog"
                                                                     aria-labelledby="modalLabeldanger">
                                                                    <div class="modal-dialog" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header bg-danger">
                                                                                <h4 class="modal-title"
                                                                                    id="modalLabeldanger">Chú
                                                                                    ý!</h4>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <p>Duyệt hợp
                                                                                    đồng: {{$val["ten_hd"]}}</p>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <form
                                                                                    action="{{ route('duyetSuutra',['id' => $val["st_id"]]) }}"
                                                                                    method="get">
                                                                                    <div class="form-inline">
                                                                                        <button type="submit"
                                                                                                class="btn btn-danger">
                                                                                            Có,
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
                      
                    </div>
  <div class="row">
                            <div class="col-sm-10">
                                {{$data->appends(request()->input())->links()}}
                            </div>
                            <div class="col-sm-2">
                                <p style="font-size: 16px;">Tổng số:
                                    <b>
                                        <span style="color: red">{{\App\Models\SuuTraModel::query()->count()}}</span>
                                    </b>
                                </p>
                            </div>
                        </div>
                </div>

                <!--Tab hồ tài sản-->
                <div class="tab-pane fade border-custom bctk-scrollable-list  in" id="ts-f">
                    <div  id="rwdts" style="">
                        <div class="">
                            <div class="col-md-7">
                                <form action="{{ route('indexOtherSuutra') }}" method="get" id="formSreachOther">
                                    <div class="col-md-5 search" style="display: none">
                                        <span class="fa fa-search fa-search-custom "></span>
                                        <input type="text" class="form-control" id="searchbox" name="coban"
                                               placeholder="Tìm kiếm đương sự"
                                               value="{{ $getcoban }}" autofocus>
                                 <span style="font-size: 12px;color: black"><b style="color: red">Gợi ý:</b> Nguyễn Văn A để tìm<span
                                                style="color: red"> hoặc</span> (kèm năm sinh hoặc CMND)</span>
                                    </div>
                                    <div class="col-md-9 search">
                                        <span class="fa fa-search fa-search-custom"></span>
                                        <input type="text" class="form-control" id="searchbox" name="nangcao"
                                               placeholder="Tìm kiếm nội dung giao dịch" value="{{ $getNangCao }}"
                                               autofocus>
                                        
                                    </div>
                                    <div class="col-md-3 search">
                                        <button class="btn btn-success btn1" type="submit" id="btnsearchOther">
                                            <i class="fa fa-search"></i>
                                            Tìm kiếm
                                        </button>
                                    </div>
                                    
                                </form>
                            </div>
                            <div class="col-md-5">
                                <a class="btn btn-primary btn2" onclick="import_file()">
                                    <i class="fa fa-plus"></i>
                                    IMPORT
                                </a>
                                <a id="btnprintOther" class="btn btn-warning">
                                    <i class="fa fa-print"></i>
                                    PRINT
                                </a>
								<a id="" class="btn btn-warning" href={{route("indexSuutraNew")}}>
                                    <i class="fa fa-certificate"></i>
                                    Dùng thử giao diện tra cứu mới
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            @if(request()->input('coban') == null && request()->input('nangcao') == null)
                                <a></a>
                            @else
                                <a>Có <span style="color : red; font-weight: bold;">{{$count}}</span> kết quả được tìm
                                    thấy. </a><span> Nội dung có màu <span style="color:black;background-color:black">###</span> là dữ liệu bình thường, màu <span style="color:#016639;background-color:#016639">###</span> là dữ liệu cảnh báo, màu <span style="color:red;background-color:red">###</span>  là ngăn chặn</span>
                            @endif
                        </div>
                        <div class="row " style="overflow-x: hidden;">
                            @php
                                $role = Sentinel::check()->user_roles()->first()->slug;
                                $id_vp=\App\Models\NhanVienModel::find(Sentinel::check()->id)->nv_vanphong;
                            @endphp
                            <table id="noi-bo-table-ts" class="table-bordered  ">
                                <thead>
                                <tr class="text-center" style="background-color:#eeeeee">
                                    <th style="width: 8%;font-size: 14px !important;">Ngày nhập<br> hệ thống</th>
                                    <th style="width: 8%;font-size: 14px !important;">Ngày CC/<br>ngăn chặn</th>
                                    <th style="width: 25%;font-size: 14px !important;">Các bên liên quan</th>
                                    <th style="width: 25%;font-size: 14px !important;">Nội dung tóm tắt/<br> giao dịch
                                    </th>
                                    <th style="width: 8%;font-size: 14px !important;">Số hợp đồng/<br> CV NC</th>
                                    <th style="width: 10%;font-size: 14px !important;">Tên hợp đồng/<br> giao dịch</th>
                                    <th style="width: 6%;font-size: 14px !important;">CCV/<br> Người nhập
                                    </th>
                                    <th style="width: 6%;font-size: 14px !important;">Văn Phòng</th>
                                    <th style="width: 6%;font-size: 14px !important;">Chặn/Giải tỏa</th>
                                    <th style="width: 6%;font-size: 14px !important;">Ghi chú</th>

                                </tr>
                                </thead>
                                <tbody>
                                @foreach($data as $val)
                                    {{--                    {{dd($data)}}--}}
                                    @php
                                        $imgs = json_decode($val["picture"]);
                                    @endphp
                                    @if($val["ngan_chan"] == \App\Models\SuuTraModel::NORMAL)
                                        <tr class="khong_ngan_chan_mau_den">
                                            <td class="qktd"><b>{{ \Carbon\Carbon::parse($val["ngay_nhap"])->format('d/m/Y') }}</b></td>
                                            <td class="qktd">{{ \Carbon\Carbon::parse($val["ngay_cc"])->format('d/m/Y') }}</td>
                                            <td class="p-22 qktd">
                                                @if (strlen($val["duong_su"]) > 280)
                                                    {{ mb_substr($val["duong_su"], 0, 280, 'UTF-8') }}
                                                    <div class="modal fade bd-example-modal-sm"
                                                         id="more-content-md-ts-3-{{ $val["st_id"] }}" tabindex="-1"
                                                         role="dialog"
                                                         aria-labelledby="mySmallModalLabel" aria-hidden="false">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header qkmodel">
                                                                    <h5 class="modal-title qkmodel">Thông tin chi tiết
                                                                        giao dịch</h5>
                                                                </div>
                                                                <div class="modal-body" style="background-color: white">
                                                                    <p class="qktrang">
                                                                        {!!str_replace(';',"</br>",$val["duong_su"])!!}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span id="three-dot2-{{ $val["st_id"] }}">...</span>
                                                    <span id="{{ $val["st_id"] }}"
                                                          onclick="showinfo_ts_3('{{ $val["st_id"] }}')">
                                        <i id="search-icon2-{{ $val["st_id"] }}" class="fa fa-search-plus fa-2x  text-primary">
                                        </i>
                                    </span>
                                                @else
                                                    {{ $val["duong_su"] }}
                                                @endif
                                            </td>
                                            <td class="p-33 qktd">
                                                @if(strlen($val["texte"]) > 280)
                                                    {{mb_substr($val["texte"], 0, 280, 'UTF-8')}}
                                                    <div class="modal fade bd-example-modal-sm"
                                                         id="more-content-md-ts-{{$val["st_id"]}}"
                                                         tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
                                                         aria-hidden="false">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header qkmodel">
                                                                    <h5 class="modal-title qkmodel">Thông tin chi tiết
                                                                        giao dịch</h5>
                                                                </div>
                                                                <div class="modal-body" style="background-color: white">
                                                                    <p class="qktrang">{!!str_replace(';',"</br>",$val["texte"])!!}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span id="three-dot-{{$val["st_id"]}}">...</span>
                                                    <span id="{{$val["st_id"]}}"
                                                          onclick="showinfo_ts_('{{$val["st_id"]}}')">
                                        <i id="search-icon-{{$val["st_id"]}}"
                                           class="fa fa-search-plus fa-2x  text-primary"></i>
                                    </span>
                                                @else
                                                    {{ $val["texte"] }}
                                                @endif
                                                <br>
                                                <span style="color: red">{{ $val["cancel_description"] }}</span>
                                            </td>
                                            <td class="qktd">{{ $val["so_hd"] }}</td>
                                            @if(is_array(json_decode($val["ten_hd"])))
                                                <td class="p-44 qktd">
                                                    @foreach(json_decode($val["ten_hd"]) as $value)
                                                        <span>{{ \App\VanBanModel::where('vb_id',$value)->first()->vb_nhan }}.</span>
                                                        <br>
                                                    @endforeach
                                                </td>
                                            @else
                                                <td class="qktd">
                                                    <span>{{ ($val["ten_hd"]) }}</span>
                                                </td>
                                            @endif
                                            @if($val["ma_phan_biet"])
{{--                                                <td class="qktd">{{ $val["first_name"] }}</td>--}}
{{--                                                <td class="qktd">{{ $val["cn_ten"] }}</td>--}}
{{--                                            @else--}}
                                                <td class="qktd">{{ $val["ccv_master"] }}</td>
                                                <td class="qktd">{{ $val["vp_master"] }}</td>
                                            @endif
                                            <td class="qktd">
                                                <div class="row">
                                                    @if($val["ma_phan_biet"]==\App\Models\SuuTraModel::CODE)
                                                        <span>Jdata</span>
                                                    @else
                                                        <span>DL khác</span>
                                                    @endif
                                                </div>
                                                @if($role=='phong-khac')
                                                    @if($val["ma_phan_biet"] == 'D' && $val["status"] == \App\Models\SuuTraModel::PREVENT)
                                                        <div class="row">chưa duyệt</div>
                                                    @else
                                                    @endif
                                                @else
                                                @endif
                                                <div class="row">
                                                    @if($val["picture"])
                                                        @if(is_array($imgs))
                                                <a data-toggle="modal" data-target="#img-{{$val->st_id}}"
                                                   class="button button-circle button-mid button-primary">
                                                    <i class="fa fa-image"></i>
                                                </a>
                                                <div class="modal fade bd-example-modal-sm" id="img-{{$val->st_id}}" role="dialog"
                                                     aria-labelledby="modalLabeldanger">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-danger qkmodel">
                                                                <h4 class="modal-title qkmodel">Danh sách tập tin
                                                                </h4>
                                                            </div>
                                                            <div class="modal-body" style="background-color: white">
                                                                <table>
                                                                    <thead>
                                                                    <tr>
                                                                        <th>Tên tập tin</th>
                                                                        <th><i class="fa fa-cog"></i></th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                     @foreach($imgs as $key=>$img)
                                                                        <tr>
                                                                            <td>
                                                                                <span>{{ json_decode($val["real_name"])[$key] }}</span></a>
                                                                            </td>
																			@php
																			$name=json_decode($val["real_name"])[$key];
																			@endphp
                                                                           @if($name)
                                                                            <td style="text-align: center">
                                                                                <a href="{{route('downloadImg',['img'=>$img,'name'=>$name])}}"><span><i class="fa fa-download"></i></span></a>
                                                                            </td>
																			@endif
                                                                        </tr>
                                                                @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="modal-footer" style="background-color: white">
                                                                <div class="form-inline">
                                                                    <a href="#" data-dismiss="modal"
                                                                       class="btn btn-warning">Đóng</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                        @endif
                                                    @else
                                                    @endif
                                                </div>
                                                @if($val["ma_phan_biet"])
                                                    @if($role=='admin'|| $role=='chuyen-vien-so'||$role=='truong-van-phong'||$role=='cong-chung-vien' || $role=='ke-toan')
                                                        <div class="row">
                                                            @if($role == 'ke-toan' && $val["vp"] == $id_vp)
                                                                <a href="{{ route('editSuutra',['id' => $val["st_id"]]) }}"
                                                                   class="button button-circle button-mid button-primary">
                                                                    <i class="fa fa-pencil-square-o"></i>
                                                                </a>
                                                            @endif
                                                            @if(\App\Http\Controllers\SuuTraController::checkEdit($val["created_at"]))
                                                                @if($role=='truong-van-phong'&& $val["vp"] == $id_vp||$val["ccv"]==Sentinel::getUser()->id)
                                                                    <a href="{{ route('editSuutra',['id' => $val["st_id"]]) }}"
                                                                       class="button button-circle button-mid button-primary">
                                                                        <i class="fa fa-pencil-square-o"></i>
                                                                    </a>
                                                                @endif
                                                            @endif
                                                            @if($val["status"] == \App\Models\SuuTraModel::PREVENT)
                                                                <a data-toggle="modal"
                                                                   data-target="#confirm-{{$val["st_id"]}}"
                                                                   class="btn btn-success">
                                                                    <i class="fa fa-check"></i>
                                                                </a>
                                                                <div class="modal fade" id="confirm-{{$val["st_id"]}}"
                                                                     role="dialog"
                                                                     aria-labelledby="modalLabeldanger">
                                                                    <div class="modal-dialog" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header bg-danger">
                                                                                <h4 class="modal-title"
                                                                                    id="modalLabeldanger">Chú ý!
                                                                                </h4>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <p>Duyệt hợp
                                                                                    đồng: {{$val["ten_hd"]}}</p>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <form
                                                                                    action="{{ route('duyetSuutra',['id' => $val["st_id"]]) }}"
                                                                                    method="get">
                                                                                    <div class="form-inline">
                                                                                        <button type="submit"
                                                                                                class="btn btn-danger">
                                                                                            Có,
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
											@if($val->is_update == 1)
												 <td class="qktd">{{$val["note"]}}  
											 <a style="color: red" href="{{ route('suutralogIndex',['so_hd'=>$val->so_hd]) }}">
                                            => Xem chỉnh sửa
                                        </a> </td>

                                      
                                    @else
										<td></td>
                                    @endif                                        </tr>
                                    @elseif($val["ngan_chan"] == \App\Models\SuuTraModel::PREVENT)
                                        <tr class="ngan_chan_mau_do text-danger">
                                            <td class="qktd"><b>{{ \Carbon\Carbon::parse($val["ngay_nhap"])->format('d/m/Y') }}</b></td>
                                            <td class="qktd">{{ \Carbon\Carbon::parse($val["ngay_cc"])->format('d/m/Y') }}</td>
                                            <td class="p-22 qktd">
                                                @if (strlen($val["duong_su"]) > 280)
                                                    {{ mb_substr($val["duong_su"], 0, 280, 'UTF-8') }}
                                                    <div class="modal fade bd-example-modal-sm"
                                                         id="more-content-md-ts-3-{{ $val["st_id"] }}" tabindex="-1"
                                                         role="dialog"
                                                         aria-labelledby="mySmallModalLabel" aria-hidden="false">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header qkmodel">
                                                                    <h5 class="modal-title qkmodel">Thông tin chi tiết
                                                                        ngăn chặn</h5>
                                                                </div>
                                                                <div class="modal-body" style="background-color: white">
                                                                    <p class="qkdo">
                                                                        {!!str_replace(';',"</br>",$val["duong_su"])!!}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span id="three-dot2-{{ $val["st_id"] }}">...</span>
                                                    <span id="{{ $val["st_id"] }}"
                                                          onclick="showinfo_ts_3('{{ $val["st_id"] }}')">
                                        <i id="search-icon2-{{ $val["st_id"] }}"
                                           class="fa fa-search-plus fa-2x  text-primary"></i>
                                    </span>
                                                @else
                                                    {{ $val["duong_su"] }}
                                                @endif
                                            </td>
                                            <td class="p-33 qktd">
                                                @if(strlen($val["texte"]) > 280){{mb_substr($val["texte"],0,280, "UTF-8")}}
                                                <div class="modal fade bd-example-modal-sm"
                                                     id="more-content-md-ts-{{$val["st_id"]}}"
                                                     tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
                                                     aria-hidden="false">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header qkmodel">
                                                                <h5 class="modal-title qkmodel">
                                                                    Thông tin chi tiết ngăn chặn
                                                                </h5>
                                                            </div>
                                                            <div class="modal-body" style="background-color: white">
                                                                <p class="qkdo">{!!str_replace(';',"</br>",$val["texte"])!!}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <span id="three-dot-{{$val["st_id"]}}">...</span>
                                                <span id="{{$val["st_id"]}}" onclick="showinfo_ts_('{{$val["st_id"]}}')">
                                    <i id="search-icon-{{$val["st_id"]}}" class="fa fa-search-plus fa-2x  text-primary"></i>
                                </span>
                                                @else
                                                    {{$val["texte"]}}
                                                @endif
                                                <br>
                                                <span style="color: red">{{ $val["cancel_description"] }}</span>
                                            </td>
                                            <td class="qktd">{{ $val["so_hd"] }}</td>
                                            @if(is_array(json_decode($val["ten_hd"])))
                                                <td class="p-44 qktd">
                                                    @foreach(json_decode($val["ten_hd"]) as $value)
                                                        <span>{{ \App\VanBanModel::where('vb_id',$value)->first()->vb_nhan }}.</span>
                                                        <br>
                                                    @endforeach
                                                </td>
                                            @else
                                                <td class="p-44 qktd">
                                                    <span>{{ ($val["ten_hd"]) }}</span>
                                                </td>
                                            @endif
                                            @if($val["ma_phan_biet"])

{{--                                                <td class="qktd">{{ $val["first_name"] }}</td>--}}
{{--                                                <td class="qktd">{{ $val["cn_ten"] }}</td>--}}
{{--                                            @else--}}
                                                <td class="qktd">{{ $val["ccv_master"] }}</td>
                                                <td class="qktd">{{ $val["vp_master"] }}</td>
                                            @endif
                                            <td class="qktd">
                                                <div class="row">
                                                    @if($val["ma_phan_biet"]==\App\Models\SuuTraModel::CODE)
                                                        <span>Jdata</span>
                                                    @else
                                                        <span>DL khác</span>
                                                    @endif
                                                </div>
                                                <div class="row">Bị chặn</div>
                                                @if($role=='phong-khac')
                                                    @if($val["ma_phan_biet"] == 'D' && $val["status"] == \App\Models\SuuTraModel::PREVENT)
                                                        <div class="row">chưa duyệt</div>
                                                    @else
                                                    @endif
                                                @else
                                                @endif
                                                <div class="row">
                                                    @if($val["picture"])
                                                        @if(is_array($imgs))
                                                           <a data-toggle="modal" data-target="#img-{{$val->st_id}}"
                                                   class="button button-circle button-mid button-primary">
                                                    <i class="fa fa-image"></i>
                                                </a>
                                                <div class="modal fade bd-example-modal-sm" id="img-{{$val->st_id}}" role="dialog"
                                                     aria-labelledby="modalLabeldanger">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-danger qkmodel">
                                                                <h4 class="modal-title qkmodel">Danh sách tập tin
                                                                </h4>
                                                            </div>
                                                            <div class="modal-body" style="background-color: white">
                                                                <table>
                                                                    <thead>
                                                                    <tr>
                                                                        <th>Tên tập tin</th>
                                                                        <th><i class="fa fa-cog"></i></th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                   @foreach($imgs as $key=>$img)
                                                                        <tr>
                                                                            <td>
                                                                                <span>{{ json_decode($val["real_name"])[$key] }}</span></a>
                                                                            </td>
																			@php
																			$name=json_decode($val["real_name"])[$key];
																			@endphp
                                                                           @if($name)
                                                                            <td style="text-align: center">
                                                                                <a href="{{route('downloadImg',['img'=>$img,'name'=>$name])}}"><span><i class="fa fa-download"></i></span></a>
                                                                            </td>
																			@endif
                                                                        </tr>
                                                                @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="modal-footer" style="background-color: white">
                                                                <div class="form-inline">
                                                                    <a href="#" data-dismiss="modal"
                                                                       class="btn btn-warning">Đóng</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                        @endif
                                                    @endif
                                                </div>
                                                @if($val["ma_phan_biet"] == 'D')
                                                    @if($role=='admin'|| $role=='chuyen-vien-so')
                                                        <div class="row">
                                                            <a href="{{ route('editSuutra',['id' => $val["st_id"]]) }}"
                                                               class="button button-circle button-mid button-primary">
                                                                <i class="fa fa-pencil-square-o"></i>
                                                            </a>
                                                            @if($val["status"] == \App\Models\SuuTraModel::PREVENT)
                                                                <a data-toggle="modal"
                                                                   data-target="#confirm-{{$val["st_id"]}}"
                                                                   class="btn btn-success">
                                                                    <i class="fa fa-check"></i>
                                                                </a>
                                                                <div class="modal fade" id="confirm-{{$val["st_id"]}}"
                                                                     role="dialog"
                                                                     aria-labelledby="modalLabeldanger">
                                                                    <div class="modal-dialog" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header bg-danger">
                                                                                <h4 class="modal-title"
                                                                                    id="modalLabeldanger">Chú
                                                                                    ý!</h4>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <p>Duyệt hợp
                                                                                    đồng: {{$val["ten_hd"]}}</p>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <form
                                                                                    action="{{ route('duyetSuutra',['id' => $val["st_id"]]) }}"
                                                                                    method="get">
                                                                                    <div class="form-inline">
                                                                                        <button type="submit"
                                                                                                class="btn btn-danger">
                                                                                            Có,
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
                                            <td class="qktd"><b>{{ \Carbon\Carbon::parse($val["ngay_nhap"])->format('d/m/Y') }}</b></td>
                                            <td class="qktd">{{ \Carbon\Carbon::parse($val["ngay_cc"])->format('d/m/Y') }}</td>
                                            <td class="p-22 qktd">
                                                @if (strlen($val["duong_su"]) > 280)
                                                    {{ mb_substr($val["duong_su"], 0, 280, 'UTF-8') }}
                                                    <div class="modal fade bd-example-modal-sm"
                                                         id="more-content-md-ts-3-{{ $val["st_id"] }}" tabindex="-1"
                                                         role="dialog"
                                                         aria-labelledby="mySmallModalLabel" aria-hidden="false">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header qkmodel">
                                                                    <h5 class="modal-title qkmodel">Thông tin chi tiết
                                                                        cảnh báo</h5>
                                                                </div>
                                                                <div class="modal-body" style="background-color: white">
                                                                    <p class="qkxanh">
                                                                        {!!str_replace(';',"</br>",$val["duong_su"])!!}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span id="three-dot2-{{ $val["st_id"] }}">...</span>
                                                    <span id="{{ $val["st_id"] }}"
                                                          onclick="showinfo_ts_3('{{ $val["st_id"] }}')">
                                        <i id="search-icon2-{{ $val["st_id"] }}" class="fa fa-search-plus fa-2x  text-primary">
                                        </i>
                                    </span>
                                                @else
                                                    {{ $val["duong_su"] }}
                                                @endif
                                            </td>
                                            <td class="p-33 qktd">
                                                @if(strlen($val["texte"]) > 280)
                                                    {{mb_substr($val["texte"], 0, 280, 'UTF-8')}}
                                                    <div class="modal fade bd-example-modal-sm"
                                                         id="more-content-md-ts-{{$val["st_id"]}}"
                                                         tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
                                                         aria-hidden="false">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header qkmodel">
                                                                    <h5 class="modal-title qkmodel">Thông tin chi tiết
                                                                        cảnh báo</h5>
                                                                </div>
                                                                <div class="modal-body" style="background-color: white">
                                                                    <p class="qkxanh">{!!str_replace(';',"</br>",$val["texte"])!!}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span id="three-dot-{{$val["st_id"]}}">...</span>
                                                    <span id="{{$val["st_id"]}}"
                                                          onclick="showinfo_ts_('{{$val["st_id"]}}')">
                                        <i id="search-icon-{{$val["st_id"]}}"
                                           class="fa fa-search-plus fa-2x  text-primary"></i>
                                    </span>
                                                @else
                                                    {{$val["texte"]}}
                                                @endif
                                                <br>
                                                <span style="color: red">{{ $val["cancel_description"] }}</span>
                                            </td>
                                            <td class="qktd" style="font-size: 14px">{{ $val["so_hd"] }}</td>
                                            @if(is_array(json_decode($val["ten_hd"])))
                                                <td class="p-44 qktd">
                                                    @foreach(json_decode($val["ten_hd"]) as $value)
                                                        <span>{{ \App\VanBanModel::where('vb_id',$value)->first()->vb_nhan }}.</span>
                                                        <br>
                                                    @endforeach
                                                </td>
                                            @else
                                                <td class="qktd">
                                                    <span>{{ ($val["ten_hd"]) }}</span>
                                                </td>
                                            @endif
                                            @if($val["ma_phan_biet"])
{{--                                                <td class="qktd">{{ $val["first_name"] }}</td>--}}
{{--                                                <td class="qktd">{{ $val["cn_ten"] }}</td>--}}
{{--                                            @else--}}
                                                <td class="qktd">{{ $val["ccv_master"] }}</td>
                                                <td class="qktd">{{ $val["vp_master"] }}</td>
                                            @endif
                                            <td class="qktd">
                                                <div class="row">
                                                    @if($val["ma_phan_biet"]==\App\Models\SuuTraModel::CODE)
                                                        <span>Jdata</span>
                                                    @else
                                                        <span>DL khác</span>
                                                    @endif
                                                </div>
                                                <div class="row">
                                                    @if($val["ngan_chan"] == \App\Models\SuuTraModel::WARNING)
                                                        Cảnh báo
                                                    @endif
                                                </div>
                                                @if($role=='phong-khac')
                                                    @if($val["ma_phan_biet"] == 'D' && $val["status"] == \App\Models\SuuTraModel::PREVENT)
                                                        <div class="row">chưa duyệt</div>
                                                    @else
                                                    @endif
                                                @else
                                                @endif
                                                <div class="row">
                                                    @if($val["picture"])
                                                        @if(is_array($imgs))
                                                          <a data-toggle="modal" data-target="#img-{{$val->st_id}}"
                                                   class="button button-circle button-mid button-primary">
                                                    <i class="fa fa-image"></i>
                                                </a>
                                                <div class="modal fade bd-example-modal-sm" id="img-{{$val->st_id}}" role="dialog"
                                                     aria-labelledby="modalLabeldanger">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-danger qkmodel">
                                                                <h4 class="modal-title qkmodel">Danh sách tập tin
                                                                </h4>
                                                            </div>
                                                            <div class="modal-body" style="background-color: white">
                                                                <table>
                                                                    <thead>
                                                                    <tr>
                                                                        <th>Tên tập tin</th>
                                                                        <th><i class="fa fa-cog"></i></th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                      @foreach($imgs as $key=>$img)
                                                                        <tr>
                                                                            <td>
                                                                                <span>{{ json_decode($val["real_name"])[$key] }}</span></a>
                                                                            </td>
																			@php
																			$name=json_decode($val["real_name"])[$key];
																			@endphp
                                                                           @if($name)
                                                                            <td style="text-align: center">
                                                                                <a href="{{route('downloadImg',['img'=>$img,'name'=>$name])}}"><span><i class="fa fa-download"></i></span></a>
                                                                            </td>
																			@endif
                                                                        </tr>
                                                                @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="modal-footer" style="background-color: white">
                                                                <div class="form-inline">
                                                                    <a href="#" data-dismiss="modal"
                                                                       class="btn btn-warning">Đóng</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                        @endif
                                                    @endif
                                                </div>
                                                @if($val["ma_phan_biet"])
                                                    @if($role=='admin'|| $role=='chuyen-vien-so')
                                                        <div class="row">
                                                            <a href="{{ route('editSuutra',['id' => $val["st_id"]]) }}"
                                                               class="button button-circle button-mid button-primary">
                                                                <i class="fa fa-pencil-square-o"></i>
                                                            </a>
                                                            @if($val["status"] == \App\Models\SuuTraModel::PREVENT)
                                                                <a data-toggle="modal"
                                                                   data-target="#confirm-{{$val["st_id"]}}"
                                                                   class="btn btn-success">
                                                                    <i class="fa fa-check"></i>
                                                                </a>
                                                                <div class="modal fade" id="confirm-{{$val["st_id"]}}"
                                                                     role="dialog"
                                                                     aria-labelledby="modalLabeldanger">
                                                                    <div class="modal-dialog" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header bg-danger">
                                                                                <h4 class="modal-title"
                                                                                    id="modalLabeldanger">Chú
                                                                                    ý!</h4>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <p>Duyệt hợp
                                                                                    đồng: {{$val["ten_hd"]}}</p>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <form
                                                                                    action="{{ route('duyetSuutra',['id' => $val["st_id"]]) }}"
                                                                                    method="get">
                                                                                    <div class="form-inline">
                                                                                        <button type="submit"
                                                                                                class="btn btn-danger">
                                                                                            Có,
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
                            <div class="col-sm-4">
                                {{$data->appends(request()->input())->links()}}
                            </div>
                            <div class="col-sm-8">
                                <p class="pull-right" style="font-size: 16px;">Tổng số:
                                    <b>
                                        <span style="color: red">{{\App\Models\SuuTraModel::query()->count()}}</span>
                                    </b>
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
		
		 
                <!--Tab hồ tài sản-->
                <div class="tab-pane fade border-custom bctk-scrollable-list  in" id="ts-f">
                    <div  id="rwdts" style="">
                        <div class="row">
                            <div class="col-md-8">
                                <form action="{{ route('indexOtherSuutra') }}" method="get" id="formSreachOther">
                                    <div class="col-md-5 search" style="display: none">
                                        <span class="fa fa-search fa-search-custom "></span>
                                        <input type="text" class="form-control" id="searchbox" name="coban"
                                               placeholder="Tìm kiếm đương sự"
                                               value="{{ $getcoban }}" autofocus>
                                 <span style="font-size: 12px;color: black"><b style="color: red">Gợi ý:</b> Nguyễn Văn A để tìm<span
                                                style="color: red"> hoặc</span> (kèm năm sinh hoặc CMND)</span>
                                    </div>
                                    <div class="col-md-9 search">
                                        <span class="fa fa-search fa-search-custom"></span>
                                        <input type="text" class="form-control" id="searchbox" name="nangcao"
                                               placeholder="Tìm kiếm nội dung giao dịch" value="{{ $getNangCao }}"
                                               autofocus>
                                        
                                    </div>
                                    <div class="col-md-3 search">
                                        <button class="btn btn-success btn1" type="submit" id="btnsearchOther">
                                            <i class="fa fa-search"></i>
                                            Tìm kiếm
                                        </button>
                                    </div>
                                    
                                </form>
                            </div>
                            <div class="col-md-4">
                                <a class="btn btn-primary btn2" onclick="import_file()">
                                    <i class="fa fa-plus"></i>
                                    IMPORT
                                </a>
                                <a id="btnprintOther" class="btn btn-warning">
                                    <i class="fa fa-print"></i>
                                    PRINT
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            @if(request()->input('coban') == null && request()->input('nangcao') == null)
                                <a></a>
                            @else
                                <a>Có <span style="color : red; font-weight: bold;">{{$count}}</span> kết quả được tìm
                                    thấy. </a><span> Nội dung có màu <span style="color:black;background-color:black">###</span> là dữ liệu bình thường, màu <span style="color:#016639;background-color:#016639">###</span> là dữ liệu cảnh báo, màu <span style="color:red;background-color:red">###</span>  là ngăn chặn</span>
                            @endif
                        </div>
                        <div class="row " style="overflow-x: hidden;">
                            @php
                                $role = Sentinel::check()->user_roles()->first()->slug;
                                $id_vp=\App\Models\NhanVienModel::find(Sentinel::check()->id)->nv_vanphong;
                            @endphp
                            <table id="noi-bo-table-ts" class="table-bordered  ">
                                <thead>
                                <tr class="text-center" style="background-color:#eeeeee">
                                    <th style="width: 8%;font-size: 14px !important;">Ngày nhập<br> hệ thống</th>
                                    <th style="width: 8%;font-size: 14px !important;">Ngày CC/<br>ngăn chặn</th>
                                    <th style="width: 25%;font-size: 14px !important;">Các bên liên quan</th>
                                    <th style="width: 25%;font-size: 14px !important;">Nội dung tóm tắt/<br> giao dịch
                                    </th>
                                    <th style="width: 8%;font-size: 14px !important;">Số hợp đồng/<br> CV NC</th>
                                    <th style="width: 10%;font-size: 14px !important;">Tên hợp đồng/<br> giao dịch</th>
                                    <th style="width: 6%;font-size: 14px !important;">CCV/<br> Người nhập
                                    </th>
                                    <th style="width: 6%;font-size: 14px !important;">Văn Phòng</th>
                                    <th style="width: 6%;font-size: 14px !important;">Chặn/Giải tỏa</th>
                                    <th style="width: 6%;font-size: 14px !important;">Ghi chú</th>

                                </tr>
                                </thead>
                                <tbody>
                                @foreach($data as $val)
                                    {{--                    {{dd($data)}}--}}
                                    @php
                                        $imgs = json_decode($val["picture"]);
                                    @endphp
                                    @if($val["ngan_chan"] == \App\Models\SuuTraModel::NORMAL)
                                        <tr class="khong_ngan_chan_mau_den">
                                            <td class="qktd"><b>{{ \Carbon\Carbon::parse($val["ngay_nhap"])->format('d/m/Y') }}</b></td>
                                            <td class="qktd">{{ \Carbon\Carbon::parse($val["ngay_cc"])->format('d/m/Y') }}</td>
                                            <td class="p-22 qktd">
                                                @if (strlen($val["duong_su"]) > 280)
                                                    {{ mb_substr($val["duong_su"], 0, 280, 'UTF-8') }}
                                                    <div class="modal fade bd-example-modal-sm"
                                                         id="more-content-md-ts-3-{{ $val["st_id"] }}" tabindex="-1"
                                                         role="dialog"
                                                         aria-labelledby="mySmallModalLabel" aria-hidden="false">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header qkmodel">
                                                                    <h5 class="modal-title qkmodel">Thông tin chi tiết
                                                                        giao dịch</h5>
                                                                </div>
                                                                <div class="modal-body" style="background-color: white">
                                                                    <p class="qktrang">
                                                                        {!!str_replace(';',"</br>",$val["duong_su"])!!}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span id="three-dot2-{{ $val["st_id"] }}">...</span>
                                                    <span id="{{ $val["st_id"] }}"
                                                          onclick="showinfo_ts_3('{{ $val["st_id"] }}')">
                                        <i id="search-icon2-{{ $val["st_id"] }}" class="fa fa-search-plus fa-2x  text-primary">
                                        </i>
                                    </span>
                                                @else
                                                    {{ $val["duong_su"] }}
                                                @endif
                                            </td>
                                            <td class="p-33 qktd">
                                                @if(strlen($val["texte"]) > 280)
                                                    {{mb_substr($val["texte"], 0, 280, 'UTF-8')}}
                                                    <div class="modal fade bd-example-modal-sm"
                                                         id="more-content-md-ts-{{$val["st_id"]}}"
                                                         tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
                                                         aria-hidden="false">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header qkmodel">
                                                                    <h5 class="modal-title qkmodel">Thông tin chi tiết
                                                                        giao dịch</h5>
                                                                </div>
                                                                <div class="modal-body" style="background-color: white">
                                                                    <p class="qktrang">{!!str_replace(';',"</br>",$val["texte"])!!}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span id="three-dot-{{$val["st_id"]}}">...</span>
                                                    <span id="{{$val["st_id"]}}"
                                                          onclick="showinfo_ts_('{{$val["st_id"]}}')">
                                        <i id="search-icon-{{$val["st_id"]}}"
                                           class="fa fa-search-plus fa-2x  text-primary"></i>
                                    </span>
                                                @else
                                                    {{ $val["texte"] }}
                                                @endif
                                                <br>
                                                <span style="color: red">{{ $val["cancel_description"] }}</span>
                                            </td>
                                            <td class="qktd">{{ $val["so_hd"] }}</td>
                                            @if(is_array(json_decode($val["ten_hd"])))
                                                <td class="p-44 qktd">
                                                    @foreach(json_decode($val["ten_hd"]) as $value)
                                                        <span>{{ \App\VanBanModel::where('vb_id',$value)->first()->vb_nhan }}.</span>
                                                        <br>
                                                    @endforeach
                                                </td>
                                            @else
                                                <td class="qktd">
                                                    <span>{{ ($val["ten_hd"]) }}</span>
                                                </td>
                                            @endif
                                            @if($val["ma_phan_biet"])
{{--                                                <td class="qktd">{{ $val["first_name"] }}</td>--}}
{{--                                                <td class="qktd">{{ $val["cn_ten"] }}</td>--}}
{{--                                            @else--}}
                                                <td class="qktd">{{ $val["ccv_master"] }}</td>
                                                <td class="qktd">{{ $val["vp_master"] }}</td>
                                            @endif
                                            <td class="qktd">
                                                <div class="row">
                                                    @if($val["ma_phan_biet"]==\App\Models\SuuTraModel::CODE)
                                                        <span>Jdata</span>
                                                    @else
                                                        <span>DL khác</span>
                                                    @endif
                                                </div>
                                                @if($role=='phong-khac')
                                                    @if($val["ma_phan_biet"] == 'D' && $val["status"] == \App\Models\SuuTraModel::PREVENT)
                                                        <div class="row">chưa duyệt</div>
                                                    @else
                                                    @endif
                                                @else
                                                @endif
                                                <div class="row">
                                                    @if($val["picture"])
                                                        @if(is_array($imgs))
                                                <a data-toggle="modal" data-target="#img-{{$val->st_id}}"
                                                   class="button button-circle button-mid button-primary">
                                                    <i class="fa fa-image"></i>
                                                </a>
                                                <div class="modal fade bd-example-modal-sm" id="img-{{$val->st_id}}" role="dialog"
                                                     aria-labelledby="modalLabeldanger">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-danger qkmodel">
                                                                <h4 class="modal-title qkmodel">Danh sách tập tin
                                                                </h4>
                                                            </div>
                                                            <div class="modal-body" style="background-color: white">
                                                                <table>
                                                                    <thead>
                                                                    <tr>
                                                                        <th>Tên tập tin</th>
                                                                        <th><i class="fa fa-cog"></i></th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                      @foreach($imgs as $key=>$img)
                                                                        <tr>
                                                                            <td>
                                                                                <span>{{ json_decode($val["real_name"])[$key] }}</span></a>
                                                                            </td>
																			@php
																			$name=json_decode($val["real_name"])[$key];
																			@endphp
                                                                           @if($name)
                                                                            <td style="text-align: center">
                                                                                <a href="{{route('downloadImg',['img'=>$img,'name'=>$name])}}"><span><i class="fa fa-download"></i></span></a>
                                                                            </td>
																			@endif
                                                                        </tr>
                                                                @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="modal-footer" style="background-color: white">
                                                                <div class="form-inline">
                                                                    <a href="#" data-dismiss="modal"
                                                                       class="btn btn-warning">Đóng</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                        @endif
                                                    @else
                                                    @endif
                                                </div>
                                                @if($val["ma_phan_biet"])
                                                    @if($role=='admin'|| $role=='chuyen-vien-so'||$role=='truong-van-phong'||$role=='cong-chung-vien' || $role=='ke-toan')
                                                        <div class="row">
                                                            @if($role == 'ke-toan' && $val["vp"] == $id_vp)
                                                                <a href="{{ route('editSuutra',['id' => $val["st_id"]]) }}"
                                                                   class="button button-circle button-mid button-primary">
                                                                    <i class="fa fa-pencil-square-o"></i>
                                                                </a>
                                                            @endif
                                                            @if(\App\Http\Controllers\SuuTraController::checkEdit($val["created_at"]))
                                                                @if($role=='truong-van-phong'&& $val["vp"] == $id_vp||$val["ccv"]==Sentinel::getUser()->id)
                                                                    <a href="{{ route('editSuutra',['id' => $val["st_id"]]) }}"
                                                                       class="button button-circle button-mid button-primary">
                                                                        <i class="fa fa-pencil-square-o"></i>
                                                                    </a>
                                                                @endif
                                                            @endif
                                                            @if($val["status"] == \App\Models\SuuTraModel::PREVENT)
                                                                <a data-toggle="modal"
                                                                   data-target="#confirm-{{$val["st_id"]}}"
                                                                   class="btn btn-success">
                                                                    <i class="fa fa-check"></i>
                                                                </a>
                                                                <div class="modal fade" id="confirm-{{$val["st_id"]}}"
                                                                     role="dialog"
                                                                     aria-labelledby="modalLabeldanger">
                                                                    <div class="modal-dialog" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header bg-danger">
                                                                                <h4 class="modal-title"
                                                                                    id="modalLabeldanger">Chú ý!
                                                                                </h4>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <p>Duyệt hợp
                                                                                    đồng: {{$val["ten_hd"]}}</p>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <form
                                                                                    action="{{ route('duyetSuutra',['id' => $val["st_id"]]) }}"
                                                                                    method="get">
                                                                                    <div class="form-inline">
                                                                                        <button type="submit"
                                                                                                class="btn btn-danger">
                                                                                            Có,
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
											@if($val->is_update == 1)
												 <td class="qktd">{{$val["note"]}}  
											 <a style="color: red" href="{{ route('suutralogIndex',['so_hd'=>$val->so_hd]) }}">
                                            => Xem chỉnh sửa
                                        </a> </td>

                                      
                                    @else
										<td></td>
                                    @endif                                        </tr>
                                    @elseif($val["ngan_chan"] == \App\Models\SuuTraModel::PREVENT)
                                        <tr class="ngan_chan_mau_do text-danger">
                                            <td class="qktd"><b>{{ \Carbon\Carbon::parse($val["ngay_nhap"])->format('d/m/Y') }}</b></td>
                                            <td class="qktd">{{ \Carbon\Carbon::parse($val["ngay_cc"])->format('d/m/Y') }}</td>
                                            <td class="p-22 qktd">
                                                @if (strlen($val["duong_su"]) > 280)
                                                    {{ mb_substr($val["duong_su"], 0, 280, 'UTF-8') }}
                                                    <div class="modal fade bd-example-modal-sm"
                                                         id="more-content-md-ts-3-{{ $val["st_id"] }}" tabindex="-1"
                                                         role="dialog"
                                                         aria-labelledby="mySmallModalLabel" aria-hidden="false">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header qkmodel">
                                                                    <h5 class="modal-title qkmodel">Thông tin chi tiết
                                                                        ngăn chặn</h5>
                                                                </div>
                                                                <div class="modal-body" style="background-color: white">
                                                                    <p class="qkdo">
                                                                        {!!str_replace(';',"</br>",$val["duong_su"])!!}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span id="three-dot2-{{ $val["st_id"] }}">...</span>
                                                    <span id="{{ $val["st_id"] }}"
                                                          onclick="showinfo_ts_3('{{ $val["st_id"] }}')">
                                        <i id="search-icon2-{{ $val["st_id"] }}"
                                           class="fa fa-search-plus fa-2x  text-primary"></i>
                                    </span>
                                                @else
                                                    {{ $val["duong_su"] }}
                                                @endif
                                            </td>
                                            <td class="p-33 qktd">
                                                @if(strlen($val["texte"]) > 280){{mb_substr($val["texte"],0,280, "UTF-8")}}
                                                <div class="modal fade bd-example-modal-sm"
                                                     id="more-content-md-ts-{{$val["st_id"]}}"
                                                     tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
                                                     aria-hidden="false">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header qkmodel">
                                                                <h5 class="modal-title qkmodel">
                                                                    Thông tin chi tiết ngăn chặn
                                                                </h5>
                                                            </div>
                                                            <div class="modal-body" style="background-color: white">
                                                                <p class="qkdo">{!!str_replace(';',"</br>",$val["texte"])!!}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <span id="three-dot-{{$val["st_id"]}}">...</span>
                                                <span id="{{$val["st_id"]}}" onclick="showinfo_ts_('{{$val["st_id"]}}')">
                                    <i id="search-icon-{{$val["st_id"]}}" class="fa fa-search-plus fa-2x  text-primary"></i>
                                </span>
                                                @else
                                                    {{$val["texte"]}}
                                                @endif
                                                <br>
                                                <span style="color: red">{{ $val["cancel_description"] }}</span>
                                            </td>
                                            <td class="qktd">{{ $val["so_hd"] }}</td>
                                            @if(is_array(json_decode($val["ten_hd"])))
                                                <td class="p-44 qktd">
                                                    @foreach(json_decode($val["ten_hd"]) as $value)
                                                        <span>{{ \App\VanBanModel::where('vb_id',$value)->first()->vb_nhan }}.</span>
                                                        <br>
                                                    @endforeach
                                                </td>
                                            @else
                                                <td class="p-44 qktd">
                                                    <span>{{ ($val["ten_hd"]) }}</span>
                                                </td>
                                            @endif
                                            @if($val["ma_phan_biet"])

{{--                                                <td class="qktd">{{ $val["first_name"] }}</td>--}}
{{--                                                <td class="qktd">{{ $val["cn_ten"] }}</td>--}}
{{--                                            @else--}}
                                                <td class="qktd">{{ $val["ccv_master"] }}</td>
                                                <td class="qktd">{{ $val["vp_master"] }}</td>
                                            @endif
                                            <td class="qktd">
                                                <div class="row">
                                                    @if($val["ma_phan_biet"]==\App\Models\SuuTraModel::CODE)
                                                        <span>Jdata</span>
                                                    @else
                                                        <span>DL khác</span>
                                                    @endif
                                                </div>
                                                <div class="row">Bị chặn</div>
                                                @if($role=='phong-khac')
                                                    @if($val["ma_phan_biet"] == 'D' && $val["status"] == \App\Models\SuuTraModel::PREVENT)
                                                        <div class="row">chưa duyệt</div>
                                                    @else
                                                    @endif
                                                @else
                                                @endif
                                                <div class="row">
                                                    @if($val["picture"])
                                                        @if(is_array($imgs))
                                                           <a data-toggle="modal" data-target="#img-{{$val->st_id}}"
                                                   class="button button-circle button-mid button-primary">
                                                    <i class="fa fa-image"></i>
                                                </a>
                                                <div class="modal fade bd-example-modal-sm" id="img-{{$val->st_id}}" role="dialog"
                                                     aria-labelledby="modalLabeldanger">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-danger qkmodel">
                                                                <h4 class="modal-title qkmodel">Danh sách tập tin
                                                                </h4>
                                                            </div>
                                                            <div class="modal-body" style="background-color: white">
                                                                <table>
                                                                    <thead>
                                                                    <tr>
                                                                        <th>Tên tập tin</th>
                                                                        <th><i class="fa fa-cog"></i></th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                      @foreach($imgs as $key=>$img)
                                                                        <tr>
                                                                            <td>
                                                                                <span>{{ json_decode($val["real_name"])[$key] }}</span></a>
                                                                            </td>
																			@php
																			$name=json_decode($val["real_name"])[$key];
																			@endphp
                                                                           @if($name)
                                                                            <td style="text-align: center">
                                                                                <a href="{{route('downloadImg',['img'=>$img,'name'=>$name])}}"><span><i class="fa fa-download"></i></span></a>
                                                                            </td>
																			@endif
                                                                        </tr>
                                                                @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="modal-footer" style="background-color: white">
                                                                <div class="form-inline">
                                                                    <a href="#" data-dismiss="modal"
                                                                       class="btn btn-warning">Đóng</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                        @endif
                                                    @endif
                                                </div>
                                                @if($val["ma_phan_biet"] == 'D')
                                                    @if($role=='admin'|| $role=='chuyen-vien-so')
                                                        <div class="row">
                                                            <a href="{{ route('editSuutra',['id' => $val["st_id"]]) }}"
                                                               class="button button-circle button-mid button-primary">
                                                                <i class="fa fa-pencil-square-o"></i>
                                                            </a>
                                                            @if($val["status"] == \App\Models\SuuTraModel::PREVENT)
                                                                <a data-toggle="modal"
                                                                   data-target="#confirm-{{$val["st_id"]}}"
                                                                   class="btn btn-success">
                                                                    <i class="fa fa-check"></i>
                                                                </a>
                                                                <div class="modal fade" id="confirm-{{$val["st_id"]}}"
                                                                     role="dialog"
                                                                     aria-labelledby="modalLabeldanger">
                                                                    <div class="modal-dialog" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header bg-danger">
                                                                                <h4 class="modal-title"
                                                                                    id="modalLabeldanger">Chú
                                                                                    ý!</h4>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <p>Duyệt hợp
                                                                                    đồng: {{$val["ten_hd"]}}</p>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <form
                                                                                    action="{{ route('duyetSuutra',['id' => $val["st_id"]]) }}"
                                                                                    method="get">
                                                                                    <div class="form-inline">
                                                                                        <button type="submit"
                                                                                                class="btn btn-danger">
                                                                                            Có,
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
                                            <td class="qktd"><b>{{ \Carbon\Carbon::parse($val["ngay_nhap"])->format('d/m/Y') }}</b></td>
                                            <td class="qktd">{{ \Carbon\Carbon::parse($val["ngay_cc"])->format('d/m/Y') }}</td>
                                            <td class="p-22 qktd">
                                                @if (strlen($val["duong_su"]) > 280)
                                                    {{ mb_substr($val["duong_su"], 0, 280, 'UTF-8') }}
                                                    <div class="modal fade bd-example-modal-sm"
                                                         id="more-content-md-ts-3-{{ $val["st_id"] }}" tabindex="-1"
                                                         role="dialog"
                                                         aria-labelledby="mySmallModalLabel" aria-hidden="false">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header qkmodel">
                                                                    <h5 class="modal-title qkmodel">Thông tin chi tiết
                                                                        cảnh báo</h5>
                                                                </div>
                                                                <div class="modal-body" style="background-color: white">
                                                                    <p class="qkxanh">
                                                                        {!!str_replace(';',"</br>",$val["duong_su"])!!}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span id="three-dot2-{{ $val["st_id"] }}">...</span>
                                                    <span id="{{ $val["st_id"] }}"
                                                          onclick="showinfo_ts_3('{{ $val["st_id"] }}')">
                                        <i id="search-icon2-{{ $val["st_id"] }}" class="fa fa-search-plus fa-2x  text-primary">
                                        </i>
                                    </span>
                                                @else
                                                    {{ $val["duong_su"] }}
                                                @endif
                                            </td>
                                            <td class="p-33 qktd">
                                                @if(strlen($val["texte"]) > 280)
                                                    {{mb_substr($val["texte"], 0, 280, 'UTF-8')}}
                                                    <div class="modal fade bd-example-modal-sm"
                                                         id="more-content-md-ts-{{$val["st_id"]}}"
                                                         tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
                                                         aria-hidden="false">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header qkmodel">
                                                                    <h5 class="modal-title qkmodel">Thông tin chi tiết
                                                                        cảnh báo</h5>
                                                                </div>
                                                                <div class="modal-body" style="background-color: white">
                                                                    <p class="qkxanh">{!!str_replace(';',"</br>",$val["texte"])!!}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span id="three-dot-{{$val["st_id"]}}">...</span>
                                                    <span id="{{$val["st_id"]}}"
                                                          onclick="showinfo_ts_('{{$val["st_id"]}}')">
                                        <i id="search-icon-{{$val["st_id"]}}"
                                           class="fa fa-search-plus fa-2x  text-primary"></i>
                                    </span>
                                                @else
                                                    {{$val["texte"]}}
                                                @endif
                                                <br>
                                                <span style="color: red">{{ $val["cancel_description"] }}</span>
                                            </td>
                                            <td class="qktd" style="font-size: 14px">{{ $val["so_hd"] }}</td>
                                            @if(is_array(json_decode($val["ten_hd"])))
                                                <td class="p-44 qktd">
                                                    @foreach(json_decode($val["ten_hd"]) as $value)
                                                        <span>{{ \App\VanBanModel::where('vb_id',$value)->first()->vb_nhan }}.</span>
                                                        <br>
                                                    @endforeach
                                                </td>
                                            @else
                                                <td class="qktd">
                                                    <span>{{ ($val["ten_hd"]) }}</span>
                                                </td>
                                            @endif
                                            @if($val["ma_phan_biet"])
{{--                                                <td class="qktd">{{ $val["first_name"] }}</td>--}}
{{--                                                <td class="qktd">{{ $val["cn_ten"] }}</td>--}}
{{--                                            @else--}}
                                                <td class="qktd">{{ $val["ccv_master"] }}</td>
                                                <td class="qktd">{{ $val["vp_master"] }}</td>
                                            @endif
                                            <td class="qktd">
                                                <div class="row">
                                                    @if($val["ma_phan_biet"]==\App\Models\SuuTraModel::CODE)
                                                        <span>Jdata</span>
                                                    @else
                                                        <span>DL khác</span>
                                                    @endif
                                                </div>
                                                <div class="row">
                                                    @if($val["ngan_chan"] == \App\Models\SuuTraModel::WARNING)
                                                        Cảnh báo
                                                    @endif
                                                </div>
                                                @if($role=='phong-khac')
                                                    @if($val["ma_phan_biet"] == 'D' && $val["status"] == \App\Models\SuuTraModel::PREVENT)
                                                        <div class="row">chưa duyệt</div>
                                                    @else
                                                    @endif
                                                @else
                                                @endif
                                                <div class="row">
                                                    @if($val["picture"])
                                                        @if(is_array($imgs))
                                                          <a data-toggle="modal" data-target="#img-{{$val->st_id}}"
                                                   class="button button-circle button-mid button-primary">
                                                    <i class="fa fa-image"></i>
                                                </a>
                                                <div class="modal fade bd-example-modal-sm" id="img-{{$val->st_id}}" role="dialog"
                                                     aria-labelledby="modalLabeldanger">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-danger qkmodel">
                                                                <h4 class="modal-title qkmodel">Danh sách tập tin
                                                                </h4>
                                                            </div>
                                                            <div class="modal-body" style="background-color: white">
                                                                <table>
                                                                    <thead>
                                                                    <tr>
                                                                        <th>Tên tập tin</th>
                                                                        <th><i class="fa fa-cog"></i></th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                      @foreach($imgs as $key=>$img)
                                                                        <tr>
                                                                            <td>
                                                                                <span>{{ json_decode($val["real_name"])[$key] }}</span></a>
                                                                            </td>
																			@php
																			$name=json_decode($val["real_name"])[$key];
																			@endphp
                                                                           @if($name)
                                                                            <td style="text-align: center">
                                                                                <a href="{{route('downloadImg',['img'=>$img,'name'=>$name])}}"><span><i class="fa fa-download"></i></span></a>
                                                                            </td>
																			@endif
                                                                        </tr>
                                                                @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="modal-footer" style="background-color: white">
                                                                <div class="form-inline">
                                                                    <a href="#" data-dismiss="modal"
                                                                       class="btn btn-warning">Đóng</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                        @endif
                                                    @endif
                                                </div>
                                                @if($val["ma_phan_biet"])
                                                    @if($role=='admin'|| $role=='chuyen-vien-so')
                                                        <div class="row">
                                                            <a href="{{ route('editSuutra',['id' => $val["st_id"]]) }}"
                                                               class="button button-circle button-mid button-primary">
                                                                <i class="fa fa-pencil-square-o"></i>
                                                            </a>
                                                            @if($val["status"] == \App\Models\SuuTraModel::PREVENT)
                                                                <a data-toggle="modal"
                                                                   data-target="#confirm-{{$val["st_id"]}}"
                                                                   class="btn btn-success">
                                                                    <i class="fa fa-check"></i>
                                                                </a>
                                                                <div class="modal fade" id="confirm-{{$val["st_id"]}}"
                                                                     role="dialog"
                                                                     aria-labelledby="modalLabeldanger">
                                                                    <div class="modal-dialog" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header bg-danger">
                                                                                <h4 class="modal-title"
                                                                                    id="modalLabeldanger">Chú
                                                                                    ý!</h4>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <p>Duyệt hợp
                                                                                    đồng: {{$val["ten_hd"]}}</p>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <form
                                                                                    action="{{ route('duyetSuutra',['id' => $val["st_id"]]) }}"
                                                                                    method="get">
                                                                                    <div class="form-inline">
                                                                                        <button type="submit"
                                                                                                class="btn btn-danger">
                                                                                            Có,
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
                            <div class="col-sm-4">
                                {{$data->appends(request()->input())->links()}}
                            </div>
                            <div class="col-sm-8">
                                <p class="pull-right" style="font-size: 16px;">Tổng số:
                                    <b>
                                        <span style="color: red">{{\App\Models\SuuTraModel::query()->count()}}</span>
                                    </b>
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
	<div class="tab-pane fade border-custom bctk-scrollable-list  in" id="ts-cb">
                    <div class="row" id="rwdcb" style="">
                        <div class="row">
                            <div class="col-md-9">
                                <form action="{{ route('indexAdvancedSuutra') }}" method="get" id="formSreachAdvanced">
                                   
                                   <div class="col-md-5 search">
                                        <span class="fa fa-search fa-search-custom "></span>
                                        <input type="text" class="form-control" id="searchbox" name="coban"
                                               placeholder="Tìm kiếm các bên liên quan"
                                               value="{{ $getcoban }}" autofocus>
                                      <span style="font-size: 12px;color: black"><b style="color: red">Gợi ý:</b> Nguyễn Văn A để tìm<span
                                                style="color: red"> hoặc</span> (kèm năm sinh hoặc CMND)</span>
                                    </div>
                                    <div class="col-md-5 search">
                                        <span class="fa fa-search fa-search-custom"></span>
                                        <input type="text" class="form-control" id="searchbox1" name="nangcao"
                                               placeholder="Tìm kiếm nội dung giao dịch" value="{{ $getNangCao }}"
                                               autofocus>
                                        
                                    </div>
                                    <div class="col-md-3 search">
                                        <button class="btn btn-success btn1" type="submit" id="btnsearchAdanced">
                                            <i class="fa fa-search"></i>
                                            Tìm kiếm
                                        </button>
                                    </div>
                                    
                                </form>
                            </div>
                            <div class="col-md-3">
                                <a class="btn btn-primary btn2" onclick="import_file()">
                                    <i class="fa fa-plus"></i>
                                    IMPORT
                                </a>
                                <a id="btnprintOther" class="btn btn-warning">
                                    <i class="fa fa-print"></i>
                                    PRINT
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            @if(request()->input('coban') == null && request()->input('nangcao') == null)
                                <a></a>
                            @else
                                <a>Có <span style="color : red; font-weight: bold;">{{$count}}</span> kết quả được tìm
                                    thấy.</a>
                            @endif
                        </div>
                        <div class="row " style="overflow-x: hidden;">
                            @php
                                $role = Sentinel::check()->user_roles()->first()->slug;
                                $id_vp=\App\Models\NhanVienModel::find(Sentinel::check()->id)->nv_vanphong;
                            @endphp
                            <table id="noi-bo-table-ts" class="table-bordered  ">
                                <thead>
                                <tr class="text-center" style="background-color:#eeeeee">
                               <th style="width: 8%;font-size: 14px !important;">Ngày nhập<br> hệ thống</th>
                                    <th style="width: 8%;font-size: 14px !important;">Ngày CC/<br>ngăn chặn</th>
                                    <th style="width: 25%;font-size: 14px !important;">Các bên liên quan</th>
                                    <th style="width: 25%;font-size: 14px !important;">Nội dung tóm tắt/<br> giao dịch
                                    </th>
                                    <th style="width: 8%;font-size: 14px !important;">Số hợp đồng/<br> CV NC</th>
                                    <th style="width: 10%;font-size: 14px !important;">Tên hợp đồng/<br> giao dịch</th>
                                    <th style="width: 6%;font-size: 14px !important;">CCV/<br> Người nhập
                                    </th>
                                    <th style="width: 6%;font-size: 14px !important;">Văn Phòng</th>
                                    <th style="width: 6%;font-size: 14px !important;">Chặn/Giải tỏa</th>
                                    <th style="width: 6%;font-size: 14px !important;">Ghi chú</th>

                                </tr>
                                </thead>
                                <tbody>
                                @foreach($data as $val)
                                    {{--                    {{dd($data)}}--}}
                                    @php
                                        $imgs = json_decode($val["picture"]);
                                    @endphp
                                    @if($val["ngan_chan"] == \App\Models\SuuTraModel::NORMAL)
                                        <tr class="khong_ngan_chan_mau_den">
                                            <td class="qktd"><b>{{ \Carbon\Carbon::parse($val["ngay_nhap"])->format('d/m/Y') }}</b></td>
                                            <td class="qktd">{{ \Carbon\Carbon::parse($val["ngay_cc"])->format('d/m/Y') }}</td>
                                            <td class="p-22 qktd">
                                                @if (strlen($val["duong_su"]) > 280)
                                                    {{ mb_substr($val["duong_su"], 0, 280, 'UTF-8') }}
                                                    <div class="modal fade bd-example-modal-sm"
                                                         id="more-content-md-ad-3-{{ $val["st_id"] }}" tabindex="-1"
                                                         role="dialog"
                                                         aria-labelledby="mySmallModalLabel" aria-hidden="false">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header qkmodel">
                                                                    <h5 class="modal-title qkmodel">Thông tin chi tiết
                                                                        giao dịch</h5>
                                                                </div>
                                                                <div class="modal-body" style="background-color: white">
                                                                    <p class="qktrang">
                                                                        {!!str_replace(';',"</br>",$val["duong_su"])!!}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span id="three-dot2-{{ $val["st_id"] }}">...</span>
                                                    <span id="{{ $val["st_id"] }}"
                                                          onclick="showinfo_ad_3('{{ $val["st_id"] }}')">
                                        <i id="search-icon2-{{ $val["st_id"] }}" class="fa fa-search-plus fa-2x  text-primary">
                                        </i>
                                    </span>
                                                @else
                                                    {{ $val["duong_su"] }}
                                                @endif
                                            </td>
                                            <td class="p-33 qktd">
                                                @if(strlen($val["texte"]) > 280)
                                                    {{mb_substr($val["texte"], 0, 280, 'UTF-8')}}
                                                    <div class="modal fade bd-example-modal-sm"
                                                         id="more-content-md-ad-{{$val["st_id"]}}"
                                                         tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
                                                         aria-hidden="false">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header qkmodel">
                                                                    <h5 class="modal-title qkmodel">Thông tin chi tiết
                                                                        giao dịch</h5>
                                                                </div>
                                                                <div class="modal-body" style="background-color: white">
                                                                    <p class="qktrang">{!!str_replace(';',"</br>",$val["texte"])!!}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span id="three-dot-{{$val["st_id"]}}">...</span>
                                                    <span id="{{$val["st_id"]}}"
                                                          onclick="showinfo_ad_('{{$val["st_id"]}}')">
                                        <i id="search-icon-{{$val["st_id"]}}"
                                           class="fa fa-search-plus fa-2x  text-primary"></i>
                                    </span>
                                                @else
                                                    {{ $val["texte"] }}
                                                @endif
                                                <br>
                                                <span style="color: red">{{ $val["cancel_description"] }}</span>
                                            </td>
                                            <td class="qktd">{{ $val["so_hd"] }}</td>
                                            @if(is_array(json_decode($val["ten_hd"])))
                                                <td class="p-44 qktd">
                                                    @foreach(json_decode($val["ten_hd"]) as $value)
                                                        <span>{{ \App\VanBanModel::where('vb_id',$value)->first()->vb_nhan }}.</span>
                                                        <br>
                                                    @endforeach
                                                </td>
                                            @else
                                                <td class="qktd">
                                                    <span>{{ ($val["ten_hd"]) }}</span>
                                                </td>
                                            @endif
                                            @if($val["ma_phan_biet"])
{{--                                                <td class="qktd">{{ $val["first_name"] }}</td>--}}
{{--                                                <td class="qktd">{{ $val["cn_ten"] }}</td>--}}
{{--                                            @else--}}
                                                <td class="qktd">{{ $val["ccv_master"] }}</td>
                                                <td class="qktd">{{ $val["vp_master"] }}</td>
                                            @endif
                                            <td class="qktd">
                                                <div class="row">
                                                    @if($val["ma_phan_biet"]==\App\Models\SuuTraModel::CODE)
                                                        <span>Jdata</span>
                                                    @else
                                                        <span>DL khác</span>
                                                    @endif
                                                </div>
                                                @if($role=='phong-khac')
                                                    @if($val["ma_phan_biet"] == 'D' && $val["status"] == \App\Models\SuuTraModel::PREVENT)
                                                        <div class="row">chưa duyệt</div>
                                                    @else
                                                    @endif
                                                @else
                                                @endif
                                                <div class="row">
                                                    @if($val["picture"])
                                                        @if(is_array($imgs))
												<a data-toggle="modal" data-target="#img-{{$val->st_id}}"
                                                   class="button button-circle button-mid button-primary">
                                                    <i class="fa fa-image"></i>
                                                </a>
                                                <div class="modal fade bd-example-modal-sm" id="img-{{$val->st_id}}" role="dialog"
                                                     aria-labelledby="modalLabeldanger">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-danger qkmodel">
                                                                <h4 class="modal-title qkmodel">Danh sách tập tin
                                                                </h4>
                                                            </div>
                                                            <div class="modal-body" style="background-color: white">
                                                                <table>
                                                                    <thead>
                                                                    <tr>
                                                                        <th>Tên tập tin</th>
                                                                        <th><i class="fa fa-cog"></i></th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                       @foreach($imgs as $key=>$img)
                                                                        <tr>
                                                                            <td>
                                                                                <span>{{ json_decode($val["real_name"])[$key] }}</span></a>
                                                                            </td>
																			@php
																			$name=json_decode($val["real_name"])[$key];
																			@endphp
                                                                           @if($name)
                                                                            <td style="text-align: center">
                                                                                <a href="{{route('downloadImg',['img'=>$img,'name'=>$name])}}"><span><i class="fa fa-download"></i></span></a>
                                                                            </td>
																			@endif
                                                                        </tr>
                                                                @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="modal-footer" style="background-color: white">
                                                                <div class="form-inline">
                                                                    <a href="#" data-dismiss="modal"
                                                                       class="btn btn-warning">Đóng</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                        @endif
                                                    @else
                                                    @endif
                                                </div>
                                                @if($val["ma_phan_biet"])
                                                    @if($role=='admin'|| $role=='chuyen-vien-so'||$role=='truong-van-phong'||$role=='cong-chung-vien' || $role=='ke-toan')
                                                        <div class="row">
                                                            @if($role == 'ke-toan' && $val["vp"] == $id_vp)
                                                                <a href="{{ route('editSuutra',['id' => $val["st_id"]]) }}"
                                                                   class="button button-circle button-mid button-primary">
                                                                    <i class="fa fa-pencil-square-o"></i>
                                                                </a>
                                                            @endif
                                                            @if(\App\Http\Controllers\SuuTraController::checkEdit($val["created_at"]))
                                                                @if($role=='truong-van-phong'&& $val["vp"] == $id_vp||$val["ccv"]==Sentinel::getUser()->id)
                                                                    <a href="{{ route('editSuutra',['id' => $val["st_id"]]) }}"
                                                                       class="button button-circle button-mid button-primary">
                                                                        <i class="fa fa-pencil-square-o"></i>
                                                                    </a>
                                                                @endif
                                                            @endif
                                                            @if($val["status"] == \App\Models\SuuTraModel::PREVENT)
                                                                <a data-toggle="modal"
                                                                   data-target="#confirm-{{$val["st_id"]}}"
                                                                   class="btn btn-success">
                                                                    <i class="fa fa-check"></i>
                                                                </a>
                                                                <div class="modal fade" id="confirm-{{$val["st_id"]}}"
                                                                     role="dialog"
                                                                     aria-labelledby="modalLabeldanger">
                                                                    <div class="modal-dialog" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header bg-danger">
                                                                                <h4 class="modal-title"
                                                                                    id="modalLabeldanger">Chú ý!
                                                                                </h4>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <p>Duyệt hợp
                                                                                    đồng: {{$val["ten_hd"]}}</p>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <form
                                                                                    action="{{ route('duyetSuutra',['id' => $val["st_id"]]) }}"
                                                                                    method="get">
                                                                                    <div class="form-inline">
                                                                                        <button type="submit"
                                                                                                class="btn btn-danger">
                                                                                            Có,
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
		 @if($val->is_update == 1)
												 <td class="qktd">{{$val["note"]}}  
											 <a style="color: red" href="{{ route('suutralogIndex',['so_hd'=>$val->so_hd]) }}">
                                            => Xem chỉnh sửa
                                        </a> </td>

                                      
                                    @else
										<td></td>
                                    @endif                                        </tr>
                                    @elseif($val["ngan_chan"] == \App\Models\SuuTraModel::PREVENT)
                                        <tr class="ngan_chan_mau_do text-danger">
                                            <td class="qktd"><b>{{ \Carbon\Carbon::parse($val["ngay_nhap"])->format('d/m/Y') }}</b></td>
                                            <td class="qktd">{{ \Carbon\Carbon::parse($val["ngay_cc"])->format('d/m/Y') }}</td>
                                            <td class="p-22 qktd">
                                                @if (strlen($val["duong_su"]) > 280)
                                                    {{ mb_substr($val["duong_su"], 0, 280, 'UTF-8') }}
                                                    <div class="modal fade bd-example-modal-sm"
                                                         id="more-content-md-ad-3-{{ $val["st_id"] }}" tabindex="-1"
                                                         role="dialog"
                                                         aria-labelledby="mySmallModalLabel" aria-hidden="false">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header qkmodel">
                                                                    <h5 class="modal-title qkmodel">Thông tin chi tiết
                                                                        ngăn chặn</h5>
                                                                </div>
                                                                <div class="modal-body" style="background-color: white">
                                                                    <p class="qkdo">
                                                                        {!!str_replace(';',"</br>",$val["duong_su"])!!}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span id="three-dot2-{{ $val["st_id"] }}">...</span>
                                                    <span id="{{ $val["st_id"] }}"
                                                          onclick="showinfo_ad_3('{{ $val["st_id"] }}')">
                                        <i id="search-icon2-{{ $val["st_id"] }}"
                                           class="fa fa-search-plus fa-2x  text-primary"></i>
                                    </span>
                                                @else
                                                    {{ $val["duong_su"] }}
                                                @endif
                                            </td>
                                            <td class="p-33 qktd">
                                                @if(strlen($val["texte"]) > 280){{mb_substr($val["texte"],0,280, "UTF-8")}}
                                                <div class="modal fade bd-example-modal-sm"
                                                     id="more-content-md-ad-{{$val["st_id"]}}"
                                                     tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
                                                     aria-hidden="false">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header qkmodel">
                                                                <h5 class="modal-title qkmodel">
                                                                    Thông tin chi tiết ngăn chặn
                                                                </h5>
                                                            </div>
                                                            <div class="modal-body" style="background-color: white">
                                                                <p class="qkdo">{!!str_replace(';',"</br>",$val["texte"])!!}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <span id="three-dot-{{$val["st_id"]}}">...</span>
                                                <span id="{{$val["st_id"]}}" onclick="showinfo_ad_('{{$val["st_id"]}}')">
                                    <i id="search-icon-{{$val["st_id"]}}" class="fa fa-search-plus fa-2x  text-primary"></i>
                                </span>
                                                @else
                                                    {{$val["texte"]}}
                                                @endif
                                                <br>
                                                <span style="color: red">{{ $val["cancel_description"] }}</span>
                                            </td>
                                            <td class="qktd">{{ $val["so_hd"] }}</td>
                                            @if(is_array(json_decode($val["ten_hd"])))
                                                <td class="p-44 qktd">
                                                    @foreach(json_decode($val["ten_hd"]) as $value)
                                                        <span>{{ \App\VanBanModel::where('vb_id',$value)->first()->vb_nhan }}.</span>
                                                        <br>
                                                    @endforeach
                                                </td>
                                            @else
                                                <td class="p-44 qktd">
                                                    <span>{{ ($val["ten_hd"]) }}</span>
                                                </td>
                                            @endif
                                            @if($val["ma_phan_biet"])

{{--                                                <td class="qktd">{{ $val["first_name"] }}</td>--}}
{{--                                                <td class="qktd">{{ $val["cn_ten"] }}</td>--}}
{{--                                            @else--}}
                                                <td class="qktd">{{ $val["ccv_master"] }}</td>
                                                <td class="qktd">{{ $val["vp_master"] }}</td>
                                            @endif
                                            <td class="qktd">
                                                <div class="row">
                                                    @if($val["ma_phan_biet"]==\App\Models\SuuTraModel::CODE)
                                                        <span>Jdata</span>
                                                    @else
                                                        <span>DL khác</span>
                                                    @endif
                                                </div>
                                                <div class="row">Bị chặn</div>
                                                @if($role=='phong-khac')
                                                    @if($val["ma_phan_biet"] == 'D' && $val["status"] == \App\Models\SuuTraModel::PREVENT)
                                                        <div class="row">chưa duyệt</div>
                                                    @else
                                                    @endif
                                                @else
                                                @endif
                                                <div class="row">
                                                    @if($val["picture"])
                                                        @if(is_array($imgs))
                                                           <a data-toggle="modal" data-target="#img-{{$val->st_id}}"
                                                   class="button button-circle button-mid button-primary">
                                                    <i class="fa fa-image"></i>
                                                </a>
                                                <div class="modal fade bd-example-modal-sm" id="img-{{$val->st_id}}" role="dialog"
                                                     aria-labelledby="modalLabeldanger">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-danger qkmodel">
                                                                <h4 class="modal-title qkmodel">Danh sách tập tin
                                                                </h4>
                                                            </div>
                                                            <div class="modal-body" style="background-color: white">
                                                                <table>
                                                                    <thead>
                                                                    <tr>
                                                                        <th>Tên tập tin</th>
                                                                        <th><i class="fa fa-cog"></i></th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                      @foreach($imgs as $key=>$img)
                                                                        <tr>
                                                                            <td>
                                                                                <span>{{ json_decode($val["real_name"])[$key] }}</span></a>
                                                                            </td>
																			@php
																			$name=json_decode($val["real_name"])[$key];
																			@endphp
                                                                           @if($name)
                                                                            <td style="text-align: center">
                                                                                <a href="{{route('downloadImg',['img'=>$img,'name'=>$name])}}"><span><i class="fa fa-download"></i></span></a>
                                                                            </td>
																			@endif
                                                                        </tr>
                                                                @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="modal-footer" style="background-color: white">
                                                                <div class="form-inline">
                                                                    <a href="#" data-dismiss="modal"
                                                                       class="btn btn-warning">Đóng</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                        @endif
                                                    @endif
                                                </div>
                                                @if($val["ma_phan_biet"] == 'D')
                                                    @if($role=='admin'|| $role=='chuyen-vien-so')
                                                        <div class="row">
                                                            <a href="{{ route('editSuutra',['id' => $val["st_id"]]) }}"
                                                               class="button button-circle button-mid button-primary">
                                                                <i class="fa fa-pencil-square-o"></i>
                                                            </a>
                                                            @if($val["status"] == \App\Models\SuuTraModel::PREVENT)
                                                                <a data-toggle="modal"
                                                                   data-target="#confirm-{{$val["st_id"]}}"
                                                                   class="btn btn-success">
                                                                    <i class="fa fa-check"></i>
                                                                </a>
                                                                <div class="modal fade" id="confirm-{{$val["st_id"]}}"
                                                                     role="dialog"
                                                                     aria-labelledby="modalLabeldanger">
                                                                    <div class="modal-dialog" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header bg-danger">
                                                                                <h4 class="modal-title"
                                                                                    id="modalLabeldanger">Chú
                                                                                    ý!</h4>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <p>Duyệt hợp
                                                                                    đồng: {{$val["ten_hd"]}}</p>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <form
                                                                                    action="{{ route('duyetSuutra',['id' => $val["st_id"]]) }}"
                                                                                    method="get">
                                                                                    <div class="form-inline">
                                                                                        <button type="submit"
                                                                                                class="btn btn-danger">
                                                                                            Có,
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
                                            <td class="qktd"><b>{{ \Carbon\Carbon::parse($val["ngay_nhap"])->format('d/m/Y') }}</b></td>
                                            <td class="qktd">{{ \Carbon\Carbon::parse($val["ngay_cc"])->format('d/m/Y') }}</td>
                                            <td class="p-22 qktd">
                                                @if (strlen($val["duong_su"]) > 280)
                                                    {{ mb_substr($val["duong_su"], 0, 280, 'UTF-8') }}
                                                    <div class="modal fade bd-example-modal-sm"
                                                         id="more-content-md-ad-3-{{ $val["st_id"] }}" tabindex="-1"
                                                         role="dialog"
                                                         aria-labelledby="mySmallModalLabel" aria-hidden="false">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header qkmodel">
                                                                    <h5 class="modal-title qkmodel">Thông tin chi tiết
                                                                        cảnh báo</h5>
                                                                </div>
                                                                <div class="modal-body" style="background-color: white">
                                                                    <p class="qkxanh">
                                                                        {!!str_replace(';',"</br>",$val["duong_su"])!!}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span id="three-dot2-{{ $val["st_id"] }}">...</span>
                                                    <span id="{{ $val["st_id"] }}"
                                                          onclick="showinfo_ad_3('{{ $val["st_id"] }}')">
                                        <i id="search-icon2-{{ $val["st_id"] }}" class="fa fa-search-plus fa-2x  text-primary">
                                        </i>
                                    </span>
                                                @else
                                                    {{ $val["duong_su"] }}
                                                @endif
                                            </td>
                                            <td class="p-33 qktd">
                                                @if(strlen($val["texte"]) > 280)
                                                    {{mb_substr($val["texte"], 0, 280, 'UTF-8')}}
                                                    <div class="modal fade bd-example-modal-sm"
                                                         id="more-content-md-ad-{{$val["st_id"]}}"
                                                         tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
                                                         aria-hidden="false">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header qkmodel">
                                                                    <h5 class="modal-title qkmodel">Thông tin chi tiết
                                                                        cảnh báo</h5>
                                                                </div>
                                                                <div class="modal-body" style="background-color: white">
                                                                    <p class="qkxanh">{!!str_replace(';',"</br>",$val["texte"])!!}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span id="three-dot-{{$val["st_id"]}}">...</span>
                                                    <span id="{{$val["st_id"]}}"
                                                          onclick="showinfo_ad_('{{$val["st_id"]}}')">
                                        <i id="search-icon-{{$val["st_id"]}}"
                                           class="fa fa-search-plus fa-2x  text-primary"></i>
                                    </span>
                                                @else
                                                    {{$val["texte"]}}
                                                @endif
                                                <br>
                                                <span style="color: red">{{ $val["cancel_description"] }}</span>
                                            </td>
                                            <td class="qktd" style="font-size: 14px">{{ $val["so_hd"] }}</td>
                                            @if(is_array(json_decode($val["ten_hd"])))
                                                <td class="p-44 qktd">
                                                    @foreach(json_decode($val["ten_hd"]) as $value)
                                                        <span>{{ \App\VanBanModel::where('vb_id',$value)->first()->vb_nhan }}.</span>
                                                        <br>
                                                    @endforeach
                                                </td>
                                            @else
                                                <td class="qktd">
                                                    <span>{{ ($val["ten_hd"]) }}</span>
                                                </td>
                                            @endif
                                            @if($val["ma_phan_biet"])
{{--                                                <td class="qktd">{{ $val["first_name"] }}</td>--}}
{{--                                                <td class="qktd">{{ $val["cn_ten"] }}</td>--}}
{{--                                            @else--}}
                                                <td class="qktd">{{ $val["ccv_master"] }}</td>
                                                <td class="qktd">{{ $val["vp_master"] }}</td>
                                            @endif
                                            <td class="qktd">
                                                <div class="row">
                                                    @if($val["ma_phan_biet"]==\App\Models\SuuTraModel::CODE)
                                                        <span>Jdata</span>
                                                    @else
                                                        <span>DL khác</span>
                                                    @endif
                                                </div>
                                                <div class="row">
                                                    @if($val["ngan_chan"] == \App\Models\SuuTraModel::WARNING)
                                                        Cảnh báo
                                                    @endif
                                                </div>
                                                @if($role=='phong-khac')
                                                    @if($val["ma_phan_biet"] == 'D' && $val["status"] == \App\Models\SuuTraModel::PREVENT)
                                                        <div class="row">chưa duyệt</div>
                                                    @else
                                                    @endif
                                                @else
                                                @endif
                                                <div class="row">
                                                    @if($val["picture"])
                                                        @if(is_array($imgs))
                                                           <a data-toggle="modal" data-target="#img-{{$val->st_id}}"
                                                   class="button button-circle button-mid button-primary">
                                                    <i class="fa fa-image"></i>
                                                </a>
                                                <div class="modal fade bd-example-modal-sm" id="img-{{$val->st_id}}" role="dialog"
                                                     aria-labelledby="modalLabeldanger">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-danger qkmodel">
                                                                <h4 class="modal-title qkmodel">Danh sách tập tin
                                                                </h4>
                                                            </div>
                                                            <div class="modal-body" style="background-color: white">
                                                                <table>
                                                                    <thead>
                                                                    <tr>
                                                                        <th>Tên tập tin</th>
                                                                        <th><i class="fa fa-cog"></i></th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                       @foreach($imgs as $key=>$img)
                                                                        <tr>
                                                                            <td>
                                                                                <span>{{ json_decode($val["real_name"])[$key] }}</span></a>
                                                                            </td>
																			@php
																			$name=json_decode($val["real_name"])[$key];
																			@endphp
                                                                           @if($name)
                                                                            <td style="text-align: center">
                                                                                <a href="{{route('downloadImg',['img'=>$img,'name'=>$name])}}"><span><i class="fa fa-download"></i></span></a>
                                                                            </td>
																			@endif
                                                                        </tr>
                                                                @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="modal-footer" style="background-color: white">
                                                                <div class="form-inline">
                                                                    <a href="#" data-dismiss="modal"
                                                                       class="btn btn-warning">Đóng</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                        @endif
                                                    @endif
                                                </div>
                                                @if($val["ma_phan_biet"])
                                                    @if($role=='admin'|| $role=='chuyen-vien-so')
                                                        <div class="row">
                                                            <a href="{{ route('editSuutra',['id' => $val["st_id"]]) }}"
                                                               class="button button-circle button-mid button-primary">
                                                                <i class="fa fa-pencil-square-o"></i>
                                                            </a>
                                                            @if($val["status"] == \App\Models\SuuTraModel::PREVENT)
                                                                <a data-toggle="modal"
                                                                   data-target="#confirm-{{$val["st_id"]}}"
                                                                   class="btn btn-success">
                                                                    <i class="fa fa-check"></i>
                                                                </a>
                                                                <div class="modal fade" id="confirm-{{$val["st_id"]}}"
                                                                     role="dialog"
                                                                     aria-labelledby="modalLabeldanger">
                                                                    <div class="modal-dialog" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header bg-danger">
                                                                                <h4 class="modal-title"
                                                                                    id="modalLabeldanger">Chú
                                                                                    ý!</h4>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <p>Duyệt hợp
                                                                                    đồng: {{$val["ten_hd"]}}</p>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <form
                                                                                    action="{{ route('duyetSuutra',['id' => $val["st_id"]]) }}"
                                                                                    method="get">
                                                                                    <div class="form-inline">
                                                                                        <button type="submit"
                                                                                                class="btn btn-danger">
                                                                                            Có,
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
                            <div class="col-sm-4">
                                {{$data->appends(request()->input())->onEachSide(2)->links()}}
                            </div>
                            <div class="col-sm-8">
                                <p class="pull-right" style="font-size: 16px;">Tổng số:
                                    <b>
                                        <span style="color: red">{{\App\Models\SuuTraModel::query()->count()}}</span>
                                    </b>
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
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
@stop
@section('footer_scripts')
    <script type="text/javascript" src="{{ asset('assets/js/jquery.highlight-5.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/js/jquery.mark.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/js/imgPreview.js') }}"></script>
    {{--    <script src="{{ asset('assets/vendors/jasny-bootstrap/js/jasny-bootstrap.js') }}" type="text/javascript"></script>--}}
    {{--    <script type="text/javascript" src="{{ asset('assets/vendors/fancybox/jquery.fancybox.pack.js') }}"></script>--}}
    {{--    <script type="text/javascript" src="{{ asset('assets/js/pages/gallery.js') }}"></script>--}}
    {{--    <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>--}}
    <script type="text/javascript">
        var isTSTab = "{{$loadTaiSan}}"
		        var isCBTab = "{{$isAdvanced??false}}"
        if (isTSTab) {
            $("#ds-f").removeClass('active show');
            $("#tab-ds-a").removeClass('active');
            $("#tab-ts-a").addClass('active');
            $("#ts-f").addClass('active show');
			$("#tab-cb-a").removeClass('active');
            $("#ts-cb").removeClass('active show');
        }
		
		if (isCBTab) {
            $("#ds-f").removeClass('active show');
            $("#tab-ds-a").removeClass('active');
            $("#tab-ts-a").removeClass('active');
            $("#ts-f").removeClass('active show');
			$("#tab-cb-a").addClass('active');
            $("#ts-cb").addClass('active show');
        }
        $('#idbody').removeClass('nav-md');
        $('#idbody').addClass('nav-sm');
    </script>
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
		


        $('#btnprintOther').click(function () {
            $('#formSreachOther').attr('target', "_blank");
            $('#formSreachOther').attr('action', "{{ route('PrintSuuTra') }}");
            $("#formSreachOther").submit();
        });

        function submit() {
            $('#formSreachOther').removeAttr('target');
            $('#formSreachOther').attr('action', "{{ route('indexOtherSuutra') }}");
            $("#formSreachOther").submit();
        }

        $('#btnsearchOther').click(function () {
            $('#formSreachOther').removeAttr('target');
            $('#formSreachOther').attr('action', "{{ route('indexOtherSuutra') }}");
            $("#formSreachOther").submit();
        });
		$('#btnprintAdvanced').click(function () {
            $('#formSreachAdvanced').attr('target', "_blank");
            $('#formSreachAdvanced').attr('action', "{{ route('PrintSuuTra') }}");
            $("#formSreachAdvanced").submit();
        });

        function submitAdvanced() {
            $('#formSreachAdvanced').removeAttr('target');
            $('#formSreachAdvanced').attr('action', "{{ route('indexAdvancedSuutra') }}");
            $("#formSreachAdvanced").submit();
        }

        $('#btnsearchAdvanced').click(function () {
            $('#formSreachAdvanced').removeAttr('target');
            $('#formSreachAdvanced').attr('action', "{{ route('indexAdvancedSuutra') }}");
            $("#formSreachAdvanced").submit();
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
                            "separateWordSearch": true,
                            "diacritics": true
                        });

                    }
                });
            } else {
                $('.p-22').unmark({
                    done: function () {
                        $.each(keywords2, function (k, v) {
							console.log(v);
                            $('.p-22').mark(v, {
                                "separateWordSearch": true,
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
                            "separateWordSearch": true,
                            "diacritics": true
                        });

                    }
                });
            } else {
                $('.p-33').unmark({
                    done: function () {
                        $.each(keywords, function (k, v) {
							alert(v);
                            $('.p-33').mark(v, {
                                "separateWordSearch": true,
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
                $('#search-icon-' + element.id).removeClass('fa-search-plus fa-2x ');
                $('#search-icon-' + element.id).addClass('fa-search-minus')

            } else {
                $('#three-dot-' + element.id).show();

                $('#more-content-' + element.id).addClass('content-disp');
                $('#search-icon-' + element.id).removeClass('fa-search-minus');
                $('#search-icon-' + element.id).addClass('fa-search-plus fa-2x ')
            }
        }

        function readMore2(element) {
            if ($('#more-content2-' + element.id).is(":hidden")) {
                $('#three-dot2-' + element.id).hide();

                $('#more-content2-' + element.id).removeClass('content-disp');
                $('#search-icon2-' + element.id).removeClass('fa-search-plus fa-2x ');
                $('#search-icon2-' + element.id).addClass('fa-search-minus')
            } else {
                $('#three-dot2-' + element.id).show();
                $('#more-content2-' + element.id).addClass('content-disp');
                $('#search-icon2-' + element.id).removeClass('fa-search-minus');
                $('#search-icon2-' + element.id).addClass('fa-search-plus fa-2x ')
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
        function showinfo_ts_(id) {
            $('#more-content-md-ts-' + id).modal();

        }

        function showinfo_ts_2(id) {
            $('#more-content-md-ts-2-' + id).modal();

        }

        function showinfo_ts_3(id) {
            $('#more-content-md-ts-3-' + id).modal();

        }
		function showinfo_ad_(id) {
            $('#more-content-md-ad-' + id).modal();

        }

        function showinfo_ad_2(id) {
            $('#more-content-md-ad-2-' + id).modal();

        }

        function showinfo_ad_3(id) {
            $('#more-content-md-ad-3-' + id).modal();

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
