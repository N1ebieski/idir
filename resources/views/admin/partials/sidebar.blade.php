<div class="sidebar position scroll {{ $isCookie('sidebarToggle', 'toggled') }}">
    <ul class="sidebar bg-light navbar-light position-fixed scroll navbar-nav border-right
    {{ $isCookie('sidebarToggle', 'toggled') }}">
        <li class="nav-item navbar-light fake-toggler">
            <a href="#" class="navbar-toggler" role="button" id="sidebarToggle">
                <span class="navbar-toggler-icon"></span>
            </a>
        </li>
        @can('admin.home.view')
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.home.index') }}"
            title="Dashboard">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
        @endcan
        @can('admin.pages.view')
        <li class="nav-item {{ $isUrlContains(['*/pages', '*/pages/*']) }}">
            <a class="nav-link" href="{{ route('admin.page.index') }}"
            title="{{ trans('icore::pages.route.index') }}">
                <i class="fas fa-fw fa-file-word"></i>
                <span>{{ trans('icore::pages.route.index') }}</span>
            </a>
        </li>
        @endcan
        @can('admin.posts.view')
        <li class="nav-item {{ $isUrlContains(['*/posts', '*/posts/*']) }}">
            <a class="nav-link" href="{{ route('admin.post.index') }}"
            title="{{ trans('icore::posts.route.index') }}">
                <i class="fas fa-fw fa-blog"></i>
                <span>{{ trans('icore::posts.route.index') }}</span>
            </a>
        </li>
        @endcan
        @canany(['admin.dirs.view', 'admin.bans.view'])
        <li class="nav-item dropdown {{ $isUrlContains(['*/dirs', '*/dirs/*']) }}
        {{ $isUrl(route('admin.banvalue.index', ['type' => 'url'])) }}">
            <div class="nav-link dropdown-toggle"
            id="dirDropdown" role="button" style="cursor: pointer;"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="far fa-fw fa-folder-open"></i>
                <span> {{ trans('idir::dirs.route.index') }}</span>
                @if ($dirs_inactive_count > 0)
                <span class="badge badge-warning"> {{ $dirs_inactive_count }}</span>
                @endif
                @if ($dirs_reported_count > 0)
                <span class="badge badge-danger"> {{ $dirs_reported_count }}</span>
                @endif
            </div>
            <div class="dropdown-menu" aria-labelledby="dirDropdown">
                @can('admin.dirs.view')
                <div class="dropdown-item {{ $isUrlContains(['*/dirs', '*/dirs/*']) }}"
                onclick="window.location.href='{{ route('admin.dir.index') }}'"
                style="cursor: pointer;">
                    {{ trans('idir::dirs.route.index') }}
                    @if ($dirs_inactive_count > 0)
                    <span>
                        <a href="{{ route('admin.dir.index', ['filter[status]' => 0]) }}"
                        class="badge badge-warning">
                            {{ $dirs_inactive_count }}
                        </a>
                    </span>
                    @endif
                    @if ($dirs_reported_count > 0)
                    <span>
                        <a href="{{ route('admin.dir.index', ['filter[report]' => 1]) }}"
                        class="badge badge-danger">
                            {{ $dirs_reported_count }}
                        </a>
                    </span>
                    @endif                    
                </div>
                @endcan
                @can('admin.bans.view')
                <a class="dropdown-item {{ $isUrl(route('admin.banvalue.index', ['type' => 'url'])) }}"
                href="{{ route('admin.banvalue.index', ['type' => 'url']) }}"
                title="{{ trans('idir::bans.value.url.route.index') }}">
                    {{ trans('idir::bans.value.url.route.index') }}
                </a>
                @endcan
            </div>
        </li>
        @endcanany
        @can('admin.comments.view')
        <li class="nav-item dropdown {{ $isUrl([
            route('admin.comment.post.index'),
            route('admin.comment.page.index'),
            route('admin.comment.dir.index')
        ]) }}">
            <a class="nav-link dropdown-toggle"
            href="#" id="commentDropdown" role="button"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-fw fa-comments"></i>
                <span> {{ trans('icore::comments.route.index') }} </span>
                @if ($count = $comments_inactive_count->sum('count'))
                <span class="badge badge-warning">{{ $count }}</span>
                @endif
                @if ($count = $comments_reported_count->sum('count'))
                <span class="badge badge-danger">{{ $comments_reported_count->sum('count') }}</span>
                @endif
            </a>
            <div class="dropdown-menu" aria-labelledby="commentDropdown">
                <h6 class="dropdown-header">{{ trans('icore::default.type') }}:</h6>
                @foreach(['post', 'page', 'dir'] as $type)
                <div class="dropdown-item {{ $isUrl(route("admin.comment.{$type}.index")) }}"
                onclick="window.location.href='{{ route("admin.comment.{$type}.index") }}'"
                style="cursor: pointer;">
                    {{ trans("icore::comments.{$type}.{$type}") }}
                    @if ($count = $comments_inactive_count->where('model', $type)->first())
                    <span>
                        <a href="{{ route("admin.comment.{$type}.index", ['filter[status]' => 0]) }}"
                        class="badge badge-warning">
                            {{ $count->count }}
                        </a>
                    </span>
                    @endif
                    @if ($count = $comments_reported_count->where('model', $type)->first())
                    <span>
                        <a href="{{ route("admin.comment.{$type}.index", ['filter[report]' => 1]) }}"
                        class="badge badge-danger">
                            {{ $count->count }}
                        </a>
                    </span>
                    @endif                    
                </div>
                @endforeach
            </div>
        </li>
        @endcan
        @can('admin.categories.view')
        <li class="nav-item dropdown {{ $isUrl([
            route('admin.category.post.index'),
            route('admin.category.dir.index')
        ]) }}">
            <a class="nav-link dropdown-toggle"
            href="#" id="pagesDropdown" role="button"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-fw fa-layer-group"></i>
                <span>{{ trans('icore::categories.route.index') }}</span>
            </a>
            <div class="dropdown-menu" aria-labelledby="pagesDropdown">
                <h6 class="dropdown-header">{{ trans('icore::default.type') }}:</h6>
                <a class="dropdown-item {{ $isUrl(route('admin.category.post.index')) }}"
                href="{{ route('admin.category.post.index') }}"
                title="{{ trans('icore::categories.post.post') }}">
                    {{ trans('icore::categories.post.post') }}
                </a>
                <a class="dropdown-item {{ $isUrl(route('admin.category.dir.index')) }}"
                href="{{ route('admin.category.dir.index') }}"
                title="{{ trans('idir::categories.dir.dir') }}">
                    {{ trans('idir::categories.dir.dir') }}
                </a>
            </div>
        </li>
        @endcan
        @canany(['admin.groups.view', 'admin.fields.view'])
        <li class="nav-item dropdown {{ $isUrlContains(['*/groups', '*/groups/*', '*/fields/group', '*/fields/group/*']) }}">
            <a class="nav-link dropdown-toggle"
            href="#" id="groupDropdown" role="button"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-fw fa-object-group"></i>
                <span>{{ trans('idir::groups.route.index') }}</span>
            </a>
            <div class="dropdown-menu" aria-labelledby="groupDropdown">
                @can('admin.groups.view')
                <a class="dropdown-item {{ $isUrlContains(['*/groups', '*/groups/*']) }}"
                href="{{ route('admin.group.index') }}"
                title="{{ trans('idir::groups.route.index') }}">
                    {{ trans('idir::groups.route.index') }}
                </a>
                @endcan
                @can('admin.fields.view')
                <a class="dropdown-item {{ $isUrl(route('admin.field.group.index')) }}"
                href="{{ route('admin.field.group.index') }}"
                title="{{ trans('idir::fields.route.index') }}">
                    {{ trans('idir::fields.route.index') }}
                </a>
                @endcan
            </div>
        </li>
        @endcan
        @can('admin.mailings.view')
        <li class="nav-item {{ $isUrlContains(['*/mailings', '*/mailings/*']) }}">
            <a class="nav-link" href="{{ route('admin.mailing.index') }}"
            title="{{ trans('icore::mailings.route.index') }}">
                <i class="fas fa-fw fa-envelope"></i>
                <span>{{ trans('icore::mailings.route.index') }}</span>
            </a>
        </li>
        @endcan
        @canany(['admin.users.view', 'admin.bans.view', 'admin.roles.view'])
        <li class="nav-item dropdown {{ $isUrl([
            route('admin.user.index'),
            route('admin.role.index'),
            route('admin.banmodel.user.index'),
            route('admin.banvalue.index', ['type' => 'ip'])
        ]) }} {{ $isUrlContains(['*/roles', '*/roles/*']) }}">
            <a class="nav-link dropdown-toggle"
            href="#" id="userDropdown" role="button"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-fw fa-users"></i>
                <span>{{ trans('icore::users.route.index') }}</span>
            </a>
            <div class="dropdown-menu" aria-labelledby="userDropdown">
                @can('admin.users.view')
                <a class="dropdown-item {{ $isUrl(route('admin.user.index')) }}"
                href="{{ route('admin.user.index') }}"
                title="{{ trans('icore::users.route.index') }}">
                    {{ trans('icore::users.route.index') }}
                </a>
                @endcan
                @can('admin.roles.view')
                <a class="dropdown-item {{ $isUrlContains(['*/roles', '*/roles/*']) }}"
                href="{{ route('admin.role.index') }}"
                title="{{ trans('icore::roles.route.index') }}">
                    {{ trans('icore::roles.route.index') }}
                </a>
                @endcan
                @can('admin.bans.view')
                <h6 class="dropdown-header">{{ trans('icore::bans.route.index') }}:</h6>
                <a class="dropdown-item {{ $isUrl(route('admin.banmodel.user.index')) }}"
                href="{{ route('admin.banmodel.user.index') }}"
                title="{{ trans('icore::bans.model.user.route.index') }}">
                    {{ trans('icore::bans.model.user.route.index') }}
                </a>
                <a class="dropdown-item {{ $isUrl(route('admin.banvalue.index', ['type' => 'ip'])) }}"
                href="{{ route('admin.banvalue.index', ['type' => 'ip']) }}"
                title="{{ trans('icore::bans.value.ip.route.index') }}">
                    {{ trans('icore::bans.value.ip.route.index') }}
                </a>
                @endcan
            </div>
        </li>
        @endcanany
        @canany(['admin.bans.view', 'admin.links.view'])
        <li class="nav-item dropdown {{ $isUrl([
            route('admin.banvalue.index', ['word']),
            route('admin.link.index', ['link']),
            route('admin.link.index', ['backlink'])
        ]) }}">
            <a class="nav-link dropdown-toggle"
            href="#" id="pagesDropdown" role="button"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-fw fa-tools"></i>
                <span>{{ trans('icore::admin.route.settings') }}</span>
            </a>
            <div class="dropdown-menu" aria-labelledby="pagesDropdown">
                @can('admin.bans.view')
                <a class="dropdown-item {{ $isUrl(route('admin.banvalue.index', ['word'])) }}"
                href="{{ route('admin.banvalue.index', ['word']) }}"
                title="{{ trans('icore::bans.value.word.route.index') }}">
                    {{ trans('icore::bans.value.word.route.index') }}
                </a>
                @endcan
                @can('admin.links.view')
                <a class="dropdown-item {{ $isUrl(route('admin.link.index', ['link'])) }}"
                href="{{ route('admin.link.index', ['link']) }}"
                title="{{ trans('icore::links.link.route.index') }}">
                    {{ trans('icore::links.link.route.index') }}
                </a>
                <a class="dropdown-item {{ $isUrl(route('admin.link.index', ['backlink'])) }}"
                href="{{ route('admin.link.index', ['backlink']) }}"
                title="{{ trans('icore::links.backlink.route.index') }}">
                    {{ trans('icore::links.backlink.route.index') }}
                </a>
                @endcan
            </div>
        </li>
        @endcanany
    </ul>
</div>
