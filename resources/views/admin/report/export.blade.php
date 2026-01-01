<tr>
    <th colspan="4">Sở Tư ….</th>
    <th></th>
    <th style="text-align: center;font-size: 14px">Cộng Hòa Xã Hội Chủ Nghĩa Việt Nam</th>
</tr>
<tr>
    <th colspan="4" style="text-align: left">
        @if(request()->input('theonvnv') == null)
            {{\App\Models\ChiNhanhModel::find(\App\Models\NhanVienModel::find(Sentinel::getUser()->id)->nv_vanphong)->cn_diachi}}
        @else
            {{\App\Models\ChiNhanhModel::find(request()->input('theonvnv'))->cn_diachi}}
        @endif

    </th>
    <th></th>
    <th style="text-align: center;font-size: 12px">Độc Lập - Tự Do - Hạnh Phúc</th>
</tr>
<tr>
    <th colspan="4"></th>
    <th colspan="3" style="font-size: 14px;text-align: center"><b>DANH SÁCH HỢP ĐỒNG CÔNG CHỨNG</b></th>
</tr>
<tr>
    <th colspan="4"></th>
    <th colspan="3" style="font-size: 13px;text-align: center">Tổ chức công chứng:
        <span style="font-weight:bold">
            @if(request()->input('theonvnv') == null)
                {{\App\Models\ChiNhanhModel::find(\App\Models\NhanVienModel::find(Sentinel::getUser()->id)->nv_vanphong)->cn_ten}}
            @else
                {{\App\Models\ChiNhanhModel::find(request()->input('theonvnv'))->cn_ten}}
            @endif
        </span>
    </th>
</tr>
<table class="table-bordered">
    <thead>
    <tr></tr>
    <tr></tr>
    <tr>
        <th style="font-weight:bold;text-align: center;vertical-align: center">STT</th>
        <th style="font-weight:bold;text-align: center;font-size: 13px;vertical-align: center">Số công chứng</th>
        <th style="font-weight:bold;text-align: center;font-size: 13px;vertical-align: center">Ngày công chứng</th>
        <th style="font-weight:bold;text-align: center;font-size: 13px;vertical-align: center" colspan="2">Tên hợp
            đồng
        </th>
        <th style="font-weight:bold;text-align: center;font-size: 13px;vertical-align: center" colspan="2">Các bên liên
            quan
        </th>
        <th style="font-weight:bold;text-align: center;font-size: 13px;vertical-align: center" colspan="2">Tóm tắt nội
            dung
        </th>
        {{--        <th style="font-weight:bold;text-align: center;font-size: 13px;vertical-align: center" colspan="2">Công chứng--}}
        {{--            viên--}}
        {{--        </th>--}}
        {{--        <th style="font-weight:bold;text-align: center;font-size: 13px;vertical-align: center" colspan="2">Văn phòng--}}
        {{--        </th>--}}
    </tr>
    </thead>
    <tbody>{{$i=1}}
    @foreach($data as $val)
        <tr>
            <td>{{$i++}}</td>
            <td style="text-align: justify;vertical-align: top">{{ $val->so_hd }}</td>
            <th style="text-align: justify;vertical-align: top">{{ $val->ngay_cc ? \Illuminate\Support\Carbon::createFromFormat('Y-m-d',$val->ngay_cc)->format('d/m/Y') : '' }}</th>
            <td colspan="2" style="text-align: justify;vertical-align: top">{{$val->ten_hd}}</td>
            <td colspan="2" style="text-align: justify;vertical-align: top">{{$val->duong_su}}</td>
            <td colspan="2" style="text-align: justify;vertical-align: top">{{$val->texte}}</td>
            {{--            <td colspan="2" style="text-align: justify;vertical-align: top">{{$val->ccv_master}}</td>--}}
            {{--            <td colspan="2" style="text-align: justify;vertical-align: top">{{ $val->vp_master }}</td>--}}
        </tr>
    @endforeach
    </tbody>
</table>
<tr>
    <th colspan="4">Tổng số: {{$i-1    }} hợp đồng</th>
    <th></th>
    <th colspan="2" style="text-align: center">
        Ngày {{ \Carbon\Carbon::now()->day}}
        tháng {{ \Carbon\Carbon::now()->month}}
        năm {{ \Carbon\Carbon::now()->year}}
    </th>
</tr>
<tr>
    <th colspan="4" style="text-align: left"></th>
    <th></th>
    <th colspan="2" style="text-align: center"><b>Người báo cáo</b></th>
</tr>
