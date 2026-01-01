$(document).ready(function () {
    $('a').click(function () {
        var id = $(this).attr('id');
        var child = "."+id;
        var signid = jQuery(this).children("i").attr('id');
        var sign = "."+signid;
        // console.log(signid, sign);
        $(child).toggle(function () {
            if (!$(this).hasClass('node-hidden')){
                $(sign).addClass('fa-plus');
                $(sign).removeClass('fa-minus');
                $(this).addClass('node-hidden');
                $(this).removeClass('node-show');
            } else{
                $(sign).removeClass('fa-plus');
                $(sign).addClass('fa-minus');
                $(this).removeClass('node-hidden');
                $(this).addClass('node-show');
            }
        })
    })
});