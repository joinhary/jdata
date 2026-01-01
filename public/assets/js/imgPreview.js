function loadImg(img) {
    var arrayFile = img.files;
    $('#images').html('');
    $.each(arrayFile, function (k, v) {
        var reader = new FileReader();
        reader.readAsDataURL(v);
        reader.onload = function (e) {
            $('#images').append(
                '<div class="col-md-3 mb-2" id="' + k + '">' +
                '<a href="' + e.target.result + '"  class="fancybox-effects-a">' +
                '<img src="' + e.target.result + '" width="50" height="50">' +
                '</a>' +
                '</div>'
            );
        };
    })
}

function img(img) {
    var arrayFile = img.files;
    var divToFill = '#img-' + img.id;
    $(divToFill).html('');
    $.each(arrayFile, function (k, v) {
            var reader = new FileReader();
            reader.readAsDataURL(v);
            reader.onload = function (e) {
                $(divToFill).append(
                    '<div class="col-md-3 mb-2" id="' + k + '">' +
                    '<a href="' + e.target.result + '"  class="fancybox-effects-a">' +
                    '<img src="' + e.target.result + '" width="30" height="30">' +
                    '</a>' +
                    '</div>'
                );
            };
        }
    )
}

function loadImgKH1(img) {
    var arrayFile = img.files;
    var divToFill = '#img-' + img.id;
    $(divToFill).html('');
    $.each(arrayFile, function (k, v) {
            var reader = new FileReader();
            reader.readAsDataURL(v);
            reader.onload = function (e) {
                $(divToFill).append(
                    '<div class="col-md-2 mb-2" id="' + k + '">' +
                    '<a href="' + e.target.result + '"  class="fancybox-effects-a">' +
                    '<img src="' + e.target.result + '" width="50" height="50">' +
                    '</a>' +
                    '</div>'
                );
            };
        }
    )
}

function loadImgKH2(img) {
    var arrayFile = img.files;
    var divToFill2 = '#img2-' + img.id;
    console.log(img);
    $(divToFill2).html('');
    $.each(arrayFile, function (k, v) {
            var reader = new FileReader();
            reader.readAsDataURL(v);
            reader.onload = function (e) {
                $(divToFill2).append(
                    '<div class="col-md-2 mb-2" id="' + k + '">' +
                    '<a href="' + e.target.result + '"  class="fancybox-effects-a">' +
                    '<img src="' + e.target.result + '" width="50" height="50">' +
                    '</a>' +
                    '</div>'
                );
            };
        }
    )
}

function loadImgKH3(img) {
    var arrayFile = img.files;
    var divToFill3 = '#img3-' + img.id;
    console.log(img);
    $(divToFill3).html('');
    $.each(arrayFile, function (k, v) {
            var reader = new FileReader();
            reader.readAsDataURL(v);
            reader.onload = function (e) {
                $(divToFill3).append(
                    '<div class="col-md-2 mb-2" id="' + k + '">' +
                    '<a href="' + e.target.result + '"  class="fancybox-effects-a">' +
                    '<img src="' + e.target.result + '" width="50" height="50">' +
                    '</a>' +
                    '</div>'
                );
            };
        }
    )
}

function loadImgKH(img, action) {
    var arrayFile = img.files;
    var divToFill = '#img';
    var divToFill2 = '#img2';
    $(divToFill).html('');
    $(divToFill2).html('');
    $.each(arrayFile, function (k, v) {
            var reader = new FileReader();
            reader.readAsDataURL(v);
            reader.onload = function (e) {
                $(divToFill).append(
                    '<div class="col-md-2 mb-2" id="' + k + '">' +
                    '<a href="' + e.target.result + '"  class="fancybox-effects-a">' +
                    '<img src="' + e.target.result + '" width="50" height="50">' +
                    '</a>' +
                    '</div>'
                );
                $(divToFill2).append(
                    '<div class="col-md-2 mb-2" id="' + k + '">' +
                    '<a href="' + e.target.result + '"  class="fancybox-effects-a">' +
                    '<img src="' + e.target.result + '" width="50" height="50">' +
                    '</a>' +
                    '</div>'
                );
            };
        }
    )
}

/**Dương cập nhật */
