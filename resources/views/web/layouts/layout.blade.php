<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <title>{{ $makeMeta(array_merge($title, [config('app.name')]), ' - ') }}</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="{{ $makeMeta(array_merge($desc, [config('app.desc')]), '. ') }}">
    <meta name="keywords" content="{{ mb_strtolower($makeMeta(array_merge($keys, [config('app.keys')]), ', ')) }}">
    <meta name="robots" content="{{ $index }}">
    <meta name="robots" content="{{ $follow }}">

    <meta property="og:title" content="{{ $og['title'] }}">
    <meta property="og:description" content="{{ $og['desc'] }}">
    <meta property="og:type" content="{{ $og['type'] }}">
    <meta property="og:image" content="{{ $og['image'] }}">
    <meta property="og:url" content="{{ url()->current() }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ asset('svg/vendor/idir/logo.svg') }}" type="image/svg+xml">
    <link href="{{ mix('css/vendor/idir/vendor/vendor.css') }}" rel="stylesheet">
    <link href="{{ mix($getStylesheet('css/vendor/idir')) }}" rel="stylesheet">
    <link href="{{ asset($getStylesheet('css/custom')) }}" rel="stylesheet">

    <script src="{{ mix('js/vendor/idir/vendor/vendor.js') }}" defer></script>
    <script src="{{ mix('js/vendor/idir/web/web.js') }}" defer></script>
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
    <script src="{{ mix('js/vendor/idir/web/scripts.js') }}" defer></script>
    <script src="{{ asset('js/custom/web/scripts.js') }}" defer></script>
</body>
</html>
