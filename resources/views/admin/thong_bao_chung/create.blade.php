@extends('admin/layouts/default')
@section('title')
    Thông báo @parent
@stop
@section('header_styles')
    <meta http-equiv="Content-Type" content="text/plain; charset=utf-8">

    <link href="{{ asset('assets/css/pages/sortable.css') }}" rel="stylesheet" />
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
        <form action="{{ route('storeTBC') }}" method="POST" enctype="multipart/form-data" accept-charset="utf-8">
            @csrf
            <input type="hidden" id="vb_doan" name="vb_doan" value="">
            <div class="col-md-12">
                {{-- <div class="form-group">
                  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
					Tải lên tập tin
					</button>
                </div> --}}
                <!-- Modal -->

            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="tieu_de">Tiêu đề: (<span class="qksao">*</span>):</label>
                    <input type="text" id="tieu_de-vb" name="tieu_de" class="form-control" value="">
                </div>
                <div class="form-group">
                    <label for="so_cv">Số Công văn:</label>
                    <input type="text" id="so_cv" name="so_cv" class="form-control" value="">
                    <small id="so_hd_help" class="form-text text-muted">Nhập nếu có để phục vụ tra cứu dễ dàng hơn. Vd: CV
                        123/2023</small>

                </div>
                <div class="form-group">
                    <label for="duong_su">Đương sự: :</label>
                    <textarea type="text" id="duongsu" name="duong_su" class="form-control mt-3" rows="7" cols="50"></textarea>
                    <small id="so_hd_help" class="form-text text-muted">Nhập nếu có để phục vụ tra cứu dễ dàng hơn. Vd: Ông
                        Nguyễn Văn A 1989,...</small>

                </div>
                <div class="form-group">
                    <label for="texte">Tài sản:</label>
                    <textarea type="text" id="duongsu" name="texte" class="form-control mt-3" rows="7" cols="50"></textarea>
                    <small id="so_hd_help" class="form-text text-muted">Nhập nếu có để phục vụ tra cứu dễ dàng hơn. Vd: Đã
                        bị ngăn chặn tài sản XYZ...</small>

                </div>
                <div class="form-group">
                    <label for="noi_dung">Nội dung: (<span class="qksao">*</span>):</label>
                    <textarea type="text" class="form-control" rows="3" id="noi_dung" name="noi_dung"></textarea>
                </div>
                <div class="form-group">
                    <label for="vp_id_edit">Văn phòng áp dụng: (<span class="qksao">*</span>):</label>
                    <select name="vp_id" required id="vb-vp_id_edit" class="form-control">
                        <option value=" "> Tất cả</option>
                        @foreach ($chi_nhanh_all as $cn)
                            <option value="{{ $cn->cn_id }}"> {{ $cn->cn_ten }} </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="file_upload">Tải lên tập tin: (<span class="qksao">*</span>):</label>
                    <input type="file" id="file" name="file[]" multiple>
                </div>
                <div class="form-group">
                    <label for="push">Thời gian hiển thị ngoài màn hình: (<span class="qksao">*</span>):</label>
                   {!! \App\Helpers\Form::select('push', $push, null, ['class' => 'form-control', 'id' => 'push']) !!}
                </div>
                @if (Sentinel::inRole('admin'))
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="type" id="flexCheckDefault">
                            <label class="form-check-label" for="flexCheckDefault">
                                thông báo ngăn chặn
                            </label>
                        </div>
                    </div>
                @endif
                <br>
                <div class="form-group">
                    <a href="javascript:history.back()" type="cancel" class="btn btn-secondary qkbtn">Hủy</a>
                    <button type="submit" class="btn btn-primary qkbtn">Lưu</button>
                </div>
            </div>
        </form>
    </section>

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Tải lên tập tin</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="get-link-form" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="file" name="image" class="form-control">
                        <br>
                        <input type="text" id="link" class="form-control">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="button" onclick="getLink()" class="btn btn-primary">Lấy đường dẫn</button>
                </div>
            </div>
        </div>
    </div>
@stop
@section('footer_scripts')
    <script src="{{ asset('assets/vendors/nestable-list/jquery.nestable.js') }}"></script>
    <script src="{{ asset('assets/vendors/html5sortable/html.sortable.js') }}"></script>
    <script src="{{ asset('assets/js/nestable_list_custom.js') }}"></script>
    {{--    <script src="{{ asset('assets/vendors/ckeditor/ckeditor.js') }}"></script> --}}
    <script src="//cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('noi_dung');

        function getLink() {
            let myForm = document.getElementById('get-link-form');
            let formData = new FormData(myForm);

            $.ajax({
                url: "{{ url('api/upload-file') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                // async: false,
                success: function(response) {
                    console.log(response)
                    $('#link').val(response['link'])
                }
            })
        }
    </script>

@stop
