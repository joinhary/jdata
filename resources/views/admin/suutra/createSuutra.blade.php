@extends('admin/layouts/default')
@section('title')
    Nhập hồ sơ giao dịch @parent
@stop
@section('header_styles')
    {{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> --}}
    <link rel="stylesheet" href="{{ asset('assets/vendors/select2/css/select2.min.css') }}">
    <style>
        .qksao {
            font-weight: bold;
            color: red;
        }

        .qkbtn {
            font-weight: bold;
            font-size: 14px !important;
        }

        .qkmodel {
            background-color: #1a67a3 !important;
        }

        .qkbtn {
            font-weight: bold;
            font-size: 14px !important;
        }

        .btn2 {
            font-weight: 500;
            background-color: white !important;
            color: #1a67a3 !important;
            font-size: 14px !important;
        }

        .nqkright {
            text-align: right !important;
            font-size: 14px !important;
            font-weight: 500;
        }
    </style>
@section('content')
    <section class="content">
        @php
            $role = Sentinel::check()
                ->user_roles()
                ->first()->slug;
            $user = Sentinel::getUser();
        @endphp
        <form action="{{ route('storeSuutra') }}" method="post" id="formPost" onsubmit="onSubmitHandler()"
            enctype="multipart/form-data">
            @csrf
            <div class="row bctk-scrollable-list" style="overflow-x: hidden; height: calc(100vh - 100px) ;">
                <input id="id_ccv" name="id_ccv" value="{{ Sentinel::getUser()->id }}" hidden>
                <div class="col-sm-12">
                    @if ($role == 'admin' || $role == 'chuyen-vien-so')
                        <div class="form-group col-md-12">
                            <label class="col-lg-4 col-form-label nqkright">
                                <div class="col-lg-12">
                                    Tên văn bản: (<span class="text-danger qksao">*</span>)
                                </div>
                            </label>
                            <div class="col-lg-8">
                                <input type="text" value="{{ Request::input('ten') }}" id="ten" name="ten"
                                    class="form-control" required>
                            </div>
                        </div>
                    @else
                        <div class="form-group col-md-12">
                            <label class="col-lg-4 col-form-label nqkright">
                                <div class="col-lg-12">
                                    Nhóm văn bản: (<span class="text-danger qksao">*</span>)
                                </div>
                            </label>
                            <div class="col-lg-8">
                                {!! \App\Helpers\Form::select('vb_kieuhd', $kieuhd, Request::input('vb_kieuhd'), [
                                    'id' => 'kieuhd',
                                    'class' => 'form-control sel',
                                    'style' => 'width: 100%',
                                ]) !!}
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label class="col-lg-4 col-form-label nqkright">
                                <div class="col-lg-12">
                                    Tên văn bản: (<span class="text-danger qksao">*</span>)
                                </div>
                            </label>
                            <div class="col-lg-8">
                                <select class="form-control sel" id="vanban" name="ten"
                                    onchange="capNhatTextNoiDung()" required style="width: 100%">
                                    <option value="">--- Chọn tên văn bản ---</option>
                                </select>
                            </div>
                        </div>
                    @endif
                    <div class="form-group col-md-12">
                        <label class="col-lg-4 col-form-label nqkright">
                            <div class="col-lg-12">
                                Công chứng viên: (<span class="text-danger qksao">*</span>)
                            </div>
                        </label>
                        <div class="col-lg-8">

                            {!! \App\Helpers\Form::select('id_ccv', $ccv, Request::input('id_ccv'), [
                                'id' => 'id_ccv',
                                'class' => 'form-control sel',
                                'required' => true,
                            ]) !!}
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <label class="col-lg-4 col-form-label nqkright">
                            <div class="col-lg-12">
                                Chuyên viên:
                            </div>
                        </label>
                        <div class="col-lg-8">

                            {!! \App\Helpers\Form::select('cv_id', $cv, Request::input('cv_id'), ['id' => 'cv_id', 'class' => 'form-control sel']) !!}
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <label class="col-lg-4 col-form-label nqkright">
                            <div class="col-lg-12">
                                Số văn bản: (<span class="text-danger qksao">*</span>)
                            </div>
                        </label>
                        <div class="col-lg-8">
                            <input type="text" id="so_hd" name="so_hd" class="form-control"
                                placeholder="Nhập số hợp đồng ..." required>
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <label class="col-lg-4 col-form-label nqkright">
                            <div class="col-lg-12">
                                Ngày công chứng: (<span class="text-danger qksao">*</span>)
                            </div>
                        </label>
                        <div class="col-lg-8">
                            <input placeholder="dd-mm-yyyy" data-date-format="dd-mm-yyyy" type="date" id="ngay_cc"
                                name="ngay_cc" value="{{ Request::input('ngay_cc') }}" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <div class="col-lg-4 col-form-label">
                            <div class="col-lg-12">
                                <div class="col-lg-12 nqkright">
                                    Các bên liên quan: (<span class="text-danger qksao">*</span>)
                                </div>
                                <div class="col-lg-12 form-check nqkright">
                                    <input type="radio" name="radioDS" value="Đương sự (Bên A)" id="radioA" checked>
                                    Đương sự A
                                    <input type="radio" name="radioDS" value="Đương sự (Bên B)" id="radioB">
                                    Đương sự B
                                    <input type="radio" name="radioDS" value="Đương sự (Bên C)" id="radioC">
                                    Đương sự C
                                </div>
                                <div class="col-lg-12 nqkright">
                                    <select class="form-control" id="searchDS"></select>
                                </div>
                                <div class="col-lg-12 nqkright">
                                    <button type="button" class="btn btn-success pull-right" onclick="addDuongSu()">Chèn
                                        >>>
                                    </button>
                                    <a href="javascript:void(0)" data-toggle="modal" data-target="#create-customer"
                                        class="btn btn-primary btn2 pull-right">Thêm
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <textarea type="text" id="duongsu" name="duongsu" class="form-control mt-3" rows="7" cols="50"
                                required>{!! Request::input('duongsu') !!}</textarea>
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <div class="col-lg-4 col-form-label">
                            <div class="col-lg-12">
                                <div class="col-lg-12 nqkright">
                                    Nội dung văn bản: (<span class="text-danger qksao">*</span>)
                                </div>
                                <br>
                                <div class="col-lg-12 form-check nqkright">
                                    <input type="radio" name="radioTS" value="Nội dung" id="radioND" checked> Nội
                                    dung
                                    <input type="radio" name="radioTS" value="Tài sản" id="radioTS"> Tài sản
                                </div>
                                <div class="col-lg-12 nqkright">
                                    <select class="form-control" id="searchTS"></select>
                                </div>
                                <div class="col-lg-12 nqkright">
                                    <button type="button" class="btn btn-success pull-right" onclick="addTaiSan()">Chèn
                                        >>>
                                    </button>
                                    <a href="{{ route('createTaiSan') }}" target="_blank"
                                        class="btn btn-primary btn2 pull-right">Thêm
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <textarea type="text" id="noidung" name="noidung" class="form-control mt-3" rows="7" cols="50"
                                required>{!! Request::input('noidung') !!}</textarea>
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <label class="col-lg-4 col-form-label nqkright">
                            <div class="col-lg-12">Loại:</div>
                        </label>
                        <div class="col-lg-8">
                            <input type="radio" name="loai" value="0" id="thuong" checked> Thường
                            @if ($role == 'chuyen-vien-so' || $role == 'admin')
                                <input type="radio" name="loai" value="3" id="nganchan"> Ngăn chặn
                            @endif
                            @if ($role == 'truong-van-phong' || $role == 'cong-chung-vien' || $role == 'chuyen-vien' || $role == 'phong-khac')
                                <input type="radio" name="loai" value="2" id="canhbao"> Cảnh báo
                            @endif

                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <label class="col-lg-4 col-form-label nqkright">
                            <div class="col-lg-12">
                                Giá trị giao dịch:
                            </div>
                        </label>
                        <div class="col-md-4">
                            <input type="text" id="userinput" name="trans_val" placeholder="Nhập giá trị giao dịch"
                                class="form-control">
                            <br>
                        </div>
                        @if (!Sentinel::inRole('admin'))
                            <div class="form-group col-md-12">
                                <label class="col-lg-4 col-form-label nqkright">
                                    <div class="col-lg-12">Nhập số công chứng cũ:</div>
                                </label>
                                <div class="col-lg-8">
                                    <input type="text" id="so_hd" name="description" class="form-control"
                                        placeholder="Nhập số hợp đồng ...">
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label class="col-lg-4 col-form-label nqkright">
                                    <div class="col-lg-12">Thời hạn:</div>
                                </label>
                                <div class="col-lg-8">
                                    <input type="text" id="contract_period" name="contract_period"
                                        class="form-control" placeholder="Thời hạn ...">
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label class="col-lg-4 col-form-label nqkright">
                                    <div class="col-lg-12">
                                        Ngân hàng:
                                    </div>
                                </label>
                                <div class="col-lg-8">

                                    {!! \App\Helpers\Form::select('bank', $bank, Request::input('bank'), ['id' => 'bank', 'class' => 'form-control sel']) !!}
                                </div>
                            </div>
                        @endif
                        <div class="form-group col-md-12">
                            @if (Sentinel::inRole('admin'))
                                <label class="col-lg-4 col-form-label nqkright">
                                    <div class="col-lg-12">Tệp công văn ngăn chặn:</div>
                                </label>
                            @else
                                <label class="col-lg-4 col-form-label nqkright">
                                    <div class="col-lg-12">Ảnh/File đính kèm:</div>
                                </label>
                            @endif
                            <div class="col-lg-8">
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <div class="row text-center row-image x_content" id="img" style="height: 80px">
                                    </div>
                                    <div>
                                        <span class=" btn-file">
                                            <input id="pic" name="pic[]" type="file" accept="image/*"
                                                class="form-control" onchange="loadImgKH(this,'modal')" multiple />
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if (!Sentinel::inRole('admin'))
                            <div class="form-group col-md-12">
                                <label class="col-lg-4 col-form-label nqkright">
                                    <div class="col-lg-12">Thù Lao:</div>
                                </label>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <input type="text" value="{{ old('thu_lao') }}" class="form-control"
                                            placeholder="0" id="thu_lao" name="thu_lao">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"
                                                style="padding-bottom: 0px;padding-top: 0px;">vnđ</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label class="col-lg-4 col-form-label nqkright">
                                    <div class="col-lg-12">Phí công chứng:</div>
                                </label>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <input type="text" value="{{ old('phi_cong_chung') }}" placeholder="0"
                                            id="phi_cong_chung" name="phi_cong_chung" class="form-control">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"
                                                style="padding-bottom: 0px;padding-top: 0px;">vnđ</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="form-group col-md-12">
                            <label class="col-lg-4 col-form-label nqkright"></label>
                            <div class="col-lg-8">
                                <a href="javascript:history.back()" type="cancel"
                                    class="btn btn-secondary qkbtn">Hủy</a>
                                <button id="btn-save" type="button" onclick="submitConfirm()"
                                    class="btn btn-primary qkbtn">Lưu</button>
                            </div>
                        </div>
                    </div>
                </div>
        </form>

        <!-- Modal show thêm đương sự-->
        <div class="modal fade" id="create-customer" role="dialog" aria-labelledby="modalLabelinfo">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #1a67a3 !important;">
                        <h4 class="modal-title qkmodel" id="modalLabelinfo">Chọn kiểu đương sự</h4>
                    </div>
                    <div class="modal-body" style="background-color: #f7f7f7;color: black">
                        <div id="treeview-expandible" class="">
                            <table class="table-bordered ">
                                @foreach ($kieuDS as $item)
                                    @php
                                        $k_id = $item->k_id;
                                        $tm = \App\Models\KieuModel::select('k_tieumuc')
                                            ->where('k_id', $k_id)
                                            ->first();
                                        $tm_arr = explode(' ', $tm->k_tieumuc);
                                        $tieumuc = \App\Models\TieuMucModel::select('tieumuc.tm_id', 'tm_nhan', 'tm_loai', 'tm_keywords', 'tm_batbuoc')
                                            ->leftjoin('tieumuc_sapxep', 'tieumuc_sapxep.tm_id', '=', 'tieumuc.tm_id')
                                            ->whereIn('tieumuc.tm_id', $tm_arr)
                                            ->where('k_id', $k_id)
                                            ->orderBy('tm_sort', 'asc')
                                            ->get();
                                    @endphp
                                    <thead>
                                        <tr class="text-center" style="background-color:#eeeeee">
                                            <th>{{ $item->k_nhan }}</th>
                                            <th>
                                                <a href="#" class="btn btn-primary mb-0" data-toggle="modal"
                                                    data-target="#modal-honphoi{{ $item->k_id }}">
                                                    Tiếp tục
                                                </a>
                                            </th>
                                        </tr>
                                    </thead>
                                    <div class="modal fade in" id="modal-honphoi{{ $item->k_id }}" tabindex="-1"
                                        role="dialog" aria-hidden="false">
                                        <div class="modal-dialog modal-xl">
                                            <div class="modal-content">
                                                <div class="modal-header qkmodel"
                                                    style="background-color: #1a67a3 !important;">
                                                    <h4 class="modal-title qkmodel"
                                                        style="background-color: #1a67a3 !important;">
                                                        Thêm mới khách hàng <span id="main-ds"></span>
                                                    </h4>
                                                </div>
                                                <div class="modal-body" style="background-color: #f7f7f7;color: black">
                                                    <form id="form-honphoi">
                                                        @csrf
                                                        <div class="row">
                                                            @foreach ($tieumuc as $tm)
                                                                <div
                                                                    class="col-sm-3 {{ $tm->tm_keywords == 'tinh-trang-hon-nhan' ? 'hidden' : '' }}">
                                                                    <div class="form-group">
                                                                        <label class="text-bold"
                                                                            for="modal-ele-{{ $tm->tm_keywords }}">
                                                                            {{ $tm->tm_nhan }}
                                                                            @if ($tm->tm_batbuoc == 1)
                                                                                (<span class="text-danger qksao">*</span>)
                                                                            @endif:
                                                                        </label>
                                                                        <input type="text" name="ds_tm[]"
                                                                            value="tm-{{ $tm->tm_id }}" hidden>
                                                                        @if ($tm->tm_loai == 'text')
                                                                            <input id="modal-ele-{{ $tm->tm_keywords }}"
                                                                                type="text"
                                                                                name="tm-{{ $tm->tm_id }}"
                                                                                class="form-control"
                                                                                @if ($tm->tm_batbuoc == 1) required @endif>
                                                                        @elseif($tm->tm_loai == 'select')
                                                                            <?php
                                                                            $select = \App\Models\KieuTieuMucModel::where('tm_id', $tm->tm_id)
                                                                                ->where('ktm_status', 1)
                                                                                ->pluck('ktm_traloi', 'ktm_id');
                                                                            ?>
                                                                            {!! \App\Helpers\Form::select('tm-' . $tm->tm_id, $select, '', [
                                                                                'class' => 'form-control',
                                                                                'id' => 'modal-ele-' . $tm->tm_keywords,
                                                                                'onchange' => 'change_tm(this,\'in-modal\')',
                                                                            ]) !!}
                                                                        @elseif($tm->tm_loai == 'file')
                                                                            <input id="{{ $tm->tm_keywords }}"
                                                                                name="tm-{{ $tm->tm_id }}[]"
                                                                                type="file" accept="image/*"
                                                                                class="form-control" onchange="img(this)"
                                                                                @if ($tm->tm_batbuoc == 1) required @endif
                                                                                multiple />
                                                                            <div class="col-md-12 text-center row-image"
                                                                                style="background-color: #fff !important; height: 80px"
                                                                                id="img-{{ $tm->tm_keywords }}"></div>
                                                                        @else
                                                                            <input type="text"
                                                                                id="modal-ele-{{ $tm->tm_keywords }}"
                                                                                class="form-control"
                                                                                name="tm-{{ $tm->tm_id }}"
                                                                                data-mask="99/99/9999"
                                                                                placeholder="Ngày / tháng / năm"
                                                                                @if ($tm->tm_batbuoc == 1) required @endif>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <div class="row">
                                                            <hr>
                                                        </div>
                                                        <div class="row">
                                                            <div hidden class="col-md-4 col-xs-12">
                                                                <div class="form-group row">
                                                                    <label class="text-bold" for="pic">Ảnh đại
                                                                        diện:</label><br>
                                                                    <div class="fileinput fileinput-new"
                                                                        data-provides="fileinput">
                                                                        <div class="fileinput-new thumbnail"
                                                                            style="width: 220px; height: 220px;">
                                                                            <img src="{{ url('/images/new-user.png') }}"
                                                                                alt="profile pic">
                                                                        </div>
                                                                        <div class="fileinput-preview fileinput-exists thumbnail"
                                                                            style="max-width: 250px; max-height: 250px;">
                                                                        </div>
                                                                        <div>
                                                                            <span class="btn btn-primary btn-file">
                                                                                <span class="fileinput-new">Chọn
                                                                                    ảnh</span>
                                                                                <span class="fileinput-exists">Thay
                                                                                    đổi</span>
                                                                                <input id="modal-ele-pic" name="pic"
                                                                                    type="file" class="form-control" />
                                                                            </span>
                                                                            <a href="#"
                                                                                class="btn btn-danger fileinput-exists"
                                                                                data-dismiss="fileinput">Gỡ bỏ</a>
                                                                        </div>
                                                                    </div>
                                                                    <span
                                                                        class="help-block">{{ $errors->first('pic_file', ':message') }}</span>
                                                                </div>
                                                            </div>
                                                            <div hidden class="col-md-8 col-xs-12">
                                                                <div class="form-group row">
                                                                    <label class="text-bold" for="modal-ele-username">Tài
                                                                        khoản:</label>
                                                                    <input type="text" id="modal-ele-username"
                                                                        class="form-control" name="username" required>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <label class="text-bold" for="modal-ele-password">Mật
                                                                        khẩu:</label>
                                                                    <input type="text" id="modal-ele-password"
                                                                        class="form-control password-validate"
                                                                        name="password" required>
                                                                    <span id="modal-ele-valid-password"
                                                                        class="text-small text-danger pl-1 pt-1"></span>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <label class="text-bold"
                                                                        for="modal-ele-first_name">Nhãn:</label>
                                                                    <input type="text" id="modal-ele-first_name"
                                                                        class="form-control" name="first_name" required>
                                                                    <span id="modal-ele-valid-first_name"
                                                                        class="text-small text-danger pl-1 pt-1"></span>
                                                                </div>
                                                                <input type="text" id="modal-ele-contact"
                                                                    name="contact" hidden>
                                                                <input type="text" id="modal-ele-contact"
                                                                    name="kieu" value="{{ $k_id }}" hidden>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                                <div class="modal-footer" style="background-color: #f7f7f7;color: black">
                                                    <button type="button" data-dismiss="modal"
                                                        class="btn btn-warning qkbtn">Hủy
                                                    </button>
                                                    <a href="javascript:void(0)" id="submit-honphoi"
                                                        class="btn btn-primary qkbtn"
                                                        onclick="submitHonPhoi({{ $item->k_id }})">Lưu</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- modal thêm tài sản -->
    </section>

@stop
@include('admin.layouts.loading')
@section('footer_scripts')
    <script type="text/javascript" src="{{ asset('assets/js/imgPreview.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.mark.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.highlight-5.js') }}"></script>
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script>
        function submitHonPhoi(id) {
            $(`#modal-honphoi${id}`).modal('hide');
            $('#create-customer').modal('hide');
            var dataForm = new FormData($('#form-honphoi')[0]);
            $.ajax({
                url: "{{ route('storeKhachHang') }}",
                type: 'post',
                processData: false,
                contentType: false,
                data: dataForm,
                success: function(res) {
                    if (res.status === 'success') {
                        msgSuccess(res.message);
                    } else {
                        $.each(res.message, function(k, v) {
                            msgError(v);
                        })
                    }
                }
            })
        }
        $('#modal-ele-ho-duong-su').focusout(function() {

            if ($('#modal-ele-ho-duong-su').val() !== '' && $('#modal-ele-ten-duong-su').val() !== '') {
                var first_name = $('#modal-ele-ho-duong-su').val() + ' ' + $('#modal-ele-ten-duong-su').val();
                $('#modal-ele-first_name').val(first_name);
            }
            $('#modal-ele-username').val(Math.floor(Math.random() * 999999) + 100000);
            $('#modal-ele-password').val(Math.floor(Math.random() * 999999) + 100000);
        });
        $('#modal-ele-giay-to-tuy-than-so').focusout(function() {
            var so_dinh_danh = $(this).val();
            if (so_dinh_danh) {
                $.ajax({
                    url: "{{ route('validCMND') }}",
                    type: "GET",
                    data: 'kh_giatri=' + so_dinh_danh,
                    success: function(err) {
                        $('#modal-ele-giay-to-tuy-than-so').tooltip({
                            title: err.message,
                            placement: 'bottom',
                            trigger: 'manual'
                        });
                        if (err.status === 'error') {
                            $('#modal-ele-giay-to-tuy-than-so').css('border', '1px solid red');
                            $('#modal-ele-giay-to-tuy-than-so').tooltip('show');
                        } else {
                            $('#modal-ele-giay-to-tuy-than-so').removeAttr('style', 'border');
                            $('#modal-ele-giay-to-tuy-than-so').tooltip('hide');
                        }
                    }
                });
            }
            if ($('#modal-ele-ho-duong-su').val() !== '' && $('#modal-ele-ten-duong-su').val() !== '') {
                var first_name = $('#modal-ele-ho-duong-su').val() + ' ' + $('#modal-ele-ten-duong-su').val() +
                    ' ' + $(this).val();
                $('#modal-ele-first_name').val(first_name);
            }
            $('#modal-ele-username').val($(this).val());
            $('#modal-ele-password').val(Math.floor(Math.random() * 999999) + 100000);
        });

        $('input:required').focusout(function() {
            $('#' + $(this).attr('id')).tooltip({
                title: 'Vui lòng không để trống!',
                placement: 'bottom',
                trigger: 'manual'
            });
            if (!$(this).val()) {
                $(this).css('border', '1px solid red');
                $('#' + $(this).attr('id')).tooltip("show");
            } else {
                $(this).removeAttr('style', 'border');
                $('#' + $(this).attr('id')).tooltip("hide");
            }
        });

        $('#dien-thoai').focusout(function() {
            $('#contact').val($(this).val());
        });

        function closeSelf() {
            self.close();
            return true;
        }

        $('#modal-ele-dien-thoai').focusout(function() {
            $('#modal-ele-contact').val($(this).val());
        });
    </script>
    <script>
        function convertDate(inputFormat) {
            function pad(s) {
                return (s < 10) ? '0' + s : s;
            }

            var d = new Date(inputFormat)
            return [pad(d.getDate()), pad(d.getMonth() + 1), d.getFullYear()].join('/')
        }

        // -------------------------------------- Tài sản --------------------------------------
        var selectedTaiSan = [];

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

        $("#searchTS").select2({
            ajax: {
                url: "{{ url('taisan/search') }}",
                method: "GET",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        keyword: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        // results: data.data
                        results: $.map(data.data, function(item) {
                            item.id = item.ts_id;
                            item.text = item.ts_nhan;
                            return item;
                        })
                    };
                },
                cache: false
            },
            placeholder: function() {
                $(this).data('placeholder');
            },
            minimumInputLength: 1,
            language: {
                inputTooShort: function(input) {
                    return "Nhập ít nhất " + input.minimum + " ký tự nhãn tài sản.";
                },
                noResults: function() {
                    return "Không tìm thấy vui lòng thêm mới!";
                },
                searching: function() {
                    return "Đang tìm...";
                },
            },
            templateResult: taiSanResultTemplater
        });
        let noidung = '';
        let number = 1;

        function addTaiSan() {
            var option = $('#searchTS').select2('data');
            var taisan = '';
            $.ajax({
                type: "GET",
                url: "{{ route('thongTinTaiSan') }}",
                data: {
                    id: option[0].id,
                    id_vanphong: '{{ \App\Models\NhanVienModel::find(Sentinel::getUser()->id)->nv_vanphong }}',

                },
                dataType: "json",
                cache: true,
                success: function(data) {
                    if (number == 1) {
                        $('#noidung').append($('input[name="radioTS"]:checked').val() + ': ' + data
                            .thong_tin_str);
                        $('#radioND').removeAttr("checked");
                        $('#radioTS').attr('checked', 'checked');
                    } else {
                        taisan = $('#noidung').val() + ('\n') + $('input[name="radioTS"]:checked').val() +
                            ': ' + data.thong_tin_str;
                        $('#noidung').val(taisan)
                        $('#noidung').append('\n');
                    }
                    number++
                }
            });
        }

        function capNhatTextNoiDung() {
            getTemplateNoiDung();
            if ($('#noidung').val() != '') {
                getTemplateNoiDung();
            }
        }

        function getTemplateNoiDung() {
            /*  let loaiHopDongId = $('#vanban option:selected').val();
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
                      id_vanphong: '{{ \App\Models\NhanVienModel::find(Sentinel::getUser()->id)->nv_vanphong }}',
                      ten_van_phong: '{{ \App\Models\ChiNhanhModel::find(\App\Models\NhanVienModel::find(Sentinel::getUser()->id)->nv_vanphong)->cn_ten }}'
                  },
                  dataType: "json",
                  cache: true,
                  success: function(data) {
                      let text = data.data + "\n";
                      $.each(selectedTaiSan, function(key, value) {
                          text += value.thong_tin_str + "\n";
                      })
                      $('#noidung').val(text);
                      $('#modal-tai-san').modal('hide');
                  }
              })*/
        }

        // ----------------------------- Đương sự --------------------------------------------------
        var selectedLabel = 'chuyen-nhuong';
        var soLuongDuongSu = 2;
        var nhomDuongSu = '';
        var benA = [];
        var benB = [];
        var benC = [];

        $(document).ready(function() {
            $("#cac-ben-lien-quan").select2({
                ajax: {
                    url: "{{ url('account/kh') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            tk_khachhang: params.term, // search term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.data
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1,
                language: {
                    inputTooShort: function(input) {
                        return "Nhập ít nhất " + input.minimum + " ký tự tên đương sự.";
                    },
                    noResults: function() {
                        return "Không tìm thấy vui lòng thêm mới!";
                    },
                    searching: function() {
                        return "Đang tìm...";
                    },
                },
                placeholder: function() {
                    $(this).data('placeholder');
                },
                templateResult: resultTemplater,
                templateSelection: selectionTemplater
            });

            $('#btn-ben-A').click(function() {
                nhomDuongSu = 'A';
                setUpListDuongSu('A');
            })
            $('#btn-ben-B').click(function() {
                nhomDuongSu = 'B';
                setUpListDuongSu('B');
            })
            $('#btn-ben-C').click(function() {
                nhomDuongSu = 'C';
                setUpListDuongSu('C');
            })
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
                    .append($("<a href='#'>").click(function() {
                        showThongTinDuongSu(object.id)
                    }).text(object.first_name))
                    .append($("<button>").attr('class', 'btn btn-default btn-xs pull-right remove-item').click(
                        function() {
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
                success: function(data) {
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
                        .append("<tr><td class=\"fit-column-kh\">" + item.tm_nhan +
                            "</td><td class=\"text-left\">" + item.kh_giatri + "</td></tr>");
                }
            })
        }

        $("#searchDS").select2({
            ajax: {
                url: "{{ url('account/kh') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        tk_khachhang: params.term, // search term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.data
                    };
                },
                cache: true
            },
            minimumInputLength: 1,
            language: {
                inputTooShort: function(input) {
                    return "Nhập ít nhất " + input.minimum + " ký tự tên đương sự.";
                },
                noResults: function() {
                    return "Không tìm thấy vui lòng thêm mới!";
                },
                searching: function() {
                    return "Đang tìm...";
                },
            },
            templateResult: resultTemplater,
            templateSelection: selectionTemplater,
            placeholder: function() {
                $(this).data('placeholder');
            },
        });
        let num = 1;
        document.getElementById("userinput").onblur = function() {
            //number-format the user input
            this.value = parseFloat(this.value.replace(/,/g, ""))
                .toFixed(2)
                .toString()
                .replace(/\B(?=(\d{3})+(?!\d))/g, ",");

            //set the numeric value to a number input
            document.getElementById("number").value = this.value.replace(/,/g, "")

        }

        function addDuongSu() {
            var option = $('#searchDS').select2('data');
            var duongsu = '';
            $.ajax({
                type: "GET",
                url: "{{ route('addDuongSu') }}",
                data: {
                    id: option[0].id,
                    id_vanphong: '{{ \App\Models\NhanVienModel::find(Sentinel::getUser()->id)->nv_vanphong }}',

                },
                dataType: "json",
                cache: true,
                success: function(data) {
                    if (num == 1) {
                        $('#duongsu').append($('input[name="radioDS"]:checked').val() + ': ' + data
                            .thong_tin_str);
                        $('#radioA').removeAttr("checked");
                        $('#radioB').attr('checked', 'checked');
                    } else if (num == 2) {
                        duongsu = $('#duongsu').val() + ('\n') + $('input[name="radioDS"]:checked').val() +
                            ': ' + data.thong_tin_str;
                        $('#duongsu').val(duongsu);
                        $('#duongsu').append('\n');
                        $('#radioB').removeAttr("checked");
                        $('#radioC').attr('checked', 'checked');
                    } else if (num == 3) {
                        duongsu = $('#duongsu').val() + ('\n') + $('input[name="radioDS"]:checked').val() +
                            ': ' + data.thong_tin_str;
                        $('#duongsu').val(duongsu);
                        $('#duongsu').append('\n');
                    } else {
                        duongsu = $('#duongsu').val() + ('\n') + $('input[name="radioDS"]:checked').val() +
                            ': ' + data.thong_tin_str;
                        $('#duongsu').val(duongsu);
                        $('#duongsu').append('\n');
                    }
                    num++
                }
            });
        }

        $('#update-duong-su').click(function() {
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
        })

        // ----------------------------- Functions --------------------------------------------------
    </script>
    <script>
        $(function() {
            $("#thu_lao").keyup(function(e) {
                $(this).val(format($(this).val()));
            });
            $("#phi_cong_chung").keyup(function(e) {
                $(this).val(format($(this).val()));
            });
        });
        var format = function(num) {
            var str = num.toString().replace("", ""),
                parts = false,
                output = [],
                i = 1,
                formatted = null;
            if (str.indexOf(".") > 0) {
                parts = str.split(".");
                str = parts[0];
            }
            str = str.split("").reverse();
            for (var j = 0, len = str.length; j < len; j++) {
                if (str[j] != ",") {
                    output.push(str[j]);
                    if (i % 3 == 0 && j < (len - 1)) {
                        output.push(",");
                    }
                    i++;
                }
            }
            formatted = output.reverse().join("");
            return ("" + formatted + ((parts) ? "." + parts[1].substr(0, 2) : ""));
        };
    </script>
    <script>
        $('.sel').select2();
        $(document).ready(function() {
            $('#nganchan').change(function() {
                $('#description').append('<label id="nganchanboi">Nhập số công chứng cũ: </label>',
                    '<input class="form-control" placeholder="Nhập số công chứng..." id="cancel_description" name="description" type="text" >'
                );
            });
            $('#thuong').change(function() {
                $('#nganchanboi').remove();
                $('#cancel_description').remove();
            });
            $('#giaitoa').change(function() {
                $('#nganchanboi').remove();
                $('#cancel_description').remove();
            });
            $('#canhbao').change(function() {
                $('#nganchanboi').remove();
                $('#cancel_description').remove();
            });
        });
        $(document).ready(function() {
            $("#kieuhd").change(function() {
                $.ajax({
                    url: "{{ route('listVanban') }}",
                    data: {
                        id: $('#kieuhd').val()
                    },
                    success: function(data) {
                        $("#vanban").empty();
                        data.map(function(val) {
                            if (val == null)
                                $("#vanban").empty();
                            else
                                $("#vanban").append(new Option(val.vb_nhan, val.vb_id));
                        });
                        $("#vanban").select2({
                            allowClear: true
                        });
                    }
                });
            });
        });
        // $("#btn-save").click(function(){
        //     $("#btn-save").addClass('disabled')
        // })

        function onSubmitHandler(event) {
            $('#animation').modal('show');
            $("#btn-save").attr('disabled', 'true');
            /* validate here */
        };
        function submitConfirm(){
           
            if (confirm("Mời xác nhận đăng tải hồ sơ ! Bấm Ok nếu đồng ý, Xin cảm ơn!") == true) {
                submitForm();
            } else {
                return;
            }
        };
        function submitForm() {
            //get date from id=ngay_cc , just get the year 
            var date = $('#ngay_cc').val();
            var year = date.substring(0, 4);
            var so_hd = $('#so_hd').val();
            var so_hd_year = '"' + so_hd + '/' + year + '"';
            $.ajax({
                type: "POST",
                url: "{{ route('checkTrungHoSo') }}",
                data: {
                    code: so_hd_year,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.result) {
                        $('#formPost').submit();
                    } else {
                        alert(response.message);
                        return;
                    }
                }
            });
        };
    </script>
@stop
