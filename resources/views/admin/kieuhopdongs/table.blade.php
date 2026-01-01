<div class="row">
    @if(request()->input('kieu_hd') == null)
        <a></a>
    @else
        <a style="margin-left: 10px;">Có <span style="color : red; font-weight: bold;">{{$count}}</span> kết quả được
            tìm thấy.</a>
    @endif
</div>
<div class="row bctk-scrollable-list" style="overflow-x: hidden;">
    <table class="table table-bordered table-hover" id="kieuhopdongs-table">
        <thead>
        <tr>
            <th style="text-align: left">Kiểu hợp đồng</th>
            <th colspan="3"><i class="fa fa-cog"></i></th>
        </tr>
        </thead>
        <tbody>
        @foreach($kieuhopdongs as $kieuhopdong)
            <tr>
                <td style="text-align: center">{!! $kieuhopdong->kieu_hd !!}</td>
                <td class="text-center">
                    <a title="Chi tiết kiểu hợp đồng" href="{{ route('admin.kieuhopdongs.show', $kieuhopdong->id) }}"
                       class="btn btn-primary">
                        Xem
                    </a>
                    <a title="Cập nhật kiểu hợp đồng"
                       href="{{ route('admin.kieuhopdongs.edit', $kieuhopdong->id) }}"
                       class="btn btn-success">
                        Sửa
                    </a>
                    <a title="Xóa kiểu hợp đồng" href="#" data-toggle="modal"
                       data-target="#confirm-delbranch-{{$kieuhopdong->id}}" class="btn btn-danger">Xóa</a>
                </td>
            </tr>
            <div class="modal fade" id="confirm-delbranch-{{$kieuhopdong->id}}" role="dialog"
                 aria-labelledby="modalLabeldanger">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-danger">
                            <h4 class="modal-title" id="modalLabeldanger">Chú ý!</h4>
                        </div>
                        <div class="modal-body">
                            <p>Bạn có thực sự muốn xóa kiểu hợp đồng "{{$kieuhopdong->kieu_hd}}"?</p>
                        </div>
                        <div class="modal-footer">
                            <form action="{{ route('admin.kieuhopdongs.delete',['id' => $kieuhopdong->id ]) }}" method="get">
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
        {{$kieuhopdongs->appends(request()->input())->links()}}
    </div>
    <div class="col-sm-6">
        <p class="pull-right" style="font-size: 16px;">Tổng số: <b><span style="color: red">{{count($tong)}}</span></b>
        </p>
    </div>
</div>
@section('footer_scripts')
    <div class="modal fade" id="delete_confirm" tabindex="-1" role="dialog" aria-labelledby="user_delete_confirm_title"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content"></div>
        </div>
    </div>
    <script>
        $(function () {
            $('body').on('hidden.bs.modal', '.modal', function () {
                $(this).removeData('bs.modal');
            });
        });
    </script>
@stop
