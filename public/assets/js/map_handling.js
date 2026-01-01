var map;

function initMap(lat = 0, lng = 0) {
    if ($('#lat').val() === '' || $('#lng').val() === '') {
        lat = 10.0363024;
        lng = 105.7771182;
    }
    else {
        lat = parseFloat($('#lat').val());
        lng = parseFloat($('#lng').val());
    }
    // console.log(lat+','+lng+' - '+parseFloat(lat).toFixed(15)+','+parseFloat(lng).toFixed(15));
    var my_position = {lat: lat, lng: lng};
    map = new google.maps.Map(document.getElementById('map'), {
        center: my_position,
        zoom: 17,
        // mapTypeId: google.maps.MapTypeId.HYBRID
    });

    var marker = new google.maps.Marker({
        position: my_position,
        map: map,
        draggable: true,
        // icon: 'https://cdn2.iconfinder.com/data/icons/mini-icon-set-map-location/91/Location_22-64.png'
    });
    google.maps.event.addListener(marker, 'dragend', function (evt) {
        $('#lat').val(evt.latLng.lat().toFixed(15));
        $('#lng').val(evt.latLng.lng().toFixed(15));
        map.setCenter(marker.position);
        marker.setMap(map);
    });
}

$('#cn_diachi, #cn_tinh, #cn_quan, #cn_phuong').change(function () {
    $.ajax({
        url: 'https://maps.googleapis.com/maps/api/geocode/json?address=' + $('#cn_diachi').val() + ',' + $('#cn_phuong option:selected').text() + ',' + $('#cn_quan option:selected').text() + ',' + $('#cn_tinh option:selected').text(),
        success: function (data) {
            var lat = data.results[0].geometry.location.lat;
            var lng = data.results[0].geometry.location.lng;
            $('#lat').val(lat);
            $('#lng').val(lng);
            initMap(lat, lng);
        }
    })
});