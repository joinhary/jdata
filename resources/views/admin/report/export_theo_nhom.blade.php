<table id="noi-bo-table" class="table-bordered  ">
    <thead>
    <tr class="text-center " style="background-color:#eeeeee">
        <th >STT</th>
        <th >Nhóm hợp đồng</th>
        <th >Số lượng</th>
      

    </tr>
    </thead>
    <tbody>
    @foreach($count as $key=> $val)
        <tr>
            <td style="vertical-align: text-top!important;">
                {{$key}}
            </td>
            <td class="text-left"><strong>{{$nhom[$key]}}</strong></td>
            <td class="text-left"><strong>{{$val}}</strong></td>
        </tr>
  

    @endforeach
    <tr><td colspan="2"><strong>Tổng cộng</strong></td>

        <td><strong>{{$tong}}</strong></td></tr>
    </tbody>
</table>
