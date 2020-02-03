jQuery(document).on('readyAndAjax', function() {
    let $map = $("#map-select");
    $map.data = $map.data();

    if ($map.length) {
        if (!$map.html().length) {        
            $map.googleMap({
                zoom: $map.data.zoom,
                coords: [52.15, 21.00],
                scrollwheel: true,            
                type: "ROADMAP"
            })
            .addClass($map.attr('data-container-class')); 

            if (typeof $map.data.coordsMarker !== 'undefined' && $map.data.coordsMarker.length) {
                $.each($map.data.coordsMarker, function(key, value) {
                    $map.addMarker({
                        coords: value,       
                        draggable: true,
                        id: 'marker' + key,
                        success: function(e) {
                            $map.siblings('[id^="marker' + key + '"]').find('input[id$="lat"]').val(e.lat);
                            $map.siblings('[id^="marker' + key + '"]').find('input[id$="long"]').val(e.lon);                    
                        }
                    });
                });
            } 
            else {
                $map.siblings('[id^="marker"]').each(function(index, element) {
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
                            success: function(e) {
                                $element.lat.val(e.lat);
                                $element.long.val(e.lon);                    
                            }
                        });
                    }
                }) 
            }
        }
    }     
});

jQuery(document).on('click', '#remove-marker', function(e) {
    e.preventDefault();

    let $map = $('#map-select');

    $map.removeMarker('marker0');
    $map.siblings('[id^="marker0"]').find('input[id$="lat"]').val(null);
    $map.siblings('[id^="marker0"]').find('input[id$="long"]').val(null);

    $('#add-marker').show();
    $(this).hide();
});

jQuery(document).on('click', '#add-marker', function(e) {
    e.preventDefault();

    let $map = $('#map-select');

    $map.addMarker({
        coords: [52.15, 21.00],
        draggable: true,
        id: 'marker0',
        success: function(e) {
            $map.siblings('[id^="marker0"]').find('input[id$="lat"]').val(e.lat);
            $map.siblings('[id^="marker0"]').find('input[id$="long"]').val(e.lon);                    
        }
    });

    $('#remove-marker').show();
    $(this).hide();
});