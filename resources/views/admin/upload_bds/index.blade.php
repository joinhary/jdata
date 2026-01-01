@php
$role = Sentinel::check()
->user_roles()
->first()->slug;
$vp = \App\Models\NhanVienModel::find(Sentinel::getUser()->id)->nv_vanphong;
@endphp
@extends('admin/layouts/default')
@section('title')
Quản lý báo cáo bất động sản hằng tháng @parent
@stop
@section('header_styles')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/pages/tables.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/datatables/css/dataTables.bootstrap.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/datatables/css/scroller.bootstrap.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/pages/tables.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendors/Buttons/css/buttons.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/css/pages/advbuttons.css') }}" />
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
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
      @if ($role == 'admin' || $role == 'truong-van-phong')
      <div class="col-md-9">
         <form action="{{ route('indexBds') }} ">
            <div class="row">
               <div class="col-md-7 search">
                  <input type="text" class="form-control" id="name" name="name"
                     placeholder="Tìm kiếm theo tên.."
                     value="{{ isset($search['name']) ? $search['name'] : '' }}" autofocus>
                  <span class="fa fa-search fa-search-custom"></span>
               </div>
               <p></p>
               <div class="col-md-4">
                  <select class="form-control" name="month" id="month">
                     <option value="" style=" font-style: italic;">Chọn tháng</option>
                     <option value="01">Tháng 1</option>
                     <option value="02">Tháng 2</option>
                     <option value="03" >Tháng 3</option>
                     <option value="04" >Tháng 4</option>
                     <option value="05" >Tháng 5</option>
                     <option value="06" >Tháng 6</option>
                     <option value="07">Tháng 7</option>
                     <option value="08">Tháng 8</option>
                     <option value="09-2022">Tháng 9</option>
                     <option value="10">Tháng 10</option>
                     <option value="11" >Tháng 11</option>
                     <option value="12">Tháng 12</option>
                  </select>
               </div>
               <p></p>
               <div class="col-md-7">
                  <select class="form-control" name="vpcc" id="vpcc">
                     <option value="" style=" font-style: italic;">Chọn văn phòng</option>
                     @php
                     $chi_nhanh_all = App\Models\ChiNhanhModel::all();
                     foreach ($chi_nhanh_all as $chi_nhanh) {
                     $chi_nhanh_id = $chi_nhanh->cn_id;
                     $chi_nhanh_name = $chi_nhanh->cn_ten;
                     
                     echo '
                     <option value="'.$chi_nhanh_id.'">' . $chi_nhanh_name . '</option>
                     ';
                     
                     }
                     @endphp
                  </select>
               </div>
               <div class="col-md-5">
                  <button class="btn btn-success btn1" type="submit">
                  <i class="fa fa-search"></i>
                  Tìm kiếm
                  </button>
                  <button id="btnclear" type="button" class="btn btn-primary"
                     onclick="clearValue()">Làm rỗng</button>
               </div>
            </div>
         </form>
      </div>
      @endif
      <div class="col-md-3" style="font-size: 25px;">
         <div class="col-md-5">
            <form action="{{ route('indexBds') }}  class="form-inline" onsubmit="openModal()" id="myForm"">
            <button type="submit" class="btn btn-primary" style="width:100%">
            <i class="fa fa-upload"></i>
            Tải lên</button>
            </form>
         </div>
         @if ($role == 'admin')
         <div class="col-sm-6">
            <button id="btnsend" type="button" class="btn btn-primary"
           > <i class="fa fa-check-square-o"></i>  Nhận File</button>
         </div>
         <div class="col-md-10">
            <form action="{{ route('exportSum') }}  class="form-inline" onsubmit="openModal()"
               id="myForm2">
               <button type="submit" class="btn btn-success" style="margin-top:4px; width:100%">
               <i class="fa fa-file-excel-o"></i>
                 Tổng hợp số liệu</button>
            </form>
         </div>
       
         @endif
      </div>
   </div>
   <div class="row">
      @if (request()->input('name') == null)
      <a></a>
      @else
      <a>Có <span style="color : red; font-weight: bold;">{{ $count }}</span> kết quả được tìm thấy.</a>
      @endif
   </div>
   <div class="row bctk-scrollable-list" style="overflow-x: hidden;">
      <table class="table table-bordered table-hover">
         <thead>
            <tr>
               <th>STT</th>
               <th>Ngày tải lên</th>
               <th>Tên</th>
               <th>Tệp tin</th>
               <th style="width:100px" >Người tải</th>
               <th>Văn phòng</th>
               <th style="width:150px">Hành động</th>
               @if ($role == 'admin')
               <th style="width:150px">Nhận file  </br>  <button id="checkall" type="button" class="btn btn-primary btn-sm" style="height:25px;text-align:center"
                  onclick="checkAll()">Chọn hết</button> </th>
               @endif
            </tr>
         </thead>
         <tbody>
            @foreach ($bank as $nv)
            <tr>
               <td align="center" class="column-align" style="text-align: center " style="text-align: center ">
                  {{ $loop->iteration }}
               </td>
               <td class="column-align" style="text-align: center " style="text-align: center ">
                  {{ Carbon\Carbon::parse($nv->date)->format('m-Y') }}
               </td>
               <td class="column-align" style="text-align: center " style="text-align: center ">
                  {{ $nv->name }}
               </td>
               <td class="column-align" style="text-align: center"><a style="color:blue;"
                  href={{ url('storage/upload_bds/' . $nv->file) }}> {{ $nv->file }}
               </td>
               <td class="column-align" style="text-align: center ">
                  {{ \App\Models\User::where('id', $nv->user_id)->first()->first_name }}
               </br> 
                  <i style="color: #e74040">{{$nv->edit_description}}</>

               </td>
               <td class="column-align" style="text-align: center ">
                  {{ \App\Models\ChiNhanhModel::where('cn_id', $nv->vpcc_id)->first()->cn_ten }}
               </td>
               
               <td class="column-align qktd">
                  @if ($nv->id == Sentinel::getUser()->id)
                  @else
                  @if ($nv->accepted == 0)
                  @if ($role == 'admin' || $role == 'truong-van-phong')
                  <a title="Cập nhật thông tin ngân hàng"
                     href="{{route('editBDS',['id' => $nv->id])}}"
                     class="btn btn-success">
                  Sửa
                  </a>
                  <a title="Xóa" href="#" data-toggle="modal"
                     data-target="#confirm-delstaff-{{$nv->id}}"
                     class="btn btn-danger">
                  Xóa
                  </a>
                  @endif
                  @endif
                  @endif
               </td>
               @if ($role == 'admin')
               <td class="column-align qktd">
                  @if ($nv->accepted == 0)
                  <input type="checkbox" name="accepted" value="{{$nv->id}}" id="accepted" onclick="accepted({{ $nv->id }})">  
                  {{-- <input type="radio" name="{{ $nv->id }}"  class="radio-check"
                     onclick="accepted({{ $nv->id }})"> --}}
                     
                  </a>
                  @else
                  <p style="color:red"> Đã nhận </p>
                  @endif
               </td>
               @endif
              
            </tr>
            
            <div class="modal fade" id="confirm-delstaff-{{$nv->id}}" role="dialog"
               aria-labelledby="modalLabeldanger">
               <div class="modal-dialog" role="document">
                  <div class="modal-content">
                     <div class="modal-header bg-danger">
                        <h4 class="modal-title" id="modalLabeldanger">Chú ý!</h4>
                     </div>
                     <div class="modal-body">
                        <p>Bạn có thực sự muốn xóa [{{$nv->id.'] - ['.$nv->name.']'}}?</p>
                     </div>
                     <div class="modal-footer">
                        <form action="{{route('destroyBDS',['id' => $nv->id])}}" method="get">
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
         {{ $bank->appends(request()->input())->links() }}
      </div>
      <div class="col-sm-6">
         <p class="pull-right" style="font-size: 16px;">Tổng số:
            <b><span style="color: red">{{ $tong }}</span></b>
         </p>
      </div>
   </div>
   <div class="modal" tabindex="-1" role="dialog" id="myModal" name="modal1">
      <div class="modal-dialog" role="document">
         <div class="modal-content" style="background-color:white">
            <div class="modal-header" style="background-color:white">
               <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                  id="clear"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="background-color:white">
               <form action="{{ route('storeBds') }}" method="post" enctype="multipart/form-data" accept-charset="character_set">
                @csrf
                <div class="form-group" style="color: black">
                   <label for="">Tên báo cáo:</label>
                   <input type="text" class="form-control" name="name" placeholder="Nhập tên">
                   </br>
                   <label>Chọn tháng <span class="text-danger">*</span></label>
                   <div>
                      <input style="width: 50%" type="month" name="date"
                         value="{{ \Illuminate\Support\Carbon::now()->format('m-Y')}}"
                         class="form-control" />
                   </div>
                   </br>
                   <label for="exampleInputFile">Chọn tệp:</label>
                   </br>
                   <input type="file" name="file" id="exampleInputFile">
                </div>
                </br>
                <button type="submit" class="btn btn-primary"
                   style="margin-left: 350px; width: 100px" ">Tải lên</button>
                </form>
            </div>
          
         </div>
      </div>
   </div>
   <div class="modal" tabindex="-1" role="dialog" name="modal2" id="myModal2">
      <div class="modal-dialog" role="document" >
         <div class="modal-content" style="background-color:white">
            <div class="modal-header" style="background-color:white">
               <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="clear" onclick="cl"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="background-color:white">
               <form action="{{ route('exportSum') }}" method="post" enctype="multipart/form-data" accept-charset="character_set">
                @csrf
                <div class="form-group" style="color: black">
                  
                   <label>Chọn tháng <span class="text-danger">*</span></label>
                   <div>
                     <select class="form-control" name="date" id="date">
                        <option value="" style=" font-style: italic;">Chọn tháng</option>
                        <option value="01">Tháng 1</option>
                        <option value="02">Tháng 2</option>
                        <option value="03" >Tháng 3</option>
                        <option value="04" >Tháng 4</option>
                        <option value="05" >Tháng 5</option>
                        <option value="06" >Tháng 6</option>
                        <option value="07">Tháng 7</option>
                        <option value="08">Tháng 8</option>
                        <option value="09-2022">Tháng 9</option>
                        <option value="10">Tháng 10</option>
                        <option value="11" >Tháng 11</option>
                        <option value="12">Tháng 12</option>
                     </select>
                   </div>
                   </br>
                <button type="submit" class="btn btn-primary"
                   style="margin-left: 350px; width: 100px" ">Xuất</button>
                </form>
         </div>
         
      </div>
   </div>
   </div>
</section>
@stop
@section('footer_scripts')
<script type="text/javascript" src="{{ asset('assets/js/imgPreview.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/jquery.mark.js') }}"></script>
<script type="text/javascript" src="http://johannburkard.de/resources/Johann/jquery.highlight-5.js"></script>
<script>
   function clearValue() {
       $('#month').val('');
       $('#vpcc').val('');
       $('#name').val('');
   }
   //hightlight search
   $('#duong_su').on('keyup', function() {
       var search = $(this).val();
       $('#idbody').highlight(search);
   
   });
   $('#myForm').on('submit', function(e) {
       $('#myModal').modal('show');
       e.preventDefault();
   });
   
   $('#myForm2').on('submit', function(e) {
       $('#myModal2').modal('show');
       e.preventDefault();
   });
   //clear form data
  $('#MyModal2').on('hidden.bs.modal', function() {
    console.log('clear');
    $(this).find('form').trigger('reset');
});
   $('#clear').on('click', function() {
    console.log('clear');
    clearValue();
});
   //modal2
   $('#modal2').on('hidden.bs.modal', function() {
    console.log('clear');
    clearValue();
});
    //get all radio checked 
    var a = [];
     function accepted(id) {
         var arr = [];
         var id = id;
         var id = id.toString();
         //if radio checked do nothing
     
         if (a.indexOf(id) > -1) {
             a.splice(a.indexOf(id), 1);
         } else {
             a.push(id);
         }
        
         console.log(a);
     }
     //send $a array to acceptedBDS
       function sendValue() {
         if (a != null){
          $.ajax({
                url: '{{ route('acceptedBDS') }}',
                type: 'POST',
                data: {
                   '_token': '{{ csrf_token() }}',
                   'data': JSON.stringify(a)
                },
                success: function(data) {
                   if (data.success == '200') {
                      alert('Đã duyệt ' + a.length + ' bản ghi');
                      location.reload();
                   }else{
                      alert('Có lỗi xảy ra');
                   }
                }
          });
         }else{
            alert('Chưa chọn bản ghi nào');

         }
       }
         function checkAll() {
            $('input[type=checkbox]').each(function() {
                a.push($(this).val());
            });
            $('input[type=checkbox]').each(function() {
                $(this).prop('checked', true);
            });
            //find duplicate value in array and remove it
            var unique = a.filter(function(item, pos) {
                return a.indexOf(item) == pos;
            });
            a = unique;
            //confirm 
            if (confirm('Tổng số bản ghi đã chọn là: ' + a.length + ' files. Bạn có muốn duyệt ngay '  + a.length + ' bản ghi này không? Nếu không hãy chọn "Hủy" và tự chọn lại các file cần duyệt, sau đó chọn nút "Nhận File" ở góc phải!')) {
                sendValue();
            }
            console.log(a);

         }
         $('#btnsend').on('click', function() {
            if (a == []){
               alert('Chưa chọn bản ghi nào');
            }else{
               sendValue();
            }
         });


</script>
@stop
