<div id="print">
    <h2 style="text-align: center"><b>KẾT QUẢ TRA CỨU</b></h2>
    <ul style="font-size: 18px">
        <li>
            <b>Thời gian:</b> {{ \Carbon\Carbon::now()->format('H:i:s d/m/Y') }}
        </li>
     <li>
           <b>Điều kiện tìm kiếm :</b><br>
           <span style="margin-left: 70px">Các bên liên quan: {{ request()->input('coban') }} ,
               Thông tin tài sản: {{ request()->input('nangcao') }}</span>
      </li>
    </ul>
    <h3>
        <b>1. Danh sách thông tin giao dịch</b>
        (Tổng số: <span style="color: red">{{ \App\Models\SuuTraModel::count() }}</span> ,
        Có <span style="color: red">{{ $count }}</span> kết quả được tìm thấy.)
    </h3>
    <table style="font-size: 14px!important;border-collapse: collapse;border: 1px solid black;">
        <thead>
        <tr class="text-center">
{{--            <th style="width: 8%;border: 1px solid black;border-collapse: collapse;">Ngày nhập<br> hệ thống</th>--}}
            <th style="width: 5%;border: 1px solid black;border-collapse: collapse;">Ngày CC/<br>ngăn chặn</th>
            <th style="width: 30%;border: 1px solid black;border-collapse: collapse;">Các bên liên quan</th>
            <th style="width: 30%;border: 1px solid black;border-collapse: collapse;">Nội dung tóm tắt/<br> công văn</th>
            <th style="width: 8%;border: 1px solid black;border-collapse: collapse;">Số hợp đồng/<br> CV NC</th>
            <th style="width: 5%;border: 1px solid black;border-collapse: collapse;">Tên hợp đồng/<br> công văn</th>
            <th style="width: 5%;border: 1px solid black;border-collapse: collapse;">Công chứng viên/<br> Người nhập</th>
            <th style="width: 5%;border: 1px solid black;border-collapse: collapse;">Văn Phòng</th>
            <th style="width: 5%;border: 1px solid black;border-collapse: collapse;">Chặn/Giải tỏa</th>
			
        </tr>
        </thead>
        <tbody>
        @foreach($data as $val)
            <tr>
{{--                <td style="border: 1px solid black;border-collapse: collapse;">{{ \Carbon\Carbon::parse($val->ngay_nhap)->format('d/m/Y') }}</td>--}}
                <td style="border: 1px solid black;border-collapse: collapse;">{{ \Carbon\Carbon::parse($val->ngay_cc)->format('d/m/Y') }}</td>
                <td style="text-align: justify;border: 1px solid black;border-collapse: collapse;"><div>{!! str_replace(';',"</br>",$val->duong_su) !!}</div></td>
                <td style="text-align: justify;border: 1px solid black;border-collapse: collapse;"><div>{!! str_replace(';',"</br>",$val->texte) !!}</div></td>
                <td style="text-align: center;border: 1px solid black;border-collapse: collapse;">{{ $val->so_hd }}</td>
                <td style="text-align: center;border: 1px solid black;border-collapse: collapse;">{{ ($val->ten_hd) }}</td>
                <td style="text-align: center;border: 1px solid black;border-collapse: collapse;">{{ $val->ccv_master }}</td>
                <td style="text-align: center;border: 1px solid black;border-collapse: collapse;">{{ $val->vp_master }}</td>
                <td style="text-align: center;border: 1px solid black;border-collapse: collapse;">
				@if($val->contract_period != null)
                                                        <b>Thời hạn: </b> <span
                                                                style="color: red">{{ $val->contract_period }}</span>
                                                    @else
                                                    @endif
                                                    <br>
{{--                    <div class="row">--}}
{{--                        @if($val->ma_phan_biet == 'D')--}}
{{--                            <span>Jdata</span>--}}
{{--                        @else--}}
{{--                            <span>Dữ liệu khác</span>--}}
{{--                        @endif--}}
{{--                    </div>--}}
                    <div class="row">
                        @if($val->ngan_chan == 2)
                            Bị chặn
                        @elseif($val->ngan_chan == 1)
                            Cảnh báo
                        @endif

                    </div>
{{--                    <div class="row">--}}
{{--                        @if($val->picture)--}}
{{--                            <?php--}}
{{--                            $imgs = json_decode($val->picture);--}}
{{--                            ?>--}}
{{--                            @if(is_array($imgs))--}}
{{--                                @foreach($imgs as $img)--}}
{{--                                    @if(substr($img, -3)=='jpg' || substr($img, -3)=='png')--}}
{{--                                        <div class="col-md-2 mb-2 mt-1" style="padding-left: 0px;">--}}
{{--                                            <img src="{{url('images/suutra').'/'.$img}}"--}}
{{--                                                 width="20" height="20">--}}
{{--                                        </div>--}}
{{--                                        <br>--}}
{{--                                    @else--}}
{{--                                        <span>{{ $img }}</span>--}}
{{--                                    @endif--}}
{{--                                @endforeach--}}
{{--                            @endif--}}
{{--                        @else--}}
{{--                        @endif--}}
{{--                    </div>--}}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<script>
    window.print();
</script>

