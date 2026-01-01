// Create template for zoom button
$.fancybox.defaults.btnTpl.zoom = '<button class="fancybox-button fancybox-zoom"><div class="zoom"><span class="zoom-inner"></span></div></button>';

// Choose what buttons to display by default
$.fancybox.defaults.buttons = [
    "zoom",
    "fullScreen",
    "download",
    "close",
    "rotate"
],


    $('[data-fancybox="images"]').fancybox({
        'overlayShow': true,

        onInit: function (instance) {

            // Make zoom icon clickable
            instance.$refs.toolbar.find('.fancybox-zoom').on('click', function () {
                if (instance.isScaledDown()) {
                    instance.scaleToActual();

                } else {
                    instance.scaleToFit();
                }

            });


        },


    });
