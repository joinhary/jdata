@extends('admin/layouts/default')
@section('title')
    Nhập hồ sơ ngăn chặn   @parent
@stop
@php
    $role = Sentinel::check()->user_roles()->first()->slug;
    $vp=\App\Models\NhanVienModel::find(Sentinel::getUser()->id)->nv_vanphong;
@endphp
@section('header_styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
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
            $role = Sentinel::check()->user_roles()->first()->slug;
            $user = Sentinel::getUser();
        @endphp
        <form action="{{ route('storeSuutra') }}" method="post" enctype="multipart/form-data" onsubmit="onSubmitHandler()">
            @csrf
            <div class="row bctk-scrollable-list" style="overflow-x: hidden; height: calc(100vh - 100px) ;">
                <input id="id_ccv" name="id_ccv" value="{{Sentinel::getUser()->id}}" hidden>
                <div class="col-sm-12">
                    @if($role=='admin' || $role=='chuyen-vien-so' || $vp === "2190")
                        <div class="form-group col-md-12">
                            <label class="col-lg-4 col-form-label nqkright">
                                <div class="col-lg-12">
                                    Tên văn bản: (<span class="text-danger qksao">*</span>)
                                </div>
                            </label>
                            <div class="col-lg-8">
                                <input type="text" value="{{Request::input('ten')}}" id="ten" name="ten"
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
                                {!! \App\Helpers\Form::select('vb_kieuhd',$kieuhd,Request::input('vb_kieuhd'),['id'=>'kieuhd','class'=>'form-control sel','style'=>'width: 100%']) !!}
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label class="col-lg-4 col-form-label nqkright">
                                <div class="col-lg-12">
                                    Tên văn bản: (<span class="text-danger qksao">*</span>)
                                </div>
                            </label>
                            <div class="col-lg-8">
                                <select class="form-control sel" id="vanban" name="ten" onchange="capNhatTextNoiDung()" required style="width: 100%">
                                    <option value="">--- Chọn tên văn bản ---</option>
                                </select>
                            </div>
                        </div>
                    @endif
                    <div class="form-group col-md-12" hidden>
                        <label class="col-lg-4 col-form-label nqkright">
                            <div class="col-lg-12">
                                Công chứng viên: (<span class="text-danger qksao">*</span>)
                            </div>
                        </label>
                        <div class="col-lg-8">
                            @if($role == 'cong-chung-vien' || $role == 'chuyen-vien-so' || $role == 'admin' || $role == 'truong-van-phong')
                                <select class="form-control select2" name="id_ccv" required>
                                    <option value="{{ $user->id }}">{{ $user->first_name }}</option>
                                </select>
                            @else
                                {!! \App\Helpers\Form::select('id_ccv',$ccv,Request::input('id_ccv'),['id'=>'id_ccv','class'=>'form-control sel']) !!}
                            @endif
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
                                Ngày ngăn chặn: (<span class="text-danger qksao">*</span>)
                            </div>
                        </label>
                        <div class="col-lg-8">
                            <input placeholder="dd-mm-yyyy" data-date-format="dd-mm-yyyy" type="date" id="ngay_cc" name="ngay_cc" value="{{ Request::input('ngay_cc') }}"
                                   class="form-control" required>
                        </div>
                    </div>
					<div class="form-group col-md-12">
                        <label class="col-lg-4 col-form-label nqkright">
                            <div class="col-lg-12">
                                Ngày nhận công văn: (<span class="text-danger qksao">*</span>)
                            </div>
                        </label>
                        <div class="col-lg-8">
                            <input data-date-format="dd-mm-yyyy" placeholder="dd-mm-yyyy" type="date" id="prevent_doc_receive_date" name="prevent_doc_receive_date" value="{{ Request::input('prevent_doc_receive_date') }}"
                                   class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <div class="col-lg-4 col-form-label">
                            <div class="col-lg-12">
                                <div class="col-lg-12 nqkright">
                                    Các bên liên quan: (<span class="text-danger qksao">*</span>)
                                </div>
                                
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <textarea type="text" id="duongsu" name="duongsu" class="form-control mt-3"
                                      rows="2" cols="50" required>{!! Request::input('duongsu') !!}</textarea>
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <div class="col-lg-4 col-form-label">
                            <div class="col-lg-12">
                                <div class="col-lg-12 nqkright">
                                    Nội dung văn bản: 
                                </div>
                                <br>
                        <select id="propertyType" class="form-control">
							<option value="0">Nhà đất</option>
							<option value="1">Ôtô- xe máy</option>
							<option value="2">Tài sản khác</option>
						</select>
						<br>
							<button type="button" class="btn btn-primary" onclick="importProptyInfo()">
  Add
</button>
                            </div>
							
                        </div>
					
                        <div class="col-lg-8">
                            <textarea type="text" id="noidung" name="noidung" class="form-control mt-3"
                                      rows="7" cols="50" >{!! Request::input('noidung') !!}</textarea>
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <label class="col-lg-4 col-form-label nqkright">
                            <div class="col-lg-12">Loại:</div>
                        </label>
                        <div class="col-lg-8">
                                <input type="radio" name="loai" value="3" checked id="nganchan"> Ngăn chặn
                           
                        
                        </div>
                    </div>
                    {{-- <div class="form-group col-md-12">
                        <label class="col-lg-4 col-form-label nqkright">
                            <div class="col-lg-12">Mức độ:</div>
                        </label>
                        <div class="col-lg-8">
                            <input type="radio" name="important" value="0" checked id="important"> Thường &nbsp;
                            <input type="radio" name="important" value="1"  id="important"> Quan trọng
                            &nbsp; &nbsp;<p> <i style="color:red; font-size:12px">*Nếu là quan trọng, thông báo về hồ sơ sẽ tự tạo và hiển thị ngoài trang chủ mặc định là 3 ngày</i></p>
                        </div>
                    </div> --}}
					    <div class="form-group col-md-12">
                        <label class="col-lg-4 col-form-label nqkright">
                            <div class="col-lg-12">Người(đơn vị) gửi yêu cầu:</div>
                        </label>
                        <div class="col-lg-8">
                            <input type="text" id="ccv_master" name="ccv_master" class="form-control"
                                   placeholder="Người(đơn vị) gửi yêu cầu ...">
                        </div>
                    </div>
					@if(!Sentinel::inRole('admin'))
                    @if($vp === "2190")
                    @else
                    <div class="form-group col-md-12">
                        <label class="col-lg-4 col-form-label nqkright">
                            <div class="col-lg-12">Nhập số công chứng cũ:</div>
                        </label>
                        <div class="col-lg-8">
                            <input type="text" id="so_hd" name="description" class="form-control"
                                   placeholder="Nhập số hợp đồng ...">
                        </div>
                    </div>
                    @endif
					@endif
                    <div class="form-group col-md-12">
										@if(Sentinel::inRole('admin') || $vp === "2190")

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
                                <div class="row text-center row-image x_content" id="img" style="height: 80px"></div>
                                <div>
                            <span class=" btn-file">
                                <input id="pic" name="pic[]" type="file" accept="image/*"
                                       class="form-control" onchange="loadImgKH(this,'modal')" multiple/>
                            </span>
                                </div>
                            </div>
                        </div>
                    </div>
					@if(!Sentinel::inRole('admin') )
                    @if($vp === "2190")
                    @else
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
					@endif
					

                    <div class="form-group col-md-12">
                        <label class="col-lg-4 col-form-label nqkright"></label>
                        <div class="col-lg-8">
                            <a href="javascript:history.back()" type="cancel"
                               class="btn btn-secondary qkbtn">Hủy</a>
                            <button id="btn-save" type="submit" class="btn btn-primary qkbtn">Lưu</button>
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
                                                                <div class="col-sm-3 {{$tm->tm_keywords == 'tinh-trang-hon-nhan' ? 'hidden' : ''}}">
                                                                    <div class="form-group">
                                                                        <label class="text-bold"
                                                                               for="modal-ele-{{$tm->tm_keywords}}">
                                                                            {{$tm->tm_nhan}}
                                                                            @if($tm->tm_batbuoc == 1)
                                                                                (
                                                                                <span class="text-danger qksao">*</span>
                                                                                )
                                                                            @endif:
                                                                        </label>
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
                                                                        <div class="fileinput-preview fileinput-exists thumbnail"
                                                                             style="max-width: 250px; max-height: 250px;"></div>
                                                                        <div>
                                                                            <span class="btn btn-primary btn-file">
                                                                                <span class="fileinput-new">Chọn ảnh</span>
                                                                                <span class="fileinput-exists">Thay đổi</span>
                                                                                <input id="modal-ele-pic" name="pic"
                                                                                       type="file"
                                                                                       class="form-control"/>
                                                                            </span>
                                                                            <a href="#"
                                                                               class="btn btn-danger fileinput-exists"
                                                                               data-dismiss="fileinput">Gỡ bỏ</a>
                                                                        </div>
                                                                    </div>
                                                                    <span class="help-block">{{ $errors->first('pic_file', ':message') }}</span>
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
		
		
		<!-- Modal them thong tin tai san-->
<div class="modal fade bd-example-modal-lg" id="propertyModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Nhập thông tin tài sản</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        </button>
      </div>
      <div class="modal-body">
        <div id="type-0" hidden>
		<div class="col-md-3"><label>Địa chỉ</label></div>
		<div class="col-md-9">		
		<input id="address" name="1" class="form-control">
		</div>
		<div class="col-md-3 mt-1"><label>Số giấy chứng nhận</label></div>
		<div class="col-md-9 mt-1">		
		<input id="land_certificate" name="2" class="form-control">
		</div>
		<div class="col-md-3 mt-1"><label>Số vào sổ</label></div>
		<div class="col-md-9 mt-1">		
		<input id="number_in_book" name="2" class="form-control">
		</div>
		<div class="col-md-3 mt-1"><label>Nơi cấp</label></div>
		<div class="col-md-3 mt-1">		
		<input id="land_issue_place" name="3" class="form-control">
		</div>
		<div class="col-md-3 mt-1"><label>Ngày cấp</label></div>
		<div class="col-md-3 mt-1">		
		<input id="land_issue_date" data-date-format="dd-mm-yyyy" type="date" name="3" class="form-control">
		</div>
			<div class="col-md-3 mt-1"><label>Thửa đất số</label></div>
		<div class="col-md-9 mt-1">		
		<input id="land_number" name="2" class="form-control">
		</div>
				<div class="col-md-3 mt-1"><label>Tờ bản đồ số </label></div>
		<div class="col-md-9 mt-1">		
		<input id="land_map_number" name="2" class="form-control">
		</div>
		<div class="col-md-3 mt-1"><label>Diện tích</label></div>
		<div class="col-md-2 mt-1">		
		<input id="land_area" name="3" class="form-control">
		</div>
		<div class="col-md-3 mt-1">		
		Hình thức sử dụng
		</div>
		<div class="col-md-1 mt-1">		
		Riêng
		</div>
		<div class="col-md-1 mt-1">		
				<input id="land_private_area" name="3" class="form-control">

		</div>
		<div class="col-md-1 mt-1">		
		Chung
		</div>
		<div class="col-md-1 mt-1">		
				<input id="land_public_area" name="3" class="form-control">

		</div>
				<div class="col-md-3 mt-1"><label>Mục đích sử dụng </label></div>
		<div class="col-md-9 mt-1">		
		<input id="land_use_perpose" name="2" class="form-control">
		</div>
				<div class="col-md-3 mt-1"><label>Thời hạn sử dụng </label></div>
		<div class="col-md-9 mt-1">		
		<input id="land_use_period" name="2" class="form-control">
		</div>
					<div class="col-md-3 mt-1"><label>Nguồn gốc sử dụng </label></div>
		<div class="col-md-9 mt-1">		
		<input id="land_use_origin" name="2" class="form-control">
		</div>
					<div class="col-md-3 mt-1"><label>Tài sản gắn liền với đất </label></div>
		<div class="col-md-9 mt-1">		
		<input id="land_associate_property" name="2" class="form-control">
		</div>
	
		
		</div>

		<div id="type-1" hidden>
		<div class="col-md-3 mt-1"><label>Biển kiểm soát</label></div>
		<div class="col-md-9 mt-1">		
		<input id="car_license_number" name="1" class="form-control">
		</div>
		<div class="col-md-3 mt-1"><label>Số giấy đăng ký</label></div>
		<div class="col-md-9 mt-1">		
		<input id="car_regist_number" name="1" class="form-control">
		</div>
		<div class="col-md-3 mt-1"><label>Nơi cấp</label></div>
		<div class="col-md-3 mt-1">		
		<input id="car_issue_place" name="1" class="form-control">
		</div>
		<div class="col-md-3 mt-1"><label>Ngày cấp</label></div>
		<div class="col-md-3 mt-1">		
		<input id="car_issue_date" data-date-format="dd-mm-yyyy" type="date" name="1" class="form-control">
		</div>
		<div class="col-md-3 mt-1"><label>Số khung</label></div>
		<div class="col-md-3 mt-1">		
		<input id="car_frame_number" name="1" class="form-control">
		</div>
		<div class="col-md-3 mt-1"><label>Số máy</label></div>
		<div class="col-md-3 mt-1">		
		<input id="car_machine_number" name="1" class="form-control">
		</div>
		
		</div>
		<div id="type-2" hidden>
		<div class="col-md-3"><label>Thông tin tài sản</label></div>
		<div class="col-md-9">		
		<input id="property_info" name="1" class="form-control">
		</div>
		</div>
		<div>
		<div class="col-md-3 mt-1"><label>Thông tin khác </label></div>
		<div class="col-md-9 mt-1">		
		<input id="other_info" name="2" class="form-control">
		</div>
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
        <button type="button" onclick="importProperty()" class="btn btn-primary">Thêm</button>
      </div>
    </div>
  </div>
</div>
    </section>

@stop
@include('admin.layouts.loading')
@section('footer_scripts')
     <script src="{{ asset('assets/js/imgPreview.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.mark.js') }}"></script>
	<script src="{{ asset('assets/js/jquery.highlight-5.js') }}"></script>
	<script src="{{ asset('assets/js/select2.min.js') }}"></script>
		<script src="{{ asset('assets/js/jquery-ui.js') }}"></script>
    <script>
	function importProptyInfo(){
		let type=$("#propertyType").val();
	$("#propertyModal").modal();
		if(type==0){
					$("#type-0").removeAttr("hidden")
					$("#type-1").attr("hidden",true)
					$("#type-2").attr("hidden",true)

		}
		if(type==1){
					$("#type-1").removeAttr("hidden")
					$("#type-0").attr("hidden",true)
					$("#type-2").attr("hidden",true)
		}
		if(type==2){
					$("#type-2").removeAttr("hidden")
					$("#type-1").attr("hidden",true)
					$("#type-0").attr("hidden",true)
		}
		
		
	
		
	}
	function importProperty(){
		let type=$("#propertyType").val();
		if(type==0){
		importLandPorperty()
		}
		if(type==1){
					importCarPorperty()
		}
		if(type==2){
		importOtherProperty()
		}
		$("#propertyModal").modal('hide');
	}
	
	function importLandPorperty(){
		
		let land_issue_date  = new Date($("#land_issue_date").val());
		land_issue_date=land_issue_date.toLocaleDateString("vi-VN");
		land_issue_date=$("#land_issue_date").val()?`Ngày cấp: ${land_issue_date};`:''
		let address=$("#address").val()?`Địa chỉ: ${$("#address").val()};`:'';
		let land_certificate=$("#land_certificate").val()?`Số giấy chứng nhận: ${$("#land_certificate").val()};`:'';
		let land_issue_place=$("#land_issue_place").val()?`Nơi cấp: ${$("#land_issue_place").val()};`:'';
		let land_number=$("#land_number").val()?`Số thửa đất: ${$("#land_number").val()};`:'';
			let land_map_number=$("#land_map_number").val()?`Số tờ bản đồ: ${$("#land_map_number").val()};`:'';
		let land_area=$("#land_area").val()?`Diện tích: ${$("#land_area").val()};`:'';
		let land_private_area=$("#land_private_area").val()?`Diện tích sử dụng riêng: ${$("#land_private_area").val()};`:'';
		let land_public_area=$("#land_public_area").val()?`Diện tích sử dụng chung: ${$("#land_public_area").val()};`:'';
		let land_use_perpose=$("#land_use_perpose").val()?`Mục đích sử dụng: ${$("#land_use_perpose").val()};`:'';
		let land_use_origin=$("#land_use_origin").val()?`Nguồn gốc sử dụng: ${$("#land_use_origin").val()};`:'';
		let land_use_period=$("#land_use_period").val()?`Thời hạn sử dụng: ${$("#land_use_period").val()};`:'';
		let land_associate_property=$("#land_associate_property").val();
		let other_info=$("#other_info").val();
		let number_in_book=$("#number_in_book").val()?`Số vào sổ ${$("#number_in_book").val()};`:"";
		let propertyInfo=`${address} ${land_certificate} ${number_in_book} ${land_issue_place} ${land_issue_date} ${land_number}  ${land_map_number} ${land_area} ${land_private_area} ${land_public_area} ${land_use_perpose} ${land_use_period} ${land_use_origin} ${land_associate_property} ${other_info}` 
		let existInfo=$("#noidung").val()?$("#noidung").val()+'\n':""
		$("#noidung").val(existInfo+propertyInfo);
	}
	
	function importCarPorperty(){
		let car_issue_date  = new Date($("#car_issue_date").val());
		car_issue_date=car_issue_date.toLocaleDateString("vi-VN");
		car_issue_date=$("#car_issue_date").val()?`Ngày cấp: ${car_issue_date};`:''
		let car_license_number=$("#car_license_number").val()?`Biển kiểm soát: ${$("#car_license_number").val()};`:'';
		let car_regist_number=$("#car_regist_number").val()?`Số đăng ký: ${$("#car_regist_number").val()};`:'';
		let car_issue_place=$("#car_issue_place").val()?`Nơi cấp: ${$("#car_issue_place").val()};`:'';
		let car_frame_number=$("#car_frame_number").val()?`Số khung: ${$("#car_frame_number").val()};`:'';
		let car_machine_number=$("#car_machine_number").val()?`Số máy : ${$("#car_machine_number").val()};`:'';
		
		let other_info=$("#other_info").val();
		
		let propertyInfo=`${car_license_number} ${car_regist_number} ${car_issue_place} ${car_issue_date} ${car_frame_number}  ${car_machine_number} ${other_info}` 
		$("#noidung").val(propertyInfo);
	}
	function importOtherProperty(){
				let property_info=$("#property_info").val();

				let other_info=$("#other_info").val();
				let propertyInfo=`${property_info} ${other_info}`
				$("#noidung").val(propertyInfo);


	}
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
		$('#modal-ele-ho-duong-su').focusout(function () {
     
            if ($('#modal-ele-ho-duong-su').val() !== '' && $('#modal-ele-ten-duong-su').val() !== '') {
                var first_name = $('#modal-ele-ho-duong-su').val() + ' ' + $('#modal-ele-ten-duong-su').val();
                $('#modal-ele-first_name').val(first_name);
            }
            $('#modal-ele-username').val(Math.floor(Math.random() * 999999) + 100000);
            $('#modal-ele-password').val(Math.floor(Math.random() * 999999) + 100000);
        });
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
                    id: option[0].id,
                    id_vanphong:'{{\App\Models\NhanVienModel::find(Sentinel::getUser()->id)->nv_vanphong}}',

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
                        $('#noidung').val(taisan)
                        $('#noidung').append('\n');
                    }
                    number++
                }
            });
        }
        function capNhatTextNoiDung () {
            getTemplateNoiDung();
            if ($('#noidung').val() != '') {
                getTemplateNoiDung();
            }
        }
        function getTemplateNoiDung () {
            let loaiHopDongId = $('#vanban option:selected').val();
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
                    id_vanphong:'{{\App\Models\NhanVienModel::find(Sentinel::getUser()->id)->nv_vanphong}}',
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
        var selectedLabel = 'chuyen-nhuong';
        var soLuongDuongSu = 2;
        var nhomDuongSu = '';
        var benA = [];
        var benB = [];
        var benC = [];

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
            $.ajax({
                type: "GET",
                url: "{{ route('addDuongSu') }}",
                data: {
                    id: option[0].id,
                    id_vanphong:'{{\App\Models\NhanVienModel::find(Sentinel::getUser()->id)->nv_vanphong}}',

                },
                dataType: "json",
                cache: true,
                success: function (data) {
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

        $('#update-duong-su').click(function () {
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
        $(function () {
            $("#thu_lao").keyup(function (e) {
                $(this).val(format($(this).val()));
            });
            $("#phi_cong_chung").keyup(function (e) {
                $(this).val(format($(this).val()));
            });
        });
        var format = function (num) {
            var str = num.toString().replace("", ""), parts = false, output = [], i = 1, formatted = null;
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
        $(document).ready(function () {
            $('#nganchan').change(function () {
                $('#description').append('<label id="nganchanboi">Nhập số công chứng cũ: </label>',
                    '<input class="form-control" placeholder="Nhập số công chứng..." id="cancel_description" name="description" type="text" >');
            });
            $('#thuong').change(function () {
                $('#nganchanboi').remove();
                $('#cancel_description').remove();
            });
            $('#giaitoa').change(function () {
                $('#nganchanboi').remove();
                $('#cancel_description').remove();
            });
            $('#canhbao').change(function () {
                $('#nganchanboi').remove();
                $('#cancel_description').remove();
            });
        });
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
                                $("#vanban").append(new Option(val.vb_nhan, val.vb_id));
                        });
                        $("#vanban").select2({
                            allowClear: true
                        });
                    }
                });
            });
        });
        function onSubmitHandler(event) {
            $('#animation').modal('show');
            $("#btn-save").attr('disabled','true');
            /* validate here */
        };
    </script>
@stop
