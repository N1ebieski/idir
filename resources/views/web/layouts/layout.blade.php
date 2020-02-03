<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <title>{{ app('Helpers\View')->makeMeta(array_merge($title, [config('app.name')]), ' - ') }}</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="{{ app('Helpers\View')->makeMeta(array_merge($desc, [config('app.desc')]), '. ') }}">
    <meta name="keywords" content="{{ strtolower(app('Helpers\View')->makeMeta(array_merge($keys, [config('app.keys')]), ', ')) }}">
    <meta name="robots" content="{{ $index }}">
    <meta name="robots" content="{{ $follow }}">

    <meta property="og:title" content="{{ $og['title'] }}">
    <meta property="og:description" content="{{ $og['desc'] }}">
    <meta property="og:type" content="{{ $og['type'] }}">
    <meta property="og:image" content="{{ $og['image'] }}">
    <meta property="og:url" content="{{ url()->current() }}">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Styles -->
    <link href="{{ mix('css/vendor/idir/vendor/vendor.css') }}" rel="stylesheet">
    <link href="{{ mix(app('Helpers\View')->getStylesheet('css/vendor/idir')) }}" rel="stylesheet">

    <!-- Scripts -->
    <script src="{{ mix('js/vendor/idir/vendor/vendor.js') }}"></script>
    <script src="{{ mix('js/vendor/idir/web/web.js') }}"></script>

    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-star-rating/4.0.6/js/star-rating.min.js" type="text/javascript"></script>

    <!-- optionally if you need to use a theme, then include the theme JS file as mentioned below -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-star-rating/4.0.6/themes/krajee-svg/theme.js"></script>    --}}
</head>
<body>

    @include('idir::web.partials.nav')

    <div class="content">
        @include('icore::web.partials.breadcrumb')
        @yield('content')
    </div>

    @include('idir::web.partials.footer')

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <script src="{{ mix('js/vendor/idir/web/scripts.js') }}" async defer></script>
    @stack('script')

</body>
</html>
