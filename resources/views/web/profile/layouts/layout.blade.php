@extends(config('icore.layout') . '::web.layouts.layout')

@section('breadcrumb')
    @yield('breadcrumb')
@overwrite

@section('content')
<div class="container">
    <div class="row">
        <div class="col-auto order-2 order-sm-1 pr-0">
            @include(config('icore.layout') . '::web.profile.partials.sidebar')
        </div>
        <div class="col-12 col-sm order-1 order-sm-2 mb-3">
            @include('icore::web.partials.alerts')

            @yield('content')
        </div>
    </div>
</div>
@overwrite
