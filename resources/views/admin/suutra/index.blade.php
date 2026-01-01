@extends('admin/layouts/default')
@section('title')
    Sưu tra    @parent
@stop
@section('header_styles')
    <style>
        .sotuphap html {
            display: none;
        }

        table, th, td {
            border: 1px solid #868585;
        }

        .p-33 {
            /*width: 20%;*/
            width: 100%;
            font-size: 9pt !important;
        }

        .p-22 {
            /*width: 20%*/
            width: 100%
            font-size: 9pt !important;
        }

        .p-44 {
            width: 10%

        }

        table {
            width: 100%;
            margin-bottom: 0.5rem;
            color: #212529;
        }

        th, td {
            text-align: left;
            padding: 10px;
            font-size: 9pt;
        }

        tr, td {
            text-align: left;
            padding: 5px !important;
            font-size: 9pt;
        }

        table th {
            background-color: #0e5965c2;
            font-size: 9pt;

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
            font-size: 9pt !important;
        }

        .qkbtn {
            font-weight: bold;
            font-size: 9pt !important;
        }

        .btn2 {
            font-weight: 500;
            background-color: white !important;
            color: #1a67a3 !important;
            font-size: 9pt !important;
        }

        .qksao {
            font-weight: bold;
        }

        .qkmodel {
            background-color: #1a67a3 !important;
        }

        .qkdo {
            text-align: justify;
            font-size: 9pt;
            color: red;
        }

        .qkxanh {
            text-align: justify;
            font-size: 9pt;
            color: #016639;
        }

        .qktrang {
            text-align: justify;
            font-size: 9pt;
            color: black;
        }

        .qktd {
            font-size: 9pt !important;
            text-align: justify;
        }

        li .active {
            color: #0b67cd !important;
        }
        .text-danger{
            color:red!important
        }
        tr:nth-child(even) {
            background-color: #e7e5e5!important;
        }
    </style>
@stop
@section('content')
    <section class="content" style="font-family: 'Tahoma',sans-serif;">
        <div class="row">
            <div class="col-md-12">
                <ul class="nav nav-tabs">
                    <li id="tab-ds" class="active">
                        <a href="#ds-f" id="tab-ds-a" class="active" onclick="submitBasic()" data-toggle="tab">Tìm kiếm
                            cơ bản(<span style='color:red'>{{\App\Models\SuuTraModel::count()}}</span>)</a>
                    </li>
                    <li id="tab-ts" hidden>
                        <a href="#ts-f" id="tab-ts-a" onclick="submit()" data-toggle="tab">Truy vết giao dịch</a>
                    </li>
                    <li id="tab-prevent">
                        <a href="#prevent-f" id="tab-prevent-a" onclick="submitPrevent()" data-toggle="tab">Dữ liệu ngăn chặn mới nhất(<span style='color:red'>{!!$countPrevent!!}</span>)</a>
                    </li>
					@if(!Sentinel::inRole('admin'))
                    <li id="tab-office">
                        <a href="#ds-f" id="tab-office-a" onclick="submitOffice()" data-toggle="tab">Dữ liệu đơn vị(<span style='color:red'>{!!$countOffice!!}</span>)</a>
                    </li>
					@endif
                </ul>
            </div>
            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade active show in" id="ds-f">
                    <div class="row" id="rwdss" style="">
                        <div class="col-md-8">
                            <form action="{{ route('indexSuutra') }}" method="get" id="formSreach">
                                <input hidden id="isOffice" name="isOffice" value="false">
                                <div class="col-md-3 search">
                                    <span class="fa fa-search fa-search-custom "></span>
                                    <input type="text" class="form-control" id="coban" name="coban"
                                           placeholder="Tìm kiếm các bên liên quan"
										   title='Tìm kiếm các bên liên quan'
                                           value="{{ $getcoban }}" autofocus>

                                </div>
                                <div class="col-md-3 search">
                                    <span class="fa fa-search fa-search-custom"></span>
                                    <input type="text" class="form-control" id="nangcao" name="nangcao"
									title='Tìm kiếm nội dung giao dịch'
                                           placeholder="Tìm kiếm nội dung giao dịch" value="{{ $getNangCao }}"
                                           autofocus>
                                </div>
								@if($isOffice=='true')
									<div class="col-md-2 search">
                                    <span class="fa fa-search fa-search-custom"></span>
                                    <input type="text" class="form-control" id="so_hd" name="so_hd"
									title='Số công chứng'
                                           placeholder="Số công chứng" value="{{ $so_hd }}"
                                           autofocus>
                                </div>
								@endif
                                <div class="col-md-3 search d-flex justify-content-around">
                                    <button class="btn btn-success btn1" type="submit" id="btnsearch">
                                        <i class="fa fa-search"></i>
                                        Tìm kiếm
                                    </button>
                                    <a class="btn btn-danger" id="btnclear">
                                        <i class="fa fa-trash"></i>
                                        Xóa
                                    </a>
                                </div>
                                <div class="col-md-12">   <span style="font-size: 12px;color: black"><b style="color: red">Gợi ý:</b> Nguyễn Văn A để tìm<span
                                                style="color: red"> hoặc</span> (kèm năm sinh hoặc CMND)</span></div>
                            </form>
                        </div>
                        <div class="col-md-4 ">
                            <a class="btn btn-primary btn2" onclick="import_file()">
                                <i class="fa fa-plus"></i>
                                IMPORT
                            </a>
                            <a id="btnprint" class="btn btn-warning">
                                <i class="fa fa-print"></i>
                                PRINT
                            </a>
                            <a href="{{route('indexSuutraNew')}}" class="btn btn-primary">
                                <i class="fa fa-eye"></i>
                                Giao diện mới
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        @if(request()->input('coban') == null && request()->input('nangcao') == null)
                            <a></a>
                        @else
                            <a>Có <span style="color : red; font-weight: bold;">{{$count}}</span> kết quả được tìm thấy
                                . </a><span> Nội dung có màu <span style="color:black;background-color:black">###</span> là dữ liệu bình thường, màu <span
                                        style="color:#016639;background-color:#016639">###</span> là dữ liệu cảnh báo, màu <span
                                        style="color:red;background-color:red">###</span>  là ngăn chặn</span>
                        @endif
                    </div>
                    @if($count>0)
                        <div class="row" style="overflow-x: hidden;">
                            @php
                                $role = Sentinel::check()->user_roles()->first()->slug;
                                $id_vp=\App\Models\NhanVienModel::find(Sentinel::check()->id)->nv_vanphong;

                            @endphp
                            <table id="noi-bo-table" class="">
                                <thead>
                                <tr class="text-center" style="background-color:#eeeeee">
                                    <th style="width: 6%;font-size: 8pt !important;">Ngày nhập<br> hệ thống</th>
                                    <th style="width: 6%;font-size: 8pt !important;">Ngày CC/<br>ngăn chặn</th>
                                    <th style="width: 25%;font-size: 8pt !important;">Các bên liên quan</th>
                                    <th style="width: 25%;font-size: 8pt !important;">Nội dung tóm tắt/<br> giao dịch
                                    </th>
                                    <th style="width: 8%;font-size: 8pt !important;">Số HD/<br> CV NC<br>Tên HD/GD</th>

                                    <th style="width: 6%;font-size: 8pt !important;">VP<br>CCV/<br> Người nhập</th>
                                    <th style="width: 4%;font-size: 8pt !important;">Chặn/<br>Giải tỏa</th>
                                    <th style="width: 4%;font-size: 8pt !important;"></th>

                                </tr>
                                </thead>
                                <tbody>
                                @foreach($data as $val)
                                    @php
                                        $imgs = json_decode($val["picture"]);
										$files=json_decode($val->release_file_path);
										$duong_su=str_replace(["Bên A","bên a","BÊN A"],"<b>Bên A</b>",$val->duong_su);

                                    $duong_su=str_replace(["Bên B","bên b","BÊN b"],"<b>Bên B</b>",$duong_su);
									$duong_su=str_replace(";","<br>",$duong_su);
                                    $duong_su = trim($duong_su,'"');

                                    $duong_su_cut=mb_substr($val->duong_su, 0,350, 'UTF-8');
                                    $duong_su_cut=str_replace(["Bên A","bên a","BÊN A"],"<b>Bên A</b>",$duong_su_cut);

                                    $duong_su_cut=str_replace(["Bên B","bên b","BÊN b"],"<b>Bên B</b>",$duong_su_cut);
									
                                        $texte=$val->texte;
                                        $texteFull=$val->texte;
                                    
									$isUpdate=\App\SuuTraLogModel::where('suutra_id',$val->st_id)->count();
      
                                    @endphp
                                    @if($val["ngan_chan"] == \App\Models\SuuTraModel::NORMAL)
                                        <tr class="khong_ngan_chan_mau_den">
                                            <td class="qktd"><b>@if($val['ccv']){{ \Carbon\Carbon::parse($val["created_at"])->format('d/m/Y H:i:s') }}@else {{ \Carbon\Carbon::parse($val["ngay_nhap"])->format('d/m/Y H:i:s') }} @endif</b></td>
                                            <td class="qktd">{{ \Carbon\Carbon::parse($val["ngay_cc"])->format('d/m/Y') }}</td>
                                            <td class="p-22 qktd">
                                                @if($val["ma_phan_biet"]==\App\Models\SuuTraModel::CODE)
                                                    <span>(J)</span>
                                                @else
                                                    <span>(U)</span>
                                                @endif
                                                @if (strlen($val["duong_su"]) > 280)
                                                    {!! $duong_su_cut !!}
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
                                                                    <span class="qktrang" style="">
                                                                        {!! $duong_su !!}
                                                                    </span>
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
                                                    {!! $duong_su !!}
                                                @endif
                                            </td>
                                            <td class="p-33 qktd">
                                                @if(strlen($texte) > 280)
                                                    {{mb_substr($texte, 0, 280, 'UTF-8')}}
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
                                                                    <span class="qktrang" style="white-space: pre-line">{!!$texteFull!!}</span>
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
                                                    {{ $texte }}
												
                                                @endif
                                                <br>
                                                <div style="color: red;white-space: pre-line;">{!! $val["cancel_description"] !!}</div>
												  @if($val->contract_period != null)
														<br>
                                                        <b>Thời hạn: </b> <span
                                                                style="color: red">{{ $val->contract_period }}</span>
                                                    @else
                                                    @endif
												@if($val->undisputed_date != null)
														<br>
                                                        <b>Giải chấp ngày: </b> <span
                                                                style="color: red">{{ \Carbon\Carbon::parse($val->undisputed_date)->format('d/m/Y') }}</span>
																 @if($val->undisputed_note)
																 <span
                                                                style="color: red">;(ghi chú: {{ $val->undisputed_note }})</span>
																@endif
                                                    @endif
													@if($val->deleted_note != null)
														<br>
                                                        <b>  <span
                                                                style="color: red">{{ $val->deleted_note }}</span>
																
																</b>
                                                    @endif
                                            </td>
                                            <td class="qktd">
											<b>
											@if($isOffice=='true')
												<a  class='btn btn-link' href="{{ route('doCanCelEdit',['id' => $val["st_id"]]) }}">
                                                        {{ $val["so_hd"] }}
                                                    </a>
											@else
											{{ $val["so_hd"] }}
											@endif
											
											</b><br>
                                                @if(is_array(json_decode($val["ten_hd"])))
                                                    @foreach(json_decode($val["ten_hd"]) as $value)
                                                        <span>{{ \App\VanBanModel::where('vb_id',$value)->first()->vb_nhan }}.</span>
                                                        <br>
                                                    @endforeach
                                                @else
                                                    <span>{{ ($val["ten_hd"]) }}</span>
                                                @endif
                                            </td>

                                            @if($val["ma_phan_biet"])
                                                {{--                                                <td class="qktd">{{ $val["first_name"] }}</td>--}}
                                                {{--                                                <td class="qktd">{{ $val["cn_ten"] }}</td>--}}
                                                {{--                                            @else--}}
                                                <td class="qktd">{{ $val["vp_master"] }}<br>
                                                    <b>{{ $val["ccv_master"] }}<b>
                                                </td>
                                            @endif
                                            <td class="qktd">
                                                @if($val->release_doc_number)
                                                    Đã giải toả theo CV: <b>{{$val->release_doc_number}}</b>
                                                @endif
                                                @if($val->release_doc_date)
                                                    <br>Ngày giải toả: {{\Illuminate\Support\Carbon::parse($val->release_doc_date)->format('d/m/Y')}}
                                                @endif
                                                <br>
                                                @if($role=='phong-khac')
                                                    @if($val["ma_phan_biet"] == 'D' && $val["status"] == \App\Models\SuuTraModel::PREVENT)
                                                        <div class="row">chưa duyệt</div>
                                                    @else
                                                    @endif
                                                @else
                                                @endif
                                                <div class="row">
                                                    @if($val["picture"]||$val["release_file_name"])
                                                        
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
																				@if(is_array($imgs))
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
																				 @endif
                                                                                </tbody>
                                                                            </table>
                                                                            @if($val->release_doc_number)
                                                                                <table>
                                                                                    <thead>
                                                                                    <tr>
                                                                                        <th>Công văn giải toả</th>
                                                                                        <th><i class="fa fa-cog"></i></th>
                                                                                    </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                    @foreach(collect($files) as $key=>$img)
                                                                                        <tr>
                                                                                            <td>
                                                                                                <span>{{ json_decode($val["release_file_name"])[$key] }}</span></a>
                                                                                            </td>
                                                                                            @php
                                                                                                $name=json_decode($val["release_file_name"])[$key];

                                                                                            @endphp
                                                                                            @if($name)
                                                                                                <td style="text-align: center">
                                                                                                    <a href="{{route('downloadImg',['img'=>$img,'name'=>$name])}}"><span><i
                                                                                                                    class="fa fa-download"></i></span></a>
                                                                                                </td>
                                                                                            @endif
                                                                                        </tr>
                                                                                    @endforeach
                                                                                    </tbody>
                                                                                </table>
                                                                                <p style="color:black">File công văn giải toả đang được cập nhật</p>
                                                                           
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
                                                              
                                                            @endif
                                                                @if($role=='truong-van-phong'&& $val["vp"] == $id_vp)
                                                                
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
                                                <td class="qktd">
                                                    {{$val["note"]}}
                                                    <a style="color: red" href="{{ route('suutralogIndex',['suutra_id'=>$val->st_id]) }}">
                                                        Nhật ký  chỉnh sửa
                                                    </a>
                                                </td>


                                            @else
                                                <td>
											
										
											</td>
                                            @endif

                                        </tr>


                                    @elseif($val["ngan_chan"] == \App\Models\SuuTraModel::PREVENT)
                                        <tr class="ngan_chan_mau_do text-danger">
                                            <td class="qktd"><b>@if($val['ccv']){{ \Carbon\Carbon::parse($val["created_at"])->format('d/m/Y H:i:s') }}@else {{ \Carbon\Carbon::parse($val["ngay_nhap"])->format('d/m/Y H:i:s') }} @endif</b></td>
                                            <td class="qktd">{{ \Carbon\Carbon::parse($val["ngay_cc"])->format('d/m/Y') }}</td>
                                            <td class="p-22 qktd">
                                                @if($val["ma_phan_biet"]==\App\Models\SuuTraModel::CODE)
                                                    <span>(J)</span>
                                                @else
                                                    <span>(U)</span>
                                                @endif
                                                @if (strlen($val["duong_su"]) > 280)
                                                    {!! $duong_su_cut !!}
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
                                                                   <span class="qkdo" style="">
                                                                        {!! $duong_su !!}
                                                                    </span>
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
                                                    {!! $duong_su !!}
                                                @endif
                                            </td>
                                            <td class="p-33 qktd">
                                                @if(strlen($texte) > 280){{mb_substr($texte,0,280, "UTF-8")}}
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
                                                                <p class="qkdo">{!!$texteFull!!}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <span id="three-dot-{{$val["st_id"]}}">...</span>
                                                <span id="{{$val["st_id"]}}" onclick="showinfo('{{$val["st_id"]}}')">
                                    <i id="search-icon-{{$val["st_id"]}}" class="fa fa-search-plus fa-2x  text-primary"></i>
                                </span>
                                                @else
                                                    {{$texte}}
                                                @endif
                                                <br>
                                                <div style="color: red;white-space: pre-line;">{!! $val["cancel_description"] !!}</div>
												  @if($val->contract_period != null)
													  	<br>
                                                        <b>Thời hạn: </b> <span
                                                                style="color: red">{{ $val->contract_period }}</span>
                                                    @else
                                                    @endif
												@if($val->undisputed_date != null)
														<br>
                                                        <b>Giải chấp ngày: </b> <span
                                                                style="color: red">{{ \Carbon\Carbon::parse($val->undisputed_date)->format('d/m/Y') }}</span>
																 @if($val->undisputed_note)
																 <span
                                                                style="color: red">;(ghi chú: {{ $val->undisputed_note }})</span>
																@endif
                                                    @endif
													@if($val->deleted_note != null)
														<br>
                                                        <b>  <span
                                                                style="color: red">{{ $val->deleted_note }}</span>
																
																</b>
                                                    @endif
                                            </td>
                                            <td class="qktd"><b>{{ $val["so_hd"] }}</b><br>
                                                @if(is_array(json_decode($val["ten_hd"])))
                                                    @foreach(json_decode($val["ten_hd"]) as $value)
                                                        <span>{{ \App\VanBanModel::where('vb_id',$value)->first()->vb_nhan }}.</span>
                                                        <br>
                                                    @endforeach
                                                @else
                                                    <span>{{ ($val["ten_hd"]) }}</span>
												<br>
                                                    <b>{{ $val["ccv_master"] }}<b>
                                                @endif
                                            </td>
                                            @if($val["ma_phan_biet"])
                                                {{--                                                <td class="qktd">{{ $val["first_name"] }}</td>--}}
                                                {{--                                                <td class="qktd">{{ $val["cn_ten"] }}</td>--}}
                                                {{--                                            @else--}}
                                                <td class="qktd">{{ $val["vp_master"] }}
                                                </td>
                                            @endif
                                            <td class="qktd">
                                                @if($val->release_doc_number)
                                                    Đã giải toả theo CV: <b>{{$val->release_doc_number}}</b>
                                                @endif
                                                @if($val->release_doc_date)
                                                    <br>Ngày giải toả: {{\Illuminate\Support\Carbon::parse($val->release_doc_date)->format('d/m/Y')}}
                                                @endif

                                                @if($role=='phong-khac')
                                                    @if($val["ma_phan_biet"] == 'D' && $val["status"] == \App\Models\SuuTraModel::PREVENT)
                                                        <div class="row">chưa duyệt</div>
                                                    @else
                                                    @endif
                                                @else
                                                @endif
                                                <div class="row">
                                                    @if($val["picture"]||$val["release_file_name"])
                                                        
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
																				@if(is_array($imgs))
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
																				 @endif
                                                                                </tbody>
                                                                            </table>
                                                                            @if($val->release_doc_number)
                                                                                <table>
                                                                                    <thead>
                                                                                    <tr>
                                                                                        <th>Công văn giải toả</th>
                                                                                        <th><i class="fa fa-cog"></i></th>
                                                                                    </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                    @foreach(collect($files) as $key=>$img)
                                                                                        <tr>
                                                                                            <td>
                                                                                                <span>{{ json_decode($val["release_file_name"])[$key] }}</span></a>
                                                                                            </td>
                                                                                            @php
                                                                                                $name=json_decode($val["release_file_name"])[$key];

                                                                                            @endphp
                                                                                            @if($name)
                                                                                                <td style="text-align: center">
                                                                                                    <a href="{{route('downloadImg',['img'=>$img,'name'=>$name])}}"><span><i
                                                                                                                    class="fa fa-download"></i></span></a>
                                                                                                </td>
                                                                                            @endif
                                                                                        </tr>
                                                                                    @endforeach
                                                                                    </tbody>
                                                                                </table>
                                                                                <p style="color:black">File công văn giải toả đang được cập nhật</p>
                                                                           
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
                                                           @if($val["ngan_chan"] == \App\Models\SuuTraModel::PREVENT)
                                                            <a href="{{ route('editSuutraSTP',['id' => $val["st_id"]]) }}"
                                                               class="button button-circle button-mid button-primary">
                                                                <i class="fa fa-pencil-square-o"></i>
                                                            </a>
															@else
															<a href="{{ route('editSuutra',['id' => $val["st_id"]]) }}"
                                                               class="button button-circle button-mid button-primary">
                                                                <i class="fa fa-pencil-square-o"></i>
                                                            </a>	
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
                                            @if($val->is_update == 1)
                                                <td class="qktd">
                                                    @if($val->contract_period != null)
                                                        <b>Thời hạn: </b> <span
                                                                style="color: red">{{ $val->contract_period }}</span>
                                                    @else
                                                    @endif
                                                    <br>

                                                    {{$val["note"]}}
                                                    <a style="color: red" href="{{ route('suutralogIndex',['suutra_id'=>$val->st_id]) }}">
                                                        Nhật ký  chỉnh sửa

                                                </td>
                                            @else
<td>
@if($val->contract_period != null)
                                                        <b>Thời hạn: </b> <span
                                                                style="color: red">{{ $val->contract_period }}</span>
                                                    @else
                                                    @endif
                                                    <br>
@if($isUpdate>1)
												<a style="color: red" href="{{ route('suutralogIndex',['suutra_id'=>$val->st_id]) }}">
                                                        Nhật ký  chỉnh sửa
                                                    </a>
													@endif
													</td>
                                            @endif
                                        </tr>
                                    @else
                                        <tr class="canh_bao_mau_xanh qkxanh">
                                            <td class="qktd"><b>{{ \Carbon\Carbon::parse($val["ngay_nhap"])->format('d/m/Y') }}</b></td>
                                            <td class="qktd">{{ \Carbon\Carbon::parse($val["ngay_cc"])->format('d/m/Y') }}</td>
                                            <td class="p-22 qktd">
                                                @if($val["ma_phan_biet"]==\App\Models\SuuTraModel::CODE)
                                                    <span>(J)</span>
                                                @else
                                                    <span>(U)</span>
                                                @endif
                                                @if (strlen($val["duong_su"]) > 280)
                                                    {!! $duong_su_cut !!}
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
                                                                   <span class="qkxanh" style="">
                                                                        {!! $duong_su !!}
                                                                    </span>
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
                                                    {!! $duong_su !!}
                                                @endif
                                            </td>
                                            <td class="p-33 qktd">
                                                @if(strlen($texte) > 280)
                                                    {{mb_substr($texte, 0, 280, 'UTF-8')}}
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
                                                                    <span class="qkxanh" style="white-space: pre-line">{!!$texteFull!!}</span>                                                                </div>
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
                                                    {{$texte}}
                                                @endif
                                                <br>
                                                <div style="color: red;white-space: pre-line;">{!! $val["cancel_description"] !!}</div>
												  @if($val->contract_period != null)
													  	<br>
                                                        <b>Thời hạn: </b> <span
                                                                style="color: red">{{ $val->contract_period }}</span>
                                                    @else
                                                    @endif
												@if($val->undisputed_date != null)
														<br>
                                                        <b>Giải chấp ngày: </b> <span
                                                                style="color: red">{{ \Carbon\Carbon::parse($val->undisputed_date)->format('d/m/Y') }}</span>
																 @if($val->undisputed_note)
																 <span
                                                                style="color: red">;(ghi chú: {{ $val->undisputed_note }})</span>
																@endif
                                                    @endif
													@if($val->deleted_note != null)
														<br>
                                                        <b>  <span
                                                                style="color: red">{{ $val->deleted_note }}</span>
																
																</b>
                                                    @endif
                                            </td>
                                            <td class="qktd"><b>{{ $val["so_hd"] }}</b><br>
                                                @if(is_array(json_decode($val["ten_hd"])))
                                                    @foreach(json_decode($val["ten_hd"]) as $value)
                                                        <span>{{ \App\VanBanModel::where('vb_id',$value)->first()->vb_nhan }}.</span>
                                                        <br>
                                                    @endforeach
                                                @else
                                                    <span>{{ ($val["ten_hd"]) }}</span>
                                                @endif
                                            </td>
                                            @if($val["ma_phan_biet"])
                                                {{--                                                <td class="qktd">{{ $val["first_name"] }}</td>--}}
                                                {{--                                                <td class="qktd">{{ $val["cn_ten"] }}</td>--}}
                                                {{--                                            @else--}}
                                                <td class="qktd">{{ $val["vp_master"] }}<br>
                                                    <b>{{ $val["ccv_master"] }}<b>
                                                </td>
                                            @endif
                                            <td class="qktd">
                                                @if($val->release_doc_number)
                                                    Đã giải toả theo CV: <b>{{$val->release_doc_number}}</b>
                                                @endif
                                                @if($val->release_doc_date)
                                                    <br>Ngày giải toả: {{\Illuminate\Support\Carbon::parse($val->release_doc_date)->format('d/m/Y')}}
                                                @endif
                                                <br>
                                                @if($role=='phong-khac')
                                                    @if($val["ma_phan_biet"] == 'D' && $val["status"] == \App\Models\SuuTraModel::PREVENT)
                                                        <div class="row">chưa duyệt</div>
                                                    @else
                                                    @endif
                                                @else
                                                @endif
                                                <div class="row">
                                                    @if($val["picture"]||$val["release_file_name"])
                                                        
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
																				@if(is_array($imgs))
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
																				@endif
                                                                                </tbody>
                                                                            </table>
                                                                            @if($val->release_doc_number)
                                                                                <table>
                                                                                    <thead>
                                                                                    <tr>
                                                                                        <th>Công văn giải toả</th>
                                                                                        <th><i class="fa fa-cog"></i></th>
                                                                                    </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                    @foreach(collect($files) as $key=>$img)
                                                                                        <tr>
                                                                                            <td>
                                                                                                <span>{{ json_decode($val["release_file_name"])[$key] }}</span></a>
                                                                                            </td>
                                                                                            @php
                                                                                                $name=json_decode($val["release_file_name"])[$key];

                                                                                            @endphp
                                                                                            @if($name)
                                                                                                <td style="text-align: center">
                                                                                                    <a href="{{route('downloadImg',['img'=>$img,'name'=>$name])}}"><span><i
                                                                                                                    class="fa fa-download"></i></span></a>
                                                                                                </td>
                                                                                            @endif
                                                                                        </tr>
                                                                                    @endforeach
                                                                                    </tbody>
                                                                                </table>
                                                                                <p style="color:black">File công văn giải toả đang được cập nhật</p>
                                                                            
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
                                                        @if($val["ngan_chan"] == \App\Models\SuuTraModel::PREVENT)
                                                            <a href="{{ route('editSuutraSTP',['id' => $val["st_id"]]) }}"
                                                               class="button button-circle button-mid button-primary">
                                                                <i class="fa fa-pencil-square-o"></i>
                                                            </a>
															@else
															<a href="{{ route('editSuutra',['id' => $val["st_id"]]) }}"
                                                               class="button button-circle button-mid button-primary">
                                                                <i class="fa fa-pencil-square-o"></i>
                                                            </a>	
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
                                            @if($val->is_update == 1)
                                                <td class="qktd">
                                                    @if($val->contract_period != null)
                                                        <b>Thời hạn: </b> <span
                                                                style="color: red">{{ $val->contract_period }}</span>
                                                    @else
                                                    @endif
                                                    <br>

                                                    {{$val["note"]}}
                                                    <a style="color: red" href="{{ route('suutralogIndex',['suutra_id'=>$val->st_id]) }}">
                                                        Nhật ký  chỉnh sửa
                                                    </a>
                                                </td>


                                            @else
                                                <td>
											@if($val->contract_period != null)
                                                        <b>Thời hạn: </b> <span
                                                                style="color: red">{{ $val->contract_period }}</span>
                                                    @else
                                                    @endif
                                                    <br>
											@if($isUpdate>1)
											<a style="color: red" href="{{ route('suutralogIndex',['suutra_id'=>$val->st_id]) }}">
                                                        Nhật ký  chỉnh sửa
                                                    </a>
													@endif
											</td>
                                            @endif
                                        </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                </div>
                <div class="tab-pane fade in" id="prevent-f">
                    <div class="row" id="rwprevents" style="">
                        <div class="col-md-8">
                            <form action="{{ route('indexSuutra') }}" method="get" id="formSreachprevent">
                                <input hidden name="prevent" value="true">
                                <div class="col-md-4 search">
                                    <span class="fa fa-search fa-search-custom "></span>
                                    <input type="text" class="form-control" id="coban" name="coban"
                                           placeholder="Tìm kiếm các bên liên quan"
                                           value="{{ $getcoban }}" autofocus>

                                </div>
                                <div class="col-md-4 search">
                                    <span class="fa fa-search fa-search-custom"></span>
                                    <input type="text" class="form-control" id="nangcao" name="nangcao"
                                           placeholder="Tìm kiếm nội dung giao dịch" value="{{ $getNangCao }}"
                                           autofocus>
                                </div>
                                <div class="col-md-3 search d-flex justify-content-around">
                                    <button class="btn btn-success btn1" type="submit" id="btnsearchprevent">
                                        <i class="fa fa-search"></i>
                                        Tìm kiếm
                                    </button>
                                    <a class="btn btn-danger" id="btnclear">
                                        <i class="fa fa-trash"></i>
                                        Xóa
                                    </a>
                                </div>
                                <div class="col-md-12">   <span style="font-size: 12px;color: black"><b style="color: red">Gợi ý:</b> Nguyễn Văn A để tìm<span
                                                style="color: red"> hoặc</span> (kèm năm sinh hoặc CMND)</span></div>
                            </form>
                        </div>
                        <div class="col-md-4 ">
                            <a class="btn btn-primary btn2" onclick="import_file()">
                                <i class="fa fa-plus"></i>
                                IMPORT
                            </a>
                            <a id="btnprintprevent" class="btn btn-warning">
                                <i class="fa fa-print"></i>
                                PRINT
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        @if(request()->input('coban') == null && request()->input('nangcao') == null)
                            <a></a>
                        @else
                            <a>Có <span style="color : red; font-weight: bold;">{{$count}}</span> kết quả được tìm thấy
                                . </a><span> Nội dung có màu <span style="color:black;background-color:black">###</span> là dữ liệu bình thường, màu <span
                                        style="color:#016639;background-color:#016639">###</span> là dữ liệu cảnh báo, màu <span
                                        style="color:red;background-color:red">###</span>  là ngăn chặn</span>
                        @endif
                    </div>
                    @if($count>0)
                        <div class="row" style="overflow-x: hidden;">
                            @php
                                $role = Sentinel::check()->user_roles()->first()->slug;
                                $id_vp=\App\Models\NhanVienModel::find(Sentinel::check()->id)->nv_vanphong;
                            @endphp
                            <table id="noi-bo-table" class="">
                                <thead>
                                <tr class="text-center" style="background-color:#eeeeee">
                                    <th style="width: 6%;font-size: 8pt !important;">Ngày nhập<br> hệ thống</th>
                                    <th style="width: 6%;font-size: 8pt !important;">Ngày CC/<br>ngăn chặn</th>
                                    <th style="width: 15%;font-size: 8pt !important;">Các bên liên quan</th>
                                    <th style="width: 25%;font-size: 8pt !important;">Nội dung tóm tắt/<br> giao dịch
                                    </th>
                                    <th style="width: 8%;font-size: 8pt !important;">Số HD/<br> CV NC<br>Tên HD/GD</th>

                                    <th style="width: 6%;font-size: 8pt !important;">VP<br>CCV/<br> Người nhập</th>
                                    <th style="width: 10%;font-size: 8pt !important;">Chặn/<br>Giải tỏa</th>
                                    <th style="width: 4%;font-size: 8pt !important;"></th>

                                </tr>
                                </thead>
                                <tbody>
                                @foreach($data as $val)
                                    @php
                                        $imgs = json_decode($val["picture"]);
										$files=json_decode($val->release_file_path);
                                    @endphp
                                    @if($val["ngan_chan"] == \App\Models\SuuTraModel::NORMAL)
                                        <tr class="khong_ngan_chan_mau_den">
                                            <td class="qktd"><b>@if($val['ccv']){{ \Carbon\Carbon::parse($val["created_at"])->format('d/m/Y H:i:s') }}@else {{ \Carbon\Carbon::parse($val["ngay_nhap"])->format('d/m/Y H:i:s') }} @endif</b></td>
                                            <td class="qktd">{{ \Carbon\Carbon::parse($val["ngay_cc"])->format('d/m/Y') }}</td>
                                            <td class="p-22 qktd">
                                                @if($val["ma_phan_biet"]==\App\Models\SuuTraModel::CODE)
                                                    <span>(J)</span>
                                                @else
                                                    <span>(U)</span>
                                                @endif
                                                @if (strlen($val["duong_su"]) > 280)
                                                    {!! $duong_su_cut !!}
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
                                                                    <span class="qktrang" style="">
                                                                        {!! $duong_su !!}
                                                                    </span>
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
                                                    {!! $duong_su !!}
                                                @endif
                                            </td>
                                            <td class="p-33 qktd">
                                                @if(strlen($texte) > 280)
                                                    {{mb_substr($texte, 0, 280, 'UTF-8')}}
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
                                                                    <span class="qktrang" style="white-space: pre-line">{!!$texteFull!!}</span>
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
                                                    {{ $texte }}
                                                @endif
                                                <br>
                                                <div style="color: red;white-space: pre-line;">{!! $val["cancel_description"] !!}</div>
												  @if($val->contract_period != null)
													  	<br>
                                                        <b>Thời hạn: </b> <span
                                                                style="color: red">{{ $val->contract_period }}</span>
                                                    @else
                                                    @endif
												@if($val->undisputed_date != null)
														<br>
                                                        <b>Giải chấp ngày: </b> <span
                                                                style="color: red">{{ \Carbon\Carbon::parse($val->undisputed_date)->format('d/m/Y') }}</span>
																 @if($val->undisputed_note)
																 <span
                                                                style="color: red">;(ghi chú: {{ $val->undisputed_note }})</span>
																@endif
                                                    @endif
													@if($val->deleted_note != null)
														<br>
                                                        <b>  <span
                                                                style="color: red">{{ $val->deleted_note }}</span>
																
																</b>
                                                    @endif
                                            </td>
                                            <td class="qktd"><b>{{ $val["so_hd"] }}</b><br>
                                                @if(is_array(json_decode($val["ten_hd"])))
                                                    @foreach(json_decode($val["ten_hd"]) as $value)
                                                        <span>{{ \App\VanBanModel::where('vb_id',$value)->first()->vb_nhan }}.</span>
                                                        <br>
                                                    @endforeach
                                                @else
                                                    <span>{{ ($val["ten_hd"]) }}</span>
                                                @endif
                                            </td>
                                            @if($val["ma_phan_biet"])
                                                {{--                                                <td class="qktd">{{ $val["first_name"] }}</td>--}}
                                                {{--                                                <td class="qktd">{{ $val["cn_ten"] }}</td>--}}
                                                {{--                                            @else--}}
                                                <td class="qktd">{{ $val["vp_master"] }}<br>
                                                    <b>{{ $val["ccv_master"] }}<b>
                                                </td>
                                            @endif
                                            <td class="qktd">
                                                @if($val->release_doc_number)
                                                    Đã giải toả theo CV: <b>{{$val->release_doc_number}}</b>
                                                @endif
                                                @if($val->release_doc_date)
                                                    <br>Ngày giải toả: {{\Illuminate\Support\Carbon::parse($val->release_doc_date)->format('d/m/Y')}}
                                                @endif
                                                <br>
                                                @if($role=='phong-khac')
                                                    @if($val["ma_phan_biet"] == 'D' && $val["status"] == \App\Models\SuuTraModel::PREVENT)
                                                        <div class="row">chưa duyệt</div>
                                                    @else
                                                    @endif
                                                @else
                                                @endif
                                                <div class="row">
                                                    @if($val["picture"]||$val["release_file_name"])
                                                        
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
																				@if(is_array($imgs))
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
																				                                                        @endif

                                                                                </tbody>
                                                                            </table>
                                                                            @if($val->release_doc_number)
                                                                                <table>
                                                                                    <thead>
                                                                                    <tr>
                                                                                        <th>Công văn giải toả</th>
                                                                                        <th><i class="fa fa-cog"></i></th>
                                                                                    </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                    @foreach(collect($files) as $key=>$img)
                                                                                        <tr>
                                                                                            <td>
                                                                                                <span>{{ json_decode($val["release_file_name"])[$key] }}</span></a>
                                                                                            </td>
                                                                                            @php
                                                                                                $name=json_decode($val["release_file_name"])[$key];

                                                                                            @endphp
                                                                                            @if($name)
                                                                                                <td style="text-align: center">
                                                                                                    <a href="{{route('downloadImg',['img'=>$img,'name'=>$name])}}"><span><i
                                                                                                                    class="fa fa-download"></i></span></a>
                                                                                                </td>
                                                                                            @endif
                                                                                        </tr>
                                                                                    @endforeach
                                                                                    </tbody>
                                                                                </table>
                                                                                <p style="color:black">File công văn giải toả đang được cập nhật</p>
                                                                            @endif
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
                                                                @if($role=='truong-van-phong'&& $val["vp"] == $id_vp)
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
                                                <td class="qktd">
                                                    @if($val->contract_period != null)
                                                        <b>Thời hạn: </b> <span
                                                                style="color: red">{{ $val->contract_period }}</span>
                                                    @else
                                                    @endif
                                                    <br>

                                                    {{$val["note"]}}
                                                    <a style="color: red" href="{{ route('suutralogIndex',['suutra_id'=>$val->st_id]) }}">
                                                        Nhật ký  chỉnh sửa
                                                    </a>
                                                </td>


                                            @else
                                                <td>
											@if($val->contract_period != null)
                                                        <b>Thời hạn: </b> <span
                                                                style="color: red">{{ $val->contract_period }}</span>
                                                    @else
                                                    @endif
                                                    <br>
											@if($isUpdate>1)
											<a style="color: red" href="{{ route('suutralogIndex',['suutra_id'=>$val->st_id]) }}">
                                                        Nhật ký  chỉnh sửa
                                                    </a>
													@endif
											</td>
                                            @endif

                                        </tr>
                                    @elseif($val["ngan_chan"] == \App\Models\SuuTraModel::PREVENT)
                                        <tr class="ngan_chan_mau_do text-danger">
                                            <td class="qktd"><b>@if($val['ccv']){{ \Carbon\Carbon::parse($val["created_at"])->format('d/m/Y H:i:s') }}@else {{ \Carbon\Carbon::parse($val["ngay_nhap"])->format('d/m/Y H:i:s') }} @endif</b></td>
                                            <td class="qktd">{{ \Carbon\Carbon::parse($val["ngay_cc"])->format('d/m/Y') }}</td>
                                            <td class="p-22 qktd">
                                                @if($val["ma_phan_biet"]==\App\Models\SuuTraModel::CODE)
                                                    <span>(J)</span>
                                                @else
                                                    <span>(U)</span>
                                                @endif
                                                @if (strlen($val["duong_su"]) > 280)
                                                    {!! $val["duong_su"] !!}
                                                    <div class="modal fade bd-example-modal-sm"
                                                         id="more-content-md3-f-{{ $val["st_id"] }}" tabindex="-1"
                                                         role="dialog"
                                                         aria-labelledby="mySmallModalLabel" aria-hidden="false">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header qkmodel">
                                                                    <h5 class="modal-title qkmodel">Thông tin chi tiết
                                                                        ngăn chặn</h5>
                                                                </div>
                                                                <div class="modal-body" style="background-color: white">
                                                                    <span class="qkdo" style="">
                                                                        {!! $val["duong_su"] !!}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span id="three-dot2-{{ $val["st_id"] }}">...</span>
                                                    <span id="{{ $val["st_id"] }}"
                                                          onclick="showinfo3f('{{ $val["st_id"] }}')">
                                        <i id="search-icon2-{{ $val["st_id"] }}"
                                           class="fa fa-search-plus fa-2x  text-primary"></i>
                                    </span>
                                                @else
                                                    {!! $val["duong_su"] !!}
                                                @endif
                                            </td>
                                            <td class="p-33 qktd">
                                                @if(strlen($val["texte"]) > 280){{mb_substr($val["texte"],0,280, "UTF-8")}}
                                                <div class="modal fade bd-example-modal-sm"
                                                     id="more-content-md-f-{{$val["st_id"]}}"
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
                                                                <p class="qkdo">{!!$val["texte"]!!}</p>
																
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <span id="three-dot-{{$val["st_id"]}}">...</span>
                                                <span id="{{$val["st_id"]}}" onclick="showinfof('{{$val["st_id"]}}')">
                                    <i id="search-icon-{{$val["st_id"]}}" class="fa fa-search-plus fa-2x  text-primary"></i>
                                </span>
                                                @else
                                                    {{$val["texte"]}}
												@if($val["prevent_doc_receive_date"])
																	<br>
										
															<b>Ngày nhận: {{ Carbon\Carbon::parse($val["prevent_doc_receive_date"])->format('d/m/Y') }}<b>
                                                @endif
												@endif
                                                <br>
                                                <div style="color: red;white-space: pre-line;">{!! $val["cancel_description"] !!}</div>
												  @if($val->contract_period != null)
													  	<br>
                                                        <b>Thời hạn: </b> <span
                                                                style="color: red">{{ $val->contract_period }}</span>
                                                    @else
                                                    @endif
                                            </td>
                                            <td class="qktd"><b>{{ $val["so_hd"] }}</b><br>
                                                @if(is_array(json_decode($val["ten_hd"])))
                                                    @foreach(json_decode($val["ten_hd"]) as $value)
                                                        <span>{{ \App\VanBanModel::where('vb_id',$value)->first()->vb_nhan }}.</span>
                                                        <br>
                                                    @endforeach
                                                @else
                                                    <span>{{ ($val["ten_hd"]) }}</span>
												<br>
                                                    <b>{{ $val["ccv_master"] }}<b>
                                                @endif
                                            </td>
                                            @if($val["ma_phan_biet"])
                                                {{--                                                <td class="qktd">{{ $val["first_name"] }}</td>--}}
                                                {{--                                                <td class="qktd">{{ $val["cn_ten"] }}</td>--}}
                                                {{--                                            @else--}}
                                                <td class="qktd">{{ $val["vp_master"] }}
                                                </td>
                                            @endif
                                            <td class="qktd">
                                                @if($val->release_doc_number)
                                                    Đã giải toả theo CV: <b>{{$val->release_doc_number}}</b>
                                                @endif
                                                @if($val->release_doc_date)
                                                    <br>Ngày giải toả: {{\Illuminate\Support\Carbon::parse($val->release_doc_date)->format('d/m/Y')}}
                                                @endif
                                                <br>
                                                @if($role=='phong-khac')
                                                    @if($val["ma_phan_biet"] == 'D' && $val["status"] == \App\Models\SuuTraModel::PREVENT)
                                                        <div class="row">chưa duyệt</div>
                                                    @else
                                                    @endif
                                                @else
                                                @endif
                                                <div class="row">
                                                    @if($val["picture"]||$val["release_file_name"])

                                                            <a data-toggle="modal" data-target="#files-{{$val->st_id}}"
                                                               class="button button-circle button-mid button-primary">
                                                                <i class="fa fa-image"></i>
                                                            </a>
                                                            <div class="modal fade bd-example-modal-sm" id="files-{{$val->st_id}}" role="dialog"
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
																				@if(is_array($imgs))
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
																				                                                                            @endif

                                                                                </tbody>
                                                                            </table>
                                                                            @if($val->release_doc_number)
                                                                                <table>
                                                                                    <thead>
                                                                                    <tr>
                                                                                        <th>Công văn giải toả</th>
                                                                                        <th><i class="fa fa-cog"></i></th>
                                                                                    </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                    @foreach(collect($files) as $key=>$img)
                                                                                        <tr>
                                                                                            <td>
                                                                                                <span>{{ json_decode($val["release_file_name"])[$key] }}</span></a>
                                                                                            </td>
                                                                                            @php
                                                                                                $name=json_decode($val["release_file_name"])[$key];

                                                                                            @endphp
                                                                                            @if($name)
                                                                                                <td style="text-align: center">
                                                                                                    <a href="{{route('downloadImg',['img'=>$img,'name'=>$name])}}"><span><i
                                                                                                                    class="fa fa-download"></i></span></a>
                                                                                                </td>
                                                                                            @endif
                                                                                        </tr>
                                                                                    @endforeach
                                                                                    </tbody>
                                                                                </table>
                                                                                <p style="color:black">File công văn giải toả đang được cập nhật</p>
                                                                         @endif
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
                                                </div>
                                                @if($val["ma_phan_biet"])
                                                    @if($role=='admin'|| $role=='chuyen-vien-so')
                                                        <div class="row">
                                                          @if($val["ngan_chan"] == \App\Models\SuuTraModel::PREVENT)
                                                            <a href="{{ route('editSuutraSTP',['id' => $val["st_id"]]) }}"
                                                               class="button button-circle button-mid button-primary">
                                                                <i class="fa fa-pencil-square-o"></i>
                                                            </a>
															@else
															<a href="{{ route('editSuutra',['id' => $val["st_id"]]) }}"
                                                               class="button button-circle button-mid button-primary">
                                                                <i class="fa fa-pencil-square-o"></i>
                                                            </a>	
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
                                            @if($val->is_update == 1)
                                                <td class="qktd">
                                                    @if($val->contract_period != null)
                                                        <b>Thời hạn: </b> <span
                                                                style="color: red">{{ $val->contract_period }}</span>
                                                    @else
                                                    @endif
                                                    <br>

                                                    {{$val["note"]}}
                                                    <a style="color: red" href="{{ route('suutralogIndex',['suutra_id'=>$val->st_id]) }}">
                                                        Nhật ký  chỉnh sửa
                                                    </a>
                                                </td>


                                            @else
                                                <td>
											@if($val->contract_period != null)
                                                        <b>Thời hạn: </b> <span
                                                                style="color: red">{{ $val->contract_period }}</span>
                                                    @else
                                                    @endif
                                                    <br>
											@if($isUpdate>1)
											<a style="color: red" href="{{ route('suutralogIndex',['suutra_id'=>$val->st_id]) }}">
                                                        Nhật ký  chỉnh sửa
                                                    </a>
													@endif
											</td>
                                            @endif
                                        </tr>
                                    @else
                                        <tr class="canh_bao_mau_xanh qkxanh">
                                            <td class="qktd"><b>{{ \Carbon\Carbon::parse($val["ngay_nhap"])->format('d/m/Y') }}</b></td>
                                            <td class="qktd">{{ \Carbon\Carbon::parse($val["ngay_cc"])->format('d/m/Y') }}</td>
                                            <td class="p-22 qktd">
                                                @if($val["ma_phan_biet"]==\App\Models\SuuTraModel::CODE)
                                                    <span>(J)</span>
                                                @else
                                                    <span>(U)</span>
                                                @endif
                                                @if (strlen($val["duong_su"]) > 280)
                                                    {!! $duong_su_cut !!}
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
                                                                 <span class="qkxanh" style="">
                                                                        {!! $duong_su !!}
                                                                    </span>
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
                                                    {!! $duong_su !!}
                                                @endif
                                            </td>
                                            <td class="p-33 qktd">
                                                @if(strlen($texte) > 280)
                                                    {{mb_substr($texte, 0, 280, 'UTF-8')}}
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
                                                                    <span class="qkxanh" style="white-space: pre-line">{!!$texteFull!!}</span>
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
                                                    {{$texte}}
                                                @endif
                                                <br>
                                                <div style="color: red;white-space: pre-line;">{!! $val["cancel_description"] !!}</div>
                                            </td>
                                            <td class="qktd"><b>{{ $val["so_hd"] }}</b><br>
                                                @if(is_array(json_decode($val["ten_hd"])))
                                                    @foreach(json_decode($val["ten_hd"]) as $value)
                                                        <span>{{ \App\VanBanModel::where('vb_id',$value)->first()->vb_nhan }}.</span>
                                                        <br>
                                                    @endforeach
                                                @else
                                                    <span>{{ ($val["ten_hd"]) }}</span>
                                                @endif
                                            </td>
                                            @if($val["ma_phan_biet"])
                                                {{--                                                <td class="qktd">{{ $val["first_name"] }}</td>--}}
                                                {{--                                                <td class="qktd">{{ $val["cn_ten"] }}</td>--}}
                                                {{--                                            @else--}}
                                                <td class="qktd">{{ $val["vp_master"] }}<br>
                                                    <b>{{ $val["ccv_master"] }}<b>
                                                </td>
                                            @endif
                                            <td class="qktd">
                                                @if($val->release_doc_number)
                                                    Đã giải toả theo CV: <b>{{$val->release_doc_number}}</b>
                                                @endif
                                                @if($val->release_doc_date)
                                                    <br>Ngày giải toả: {{\Illuminate\Support\Carbon::parse($val->release_doc_date)->format('d/m/Y')}}
                                                @endif
                                                <br>
                                                @if($role=='phong-khac')
                                                    @if($val["ma_phan_biet"] == 'D' && $val["status"] == \App\Models\SuuTraModel::PREVENT)
                                                        <div class="row">chưa duyệt</div>
                                                    @else
                                                    @endif
                                                @else
                                                @endif
                                                <div class="row">
                                                    @if($val["picture"]||$val["release_file_name"])
                                                        
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
																				@if(is_array($imgs))
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
																				@endif
                                                                                </tbody>
                                                                            </table>
                                                                            @if($val->release_doc_number)
                                                                                <table>
                                                                                    <thead>
                                                                                    <tr>
                                                                                        <th>Công văn giải toả</th>
                                                                                        <th><i class="fa fa-cog"></i></th>
                                                                                    </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                    @foreach(collect($files) as $key=>$img)
                                                                                        <tr>
                                                                                            <td>
                                                                                                <span>{{ json_decode($val["release_file_name"])[$key] }}</span></a>
                                                                                            </td>
                                                                                            @php
                                                                                                $name=json_decode($val["release_file_name"])[$key];

                                                                                            @endphp
                                                                                            @if($name)
                                                                                                <td style="text-align: center">
                                                                                                    <a href="{{route('downloadImg',['img'=>$img,'name'=>$name])}}"><span><i
                                                                                                                    class="fa fa-download"></i></span></a>
                                                                                                </td>
                                                                                            @endif
                                                                                        </tr>
                                                                                    @endforeach
                                                                                    </tbody>
                                                                                </table>
                                                                                <p style="color:black">File công văn giải toả đang được cập nhật</p>
                                                                            
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
													@if($val["status"] == \App\Models\SuuTraModel::PREVENT)
                                                            <a href="{{ route('editSuutra',['id' => $val["st_id"]]) }}"
                                                               class="button button-circle button-mid button-primary">
                                                                <i class="fa fa-pencil-square-o"></i>
                                                            </a>
															@else
															<a href="{{ route('editSuutra',['id' => $val["st_id"]]) }}"
                                                               class="button button-circle button-mid button-primary">
                                                                <i class="fa fa-pencil-square-o"></i>
                                                            </a>	
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
                                            @if($val->is_update == 1)
                                                <td class="qktd">
                                                    @if($val->contract_period != null)
                                                        <b>Thời hạn: </b> <span
                                                                style="color: red">{{ $val->contract_period }}</span>
                                                    @else
                                                    @endif
                                                    <br>

                                                    {{$val["note"]}}
                                                    <a style="color: red" href="{{ route('suutralogIndex',['suutra_id'=>$val->st_id]) }}">
                                                        Nhật ký  chỉnh sửa
                                                    </a>
                                                </td>


                                            @else
                                                <td>
											@if($val->contract_period != null)
                                                        <b>Thời hạn: </b> <span
                                                                style="color: red">{{ $val->contract_period }}</span>
                                                    @else
                                                    @endif
                                                    <br>
											@if($isUpdate>1)
											<a style="color: red" href="{{ route('suutralogIndex',['suutra_id'=>$val->st_id]) }}">
                                                        Nhật ký  chỉnh sửa
                                                    </a>
													@endif
											</td>
                                            @endif
                                        </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                </div>

                <div class="tab-pane fade in" id="ts-f">
                    <div class="row" id="rwdts" style="">
                        <div class="col-md-8">
                            <form action="{{ route('indexSuutra') }}" method="get" id="formSreachOther">

                                <div class="col-md-6 search">
                                    <span class="fa fa-search fa-search-custom"></span>
                                    <input type="text" class="form-control" id="nangcao" name="nangcao"
                                           placeholder="Tìm kiếm nội dung giao dịch" value="{{ $getNangCao }}"
                                           autofocus>

                                </div>
                                <div class="col-md-6 search d-flex justify-content-around">
                                    <button class="btn btn-success btn1" type="submit" id="btnsearchOther">
                                        <i class="fa fa-search"></i>
                                        Tìm kiếm
                                    </button>
                                    <a class="btn btn-danger" id="btnclear">
                                        <i class="fa fa-trash"></i>
                                        Xóa
                                    </a>
                                </div>
                                <div class="col-md-12">   <span style="font-size: 12px;color: black"><b style="color: red">Gợi ý:</b> Nguyễn Văn A để tìm<span
                                                style="color: red"> hoặc</span> (kèm năm sinh hoặc CMND)</span></div>
                            </form>
                        </div>
                        <div class="col-md-4 ">
                            <a class="btn btn-primary btn2" onclick="import_file()">
                                <i class="fa fa-plus"></i>
                                IMPORT
                            </a>
                            <a id="btnprintOther" class="btn btn-warning">
                                <i class="fa fa-print"></i>
                                PRINT
                            </a>
                            <a href="{{route('indexSuutraNew')}}" class="btn btn-primary">
                                <i class="fa fa-eye"></i>
                                Giao diện mới
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        @if(request()->input('coban') == null && request()->input('nangcao') == null)
                            <a></a>
                        @else
                            <a>Có <span style="color : red; font-weight: bold;">{{$count}}</span> kết quả được
                                tìm thấy .</a> <span> Nội dung có màu <span
                                        style="color:black;background-color:black">###</span> là dữ liệu bình thường, màu <span
                                        style="color:#016639;background-color:#016639">###</span> là dữ liệu cảnh báo, màu <span
                                        style="color:red;background-color:red">###</span>  là ngăn chặn</span>
                        @endif
                    </div>

                    @if($count>0)
                        <div class="row scrollable-list-suutra" style="overflow-x: hidden;">
                            @php
                                $role = Sentinel::check()->user_roles()->first()->slug;
                                $id_vp=\App\Models\NhanVienModel::find(Sentinel::check()->id)->nv_vanphong;
                            @endphp
                            @foreach($data as $val)
                                @php
                                    $imgs = json_decode($val->picture);
                                    $files=json_decode($val->release_file_path);
                                    $duong_su=str_replace(["Bên A","bên a","BÊN A"],"<b>Bên A</b>",$val->duong_su);
                                    $duong_su=str_replace(["Bên B","bên b","BÊN b"],"<b>Bên B</b>",$duong_su);
									$duong_su=str_replace(";","<br>",$duong_su);
                                    $duong_su_cut=mb_substr($val->duong_su, 0,350, 'UTF-8');
                                    $duong_su_cut=str_replace(["Bên A","bên a","BÊN A"],"<b>Bên A</b>",$duong_su_cut);

                                    $duong_su_cut=str_replace(["Bên B","bên b","BÊN b"],"<b>Bên B</b>",$duong_su_cut);
                                    
                                        $texte=$val->texte;
                                        $texteFull=$val->texte;
                                    
									
									$isUpdate=\App\SuuTraLogModel::where('suutra_id',$val->st_id)->count();
									
                                @endphp
                                <div class="col-sm-12"
                                     style="background-color: #f0f0f0;border-radius: 9pt;padding: 10px;border: 1px solid #827c7c;border-color: #c7c2c2;">
                                    <div class="col-sm-12">
                                        <a style="color:#1a0dab;margin: 0px;font-size: 9pt;margin-bottom: 5px; "><b>

                                                {{ $val->vp_master }}

                                                <span style="margin-left: 1px;margin-right: 1px;"> <i
                                                            class="fa fa-angle-right" aria-hidden="true"></i> </span>

                                                {{ $val->ccv_master }}

                                                <span style="margin-left: 1px;margin-right: 1px;"> <i
                                                            class="fa fa-angle-right" aria-hidden="true"></i> </span>
                                                @if(is_array(json_decode($val->ten_hd)))
                                                    @foreach(json_decode($val->ten_hd) as $value)
                                                        <span>{{ \App\VanBanModel::where('vb_id',$value)->first()->vb_nhan }}.</span>
                                                        <br>
                                                    @endforeach
                                                @else
                                                    {{ $val->ten_hd }}
                                                @endif
                                                {{--                                    <span style="margin-left: 1px;margin-right: 1px;"> <i class="fa fa-angle-right" aria-hidden="true"></i> </span>--}}
                                                {{--                                    Ngày nhập: <b>{{ \Illuminate\Support\Carbon::parse($val->ngay_nhap)->format('d/m/Y') }}</b>--}}
                                                {{--                                    <span style="margin-left: 1px;margin-right: 1px;"> <i class="fa fa-angle-right" aria-hidden="true"></i> </span>--}}
                                                {{--                                    Ngày công chứng: <b>{{ \Illuminate\Support\Carbon::parse($val->ngay_cc)->format('d/m/Y') }}</b>--}}

                                            </b>
                                        </a>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="col-sm-6  p-22"
                                             style="padding-left: 0px;padding-right: 20px;text-align: justify; @if($val->ngan_chan == \App\Models\SuuTraModel::PREVENT) color: red
                                             @elseif($val->ngan_chan == \App\Models\SuuTraModel::WARNING) color: #016639 @else @endif">
                                            @if(strlen($val->duong_su) > 300)
                                                <div
                                                        style="white-space: normal;">    {!!"<b>(Bên liên quan):</b> ".$duong_su_cut!!}</div>
                                                <div class="modal fade bd-example-modal-sm"
                                                     id="more-content-md-ts-{{$val->st_id}}"
                                                     tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
                                                     aria-hidden="false">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header qkmodel">
                                                                <h5 class="modal-title qkmodel">Thông tin chi
                                                                    tiết</h5>
                                                            </div>
                                                            <div class="modal-body" style="background-color: white">
                                                <span style=""
                                                      class="@if($val->ngan_chan == \App\Models\SuuTraModel::PREVENT) qkdo
                        @elseif($val->ngan_chan == \App\Models\SuuTraModel::WARNING) qkxanh @else qktrang @endif">{!!$duong_su !!}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                            @else
                                                <div
                                                        style=";"> {!!"<b>(Bên liên quan):</b> ".$duong_su!!}</div>
                                            @endif
                                        </div>
                                        <div class="col-sm-6 p-33"
                                             style="padding-left: 20px;padding-right: 0px;text-align: justify; @if($val->ngan_chan == \App\Models\SuuTraModel::PREVENT) color: red
                                             @elseif($val->ngan_chan == \App\Models\SuuTraModel::WARNING) color: #016639 @else @endif ">
                                            <span><b>(Nội dung giao dịch): </b></span>
                                            @if(strlen($texte) > 300)
                                                {{mb_substr($texte, 0, 300, 'UTF-8')}}
                                                <div class="modal fade bd-example-modal-sm"
                                                     id="more-content-md-ts-2-{{$val->st_id}}"
                                                     tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
                                                     aria-hidden="false">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header qkmodel">
                                                                <h5 class="modal-title qkmodel">Thông tin chi
                                                                    tiết</h5>
                                                            </div>
                                                            <div class="modal-body" style="background-color: white">
                                                <span style="white-space: pre-line"
                                                      class="@if($val->ngan_chan == \App\Models\SuuTraModel::PREVENT) qkdo
                        @elseif($val->ngan_chan == \App\Models\SuuTraModel::WARNING) qkxanh @else qktrang @endif">{!!$texteFull!!}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <span id="three-dot-{{$val->st_id}}">...</span>
                                                <span id="{{$val->st_id}}"
                                                      onclick="showinfo_ts_2('{{$val->st_id}}')">
                                        <i id="search-icon-{{$val->st_id}}" class="fa fa-lg fa-search text-primary"></i>
                                    </span>
                                            @else
                                                {{$texte}}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="col-sm-7" style="padding-left: 0px; ">
                                            <p style="margin: 0px;font-size: 9pt;margin-top: 5px;">
                                                Ngày nhập:
                                                <b>{{ \Illuminate\Support\Carbon::parse($val->ngay_nhap)->format('d/m/Y') }}</b>
                                                | Ngày
                                                CC: {{ \Illuminate\Support\Carbon::parse($val->ngay_cc)->format('d/m/Y') }}
                                                | Số CC: <b>{{ $val->so_hd }}</b>
                                                <span id="{{$val->st_id}}"
                                                      style="padding-left:10px"
                                                      onclick="showinfo_ts_('{{$val->st_id}}')">
                                        <i id="search-icon-{{$val->st_id}}" class="fa fa-lg fa-search text-primary"></i>
                                    </span>
                                            </p>
                                        </div>
                                        <div class="col-sm-5" style="padding-left: 0px;">
                                            <div class="col-sm-10">
                                                <p style="margin: 0px;font-size: 9pt;margin-top: 5px;">

                                                    @if($val->is_update == 1)
                                                        | <span style="color: red;">Cập nhật lần cuối bởi:{{ $val->user_update }} <span><i
                                                                        class="fa fa-angle-right"
                                                                        aria-hidden="true"></i></span></span>
                                                        <a href="{{ route('suutralogIndex',['suutra_id'=>$val->st_id]) }}"
                                                           class="button button-circle button-mid button-primary">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                    @else
                                                    @endif
                                                    @if($role=='phong-khac')
                                                        @if($val->ma_phan_biet == 'D' && $val->status == \App\Models\SuuTraModel::PREVENT)
                                                            Chưa duyệt
                                                        @else
                                                        @endif
                                                    @else
                                                    @endif
                                                    @if($val->contract_period != null)
                                                        <b>Thời hạn: <b> <span
                                                                        style="color: red">{{ $val->contract_period }}</span>
                                                                @else
                                                                @endif
														
                                                                @if($val->cancel_description != null)
																		<br>
                                                                    | <span
                                                                            style="color: red">{{ $val->cancel_description }}</span>
                                                    @else
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="col-sm-2 d-flex justify-content-around">
                                                @if($role == 'ke-toan' && $val->vp == $id_vp)
                                                    <a href="{{ route('editSuutra',['id' => $val->st_id]) }}"
                                                       class="button button-circle button-mid button-primary">
                                                        <i class="fa fa-pencil-square-o"></i>
                                                    </a>
                                                @endif
                                                @if($val->ccv==Sentinel::getUser()->id)
                                                    <a href="{{ route('editSuutra',['id' => $val->st_id]) }}"
                                                       class="button button-circle button-mid button-primary">
                                                        <i class="fa fa-pencil-square-o"></i>
                                                    </a>
                                                @endif
                                                @if($val->real_name)
                                                    @if(is_array($imgs))
                                                        <div class="row">
                                                            <a data-toggle="modal"
                                                               data-target="#img-{{$val->st_id}}"
                                                               class="button button-circle button-mid button-primary">
                                                                <i class="fa fa-image"></i>
                                                            </a>
                                                            <div class="modal fade bd-example-modal-sm"
                                                                 id="img-{{$val->st_id}}" role="dialog"
                                                                 aria-labelledby="modalLabeldanger">
                                                                <div class="modal-dialog" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header bg-danger qkmodel">
                                                                            <h4 class="modal-title qkmodel">Danh
                                                                                sách tập tin
                                                                            </h4>
                                                                        </div>
                                                                        <div class="modal-body"
                                                                             style="background-color: white">
                                                                            <table>
                                                                                <thead>
                                                                                <tr>
                                                                                    <th>Tên tập tin</th>
                                                                                    <th><i class="fa fa-cog"></i>
                                                                                    </th>
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
                                                                                                <a href="{{route('downloadImg',['img'=>$img,'name'=>$name])}}"><span><i
                                                                                                                class="fa fa-download"></i></span></a>
                                                                                            </td>
                                                                                        @endif
                                                                                    </tr>
                                                                                @endforeach
                                                                                </tbody>
                                                                            </table>
                                                                            @if($val->release_doc_number)
                                                                                <table>
                                                                                    <thead>
                                                                                    <tr>
                                                                                        <th>Công văn giải toả</th>
                                                                                        <th><i class="fa fa-cog"></i></th>
                                                                                    </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                    @foreach(collect($files) as $key=>$img)
                                                                                        <tr>
                                                                                            <td>
                                                                                                <span>{{ json_decode($val["release_file_name"])[$key] }}</span></a>
                                                                                            </td>
                                                                                            @php
                                                                                                $name=json_decode($val["release_file_name"])[$key];

                                                                                            @endphp
                                                                                            @if($name)
                                                                                                <td style="text-align: center">
                                                                                                    <a href="{{route('downloadImg',['img'=>$img,'name'=>$name])}}"><span><i
                                                                                                                    class="fa fa-download"></i></span></a>
                                                                                                </td>
                                                                                            @endif
                                                                                        </tr>
                                                                                    @endforeach
                                                                                    </tbody>
                                                                                </table>
                                                                                <p style="color:black">File công văn giải toả đang được cập nhật</p>
                                                                            @endif
                                                                        </div>
                                                                        <div class="modal-footer"
                                                                             style="background-color: white">
                                                                            <div class="form-inline">
                                                                                <a href="#" data-dismiss="modal"
                                                                                   class="btn btn-warning">Đóng</a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr style="width: 100%;border-top: solid 0px;margin: 0.5rem;">
                            @endforeach

                        </div>
                    @endif
                </div>
            </div>

        </div>

        <div class="col-ms-12">
            <div class="col-sm-6">
                {{$data->onEachSide(1)->appends(request()->input())->links()}}
                
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
                            <a style="color: #016639;" title="File Mẫu"
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
    <script type="text/javascript" src="{{ asset('assets/js/imgPreview.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/jquery.mark.js') }}"></script>
    <script type="text/javascript" src="http://johannburkard.de/resources/Johann/jquery.highlight-5.js"></script>
    <script type="text/javascript">
        $('#idbody').removeClass('nav-md');
        $('#idbody').addClass('nav-sm');



    </script>
    <script type="text/javascript">
        var isTSTab = "{{$loadTaiSan}}"

        var isPrevent = "{{$isPrevent??false}}"
		        var isOffice = "{{$isOffice}}"
				$('#isOffice').val(isOffice)

        if (isTSTab) {
            $("#ds-f").removeClass('active show');
            $("#tab-ds-a").removeClass('active');
            $("#tab-ts-a").addClass('active');
            $("#ts-f").addClass('active show');
            $("#tab-prevent-a").removeClass('active');
            $("#prevent-f").removeClass('active show')
        }
        if (isPrevent) {
            $("#ds-f").removeClass('active show');
            $("#tab-ds-a").removeClass('active');
            $("#tab-ts-a").removeClass('active');
            $("#ts-f").removeClass('active show');
            $("#tab-prevent-a").addClass('active');
            $("#prevent-f").addClass('active show');
        }
		if (isOffice=="true") {
            $("#tab-ds-a").removeClass('active');
            $("#tab-prevent-a").removeClass('active');
            $("#prevent-f").removeClass('active show');
			$("#tab-office-a").addClass('active');
            $("#ds-f").addClass('active show');
        }
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
        $('#btnprintprevent').click(function () {
            $('#formSreachprevent').attr('target', "_blank");
            $('#formSreachprevent').attr('action', "{{ route('PrintSuuTra') }}");
            $("#formSreachprevent").submit();
        });
        $('#btnsearchprevent').click(function () {
            $('#formSreachprevent').removeAttr('target');
            $('#formSreachprevent').attr('action', "{{ route('indexSuutra') }}");
            $("#formSreachprevent").submit();
        });
        $('#btnclear').on('click',function (){
            $('#coban').val('');
            $('#nangcao').val('');
        })

        function submitPrevent() {
            $('#formSreachprevent').removeAttr('target');
            $('#formSreachprevent').attr('action', "{{ route('indexSuutra') }}");
            $("#formSreachprevent").submit();
        }
        function submitBasic() {
				$('#isOffice').val('false')
            $('#formSreach').removeAttr('target');
            $('#formSreach').attr('action', "{{ route('indexSuutra') }}");
            $("#formSreach").submit();
        }
        function submitOffice() {
            $('#formSreach').removeAttr('target');
            $('#formSreach').attr('action', "{{ route('indexSuutra') }}");
            $('#isOffice').val('true');
            $("#formSreach").submit();
        }

        $('#btnprintOther').click(function () {
            $('#formSreachOther').attr('target', "_blank");
            $('#formSreachOther').attr('action', "{{ route('PrintSuuTra') }}");
            $("#formSreachOther").submit();
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
        var str_search_json_symbol = JSON.parse('{!! $str_json_symbol !!}');
        var str_search_json2_symbol = JSON.parse('{!! $str_json2_symbol !!}');

        var keywords2 = str_search_json2;
        var keywords = str_search_json;
        var keywords_symbol = str_search_json_symbol;
        var keywords2_symbol = str_search_json2_symbol;

        var getcoban = '{!! $getcoban !!}';
        var getnangcao = '{!! $getNangCao !!}';
        if (getcoban) {
            if (getcoban.search("%") == -1) {
                $('.p-22').unmark({
                    done: function () {
                        var str = "";
                        $.each(keywords2, function (k, v) {
                            str = " " + v
                            $('.p-22').mark(v, {
                                "separateWordSearch": false,
                                "diacritics": false,

                            });
                        })
                        $.each(keywords2_symbol, function (k, v) {
                            str = " " + v
                            $('.p-22').mark(v, {
                                "separateWordSearch": false,
                                "diacritics": true,
                                "accuracy": "complementary"


                            });

                        })


                    }
                });

            } else {
                $('.p-22').unmark({
                    done: function () {
                        $.each(keywords2, function (k, v) {
                            $('.p-22').mark(v, {
                                "separateWordSearch": false,
                                "diacritics": false,

                            });
                            $.each(keywords2_symbol, function (k, v) {
                                $('.p-22').mark(v, {
                                    "separateWordSearch": false,
                                    "diacritics": true,
                                    "accuracy": "complementary"

                                });
                            })
                        })
                    }
                });

            }

        }
        console.log(getnangcao)
        if (getnangcao) {
            if (getnangcao.search("%") == -1) {

                $('.p-33').unmark({
                    done: function () {
                        var str = "";

                        $.each(keywords, function (k, v) {
							
                            $('.p-33').mark(v, {
                                "separateWordSearch": false,
                                "diacritics": true,
//                                "accuracy": "complementary"
                            });
                        })
						console.log(keywords_symbol);
                        $.each(keywords_symbol, function (k, v) {
									
                            $('.p-33').mark(v, {
                                "separateWordSearch": false,
                                "diacritics": true,
                                "accuracy": "complementary"
                            });
                        })


                    }
                });
            } else {
                $('.p-33').unmark({
                    done: function () {
                        $.each(keywords, function (k, v) {
                            $('.p-33').mark(v, {
                                "separateWordSearch": false,
                                "diacritics": true,
                                "accuracy": "complementary"

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
        function showinfof(id) {
            $('#more-content-md-f-' + id).modal();

        }

        function showinfo3f(id) {
            $('#more-content-md3-f-' + id).modal();

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
