@section('logo')
<div id="navbarLogo" class="flex-grow-1 mr-2">
    <a href="/" class="navbar-brand">
        <img src="{{ asset('svg/vendor/icore/logo.svg') }}" class="pb-1 logo" alt="{{ config('app.name') }}">
        <span class="pl-1 d-none d-lg-inline">
            {{ config('app.name') }}
        </span>
    </a>
</div>
@endsection

@section('navbar-toggler')
<a href="#" id="navbarToggle" class="my-auto navbar-toggler" role="button">
    <span class="navbar-toggler-icon"></span>
</a>
@endsection

@section('search-toggler')
<a href="#" class="nav-link search-toggler" role="button">
    <i class="fa fa-lg fa-search"></i>
</a>
@endsection

<nav class="navbar navbar-expand-md navbar-light bg-light fixed-top border-bottom">
    <div class="container">
        @if (!app('Helpers\Active')->isUrl(route('web.home.index')))
        <div class="d-flex flex-grow-1 navbar-search pr-3 pr-md-0">
            @yield('logo')
            <form id="searchForm" method="GET" action="{{ route('web.search.index') }}" class="my-auto w-100 hide search">
                <div class="input-group">
                    <input id="typeahead" data-route="{{ route('web.search.autocomplete') }}" type="text" name="search"
                    class="form-control border-right-0" placeholder="{{ trans('icore::search.search') }}"
                    value="{{ $search ?? null }}">
                    <select class="custom-select" name="source">
                        <option value="post" @isRouteContains('post', 'selected')>{{ trans('icore::search.type.post') }}</option>
                        <option value="dir" @isRouteContains('dir', 'selected')>{{ trans('idir::search.type.dir') }}</option>
                    </select>
                    <span class="input-group-append">
                        <button class="btn btn-outline-secondary border border-left-0"
                        type="submit" {{ isset($search) ?: 'disabled' }}>
                            <i class="fa fa-search"></i>
                        </button>
                    </span>
                </div>
            </form>
            <div class="my-auto">
                <ul class="navbar-nav">
                    <li class="nav-item d-sm-inline d-md-none ml-2">
                        @yield('search-toggler')
                    </li>
                </ul>
            </div>
            @yield('navbar-toggler')
        </div>
        @else
        <div class="d-flex flex-grow-1 pr-3 pr-md-0">
            @yield('logo')
            @yield('navbar-toggler')
        </div>
        @endif
        <div class="navbar-collapse scroll collapse flex-grow-0 justify-content-end">
            @render('icore::page.menuComponent', ['limit' => 3])
            <ul class="navbar-nav pr-3 pr-md-0">
                @if (!app('Helpers\Active')->isUrl(route('web.home.index')))
                <li class="nav-item d-none d-md-inline mr-1">
                    @yield('search-toggler')
                </li>
                @endif
                <li class="nav-item mr-sm-0 mr-md-2 mb-2 mb-md-0">
                    <a class="nav-link text-primary btn btn-sm btn-outline-primary"
                    href="{{ route('web.dir.create_1') }}" role="button">
                        {{ trans('idir::dirs.page.create.index') }}
                    </a>
                </li>
                <li class="nav-item dropdown @isUrl([route('web.profile.edit')])">
                    @auth
                    <a class="nav-link text-nowrap" href="#" role="button" id="navbarDropdownMenuProfile"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-fw fa-lg fa-users-cog"></i><span class="d-md-none d-lg-inline">&nbsp;{{ auth()->user()->short_name }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuProfile">
                        <h6 class="dropdown-header">{{ trans('icore::auth.hello')}}, {{ auth()->user()->name }}</h6>
                        <a class="dropdown-item @isUrl(route('web.profile.edit'))" href="{{ route('web.profile.edit') }}">{{ trans('icore::profile.page.edit') }}</a>
                        @can('index dashboard')
                        <a class="dropdown-item" href="{{ route('admin.home.index') }}">{{ trans('icore::admin.page.index') }}</a>
                        @endcan
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('logout') }}">{{ trans('icore::auth.page.logout') }}</a>
                    </div>
                    @else
                    <a class="nav-link btn btn-sm btn-primary text-white text-nowrap text-center" href="{{ route('login') }}"
                    role="button">{{ trans('icore::auth.page.login') }}</a>
                    @endauth
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="menu-height"></div>
