<!DOCTYPE html>
<html lang="en">
<head>

    <title>{{ app('Helpers\View')->makeMeta(array_merge($title, [trans('icore::admin.page.index'), config('app.name')]), ' - ') }}</title>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="{{ app('Helpers\View')->makeMeta(array_merge($desc, [trans('icore::admin.page.index'), config('app.desc')]), '. ') }}">
    <meta name="keywords" content="{{ strtolower(app('Helpers\View')->makeMeta(array_merge($keys, [trans('icore::admin.page.index'), config('app.keys')]), ', ')) }}">
    <meta name="robots" content="noindex, nofollow">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Styles -->
    <link href="{{ asset('css/vendor/icore/vendor/vendor.css') }}" rel="stylesheet">
    <link href="{{ app('Helpers\View')->getStylesheet() }}" rel="stylesheet">

    <!-- Scripts -->
    <script src="{{ asset('js/vendor/icore/vendor/vendor.js') }}"></script>
    <script src="{{ asset('js/vendor/icore/admin/admin.js') }}"></script>

</head>
<body>

    @include('icore::admin.partials.nav')

    <div class="wrapper">

        @include('idir::admin.partials.sidebar')

        <div class="content-wrapper">

            <div class="container-fluid">
                @include('icore::admin.partials.breadcrumb')
                @include('icore::admin.partials.alerts')
                @yield('content')
            </div>

            @include('icore::admin.partials.footer')

        </div>

    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <script src="{{ asset('js/vendor/icore/admin/scripts.js') }}" defer></script>
    @stack('script')

</body>
</html>
