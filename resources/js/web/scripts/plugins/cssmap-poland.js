$(document).ready(function () {
    // CSSMap;
    $("#map-poland").CSSMap({
      "size": 430,
      "tooltips": "floating-top-center",
      "responsive": "auto",
      "tapOnce": true,
      onLoad: function () {
        $("#map-poland").find('a.active-region').parent().addClass('active-region');
      }
    }).children().show();
    // END OF THE CSSMap;
 });