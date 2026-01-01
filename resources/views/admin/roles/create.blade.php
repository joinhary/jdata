@extends('admin.layouts.default')
@section('header_styles')
    <link rel="stylesheet" href="{{ asset('assets/vendors/Buttons/css/buttons.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/css/pages/advbuttons.css') }}"/>
@endsection
@section('content')
    <section class="content-header" style="margin-bottom: 0px">
        <h1>Phân quyền</h1>
        <ol class="breadcrumb">
            <li>
                <a href="{{ route('admin.dashboard') }}">

                <i class="livicon" data-name="home" data-size="14" data-loop="true"></i>
                    Trang chủ
                </a>
            </li>
            <li>
                <a href="{{ route('admin.roles.index') }}">Phân quyền</a>
            </li>
            <li class="active">Tạo phân quyền</li>
        </ol>
    </section>
    <br>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="livicon" data-name="bell" data-loop="true" data-color="#fff" data-hovercolor="#fff"
                           data-size="18"></i> Tạo phân quyền
                    </h3>
                    <span class="pull-right">
                        <i class="fa fa-fw fa-chevron-up clickable"></i>
                    </span>
                </div>
                <div class="panel-body border">
                    {!! \App\Helpers\Form::open(['role'=>'form']) !!}
                    <div class="form-group">
                        <label for="">
                            Ký hiệu
                            <p style="color: red;">Chú ý: viết không dấu, cách nhau bởi dấu "-"!!</p>
                        </label>
                        <input type="text" class="form-control" name="slug" placeholder="nhan-vien" required
                               value="{{ old('slug') }}">
                    </div>
                    <div class="form-group">
                        <label for="">Tên hiển thị
                        </label>
                        <input type="text" class="form-control" name="display_name" placeholder="Nhân Viên" required
                               value="{{ old('display_name') }}">
                    </div>
                    <div class="form-group" style="word-wrap: break-word;">
                        <span style="font-size: large;"> Phân quyền:  </span>
                        <input class="btn btn-default" id="checkAll" type="button" value="Check/Uncheck All">
                        <div class="checkbox" style="padding: 0 15px;">
                            @if(count($permission)>0)
                                @foreach($permission as $key => $item)
                                    <label for="" style="margin: 0 15px">
                                        <input type="checkbox" class="checkbox-inline first" name="permissions[]"
                                               value="{{$item->permissions}}"> {{$item->permissions}}
                                    </label>
                                    <br>
                                @endforeach
                            @else
                                <p>Not found permission</p>
                            @endif
                        </div>
                        <hr>
                        <div>
                            <input class="form-control btn btn-primary off" type="submit"
                                   value="Lưu lại">
                        </div>
                    </div>
                    {!! \App\Helpers\Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@stop
@section('footer_scripts')
    <script>
        $(document).ready(function () {
            $(".first").click(function () {
                $("#checkAll").attr("data-type", "uncheck");
            });
            $("#checkAll").attr("data-type", "check");
            $("#checkAll").click(function () {
                if ($("#checkAll").attr("data-type") === "check") {
                    $(".first").prop("checked", true);
                    $("#checkAll").attr("data-type", "uncheck");
                } else {
                    $(".first").prop("checked", false);
                    $("#checkAll").attr("data-type", "check");
                }
            });
        });
    </script>
@stop