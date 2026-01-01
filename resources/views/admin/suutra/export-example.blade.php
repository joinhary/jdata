<table>
    <thead>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
        <tr>
            <th>
                Số DM Công Chứng
            </th>
            <th></th>
            <th colspan="2">
                Ngày Công Chứng
            </th>
            <th></th>
       {{--     <th>
                Ngày thụ lý
            </th>--}}
            <th></th>
            <th colspan="5">
                Họ tên, nơi cư trú người yêu cầu công chứng
            </th>
            <th></th>
            <th>
                Loại việc công chứng
            </th>
            <th></th>
            <th>
                Tóm tắt nội dung
            </th>
            <th></th>
{{--
            <th colspan="4">Họ tên người ký công chứng</th>
--}}
            <th></th>
            <th colspan="3">
                Ghi chú
            </th>
            <th>
                ID Nhân viên
            </th>
            <th>
                ID Văn Phòng
            </th>
            @if(\App\Models\RoleUsersModel::where('user_id',Sentinel::getUser()->id)->first()->role_id==18||\App\Models\RoleUsersModel::where('user_id',Sentinel::getUser()->id)->first()->role_id==10||Sentinel::check()->isPC())
            <th> Bị chặn(1) Giải tỏa  (0)</th>
                @endif
        </tr>
    </thead>
    <tbody>
    <tr>
        <th>
            123
        </th>
        <th></th>
        <th colspan="2">
            01/01/2019
        </th>
        <th></th>
    {{--    <th>
            01/01/2019
        </th>--}}
        <th></th>
        <th colspan="5" >
          Bên chuyển nhượng (Bên A): Trần Thanh Thiên Thảo
        </th>
        <th></th>
        <th>
            01. DTN-NHOM C.NHUONG-M.BAN
        </th>
        <th></th>
        <th>
            nội dung
        </th>
        <th></th>
{{--
        <th colspan="4">{{Sentinel::getUser()->first_name}}</th>
--}}
        <th></th>
        <th colspan="3">
            Ghi chú
        </th>
        <th>
            {{Sentinel::getUser()->id}}
        </th>
        <th>
            {{\App\Models\NhanVienModel::find(Sentinel::getUser()->id)->nv_vanphong}}
        </th>
        @if(\App\Models\RoleUsersModel::where('user_id',Sentinel::getUser()->id)->first()->role_id==18|\App\Models\RoleUsersModel::where('user_id',Sentinel::getUser()->id)->first()->role_id==10)
            <th>0</th>
            @endif
    </tr>

    </tbody>
</table>