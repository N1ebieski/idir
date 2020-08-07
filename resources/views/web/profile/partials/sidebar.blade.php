<h3 class="h5 d-sm-none">{{ trans('icore::profile.pages') }}</h3>
<ul class="sidebar navbar-nav h-100">
    <li class="nav-item {{ $isUrl(route('web.profile.edit')) }}">
        <a class="nav-link {{ $isUrl(route('web.profile.edit')) }}"
        title="{{ trans('icore::profile.route.edit') }}"
        href="{{ route('web.profile.edit') }}">
            <i class="fas fa-fw fa-user-edit"></i>
            <span>{{ trans('icore::profile.route.edit') }}</span>
        </a>
    </li>
    @if (app('router')->has('web.profile.edit_socialite'))
    <li class="nav-item {{ $isUrl(route('web.profile.edit_socialite')) }}">
        <a class="nav-link {{ $isUrl(route('web.profile.edit_socialite')) }}"
        title="{{ trans('icore::profile.route.edit_socialite') }}"
        href="{{ route('web.profile.edit_socialite') }}">
            <i class="fab fa-fw fa-facebook-square"></i>
            <span>{{ trans('icore::profile.route.edit_socialite') }}</span>
        </a>
    </li>
    @endif
    @canany(['web.dirs.edit', 'web.dirs.delete'])
    <li class="nav-item {{ $isUrl(route('web.profile.edit_dir')) }}">
        <a class="nav-link {{ $isUrl(route('web.profile.edit_dir')) }}"
        title="{{ trans('idir::profile.route.edit_dir') }}"
        href="{{ route('web.profile.edit_dir') }}">
            <i class="far fa-fw fa-folder-open"></i>
            <span>{{ trans('idir::profile.route.edit_dir') }}</span>
        </a>
    </li>
    @endcan
</ul>
