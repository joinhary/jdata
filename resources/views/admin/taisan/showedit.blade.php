@extends('admin/layouts/default')

{{-- Page title --}}
@section('title')
    Chỉnh sửa tài sản
    @parent
@stop

{{-- page level styles --}}
@section('header_styles')
    <meta name="_token" content="{{ csrf_token() }}">
    <script src="{{asset('assets/js/jquery-3.3.1.min.js')}}"></script>
    <link href="{{ asset('assets/css/pages/toastr.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/vendors/toastr/css/toastr.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/vendors/datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet"
          type="text/css"/>
    <link href="{{ asset('assets/vendors/jasny-bootstrap/css/jasny-bootstrap.css') }}" rel="stylesheet"
          type="text/css"/>
    <link rel="stylesheet" href="{{asset('assets/css/jquery.fancybox.css')}} "/>
    <style>
        .new {
            font-weight: bold;
        }


        .form-control {
            font-size: 13px !important;
        }

        label {
            font-size: 13px !important;
        }

        .form-control {
            font-size: 13px !important;
        }

        .form-group {
            margin-bottom: 5px !important;
        }
    </style>

@stop

{{-- Page content --}}
@section('content')
    <section class="content p-2 pt-1">
        <div class="container">
    <h3 class="mb-4">Cập nhật tài sản</h3>

    <form action="{{ route('updateTaiSan', $id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('POST')

        <div class="row">

            @foreach($tieumuc_nhan as $tm)
                @php
                    $fieldName = 'tm-' . $tm->tm_id;       // input name
                    $oldValue  = $dtb[$tm->tm_id] ?? '';   // giá trị cũ
                @endphp

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">
                        {{ $tm->tm_nhan }}
                        @if($tm->tm_batbuoc == 1) <span class="text-danger">*</span> @endif
                    </label>

                    {{-- ====== TEXT / NUMBER ====== --}}
                    @if($tm->tm_loai == 'text' || $tm->tm_loai == 'number')
                        <input type="{{ $tm->tm_loai }}"
                               class="form-control"
                               name="{{ $fieldName }}"
                               value="{{ $oldValue }}"
                               @if($tm->tm_loai == 'number') step="0.01" @endif
                               @if($tm->tm_batbuoc == 1) required @endif>

                    {{-- ====== TEXTAREA ====== --}}
                    @elseif($tm->tm_loai == 'textarea')
                        <textarea class="form-control"
                                  name="{{ $fieldName }}"
                                  rows="3"
                                  @if($tm->tm_batbuoc == 1) required @endif>{{ $oldValue }}</textarea>

                    {{-- ====== DATE ====== --}}
                    @elseif($tm->tm_loai == 'date')
                        <input type="date"
                               class="form-control"
                               name="{{ $fieldName }}"
                               value="{{ $oldValue }}"
                               @if($tm->tm_batbuoc == 1) required @endif>

                    {{-- ====== FILE UPLOAD ====== --}}
                    @elseif($tm->tm_loai == 'file')
                        <input type="file"
                               class="form-control"
                               name="{{ $fieldName }}[]"
                               multiple>

                        {{-- Hiển thị ảnh / file cũ --}}
                        @if(!empty($oldValue))
                            <div class="mt-2">
                                <label class="small text-muted">Tệp hiện tại:</label> <br>

                                {{-- Nếu là mảng file --}}
                                @if(is_array($oldValue))
                                    @foreach($oldValue as $img)
                                        <a href="/imagesTS/{{ $img }}" target="_blank">
                                            <img src="/imagesTS/{{ $img }}"
                                                 style="width: 90px; margin-right: 6px; border:1px solid #ccc;">
                                        </a>
                                    @endforeach

                                {{-- Nếu chỉ có 1 file --}}
                                @else
                                    <a href="/imagesTS/{{ $oldValue }}" target="_blank">
                                        <img src="/imagesTS/{{ $oldValue }}"
                                             style="width: 90px; border:1px solid #ccc;">
                                    </a>
                                @endif
                            </div>
                        @endif

                    {{-- ====== SELECT (nếu sau này có) ====== --}}
                    @elseif($tm->tm_loai == 'select')
                        <select name="{{ $fieldName }}" class="form-control">
                            <option value="">-- Chọn --</option>
                            {{-- bạn có thể đổ option động ở đây nếu có --}}
                        </select>

                    @endif

                </div>
            @endforeach

        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
            <a href="{{ route('indexTaiSan') }}" class="btn btn-secondary">Hủy</a>
        </div>

    </form>
</div>
    </section>
@stop
@section('footer_scripts')
    <script src="{{asset('assets/js/jquery.fancybox.js')}}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/pages/gallery.js') }}"></script>

    <script>

        $('.thumbnail').bind('DOMNodeInserted', function () {
            $value = $(this).children().attr('src');
            $(this).children().closest('.fancy-box').attr('href', $value);
        });

        $(document).ready(function () {
            let json = @json($dtb??[]);
            let items = @json($tieumuc_nhan??[]);
            for (var i = 0; i < items.length; i++) {
                if (items[i].tm_loai === "select") {
                    items[i].id = items[i].tm_id;
                    change_tm(items[i]);
                }
            }


            function change_tm(value) {
    var id_selected = '#' + value.id;
    var selected_val = $(id_selected).val();
    if (selected_val === undefined) return;

    $.ajax({
        url: "{{ route('getTMSelect') }}",
        method: "GET",
        data: { ktm_id: selected_val },
        success: function(tieumuc) {
            // Xóa các sub trước đó
            $('.add-sub').remove();

            if (tieumuc.data.length === 0) return;

            var current_ele = $(id_selected).closest('.row');

            tieumuc.data.forEach(function(val) {
                var fill_id = '#fill_' + val.tm_id;

                // Thêm row
                $('<div class="row add-sub" id="add-' + val.tm_id + '">' +
                    '<div class="col-md-4 text-right">' +
                    '<label for="' + val.tm_id + '">' + val.tm_nhan + ':</label>' +
                    '<input type="hidden" name="ds_tm[]" value="tm-' + val.tm_id + '">' +
                    '</div>' +
                    '<div class="col-md-8" id="fill_' + val.tm_id + '"></div>' +
                  '</div>').insertAfter(current_ele);

                // Xử lý theo loại
                var oldValue = json[val.tm_id] ?? '';

                if (val.tm_loai === "text") {
                    $(fill_id).append(
                        '<input id="'+val.tm_id+'" type="text" name="tm-'+val.tm_id+'" ' +
                        'class="form-control" placeholder="Nhập '+val.tm_nhan.toLowerCase()+' ..." ' +
                        'value="'+(oldValue ?? '').replace(/"/g,'&quot;')+'">'
                    );
                } 
                else if (val.tm_loai === "select") {
                    $(fill_id).append('<select id="' + val.tm_id + '" class="form-control" name="tm-' + val.tm_id + '"></select>');

                    $.ajax({
                        url: "{{ route('getTMOptions') }}",
                        type: "GET",
                        data: { tm_id: val.tm_id },
                        success: function(options) {
                            var sel_id = '#' + val.tm_id;
                            $.each(options.data, function(key, value_opt) {
                                if (oldValue == key) {
                                    $(sel_id).append('<option value="'+key+'" selected>'+value_opt+'</option>');
                                } else {
                                    $(sel_id).append('<option value="'+key+'">'+value_opt+'</option>');
                                }
                            });
                        }
                    });
                } 
                else if (val.tm_loai === "file") {
                    var files = Array.isArray(oldValue) ? oldValue : (oldValue ? [oldValue] : []);
                    var thumb = files.length > 0 ? '<img src="{{ url('imagesTS') }}/'+files[0]+'" alt="pic">' : '';
                    $(fill_id).append(
                        '<div class="fileinput fileinput-new" data-provides="fileinput">' +
                        '<div class="fileinput-new thumbnail" style="width:100px;height:100px;">'+thumb+'</div>' +
                        '<div class="fileinput-preview fileinput-exists thumbnail" style="max-width:100px;max-height:100px;"></div>' +
                        '<div>' +
                        '<span class="btn btn-primary btn-file">' +
                        '<span class="fileinput-new">Chọn ảnh</span>' +
                        '<span class="fileinput-exists">Thay đổi</span>' +
                        '<input type="file" name="tm-'+val.tm_id+'" class="form-control"/>' +
                        '</span>' +
                        '<a href="#" class="btn btn-danger fileinput-exists" data-dismiss="fileinput">Gỡ bỏ</a>' +
                        '</div></div>'
                    );
                } 
                else { // date / masked input
                    $(fill_id).append(
                        '<input type="text" id="'+val.tm_keywords+'" class="form-control" name="tm-'+val.tm_id+'" ' +
                        'data-mask="99/99/9999" placeholder="Ngày / tháng / năm" ' +
                        'value="'+(oldValue ?? '')+'">'
                    );
                }

                current_ele = '#add-' + val.tm_id; // update cho sub tiếp theo
            });
        }
    });
}

        });

        $('#dien-tich').change(function () {
            $.ajax({
                url: "{{route('readArea')}}",
                type: "get",
                data: {
                    'number': $('#dien-tich').val()
                },
                success: function (res) {
                    if (res.data != false) {
                        $('#tong-dien-tich-bang-chu').val(res.data);

                    } else {
                        $('#tong-dien-tich-bang-chu').val('');

                    }
                }
            })
        });
        $('input:required').focusout(function () {
            if (!$(this).val()) {
                $(this).css('border', '1px solid red');
                $('#valid-' + $(this).attr('id')).text('Vui lòng không để trống!');
            } else {
                $(this).removeAttr('style', 'border');
                $('#valid-' + $(this).attr('id')).text('');
            }
        });

    </script>
    <script src="{{ asset('assets/vendors/moment/js/moment.min.js') }}" type="text/javascript"></script>
    {{--<script src="{{ asset('assets/vendors/daterangepicker/js/daterangepicker.js') }}" type="text/javascript"></script>--}}
    {{--<script src="{{ asset('assets/vendors/datetimepicker/js/bootstrap-datetimepicker.min.js') }}"--}}
    {{--type="text/javascript"></script>--}}
    <script src="{{ asset('assets/vendors/daterangepicker/js/daterangepicker.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/vendors/jasny-bootstrap/js/jasny-bootstrap.js') }}" type="text/javascript"></script>
    {{--<script src="{{ asset('assets/js/pages/datepicker.js') }}" type="text/javascript"></script>--}}
    <script src="{{asset('assets/js/jquery.fancybox.js')}}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/pages/gallery.js') }}"></script>
@stop
