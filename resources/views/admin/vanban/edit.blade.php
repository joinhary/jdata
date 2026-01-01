@extends('admin/layouts/default')
@section('title')
    Quản lý văn bản  @parent
@stop
@section('header_styles')
    <meta name="_token" content="{{ csrf_token() }}">
    <link href="{{ asset('assets/css/pages/editor.css') }}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{ asset('assets/vendors/Buttons/css/buttons.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/css/pages/advbuttons.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/select2/css/select2.min.css') }}"/>
    <style>
        .qksao {
            font-weight: bold;
            color: red;
        }

        .qkbtn {
            font-weight: bold;
            font-size: 14px !important;
        }

    </style>
@stop
@section('content')
    <section class="content">
        <form action="{{route('updateVB',['id' => $vanban->vb_id])}}" method="POST">
            @csrf
            <div class="col-md-6">
                <div class="form-group">
                    <label for="kieu_hd">Kiểu hợp đồng (<span class="qksao">*</span>):</label>
                    {!! \App\Helpers\Form::select('vb_kieuhd',$kieuHD,$vanban->vb_kieuhd,['class'=>'form-control']) !!}
                </div>
                <div class="form-group">
                    <label for="nhan-vb">Nhãn (<span class="qksao">*</span>):</label>
                    <input type="text" id="nhan-vb" name="vb_nhan"  class="form-control"
                           value="{{ $vanban->vb_nhan }}">
                </div>
                <div class="form-group">
                    <label for="vb-loai">ID liên kết:</label>
                    <input type="text" id="lien_ket-vb" name="lien_ket"  class="form-control"
                           value="{{ $vanban->lien_ket }}">
                </div>
                <br>
                <div class="form-group">
                    <a href="javascript:history.back()" type="cancel" class="btn btn-secondary qkbtn">Hủy</a>
                    <button id="k_btn" type="submit" class="btn btn-primary qkbtn">Cập nhật</button>
                </div>
            </div>
        </form>
    </section>
@stop
@section('footer_scripts')
    <script src="{{ asset('assets/vendors/ckeditor/js/ckeditor.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/vendors/ckeditor/js/jquery.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/vendors/ckeditor/js/config.js') }}" type="text/javascript"></script>
    <script>
        $('.select').select({width: '100%'});

        function add_condi() {

            var clonedRow = $('tbody tr:first').clone();

            $('#tbody_clone').append(clonedRow);


            $("td:last").html('<a class="btn btn-danger btn-sm" onclick="remov_condi(this)"><i class="fa fa-times"></i></a>')


        }

        function remov_condi(e) {
            console.log($(e).parent().parent().remove());
        }

    </script>
@stop

