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
            src="{{ asset('images/vendor/idir/logo.svg') }}" 
            class="pb-1 pr-1 logo" 
            alt="{{ config('app.name_short') }}" 
            title="{{ config('app.name') }}"
        >
        <span class="d-none d-md-inline">{{ config('app.name_short') }}</span>
    </a>
    <ul class="navbar-nav ml-auto">
        @if (count(config('icore.multi_themes')) > 1)
        <li class="nav-item dropdown">
            <a 
                class="nav-link text-nowrap" 
                href="#" 
                role="button" 
                id="dropdown-multi-theme"
                data-toggle="dropdown" 
                aria-haspopup="true" 
                aria-expanded="false"
            >
                <span class="fas fa-lg fa-icore-{{ $currentTheme }}"></span>
            </a>
            <div 
                class="dropdown-menu dropdown-menu-right"
                id="dropdown-multi-theme-toggle"
                aria-labelledby="dropdown-multi-theme"
            >
                <h6 class="dropdown-header">
                    {{ trans('icore::default.theme_toggle') }}:
                </h6>
                @foreach ($themes as $theme)
                <a 
                    class="dropdown-item {{ $isCurrentTheme($theme) }}"
                    data-theme="{{ $theme }}"
                    href="#{{ $theme }}" 
                    title="{{ trans('icore::default.' . $theme) }}"
                >
                    <span class="fas fa-icore-{{ $theme }}"></span>
                    <span>{{ trans('icore::default.' . $theme) }}</span>
                </a>
                @endforeach                       
            </div>
        </li>
        @endif        
        <li class="nav-item dropdown">
            <a 
                class="nav-link text-nowrap" 
                href="#" 
                role="button" 
                id="navbar-dropdown-menu-profile"
                data-toggle="dropdown" 
                aria-haspopup="true" 
                aria-expanded="false"
            >
                <i class="fas fa-lg fa-users-cog"></i>
            </a>
            <div 
                class="dropdown-menu dropdown-menu-right" 
                aria-labelledby="navbar-dropdown-menu-profile"
            >
                <h6 class="dropdown-header">
                    {{ trans('icore::auth.hello')}}, {{ auth()->user()->name }}!
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
                <form 
                    class="d-inline" 
                    method="POST" 
                    action="{{ route('logout') }}"
                >
                    @csrf

                    <button type="submit" class="btn btn-link dropdown-item">
                        {{ trans('icore::auth.route.logout') }}
                    </button>
                </form>
            </div>
        </li>
    </ul>
</nav>
