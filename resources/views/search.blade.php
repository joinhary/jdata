@php
    if (Sentinel::check()) {
        $role = Sentinel::check()
            ->user_roles()
            ->first()->slug;
        $vp = \App\Models\NhanVienModel::find(Sentinel::getUser()->id)->nv_vanphong;
        $nv = \App\Models\NhanVienModel::find(Sentinel::getUser()->id)->nv_ten;
        $id_user = \App\Models\NhanVienModel::find(Sentinel::getUser()->id)->nv_id;
    } else {
        return view('admin.login');
    }
    
@endphp
@extends('admin/layouts/default')
@section('title')
    Sưu tra @parent
@stop
@section('header_styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/pages/tables.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/datatables/css/dataTables.bootstrap.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/datatables/css/scroller.bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/pages/tables.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/Buttons/css/buttons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/pages/advbuttons.css') }}" />
    <style type="text/css">
        th,
        td {
            text-align: left;
            padding: 10px;
            font-size: 9pt;
        }

        tr,
        td {
            text-align: left;
            padding: 5px !important;
            font-size: 9pt;
        }

        table th {
            background-color: #0e5965c2;
            font-size: 9pt;

            color: rgb(255, 251, 251)
        }


        table td {
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .table td,
        .table th {
            vertical-align: middle !important;
        }

        .content-disp {
            display: none;
        }

        .th {
            font-family: "Times New Roman", Times, serif;
        }

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

        .highlight {
            background-color: yellow;
        }

        .mark {
            background-color: #ff0;
        }

        .nav-tabs>li.active>a,
        .nav-tabs>li.active>a:hover,
        .nav-tabs>li.active>a:focus {
            /* background-color: rgb(164, 139, 235); */
            color: rgb(74, 47, 226);
        }

        .nav-tabs>li>a:hover {
            /* background-color: rgb(164, 139, 235); */
            color: rgb(74, 47, 226);
        }

        .modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            /* Stay in place */
            padding-top: 100px;
            /* Location of the box */
            left: 0;
            top: 0px;
            width: 100%;
            /* Full width */
            height: 100%;
            /* Full height */
            overflow: auto;
            /* Enable scroll if needed */
            color: rgb(74, 47, 226);

        }

        .modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            /* Stay in place */
            padding-top: 100px;
            /* Location of the box */
            left: 0;
            top: 0px;
            width: 100%;
            /* Full width */
            height: 100%;
            /* Full height */
            overflow: auto;
            /* Enable scroll if needed */
            color: rgb(74, 47, 226);

        }

        mark {
    background: #ffe066;
    color: #000;
    font-weight: 600;
    padding: 0 2px;
    border-radius: 2px;
}
    </style>
@stop
@section('content')
    <section class="content" style="font-family: 'Tahoma',sans-serif;">
        <div class="container">
            <div class="row" style="display:flex; justify-content:flex-end">
                <button class="btn btn-primary" onclick="openModal()">
                    <i class="fa fa-question-circle-o" aria-hidden="true"></i> Hướng dẫn tra cứu
                </button>

            </div>
        </div>
        <div id="exTab2" class="container">
            <div class="row">
                <ul class="nav nav-tabs justify-content-center">
                    <li class="active" id="tab-ds">
                        <a id="tab-basic" href="#tab1" data-toggle="tab" onclick="ReloadData_Basic()">Tìm kiếm cơ bản<span
                                style="color: #e74040"> ({{ $count_bs }}) </span></a>
                    </li>
                    @if ($role != 'admin')
                        <li id="tab-prevent"><a href="#tab2" data-toggle="tab" onclick="ReloadData_Prevent()">Dữ liệu ngăn
                                chặn mới nhất <span style="color: #e74040"> ({{ $count_ngan_chan }}) </span></a>
                        </li>
                        <li id="tab-office"><a href="#tab3" data-toggle="tab" onclick="ReloadData_Office()">Dữ liệu đơn
                                vị<span style="color: #e74040"> ({{ $count_office }}) </span></a>
                        </li>
                    @else
                        <li id="tab-prevent"><a href="#tab2" data-toggle="tab" onclick="ReloadData_Prevent()">Dữ liệu ngăn
                                chặn mới nhất <span style="color: #e74040"> ({{ $count_ngan_chan }}) </span></a>
                        </li>
                    @endif
                </ul>
            </div>
            <!-- <div>
                                                                                    <marquee><p style="color:red"> **Đây là phiên bản thử nghiệm, chúng tôi sẽ thường xuyên cập nhật. Xem chi tiết hướng dẫn sử dụng <a href="https://www.w3schools.com" target="_blank">tại đây. </a>Xin cảm ơn!**</p></marquee>
                                                                                    </div>
                                                                                   -->
            <div class="tab-content ">
                @include('searchtab1')
                @include('searchtab2')
                @include('searchtab3')

                <div class="modal" tabindex="-1" role="dialog" name="myModal" id="myModal">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content" style="background-color:white">
                            <div class="modal-header" style="background-color:white">
                                <h2>&nbsp;Hướng dẫn cách tra cứu</h2>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="clear"
                                    onclick="cl"><span aria-hidden="true">&times;</span></button>
                            </div>
                            <div class="modal-body" style="background-color:white">\
                                <div class="container" style="color: black">
                                    <p> <b> 1. Tra cứu mở rộng </b></p>
                                    <p>Ví dụ: <i> Huỳnh Thị Thu 1952 *092152* Phong Điền </i></p>
                                    <p> <b> 2. Tra cứu chính xác </b></p>
                                    <p>Ví dụ: <i> "Huỳnh Thị Thu" 1952 092152 Phong Điền </i></p>
                                    <b> <i> - Không tìm thêm dấu '*' khi tìm chính xác! </i> </b> </br>
                                    <b> <i> - Chỉ dùng 1 dấu ngoặc kép cho đối tượng đứng đầu! </i> </b>
                                    <p> <b> 3. Nên tra cứu với có dấu và không dấu</b></p>
                                </div>
                            </div>
                        </div>
                    </div>




    </section>
@stop
@section('footer_scripts')
    <script type="text/javascript" src="{{ asset('assets/js/imgPreview.js') }}"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="{{ asset('assets/js/jquery.mark.js') }}"></script>
    <script type="text/javascript">
        $('#idbody').removeClass('nav-md');
        $('#idbody').addClass('nav-sm');
    </script>
    <script>
        function openModal() {
            $('#myModal').modal('show');
        }

        function hide_input() {
            //if input is empty
            if ($('#tat_ca').val() != '') {
                document.getElementById("duong_su").disabled = true;
                document.getElementById("tai_san").disabled = true;
                document.getElementById("so_hd1").disabled = true;
            } else {
                document.getElementById("duong_su").disabled = false;
                document.getElementById("tai_san").disabled = false;
                document.getElementById("so_hd1").disabled = false;
            }
        }

        function hide_input2() {
            //if input is empty
            if ($('#tat_ca2').val() != '') {
                document.getElementById("duong_su2").disabled = true;
                document.getElementById("tai_san2").disabled = true;
            } else {
                document.getElementById("duong_su2").disabled = false;
                document.getElementById("tai_san2").disabled = false;
            }
        }

        function hide_input3() {
            //if input is empty
            if ($('#tat_ca3').val() != '') {
                document.getElementById("duong_su3").disabled = true;
                document.getElementById("tai_san3").disabled = true;
                document.getElementById("so_hd3").disabled = true;
            } else {
                document.getElementById("duong_su3").disabled = false;
                document.getElementById("tai_san3").disabled = false;
                document.getElementById("so_hd3").disabled = false;
            }
        }

        function hide_inputAll1() {
            //if input is empty
            if ($('#duong_su').val() != '' || $('#tai_san').val() != '' ) {
                document.getElementById("tat_ca").disabled = true;
                document.getElementById("so_hd1").disabled = true;
                
            } else {
                document.getElementById("so_hd1").disabled = false;
                document.getElementById("tat_ca").disabled = false;
            }

            if($('#so_hd1').val() != ''){
                document.getElementById("duong_su").disabled = true;
                document.getElementById("tai_san").disabled = true;
                document.getElementById("tat_ca").disabled = true;
            }else{
                document.getElementById("duong_su").disabled = false;
                document.getElementById("tai_san").disabled = false;
                document.getElementById("tat_ca").disabled = false;
            }
        }

        function hide_inputAll2() {
            //if input is empty
            if ($('#duong_su2').val() != '' || $('#tai_san2').val() != '') {
                document.getElementById("tat_ca2").disabled = true;
            } else {
                document.getElementById("tat_ca2").disabled = false;
            }
        }

        function hide_inputAll3() {
            //if input is empty
            if ($('#duong_su3').val() != '' || $('#tai_san3').val() != '' || $('#so_hd3').val() != '') {
                document.getElementById("tat_ca3").disabled = true;
            } else {
                document.getElementById("tat_ca3").disabled = false;
            }
        }
        var activated = document.querySelector('.active').id;

        function clearValue() {
            $('#duong_su').val('');
            $('#tai_san').val('');
            $('#tat_ca').val('');
            $('#test').val('');
            $('#so_hd1').val('');
            $('#duong_su2').val('');
            $('#tai_san2').val('');
            $('#tat_ca2').val('');
            $('#test2').val('');
            $('#duong_su3').val('');
            $('#tai_san3').val('');
            $('#tat_ca3').val('');
            $('#test3').val('');
            $('#so_hd3').val('');
            document.getElementById("duong_su").disabled = false;
            document.getElementById("tai_san").disabled = false;
            document.getElementById("tat_ca").disabled = false;
            document.getElementById("so_hd1").disabled = false;
            document.getElementById("duong_su2").disabled = false;
            document.getElementById("tai_san2").disabled = false;
            document.getElementById("tat_ca2").disabled = false;
            document.getElementById("duong_su3").disabled = false;
            document.getElementById("tai_san3").disabled = false;
            document.getElementById("tat_ca3").disabled = false;
            document.getElementById("so_hd3").disabled = false;
            

        }

        function ReloadData_Prevent() {
            $("#formSreachprevent").submit();
            localStorage.setItem('tab', 'tab-prevent');
            localStorage.setItem('count_prevent', $count);


        }

        function ReloadData_Office() {
            $("#formSreachoffice").submit();
            localStorage.setItem('tab', 'tab-office');
            localStorage.setItem('count_office', $count);

        }

        function ReloadData_Basic() {
            $("#formSreachbasic").submit();
            localStorage.setItem('tab', 'tab-ds');
            localStorage.setItem('count_bs', $count);

        }

        function btnprint_click() {
            var duong_su = $('#duong_su').val();
            var tai_san = $('#tai_san').val();
            var tat_ca = $('#tat_ca').val();
            var so_hd = $('#so_hd1').val();
            var url = "{{ route('printSolr') }}";
            // window.location.href = url + "?duong_su=" + duong_su + "&tai_san=" + tai_san + "&tat_ca=" + tat_ca ;
            //open new tab
            window.open(url + "?duong_su=" + duong_su + "&tai_san=" + tai_san + "&tat_ca=" + tat_ca + "&so_hd=" + so_hd, '_blank');

        }

        function btnprint_click2() {
            var duong_su = $('#duong_su2').val();
            var tai_san = $('#tai_san2').val();
            var tat_ca = $('#tat_ca2').val();
            var test = $('#test').val();
            var url = "{{ route('printSolr_nganchan') }}";
            // window.location.href = url + "?duong_su=" + duong_su + "&tai_san=" + tai_san + "&tat_ca=" + tat_ca ;
            //open new tab
            window.open(url + "?duong_su=" + duong_su + "&tai_san=" + tai_san + "&tat_ca=" + tat_ca, '_blank');
        }

        function btnprint_click3() {
            var duong_su = $('#duong_su3').val();
            var tai_san = $('#tai_san3').val();
            var tat_ca = $('#tat_ca3').val();
            var so_hd = $('#so_hd3').val();
            var url = "{{ route('printSolr_vp') }}";
            // window.location.href = url + "?duong_su=" + duong_su + "&tai_san=" + tai_san + "&tat_ca=" + tat_ca ;
            //open new tab
            window.open(url + "?duong_su=" + duong_su + "&tai_san=" + tai_san + "&tat_ca=" + tat_ca + "&so_hd=" + so_hd,
                '_blank');

        }

        function showModal(id) {
            $('#' + 'img-' + id).modal('show');
        }

        function showModal2(id) {
            $('#' + 'imgg-' + id).modal('show');
        }

        function showinfo3(id) {
            $('#' + 'more-content-md3-' + id).modal('show');
        }

        function showinfo2(id) {
            $('#' + 'more-content-md2-' + id).modal('show');
        }

        function showinfo1(id) {
            $('#' + 'more-content-md1-' + id).modal('show');
        }

        function showtaisan1(id) {
            $('#' + 'more-content-md_taisan1-' + id).modal('show');
        }

        function showtaisan3(id) {
            $('#' + 'more-content-md_taisan3-' + id).modal('show');
        }

        function showtaisan2(id) {
            $('#' + 'more-content-md_taisan2-' + id).modal('show');
        }
        $(document).ready(function() {
            $(window).on('load', function() {

            });
            if ($('#tat_ca').val() != '') {
                document.getElementById("duong_su").disabled = true;
                document.getElementById("tai_san").disabled = true;
                document.getElementById("so_hd1").disabled = true;
            } else {
                document.getElementById("duong_su").disabled = false;
                document.getElementById("tai_san").disabled = false;
                document.getElementById("so_hd1").disabled = false;
            }

            //if input is empty
            if ($('#tat_ca2').val() != '') {
                document.getElementById("duong_su2").disabled = true;
                document.getElementById("tai_san2").disabled = true;
            } else {
                document.getElementById("duong_su2").disabled = false;
                document.getElementById("tai_san2").disabled = false;
            }


            //if input is empty
            if ($('#tat_ca3').val() != '') {
                document.getElementById("duong_su3").disabled = true;
                document.getElementById("tai_san3").disabled = true;
                document.getElementById("so_hd3").disabled = true;
            } else {
                document.getElementById("duong_su3").disabled = false;
                document.getElementById("tai_san3").disabled = false;
                document.getElementById("so_hd3").disabled = false;
            }
            //if input is empty
            if ($('#duong_su').val() != '' || $('#tai_san').val() != '') {
                document.getElementById("tat_ca").disabled = true;
                document.getElementById("so_hd1").disabled = true;
            } else {
                document.getElementById("tat_ca").disabled = false;
                document.getElementById("so_hd1").disabled = false;
            }
            //if input is empty
            if ($('#duong_su2').val() != '' || $('#tai_san2').val() != '') {
                document.getElementById("tat_ca2").disabled = true;
            } else {
                document.getElementById("tat_ca2").disabled = false;
            }

            if ($('#duong_su3').val() != '' || $('#tai_san3').val() != '' || $('#so_hd3').val() != '') {
                document.getElementById("tat_ca3").disabled = true;
            } else {
                document.getElementById("tat_ca3").disabled = false;
            }
            //lock duongsu va taisan input
            if($('#so_hd1').val() != '' || $('#so_hd3').val() != ''){
                document.getElementById("duong_su").disabled = true;
                document.getElementById("tai_san").disabled = true;
                document.getElementById("tat_ca").disabled = true;

            }else{
                document.getElementById("duong_su").disabled = false;
                document.getElementById("tai_san").disabled = false;
                document.getElementById("tat_ca").disabled = false;

            }
            var last_tab = "{{ $type }}";
            var vp = "{{ $vp }}";
            if (last_tab == null) {
                last_tab = 'tab-ds';
            } else {
                if (last_tab == "basic") {
                    var x = $("li.tab1");
                    //  $("li").not(x).removeClass("active");
                    $("#tab-ds").addClass("active");
                    $("#tab-prevent").removeClass("active show");
                    $("#tab-office").removeClass("active show");
                    $("#tab2").removeClass('active show');
                    $("#tab3").removeClass('active show');
                    $("#tab1").addClass('active show');
                }
                if (last_tab == "prevent") {
                    var x = $("li.tab2");
                    // $("li").not(x).removeClass("active");
                    $("#tab-prevent").addClass("active");
                    $("#tab-ds").removeClass("active show");
                    $("#tab-office").removeClass("active show");
                    $("#tab1").removeClass('active show');
                    $("#tab3").removeClass('active show');
                    $("#tab2").addClass('active show');
                }
                if (last_tab == "office") {
                    var x = $("li.tab3");
                    //$("li").not(x).removeClass("active");
                    $("#tab-office").addClass("active");
                    $("#tab-ds").removeClass("active show");
                    $("#tab-prevent").removeClass("active show");
                    $("#tab2").removeClass('active show');
                    $("#tab1").removeClass('active show');
                    $("#tab3").addClass('active show');

                }
            }

            
            
        });
    </script>
  
<script>
    window.searchDuongSu = {!! json_encode(request('duong_su') ?? '') !!};
    
</script>
<script>
    window.searchDuongSu2 = {!! json_encode(request('duong_su2') ?? '') !!};
</script>
<script>
window.searchDuongSu3 = {!! json_encode(request('duong_su3') ?? '') !!};
</script>
<script>
    window.searchTaisan = {!! json_encode(request('tai_san') ?? '') !!};
</script>
<script>
    window.searchTaisan2 = {!! json_encode(request('tai_san2') ?? '') !!};
</script>
<script>
    window.searchTaisan3 = {!! json_encode(request('tai_san3') ?? '') !!};
</script>
<script>
    window.searchSohd1 = {!! json_encode(request('so_hd1') ?? '') !!};
</script>

<script>
    window.searchTatca = {!! json_encode(request('tat_ca') ?? '') !!};
</script>
<script>
    window.searchTatca2 = {!! json_encode(request('tat_ca2') ?? '') !!};
</script>
<script>
    window.searchTatca3 = {!! json_encode(request('tat_ca3') ?? '') !!};
</script>
<script>
$(window).on('load', function () {

    if (typeof $.fn.mark !== 'function') {
        console.error('mark.js chưa load');
        return;
    }

    let raw = window.searchDuongSu || '';
    raw = raw.trim();
    if (!raw) return;

    /* =============================
     * 1️⃣ Lấy cụm trong ngoặc kép
     * ============================= */
    let phraseMatches = [...raw.matchAll(/"([^"]+)"/g)];
    let phrases = phraseMatches.map(m => m[1].trim());

    /* =============================
     * 2️⃣ Loại cụm khỏi chuỗi
     * ============================= */
    phraseMatches.forEach(m => {
        raw = raw.replace(m[0], '');
    });

    /* =============================
     * 3️⃣ Token còn lại
     * ============================= */
    let tokens = raw
        .replace(/['"]/g, '')
        .split(/\s+/)
        .filter(t => t.length > 0);

    console.log({ phrases, tokens });

    function highlight(selector) {
        $(selector).unmark({
            done: function () {

                /* =============================
                 * 🔶 HIGHLIGHT CỤM CHÍNH XÁC
                 * ❌ KHÔNG dùng \b
                 * ============================= */
                phrases.forEach(text => {
                    let escaped = text.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');

                    let regex = new RegExp(escaped, 'gi');

                    $(selector).markRegExp(regex, {
                        ignoreJoiners: true,
                        separateWordSearch: false
                    });
                });

                /* =============================
                 * 🔶 HIGHLIGHT TOKEN RIÊNG
                 * ============================= */
                tokens.forEach(token => {
                    let escaped = token.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');

                    let regex = new RegExp(`\\b${escaped}\\b`, 'gi');

                    $(selector).markRegExp(regex, {
                        ignoreJoiners: true
                    });
                });
            }
        });
    }

    
    highlight('.duong_su_1');
});
</script>
<!-- duong su 2 -->
 <script>
$(window).on('load', function () {

    if (typeof $.fn.mark !== 'function') {
        console.error('mark.js chưa load');
        return;
    }

    let keywordRaw = window.searchDuongSu2 || '';

    // 1️⃣ bỏ dấu "
    keywordRaw = keywordRaw.replace(/["']/g, '').trim();

    if (!keywordRaw) return;

    // 2️⃣ tách từ theo khoảng trắng
    let keywords = keywordRaw.split(/\s+/);

    console.log('Keywords:', keywords);

    // 3️⃣ clear mark cũ
    $('.duong_su_2').unmark({
        done: function () {
            // 4️⃣ mark từng từ
            $('.duong_su_2').mark(keywords, {
                separateWordSearch: false,
                accuracy: "partially",
                caseSensitive: false,
                ignoreJoiners: true
            });
        }
    });
});
</script>
<script>
$(window).on('load', function () {

    if (typeof $.fn.mark !== 'function') {
        console.error('mark.js chưa load');
        return;
    }

    let keywordRaw = window.searchTaisan || '';

    // 1️⃣ bỏ dấu "
    keywordRaw = keywordRaw.replace(/["']/g, '').trim();

    if (!keywordRaw) return;

    // 2️⃣ tách từ theo khoảng trắng
    let keywords = keywordRaw.split(/\s+/);

    console.log('Keywords:', keywords);

    // 3️⃣ clear mark cũ
    $('.tai_san_1').unmark({
        done: function () {
            // 4️⃣ mark từng từ
            $('.tai_san_1').mark(keywords, {
                separateWordSearch: false,
                accuracy: "partially",
                caseSensitive: false,
                ignoreJoiners: true
            });
        }
    });
});
</script>
<!-- search highlight tai san 2 -->
 <script>
$(window).on('load', function () {

    if (typeof $.fn.mark !== 'function') {
        console.error('mark.js chưa load');
        return;
    }

    let keywordRaw = window.searchTaisan2 || '';

    // 1️⃣ bỏ dấu "
    keywordRaw = keywordRaw.replace(/["']/g, '').trim();

    if (!keywordRaw) return;

    // 2️⃣ tách từ theo khoảng trắng
    let keywords = keywordRaw.split(/\s+/);

    console.log('Keywords:', keywords);

    // 3️⃣ clear mark cũ
    $('.tai_san_2').unmark({
        done: function () {
            // 4️⃣ mark từng từ
            $('.tai_san_2').mark(keywords, {
                separateWordSearch: false,
                accuracy: "partially",
                caseSensitive: false,
                ignoreJoiners: true
            });
        }
    });
});
</script>
<!-- highlight tat ca tr cuu chung -->
<script>
$(window).on('load', function () {

    if (typeof $.fn.mark !== 'function') {
        console.error('mark.js chưa load');
        return;
    }

    let raw = window.searchTatca || '';
    raw = raw.trim();
    if (!raw) return;

    /* =============================
     * 1️⃣ Lấy cụm trong ngoặc kép
     * ============================= */
    let phraseMatches = [...raw.matchAll(/"([^"]+)"/g)];
    let phrases = phraseMatches.map(m => m[1].trim());

    /* =============================
     * 2️⃣ Loại cụm khỏi chuỗi
     * ============================= */
    phraseMatches.forEach(m => {
        raw = raw.replace(m[0], '');
    });

    /* =============================
     * 3️⃣ Token còn lại
     * ============================= */
    let tokens = raw
        .replace(/['"]/g, '')
        .split(/\s+/)
        .filter(t => t.length > 0);

    console.log({ phrases, tokens });

    function highlight(selector) {
        $(selector).unmark({
            done: function () {

                /* =============================
                 * 🔶 HIGHLIGHT CỤM CHÍNH XÁC
                 * ❌ KHÔNG dùng \b
                 * ============================= */
                phrases.forEach(text => {
                    let escaped = text.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');

                    let regex = new RegExp(escaped, 'gi');

                    $(selector).markRegExp(regex, {
                        ignoreJoiners: true,
                        separateWordSearch: false
                    });
                });

                /* =============================
                 * 🔶 HIGHLIGHT TOKEN RIÊNG
                 * ============================= */
                tokens.forEach(token => {
                    let escaped = token.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');

                    let regex = new RegExp(`\\b${escaped}\\b`, 'gi');

                    $(selector).markRegExp(regex, {
                        ignoreJoiners: true
                    });
                });
            }
        });
    }

    highlight('.tai_san_1');
    highlight('.duong_su_1');
});
</script>
<!-- search highlight tat ca 2 -->
 <script>
$(window).on('load', function () {

    if (typeof $.fn.mark !== 'function') {
        console.error('mark.js chưa load');
        return;
    }

    let raw = window.searchTatca2 || '';
    raw = raw.trim();
    if (!raw) return;

    // 1️⃣ Lấy cụm trong ""
    let phraseMatches = [...raw.matchAll(/"([^"]+)"/g)];
    let phrases = phraseMatches.map(m => m[1].trim());

    // 2️⃣ Loại cụm đã lấy ra khỏi chuỗi
    phraseMatches.forEach(m => {
        raw = raw.replace(m[0], '');
    });

    // 3️⃣ Token còn lại (không tách chữ)
    let tokens = raw
        .replace(/['"]/g, '')
        .split(/\s+/)
        .filter(t => t.length > 0);

    console.log({
        phrases,
        tokens
    });

    function highlight(selector) {
        $(selector).unmark({
            done: function () {

                // 🔶 highlight cụm chính xác
                phrases.forEach(text => {
    let escaped = text.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    let regex = new RegExp(escaped, 'gi');

    $(selector).markRegExp(regex, {
        ignoreJoiners: true,
        separateWordSearch: false,
        acrossElements: true
    });
});


                // 🔶 highlight token độc lập
                tokens.forEach(token => {
                    let escaped = token.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                    let regex = new RegExp(`\\b${escaped}\\b`, 'gi');

                    $(selector).markRegExp(regex, {
                        ignoreJoiners: true
                    });
                });
            }
        });
    }

    highlight('.duong_su_2');
    highlight('.tai_san_2');

});
</script>
<!-- search duong su 3 -->
<script>
$(window).on('load', function () {

    if (typeof $.fn.mark !== 'function') {
        console.error('mark.js chưa load');
        return;
    }

    let keywordRaw = window.searchDuongSu3 || '';

    // 1️⃣ bỏ dấu "
    keywordRaw = keywordRaw.replace(/["']/g, '').trim();

    if (!keywordRaw) return;

    // 2️⃣ tách từ theo khoảng trắng
    let keywords = keywordRaw.split(/\s+/);

    console.log('Keywords:', keywords);

    // 3️⃣ clear mark cũ
    $('.duong_su_3').unmark({
        done: function () {
            // 4️⃣ mark từng từ
            $('.duong_su_3').mark(keywords, {
                separateWordSearch: false,
                accuracy: "partially",
                caseSensitive: false,
                ignoreJoiners: true
            });
        }
    });
});
</script>
<script>
$(window).on('load', function () {

    if (typeof $.fn.mark !== 'function') {
        console.error('mark.js chưa load');
        return;
    }

    let raw = window.searchTaisan3 || '';
    raw = raw.trim();
    if (!raw) return;

    let phraseMatches = [...raw.matchAll(/"([^"]+)"/g)];
    let phrases = phraseMatches.map(m => m[1].trim());

    phraseMatches.forEach(m => raw = raw.replace(m[0], ''));

    let tokens = raw
        .replace(/['"]/g, '')
        .split(/\s+/)
        .filter(t => t.length >= 3); // tránh văn, an

    function highlight(selector) {
        $(selector).unmark({
            done: function () {

                phrases.forEach(text => {
                    let regex = new RegExp(`\\b${escapeRegex(text)}\\b`, 'gi');
                    $(selector).markRegExp(regex, { ignoreJoiners: true });
                });

                tokens.forEach(token => {
                    let regex = new RegExp(`\\b${escapeRegex(token)}\\b`, 'gi');
                    $(selector).markRegExp(regex, { ignoreJoiners: true });
                });
            }
        });
    }

    highlight('.tai_san_3');

    function escapeRegex(str) {
        return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

});
</script>
<script>
$(window).on('load', function () {

    if (typeof $.fn.mark !== 'function') {
        console.error('mark.js chưa load');
        return;
    }

    // lấy giá trị từ input
    let keyword = $('#so_hd1').val() || '';
    keyword = keyword.trim();

    // chỉ xử lý dạng số/số
    if (!/^\d+\/\d+$/.test(keyword)) return;

    // escape regex an toàn
    let escaped = keyword.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');

    // regex match chính xác 6941/2025
    let regex = new RegExp(`\\b${escaped}\\b`, 'g');

    $('.so_hd1').unmark({
        done: function () {
            $('.so_hd1').markRegExp(regex);
        }
    });
});
</script>
<!-- search so cong chung trong van phong -->
<script>
$(window).on('load', function () {

    if (typeof $.fn.mark !== 'function') {
        console.error('mark.js chưa load');
        return;
    }

    // lấy giá trị từ input
    let keyword = $('#so_hd3').val() || '';
    keyword = keyword.trim();

    // chỉ xử lý dạng số/số
    if (!/^\d+\/\d+$/.test(keyword)) return;

    // escape regex an toàn
    let escaped = keyword.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');

    // regex match chính xác 6941/2025
    let regex = new RegExp(`\\b${escaped}\\b`, 'g');

    $('.so_hd3').unmark({
        done: function () {
            $('.so_hd3').markRegExp(regex);
        }
    });
});
</script>
<script>
$(window).on('load', function () {

    if (typeof $.fn.mark !== 'function') {
        console.error('mark.js chưa load');
        return;
    }

    let raw = window.searchTatca3 || '';
    raw = raw.trim();
    if (!raw) return;

    /* =============================
     * 1️⃣ Lấy cụm trong ngoặc kép
     * ============================= */
    let phraseMatches = [...raw.matchAll(/"([^"]+)"/g)];
    let phrases = phraseMatches.map(m => m[1].trim());

    /* =============================
     * 2️⃣ Loại cụm khỏi chuỗi
     * ============================= */
    phraseMatches.forEach(m => {
        raw = raw.replace(m[0], '');
    });

    /* =============================
     * 3️⃣ Token còn lại
     * ============================= */
    let tokens = raw
        .replace(/['"]/g, '')
        .split(/\s+/)
        .filter(t => t.length > 0);

    console.log({ phrases, tokens });

    function highlight(selector) {
        $(selector).unmark({
            done: function () {

                /* =============================
                 * 🔶 HIGHLIGHT CỤM CHÍNH XÁC
                 * ❌ KHÔNG dùng \b
                 * ============================= */
                phrases.forEach(text => {
                    let escaped = text.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');

                    let regex = new RegExp(escaped, 'gi');

                    $(selector).markRegExp(regex, {
                        ignoreJoiners: true,
                        separateWordSearch: false
                    });
                });

                /* =============================
                 * 🔶 HIGHLIGHT TOKEN RIÊNG
                 * ============================= */
                tokens.forEach(token => {
                    let escaped = token.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');

                    let regex = new RegExp(`\\b${escaped}\\b`, 'gi');

                    $(selector).markRegExp(regex, {
                        ignoreJoiners: true
                    });
                });
            }
        });
    }

    highlight('.tai_san_3');
    highlight('.duong_su_3');
});
</script>




@stop
