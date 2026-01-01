@extends('admin/layouts/default')
@php
    if(Sentinel::check()){
    $role = Sentinel::check()->user_roles()->first()->slug;
    $vp=\App\Models\NhanVienModel::find(Sentinel::getUser()->id)->nv_vanphong;
    }else{
        return view('admin.login');
    }
    
@endphp
@section('title')
    Thông báo
    @parent
@stop
@section('header_styles')
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/pages/tables.css') }}"/> -->
    <link rel="stylesheet" type="text/css"
          href="{{ asset('assets/vendors/datatables/css/dataTables.bootstrap.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/datatables/css/scroller.bootstrap.css') }}">
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/fancybox/jquery.fancybox.css') }}" -->
          <!-- media="screen"/> -->
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/fancybox/helpers/jquery.fancybox-buttons.css') }}"> -->
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css"> -->
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/fancybox/jquery.fancybox-thumbs.css') }}"/> -->
    <link rel="stylesheet" href="{{ asset('assets/vendors/Buttons/css/buttons.css') }}"/>
    <style>
        mark {
            padding: 0;
            background-color: #ffe456 !important;
        }

        .btn1 {
            font-weight: 500 !important;
            background-color: white !important;
            color: #01bc8c !important;
            font-size: 14px !important;
        }

        .qktd {
            text-align: center;
        }

        .btn2 {
            font-weight: 500 !important;
            background-color: white !important;
            color: #1a67a3 !important;
            font-size: 14px !important;
        }

        .qkghichu {
            color: red;
        }

        .bg-danger {
            background: #e74040 !important;
        }

        li .active {
            color: #0b67cd !important;
        }
        .carousel{
            margin-top: 50px;
        }
        .tb_content
        {
            border: 4px solid #e6e6e6;
            padding: 10px;
            margin: 0 auto;
            margin-top: 100px;
            background-color: #fff;
            border-radius: 7px;
        } 
.carousel-control-next-icon:after
{
  content: '>';
  font-size:30px;
  color: rgb(172, 108, 108);
  font-weight: bold;
}

.carousel-control-prev-icon:after {
  content: '<';
  font-size: 30px;
  color: rgb(172, 108, 108);
    font-weight: bold;
}
ol.carousel-indicators {
  position: absolute;
  bottom: 0;
  margin: 0;
  left: 0;
  right: 0;
  width: auto;
}


    </style>
@stop
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="wow slideInRight">
                    <div class="panel-group">
                        <div class="panel panel-default">
                            <div class="panel-body text_bg">
                                <div class="panel-title">
                                    <div class="row" style="font-size: 23px;font-weight: 500;">
                                        <span>
                                            <i class="fa fa-bookmark-o"></i>
                                            BẢNG THÔNG BÁO
                                        </span>
                                    </div>
                                </div>
                                <br>
                                <div style="border: 1.5px solid; border-radius: 10px;overflow:scroll; height:500px;">

                                    <ul class="nav nav-tabs">
                                        <li id="tab-ds" class="active">
                                            <a href="#ds-f" id="tab-ds-a" class="active" onclick="submitBasic()" data-toggle="tab">Thông báo</a>
                                        </li>

                                        <li id="tab-prevent">
                                            <a href="#prevent-f" id="tab-prevent-a" onclick="submitPrevent()" data-toggle="tab">Thông báo ngăn chặn</a>
                                        </li>
                                    </ul>
                                    <div id="myTabContent" class="tab-content">
                                        <div class="tab-pane fade active show in" id="ds-f">
                                            <div class="row" id="rwdss" style="">
                                                <div class="panel-body">
                                                    @if(Sentinel::inRole('admin') || Sentinel::inRole('chuyen-vien-so'))
                                                        @foreach($data as $val)
                                                            @if(strlen($val->tieu_de) > 50)
                                                                <li style="color: blue">
                                                                    <a href="{{ route('showTBC',$val->id) }}" style="color: blue">
                                                                        {!! mb_substr($val->tieu_de,0, 50,'UTF-8') !!}<span>...</span>
                                                                    </a>
                                                                </li>
                                                            @else
                                                                <li style="color: blue">
                                                                    <a href="{{ route('showTBC',$val->id) }}" style="color: blue">
                                                                        {!! $val->tieu_de !!}
                                                                    </a>
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        @foreach($tbc_user as $val)
                                                            @if(strlen($val->tieu_de) > 50)
                                                                <li style="color: blue">
                                                                    <a href="{{ route('showTBC',$val->id) }}" style="color: blue">
                                                                        {!! mb_substr($val->tieu_de,0, 50,'UTF-8') !!}<span>...</span>
                                                                    </a>
                                                                </li>
                                                            @else
                                                                <li style="color: blue">
                                                                    <a href="{{ route('showTBC',$val->id) }}" style="color: blue">
                                                                        {!! $val->tieu_de !!}
                                                                    </a>
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade in" id="prevent-f">
                                            <div class="row" id="rwprevents" style="">
                                                <div class="panel-body">
                                                    @if(Sentinel::inRole('admin') || Sentinel::inRole('chuyen-vien-so'))
                                                        @foreach($data_chan as $val)
                                                            @if(strlen($val->tieu_de) > 50)
                                                                <li style="color: blue">
                                                                    <a href="{{ route('showTBC',$val->id) }}" style="color: blue">
                                                                        {!! mb_substr($val->tieu_de,0, 50,'UTF-8') !!}<span>...</span>
                                                                    </a>
                                                                </li>
                                                            @else
                                                                <li style="color: blue">
                                                                    <a href="{{ route('showTBC',$val->id) }}" style="color: blue">
                                                                        {!! $val->tieu_de !!}
                                                                    </a>
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        @foreach($tbc_user_chan as $val)
                                                            @if(strlen($val->tieu_de) > 50)
                                                                <li style="color: blue">
                                                                    <a href="{{ route('showTBC',$val->id) }}" style="color: blue">
                                                                        {!! mb_substr($val->tieu_de,0, 50,'UTF-8') !!}<span>...</span>
                                                                    </a>
                                                                </li>
                                                            @else
                                                                <li style="color: blue">
                                                                    <a href="{{ route('showTBC',$val->id) }}" style="color: blue">
                                                                        {!! $val->tieu_de !!}
                                                                    </a>
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                </div>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="wow slideInRight" data-wow-duration="3s">
                    <div class="panel-group" id="accordion">
                        <div class="panel panel-default">
                            <div class="panel-heading text_bg">
                                <div class="panel-title" style="font-weight: 500; font-size: 23px">
                                    <div data-toggle="collapse" data-parent="#accordion">
                                        <i class="fa fa-wrench"></i>
                                        <span class="success">
                                            CÔNG CỤ TRUY CẬP NHANH</span>
                                    </div>
                                    <hr>
                                </div>
                                <div class="panel-body">

                                    <div class="form-control">
                                        <b>
                                            <i class="fa fa-search"></i>
                                            <a href="{{ route('searchSolr') }}">
                                                TRA CỨU THÔNG TIN
                                            </a>
                                        </b>
                                    </div>
                                    <br>
                                    @if($vp !== "2190")
                                    <div class="form-control">
                                        <b>
                                            <i class="fa fa-plus"></i>
                                            <a href="{{ route('createSuutra') }}">

                                                NHẬP MỚI THÔNG TIN GIAO DỊCH
                                            </a>
                                        </b>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if($vp !== "2190")
                <div class="wow slideInRight" data-wow-duration="3s">
                    <div class="panel-group" id="accordion">
                        <div class="panel panel-default">
                            <div class="panel-heading text_bg">
                                @if(TRUE)
                                    <div class="panel-title pb-2" style="font-weight: 500; font-size: 23px">

                                        <div class="form-control">
                                            <b>
                                                <i class="fa fa-plus"></i>
                                                <a href="#" data-toggle="modal" data-target="#exportReportBds">Báo cáo BDS theo tháng</a>
                                            </b>
                                            <div class="modal fade" id="exportReportBds" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header" style="background-color: blue !important;">
                                                            <h5 class="modal-title" id="exampleModalLongTitle">Báo cáo BDS theo tháng</h5>
                                                        </div>
                                                        <form action="{{route('exportReportBds')}}">
                                                            <div class="modal-body" style="background-color: white">
                                                                <input type="text" hidden name="type" value="month"/>
                                                                <div class="form-group" style="color: black">
                                                                    <label><b>Chọn tháng</b> <span class="text-danger">*</span></label>
                                                                    <div>
                                                                        <input style="width: 50%" type="month" name="months" value="{{ \Illuminate\Support\Carbon::now()->format('Y-m') }}"
                                                                               class="form-control"/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer" style="background-color: #d2d2d2;">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                                                                <button type="submit" class="btn btn-primary">Export</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel-title pb-2" style="font-weight: 500; font-size: 23px">
                                        <div class="form-control">
                                            <b>
                                                <i class="fa fa-plus"></i>
                                                <a href="{{route('exportReportBds',['type'=>'quater'])}}">Báo cáo BDS theo quý</a>
                                            </b>
                                        </div>
                                    </div>
                                    <div class="panel-title pb-2" style="font-weight: 500; font-size: 23px">

                                        <div class="form-control">
                                            <b>
                                                <i class="fa fa-plus"></i>
                                                <a href="{{route('exportReportBds',['type'=>'year'])}}">Báo cáo BDS theo năm</a>

                                            </b>
                                        </div>
                                    </div>
                                @endif


                                <div class="panel-body">

                                   
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="wow slideInRight" data-wow-duration="3s">
                    <div class="panel-group" id="accordion">
                        <div class="panel panel-default">
                            <div class="panel-body">

                              
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    
    <script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
<style type="text/css">
#popup-giua-man-hinh .headerContainer,#popup-giua-man-hinh .bodyContainer,#popup-giua-man-hinh .footerContainer{max-width:1000px;margin:0 auto;background:#FFF}
#popup-giua-man-hinh .padding{padding:10px}
#popup-giua-man-hinh .bodyContainer{min-height:500px}
#popup-giua-man-hinh .popUpBannerBox{position:fixed;background:rgba(0,0,0,0.9);width:100%;height:100%;top:0;left:0;color:#FFF;z-index:99999;display:none;     display: flex;
;justify-content: center}
#popup-giua-man-hinh .popUpBannerContent{top:; width: 500px;height: 300px; ; position: relative; }
#popup-giua-man-hinh .closeButton{color:red;text-decoration:none;font-size:12px}
#popup-giua-man-hinh a.closeButton{float:right}
</style>
<div id="popup-giua-man-hinh">
<div class="popUpBannerBox">
 <div class="popUpBannerInner">
  <div class="popUpBannerContent">

<!-- ==================== CODE HIỂN THỊ QUẢNG CÁO ====================-->

              @php
                    $ads = \App\Models\ThongBaoChung::whereNotNull('push')->orderBy('id','desc')->get();
$ads = $ads->filter(function ($item) {
    $days = (int) ($item->push ?? 0);
    return \Carbon\Carbon::parse($item->updated_at)->addDays($days) >= \Carbon\Carbon::now();
});


              @endphp
              @if($ads)
              <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
                @if(count($ads) > 0)
                    <div class="carousel-inner">
                        @foreach($ads as $key => $ad)
                            @php
                            
    $days = (int) ($ad->push ?? 0);

    if ($days === 0) {
        $time = \Carbon\Carbon::parse($ad->updated_at)->addDays(1000)->format('Y-m-d');
    } else {
        $time = \Carbon\Carbon::parse($ad->updated_at)->addDays($days)->format('Y-m-d');
    }

                            
                                $ymd = DateTime::createFromFormat('d-m-Y', '01-01-2023')->format('Y-m-d');
                            @endphp
                            @if(\Illuminate\Support\Carbon::now()->format('Y-m-d') <= $time )
                            <div class="carousel-item @if($key == 0) active @endif">
                                <div class="tb_content" style="overflow-y: scroll; height:330px;width:500px">
                                         
                                    <p style="text-align: center; font-size: 20px; font-weight: bold; color: #431deb">
                                        THÔNG BÁO GẦN NHẤT</p>
                                    <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev"> 
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    </a>
                                    <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    </a>
                                <a href="{{ route('showTBC',$ad->id) }}" style="color:#fff">
                                    <p style="text-align: center; font-size: 16px; font-weight: bold; color: #eb1d1d">
                                        {{ mb_substr($ad->tieu_de , 0, 150, 'UTF-8') }} ...</p>
                                    </a>
                                <p style="text-align: right; font-size: 10px; color: #111010">
                                    <i> Ngày đăng: {{ \Carbon\Carbon::parse($ad->created_at)->format('H:m:i d/m/Y') }} </i></p>
                                    @if($ad->noi_dung)
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                        <p style="text-align: center; font-size: 15px; font-weight: bold; color: rgb(36, 30, 30)">
                                                {!! $ad->noi_dung !!}
                                         </p>
                                         @if($ad->created_at <= $ymd)
                                            <a href="{{ route('showTBC',$ad->id) }}" style="color:#fff">
                                            <a href="{{url('storage/upload_thongbao/' . $ad ->file)}}" style="color:#1a67a3" target="blank"> <i class="fa fa-paperclip" aria-hidden="true"></i>{{$ad->file}}</a>
                                        @else
                                            @forelse(json_decode($ad->file, true) ?? [] as $img)
    <a href="{{ url('storage/upload_thongbao/' . $img) }}" target="_blank">
        <i class="fa fa-paperclip"></i> {{ $img }}
    </a><br>
@empty
    <span class="text-muted">Không có file đính kèm</span>
@endforelse
                                        @endif
                                        </a>
                                        </div>
                                    </div>  
                                    @else
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            @if($ad->created_at <= $ymd)
                                            <a href="{{ route('showTBC',$ad->id) }}" style="color:#fff">
                                            <a href="{{url('storage/upload_thongbao/' . $ad ->file)}}" style="color:#1a67a3" target="blank"> <i class="fa fa-paperclip" aria-hidden="true"></i> {{$ad->file}}</a>
                                        @else
                                           @php
    $files = json_decode($ad->file, true);
@endphp

@if(is_array($files))
    @foreach($files as $img)
        <a href="{{ url('storage/upload_thongbao/' . $img) }}" style="color:#1a67a3" target="blank">
            <i class="fa fa-paperclip" aria-hidden="true"></i> {{ $img }}
        </a>
        <br>
    @endforeach
@endif
                                        @endif
                                        </div>
                                    </div> 
                                    @endif 
                            </div>
                            </div>
                            @endif
                        @endforeach
                    </div> 
                          <ol class="carousel-indicators" >
                            @foreach($ads as $key => $ad)
                                <li data-target="#carouselExampleCo" data-slide-to="{{ $key }}" class="@if($key == 0) active @endif" style="background-color: #5d1bd8"></li>
                            @endforeach
                        </ol>
                    <p><a href="#" class="closeButton">Đóng</a></p>
                @else
                <a href="https://alphasoftware.vn/"><img  width="500" height="300" src="https://alphasoftware.vn/wp-content/uploads/2021/09/cropped-LOGO-ALPHAGROUP-1.png"/></a>
                @endif
            </div>
            @else
            <a href="https://alphasoftware.vn/"><img  width="500" height="300" src="https://alphasoftware.vn/wp-content/uploads/2021/09/cropped-LOGO-ALPHAGROUP-1.png"/></a>
            @endif
          

<!-- ==================== END HIỂN THỊ QUẢNG CÁO ====================-->
  </div>
 </div>
</div>
<script type="text/javascript">

        function showPopUpBanner() {
            $('.popUpBannerBox').fadeIn("300");
            }
            setTimeout(showPopUpBanner, 500); //thời gian popup bắt đầu hiển thị

            $('.popUpBannerBox').click(function(e) {
            if ( !$(e.target).is('.popUpBannerContent, .popUpBannerContent *' ) ) {
            $('.popUpBannerBox').fadeOut("300");
            return false;
        }
        });
        $('.closeButton').click(function() {
        $('.popUpBannerBox').fadeOut("300");
        return false;
        });
</script>
</div>
    <script>

        $('input[name="birthday"]').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
        });


        function submitPrevent() {
            $("#ds-f").removeClass('active show');
            $("#tab-ds-a").removeClass('active');

            $("#tab-prevent-a").addClass('active');
            $("#prevent-f").addClass('active show');
        }

        function submitBasic() {
            $("#tab-ds-a").addClass('active');
            $("#ds-f").addClass('active show');
            $("#prevent-f").removeClass('active show');
            $("#tab-prevent-a").removeClass('active');


        }
    </script>
@stop
