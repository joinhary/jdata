<div id="print" style="font-family: 'Tahoma',sans-serif;">
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
        (Tổng số: <span style="color: black">{{ \App\Models\SuuTraModel::count() }}</span> ,
        Có <span style="color: black">{{ $count }}</span> kết quả được tìm thấy.)
    </h3>
    <table style="font-size: 12px!important;border-collapse: collapse;border: 0.5px solid black;">
        <thead>
        <tr class="text-center">
{{--            <th style="width: 8%;border: 0.5px solid black;border-collapse: collapse;">Ngày nhập<br> hệ thống</th>--}}
            <th style="width: 5%;border: 0.5px solid black;border-collapse: collapse;">Ngày CC/<br>ngăn chặn</th>
            <th style="width: 45%;border: 0.5px solid black;border-collapse: collapse;">Các bên liên quan</th>
            <th style="width: 45%;border: 0.5px solid black;border-collapse: collapse;">Nội dung tóm tắt/<br> công văn</th>
            <th style="width: 2%;border: 0.5px solid black;border-collapse: collapse;">Số HD<br> CV NC</th>
            <th style="width: 8%;border: 0.5px solid black;border-collapse: collapse;">Tên HD/<br> CV</th>
            <th style="width: 5%;border: 0.5px solid black;border-collapse: collapse;">Công chứng viên/<br> Người nhập</th>
            <th style="width: 5%;border: 0.5px solid black;border-collapse: collapse;">Văn Phòng</th>

		</tr>
        </thead>
        <tbody>
        @foreach($data as $val)
            <tr>
{{--                <td style="border: 0.5px solid black;border-collapse: collapse;">{{ \Carbon\Carbon::parse($val->ngay_nhap)->format('d/m/Y') }}</td>--}}
                <td style="border: 0.5px solid black;border-collapse: collapse;">{{ \Carbon\Carbon::parse($val->ngay_cc)->format('d/m/Y') }}</td>
                <td style="text-align: justify;border: 0.5px solid black;border-collapse: collapse;"><div style="margin:0.5em">{!! str_replace(';',"</br>",$val->duong_su) !!}</div></td>
                <td style="text-align: justify;border: 0.5px solid black;border-collapse: collapse;"><div style="margin:0.5em">{!! str_replace(';',"</br>",$val->texte) !!}</div><br>
                                                <span style="color: black">{{ $val["cancel_description"] }}</span>
												<br>
												@if($val->contract_period != null)
                                                        <b>Thời hạn: </b> <span
                                                                style="color: black">{{ $val->contract_period }}</span>
                                                    @else
                                                    @endif
												@if($val->undisputed_date != null)
														<br>
                                                        <b>Giải chấp ngày: </b> <span
                                                                style="color: red">{{ \Carbon\Carbon::parse($val->undisputed_date)->format('d/m/Y') }}</span>
																 @if($val->undisputed_note)
																 <span
                                                                style="color: red">;(ghi chú: {{ $val->undisputed_note }})</span>
																@endif
                                                    @endif
												@if($val->deleted_note != null)
														<br>
                                                        <b>  <span
                                                                style="color: red">{{ $val->deleted_note }}</span>
																
																</b>
                                                    @endif
												</td>
                <td style="text-align: center;border: 0.5px solid black;border-collapse: collapse;">{{ $val->so_hd }}</td>
                <td style="text-align: center;border: 0.5px solid black;border-collapse: collapse;">{{ ($val->ten_hd) }}<br>
				
				@if($val->ngan_chan == 3)
                            Bị chặn
                        @elseif($val->ngan_chan == 1)
                            Cảnh báo
                        @endif
							 <br>
													@if($val->release_doc_number)
													Đã được giải toả theo CV: <b>{{$val->release_doc_number}}</b>
													@endif
													@if($val->release_doc_date)
														|Ngày giải toả : {{\Illuminate\Support\Carbon::parse($val->release_doc_date)->format('d/m/Y')}}
													@endif
				</td>
                <td style="text-align: center;border: 0.5px solid black;border-collapse: collapse;">{{ $val->ccv_master }}</td>
                <td style="text-align: center;border: 0.5px solid black;border-collapse: collapse;">{{ $val->vp_master }}</td>
                            

            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<script>
    window.print();
</script>

