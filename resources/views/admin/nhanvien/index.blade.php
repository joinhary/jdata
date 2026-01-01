@extends('admin/layouts/default')
@section('title')
    Quản lý nhân viên @parent
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
        <form action="{{route('indexNhanVien')}}">
            <div class="row">
                <div class="col-md-5 search">
                    <input type="text" class="form-control" id="searchbox" name="nv_tk"
                           placeholder="Tìm kiếm theo mã hoặc tên nhân viên..." value="{{$search}}" autofocus>
                    <span class="fa fa-search fa-search-custom"></span>
                </div>
                <div class="col-md-2"><select name="vanphong_id" class="form-control">
    <option value="">-- Chọn văn phòng --</option>
    @foreach ($vanphong as $vp)
        <option value="{{ $vp->cn_id }}" 
            {{ old('vanphong_id', $vanphong_sl) == $vp->cn_id ? 'selected' : '' }}>
            {{ $vp->cn_ten }}
        </option>
    @endforeach
</select>
</div>
                <div class="col-md-5">
                    <button class="btn btn-success btn1" type="submit">
                        <i class="fa fa-search"></i>
                        Tìm kiếm
                    </button>
                    <a href="{{route('createNhanVien')}}" class="btn btn-primary btn2">
                        <i class="fa fa-plus"></i>
                        Thêm mới
                    </a>
                </div>
            </div>
        </form>
        <div class="row">
            @if(request()->input('nv_tk') == null)
                <a></a>
            @else
                <a>Có <span style="color : red; font-weight: bold;">{{$count}}</span> kết quả được tìm thấy.</a>
            @endif
        </div>
        <div class="row bctk-scrollable-list" style="overflow-x: hidden;">
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>Mã NV</th>
                    <th>Họ và tên</th>
                    <th>Chức vụ</th>
                    <th>Số điện thoại</th>
                    <th>Văn phòng</th>
                    <th>Id liên kết</th>
                    <th><i class="fa fa-cog"></i></th>
                </tr>
                </thead>
                <tbody>
                @foreach($nhanvien as $nv)
                    <tr>
                        <td align="center" class="column-align">{{$nv->nv_id}}</td>
                        <td class="column-align">{{$nv->nv_hoten}}</td>
                        <td class="column-align">{{$nv->nv_tenchucvu}}</td>
                        <td class="column-align">{{$nv->phone}}</td>
                        <td class="column-align">{{$nv->cn_ten}}</td>
                        <td class="column-align">{{$nv->id_lienket}}</td>
                        <td class="column-align qktd">
                            <a title="Chi tiết nhân viên" href="{{route('showNhanVien',['id' => $nv->nv_id])}}"
                               class="btn btn-primary">
                                Xem
                            </a>
                            @if($nv->nv_id == Sentinel::getUser()->id)
                            @else
                                <a title="Cập nhật thông tin nhân viên"
                                   href="{{route('editNhanVien',['id' => $nv->nv_id])}}"
                                   class="btn btn-success">
                                    Sửa
                                </a>
                                {{-- <a title="Xóa nhân viên" href="#" data-toggle="modal"
                                   data-target="#confirm-delstaff-{{$nv->nv_id}}"
                                   class="btn btn-danger">
                                    Xóa
                                </a> --}}
                            @endif
                        </td>
                    </tr>
                    <div class="modal fade" id="confirm-delstaff-{{$nv->nv_id}}" role="dialog"
                         aria-labelledby="modalLabeldanger">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-danger">
                                    <h4 class="modal-title" id="modalLabeldanger">Chú ý!</h4>
                                </div>
                                <div class="modal-body">
                                    <p>Bạn có thực sự muốn xóa nhân viên [{{$nv->nv_id.'] - ['.$nv->nv_hoten.']'}}?</p>
                                </div>
                                <div class="modal-footer">
                                    <form action="{{route('destroyNhanVien',['id' => $nv->nv_id])}}" method="get">
                                        <div class="form-inline">
                                            <button type="submit" class="btn btn-danger">Có, xóa!</button>
                                            <a href="#" data-dismiss="modal" class="btn btn-warning">Không</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="col-sm-12">
            <div class="col-sm-6">
                {{$nhanvien->appends(request()->input())->links()}}
            </div>
            <div class="col-sm-6">
                <p class="pull-right" style="font-size: 16px;">Tổng số:
                    <b><span style="color: red">{{count($tong)}}</span></b>
                </p>
            </div>
        </div>
    </section>
@stop
@section('footer_scripts')
@stop
