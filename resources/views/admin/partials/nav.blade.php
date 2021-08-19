<nav class="navbar menu navbar-expand navbar-light bg-light fixed-top border-bottom">
    <a 
        href="#" 
        class="navbar-toggler" 
        role="button" 
        id="sidebar-toggle"
    >
        <span class="navbar-toggler-icon"></span>
    </a>
    <a href="/" class="navbar-brand">
        <img 
            src="{{ asset('svg/vendor/idir/logo.svg') }}" 
            class="pb-1 pr-1 logo" 
            alt="{{ config('app.name_short') }}" 
            title="{{ config('app.name') }}"
        >
        <span>{{ config('app.name_short') }}</span>
    </a>
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a 
                class="nav-link text-nowrap" 
                href="#" 
                role="button" 
                id="navbar-dropdown-menu-link"
                data-toggle="dropdown" 
                aria-haspopup="true" 
                aria-expanded="false"
            >
                <i class="fas fa-lg fa-users-cog"></i>
                <span class="d-none d-sm-inline">{{ auth()->user()->short_name }}</span>
            </a>
            <div 
                class="dropdown-menu dropdown-menu-right" 
                aria-labelledby="navbar-dropdown-menu-link"
            >
                <h6 class="dropdown-header">
                    {{ trans('icore::auth.hello')}}, {{ auth()->user()->name }}
                </h6>
                <a 
                    class="dropdown-item {{ $isUrl(route('web.profile.edit')) }}" 
                    href="{{ route('web.profile.edit') }}" 
                    title="{{ trans('icore::profile.route.edit') }}"
                >
                    {{ trans('icore::profile.route.index') }}
                </a>
                @can('admin.home.view')
                <a 
                    class="dropdown-item" 
                    href="{{ route('admin.home.index') }}"
                    title="{{ trans('icore::admin.route.index') }}"
                >
                    {{ trans('icore::admin.route.index') }}
                </a>
                @endcan
                <div class="dropdown-divider"></div>
                <a 
                    class="dropdown-item" 
                    href="{{ route('logout') }}"
                    title="{{ trans('icore::auth.route.logout') }}"
                >
                    {{ trans('icore::auth.route.logout') }}
                </a>
            </div>
        </li>
    </ul>
</nav>
