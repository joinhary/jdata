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
        .qksao {
            font-weight: bold;
        }

        .qkbtn {
            font-weight: bold;
            font-size: 14px !important;
        }
    </style>
@stop
@section('content')
    <section class="content">
        <form method="POST" action="{{route('storeChiNhanh')}}" class="form-create">
            @csrf
            <div class="row scrollable-list-custom">
                <div class="col-md-6 col-xs-12">
                    <div class="form-group row">
                        <label for="code_cn">Mã văn phòng: (<span class="text-danger qksao">*</span>)</label>
                        <input type="text" id="code_cn" name="code_cn" class="form-control" value="{{old('code_cn')}}"
                               required>
                    </div>
                    <div class="form-group row">
                        <label for="cn_ten">Tên văn phòng: (<span class="text-danger qksao">*</span>)</label>
                        <input type="text" id="cn_ten" name="cn_ten" class="form-control" value="{{old('cn_ten')}}"
                               required>
                    </div>
                    <div class="form-group row">
                        <label for="cn_sdt">Số điện thoại: (<span class="text-danger qksao">*</span>)</label>
                        <input type="text" id="cn_sdt" name="cn_sdt" class="form-control" value="{{old('cn_sdt')}}"
                               required>
                    </div>

                    <div class="form-group ">
                        <label for="cn_tinh" class="col-form-label  text-left">Người đại diện: (<span
                                class="text-danger qksao">*</span>)</label>
                        <input type="text" id="cn_ndd" name="cn_ndd" class="form-control" value="{{old('cn_ndd')}}"
                               required>

                    </div>
                    <div class="form-group row">
                        <label for="cn_diachi">Địa chỉ: </label>
                        <input type="text" id="cn_diachi" name="cn_diachi" class="form-control"
                               value="{{old('cn_diachi')}}">
                    </div>

                    <div class="form-group row">
                        <label for="tinh" class="col-form-label  text-left">Tỉnh/Tp: (<span
                                class="text-danger qksao">*</span>)</label>
                        {!! \App\Helpers\Form::select('cn_tinh',$tinhthanh,old('cn_tinh'),[
                            'id' => 'tinh',
                            'required' => 'required',
                            'class'=>'form-control select2']) !!}
                    </div>
                    <div class="form-group row">
                        <label for="quan" class="col-form-label text-left">Quận/Huyện: (<span
                                class="text-danger qksao">*</span>)</label>
                        {!! \App\Helpers\Form::select('cn_quan',$quanhuyen,old('cn_quan'),[
                            'id' => 'quan',
                            'required' => 'required',
                             'class' => 'form-control select2']) !!}
                    </div>
                    <div class="form-group row">
                        <label for="phuong" class="col-form-label  text-left">Xã/Phường: (<span
                                class="text-danger qksao">*</span>)</label>
                        {!! \App\Helpers\Form::select('cn_phuong',$phuongxa,old('cn_phuong'),[
                            'id' => 'phuong',
                            'required' => 'required',
                            'class' => 'form-control select2']) !!}
                    </div>
                    <div class="form-group row">
                        <label for="ap" class="col-form-label  text-left">Thôn/Ấp/Khu vực: (<span
                                class="text-danger qksao">*</span>)</label>
                        {!! \App\Helpers\Form::select('cn_ap',$ap,old('cn_ap'),[
                            'id' => 'ap',
                            'required' => 'required',
                            'class' => 'form-control select2']) !!}
                    </div>
                    <input type="text" id="lat" name="lat" value="" readonly hidden>
                    <input type="text" id="lng" name="lng" value="" readonly hidden>
                </div>
                <div class="col-md-6 col-xs-12">
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
                    <a href="{{ route('indexChiNhanh') }}" class="btn btn-secondary qkbtn">Hủy</a>
                    <button type="submit" class="btn btn-primary qkbtn">Lưu</button>

                </div>
            </div>
        </form>

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
    </script>
    <script src="{{asset('assets/js/getGeometry.js')}}"></script>
@stop
