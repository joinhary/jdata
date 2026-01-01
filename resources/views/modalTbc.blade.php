<div class="modal" tabindex="-1" role="dialog" name="myModalTbc" id="myModalTbc">
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

                    @if (isset($Tbc) && $Tbc !== [])
                        @foreach ($Tbc['data'] as $tbc)
                            <div class="panel panel-default">
                                <div class="panel-heading"
                                    style="
                                background-color: #1a67a3;
                                color:white">
                                    <a href="{{ route('showTBC', $tbc['id']) }}" style="color:white" target="blank">
                                        <b> {{ $loop->iteration }}.
                                            {{ $tbc['tieu_de'][0] }} </b></a>
                                </div>
                                <div class="panel-body" style="color:black">
                                    <div class="tbc_modal search-result">
                                        @php
    $noiDung = data_get($tbc, 'noi_dung.0', '');
@endphp

<p>
Tóm tắt nội dung:
{!! strlen($noiDung) > 1000 ? substr($noiDung, 0, 500) . '...' : $noiDung !!}
</p>

                                        @if (!empty($tbc['file'][0]))
                                            @foreach (json_decode($tbc['file'][0], true) as $key => $img)
                                                <a href="{{ url('storage/upload_thongbao/' . $img) }}"
                                                    style="color:#1a67a3" target="blank"><i class="fa fa-paperclip"
                                                        aria-hidden="true"></i>
                                                    {{ $img }}</a>
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
