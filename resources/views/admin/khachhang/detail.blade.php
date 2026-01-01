@extends('admin/layouts/default')
@section('title')
    Quản lý khách hàng    @parent
@stop
@section('header_styles')
    <meta name="_token" content="{{ csrf_token() }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/pages/tables.css') }}"/>
    <link rel="stylesheet" type="text/css"
          href="{{ asset('assets/vendors/datatables/css/dataTables.bootstrap.css') }}"/>
    <script src="{{asset('assets\js\jquery-3.3.1.min.js')}}"></script>

    <link rel="stylesheet" href="{{asset('assets/css/jquery.fancybox.css')}} "/>
    <script src="{{asset('assets/js/jquery.fancybox.js')}}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/pages/gallery.js') }}"></script>
    <style>
        #customer-images img {
            width: 30vh;
            height: 20vh;
        }

        body.modal-open .modal {
            display: flex !important;
            height: 100%;
        }

        body.modal-open .modal .modal-dialog {
            margin: auto;
        }
    </style>
@stop
@section('content')
    <section class="content">
        <div class="row bctk-scrollable-list" style="overflow-x: hidden;height: calc(100vh - 100px) !important;">
            <div class="col-md-8">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 style="text-align: center;font-weight: bold">THÔNG TIN ĐƯƠNG SỰ</h4>
                                    DỮ LIỆU TỪ VĂN PHÒNG
                                    : {{ \App\Models\ChiNhanhModel::where('cn_id',$account->id_vp)->first()->cn_ten??'' }}
                                    <br>
									@if($account->id_ccv)
                                    NGƯỜI NHẬP : {{ \App\Models\User::where('id',$account->id_ccv)->first()->first_name??'' }}
                                    ( Chức vụ
                                    : {{ \App\Models\RoleModel::where('id',\App\Models\RoleUsersModel::where('user_id',$account->id_ccv)->first()->role_id)->first()->name ??''}}
                                    )
									@endif
                                    <hr>
                                    <h4>{{$account->nhan}}</h4>
                                </div>
                            </div>
                        </h4>
                    </div>
                    <div class="panel-body">
                        <table class="table table-bordered table-hover mb-0">
                            @foreach($khachhang as $kh)
                                <tr>
                                    <td class="fit-column-kh">{{$kh->tm_nhan}}:</td>
                                    @if($kh->tm_loai == 'file')
                                        <?php
                                        $imgs = json_decode($kh->kh_giatri);
                                        ?>
                                        @if($kh->kh_giatri && !empty($imgs))
                                            <td class="row column-align">
                                                @foreach($imgs as $img)
                                                    <div class="col-md-2 mb-2 mt-1">
                                                        <a data-fancybox="images"
                                                           href="{{url('images/khachhang/'.$img )}}">
                                                            <div class="fileinput-new">
                                                                <img src="{{url('images/khachhang/'.$img )}}"
                                                                     width="50px" height="50px" alt="profile pic">
                                                            </div>
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </td>
                                        @else
                                            <td class="text-left">
                                                <p style="margin: 0px;">Không có hình ảnh nào</p>
                                            </td>
                                        @endif
                                    @else
                                        <td class="text-left">
                                            {{$kh->kh_giatri}}
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <a data-toggle="collapse" href="#collapse5">
                                <b>Thông tin tài khoản</b>
                                <b>Thông tin hôn phối</b>
                                <span class="pull-right">
                                    <i class="glyphicon glyphicon-chevron-down panel-collapsed showhide clickable"></i>
                                </span>
                            </a>
                        </div>
                    </div>
                    <div id="collapse5" class="panel-body collapse show">
                        @if(!empty($honphoi))
                            <table class="table table-bordered table-hover mb-0">
                                @foreach($honphoi as $hp)
                                    <tr>
                                        <td class="fit-column-kh">{{$hp->tm_nhan}}:</td>
                                        @if($hp->tm_loai == 'file')
                                            @if($hp->kh_giatri)
                                                <?php
                                                $imgs = json_decode($hp->kh_giatri);
                                                ?>
                                                <td class="row column-align">
                                                    @foreach($imgs as $img)
                                                        <div class="col-md-2 mb-2 mt-1">
                                                            <a data-fancybox="images"
                                                               href="{{url('images/khachhang/'.$img)}}">
                                                                <img src="{{url('images/khachhang/'.$img)}}" width="50"
                                                                     height="50">
                                                            </a>
                                                        </div>
                                                    @endforeach
                                                </td>
                                            @else
                                                <td class="text-left">
                                                    <p style="margin: 0px;">Không có hình ảnh nào</p>
                                                </td>
                                            @endif
                                        @else
                                            <td class="text-left">
                                                {{$hp->kh_giatri}}
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </table>
                        @else
                            Không có thông tin hôn phối
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div hidden class="col-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <a data-toggle="collapse" href="#collapse4">
                                    <b>Thông tin tài khoản</b>
                                    <span class="pull-right">
                                        <i class="glyphicon glyphicon-chevron-down panel-collapsed showhide clickable"></i>
                                    </span>
                                </a>
                            </div>
                        </div>
                        <div id="collapse4" class="panel-body collapse show">
                            <table class="table table-bordered table-hover mb-0">
                                <tr>
                                    <td colspan="2">
                                        @if($account->pic)
										
                                            <img src="{{ url('assets/images/authors/'.$account->pic) }}" class="avt-kh">
                                        @else
                                            Không có ảnh đại diện
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: left!important; font-weight: bold;">Nhãn:</td>
                                    <td style="text-align: left!important;">{{$account->nhan}}</td>
                                </tr>
                                <tr>
                                    <td style="text-align: left!important; font-weight: bold;">Tài khoản:</td>
                                    <td style="text-align: left!important;">{{$account->username}}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <a data-toggle="collapse" href="#collapse3"><b>Lịch sử hôn nhân</b>
                                    <span class="pull-right"><i
                                            class="glyphicon glyphicon-chevron-down panel-collapsed showhide clickable"></i></span>
                                </a>
                            </div>
                        </div>
                        <div id="collapse3" class="panel-body collapse show">
                            @if($lichsuhonnhan->isNotEmpty())
                                <table class="table table-bordered table-hover mb-0">
                                    <thead>
                                    <tr>
                                        <th>Hôn phối</th>
                                        <th>Tình trạng</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($lichsuhonnhan as $ls)
                                        <tr>
                                            <td style="width: 70%; vertical-align: middle;">
                                                <a href="javascript:void(0)" id="{{$ls->ds2_id}}" data-toggle="tooltip"
                                                   data-placement="bottom" title="Nhấp để xem chi tiết"
                                                   onclick="getDetailKH(this)">{{$ls->first_name}}</a></td>
                                            <td style="vertical-align: middle;">{{$ls->tinhtrang}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                Không có lịch sử hôn nhân
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-12" hidden>
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <a data-toggle="collapse" href="#collapse2"><b>Lịch sử giao dich</b>
                                    <span class="pull-right"><i
                                            class="glyphicon glyphicon-chevron-down panel-collapsed showhide clickable"></i></span>
                                </a>
                            </div>
                        </div>
                        <div id="collapse2" class="panel-body collapse show" style="overflow: scroll;height: 400px">
                            <table class="table table-bordered table-hover mb-0">
                                <thead>
                                <tr>
                                    <th>Tên HĐ</th>
                                    <th>Ngày ký</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($kh_gd as $ls)
                                    @if(\App\HopDongModel::find($ls->hd_id))
                                        @if($ls->ver!=1)
                                            <tr>
                                                <td><a target="_blank" href="{{route('editHopDong',$ls->ho_so_id)}}">
                                                        {{\App\HopDongModel::find($ls->hd_id)->nhan}}
                                                    </a>
                                                </td>
                                                <td>{{\App\HopDongModel::find($ls->hd_id)->ngayky}}</td>

                                            </tr>
                                        @else
                                            <tr>
                                                <td><a target="_blank" href="{{route('editHopDongV2',$ls->ho_so_id)}}">
                                                        {{\App\HopDongModel::find($ls->hd_id)->nhan}}

                                                    </a>
                                                </td>
                                                <td>{{\App\HopDongModel::find($ls->hd_id)->ngayky}}</td>

                                            </tr>
                                        @endif
                                    @endif

                                @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade in" id="detail-kh" tabindex="-1" role="dialog" aria-hidden="false">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <a href="javascript:void(0)" class="text-danger pull-right" data-dismiss="modal"
                           aria-hidden="true"><i class="fa fa-times"></i></a>
                        <h4 class="modal-title">Thông tin đương sự "<b><span id="placeToFillLabelDS"></span></b>"</h4>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered table-hover mb-0" id="kh-table">
                        </table>
                    </div>
                </div>
            </div>
        </div>
        </div>
        <!-- Modal for ảnh thật khách hàng -->
        <div class="modal fade-in modal-center" id="customer-images" tabindex="-1" role="dialog" aria-hidden="false">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title"><b id="title" style="font-family: sans-serif;">Ảnh chụp thật</b><a
                                id="print-route"
                                href="{{ Route::has('print_pdf') ? route('print_pdf') : '#' }}"
                            >
                                <button class="btn btn-info pull-right"><i class="fa fa-print"></i> In PDF</button>
                            </a>
                        </h3>
                    </div>
                    <div class="modal-body">
                        <div class="row text-center">
                            <div class="col-md-4 kh-img">
                                <a class="fancybox-effects-a" data-fancybox="images" id='fancy-box-img1' href="">
                                    <img src="" id="img1">
                                </a>
                            </div>
                            <div class="col-md-4 kh-img">
                                <a class="fancybox-effects-a" data-fancybox="images" id='fancy-box-img2' href="">
                                    <img src="" id="img2">
                                </a>
                            </div>
                            <div class="col-md-4 kh-img">
                                <a class="fancybox-effects-a" data-fancybox="images" id='fancy-box-img3' href="">
                                    <img src="" id="img3">
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Modal for ảnh thật khách hàng -->

    </section>
@stop
@section('footer_scripts')
    <script type="text/javascript" src="{{ asset('assets/vendors/fancybox/jquery.fancybox.pack.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/pages/gallery.js') }}"></script>
    <script src="{{ asset('assets/vendors/moment/js/moment.min.js') }}" type="text/javascript"></script>
    <script language="javascript" type="text/javascript"
            src="{{ asset('assets/vendors/select2/js/select2.js') }}"></script>
    <script src="{{ asset('assets/vendors/jasny-bootstrap/js/jasny-bootstrap.js') }}" type="text/javascript"></script>
    <script type="text/javascript" src="{{ asset('assets/js/imgPreview.js') }}"></script>
    <script>
        function getDetailKH(ele) {
            var kh_id = ele.id;
            var urlGet = '{{url('admin/customer/show')}}' + '/' + kh_id;
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

        var images = @JSON($imagesArray);

        // function for showing customer images
        function imagesShow(ho_so_id) {
            // set attribute for the images
            $("#img1").attr("src", images[ho_so_id].img1);
            $("#img2").attr("src", images[ho_so_id].img2);
            $("#img3").attr("src", images[ho_so_id].img3);
            $("#fancy-box-img1").attr("href", images[ho_so_id].img1);
            $("#fancy-box-img2").attr("href", images[ho_so_id].img2);
            $("#fancy-box-img3").attr("href", images[ho_so_id].img3);
            // set print route
            $("#print-route").attr("href", "{{Route::has('print_pdf') ? route('print_pdf') : '#' }}" + "?khach_hang_id={{ $account->id }}&ho_so_id=" + ho_so_id);
            // set the title
            $("#title").text("Ảnh chụp thật hồ sơ id : " + ho_so_id);
            // show the modal
            $("#customer-images").modal("show");

        }
    </script>
    <script>
        var i = 1;

        function fancyboxRotation() {
            var n = 90 * ++i;
            $('.fancybox-content img').css('webkitTransform', 'rotate(-' + n + 'deg)');
            $('.fancybox-content img').css('mozTransform', 'rotate(-' + n + 'deg)');
        }
    </script>
@stop
