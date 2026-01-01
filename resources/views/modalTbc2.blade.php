<div class="modal" tabindex="-1" role="dialog" name="myModalTbc2" id="myModalTbc2">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="background-color:white;width:1000px; right:200px; top:-40px">
            <div class="modal-header" style="background-color:white;text-align:center">
                <h5 class="modal-title">
                    <i class="fa fa-warning" aria-hidden="true"></i>
                    Có {{ $Tbc['total'] ?? '' }} thông báo được tìm thấy
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="clear"
                    onclick="cl"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="background-color:white">
                <div style="overflow-y: scroll; height:400px;">

                  @if (!empty($tbc['data']))
    @foreach ($tbc['data'] as $item)
        <div class="panel panel-default">
            <div class="panel-heading" style="background-color:#1a67a3;color:white">
                <a href="{{ route('showTBC', $item['id']) }}" style="color:white" target="_blank">
                    <b>{{ $loop->iteration }}. {{ $item['tieu_de'][0] }}</b>
                </a>
            </div>

            <div class="panel-body" style="color:black">
                <div class="tbc_modal search-result">
                    <p>
                        Tóm tắt nội dung:
                        {!! strlen($item['noi_dung'][0]) > 1000 
                            ? substr($item['noi_dung'][0], 0, 500) . '...' 
                            : $item['noi_dung'][0] !!}
                    </p>

                    @if (!empty($item['file'][0]))
                        @foreach (json_decode($item['file'][0], true) as $img)
                            <a href="{{ url('storage/upload_thongbao/' . $img) }}" target="_blank" style="color:#1a67a3">
                                <i class="fa fa-paperclip"></i> {{ $img }}
                            </a>
                            <br>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    @endforeach
@endif


                </div>

            </div>
        </div>
    </div>
</div>
