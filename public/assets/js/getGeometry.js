$('#tinh').on('change', function () {
    var provinceid = $(this).val();
    $.ajax({
        url: geometryURL,
        method: 'GET',
        data: 'provinceid=' + provinceid,
        success: function (res) {
            $('#quan').html('');
            $('#phuong').html('');
            $('#ap').html('');
            $('#quan').append('<option value="">---Chọn Quận/Huyện---</option>');
            $('#phuong').append('<option value="">---Chọn Xã/Phường---</option>');
            $('#ap').append('<option value="">---Chọn Ấp/Thôn/Khu vực---</option>');
            res.data.map(function (val) {
                $('#quan').append('<option value="' + val.districtid + '">' + val.name + '</option>');
            });
        }
    })
});
$('#quan').on('change', function () {
    var districtid = $(this).val();
    $.ajax({
        url: geometryURL,
        method: 'GET',
        data: 'districtid=' + districtid,
        success: function (res) {
            $('#phuong').html('');
            $('#ap').html('');
            $('#phuong').append('<option value="">---Chọn Xã/Phường---</option>');
            $('#ap').append('<option value="">---Chọn Ấp/Thôn/Khu vực---</option>');
            res.data.map(function (val) {
                $('#phuong').append('<option value="' + val.wardid + '">' + val.name + '</option>');
            });
        }
    })
});
$('#phuong').on('change', function () {
    var wardid = $(this).val();
    $.ajax({
        url: geometryURL,
        method: 'GET',
        data: 'wardid=' + wardid,
        success: function (res) {
            $('#ap').html('');
            $('#ap').append('<option value="">---Chọn Ấp/Thôn/Khu vực---</option>');
            res.data.map(function (val) {
                $('#ap').append('<option value="' + val.villageid + '">' + val.name + '</option>');
            });
        }
    })
});