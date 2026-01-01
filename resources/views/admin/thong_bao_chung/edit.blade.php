@extends('admin/layouts/default')
@section('title')
    Thông báo @parent
@stop
@section('header_styles')
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
        <div class="col-md-12 bctk-scrollable-list" style="overflow-x: hidden;height: calc(100vh - 100px);">
            <form action="{{ route('updateTBC', ['id' => $thongbaochung->id]) }}" enctype="multipart/form-data" method="POST">
                @csrf
                <input type="hidden" id="vb_doan" name="vb_doan" value="">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="tieu_de">Tiêu đề: (<span class="qksao">*</span>):</label>
                        <input type="text" id="tieu_de-vb" name="tieu_de" class="form-control"
                            value="{{ $thongbaochung->tieu_de }}">
                    </div>
                    <div class="form-group">
                        <label for="so_cv">Số Công văn:</label>
                        <input type="text" id="so_cv" name="so_cv" class="form-control"
                            value="{{ $thongbaochung->so_cv }}">
                        <small id="so_hd_help" class="form-text text-muted">Nhập nếu có để phục vụ tra cứu dễ dàng hơn. Vd:
                            CV
                            123/2023</small>

                    </div>
                    <div class="form-group">
                        <label for="duong_su">Đương sự: :</label>
                        <textarea type="text" id="duongsu" name="duong_su" class="form-control mt-3" rows="7" cols="50">{{ $thongbaochung->duong_su }}</textarea>
                        <small id="so_hd_help" class="form-text text-muted">Nhập nếu có để phục vụ tra cứu dễ dàng hơn. Vd:
                            Ông
                            Nguyễn Văn A 1989,...</small>

                    </div>
                    <div class="form-group">
                        <label for="texte">Tài sản:</label>
                        <textarea type="text" id="duongsu" name="texte" class="form-control mt-3" rows="7" cols="50">{{ $thongbaochung->texte }}</textarea>
                        <small id="so_hd_help" class="form-text text-muted">Nhập nếu có để phục vụ tra cứu dễ dàng hơn. Vd:
                            Đã
                            bị ngăn chặn tài sản XYZ...</small>

                    </div>
                    <div class="form-group">
                        <label for="noi_dung">Nội dung: (<span class="qksao">*</span>):</label>
                        <textarea type="text" class="form-control" rows="3" id="noi_dung" name="noi_dung">{!! $thongbaochung->noi_dung !!}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="name">File hiện tại: </label> <br>
                        @php
                            $ymd = DateTime::createFromFormat('d-m-Y', '05-11-2022')->format('Y-m-d');
                        @endphp
                        @if ($thongbaochung->created_at <= $ymd)
                            <a style="color:blue;" href={{ url('storage/upload_thongbao/' . $thongbaochung->file) }}
                                target="blank"><i class="fa fa-paperclip" aria-hidden="true"></i> {{ $thongbaochung->file }}
                            </a>
                        @else
                           @foreach (json_decode($thongbaochung->file ?? '[]', true) as $img)
    <a href="{{ url('storage/upload_thongbao/' . $img) }}" style="color:#1a67a3" target="_blank">
        <i class="fa fa-paperclip" aria-hidden="true"></i> {{ $img }}
    </a>
    <br>
@endforeach
                        @endif

                    </div>
                    <div class="form-group">
                        <label for="file_upload">Tải lên tập tin: (<span class="qksao">*</span>):</label>
                        <input type="file" id="file" name="file[]" multiple>
                    </div>
                    <div class="form-group">
                        <label for="vp_id">Văn phòng áp dụng: (<span class="qksao">*</span>):</label>
                        {!! \App\Helpers\Form::select('vp_id', $chi_nhanh_all, $thongbaochung->vp_id, ['class' => 'form-control', 'id' => 'vp_id']) !!}
                    </div>

                    <div class="form-group">
                        <label for="push">Thời gian hiển thị ngoài màn hình: (<span class="qksao">*</span>):</label>
                        {!! \App\Helpers\Form::select('push', $push, $thongbaochung->push, ['class' => 'form-control', 'id' => 'push']) !!}
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
        </div>
    </section>
@stop
@section('footer_scripts')
    <script src="{{ asset('assets/vendors/nestable-list/jquery.nestable.js') }}"></script>
    <script src="{{ asset('assets/vendors/html5sortable/html.sortable.js') }}"></script>
    <script src="{{ asset('assets/js/nestable_list_custom.js') }}"></script>
    <script src="//cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('noi_dung');
    </script>
@stop
