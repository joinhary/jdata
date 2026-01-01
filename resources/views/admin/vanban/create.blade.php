@extends('admin/layouts/default')
@section('title')
    Quản lý văn bản @parent
@stop
@section('header_styles')
    <link href="{{ asset('assets/css/pages/sortable.css') }}" rel="stylesheet"/>
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
        <form action="{{route('storeVB')}}" method="POST">
            @csrf
            <input type="hidden" id="vb_doan" name="vb_doan" value="">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="kieu_hd">Kiểu hợp đồng (<span class="qksao">*</span>):</label>
                    {!! \App\Helpers\Form::select('vb_kieuhd',$kieuhd,old('vb_kieuhd'),['class'=>'form-control select2']) !!}
                </div>
                <div class="form-group">
                    <label for="nhan-vb">Nhãn (<span class="qksao">*</span>):</label>
                    <input type="text" id="nhan-vb" required name="vb_nhan" class="form-control" value="">
                </div>
                <div class="form-group">
                    <label for="vb-loai">ID liên kết:</label>
                    <input type="text" id="lien_ket-vb" name="lien_ket"  class="form-control"
                    >
                </div>
                <br>
                <div class="form-group">
                    <a href="{{ route('indexVB') }}" type="cancel" class="btn btn-secondary qkbtn">Hủy</a>
                    <button type="submit" class="btn btn-primary qkbtn">Lưu</button>
                </div>
            </div>
        </form>
    </section>
@stop
@section('footer_scripts')
    <script>
        function getFullKieuHD() {
            $.ajax({
                url: '{{route('admin.kieuhopdongs.index')}}',
                type: 'GET',
                success: function (dieukhoan) {
                    dieukhoan.data.map(function (val) {
                        $('#kieu_hd').append('<option value="' + val.id + '" >' + val.kieu_hd + '</option>');
                    });
                }
            })
        }

        getFullKieuHD();

        function getListSub() {
            var doan_dk = '';
            $('#sortable2>li').each(function () {
                doan_dk += $(this).attr('id') + ' '
            });
            $('#vb_doan').val(doan_dk);
        }

        var UINestable = function () {
            return {
                //main function to initiate the module
                init: function () {
                    $('#sortable2, #sortable3').sortable({
                        connectWith: '.connected'
                    });
                }
            };
        }();
        getFullDoan()
        $('#d_nhan').on('keyup', function () {
            var nhan = $('#d_nhan').val();
            $('#sortable3').html('');
            getFullDoan(nhan);
        })

        $(document).ready(function () {
            $('#refr_list').click(function () {
                getListSub();
                $('#k_btn').removeProp('disabled');
                toastr.success('Cập nhật danh sách thành công!',
                    toastr.options = {
                        "closeButton": false,
                        "debug": true,
                        "newestOnTop": false,
                        "progressBar": true,
                        "positionClass": "toast-bottom-right",
                        "preventDuplicates": true,
                        "showDuration": "300",
                        "hideDuration": "1000",
                        "timeOut": "3000",
                        "extendedTimeOut": "1000",
                        "showEasing": "swing",
                        "hideEasing": "linear",
                        "showMethod": "fadeIn",
                        "hideMethod": "fadeOut"
                    }
                );
            });

        });

    </script>
    <script src="{{ asset('assets/vendors/nestable-list/jquery.nestable.js') }}"></script>
    <script src="{{ asset('assets/vendors/html5sortable/html.sortable.js') }}"></script>
    <script src="{{ asset('assets/js/nestable_list_custom.js') }}"></script>
@stop
