@extends('admin/layouts/default')

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

    tr, td {
        text-align: left;
        padding: 5px !important;
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
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Tạo công văn ngăn chặn</h5>
                <form id="edit-form" action="{{ route('updateCongVanNganChan', $congVanNganChan->st_id) }}" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input id="tai-san-input" name="tai_san" type="hidden">
                    <input id="duong-su-input" name="duong_su" type="hidden">
                    <input id="id_ccv" name="id_ccv" value="{{Sentinel::getUser()->id}}" hidden>
                    <div class="card-body mx-3">
                        <div class="row mb-3">
                            <div class="md-form col-3">
                                <label data-error="wrong" data-success="right">Nhóm hợp đồng(<span class="text-danger">*</span>) </label>
                                <select class="form-control select2" id="kieu_hop_dong" onchange="capNhatSoLuongBenDuongSu()" name="kieu_hop_dong_id">
                                    @foreach($nhomHopDong ?? [] as $nhom)
                                        <option @if($selectedNhomHopDong->id == $nhom->id) selected @endif value="{{ $nhom->id }}">{{ $nhom->kieu_hd }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md-form col-4">
                                <label data-error="wrong" data-success="right">Kiểu hợp đồng(<span class="text-danger">*</span>) </label>
                                <select class="form-control select2" id="ten" name="van_ban_id" onchange="capNhatTextNoiDung()">
                                    @foreach($tenhd ?? [] as $kieu_van_ban)
                                        <option @if($congVanNganChan->van_ban_id == $kieu_van_ban->vb_id) selected @endif value="{{ $kieu_van_ban->vb_id }}">{{ $kieu_van_ban->vb_nhan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md-form col-3">
                                <label data-error="wrong" data-success="right">Số công văn(<span class="text-danger">*</span>)</label>
                                <input type="text" id="so_hd" name="so_hd" class="form-control" value="{{ $congVanNganChan->so_hd }}" required>
                            </div>
                            <div class="md-form col-2">
                                <label data-error="wrong" data-success="right">Ngày ngăn chặn(<span class="text-danger">*</span>)</label>
                                <input type="date" id="ngay_cc" name="ngay_cc" class="form-control" required value="{{ $congVanNganChan->ngay_cc }}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="md-form col-12">
                                <label data-error="wrong" data-success="right">Các bên liên quan(<span class="text-danger">*</span>)</label>
                                <button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#modal-nhom-duong-su">Thêm đương sự</button>
                                <textarea type="text" id="duongsu" name="duongsu" class="form-control mt-3" rows="6" cols="50" required>{{ $congVanNganChan->duong_su }}</textarea>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="md-form col-12">
                                <label data-error="wrong" data-success="right">Nội dung công văn(<span class="text-danger">*</span>)</label>
                                <button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#modal-tai-san">Thêm tài sản</button>
                                <textarea type="text" id="noidung" name="noidung" class="form-control mt-3" rows="6" cols="50" required>{{ $congVanNganChan->texte }}</textarea>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="btn-group" id="filterDay">
                                    <label>Loại(<span class="text-danger">*</span>):</label>
                                    <label>
                                        <input type="radio" name="loai" value="1" @if($congVanNganChan->ngan_chan == 1) checked @endif>Ngăn chặn
                                    </label>
                                    <label>
                                        <input type="radio" name="loai" value="0" @if($congVanNganChan->ngan_chan == 0) checked @endif> Giải tỏa
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="form-group col-12">
                                <label for="pic">Ảnh đính kèm (Update nếu thay đổi):</label><br>
                                <input id="pic" name="pic[]" type="file" accept="image/*" class="form-control" onchange="loadImgKH(this,'modal')" multiple/>
                                <div class="row text-center row-image x_content" id="img">
                                    @foreach(json_decode($congVanNganChan->picture) as $pic)
                                        <a class="fancybox-effects-a mr-3" href="{{ asset($pic) }}">
                                            <img src="{{ asset($pic) }}" height="100px">
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a class="btn btn-default" href="{{ route('indexSuuTra') }}">Đóng</a>
                        <button id="save-btn" type="button" class="btn btn-success">Xác nhận</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal chọn nhóm đương sự-->
        <div class="modal fade " id="modal-nhom-duong-su" tabindex="-1" role="dialog" aria-labelledby="modal-nhom-duong-su" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content" style="background-color: #ffffff !important;">
                    <div class="modal-header" style="padding: 1rem !important;">
                        <h5 class="modal-title">Chọn nhóm đương sự</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="btn-group d-flex btn-group-lg" role="group">
                            <button id="btn-ben-A" type="button" class="btn btn-secondary" data-toggle="modal" data-target="#modal-duong-su">Bên A</button>
                            <button id="btn-ben-B" type="button" class="btn btn-secondary" data-toggle="modal" data-target="#modal-duong-su">Bên B</button>
                            <button tid="btn-ben-C" type="button" class="btn btn-secondary" data-toggle="modal" data-target="#modal-duong-su">Bên C</button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Đóng</button>
                        <button type="button" class="btn btn-primary" id="update-duong-su">Xác nhận</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal chọn đương sự-->
        <div class="modal fade" id="modal-duong-su" role="dialog" aria-labelledby="modal-duong-su" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" style="background-color: #ffffff !important;">
                    <div class="modal-header" style="padding: 1rem !important;">
                        <h5 class="modal-title">Tìm đương sự</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <select class="form-control" style="width: 80%" id="cac-ben-lien-quan"></select>
                            <button type="button" class="btn btn-success" style="width: 15%" onclick="addToUlDuongSu()">Thêm</button>
                        </div>
                        <hr>
                        <div class="form-group" style="color: black">
                            <p class="text-dark">Danh sách đương sự đã chọn</p>
                            <ul class="list-group" id="ul-duong-su">
                            </ul>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Xong</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal show thong tin duong su-->
        <div class="modal fade" id="modal-show-duong-su" role="dialog" aria-labelledby="modal-show-duong-su" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" style="background-color: #ffffff !important;">
                    <div class="modal-header" style="padding: 1rem !important;">
                        <h5 class="modal-title">Thông tin đương sự</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table id="table-show-thong-tin-duong-su" class="table table-bordered table-hover mb-0">
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-tai-san" role="dialog" aria-labelledby="modal-tai-san" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" style="background-color: #ffffff !important;">
                    <div class="modal-header" style="padding: 1rem !important;">
                        <h5 class="modal-title">Tìm tài sản</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <select class="form-control" style="width: 80%" id="tai-san"></select>
                            <button type="button" class="btn btn-success" style="width: 15%" onclick="addToUlTaiSan()">Thêm</button>
                        </div>
                        <hr>
                        <div class="form-group" style="color: black">
                            <p class="text-dark">Danh sách tài sản đã chọn</p>
                            <ul class="list-group" id="ul-tai-san">
                            </ul>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal" id="update-tai-san">Xong</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal show thong tin tai san-->
        <div class="modal fade" id="modal-show-tai-san" role="dialog" aria-labelledby="modal-show-tai-san" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" style="background-color: #ffffff !important;">
                    <div class="modal-header" style="padding: 1rem !important;">
                        <h5 class="modal-title">Thông tin tài sản</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table id="table-show-thong-tin-tai-san" class="table table-bordered table-hover mb-0">
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Đóng</button>
                    </div>
                </div>
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

    <style type="text/css">
        body {
            background-color: #E1E1E1
        }

        p {
            font-size: 16px
        }

        .highlight {
            background-color: yellow
        }
    </style>

    <script>
        function convertDate(inputFormat) {
            function pad(s) { return (s < 10) ? '0' + s : s; }
            var d = new Date(inputFormat)
            return [pad(d.getDate()), pad(d.getMonth()+1), d.getFullYear()].join('/')
        }
        // -------------------------------------- Tài sản --------------------------------------
        var selectedTaiSan = JSON.parse('{!! json_encode($taiSan, true) !!}');

        function taiSanResultTemplater(option) {
            let duplicated = false;
            selectedTaiSan.forEach((obj) => {
                if (obj.id == option.id) {
                    duplicated = true;
                }
            });
            if (duplicated) {
                return null;
            }
            return option.text;
        }

        $("#tai-san").select2({
            ajax: {
                url: "{{ url('taisan/search') }}",
                method: "GET",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        // results: data.data
                        results: $.map(data.data, function (item) {
                            item.id = item.ts_id;
                            item.text = item.ts_nhan;
                            return item;
                        })
                    };
                },
                cache: false
            },
            placeholder: function () {
                $(this).data('placeholder');
            },
            minimumInputLength: 2,
            templateResult: taiSanResultTemplater
        });

        function addToUlTaiSan() {
            var option = $('#tai-san').select2('data');
            if (option[0]) {
                $('#ul-tai-san').append($("<li>").attr('data-value', option[0].id).attr('class', 'list-group-item')
                    .append($("<a href='#'>").click(function () {
                        showThongTinTaiSan(option[0].id)
                    }).text(option[0].text))
                    .append($("<button>").attr('class', 'btn btn-default btn-xs pull-right remove-item').click(function () {
                        xoaTaiSan(option[0].id)
                    }).append($("<span>").attr('class', 'glyphicon glyphicon-remove')))
                );
                capNhatThongTinTaiSan(option[0]);
            }
        }

        function xoaTaiSan(dataValue) {
            selectedTaiSan = selectedTaiSan.filter(value => {
                return value.id != dataValue;
            });
            setUpListTaiSan(nhomDuongSu);
        }

        function setUpListTaiSan() {
            $('#ul-tai-san').empty();
            selectedTaiSan.forEach((object) => {
                $('#ul-tai-san').append($("<li>").attr('data-value', object.id).attr('class', 'list-group-item')
                    .append($("<a href='#'>").click(function () {
                        showThongTinTaiSan(object.id)
                    }).text(object.text))
                    .append($("<button>").attr('class', 'btn btn-default btn-xs pull-right remove-item').click(function () {
                        xoaTaiSan(object.id)
                    }).append($("<text>").attr('class', 'glyphicon glyphicon-remove')))
                );
            })
        }

        function capNhatThongTinTaiSan(info) {
            $.ajax({
                type: "GET",
                url: "{{ route('thongTinTaiSan') }}",
                data: {
                    id: info.id
                },
                dataType: "json",
                cache: true,
                success: function (data) {
                    info.thong_tin_str = data.thong_tin_str;
                    selectedTaiSan.push(info);
                }
            });
        }

        function showThongTinTaiSan(id) {
            $.ajax({
                type: "GET",
                url: "{{ route('thongTinTaiSan') }}",
                data: {
                    id: id
                },
                dataType: "json",
                cache: true,
                success: function (data) {
                    capNhatModalThongTinTaiSan(data.thong_tin_arr);
                    $('#modal-show-tai-san').modal('show');
                }
            });
        }

        function capNhatModalThongTinTaiSan(data) {
            $('#table-show-thong-tin-tai-san').empty();
            data.forEach((item, index) => {
                if (item.tm_loai != 'file') {
                    $('#table-show-thong-tin-tai-san')
                        .append("<tr><td class=\"fit-column-kh\">" + item.tm_nhan + "</td><td class=\"text-left\">" + item.ts_giatri + "</td></tr>");
                }
            })
        }

        $('#update-tai-san').click(function () {
            getTemplateNoiDung();
        })

        function capNhatTextNoiDung () {
            if ($('#noidung').val() != '') {
                getTemplateNoiDung();
            }
        }

        function getTemplateNoiDung () {
            let loaiHopDongId = $('#ten').val();
            let soCongChung = $('#so_hd').val();
            let ngayCongChung = convertDate($('#ngay_cc').val());
            $.ajax({
                type: "GET",
                url: "{{ route('admin.templates.loai-hop-dong.convert-to-text') }}",
                data: {
                    loai_hop_dong_id: loaiHopDongId,
                    so_cong_chung: soCongChung,
                    ngay_chung_nhan: ngayCongChung,
                    quyen_so: '...',
                    ten_van_phong: '{{ \App\Models\ChiNhanhModel::find(\App\Models\NhanVienModel::find(Sentinel::getUser()->id)->nv_vanphong)->cn_ten }}'
                },
                dataType: "json",
                cache: true,
                success: function (data) {
                    let text = data.data + "\n";
                    $.each(selectedTaiSan, function (key, value) {
                        text += value.thong_tin_str + "\n";
                    })
                    $('#noidung').val(text);
                    $('#modal-tai-san').modal('hide');
                }
            })
        }

        // ----------------------------- Đương sự --------------------------------------------------
        var currentDuongSu = JSON.parse('{!! json_encode($duongSu) !!}');
        var selectedLabel = 'chuyen-nhuong';
        var soLuongDuongSu = 2;
        var nhomDuongSu = '';
        var benA = currentDuongSu.benA;
        var benB = currentDuongSu.benB;
        var benC = currentDuongSu.benC;

        $(document).ready(function () {
            $("#cac-ben-lien-quan").select2({
                ajax: {
                    url: "{{ url('account/kh') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            tk_khachhang: params.term, // search term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.data
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2,
                placeholder: function () {
                    $(this).data('placeholder');
                },
                templateResult: resultTemplater,
                templateSelection: selectionTemplater
            });

            $('#btn-ben-A').click(function () {
                nhomDuongSu = 'A';
                setUpListDuongSu('A');
            })
            $('#btn-ben-B').click(function () {
                nhomDuongSu = 'B';
                setUpListDuongSu('B');
            })
            $('#btn-ben-C').click(function () {
                nhomDuongSu = 'C';
                setUpListDuongSu('C');
            })
            firstSetting();
        })

        function setUpListDuongSu(nhomDuongSu) {
            $('#ul-duong-su').empty();
            let arr = [];
            switch (nhomDuongSu) {
                case 'A':
                    arr = benA;
                    break;
                case 'B':
                    arr = benB;
                    break;
                case 'C':
                    arr = benC;
                    break;
            }
            arr.forEach((object) => {
                $('#ul-duong-su').append($("<li>").attr('data-value', object.id).attr('class', 'list-group-item')
                    .append($("<a href='#'>").click(function () {
                        showThongTinDuongSu(object.id)
                    }).text(object.first_name))
                    .append($("<button>").attr('class', 'btn btn-default btn-xs pull-right remove-item').click(function () {
                        xoaDuongSu(object.id)
                    }).append($("<span>").attr('class', 'glyphicon glyphicon-remove')))
                );
            })
        }

        function resultTemplater(option) {
            let arr = [];
            let duplicated = false;
            switch (nhomDuongSu) {
                case 'A':
                    arr = benA;
                    break;
                case 'B':
                    arr = benB;
                    break;
                case 'C':
                    arr = benC;
                    break;
            }
            arr.forEach((obj) => {
                if (obj.id == option.id) {
                    duplicated = true;
                }
            });
            if (duplicated) {
                return null;
            }
            return option.first_name;
        }

        function selectionTemplater(option) {
            if (typeof option.first_name !== "undefined") {
                return resultTemplater(option);
            }
            return option.first_name; // I think its either text or label, not sure.
        }

        function showThongTinDuongSu(id) {
            $.ajax({
                type: "GET",
                url: "{{ route('thongTinDuongSu') }}",
                data: {
                    id: id
                },
                dataType: "json",
                cache: true,
                success: function (data) {
                    capNhatModalThongTinDuongSu(data.thong_tin_arr);
                    $('#modal-show-duong-su').modal('show');
                }
            });
        }

        function capNhatModalThongTinDuongSu(data) {
            $('#table-show-thong-tin-duong-su').empty();
            data.forEach((item, index) => {
                if (item.tm_loai != 'file') {
                    $('#table-show-thong-tin-duong-su')
                        .append("<tr><td class=\"fit-column-kh\">" + item.tm_nhan + "</td><td class=\"text-left\">" + item.kh_giatri + "</td></tr>");
                }
            })
        }

        function addToUlDuongSu() {
            var option = $('#cac-ben-lien-quan').select2('data');
            if (option[0]) {
                $('#ul-duong-su').append($("<li>").attr('data-value', option[0].id).attr('class', 'list-group-item')
                    .append($("<a href='#'>").click(function () {
                        showThongTinDuongSu(option[0].id)
                    }).text(option[0].first_name))
                    .append($("<button>").attr('class', 'btn btn-default btn-xs pull-right remove-item').click(function () {
                        xoaDuongSu(option[0].id)
                    }).append($("<span>").attr('class', 'glyphicon glyphicon-remove')))
                );
                capNhatThongTinNhomDuongSu(option[0]);
            }
            $('#cac-ben-lien-quan').val('').trigger('change');
        }

        function capNhatThongTinNhomDuongSu(info) {
            $.ajax({
                type: "GET",
                url: "{{ route('thongTinDuongSu') }}",
                data: {
                    id: info.id
                },
                dataType: "json",
                cache: true,
                success: function (data) {
                    info.thong_tin_str = data.thong_tin_str;
                    switch (nhomDuongSu) {
                        case 'A':
                            benA.push(info);
                            break;
                        case 'B':
                            benB.push(info);
                            break;
                        case 'C':
                            benC.push(info);
                            break;
                    }
                }
            });
        }

        function capNhatSoLuongBenDuongSu() {
            soLuongDuongSu = 2;
            // chung thuc = 1, default = 2, the chap = 3 // key + 1
            var keywords = ['chung-thuc', 'default', 'the-chap'];

            var label = $("#kieu_hop_dong option:selected").text();
            capNhatSelectKieuVanBan($("#kieu_hop_dong").val())
            var slugLabel = slugify(label);
            selectedLabel = slugLabel;
            $.each(keywords, function (key, value) {
                if (slugLabel.includes(value)) {
                    soLuongDuongSu = key + 1;
                }
            });
        }

        function capNhatSelectKieuVanBan(id) {
            $('#ten').empty();
            $.ajax({
                type: "GET",
                url: "{{ route('listKieuVanBan') }}",
                data: {
                    id: id
                },
                dataType: "json",
                cache: true,
                success: function (data) {
                    $.each(data, function (index, object) {
                        $('#ten').append('<option value="' + object.vb_id + '">' + object.vb_nhan + '</option>');
                    })
                }
            });

            if ($('#noidung').val() != '') {
                getTemplateNoiDung();
            }
        }

        function xoaDuongSu(dataValue) {
            switch (nhomDuongSu) {
                case 'A':
                    benA = benA.filter(value => {
                        return value.id != dataValue;
                    });
                    break;
                case 'B':
                    benB = benB.filter(value => {
                        return value.id != dataValue;
                    });
                    break;
                case 'C':
                    benC = benC.filter(value => {
                        return value.id != dataValue;
                    });
                    break;
            }
            setUpListDuongSu(nhomDuongSu);
        }

        $('#update-duong-su').click(function () {
            console.log(selectedLabel);
            let textA = "Bên A: ";
            let textB = "Bên B: ";
            let textC = "Bên C: ";
            if (selectedLabel.includes('chuyen-nhuong')) {
                textA = "Bên chuyển nhượng (Bên A): ";
                textB = "Bên nhận chuyển nhượng (Bên B): ";
            } else if (selectedLabel.includes('the-chap')) {
                textA = "Bên thế chấp (Bên A): ";
                textB = "Bên nhận thế chấp (Bên B): ";
                textC = "Bên được cấp tín dụng (Bên C): ";
            } else if (selectedLabel.includes('chung-thuc')) {
                textA = "Người chứng thực";
            } else if (selectedLabel.includes('tang-cho')) {
                textA = "Bên tặng cho(Bên A): ";
                textB = "Bên nhận tặng cho(Bên B): ";
            } else if (selectedLabel.includes('uy-quyen')) {
                textA = "Bên ủy quyền (Bên A): ";
                textB = "Bên nhận ủy quyền (Bên B): ";
            } else if (selectedLabel.includes('thue-muon')) {
                textA = "Bên cho thuê (Bên A): ";
                textB = "Bên thuê (Bên B): ";
            } else if (selectedLabel.includes('di-chuc')) {
                textA = "Bên lập di chúc (Bên A): ";
            } else if (selectedLabel.includes('thua-ke')) {
                textA = "Bên khai nhận/từ chối thừa kế (Bên A): ";
                textB = "Bên để lại thừa kế (Bên B): ";
            }

            var text = textA;
            $.each(benA, function (key, value) {
                text += value.thong_tin_str + ", ";
            })
            if (soLuongDuongSu > 1) {
                text += "\n" + textB;
                $.each(benB, function (key, value) {
                    text += value.thong_tin_str + ", ";
                })
            }
            if (soLuongDuongSu > 2) {
                text += "\n" + textC;
                $.each(benC, function (key, value) {
                    text += value.thong_tin_str + ", ";
                })
            }
            $('#duongsu').val(text);
            $('#modal-nhom-duong-su').modal('hide');
        })

        function firstSetting() {
            setUpListDuongSu('A');
            setUpListDuongSu('B');
            setUpListDuongSu('C');
            setUpListTaiSan();

            $('#save-btn').click(function() {
                $('#tai-san-input').val(JSON.stringify(selectedTaiSan.map(obj => obj.ts_id)));
                $('#duong-su-input').val(JSON.stringify({
                    benA: benA.map(obj => obj.id),
                    benB: benB.map(obj => obj.id),
                    benC: benC.map(obj => obj.id)
                }));
                $('#edit-form').submit();
            })
        }
    </script>
@stop

