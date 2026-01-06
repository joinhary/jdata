<!doctype html>
<html lang="en">

<head>
    <title>Solr Edit</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>

<body>
    <br>
    <div class="container">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab"
                    aria-controls="home" aria-selected="true">Edit</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab"
                    aria-controls="profile" aria-selected="false">Check</a>
            </li>

        </ul>
        <br>
        <div class="tab-content">
            <div class="tab-pane active" id="home">
                
                <form method="POST"
      action="{{ route('suutra.solr.update', $suutra->st_id) }}"
      id="form6_{{ $suutra->st_id }}">
    @csrf
    @method('PUT')

                <!-- 2 column grid layout with text inputs for the first and last names -->
                <div class="row mb-4">
                    <div class="col">

                        <div class="form-outline">
                          <input type="text" id="id" class="form-control" name="id"
                                value="{{ $suutra->st_id }}"  />
                            <input type="text" id="form6Example1" class="form-control" name="ma_dong_bo"
                                value="{{ $suutra->ma_dong_bo }}" readonly />
                            <label class="form-label" for="form6Example1">ma_dong_bo</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-outline">
                            <input type="text" id="form6Example2" class="form-control" value="{{ $suutra->loai }}"
                                name="loai" />
                            <label class="form-label" for="form6Example2">loai</label>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col">
                        <div class="form-outline">
                            <input type="text" id="form6Example1" class="form-control" name="ngay_nhap"
                                value="{{ $suutra->ngay_nhap }}" />
                            <label class="form-label" for="form6Example1">ngay_nhap</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-outline">
                            <input type="text" id="form6Example2" class="form-control" name="ngay_cc"
                                value="{{ $suutra->ngay_cc }}" />
                            <label class="form-label" for="form6Example2">ngay_cc</label>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col">
                        <div class="form-outline">
                            <input type="text" id="form6Example1" class="form-control" name="so_hd"
                                value="{{ $suutra->so_hd }}" />
                            <label class="form-label" for="form6Example1">so_hd</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-outline">
                            <input type="text" id="form6Example2" class="form-control" name="ten_hd"
                                value="{{ $suutra->ten_hd }}" />
                            <label class="form-label" for="form6Example2">ten_hd</label>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col">
                        <div class="form-outline">
                            <input type="text" id="form6Example1" class="form-control" name="ccv"
                                value="{{ $suutra->ccv }}" />
                            <label class="form-label" for="form6Example1">ccv</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-outline">
                            <input type="text" id="form6Example2" class="form-control" name="vp"
                                value="{{ $suutra->vp }}" />
                            <label class="form-label" for="form6Example2">vp</label>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col">
                        <div class="form-outline">
                            <input type="text" id="form6Example1" class="form-control" name="ccv_master"
                                value="{{ $suutra->ccv_master }}" />
                            <label class="form-label" for="form6Example1">ccv_master</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-outline">
                            <input type="text" id="form6Example2" class="form-control" name="vp_master"
                                value="{{ $suutra->vp_master }}" />
                            <label class="form-label" for="form6Example2">vp_master</label>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col">
                        <div class="form-outline">
                            <input type="text" id="form6Example1" class="form-control" name="sync_code"
                                value="{{ $suutra->sync_code }}" />
                            <label class="form-label" for="form6Example1">sync_code</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-outline">
                            <input type="text" id="form6Example2" class="form-control" name="created_at"
                                value="{{ $suutra->created_at }}" />
                            <label class="form-label" for="form6Example2">created_at</label>
                        </div>
                    </div>
                </div>
                <!-- Message input -->
                <div class="form-outline mb-4">

                    <input class="form-control" id="form6Example7" rows="4" name="cancel_description"
                        value="{{ $suutra->cancel_description }}"></textarea>
                    <label class="form-label" for="form6Example7">cancel_description</label>
                </div>
                <div class="form-outline mb-4">

                    <input class="form-control" id="form6Example7" rows="4" name="deleted_note"
                        value="{{ $suutra->deleted_note }}"></textarea>
                    <label class="form-label" for="form6Example7">deleted_note</label>
                </div>
                <div class="form-outline mb-4">

                    <input class="form-control" id="form6Example7" rows="4" name="is_update"
                        value="{{ $suutra->is_update }}"></textarea>
                    <label class="form-label" for="form6Example7">is_update</label>
                </div>
                <div class="form-outline mb-4">

                    <input class="form-control" id="form6Example7" rows="4" name="note"
                        value="{{ $suutra->note }}"></textarea>
                    <label class="form-label" for="form6Example7">note</label>
                </div>
                <div class="form-outline mb-4">

                    <input class="form-control" id="form6Example7" rows="4" name="duong_su"
                        value="{{ $suutra->duong_su }}"></textarea>
                    <label class="form-label" for="form6Example7">Đương sự</label>
                </div>
                <!-- Submit button -->
                <button type="button" data-toggle="modal" data-target="#modelId"
                    class="btn btn-primary btn-block mb-4">Updates</button>
              
                    <a type="button"  class="btn btn-primary btn-block mb-4" style="color: red" href="{{ route('deleteSolr', ['id' => $suutra->st_id]) }}">
                        <i class="fa fa-trash"></i>
                        Delete
              
                    </a>
                </form>
            </div>
            <div class="tab-pane" id="profile">
                <label class="form-label" for="form6Example1">Chi nhánh :</label>
                <select class="form-select" aria-label="Chi nhánh">
                    @foreach ($chinhanh as $item)
                        <option value="{{ $item->id }}">
                            {{ $item->cn_ten }}--{{ $item->cn_id }}--{{ $item->code_cn }}</option>
                    @endforeach
                </select>

                <hr>
                <label class="form-label" for="form6Example1">Nhân viên :</label>
                <select class="form-select" aria-label="Chi nhánh">
                    @foreach ($nhanvien as $item)
                        <option value="{{ $item->id }}">
                            {{ $item->first_name }}--{{ $item->id }}</option>
                    @endforeach
                </select>
            </div>

        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <input type="password" class="form-control" name="confirm_code" id="confirm_code" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">X</button>
                    <button type="button" class="btn btn-primary" onclick="save({{ $suutra->st_id }})">Update</button>
                </div>
            </div>
        </div>
    </div>

    <!-- <script>
        $('#exampleModal').on('show.bs.modal', event => {
            var button = $(event.relatedTarget);
            var modal = $(this); -->
            <!-- // Use above variables to manipulate the DOM -->
        <!-- }); -->
    <!-- </script> -->
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>
    {{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> --}}
    <link rel="stylesheet" href="{{ asset('assets/vendors/select2/css/select2.min.css') }}">
    {{-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}
    <script src="{{ asset('assets/vendors/select2/js/select2.js') }}"></script>

    <script src="
                            https://cdn.jsdelivr.net/npm/js-md5@0.7.3/build/md5.min.js
                            "></script>
    <script>
        $(document).ready(function() {
            $('.form-select').select2();
            $('#modelId').on('hidden.bs.modal', function() {
                document.getElementById('confirm_code').value = '';
            });
            //prevent right click on page
            $(document).bind("contextmenu", function(e) {
                e.preventDefault();
            });
            //prevent F12
            $(document).keydown(function(event) {
                if (event.keyCode == 123) { // Prevent F12
                    return false;
                } else if (event.ctrlKey && event.shiftKey && event.keyCode ==
                    73) { // Prevent Ctrl+Shift+I        
                    return false;
                }
            });
        });

        function save(id) {
    var confirm_code = md5(document.getElementById('confirm_code').value);
    var code = md5('apg135792022');

    if (confirm_code === code) {
        var form = document.getElementById('form6_' + id);
        if (!form) {
            alert('Không tìm thấy form');
            return;
        }
        form.submit();
    } else {
        alert('Mã xác nhận không đúng');
    }
}


    </script>
</body>

</html>
