@extends('admin/layouts/default')
@section('title')
    Quản lý văn phòng  @parent
@stop
@section('header_styles')
    <meta name="_token" content="{{ csrf_token() }}">
    <link href="{{ asset('assets/css/pages/toastr.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/vendors/toastr/css/toastr.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/vendors/select2/css/select2-bootstrap.css') }}" rel="stylesheet"/>
    <style>
        .qkbtn {
            font-weight: bold;
            font-size: 14px !important;
        }

        .qksao {
            font-weight: bold;
        }
    </style>
@stop
@section('content')
    <section class="content">
        <form method="POST" action="{{route('updateChiNhanh',['id'=>$chinhanh->cn_id])}}" style="padding-left: .5em">
            @csrf
            <div class="row scrollable-list-custom">
                <div class="col-md-6 col-xs-12">
                    <div class="form-group row">
                        <label for="code_cn">Mã văn phòng: (<span class="text-danger qksao">*</span>)</label>
                        <input type="text" id="code_cn" name="code_cn" class="form-control" value="{{$chinhanh->code_cn}}"
                               required>
                    </div>
                    <div class="form-group row">
                        <label for="cn_ten">Tên văn phòng: (<span class="text-danger qksao">*</span>)</label>
                        <input type="text" id="cn_ten" name="cn_ten" class="form-control" value="{{$chinhanh->cn_ten}}"
                               required>
                    </div>
                    <div class="form-group row">
                        <label for="cn_sdt">Số điện thoại: (<span class="text-danger qksao">*</span>)</label>
                        <input type="text" id="cn_sdt" name="cn_sdt" class="form-control" value="{{$chinhanh->cn_sdt}}"
                               required>
                    </div>
                    <div class="form-group ">
                        <label for="cn_tinh">Người đại diện: (<span class="text-danger qksao">*</span>)</label>
                        <input type="text" id="cn_ndd" name="cn_ndd" class="form-control" value="{{$chinhanh->cn_ndd}}"
                               required>
                    </div>
                    <div class="form-group row">
                        <input type="text" id="cn_diachi" name="cn_diachi" class="form-control"
                               value="{{$chinhanh->cn_diachi}}">
                    </div>
                    <div class="form-group row">
                        <label for="login_code">Mã đăng nhập: </label>

                        <input name="login_code" class="form-control" value="{{$chinhanh->login_code}}" required>

                    </div>
                    <div class="form-group ">
                        <label for="cn_tinh">Tỉnh/Tp: (<span class="text-danger qksao">*</span>)</label>
                        {!! \App\Helpers\Form::select('cn_tinh',$tinhthanh,$chinhanh->cn_tinh,['id' => 'tinh','required' => 'required','class'=>'form-control select2']) !!}
                    </div>
                    <div class="form-group row">
                        <label for="cn_quan">Quận/Huyện: (<span class="text-danger qksao">*</span>)</label>
                        {!! \App\Helpers\Form::select('cn_quan',$quanhuyen,$chinhanh->cn_quan,['id' => 'quan','required' => 'required', 'class' => 'form-control']) !!}
                    </div>
                    <div class="form-group row">
                        <label for="cn_phuong">Xã/Phường: (<span class="text-danger qksao">*</span>)</label>
                        {!! \App\Helpers\Form::select('cn_phuong',$phuongxa,$chinhanh->cn_phuong,['id' => 'phuong','required' => 'required', 'class' => 'form-control']) !!}
                    </div>
                    <div class="form-group row">
                        <label for="cn_ap">Thôn/Ấp/Khu vực: (<span class="text-danger qksao">*</span>)</label>
                        {!! \App\Helpers\Form::select('cn_ap',$ap,$chinhanh->cn_ap,['id' => 'ap','required' => 'required', 'class' => 'form-control select2']) !!}
                    </div>

                    <input type="text" id="lat" name="lat" @if($chinhanh->lat == 1)value=""
                           @else value="{{$chinhanh->lat}}" @endif readonly hidden>
                    <input type="text" id="lng" name="lng" @if($chinhanh->lng == 1)value=""
                           @else value="{{$chinhanh->lng}}" @endif readonly hidden>
                </div>
                <div class="col-md-6 col-xs-12" style="padding-top: 10px">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <span>Bản đồ (tùy chỉnh lại vị trí nếu có sai lệch)</span>
                        </div>
                        <div class="panel-body">
                            <div id="map" style="width: 100%; height: 485px"></div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12" align="center"><br>
                    <a href="javascript:history.back()" class="btn btn-secondary qkbtn">Hủy</a>
                    <button type="submit" class="btn btn-primary qkbtn">Cập nhật</button>
                </div>
            </div>
            <div class="modal fade" id="confirm-edit" role="dialog" aria-labelledby="modalLabelinfo">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-info">
                            <h4 class="modal-title" id="modalLabelinfo">Thông báo!</h4>
                        </div>
                        <div class="modal-body">
                            <p>Bạn có thực sự muốn cập nhật thông tin văn phòng này?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" id="btn-update" class="btn  btn-info">Cập nhật!</button>
                            <a href="#" data-dismiss="modal" class="btn  btn-info">Đóng</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!--Notice about changing address information modal-->
        <div class="modal fade" id="modal-20" role="dialog" aria-labelledby="modalLabelwarn">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h4 class="modal-title" id="modalLabelwarn">Lưu ý!</h4>
                    </div>
                    <div class="modal-body">
                        <p>
                            Bạn vừa thay đổi địa chỉ văn phòng, vui lòng kiểm tra và xác thực các vấn đề sau:
                        </p>
                        <ul>
                            <li>
                                Tính đúng đắn của địa chỉ hiện tại.
                            </li>
                            <li>
                                Kiểm tra lại thông tin trên bản đồ xem con dấu đặt đúng vị trí chưa.
                            </li>
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button class="btn  btn-warning" data-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
@section('footer_scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key=&callback=initMap" async defer></script>
    <script language="javascript" type="text/javascript"
            src="{{ asset('assets/vendors/select2/js/select2.js') }}"></script>
    <script src="{{asset('assets/js/map_handling.js')}}"></script>
    <script>
        var geometryURL = "{{route('getGeometry')}}";
        $('#tinh, #quan, #phuong, #ap, #cn_ndd').select2();
        $('#cn_diachi').change(function () {
            var diachi = '{{$chinhanh->cn_diachi}}';
            var curr_diachi = $(this).val();
            if (curr_diachi !== diachi) {
                $('#modal-20').modal('show');
            }
        });
        $('#ap').change(function () {
            $('#modal-20').modal('show');
        });
        $('#btn-update').click(function () {
            $('form').submit();
        });
        $('#cn_diachi, #tinh, #quan, #phuong, #ap').change(function () {
            $.ajax({
                url: 'https://maps.googleapis.com/maps/api/geocode/json?address=' + $('#cn_diachi').val() + ',' + $('#phuong option:selected').text() + ',' + $('#quan option:selected').text() + ',' + $('#tinh option:selected').text(),
                success: function (data) {
                    var lat = data.results[0].geometry.location.lat;
                    var lng = data.results[0].geometry.location.lng;
                    $('#lat').val(lat);
                    $('#lng').val(lng);
                    initMap(lat, lng);
                }
            })
        });
    </script>
    <script src="{{asset('assets/js/getGeometry.js')}}"></script>
@stop
