$(document).ready(function () {
    $('.select2-kieu').select2({
        theme: 'bootstrap'
    });
    var i = 1;
    $('#add-row').click(function () {
        i++;
        $('#form-ktm').append(
            '<div class="form-group row" id="group-row">' +
            '<input type="text" name="ktm_id[]" value="new" hidden>' +
            '<div class="col-md-6 col-xs-12">' +
            '<input type="text" id="ktm_traloi" class="form-control" name="ktm_traloi[]" autofocus required>' +
            '</div>' +
            '<div id="select-' + i + '" class="col-md-5 col-xs-12">' +
            '</div>' +
            '<div class="col-md-1 col-xs-12">' +
            '<a href="javascript:void(0)" class="button button-circle button-little button-danger mt-1 del-row" onclick="delRow(this)"><i class="fa fa-minus"></i></a>' +
            '</div>' +
            '</div>'
        );
        $('#select24').clone().attr({
            'id':'select' + i,
            'name':'k_id[]'
        }).appendTo('#select-' + i);
        var current_id = '#select' + i;
        $(current_id).select2({
            theme: "bootstrap"
        });
        $(current_id).addClass('form-control, w-100');
    });
});
var delItems = '';
function delRow(value) {
    var rowID = $(value).parent().parent().attr('id');
    delItems += rowID + ' ';
    $('#del-items').val(delItems);
    $(value).parent().parent().remove();
}