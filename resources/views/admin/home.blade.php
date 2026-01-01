<form action="{{ route('nqk_Xem') }}" method="get">
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-10 nqkpading">
                <div class="row nqkbutton">
                    <div class="col-md-12 nqkpading">
                        <div class="col-md-2 nqkpading" style="width: 12%">
                            <input type="radio" name="radio" checked class="nqkradio" value="1"/>
                            <b>Tất Cả</b>
                        </div>
                        <div class="col-md-2 nqkpading" style="width: 12%">
                            <input type="radio" name="radio" class="nqkradio" value="2"/>
                            <b>Theo CCV</b>
                        </div>
                        <div class="col-md-3 nqkpading" style="width: 32%;padding-right: 10px !important;">
                            {!! \App\Helpers\Form::select('theoccv',$ccv,'',['class'=>'form-control']) !!}
                        </div>
                        <div class="col-md-2 nqkpading" style="width: 12%">
                            <input type="radio" name="radio" class="nqkradio" value="3"/>
                            <b>Theo NVNV</b>
                        </div>
                        <div class="col-md-3 nqkpading" style="width: 32%;padding-right: 10px !important;">
                            {!! \App\Helpers\Form::select('theonvnv',$nvnv,'',['class'=>'form-control']) !!}
                        </div>
                    </div>
                </div>
                <div class="row nqkbutton">
                    <div class="col-md-12 nqkpading">
                        <div class="col-md-1 nqkpading" style="width: 12%;"><b>Từ</b></div>
                        <div class="col-md-5 nqkpading" style="width: 38%;padding-right: 10px !important">
                            <input class="form-control" type="date" name="tungay">
                        </div>
                        <div class="col-md-1 nqkpading" style="width: 12%;"><b>Đến</b></div>
                        <div class="col-md-5 nqkpading" style="width: 38%;padding-right: 10px !important">
                            <input class="form-control" type="date" name="denngay">
                        </div>
                    </div>
                </div>
                <div class="row nqkbutton">
                    <div class="col-md-12 nqkpading" style="padding-right: 10px !important">
                        <select id="select" name="chonhoso" class="form-control">
                            <option value="1">Sổ công chứng</option>
                            <option value="2">Danh sách tất cả hợp đồng</option>
                            <option value="4">Hồ sơ theo đương sự</option>
                            <option value="8">Hồ sơ không có ngày ký</option>
                            <option value="9">Báo cáo theo từ khóa</option>
                            <option value="11">Báo cáo mượn trả của một hồ sơ</option>
                            <option value="12">Báo cáo so sánh giữa các văn phòng</option>
                            <option value="14">Hồ sơ đã được ký mà chưa đăng uchi</option>
                            <option value="15">Hồ sơ đã đăng uchi</option>
                            <option value="16">Hồ sơ đang xử lý</option>
                            <option value="17">Hồ sơ đã nhập kho</option>
                            <option value="18">Hồ sơ chưa nhập kho</option>
                        </select>
                    </div>
                </div>
                <div class="row nqkbutton">
                    <div class="col-md-12 nqkpading" style="padding-right: 10px !important">
                        <input type="text" name="nhaptukhoa" class="form-control" placeholder="Nhập từ khóa"/>
                    </div>
                </div>
            </div>
            <div class="col-md-2" style="padding: 0;background-color: #F6F7E4;">
                <div>
                    <a class="btn btn-default nqkbutton">
                        Đóng
                    </a>
                </div>
                <div>
                    <button type="submit" class="btn btn-default nqkbutton">
                        Xem
                    </button>
                </div>
                <div>
                    <a class="btn btn-default nqkbutton">
                        Export
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>