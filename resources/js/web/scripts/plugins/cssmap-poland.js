$(document).on('ready.n1ebieski/idir/web/scripts/plugins/cssmap-poland@init', function () {
    $("#map-poland").CSSMap({
        "size": 430,
        "tooltips": "floating-top-center",
        "responsive": "auto",
        "tapOnce": true,
        onLoad: function () {
            $("#map-poland").find('a.active-region').parent().addClass('active-region');
        }
    }).children().show();
});
