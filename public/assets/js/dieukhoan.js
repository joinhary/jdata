CKEDITOR.replace('ckeditor_full', {
    extraPlugins: 'colorbutton,font,justify',
    height: '450px'
});

$('#choose-tm').click(function () {
    var choosenTM = $('input:radio:checked');
    CKEDITOR.instances['ckeditor_full'].insertText("^" + choosenTM.val() + "^ ");
    $(choosenTM).prop('checked', false);
    $('#modal-addTM').modal('hide');
});

$('#btn-done').click(function () {
    var tm_list = '';
    var tm_newlist = '';
    $('#tm-list').val('');
    $('#tm-newlist').val('');
    $('#tm-dk > li').each(function () {
        if ($(this).hasClass('newTM')) {
            tm_newlist += $(this).attr('id') + ' ';
        }
        else {
            tm_list += $(this).attr('id') + ' ';
        }
    });
    $('#tm-list').val(tm_list);
    $('#tm-newlist').val(tm_newlist);
    $('#tm-dellist').val(tm_dellist);
});
var titleTooltip = '';
$('#dk_id').focusout(function () {
    var dk_id = $('#dk_id').val();
    if (dk_id === ''){
        var t = $("label[for='" + $('#dk_id').attr('id') + "']").text();
        titleTooltip = 'Vui lòng không để trống ' + t.slice(0, t.indexOf(":")) + '!';
        $('#dk_id').tooltip({
            title: titleTooltip,
            placement: 'bottom',
            trigger: 'manual'
        });
        if (!$(this).val()) {
            $(this).css('border', '1px solid red');
            $('#' + $(this).attr('id')).tooltip("show");
        } else {
            $(this).removeAttr('style', 'border');
            $('#' + $(this).attr('id')).tooltip("hide");
        }
    }
    else {
        $.ajax({
            url: urlValid,
            type: 'GET',
            data: 'dk_id=' + dk_id,
            success: function (err) {
                titleTooltip = 'Mã đã tồn tại!';
                $('#dk_id').tooltip({
                    title: titleTooltip,
                    placement: 'bottom',
                    trigger: 'manual'
                });
                if (err.status === 'error') {
                    $('#dk_id').css('border', '1px solid red');
                    $('#dk_id').tooltip('show');
                    $('#sub-dk').prop('disabled', true);
                } else {
                    if ($(this).val()) {
                        $('#sub-dk').prop('disabled', false);
                        $('#dk_id').tooltip('hide');
                        $('#dk_id').removeAttr('style', 'border');
                    }
                }
            }
        })
    }
});

$('#dk_nhan, #dk_phaply1, #dk_phaply2').focusout(function () {
    var t = $("label[for='" + $(this).attr('id') + "']").text();
    titleTooltip = 'Vui lòng không để trống ' + t.slice(0, t.indexOf(":")) + '!';
    $('#' + $(this).attr('id')).tooltip({
        title: titleTooltip,
        placement: 'bottom',
        trigger: 'manual'
    });
    if (!$(this).val()) {
        $(this).css('border', '1px solid red');
        $('#' + $(this).attr('id')).tooltip("show");
    } else {
        $(this).removeAttr('style', 'border');
        $('#' + $(this).attr('id')).tooltip("hide");
    }
});

$('#tk_noidung').on('keyup', function () {
    var loai_tk = $('#tieumuc_sel').val();
    var tk_noidung = $('#tk_noidung').val();
    var data = 'tk_noidung=' + tk_noidung + '&loaitk=' + loai_tk;
    $.ajax({
        url: urlTM,
        type: 'GET',
        data: data,
        success: function (tieumuc) {
            $('#tm-ds').html('');
            tieumuc.data.map(function (val) {
                $('#tm-ds').append(
                    '<li class="list-group-item row">' +
                    '<div class="col-md-1 border-right-custom">' +
                    '<input type="radio" id="tm__' + val.tm_id + '" name="tm_id" value="' + val.tm_id + '">' +
                    '</div>' +
                    '<div class="col-md-10 col-md-offset-1"><label for="tm__' + val.tm_id + '" class="none-bold mb-0">' + val.tm_id + ' - ' + val.tm_nhan + '</label>' +
                    '</div>' +
                    '</li>'
                )
            })
        }
    })
});


function checkInputs() {
    var isValid = true;
    $('input').filter('[required]').each(function() {
        if ($(this).val() === '') {
            $('#sub-dk').prop('disabled', true);
            isValid = false;
            return false;
        }
    });
    if(isValid) {$('#sub-dk').prop('disabled', false)}
    return isValid;
}

$('input[type=text]').filter('[required]').on('keyup',function() {
    checkInputs()
});

checkInputs();