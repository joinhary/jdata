@extends('admin/layouts/default')

{{-- Page title --}}
@section('title')
DTN    @parent
@stop

{{-- page level styles --}}
@section('header_styles')
    <meta name="_token" content="{{ csrf_token() }}">
    <link href="{{ asset('assets/css/pages/toastr.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/vendors/toastr/css/toastr.css') }}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{ asset('assets/vendors/Buttons/css/buttons.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/css/pages/advbuttons.css') }}"/>
    <link href="{{ asset('assets/vendors/jasny-bootstrap/css/jasny-bootstrap.css') }}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/fancybox/jquery.fancybox.css') }}" media="screen"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/fancybox/jquery.fancybox-thumbs.css') }}"/>
    <script src="{{asset('assets\js\jquery-3.3.1.min.js')}}"></script>
    <link rel="stylesheet" href="{{asset('assets\css\jquery.fancybox.css')}} " />
    <script src="{{asset('assets\js\jquery.fancybox.js')}}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/pages/gallery.js') }}"></script>

@stop

{{-- Page content --}}
@section('content')

    {{--
        <section class="content-header">
            <h1>Lý lịch khách hàng <b>{{$khachhang->first_name}}</b></h1>
            <a href="{{route('indexKhachHang')}}"><i class="fa fa-arrow-left"></i> Trở lại</a>
        </section>
    --}}
    <section class="content">
        <div class="row pr-2">
            <a href="{{route('createLyLich',['idKH' => $khachhang->id])}}" class="btn btn-primary pull-right mb-0">
                <i class="fa fa-plus"></i>
                Thêm mới
            </a>
        </div>
        <div class="row p-1">
            <table class="table table-bordered table-hover mb-0">
                <thead>
                <tr>
                    <th rowspan="2" class="v-align">Số HS/CV</th>
                    <th rowspan="2">Số công chứng</th>
                    <th rowspan="2">Số vào sổ</th>
                    <th rowspan="2" class="v-align">Mô tả</th>
                    <th rowspan="2" class="v-align">Ngày ký</th>
                    <th rowspan="2" class="v-align">Tình trạng</th>
                    <th rowspan="2" class="v-align">
                        <i class="fa fa-cog"></i>
                    </th>
                </tr>

                </thead>
                <tbody>
                @foreach($lylich as $item)
                    <tr>
                        <td id="hoso-{{$item->id}}">{{$item->sohoso}}</td>
                        <td>@if ($item->so_cc)
                                {{$item->so_cc}}
                            @else
                                -
                            @endif
                        </td>
                        <td>{{$item->so_vaoso}}</td>
                        <td @if ($item->tinhtrang == 1) class="text-danger" @endif >{{$item->mota}}</td>
                        <td>{{date('d/m/Y', strtotime($item->ngayky))}}</td>
                        <td @if ($item->tinhtrang == 1) class="text-danger" @endif >
                            @switch($item->tinhtrang)
                                @case (1) <b><u>NGĂN CHẶN</u></b> @break
                                @case (2) <b>GIẢI CHẤP</b> @break
                                @case (3) <b>GIẢI TỎA</b>@break
                            @endswitch
                        </td>
                        <td>
                            <a title="Xem hình ảnh" href="javascript:void(0)" id="{{$item->id}}" class="button button-circle button-mid button-action mr-2" onclick="viewImg(this)"><i
                                        class="fa fa-eye"></i></a>
                            <a title="Cập nhật lý lịch" href="{{route('editLyLich',['id'=>$item->id])}}" class="button button-circle button-mid button-info mr-2"><i class="fa fa-pencil-square-o"></i></a>
                            <a title="Xóa lý lịch" href="javascript:void(0)" id="{{$item->id}}" class="button button-circle button-mid button-danger" onclick="del_confirm(this)"><i
                                        class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="row text-center">
                {{$lylich->links()}}
            </div>
            <div class="modal fade" id="confirm-delhistory" role="dialog" aria-labelledby="modalLabeldanger">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-danger">
                            <h4 class="modal-title" id="modalLabeldanger">Chú ý!</h4>
                        </div>
                        <form action="{{route('deleteLyLich')}}" id="form-del" method="post">
                            <div class="modal-body">
                                <p>Bạn có thực sự muốn xóa lịch sử số <span id="confirm-info"></span>?</p>
                                {{csrf_field()}}
                                <input type="text" id="lylich_id" name="id" hidden>
                            </div>
                            <div class="modal-footer">
                                <button id="submit-delete" type="submit" class="btn btn-danger mb-0">Có, xóa!</button>
                                <a href="#" data-dismiss="modal" class="btn btn-warning">Không</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="images-modal" role="dialog" aria-labelledby="modalLabelprimary">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h4 class="modal-title" id="modalLabeldanger">Xem các ảnh lý lịch</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row text-center" id="image-dialog"></div>
                        </div>
                        <div class="modal-footer">
                            <a href="javascript:void(0)" data-dismiss="modal" class="btn btn-warning">Đóng</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            {{$lylich->links()}}
        </div>
    </section>
@stop
@section('footer_scripts')
    <script src="{{ asset('assets/vendors/jasny-bootstrap/js/jasny-bootstrap.js') }}" type="text/javascript"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/fancybox/jquery.fancybox.pack.js') }}"></script>
    <script>
        function del_confirm(val) {
            var id = val.id;
            var sohoso = $('#hoso-' + id).text();
            $('#confirm-info').html('<b>"' + sohoso + '"</b>');
            $('#lylich_id').val(id);
            $('#confirm-delhistory').modal('show');
        }

        function viewImg(img) {
            var dataVar = 'id=' + img.id;
            var imgURL = "{{url('images/lylich').'/'}}";
            $.ajax({
                url: "{{route('getImage')}}",
                type: "GET",
                data: dataVar,
                success: function (res) {
                    $('#image-dialog').empty();
                    $.each(res.data, function (k, v) {
                        $('#image-dialog').append(
                            '<div class="col-md-3 gallery-border">' +
                            '<div class="fileinput-new thumbnail" style="width: 110px; height: 110px;">' +
                            '<a data-fancybox="images"    href="' + imgURL + v + '">' +
                            '<img src="' + imgURL + v + '" style="width: 100px; height: 100px;">' +
                            '</a>' +
                            '</div>' +
                            '</div>'
                        );
                        $('#images-modal').modal('show');
                    })
                }
            });
        }
    </script>
    <script>
        var i=1;
        function fancyboxRotation(){
            var n = 90 * ++i;
            $('.fancybox-content img').css('webkitTransform', 'rotate(-' + n + 'deg)');
            $('.fancybox-content img').css('mozTransform', 'rotate(-' + n + 'deg)');
        }
    </script>
@stop

