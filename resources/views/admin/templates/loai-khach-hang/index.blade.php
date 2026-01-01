@extends('admin.layouts.default')

{{-- Page title --}}
@section('title')
    Sưu tra    @parent
@stop

{{-- page level styles --}}
@section('header_styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/pages/tables.css') }}"/>
    <link rel="stylesheet" type="text/css"
          href="{{ asset('assets/vendors/datatables/css/dataTables.bootstrap.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/datatables/css/scroller.bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/fancybox/jquery.fancybox.css') }}"
          media="screen"/>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/fancybox/jquery.fancybox-thumbs.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/vendors/Buttons/css/buttons.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/css/pages/advbuttons.css') }}"/>
    <link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/vendors/select2/css/select2-bootstrap.css') }}" rel="stylesheet"/>
    <style>
        .sotuphap html {
            display: none;
        }
    </style>
@stop
<style>
    .fakeimg {
        height: 200px;
        background: #aaa;
    }
</style>
<style type="text/css">
    table, th, td {
        border: 1px solid #868585;
    }

    table {
        width: 100%;
        margin-bottom: 1rem;
        color: #212529;
    }

    th, td {
        text-align: left;
        padding: 10px;
        font-size: 11px;
    }

    table th {
        background-color: #0e5965c2;
        font-size: 11px;

        color: rgb(255, 251, 251)
    }

    .table td, .table th {
        vertical-align: middle !important;
    }
</style>
<style>
    mark {
        padding: 0;
        background-color: #ffe456 !important;
    }

    table {
        table-layout: fixed;
        width: 100%;
    }

    table td {
        word-wrap: break-word; /* All browsers since IE 5.5+ */
        overflow-wrap: break-word; /* Renamed property in CSS3 draft spec */
    }
</style>
{{-- Page content --}}
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-md-10 search">
            </div>
            <div class="col-md-2">
                @if($createDisable)
                    <a href="{{route('admin.templates.loai-khach-hang.create')}}" class="btn btn-primary btn2" style="float: right">
                        <i class="fa fa-plus"></i>
                        Thêm mới
                    </a>
                @endif
            </div>
        </div>
        <div class="row bctk-scrollable-list" style="overflow-x: hidden;">
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th width="20%">Loại tài sản</th>
                    <th>Mẫu thông tin</th>
                    <th width="10%"><i class="fa fa-cog"></i></th>
                </tr>
                </thead>
                <tbody>
                @foreach($templates as $template)
                    <tr>
                        <td align="center"
                            class="column-align">{{ $template->loai_khach_hang ? $template->loai_khach_hang->k_nhan : 'Không có'}}</td>
                        <td class="column-align">{{ $template->template }}</td>
                        <td class="column-align qktd">
                            <a href="{{ route('admin.templates.loai-khach-hang.edit', $template->id) }}"
                               class="btn btn-success">
                                Sửa
                            </a>
                         
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="col-sm-12">
            <div class="col-sm-6">
            </div>
            <div class="col-sm-6">
                <p class="pull-right" style="font-size: 16px;">Tổng số:
                    <b><span style="color: red">{{count($templates)}}</span></b>
                </p>
            </div>
        </div>
    </section>
@stop
@section('footer_scripts')
    <script type="text/javascript" src="{{ asset('assets/js/custom/helper.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/imgPreview.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/jquery.mark.js') }}"></script>
    <script src="{{ asset('assets/vendors/jasny-bootstrap/js/jasny-bootstrap.js') }}" type="text/javascript"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/fancybox/jquery.fancybox.pack.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/pages/gallery.js') }}"></script>
    <script type="text/javascript" src="http://johannburkard.de/resources/Johann/jquery.highlight-5.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js" type="text/javascript"></script>
    <script>
        function confirmDelete(id, name) {
            if (confirm('Xác nhận template mẫu câu cho loại ' + name)) {
                $.post('{{ route("admin.templates.loai-khach-hang.delete", "") }}/' + id,
                    {
                        _token: '{{ csrf_token() }}'
                    },
                    function (data, status, xhr) {
                    })
                    .done(function () {
                        alert('Xóa template mẫu câu cho loại ' + name + ' thành công!');
                        location.reload();
                    })
                    .fail(function (jqxhr, settings, ex) {
                        alert('Xóa template mẫu câu cho loại ' + name + ' không thành công!');
                    });
            }
        }
    </script>
@stop

