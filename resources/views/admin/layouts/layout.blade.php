<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <title>{{ $getMeta(array_merge($title, [trans('icore::admin.route.index'), config('app.name')]), ' - ') }}</title>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="{{ $getMeta(array_merge($desc, [trans('icore::admin.route.index'), config('app.desc')]), '. ') }}">
    <meta name="keywords" content="{{ mb_strtolower($getMeta(array_merge($keys, [trans('icore::admin.route.index'), config('app.keys')]), ', ')) }}">
    <meta name="robots" content="noindex, nofollow">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="canonical" href="{{ $getUrl }}">
    <link rel="icon" href="{{ asset('svg/vendor/idir/logo.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('images/vendor/idir/logo.png') }}">  
    <link href="{{ asset(mix('css/vendor/idir/vendor/vendor.css')) }}" rel="stylesheet">
    @stack('style')
    <link href="{{ asset(mix($getStylesheet('css/vendor/idir'))) }}" rel="stylesheet">
    <link href="{{ asset($getStylesheet('css/custom')) }}" rel="stylesheet">

    <script src="{{ asset(mix('js/vendor/idir/vendor/vendor.js')) }}" defer></script>
    <script src="{{ asset(mix('js/vendor/idir/admin/admin.js')) }}" defer></script>
    <script src="{{ asset('js/custom/admin/admin.js') }}" defer></script>
</head>
<body>

    @include('idir::admin.partials.nav')

    <div class="wrapper">

        @include('idir::admin.partials.sidebar')

        <div class="content-wrapper">

            <div class="menu-height"></div>

            <div class="container-fluid">
                @include('icore::admin.partials.breadcrumb')
                @include('icore::admin.partials.alerts')
                @yield('content')
            </div>

            @include('idir::admin.partials.footer')

        </div>

    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    @stack('script')
    <script src="{{ asset(mix('js/vendor/idir/admin/scripts.js')) }}" defer></script>
    <script src="{{ asset('js/custom/admin/scripts.js') }}" defer></script>
</body>
</html>
