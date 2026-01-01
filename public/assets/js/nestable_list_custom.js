jQuery(document).ready(function () {
    UINestable.init();
});
var UINestable = function () {
    return {
        //main function to initiate the module
        init: function () {
            $('#sortable2,#sortable3').sortable({
                connectWith: '.connected'
            });
        }

    };

}();