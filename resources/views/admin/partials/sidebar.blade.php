<div class="sidebar position scroll @isCookie('sidebarToggle', 'toggled')">
    <ul class="sidebar bg-light navbar-light position-fixed scroll navbar-nav border-right
    @isCookie('sidebarToggle', 'toggled')">
        <li class="nav-item navbar-light fake-toggler">
            <a href="#" class="navbar-toggler" role="button" id="sidebarToggle">
                <span class="navbar-toggler-icon"></span>
            </a>
        </li>
        @can('index dashboard')
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.home.index') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
        @endcan
        @can('index pages')
        <li class="nav-item @isUrlContains(['*/pages', '*/pages/*'])">
            <a class="nav-link" href="{{ route('admin.page.index') }}">
                <i class="fas fa-fw fa-file-word"></i>
                <span>{{ trans('icore::pages.route.index') }}</span>
            </a>
        </li>
        @endcan
        @can('index posts')
        <li class="nav-item @isUrlContains(['*/posts', '*/posts/*'])">
            <a class="nav-link" href="{{ route('admin.post.index') }}">
                <i class="fas fa-fw fa-blog"></i>
                <span>{{ trans('icore::posts.route.index') }}</span>
            </a>
        </li>
        @endcan
        @canany(['index dirs', 'index bans'])
        <li class="nav-item dropdown @isUrlContains(['*/dirs', '*/dirs/*'])
        @isUrl(route('admin.banvalue.index', ['type' => 'url']))">
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
                @can('index dirs')
                <div class="dropdown-item @isUrlContains(['*/dirs', '*/dirs/*'])"
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
                @can('index bans')
                <a class="dropdown-item @isUrl(route('admin.banvalue.index', ['type' => 'url']))"
                href="{{ route('admin.banvalue.index', ['type' => 'url']) }}">
                    {{ trans('idir::bans.value.url.route.index') }}
                </a>
                @endcan
            </div>
        </li>
        @endcanany
        @can('index comments')
        <li class="nav-item dropdown @isUrl([
            route('admin.comment.post.index'),
            route('admin.comment.page.index'),
            route('admin.comment.dir.index')
        ])">
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
                <div class="dropdown-item @isUrl(route("admin.comment.{$type}.index"))"
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
        @can('index categories')
        <li class="nav-item dropdown @isUrl([
            route('admin.category.post.index'),
            route('admin.category.dir.index')
        ])">
            <a class="nav-link dropdown-toggle"
            href="#" id="pagesDropdown" role="button"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-fw fa-layer-group"></i>
                <span>{{ trans('icore::categories.route.index') }}</span>
            </a>
            <div class="dropdown-menu" aria-labelledby="pagesDropdown">
                <h6 class="dropdown-header">{{ trans('icore::default.type') }}:</h6>
                <a class="dropdown-item @isUrl(route('admin.category.post.index'))"
                href="{{ route('admin.category.post.index') }}">
                    {{ trans('icore::categories.post.post') }}
                </a>
                <a class="dropdown-item @isUrl(route('admin.category.dir.index'))"
                href="{{ route('admin.category.dir.index') }}">
                    {{ trans('idir::categories.dir.dir') }}
                </a>
            </div>
        </li>
        @endcan
        @canany(['index groups', 'index fields'])
        <li class="nav-item dropdown @isUrlContains(['*/groups', '*/groups/*', '*/fields/group', '*/fields/group/*'])">
            <a class="nav-link dropdown-toggle"
            href="#" id="groupDropdown" role="button"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-fw fa-object-group"></i>
                <span>{{ trans('idir::groups.route.index') }}</span>
            </a>
            <div class="dropdown-menu" aria-labelledby="groupDropdown">
                @can('index groups')
                <a class="dropdown-item @isUrlContains(['*/groups', '*/groups/*'])"
                href="{{ route('admin.group.index') }}">
                    {{ trans('idir::groups.route.index') }}
                </a>
                @endcan
                @can('index fields')
                <a class="dropdown-item @isUrl(route('admin.field.group.index'))"
                href="{{ route('admin.field.group.index') }}">
                    {{ trans('idir::fields.route.index') }}
                </a>
                @endcan
            </div>
        </li>
        @endcan
        @can('index mailings')
        <li class="nav-item @isUrlContains(['*/mailings', '*/mailings/*'])">
            <a class="nav-link" href="{{ route('admin.mailing.index') }}">
                <i class="fas fa-fw fa-envelope"></i>
                <span>{{ trans('icore::mailings.route.index') }}</span>
            </a>
        </li>
        @endcan
        @canany(['index users', 'index bans', 'index roles'])
        <li class="nav-item dropdown
        @isUrl([
            route('admin.user.index'),
            route('admin.role.index'),
            route('admin.banmodel.user.index'),
            route('admin.banvalue.index', ['type' => 'ip'])
        ])
        @isUrlContains(['*/roles', '*/roles/*'])">
            <a class="nav-link dropdown-toggle"
            href="#" id="userDropdown" role="button"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-fw fa-users"></i>
                <span>{{ trans('icore::users.route.index') }}</span>
            </a>
            <div class="dropdown-menu" aria-labelledby="userDropdown">
                @can('index users')
                <a class="dropdown-item @isUrl(route('admin.user.index'))"
                href="{{ route('admin.user.index') }}">
                    {{ trans('icore::users.route.index') }}
                </a>
                @endcan
                @can('index roles')
                <a class="dropdown-item @isUrlContains(['*/roles', '*/roles/*'])"
                href="{{ route('admin.role.index') }}">
                    {{ trans('icore::roles.route.index') }}
                </a>
                @endcan
                @can('index bans')
                <h6 class="dropdown-header">{{ trans('icore::bans.route.index') }}:</h6>
                <a class="dropdown-item @isUrl(route('admin.banmodel.user.index'))"
                href="{{ route('admin.banmodel.user.index') }}">
                    {{ trans('icore::bans.model.user.route.index') }}
                </a>
                <a class="dropdown-item @isUrl(route('admin.banvalue.index', ['type' => 'ip']))"
                href="{{ route('admin.banvalue.index', ['type' => 'ip']) }}">
                    {{ trans('icore::bans.value.ip.route.index') }}
                </a>
                @endcan
            </div>
        </li>
        @endcanany
        @canany(['index bans', 'index links'])
        <li class="nav-item dropdown @isUrl([
            route('admin.banvalue.index', ['word']),
            route('admin.link.index', ['link']),
            route('admin.link.index', ['backlink'])
        ])">
            <a class="nav-link dropdown-toggle"
            href="#" id="pagesDropdown" role="button"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-fw fa-tools"></i>
                <span>{{ trans('icore::admin.route.settings') }}</span>
            </a>
            <div class="dropdown-menu" aria-labelledby="pagesDropdown">
                @can('index bans')
                <a class="dropdown-item @isUrl(route('admin.banvalue.index', ['word']))"
                href="{{ route('admin.banvalue.index', ['word']) }}">
                    {{ trans('icore::bans.value.word.route.index') }}
                </a>
                @endcan
                @can('index links')
                <a class="dropdown-item @isUrl(route('admin.link.index', ['link']))"
                href="{{ route('admin.link.index', ['link']) }}">
                    {{ trans('icore::links.link.route.index') }}
                </a>
                <a class="dropdown-item @isUrl(route('admin.link.index', ['backlink']))"
                href="{{ route('admin.link.index', ['backlink']) }}">
                    {{ trans('icore::links.backlink.route.index') }}
                </a>
                @endcan
            </div>
        </li>
        @endcanany
    </ul>
</div>
