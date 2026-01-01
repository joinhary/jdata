@extends('admin/layouts/default')
@section('title')
    Quản lý tài sản @parent
@stop
@section('header_styles')
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

        th, td {
            text-align: left;
            padding: 5px !important;
            font-size: 13px;
        }
    </style>
@stop
@section('content')
    <section class="content p-2 pt-1">
        <div class="row scrollable-list-custom">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading" style="background-color: #d9edf7; color: #31708f;font-weight: bold">
                        <h4 style="text-align: center;font-weight: bold">THÔNG TIN TÀI SẢN</h4><br>
                        DỮ LIỆU TỪ VĂN PHÒNG : {{ \App\Models\ChiNhanhModel::where('cn_id',$taisan->id_vp)->first()->cn_ten }}<br>
                        NGƯỜI NHẬP : {{ \App\Models\User::where('id',$taisan->id_ccv)->first()->first_name }}
                        ( Chức vụ
                        : {{ \App\Models\RoleModel::where('id',\App\Models\RoleUsersModel::where('user_id',$taisan->id_ccv)->first()->role_id)->first()->name }} )
                    </div>
                    <div class="panel-body">
                        <table class="table table-bordered table-hover mb-0">
                            @foreach($data as $value)
                                <tr>
                                    <td class="text-right" style="width: 30%">{{$value['tm_nhan']}}
                                        @if($value['tm_batbuoc'] == 1)
                                            <span style="color: red">( * ) </span>
                                        @endif
                                        :
                                    </td>
                                    <td style="width: 70%">
                                        @if($value['tm_loai'] == 'file')
                                            <div class="col-md-2 mb-2 mt-1">
                                                <div class="col-md-2 mb-2 mt-1">
                                                    <a data-fancybox="images"
                                                       href="{{url('imagesTS/'.$value['ts_giatri'])}}">
                                                        <img src="{{url('imagesTS/'.$value['ts_giatri'])}}" width="50"
                                                             height="50">
                                                    </a>
                                                </div>
{{--                                                <img @if(!empty($value['ts_giatri']))--}}
{{--                                                     src="{{url('imagesTS/'.$value['ts_giatri'])}}"--}}
{{--                                                     @else--}}
{{--                                                     src="http://placehold.it/100x100"--}}
{{--                                                     @endif--}}
{{--                                                     alt="profile pic" width="50"--}}
{{--                                                     height="50">--}}
                                            </div>
                                        @else
                                            <div>
                                                @if(!empty($value['ts_giatri']))
                                                    {{$value['ts_giatri']}}
                                                @else
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
            {{--            <div class="col-md-3 text-center" >--}}
            {{--                <div class="panel panel-primary" >--}}
            {{--                    <div class="panel-heading" style="background-color: #d9edf7; color: #31708f;font-weight: bold">--}}
            {{--                        THÔNG TIN NGƯỜI NHẬP--}}
            {{--                    </div>--}}
            {{--                    <div class="panel-body">--}}
            {{--                    </div>--}}
            {{--                </div>--}}
            {{--            </div>--}}
        </div>
    </section>

@stop
@section('footer_scripts')
    <script>
        var i = 1;

        function fancyboxRotation() {
            var n = 90 * ++i;
            $('.fancybox-content img').css('webkitTransform', 'rotate(-' + n + 'deg)');
            $('.fancybox-content img').css('mozTransform', 'rotate(-' + n + 'deg)');
        }
    </script>
    <script type="text/javascript" src="{{ asset('assets/vendors/fancybox/jquery.fancybox.pack.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/pages/gallery.js') }}"></script>
    <script src="{{ asset('assets/vendors/moment/js/moment.min.js') }}" type="text/javascript"></script>
    <script language="javascript" type="text/javascript"
            src="{{ asset('assets/vendors/select2/js/select2.js') }}"></script>
    <script src="{{ asset('assets/vendors/jasny-bootstrap/js/jasny-bootstrap.js') }}" type="text/javascript"></script>
    <script type="text/javascript" src="{{ asset('assets/js/imgPreview.js') }}"></script>
@endsection
