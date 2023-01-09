<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <title>{{ $getMeta(array_merge($title, [config('app.name')]), ' - ') }}</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="{{ $getMeta(array_merge($desc, [config('app.desc')]), '. ') }}">
    <meta name="keywords" content="{{ mb_strtolower($getMeta(array_merge($keys, [config('app.keys')]), ', ')) }}">
    <meta name="robots" content="{{ $index }}">
    <meta name="robots" content="{{ $follow }}">

    <meta property="og:title" content="{{ $og['title'] }}">
    <meta property="og:description" content="{{ $og['desc'] }}">
    <meta property="og:type" content="{{ $og['type'] }}">
    <meta property="og:image" content="{{ $og['image'] }}">
    <meta property="og:url" content="{{ $getUrl }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="canonical" href="{{ $getUrl }}">

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/vendor/idir/icons/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/vendor/idir/icons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/vendor/idir/icons/android-chrome-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/vendor/idir/icons/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('images/vendor/idir/icons/site.webmanifest') }}">
    <link rel="mask-icon" href="{{ asset('images/vendor/idir/icons/safari-pinned-tab.svg') }}" color="#5bbad5">
    <link rel="shortcut icon" href="{{ asset('images/vendor/idir/icons/favicon.ico') }}">
    <meta name="msapplication-TileColor" content="#2d89ef">
    <meta name="msapplication-config" content="{{ asset('images/vendor/idir/icons/browserconfig.xml') }}">
    <meta name="theme-color" content="#ffffff">

    <link href="{{ asset(mix('css/vendor/idir/vendor/vendor.css')) }}" rel="stylesheet">
    <link href="{{ asset(mix($getStylesheet('css/vendor/idir'))) }}" rel="stylesheet">
    <link href="{{ asset($getStylesheet('css/custom')) }}" rel="stylesheet">

    <script src="{{ asset(mix('js/vendor/idir/vendor/vendor.js')) }}" defer></script>
    <script src="{{ asset(mix('js/vendor/idir/web/web.js')) }}" defer></script>
    <script src="{{ asset('js/custom/web/web.js') }}" defer></script>
</head>
<body>

    @include('idir::web.partials.nav')

    <div class="content">
        @include('icore::web.partials.breadcrumb')
        @yield('content')
    </div>

    @include('idir::web.partials.footer')

    @include('icore::web.partials.policy')

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    @stack('script')
    <script src="{{ asset(mix('js/vendor/idir/web/scripts.js')) }}" defer></script>
    <script src="{{ asset('js/custom/web/scripts.js') }}" defer></script>
</body>
</html>
