@extends('admin/layouts/default')
@section('title')
    Sửa hồ sơ giao dịch @parent
@stop
@section('header_styles')
    <style>
        .qksao {
            font-weight: bold;
            color: red;
        }

        .qkbtn {
            font-weight: bold;
            font-size: 14px !important;
        }

        .nqkright {
            text-align: right !important;
            font-size: 14px !important;
            font-weight: 500;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
@stop
@section('content')
    <section class="content">
        @php
            $role = Sentinel::check()->user_roles()->first()->slug;
            $user = Sentinel::getUser();
        @endphp
        <form action="{{ route('storeSuutra',['id'=>$data->st_id]) }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row bctk-scrollable-list" style="overflow-x: hidden; height: calc(100vh - 100px); ">
                <div class="col-sm-12">
                 
                    @if($role=='admin' || $role=='chuyen-vien-so' )
                    
                    @else
                        <div class="form-group col-md-12">
                            <label class="col-lg-4 col-form-label nqkright">
                                <div class="col-lg-12">
                                    Nhóm công văn: (<span class="text-danger qksao">*</span>)
                                </div>
                            </label>
                            <div class="col-lg-8">
                                {!! \App\Helpers\Form::select('vb_kieuhd',$kieuhd,$kieu,['id'=>'kieuhd','class'=>'form-control sel','style'=>'width: 100%','required']) !!}
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label class="col-lg-4 col-form-label nqkright">
                                <div class="col-lg-12">
                                    Tên công văn: (<span class="text-danger qksao">*</span>)
                                </div>
                            </label>
                            <div class="col-lg-8">
                                @if($role!="ke-toan")
                                    {!! \App\Helpers\Form::select('ten',$vanban,$data->vanban,['id'=>'ten','class'=>'form-control sel','required']) !!}
                                @else
                                    {!! \App\Helpers\Form::select('ten',$vanban,$data->vanban,['id'=>'ten','class'=>'form-control sel','disabled=disabled']) !!}
                                @endif
                            </div>
                        </div>
                    @endif
{{--                        {{ dd(intval($data->vanban)) }}--}}
                    <div class="form-group col-md-12">
                        <label class="col-lg-4 col-form-label nqkright">
                            <div class="col-lg-12">
                                Công chứng viên: (<span class="text-danger qksao">*</span>)
                            </div>
                        </label>
					<div class="col-lg-8">
                  {!! \App\Helpers\Form::select('id_ccv',$ccv,$data->ccv,['id'=>'id_ccv','class'=>'form-control sel','required']) !!}

                        </div>
                    </div>
					<div class="form-group col-md-12" >
                        <label class="col-lg-4 col-form-label nqkright">
                            <div class="col-lg-12">
                               Chuyên viên: 
                            </div>
                        </label>
                        <div class="col-lg-8">
                         
                                {!! \App\Helpers\Form::select('cv_id',$cv,Request::input('cv_id'),['id'=>'cv_id','class'=>'form-control sel']) !!}
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <label class="col-lg-4 col-form-label nqkright">
                            <div class="col-lg-12">
                                Số công chứng: (<span class="text-danger qksao">*</span>)
                            </div>
                        </label>
                        <div class="col-lg-8">
                            <input type="text" id="so_hd" name="so_hd" class="form-control"
                                   placeholder="Nhập số hợp đồng ..." 
                                   required>
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <label class="col-lg-4 col-form-label nqkright">
                            <div class="col-lg-12">
                                Ngày công chứng/ngăn chặn: (<span class="text-danger qksao">*</span>)
                            </div>
                        </label>
                        <div class="col-lg-8">
                            <input data-date-format="dd-mm-yyyy" type="date" id="ngay_cc"  name="ngay_cc"
                                   class="form-control"   required>
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <label class="col-lg-4 col-form-label">
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
                                    <button type="button" class="btn btn-success pull-right"
                                            onclick="addDuongSu()">Chèn >>>
                                    </button>
                                    <a href="javascript:void(0)" data-toggle="modal" data-target="#create-customer"
                                       class="btn btn-primary btn2 pull-right">Thêm
                                    </a>
                                </div>
                            </div>
                        </label>
                        <div class="col-lg-8">
                            <textarea type="text" id="duongsu" name="duongsu"
                                      class="form-control mt-3" rows="7" cols="50"
                                      required>{!! $data->duong_su !!}</textarea>
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <label class="col-lg-4 col-form-label">
                            <div class="col-lg-12">
                                <div class="col-lg-12 nqkright">
                                    Nội dung công văn: (<span class="text-danger qksao">*</span>)
                                </div>
                                <br>
                                <div class="col-lg-12 form-check nqkright">
                                    <input type="radio" name="radioTS" value="Nội dung" id="radioND" checked> Nội dung
                                    <input type="radio" name="radioTS" value="Tài sản" id="radioTS"> Tài sản
                                </div>
                                <div class="col-lg-12 nqkright">
                                    <select class="form-control" id="searchTS"></select>
                                </div>
                                <div class="col-lg-12 nqkright">
                                    <button type="button" class="btn btn-success pull-right"
                                            onclick="addTaiSan()">Chèn >>>
                                    </button>
                                    <a href="{{route('createTaiSan')}}" target="_blank"
                                       class="btn btn-primary btn2 pull-right">Thêm
                                    </a>
                                </div>
                            </div>
                        </label>
                        <div class="col-lg-8">
                            <textarea type="text" id="noidung" name="noidung" class="form-control mt-3" rows="7"
                                      cols="50" required>{!! $data->texte !!}</textarea>
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <label class="col-lg-4 col-form-label nqkright">
                            <div class="col-lg-12">Loại:</div>
                        </label>
                        <div class="col-lg-8">
		
                            <input type="radio" name="loai" value="0" id="thuong" @if($data->ngan_chan==0) checked @endif > Thường
                            @if($role=='chuyen-vien-so' || $role=='admin')
                                <input type="radio" name="loai" value="3" id="nganchan" @if($data->ngan_chan==3) checked @endif > Ngăn chặn
                            @endif
                            @if($role == 'truong-van-phong'|| $role=='cong-chung-vien' || $role == 'chuyen-vien' || $role == 'phong-khac')
                                <input type="radio" name="loai" value="2" id="canhbao" @if($data->ngan_chan==2) checked @endif> Cảnh báo
                            @endif
                           
                            <div class="col-md-12" id="description">
                            </div>
                        </div>
                    </div>
					@if(!Sentinel::inRole('admin'))
                    <div class="form-group col-md-12">
                        <label class="col-lg-4 col-form-label nqkright">
                            <div class="col-lg-12">Nhập số công chứng cũ:</div>
                        </label>
                        <div class="col-lg-8">
                            <input type="text" id="so_hd" value="{{$data->so_hd}}" name="description" class="form-control"
                                   placeholder="Nhập số hợp đồng ...">
                        </div>
                    </div>
					<div class="form-group col-md-12">
                        <label class="col-lg-4 col-form-label nqkright">
                            <div class="col-lg-12">Thời hạn:</div>
                        </label>
                        <div class="col-lg-8">
                            <input type="text" id="contract_period" value="{{$data->contract_period}}" name="contract_period" class="form-control"
                                   placeholder="Thời hạn ...">
                        </div>
                    </div>
					<div class="form-group col-md-12">
                        <label class="col-lg-4 col-form-label nqkright">
                            <div class="col-lg-12">Thù Lao:</div>
                        </label>
                        <div class="col-lg-8">
                            <div class="input-group">
                                <input type="text" value="{{old('thu_lao')}}" class="form-control"
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
                                <input type="text" value="{{old('phi_cong_chung')}}" placeholder="0"
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
                                <button type="submit" class="btn btn-primary qkbtn">Lưu</button>
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
                                @foreach($kieuDS as $item)
                                    @php
                                        $k_id = $item->k_id;
                                        $tm = \App\Models\KieuModel::select('k_tieumuc')->where('k_id', $k_id)->first();
                                        $tm_arr = explode(' ', $tm->k_tieumuc);
                                        $tieumuc = \App\Models\TieuMucModel::select('tieumuc.tm_id', 'tm_nhan', 'tm_loai', 'tm_keywords', 'tm_batbuoc')
                                                ->leftjoin('tieumuc_sapxep', 'tieumuc_sapxep.tm_id', '=', 'tieumuc.tm_id')
                                                ->whereIn('tieumuc.tm_id', $tm_arr)
                                                ->where('k_id', $k_id)
                                                ->orderBy('tm_sort', 'asc')->get();
                                    @endphp
                                    <thead>
                                    <tr class="text-center" style="background-color:#eeeeee">
                                        <th>{{ $item->k_nhan }}</th>
                                        <th>
                                            <a href="#" class="btn btn-primary mb-0"
                                               data-toggle="modal" data-target="#modal-honphoi{{ $item->k_id }}">
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
                                                            @foreach($tieumuc as $tm)
                                                                <div class="col-md-3 {{$tm->tm_keywords == 'tinh-trang-hon-nhan' ? 'hidden' : ''}}">
                                                                    <div class="form-group">
                                                                        <label class="text-bold"
                                                                               for="modal-ele-{{$tm->tm_keywords}}">{{$tm->tm_nhan}}
                                                                            :</label>
                                                                        <input type="text" name="ds_tm[]"
                                                                               value="tm-{{$tm->tm_id}}" hidden>
                                                                        @if($tm->tm_loai == "text")
                                                                            <input id="modal-ele-{{$tm->tm_keywords}}"
                                                                                   type="text" name="tm-{{$tm->tm_id}}"
                                                                                   class="form-control"
                                                                                   @if($tm->tm_batbuoc == 1) required @endif>
                                                                        @elseif($tm->tm_loai == "select")
                                                                            <?php
                                                                            $select = \App\Models\KieuTieuMucModel::where('tm_id',
                                                                                $tm->tm_id)
                                                                                ->where('ktm_status', 1)
                                                                                ->pluck('ktm_traloi', 'ktm_id');
                                                                            ?>
                                                                            {!! \App\Helpers\Form::select('tm-'.$tm->tm_id,$select,'',['class'=>'form-control','id'=>'modal-ele-'.$tm->tm_keywords,'onchange'=>'change_tm(this,\'in-modal\')']) !!}
                                                                        @elseif($tm->tm_loai == 'file')
                                                                            <input id="{{$tm->tm_keywords}}"
                                                                                   name="tm-{{$tm->tm_id}}[]"
                                                                                   type="file" accept="image/*"
                                                                                   class="form-control"
                                                                                   onchange="img(this)"
                                                                                   @if($tm->tm_batbuoc == 1) required
                                                                                   @endif multiple/>
                                                                            <div class="col-md-12 text-center row-image"
                                                                                 style="background-color: #fff !important; height: 80px"
                                                                                 id="img-{{$tm->tm_keywords}}"></div>
                                                                        @else
                                                                            <input type="text"
                                                                                   id="modal-ele-{{$tm->tm_keywords}}"
                                                                                   class="form-control"
                                                                                   name="tm-{{$tm->tm_id}}"
                                                                                   data-mask="99/99/9999"
                                                                                   placeholder="Ngày / tháng / năm"
                                                                                   @if($tm->tm_batbuoc == 1) required @endif>
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
                                                                            <img src="{{url('/images/new-user.png')}}"
                                                                                 alt="profile pic">
                                                                        </div>
                                                                        <div
                                                                                class="fileinput-preview fileinput-exists thumbnail"
                                                                                style="max-width: 250px; max-height: 250px;"></div>
                                                                        <div>
                                                                            <span class="btn btn-primary btn-file">
                                                                                <span
                                                                                        class="fileinput-new">Chọn ảnh</span>
                                                                                <span
                                                                                        class="fileinput-exists">Thay đổi</span>
                                                                                <input id="modal-ele-pic" name="pic"
                                                                                       type="file"
                                                                                       class="form-control"/>
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
                                                                           class="form-control" name="username"
                                                                           required>
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
                                                                    <label class="text-bold" for="modal-ele-first_name">Nhãn:</label>
                                                                    <input type="text" id="modal-ele-first_name"
                                                                           class="form-control" name="first_name"
                                                                           required>
                                                                    <span id="modal-ele-valid-first_name"
                                                                          class="text-small text-danger pl-1 pt-1"></span>
                                                                </div>
                                                                <input type="text" id="modal-ele-contact" name="contact"
                                                                       hidden>
                                                                <input type="text" id="modal-ele-contact" name="kieu"
                                                                       value="{{$k_id}}" hidden>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                                <div class="modal-footer"
                                                     style="background-color: #f7f7f7;color: black">
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
@section('footer_scripts')
     <script src="{{ asset('assets/js/imgPreview.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.mark.js') }}"></script>
	<script src="{{ asset('assets/js/jquery.highlight-5.js') }}"></script>
	<script src="{{ asset('assets/js/select2.min.js') }}"></script>
		<script src="{{ asset('assets/js/jquery-ui.js') }}"></script>
    <script>
        function submitHonPhoi(id) {
            $(`#modal-honphoi${id}`).modal('hide');
            $('#create-customer').modal('hide');
            var dataForm = new FormData($('#form-honphoi')[0]);
            $.ajax({
                url: "{{route('storeKhachHang')}}",
                type: 'post',
                processData: false,
                contentType: false,
                data: dataForm,
                success: function (res) {
                    console.log(res)
                    if (res.status === 'success') {
                        msgSuccess(res.message);
                    } else {
                        $.each(res.message, function (k, v) {
                            msgError(v);
                        })
                    }
                }
            })
        }

        $('#modal-ele-giay-to-tuy-than-so').focusout(function () {
            var so_dinh_danh = $(this).val();
            if (so_dinh_danh) {
                $.ajax({
                    url: "{{route('validCMND')}}",
                    type: "GET",
                    data: 'kh_giatri=' + so_dinh_danh,
                    success: function (err) {
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
                var first_name = $('#modal-ele-ho-duong-su').val() + ' ' + $('#modal-ele-ten-duong-su').val() + ' ' + $(this).val();
                $('#modal-ele-first_name').val(first_name);
            }
            $('#modal-ele-username').val($(this).val());
            $('#modal-ele-password').val(Math.floor(Math.random() * 999999) + 100000);
        });

        $('input:required').focusout(function () {
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

        $('#dien-thoai').focusout(function () {
            $('#contact').val($(this).val());
        });

        function closeSelf() {
            self.close();
            return true;
        }

        $('#modal-ele-dien-thoai').focusout(function () {
            $('#modal-ele-contact').val($(this).val());
        });
    </script>
    <script>
       
    </script>
    <script>
        $(document).ready(function () {
            $('#nganchan').change(function () {
                $('#description').append('<label id="nganchanboi">Ngăn chặn bởi: </label>',
                    '<input class="form-control" placeholder="Ngăn chặn bởi hợp đồng số..." id="cancel_description" name="description" type="text" >');
            });
            $('#giaitoa').change(function () {
                $('#labelnganchan').remove();
                $('#nganchanboi').remove();
                $('#cancel_description').remove();
            });
            $('#canhbao').change(function () {
                $('#labelnganchan').remove();
                $('#nganchanboi').remove();
                $('#cancel_description').remove();
            });
        });
    </script>
    <script>
        $('.sel').select2();
        $(document).ready(function () {
            $("#kieuhd").change(function () {
                $.ajax({
                    url: "{{ route('listVanban') }}",
                    data: {
                        id: $('#kieuhd').val()
                    },
                    success: function (data) {
                        $("#ten").empty();
                        data.map(function (val) {
                            if (val == null)
                                $("#ten").empty();
                            else
                                $("#ten").append(new Option(val.vb_nhan, val.vb_id));
                        });
                        $("#ten").select2({
                            allowClear: true
                        });
                    }
                });
            });
        });
        $(function () {
            $("#ngay_cc").datepicker();
        });
    </script>
    <script>
        $(document).ready(function () {
            $("#kieuhd").change(function () {
                $.ajax({
                    url: "{{ route('listVanban') }}",
                    data: {
                        id: $('#kieuhd').val()
                    },
                    success: function (data) {
                        $("#vanban").empty();
                        data.map(function (val) {
                            if (val == null)
                                $("#vanban").empty();
                            else
                                $("#vanban").append(new Option(val.vb_nhan, val.vb_id + '.$.' + val.vb_nhan));
                        });
                        $("#vanban").select2({
                            allowClear: true
                        });
                    }
                });
            });
        });
    </script>
    <script>
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
            minimumInputLength: 1,
            language: {
                inputTooShort: function (input) {

                    return "Nhập ít nhất " + input.minimum + " ký tự nhãn tài sản.";
                },
                noResults: function () {
                    return "Không tìm thấy vui long thêm mới!";
                },
                searching: function () {
                    return "Đang tìm...";
                },
            },
            templateResult: taiSanResultTemplater
        });

        $("#searchTS").select2({
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
            minimumInputLength: 1,
            language: {
                inputTooShort: function (input) {

                    return "Nhập ít nhất " + input.minimum + " ký tự nhãn tài sản.";
                },
                noResults: function () {
                    return "Không tìm thấy vui lòng thêm mới!";
                },
                searching: function () {
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
                    id: option[0].id
                },
                dataType: "json",
                cache: true,
                success: function (data) {
                    if (number == 1) {
                        $('#noidung').append($('input[name="radioTS"]:checked').val() + ': ' + data.thong_tin_str);
                        $('#radioND').removeAttr("checked");
                        $('#radioTS').attr('checked', 'checked');
                    } else {
                        taisan = $('#noidung').val() + ('\n') + $('input[name="radioTS"]:checked').val() + ': ' + data.thong_tin_str;
                        console.log($('#noidung').val());
                        console.log(taisan);
                        $('#noidung').val(taisan)
                        $('#noidung').append('\n');
                    }
                    number++
                }
            });
        }

        // ----------------------------- Đương sự --------------------------------------------------
        var selectedLabel = 'chuyen-nhuong';
        var soLuongDuongSu = 2;
        var nhomDuongSu = '';
        var benA = [];
        var benB = [];
        var benC = [];

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


        $("#searchDS").select2({
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
            minimumInputLength: 1,
            language: {
                inputTooShort: function (input) {

                    return "Nhập ít nhất " + input.minimum + " ký tự tên đương sự.";
                },
                noResults: function () {
                    return "Không tìm thấy vui lòng thêm mới!";
                },
                searching: function () {
                    return "Đang tìm...";
                },
            },
            templateResult: resultTemplater,
            templateSelection: selectionTemplater,
            placeholder: function () {
                $(this).data('placeholder');
            },
        });
        let num = 1;

        function addDuongSu() {
            var option = $('#searchDS').select2('data');
            var duongsu = '';
            console.log(option)
            $.ajax({
                type: "GET",
                url: "{{ route('addDuongSu') }}",
                data: {
                    id: option[0].id
                },
                dataType: "json",
                cache: true,
                success: function (data) {
                    console.log(data)
                    if (num == 1) {
                        $('#duongsu').append($('input[name="radioDS"]:checked').val() + ': ' + data.thong_tin_str);
                        $('#radioA').removeAttr("checked");
                        $('#radioB').attr('checked', 'checked');
                    } else if (num == 2) {
                        duongsu = $('#duongsu').val() + ('\n') + $('input[name="radioDS"]:checked').val() + ': ' + data.thong_tin_str;
                        $('#duongsu').val(duongsu);
                        $('#duongsu').append('\n');
                        $('#radioB').removeAttr("checked");
                        $('#radioC').attr('checked', 'checked');
                    } else if (num == 3) {
                        duongsu = $('#duongsu').val() + ('\n') + $('input[name="radioDS"]:checked').val() + ': ' + data.thong_tin_str;
                        $('#duongsu').val(duongsu);
                        $('#duongsu').append('\n');
                    } else {
                        duongsu = $('#duongsu').val() + ('\n') + $('input[name="radioDS"]:checked').val() + ': ' + data.thong_tin_str;
                        $('#duongsu').val(duongsu);
                        $('#duongsu').append('\n');
                    }
                    num++
                }
            });
        }
    </script>
@stop
