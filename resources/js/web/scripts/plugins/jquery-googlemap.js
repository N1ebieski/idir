jQuery(document).ready(function () {
    let $map = $('#map');

    if ($map.length) {
        $map.data = $map.data();

        if (typeof $map.data.coordsMarker !== 'undefined' && $map.data.coordsMarker.length) {
            if (!$map.html().length) {
                $map.googleMap({
                    zoom: parseInt($map.data.zoom),
                    coords: $map.data.coords,
                    scrollwheel: true,              
                    type: "ROADMAP"
                })
                .addClass($map.data.containerClass);
            }
                    
            $.each($map.data.coordsMarker, function (key, value) {
                $map.addMarker({
                    coords: value,       
                });
            });
        }        
    }
});

jQuery(document).on('readyAndAjax', function () {
    let $map = $("#map-select");

    if ($map.length) {
        $map.data = $map.data();
        
        if (!$map.html().length) {        
            $map.googleMap({
                zoom: $map.data.zoom,
                coords: $map.data.coords,
                scrollwheel: true,            
                type: "ROADMAP"
            })
            .addClass($map.attr('data-container-class')); 

            $map.siblings('[id^="marker"]').each(function (index, element) {
                let $element = $(element);
                $element = {
                    lat: $element.find('input[id$="lat"]'),
                    long: $element.find('input[id$="long"]')
                };

                if ($element.lat.val().length && $element.long.val().length) {
                    $map.addMarker({
                        coords: [
                            $element.lat.val(), 
                            $element.long.val()
                        ],
                        id: 'marker' + index,
                        draggable: true,
                        success: function (e) {
                            $element.lat.val(e.lat);
                            $element.long.val(e.lon);                    
                        }
                    });
                }
            });
        }
    }     
});

jQuery(document).on('click', '#remove-marker', function (e) {
    e.preventDefault();

    let $map = $('#map-select');

    $map.removeMarker('marker0');
    $map.siblings('#marker0').find('input[id$="lat"]').val(null);
    $map.siblings('#marker0').find('input[id$="long"]').val(null);

    $('#add-marker').show();
    $(this).hide();
});

jQuery(document).on('click', '#add-marker', function (e, coords, address) {
    e.preventDefault();

    let $map = $('#map-select');

    if (!address && !coords) {
        coords = $map.data('coords');
    }

    $map.addMarker({
        coords: coords || null,
        address: address || null,
        draggable: true,
        id: 'marker0',
        success: function (e) {
            $map.siblings('#marker0').find('input[id$="lat"]').val(e.lat);
            $map.siblings('#marker0').find('input[id$="long"]').val(e.lon);                    
        }
    });

    $('#remove-marker').show();
    $(this).hide();
});
