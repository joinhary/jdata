@extends('admin.layouts.default')

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
                <h5 class="card-title">Chỉnh sửa template cho tài sản loại {{ $template->kieu_tai_san ? mb_strtoupper($template->kieu_tai_san->k_nhan) : '' }}</h5>
                <div class="card-body mx-3">
                    <div class="col-12 ml-3">
                        <div class="text-lg-left"><b>CÁC TIỂU MỤC</b></div>
                        @foreach($tieuMucList as $tieuMuc)
                            <button class="form-control btn btn-default w-auto" onclick="addToTemplate('{{ $tieuMuc }}')">
                                {{ $tieuMuc }}
                            </button>
                        @endforeach
                    </div>
                    <div class="col-12 mt-2">
                        <form class="form" method="POST" action="{{ route('admin.templates.tai-san.update', $template->id) }}">
                            {{ csrf_field() }}
                            <div class="col-12">
                                <label for="template" class="text-lg-left"><b>Template</b></label>
                                <textarea rows="7" id="template" class="form-control" name="template">{{ $template->template }}</textarea>
                            </div>
                            <div class="float-right mt-3">
                                <button type="submit" class="btn btn-success">Chỉnh sửa</button>
                            </div>
                        </form>
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
    <script>
        function insertAtCursor(myField, myValue) {
            //IE support
            let startPos = myField.selectionStart;
            let endPos = myField.selectionEnd;
            if (document.selection) {
                myField.focus();
                let sel = document.selection.createRange();
                sel.text = myValue;
            }
            // Microsoft Edge
            else if(window.navigator.userAgent.indexOf("Edge") > -1) {
                myField.value = myField.value.substring(0, startPos)+ myValue
                    + myField.value.substring(endPos, myField.value.length);

                let pos = startPos + myValue.length;
                myField.focus();
                myField.setSelectionRange(pos, pos);
            }
            //MOZILLA and others
            else if (myField.selectionStart || myField.selectionStart == '0') {
                myField.value = myField.value.substring(0, startPos)
                    + myValue
                    + myField.value.substring(endPos, myField.value.length);
            } else {
                myField.value += myValue;
            }

            setCaretPosition(myField, endPos + myValue.length);
        }

        function setCaretPosition(elem, caretPos) {
            let range;
            if (elem.createTextRange) {
                range = elem.createTextRange();
                range.move('character', caretPos);
                range.select();
            } else {
                elem.focus();
                if (elem.selectionStart !== undefined) {
                    elem.setSelectionRange(caretPos, caretPos);
                }
            }
        }

        function addToTemplate(text) {
            let templateObj = document.getElementById('template');
            insertAtCursor(templateObj, '<' + text + '>');
        }
    </script>
@stop

