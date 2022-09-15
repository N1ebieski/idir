/*
 * NOTICE OF LICENSE
 * 
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 * 
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 * 
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

$(document).on('ready.n1ebieski/idir/web/scripts/plugins/jquery-googlemap@init', function () {
    $('#map, .map').each(function () {
        let $map = $(this);

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
});

$(document).on('readyAndAjax.n1ebieski/idir/web/scripts/plugins/jquery-googlemap@initSelect', function () {
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

$(document).on(
    'click.n1ebieski/idir/web/scripts/plugins/jquery-googlemap@remove',
    '#remove-marker',
    function (e) {
        e.preventDefault();

        let $map = $('#map-select');

        $map.removeMarker('marker0');
        $map.siblings('#marker0').find('input[id$="lat"]').val(null);
        $map.siblings('#marker0').find('input[id$="long"]').val(null);

        $('#add-marker').show();
        $(this).hide();
    }
);

$(document).on(
    'click.n1ebieski/idir/web/scripts/plugins/jquery-googlemap@add',
    '#add-marker',
    function (e, coords, address) {
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
    }
);
