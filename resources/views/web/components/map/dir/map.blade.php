<div 
    id="{{ $selector }}"
    data-coords="{{ $coords }}" 
    data-container-class="{{ $containerClass }}"
    data-address-marker="{{ $addressMarker ?? null }}" 
    data-zoom="{{ $zoom }}"
    data-coords-marker="{{ $coordsMarker ?? null }}"
></div>

@pushonce('script.map')
<script defer 
    src="https://maps.googleapis.com/maps/api/js?key={{ config('services.googlemap.api_key') }}&callback=initMap" 
    type="text/javascript"
></script>
@endpushonce