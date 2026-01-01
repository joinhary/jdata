@extends('admin.layouts.default')
@section('header_styles')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/fancybox/jquery.fancybox.css') }}" media="screen"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/fancybox/jquery.fancybox-thumbs.css') }}"/>
    <link href="{{ asset('assets/css/pages/toastr.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/vendors/toastr/css/toastr.css') }}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{ asset('assets/vendors/Buttons/css/buttons.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/css/pages/advbuttons.css') }}"/>
    <link href="{{ asset('assets/vendors/jasny-bootstrap/css/jasny-bootstrap.css') }}" rel="stylesheet" type="text/css"/>
  <script src="{{asset('assets\js\jquery-3.3.1.min.js')}}"></script>
  <link rel="stylesheet" href="{{asset('assets\css\jquery.fancybox.css')}} " />
  <script src="{{asset('assets\js\jquery.fancybox.js')}}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/pages/gallery.js') }}"></script>
@endsection
@section('content')
    <section class="content-header" style="margin-bottom: 0px">
        <h1>Tài sản</h1>
        <ol class="breadcrumb">
            <li>
                <a href="{{ route('admin.dashboard') }}">
                    <i class="livicon" data-name="home" data-size="14" data-loop="true"></i>
                    Trang chủ
                </a>
            </li>
            <li>
                <a href="{{ route('indexTaiSan') }}">
                    Tài sản
                </a>
            </li>
            <li class="active">
                Lý lịch
            </li>
        </ol>
    </section>
    <br>
    <div class="row">
        <div class="col-lg-12">
            <div class="portlet box primary">
                <div class="portlet-body">
                    <a href="{{ route('admin.taisan.lichsu.create',$id_ts) }}" class="btn-primary btn pull-right"><i
                                class="fa fa-fw fa-plus-circle"></i> Thêm</a>
                    <div class="table-scrollable">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>
                                    Số HS/VC
                                </th>
                                <th>
                                    Số CC
                                </th>
                                <th>
                                    Số vào sổ
                                </th>
                                <th>Nhãn</th>
                                <th>Ngày ký</th>
                                <th width="25%"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($lylich_taisan)>0)
                                @foreach ($lylich_taisan as $item)
                                    <tr>
                                        <td style="vertical-align: middle;"> {{$num++}}</td>
                                        <td style="vertical-align: middle;">
                                            {{ $item->sohoso }}
                                        </td>
                                        <td style="vertical-align: middle;">
                                            {{ $item->so_cc }}
                                        </td>
                                        <td style="vertical-align: middle;">
                                            {{ $item->so_vaoso }}
                                        </td>
                                        <td @if ($item->tinhtrang == 1) class="text-danger" @endif style="vertical-align: middle;">
                                            {{ $item->mota }}
                                        </td>
                                        <td style="vertical-align: middle;">
                                            {{ \Carbon\Carbon::parse($item->ngayky)->format('d/m/Y') }}
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <ul class="list-inline">

<li class="item-list">
                                                    <a title="Xem hình ảnh" href="javascript:void(0)" id="{{$item->id}}" class="button button-circle button-mid button-action mr-2" onclick="viewImg(this,{{$id_ts}})"><i
                                        class="fa fa-eye"></i></a>
                                                </li>
                                                <li class="item-list">
                                                    <a class="button button-circle button-mid button-info mr-2"
                                                       href="{{ route('admin.taisan.lichsu.edit',[$id_ts,$item->id]) }}"><i class="fa fa-pencil-square-o"></i></a>
                                                </li>
                                                <li class="item-list">
                                                    <form action="{{ route('admin.taisan.lichsu.delete',[$id_ts,$item->id]) }}"
                                                          method="post">
                                                        {{ csrf_field() }}
                                                        {{ method_field('DELETE') }}
                                                        <button class="button button-circle button-mid button-danger" type="submit"><i
                                                                    class="fa fa-trash"></i></button>
                                                    </form>
                                                </li>
                                            </ul>


                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7">
                                        <p style="color: red;">Không có dữ liệu</p>
                                    </td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                        <div class="text-center">
                            {{ $lylich_taisan->links() }}
                        </div>
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
    </div>
@stop
@section('footer_scripts')
 <script src="{{ asset('assets/vendors/jasny-bootstrap/js/jasny-bootstrap.js') }}" type="text/javascript"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/fancybox/jquery.fancybox.pack.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/pages/gallery.js') }}"></script>
    <script>
    function viewImg(img,ls_id) {
            var imgURL = "{{url('images/lylich').'/'}}";
            $.ajax({
                url: "{{route('get-image-ts')}}",
                type: "GET",
                data: {
'id':img.id,
'ls_id':ls_id

},
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
        }</script>
    
@stop