@extends('admin/layouts/default')

{{-- Page title --}}
@section('title')
    Tạo văn bản mới
    @parent
@stop

{{-- page level styles --}}
@section('header_styles')
    <meta name="_token" content="{{ csrf_token() }}">
    <link href="{{ asset('assets/css/pages/editor.css') }}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{ asset('assets/vendors/Buttons/css/buttons.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/css/pages/advbuttons.css') }}"/>
@stop

{{-- Page content --}}
@section('content')

{{--
    <section class="content-header mb-half">
            <h1>Cập nhật Văn bản</h1>
        <a href="javascript:history.back()"><i class="fa fa-arrow-left"></i> Trở lại</a>
    </section>
--}}
    <section class="content" style="padding-top: 10px">
        <form action="{{route('storeVBs2',['id' => $vanban->vb_id])}}" method="POST">
            {{csrf_field()}}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group row">
                        <label for="kieu_hd">Kiểu hợp đồng(*)</label>
                        {!! \App\Helpers\Form::select('vb_kieuhd',$kieuHD,$vanban->vb_kieuhd,['class'=>'form-control']) !!}
                    </div>
                    <div class="form-group row">
                        <label for="nhan-vb">Nhãn(*)</label>
                        <input type="text" id="nhan-vb" name="vb_nhan" required class="form-control" value="{{ $vanban->vb_nhan }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="vb-loai">Loại văn bản(*)</label>{!! \App\Helpers\Form::select('vb_loai',['1'=>'Văn bản','2'=>'Danh mục'],$vanban->vb_loai,['class'=>'form-control']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>
                                Đoạn
                            </th>
                            <th>
                                Xác định vai trò
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                            $vaitro = \App\Models\Admin\Builder\vaitro::pluck('vt_nhan','vt_id');
                    ?>
                        @foreach($vb_doan as $doan)
                            <tr>
                                <td>
                                    {{ $doan->d_nhan }}
                                </td>
                                <td>
                                    {!! \App\Helpers\Form::select('vaitro_'.$doan->d_idfk,
                                    $vaitro,$doan->vaitro,['class'=>'form-control']) !!}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="col-md-6">
                    <div class="form-group">
                    <center> <button type="submit" id="k_btn" class="btn btn-primary" >Lưu</button></center>
                    </div>
                </div>

            </div>
        </form>

    </section>
@stop
@section('footer_scripts')
    <script src="{{ asset('assets/vendors/ckeditor/js/ckeditor.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/vendors/ckeditor/js/jquery.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/vendors/ckeditor/js/config.js') }}" type="text/javascript"></script>

@stop

