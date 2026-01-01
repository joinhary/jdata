@extends('admin/layouts/default')

{{-- Page title --}}
@section('title')
    Thông báo
    @parent
@stop


{{-- page level styles --}}
@section('header_styles')
    <link rel="stylesheet" href="{{ asset('assets/vendors/animate/animate.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/datatables/css/dataTables.bootstrap.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/datatables/css/scroller.bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/pages/tables.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/Buttons/css/buttons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/pages/advbuttons.css') }}" />
    {{--    <link href="{{asset('/assets/vendors/summernote/summernote.css')}}" rel="stylesheet"> --}}

    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">
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
    <style type="text/css">
        table.dataTable tfoot th {
            text-align: right;
        }

        #hopdong-table {
            border: 1px solid #868585;
        }

        #hopdong-table {
            border-collapse: collapse;
            width: 100%;
            margin: 3px;
        }

        textarea {
            resize: vertical;
        }

        th,
        td {
            text-align: center;
            font-size: 13px;
        }

        input,
        select {
            border-radius: 0 !important;
        }

        .select2 {
            width: 100% !important;
        }

        #hopdong-table tr:nth-child(odd) {
            background-color: #eee;
            font-size: 13px;
            height: 50px;
        }

        #hopdong-table tr:nth-child(even) {
            background-color: white;
            font-size: 13px;
            height: 50px;
        }

        #hopdong-table th {
            background-color: #138165;
            font-size: 12px;
            color: rgb(255, 251, 251);
        }

        #service-fees th {
            background-color: #138165;
            font-size: 12px;
            color: rgb(255, 251, 251);
        }

        #hopdong-table tr:hover {
            background: #1bb38d;
            color: white;
        }

        mark {
            padding: 0;
            background-color: #ffe456 !important;
        }

        #hopdong-table td {
            text-align: center;
            vertical-align: middle;
        }

        #hopdong-table th {
            text-align: center;
            vertical-align: middle;
        }
    </style>
@stop

{{-- Page content --}}
@section('content')
    <section class="content">


        {!! \App\Helpers\Form::open(['method' => 'get']) !!}
        <div class="form-group">
            <div class="row">
                <div class="col-md-4">
                    <label for="">Đương sự</label>
                    <input type="text" class="form-control" name="" id="" aria-describedby="helpId"
                        placeholder="">
                </div>
                <div class="col-md-4">
                    <label for="">Tài sản</label>
                    <input type="text" class="form-control" name="" id="" aria-describedby="helpId"
                        placeholder="">
                </div>
                <div class="col-md-4">
                    <label for="">Trạng thái</label>
                    {!! \App\Helpers\Form::select('status', ['' => 'Trạng thái', '1' => 'Đã duyệt', '0' => 'Chưa duyệt'], null, ['class' => 'form-control']) !!}

                </div>
            </div>
            <div class="col-md-4" style="margin-top: 8px;">
                <button class="btn btn-info" id="search2" type="submit" onclick="highlight('a')">
                    <i class="fa fa-search"></i>
                    Tìm kiếm
                </button>
                <button id="btnclear" type="button" class="btn btn-danger" onclick="clearValue()"> <i
                        class="fa fa-trash"></i> Xóa</button>
                <a id="btnprint" type="button" class="btn btn-warning" href="{{ route('createTBC') }}"> <i
                        class="fa fa-plus"></i> Thêm mới </a>

            </div>
            {{-- <div class="row">
                <div class="col-md-4">

                    <button class="form-control btn btn-outline-success" type="submit"
                        style="margin-bottom: 10px !important;">
                        <i class="fa fa-search"></i>Tìm kiếm
                    </button>
                </div>
                <div class="col-md-4">

                    <a href="{{ route('adminIndex') }}" class="form-control btn btn-outline-danger"
                        style="margin-bottom: 10px !important;" type="reset">
                        <i class="fa fa-trash"></i> Xóa tìm kiếm</a>
                </div>
                <div class="col-md-4">

                    <a href="{{ route('createTBC') }}" class="form-control btn btn-outline-primary"
                        style="margin-bottom: 10px">
                        <i class="fa fa-plus"></i>Thêm mới</a>
                </div>
            </div> --}}

        </div>


        {!! \App\Helpers\Form::close() !!}
        <div class="row">
            @if (request()->input('tu_ngay') == null &&
                    request()->input('den_ngay') == null &&
                    request()->input('nv_id') == null &&
                    request()->input('vp_id') == null)
                <a></a>
            @else
               
                <a>Có <span style="color : red; font-weight: bold;">{{ $count }}</span> kết quả được tìm thấy.</a>
            @endif
        </div>
        <div class="row bctk-scrollable-list" style="overflow-x: hidden;height: calc(72vh - 100px);">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th style="width: 10%">Văn phòng áp dụng</th>
                        <th style="width: 40%">Tiêu đề</th>
                        <th style="width: 10%">Người tạo</th>
                        <th style="width: 10%">Ngày tạo</th>
                        <th style="width: 10%">Thời gian hiển thị</th>
                        <th style="width: 20%"><i class="fa fa-cog"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($thong_bao_chung as $tbc)
                        <tr>
                            <td class="column-align">{{ $tbc->cn_ten ?? 'Tất cả' }}</td>
                            <td class="column-align">{{ $tbc->tieu_de }}</td>
                            <td class="text-left">{{\App\Models\NhanVienModel::find($tbc->nv_id)->nv_hoten }}</td>
                            <td class="column-align">{{ $tbc->created_at->format('Y-m-d') }}</td>
                            <td class="column-align">
                                @if ($tbc->push)
                                    @if ($tbc->push == 9999)
                                        Luôn hiển Thị
                                    @else
                                        {{ $tbc->push }} ngày
                                    @endif
                                @else
                                    Không hiển Thị
                                @endif
                            </td>
                            <td class="column-align qktd" style="width: 200px;">
                                <a title="Chi tiết nhân viên" href="{{ route('showTBC', $tbc->id) }}"
                                    class="btn btn-primary">
                                    Xem
                                </a>
                                @if ($user->id == $tbc->nv_id || $user->isAdmin())
                                    <a title="Cập nhật thông tin nhân viên"
                                        href="{{ route('editTBC', ['id' => $tbc->id]) }}" class="btn btn-success">
                                        Sửa
                                    </a>
                                    <a title="Xóa nhân viên" href="{{ route('deleteTBC', ['id' => $tbc->id]) }}"
                                        class="btn btn-danger">
                                        Xóa
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="col-sm-12">
            <div class="col-sm-6">
                {{ $thong_bao_chung->appends(request()->input())->links() }}
            </div>
            <div class="col-sm-6">
                <p class="pull-right" style="font-size: 16px;">Tổng số:
                    <b><span style="color: red">{{ count(\App\Models\ThongBaoChung::all()) }}</span></b>
                </p>
            </div>
        </div>
    </section>


    <div class="modal fade" id="add-tbc" name="add-tbc" role="dialog" aria-labelledby="modalLabelinfo">
        <div class="modal-dialog" role="document">
            <div class="modal-content modal-middle" style="margin-top:30% !important;">
                <div class="modal-header" style="background-color: #138165; color:white">
                    <h4 class="modal-title" id="modalLabelinfo">Thông báo chung</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="tieu_de">Tiêu đề:</label>
                        <input type="text" class="form-control" id="tieu_de" name="tieu_de">
                    </div>

                    <div class="form-group">
                        <label for="noi_dung">Nội dung:</label>
                        <textarea type="text" class="form-control" rows="3" id="noi_dung" name="noi_dung"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="vp_id">Văn phòng áp dụng:</label>
                        <select class="form-control select2-single" type="text" name="vp_id_edit" id="vp_id_edit">
                            <option value="0"> Tất cả</option>
                            @foreach ($chi_nhanh_all as $cn)
                                <option value="{{ $cn->cn_id }}"> {{ $cn->cn_ten }} </option>
                            @endforeach
                        </select>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <a style="margin-bottom: 10px;" type="button" class="btn btn-success" onclick="add_tbc()"
                        id="add-btn">Thêm</a>
                    <a style="margin-bottom: 10px;" type="button" class="btn btn-success" id="edit-btn">Sửa</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="show-tbc" name="show-tbc" role="dialog" aria-labelledby="modalLabelinfo">
        <div class="modal-dialog" role="document">
            <div class="modal-content modal-middle" style="margin-top:30% !important;">
                <div class="modal-header" style="background-color: #138165; color:white">
                    <h4 class="modal-title" id="modalLabelinfo">Xem thông báo chung</h4>
                </div>
                <div class="modal-body">
                    <table class="table table-striped table-dark table-responsive">
                        <thead class="thead-inverse">
                        <tbody>
                            <tr style="border: 1px solid grey; ">
                                <td style="background-color: green; color:white">Tiêu đề</td>
                            </tr>
                            <tr style="padding-bottom:2px">
                                <td style="border: 1px solid grey; "><input type="text" readonly class="form-control"
                                        id="tieu_de_show"
                                        style="background-color: transparent; border:none;box-shadow: none;">
                                </td>
                            </tr>
                            <tr style="border: 1px solid grey;">
                                <td style="background-color: green; color:white;">Nội dung</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid grey; text-align: left">
                                    <textarea type="text" class="form-control" rows="3" id="noi_dung_show"></textarea>
                                </td>
                            </tr>
                            <tr style="border: 1px solid grey;">
                                <td style="background-color: green; color:white;">Văn phòng áp dụng</td>
                            </tr>
                            <tr style="padding-bottom:2px">
                                <td style="border: 1px solid grey; "><input type="text" readonly class="form-control"
                                        id="vp_show"
                                        style="background-color: transparent; border:none;box-shadow: none;">
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- END modal-->
@stop
@section('footer_scripts')
    {{--    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/js/select2.min.js"></script> --}}
    {{--    <script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap.min.js"></script> --}}
    <script type="text/javascript" src="{{ asset('assets/vendors/datatables/js/dataTables.bootstrap.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/datatables/js/dataTables.colReorder.js') }}"></script>
    <script src="{{ asset('/assets/vendors/summernote/summernote.js') }}"></script>
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        let thong_bao_chung = @JSON($thong_bao_chung);
        $(document).ready(function() {

            $("#tu-ngay").datepicker({
                format: " yyyy", // Notice the Extra space at the beginning
                viewMode: "years",
                minViewMode: "years"
            });
            $("#den-ngay").datepicker({
                format: " yyyy", // Notice the Extra space at the beginning
                viewMode: "years",
                minViewMode: "years"
            });

            // $('.select2-single').select2();
            // $('.select2-no-search').select2({
            //     minimumResultsForSearch: Infinity
            // });

            // $('#noi_dung').summernote({
            //     height: 400
            // });

            // $('#noi_dung_show').summernote({
            //     airMode: true,
            //     height: 400
            // });

            $('#den-ngay').val('{{ $den_ngay }}')
            $('#tu-ngay').val('{{ $tu_ngay }}')

            $('#nv_id').val('').trigger('change');

            if ('{{ $nv_id ?? '' }}' != '') {
                $('select[name=nv_id]').val('{{ $nv_id ?? '' }}').trigger('change');
            }

        });

        var id = 1;

        function show_add() {
            $('#add-btn').show()
            $('#edit-btn').hide()
            $('#tieu_de').val('')
            // $('#vp_id_edit').select2('val', "0")
            // $('#noi_dung').summernote('code', '');
            // $('#add-tbc').modal('show');
        }

        function show_edit(sub_id) {
            $('#edit-btn').show()
            $('#add-btn').hide()
            id = sub_id
            var tbc = thong_bao_chung.find(x => x.id === id)
            var tieu_de = tbc.tieu_de;
            var noi_dung = tbc.noi_dung;
            var vp_id = tbc.vp_id;
            $('#tieu_de').val(tieu_de)
            $('#noi_dung').summernote('code', noi_dung)
            $('#vp_id_edit').select2('val', vp_id)
            $('#add-tbc').modal('show');
        }

        function show_show(sub_id) {
            var tbc = thong_bao_chung.find(x => x.id === sub_id)
            var tieu_de = tbc.tieu_de;
            var noi_dung = tbc.noi_dung;
            var vp_name = tbc.cn_ten == null ? 'Tất cả' : tbc.cn_ten;
            $('#noi_dung_show').summernote('code', noi_dung)
            $('#noi_dung_show').summernote('disable');
            $('#tieu_de_show').val(tieu_de)
            $('#vp_show').val(vp_name)
            $('#show-tbc').modal('show');
        }

        {{-- function add_tbc() { --}}
        {{--    $.ajax({ --}}
        {{--        url: "{{ route('storeTBC') }}", --}}
        {{--        type: "get", --}}
        {{--        data: { --}}
        {{--            'tieu_de': $('#tieu_de').val(), --}}
        {{--            'noi_dung': $('#noi_dung').val(), --}}
        {{--            'vp_id': $('#vp_id_edit').val() --}}
        {{--        }, --}}
        {{--        success: function (res) { --}}
        {{--            console.log(res.result) --}}
        {{--        } --}}
        {{--    }) --}}
        {{--    window.location.reload(); --}}
        {{--    $('#add-tbc').modal('hide'); --}}
        {{-- } --}}

        {{-- function edit_tbc() { --}}
        {{--    $.ajax({ --}}
        {{--        url: "{{ route('updateTBC') }}", --}}
        {{--        type: "post", --}}
        {{--        data: { --}}
        {{--            'id': id, --}}
        {{--            'tieu_de': $('#tieu_de').val(), --}}
        {{--            'noi_dung': $('#noi_dung').val(), --}}
        {{--            'vp_id': $('#vp_id_edit').val() --}}
        {{--        }, --}}
        {{--        success: function (res) { --}}
        {{--            console.log(res.result) --}}
        {{--        } --}}
        {{--    }) --}}
        {{--    window.location.reload(); --}}
        {{--    $('#add-tbc').modal('hide'); --}}
        {{-- } --}}

        {{-- function delete_tbc(id) { --}}
        {{--    $.ajax({ --}}
        {{--        url: "{{ route('deleteTBC') }}", --}}
        {{--        type: "get", --}}
        {{--        data: { --}}
        {{--            'id': id, --}}
        {{--        }, --}}
        {{--        success: function (res) { --}}
        {{--            console.log(res.result) --}}
        {{--        } --}}
        {{--    }) --}}
        {{--    window.location.reload(); --}}
        {{-- } --}}


        $('#den-ngay').attr('disabled', 'disabled')
        if ('{{ $tu_ngay ?? '' }}' != '') {
            $('#den-ngay').removeAttr('disabled')
        }

        $('#tu-ngay').change(function() {
            $('#den-ngay').removeAttr('disabled')
        })
    </script>
@stop
