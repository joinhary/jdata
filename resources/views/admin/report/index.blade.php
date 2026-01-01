@extends('admin/layouts/default')
@section('title')
    Thống kê    @parent
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
	{{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> --}}
    <link rel="stylesheet" href="{{ asset('assets/vendors/select2/css/select2.min.css') }}">

@stop
@section('content')
    @php
        $role = Sentinel::check()->user_roles()->first()->slug;
        $id_vp=\App\Models\ChiNhanhModel::find(\App\Models\NhanVienModel::find(Sentinel::check()->id)->nv_vanphong)->code_cn;
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
                        {{-- <div class="col-md-12 mb-1 mt-1">
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
                        </div> --}}
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
                            <b>Nhóm HĐ</b>
                        </div>
                        <div class="col-md-10 mb-1">
                            {!! \App\Helpers\Form::select(
    'contract[]',
    $contract,
    $contract_key,
    [
        'class' => 'form-control js-example-basic-multiple',
        'id' => 'contract',
        'multiple' => true,    // hoặc '' đều được
        'style' => 'width: 50%'
    ]
) !!}

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
                            {!! \App\Helpers\Form::select('bank[]',$banks,request()->input('bank'),['class'=>'form-control js-example-basic-multiple','multiple','id'=>'bank','style'=>'width: 50%']) !!}
                           
                        </div>
						<div class="col-md-2" style="padding-right: 0px;">
                                <b>Theo Loại</b>
                            </div>
                            <div class="col-md-10 mb-1">
                                {!! \App\Helpers\Form::select('type',$type,request()->input('type'),['class'=>'form-control','id'=>'type']) !!}
                            </div>
                            <div class="col-md-2" style="padding-right: 0px;">
                                <b>Sắp xếp theo:</b>
                            </div>
                            <div class="col-md-10 mb-1">
                                {!! \App\Helpers\Form::select('sortExcel',$sortExcel,request()->input('sortExcel'),['class'=>'form-control','id'=>'sortExcel']) !!}
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
                                >Tải báo cáo
                                </button>
                            </div>
							<div class="col-md-2 mb-1"></div>
                            {{-- <div class="col-md-5 mb-1">
                                <button type="button" style="width:100%" class="btn btn-sm btn-success" onclick="ExportDraw()" id="export-draw">
                                 Download Data
                                </button>
                            </div> --}}
                        <div class="col-md-5 mb-1">
                            <a id="close-menu" class="btn btn-sm btn-danger" style="width:100%">Đóng</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-12">

        </div>
        <div class="row">
            @if(request()->input('office') == null
                && request()->input('status') == null
                && request()->input('contract') == null
                && request()->input('notary') == null
                && request()->input('dateFrom') == null
                && request()->input('dateTo') == null)
                <a></a>
            @else
            
                <a>Có <span style="color : red; font-weight: bold;">{{$count}}</span> kết quả được tìm thấy.</a>
            @endif
        </div>
        <div class="row bctk-scrollable-list" style="overflow-x: hidden;">
            <table class="table-bordered">
                <thead>
                <tr class="text-center " style="background-color:#eeeeee">
                    <th style="width: 7%;font-size: 14px">Ngày nhập<br> hệ thống</th>
                    <th style="width: 7%;font-size: 14px">Ngày CC/<br>ngăn chặn</th>
                    <th style="width: 19%;font-size: 14px">Các bên liên quan</th>
                    <th style="width: 35%;font-size: 14px">Nội dung tóm tắt/<br> công văn</th>
                    <th style="width: 7%;font-size: 14px">Số hợp đồng/<br> CV NC</th>
                    <th style="width: 10%;font-size: 14px">Tên hợp đồng/<br> công văn</th>
                    <th style="width: 10%;font-size: 14px">Công chứng viên/<br> Người nhập</th>
                    <th style="width:7%;font-size: 14px">Văn<br> Phòng</th>
                    <th style="width: 7%;font-size: 14px">Phí/Thù lao</th>
                    <th style="width: 7%;font-size: 14px">Chặn/<br>Giải tỏa</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $val)
                    @if($val->ngan_chan == \App\Models\SuuTraModel::NORMAL)
                        <tr class="khong_ngan_chan_mau_den">
                            <td class="qktd">{{ \Carbon\Carbon::parse($val->ngay_nhap)->format('d/m/Y') }}</td>
                            <td class="qktd">{{ \Carbon\Carbon::parse($val->ngay_cc)->format('d/m/Y') }}</td>
                            <td class="p-22 qktd" style="text-align: justify;">
                                @if(strlen($val->duong_su) > 150)
                                    {{mb_substr($val->duong_su, 0, 150, 'UTF-8')}}
                                    <div class="modal fade bd-example-modal-sm"
                                         id="more-content-md3-{{$val->st_id}}" tabindex="-1" role="dialog"
                                         aria-labelledby="mySmallModalLabel" aria-hidden="false">
                                        <div class="modal-dialog modal-sm modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header qkmodel">
                                                    <h5 class="modal-title qkmodel">
                                                        Các bên liên quan chi tiết
                                                    </h5>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="qktrang">{!!$val->duong_su !!}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <span id="three-dot2-{{$val->st_id}}">...</span>
                                    <span id="{{$val->st_id}}" onclick="showinfo3('{{$val->st_id}}')">
                                            <i id="search-icon2-{{$val->st_id}}"
                                               class="fa fa-search-plus text-primary"></i>
                                        </span>
                                @else
                                    {{$val->duong_su}}
                                @endif
                            </td>
                            <td class="p-33 qktd">
                                @if(strlen($val->texte) > 280)
                                    {{mb_substr($val->texte, 0, 280, 'UTF-8')}}
                                    <div class="modal fade bd-example-modal-sm"
                                         id="more-content-md-{{$val->st_id}}"
                                         tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
                                         aria-hidden="false">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header qkmodel">
                                                    <h5 class="modal-title qkmodel">Thông tin chi tiết
                                                        giao dịch</h5>
                                                </div>
                                                <div class="modal-body" style="background-color: rgb(59, 36, 36)">
                                                    <span class="qktrang" style="white-space: pre-line">{{$val->texte}}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <span id="three-dot-{{$val->st_id}}">...</span>
                                    <span id="{{$val->st_id}}"
                                          onclick="showinfo('{{$val->st_id}}')">
                        <i id="search-icon2-{{$val->st_id}}"
                           class="fa fa-search-plus fa-2x  text-primary"></i>
                    </span>
                                @else
                                    {{ $val->texte }}

                                @endif
                                <br>
                                <span style="color: red">{{ $val["cancel_description"] }}</span>
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
                            <td class="qktd">{{ $val->so_hd }}</td>
                            @if(is_array(json_decode($val->ten_hd)))
                                <td class="p-44 qktd" style="text-align: justify;">
                                    @foreach(json_decode($val->ten_hd) as $value)
                                        <span>{{ \App\VanBanModel::where('vb_id',$value)->first()->vb_nhan }}.</span>
                                        <br>
                                    @endforeach
                                </td>
                            @else
                                <td class="qktd">
                                    <span>{{ ($val->ten_hd) }}</span>
                                </td>
                            @endif

                                <td class="qktd">{{ $val->ccv_master }}</td>
                                <td class="qktd">{{ $val->vp_master }}</td>
                            <td class="qktd">
                                {{number_format((int)$val->phi_cong_chung)}} đ/<br>
                                {{number_format((int)$val->thu_lao)}} đ
                            </td>
                            <td class="qktd">
                                <div class="row">
                                    @if($val->ma_phan_biet == 'D')
                                        <span>Jdata</span>
                                        @if($role=='ke-toan'&& $id_vp==$val->vp)
                                            <a href="{{ route('editSuutra',['id' => $val->st_id]) }}"
                                               class="button button-circle button-mid button-primary">
                                                <i class="fa fa-pencil-square-o"></i>
                                            </a>
                                        @endif
                                    @else
                                        <span></span>
                                    @endif
                                </div>
                                <div class="row">
                                    @if($val->picture)
                                        <?php
                                        $imgs = json_decode($val->picture);
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
                            </td>
                        </tr>
                    @elseif($val->ngan_chan == \App\Models\SuuTraModel::PREVENT)
                        <tr class="ngan_chan_mau_do text-danger">
                            <td class="qktd">{{ \Carbon\Carbon::parse($val->ngay_nhap)->format('d/m/Y') }}</td>
                            <td class="qktd">{{ \Carbon\Carbon::parse($val->ngay_cc)->format('d/m/Y') }}</td>
                            <td class="p-22 qktd" style=" text-align: justify;">
                                @if(strlen($val->duong_su) > 150){{mb_substr($val->duong_su,0,150, "UTF-8")}}
                                <div class="modal fade bd-example-modal-sm" id="more-content-md3-{{$val->st_id}}"
                                     tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
                                     aria-hidden="false">
                                    <div class="modal-dialog modal-sm modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header qkmodel">
                                                <h5 class="modal-title qkmode   l">
                                                    Các bên liên quan chi tiết
                                                </h5>
                                            </div>
                                            <div class="modal-body ">
                                                <p class="qkdo">{!!$val->duong_su !!}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <span id="three-dot2-{{$val->st_id}}">...</span>
                                <span id="{{$val->st_id}}" onclick="showinfo3('{{$val->st_id}}')">
                                        <i id="search-icon2-{{$val->st_id}}" class="fa fa-search-plus text-primary"></i>
                                    </span>
                                @else
                                    {{$val->duong_su}}
                                @endif
                            </td>
                            <td class="p-33 qktd" style=" text-align: justify;">
                                @if(strlen($val->texte) > 150){{mb_substr($val->texte,0,150, "UTF-8")}}
                                <div class="modal fade bd-example-modal-sm" id="more-content-md-{{$val->st_id}}"
                                     tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
                                     aria-hidden="false">
                                    <div class="modal-dialog modal-sm modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header qkmodel">
                                                <h5 class="modal-title qkmodel">
                                                    Thông tin chi tiết
                                                </h5>
                                            </div>
                                            <div class="modal-body">
                                                <p class="qkdo">{!!$val->texte !!}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <span class="content-disp" id="more-content-{{$val->st_id}}">
                                        {!! substr($val->texte, 150) !!}
                                    </span>
                                <span id="three-dot-{{$val->st_id}}">...</span>
                                <span id="{{$val->st_id}}" onclick="showinfo('{{$val->st_id}}')">
                                        <i id="search-icon-{{$val->st_id}}" class="fa fa-search-plus text-primary"></i>
                                    </span>
                                @else
                                    {{$val->texte}}
                                @endif
                                <br>
                                @if($val->cancel_status == 1)
                                    <span style="color: red">{{ $val->cancel_description }}</span>
                                @else
                                @endif
                            </td>
                            <td class="qktd">{{ $val->so_hd }}</td>
                            @if(is_array(json_decode($val->ten_hd)))
                                <td class="p-44 qktd" style="text-align: justify;">
                                    @foreach(json_decode($val->ten_hd) as $value)
                                        <span>{{ \App\VanBanModel::where('vb_id',$value)->first()->vb_nhan }}.</span>
                                        <br>
                                    @endforeach
                                </td>
                            @else
                                <td class="p-44 qktd" style="text-align: justify;">
                                    <span>{{ ($val->ten_hd) }}</span>
                                </td>
                            @endif
                            @if($val->ma_phan_biet == 'D')
                                <td class="qktd">{{ $val->first_name }}</td>
                                <td class="qktd">{{ $val->cn_ten }}</td>
                            @else
                                <td class="qktd">{{ $val->ccv_master }}</td>
                                <td class="qktd">{{ $val->vp_master }}</td>
                            @endif
                            <td class="qktd">
                                {{number_format((int)$val->phi_cong_chung)}} đ/<br>
                                {{number_format((int)$val->thu_lao)}} đ
                            </td>
                            <td class="qktd">
                                <div class="row">
                                    @if($val->ma_phan_biet == 'D')
                                        <span>Jdata</span>
                                        <br>
                                        @if($role=='ke-toan'&& $id_vp==$val->vp)
                                            <a href="{{ route('editSuutra',['id' => $val->st_id]) }}"
                                               class="button button-circle button-mid button-primary">
                                                <i class="fa fa-pencil-square-o"></i>
                                            </a>

                                        @endif
                                    @else
                                        <span></span>
                                    @endif
                                </div>
                                <div class="row">Bị chặn</div>
                                <div class="row">
                                    @if($val->picture)
                                        <?php
                                        $imgs = json_decode($val->picture);
                                        ?>
                                        @if(is_array($imgs))
                                          

                                            @foreach(json_decode($val->picture, true) as $key=>$img)
                                            <div>
                                                    <span>{{substr(json_decode($val->real_name)[$key] , 0,10) . '...'}}
                                                    </span>
                                                @php
                                                    $name=json_decode($val->real_name)[$key];
                                                @endphp
                                                @if($name)
                                                        <a href="{{route('downloadImg',['img'=>$img,'name'=>$name])}}"><span><i class="fa fa-download"></i></span></a>
                                                @endif
                                                </div>
                                        @endforeach
                                        @endif
                                    @endif
                                    <br/>
                                    @if($val->release_doc_number || $val->release_file_path)
                                    @foreach(collect(json_decode($val->release_file_path)) as $key=>$img)
                                    <div>
                                            <span>
                                                {{substr(json_decode($val->release_file_name)[$key] , 0,10) . '...'}}
                                            </span>
                                        @php
                                            $name=json_decode($val->release_file_name)[$key];
                                        @endphp
                                        @if($name)
                                                <a href="{{route('downloadImg',['img'=>$img,'name'=>$name])}}"><span><i
                                                                class="fa fa-download"></i></span></a>
                                        @endif
                                        </div>
                                @endforeach
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @else
                        <tr class="canh_bao_mau_xanh text-primary">
                            <td class="qktd">{{ \Carbon\Carbon::parse($val->ngay_nhap)->format('d/m/Y') }}</td>
                            <td class="qktd">{{ \Carbon\Carbon::parse($val->ngay_cc)->format('d/m/Y') }}</td>
                            <td class="p-22 qktd" style="text-align: justify;">
                                @if(strlen($val->duong_su) > 150)
                                    {{mb_substr($val->duong_su, 0, 150, 'UTF-8')}}
                                    <div class="modal fade bd-example-modal-sm"
                                         id="more-content-md3-{{$val->st_id}}" tabindex="-1" role="dialog"
                                         aria-labelledby="mySmallModalLabel" aria-hidden="false">
                                        <div class="modal-dialog modal-sm modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header qkmodel">
                                                    <h5 class="modal-title qkmodel">
                                                        Các bên liên quan chi tiết</h5>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="qkxanh">{!!$val->duong_su !!}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <span id="three-dot2-{{$val->st_id}}">...</span>
                                    <span id="{{$val->st_id}}" onclick="showinfo3('{{$val->st_id}}')">
                                            <i id="search-icon2-{{$val->st_id}}"
                                               class="fa fa-search-plus text-primary"></i>
                                        </span>
                                @else
                                    {{$val->duong_su}}
                                @endif</td>
                            <td class="p-33 qktd" style="text-align: justify ;">
                                @if(strlen($val->texte) > 150)
                                    {{mb_substr($val->texte, 0, 150, 'UTF-8')}}
                                    <div class="modal fade bd-example-modal-sm" id="more-content-md-{{$val->st_id}}"
                                         tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
                                         aria-hidden="false">
                                        <div class="modal-dialog modal-sm modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header qkmodel">
                                                    <h5 class="modal-title qkmodel">
                                                        Thông tin chi tiết
                                                    </h5>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="qkxanh">{!!$val->texte !!}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="content-disp"
                                          id="more-content-{{$val->st_id}}">{!! substr($val->texte, 150) !!}</span>
                                    <span id="three-dot-{{$val->st_id}}">...</span>
                                    <span id="{{$val->st_id}}" onclick="showinfo('{{$val->st_id}}')">
                                            <i id="search-icon-{{$val->st_id}}"
                                               class="fa fa-search-plus text-primary"></i>
                                        </span>
                                @else
                                    {{$val->texte}}
                                @endif
                                <br>
                                @if($val->cancel_status == 1)
                                    <span style="color: red">{{ $val->cancel_description }}</span>
                                @else
                                @endif
                            </td>
                            <td style="font-size: 14px">{{ $val->so_hd }}</td>
                            @if(is_array(json_decode($val->ten_hd)))
                                <td class="p-44 qktd" style="text-align: justify;">
                                    @foreach(json_decode($val->ten_hd) as $value)
                                        <span>{{ \App\VanBanModel::where('vb_id',$value)->first()->vb_nhan }}.</span>
                                        <br>
                                    @endforeach
                                </td>
                            @else
                                <td class="qktd">
                                    <span>{{ ($val->ten_hd) }}</span>
                                </td>
                            @endif
                            @if($val->ma_phan_biet == "D")
                                <td class="qktd">{{ $val->first_name }}</td>
                                <td class="qktd">{{ $val->cn_ten }}</td>
                            @else
                                <td class="qktd">{{ $val->ccv_master }}</td>
                                <td class="qktd">{{ $val->vp_master }}</td>
                            @endif
                            <td class="qktd">
                                {{number_format((int)$val->phi_cong_chung)}} đ/<br>
                                {{number_format((int)$val->thu_lao)}} đ
                            </td>
                            <td class="qktd">
                                <div class="row">
                                    @if($val->ma_phan_biet == 'D')
                                        <span>Jdata</span><br>
                                        @if($role=='ke-toan'&& $id_vp==$val->vp)
                                            <a href="{{ route('editSuutra',['id' => $val->st_id]) }}"
                                               class="button button-circle button-mid button-primary">
                                                <i class="fa fa-pencil-square-o"></i>
                                            </a>
                                        @endif
                                    @else
                                        <span></span>
                                    @endif
                                </div>
                                <div class="row">
                                    @if($val->ngan_chan == \App\Models\SuuTraModel::WARNING)
                                        Cảnh báo
                                    @endif
                                </div>
                                <div class="row">
                                    @if($val->picture)
                                        <?php
                                        $imgs = json_decode($val->picture);
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
                @if($role == 'admin' || $role == 'chuyen-vien--so')
                    <p class="pull-right">Tổng số: <b><span
                                    style="color: red">{{\App\Models\SuuTraModel::count()}}</span></b>
                    </p>
                @else
                    <p class="pull-right">Tổng số: <b><span
                                    style="color: red">{{\App\Models\SuuTraModel::where('sync_code','=',$id_vp)->count()}}</span></b>
                    </p>
                @endif
            </div>
        </div>
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
            $('#animation').modal('show');

        }
        function ExportDraw(){
            var xem = '{{ route('exportReportView') }}';
            $("#formBaoCao").attr("action",xem);
            $("#formBaoCao").submit();
        }
        function ExportReport(){
            var xem = '{{ route('exportReport') }}';
            $("#formBaoCao").attr("action",xem);
            $("#formBaoCao").submit();
        }

    </script>
@stop

