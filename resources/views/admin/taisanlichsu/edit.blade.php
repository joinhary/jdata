@extends('admin.layouts.default')
@section('header_styles')
    <link href="{{ asset('assets/vendors/jasny-bootstrap/css/jasny-bootstrap.css') }}" rel="stylesheet"
          type="text/css"/>
    <link href="{{ asset('assets/vendors/iCheck/css/all.css') }}" rel="stylesheet" type="text/css"/>

    <!-- Add fancyBox main CSS files -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/fancybox/jquery.fancybox.css') }}"
          media="screen"/>
    <script src="{{asset('assets\js\jquery-3.3.1.min.js')}}"></script>
    <link rel="stylesheet" href="{{asset('assets\css\jquery.fancybox.css')}} " />
    <script src="{{asset('assets\js\jquery.fancybox.js')}}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/pages/gallery.js') }}"></script>
@endsection
@section('content')
    <section class="content-header" style="margin-bottom: 0px">
        <h1>Tài sản</h1>
        <ol class="breadcrumb">
            <li>
                <a href="{{ route('admin.dashboard') }}">
                    <i class="livicon" data-name="home" data-size="14" data-loop="true"></i>
                    Trang chủ
                </a>
            </li>
            <li>
                <a href="{{ route('indexTaiSan') }}">
                    Tài sản
                </a>
            </li>
            <li>
                <a href="{{ route('admin.taisan.lichsu.index',$id_ts) }}">
                    Lý lịch
                </a>
            </li>
            <li class="active">
                Sửa
            </li>
        </ol>
    </section>
    <br>
    <div class="row">
        <div class="col-lg-12">
            <div class="portlet box">
                <!--form control starts-->
                <div class="panel panel-primary" id="hidepanel6">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <i class="fa fa-fw fa-save"></i> Lý lịch - sửa
                        </h3>
                        <span class="pull-right">
                                    <i class="glyphicon glyphicon-chevron-up clickable"></i>
                            {{--<i class="glyphicon glyphicon-remove removepanel clickable"></i>--}}
                                </span>
                    </div>
                    <div class="panel-body">
                        <form method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            {{ method_field('PATCH') }}
                            <div class="row form-group">
                                <label class="col-md-2 p-0">Loại:</label>
                                <div class="col-md-10 p-0">
                                    <label class="radio-inline">
                                        &nbsp;<input type="radio" name="tinhtrang" class="custom-radio" value="1"
                                                     required {{ $lichsu->tinhtrang == 1?'checked':'' }}>&nbsp;Ngăn
                                        chặn</label>
                                    <label class="radio-inline">
                                        <input type="radio" name="tinhtrang" class="custom-radio"
                                               value="2" {{ $lichsu->tinhtrang == 2?'checked':'' }}>&nbsp;Giải
                                        chấp</label>
                                    <label class="radio-inline">
                                        <input type="radio" name="tinhtrang" class="custom-radio"
                                               value="3" {{ $lichsu->tinhtrang == 3?'checked':'' }}>&nbsp;Giải
                                        tỏa</label>
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="control-label col-md-2 p-0" for="sohoso">Số HS/CV:</label>
                                <div class="col-md-10 p-0">
                                    <input type="text" class="form-control" id="sohoso" name="sohoso"
                                           placeholder="Số HS/CV"  maxlength="20" value="{{ $lichsu->sohoso }}">
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="control-label col-md-2 p-0" for="ngayky">Ngày ký:</label>
                                <div class="col-md-10 p-0">

                                    <input type="date" class="form-control" id="ngayky" name="ngayky"

                                           placeholder="Ngày ký" required value="{{ $lichsu->ngayky }}" disabled>
                                </div>
                            </div>
                            <div>
                                <label class="control-label" for="">Danh mục:</label>
                                <div class="row"
                                     style="border: 1px solid #dbd8d5; border-radius: 5px; padding: 10px 0px !important;">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="so_cc">Số CC:</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" id="so_cc" name="so_cc"
                                                       placeholder="Số CC" maxlength="20"
                                                       value="{{ $lichsu->so_cc }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="so_vaoso">Số chính thức:</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" id="so_vaoso" name="so_vaoso"
                                                       placeholder="Số chính thức"  maxlength="20"
                                                       value="{{ $lichsu->so_vaoso }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-12 p-0">
                                <div class="col-md-8 p-0">
                                    <label>Nhãn:</label>
                                    <textarea class="form-control" name="mota"  rows="5"
                                              style="resize: vertical">{{ $lichsu->mota }}</textarea>
                                </div>
                                <div class="col-md-4" style="padding-right: 0 !important;">
                                    <label for="preview_image">Ảnh:</label>
                                    {{--{{ dd($lichsu->lylich_hinhanh) }}--}}
                                    <ul class="list-inline" id="preview_image"
                                        style="border-radius: 10px; border: 1px solid #dbd8d5">
                                        @if(count(json_decode($lichsu->lylich_hinhanh))>0)
                                            @foreach(json_decode($lichsu->lylich_hinhanh) as $item)
                                                <li style="padding: 5px 0 5px 12px !important;">
                                                    <a data-fancybox="images"   
                                                       href="{{ url('images/lylich/'.$item) }}">
                                                        <img style="border-radius:5px;"
                                                             class="img-responsive gallery-style"
                                                             src="{{ url('images/lylich/'.$item) }}" alt="Image"
                                                             height="50" width="50">
                                                    </a>
                                                </li>
                                            @endforeach
                                        @endif
                                    </ul>
                                    <label for="image"></label>
                                    <input type="file" name="image[]" id="image" multiple onchange="add_image(this)">
                                </div>
                                <input type="text" hidden id="array_name_image" name="array_name_image">
                            </div>
                            <div>
                                <label class="control-label" for="">Người thụ lý:</label>
                                <div class="row"
                                     style="border: 1px solid #dbd8d5; border-radius: 5px; padding: 10px 0px !important;">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="form-group">
                                                <label>CCV:</label>
                                                {!! \App\Helpers\Form::select('ccv_id',$ccv,$lichsu->ccv_id,['id' => 'ccv_id','class'=>'form-control select2','required']) !!}

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="form-group">
                                                <label>Nhân viên NV:</label>
                                              {!! \App\Helpers\Form::select('nhanviennv_id',$nvnv,$lichsu->nhanviennv_id,['id' => 'nhanviennv_id','class'=>'form-control select2']) !!}

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr width="50%">
                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-responsive btn-primary">Xác nhận</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('footer_scripts')
    <script src="{{ asset('assets/vendors/jasny-bootstrap/js/jasny-bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendors/iCheck/js/icheck.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form_examples.js') }}"></script>

    <!-- Add mousewheel plugin (this is optional) -->
    <script type="text/javascript"
            src="{{ asset('assets/vendors/fancybox/jquery.mousewheel.pack.js') }}"></script>
    <script type="text/javascript"
            src="{{ asset('assets/vendors/fancybox/jquery.fancybox.pack.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/pages/gallery.js') }}"></script>
    <script>
        function add_image(e) {
            // console.log(this.files);
            var FormData_image = new FormData();
            $('#preview_image').removeAttr('hidden');

            $.each(e.files, function (key, value) {
                FormData_image.append('image[]', value);
            });
            $.ajax({
                url: '{{ route('admin.taisan.lichsu.formdata_image') }}',
                type: 'POST',
                processData: false,
                contentType: false,
                data: FormData_image,
                success: function (result) {
                    $('#array_name_image').val(result.data);
                    console.log($('#array_name_image').val());
                    console.log(result.data);
                    $('#preview_image').empty();
                    $.each(result.data, function (k, v) {
                        $('#preview_image').append('<li style="padding: 5px 0px 5px 14px !important;">' +
                            '<a data-fancybox="images" href="{{ url('images/lylich') }}/' + v + '"><img style="border-radius:5px;" class="img-responsive gallery-style" src="{{ url('images/lylich') }}/' + v + '" alt="" height="50" width="50"></a></li>')
                    });
                }
            })
        }
    </script>
    <script>
        var i=1;
        function fancyboxRotation(){
            var n = 90 * ++i;
            $('.fancybox-content img').css('webkitTransform', 'rotate(-' + n + 'deg)');
            $('.fancybox-content img').css('mozTransform', 'rotate(-' + n + 'deg)');
        }
    </script>
@stop