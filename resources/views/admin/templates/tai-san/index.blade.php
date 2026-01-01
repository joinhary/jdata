@extends('admin/layouts/default')
@section('title')
    Mẫu nhập liệu tài sản @parent
@stop
@section('header_styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/pages/tables.css') }}"/>
    <link rel="stylesheet" type="text/css"
          href="{{ asset('assets/vendors/datatables/css/dataTables.bootstrap.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/datatables/css/scroller.bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/pages/tables.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/vendors/Buttons/css/buttons.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/css/pages/advbuttons.css') }}"/>
    <style type="text/css">
        .btn1 {
            font-weight: 500;
            background-color: white !important;
            color: #01bc8c !important;
            font-size: 14px !important;
        }

        .qktd {
            text-align: center;
        }

        .btn2 {
            font-weight: 500;
            background-color: white !important;
            color: #1a67a3 !important;
            font-size: 14px !important;
        }

        .bg-danger {
            background: #e74040 !important;
        }

    </style>
@stop
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-md-10 search">
            </div>
            <div class="col-md-2">
                @if($createDisable)
                    <a href="{{route('admin.templates.tai-san.create')}}" class="btn btn-primary btn2" style="float: right">
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
                            class="column-align">{{ $template->kieu_tai_san ? $template->kieu_tai_san->k_nhan : 'Không có'}}</td>
                        <td class="column-align">{{ $template->template }}</td>
                        <td class="column-align qktd">
                            <a href="{{ route('admin.templates.tai-san.edit', $template->id) }}"
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
    <script>
        function confirmDelete(id, name) {
            if (confirm('Xác nhận template mẫu câu cho loại ' + name)) {
                $.post('{{ route("admin.templates.tai-san.delete", "") }}/' + id,
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
