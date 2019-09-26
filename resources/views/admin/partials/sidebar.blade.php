<div class="sidebar position scroll @isCookie('sidebarToggle', 'toggled')">
    <ul class="sidebar bg-light navbar-light position-fixed scroll navbar-nav border-right
    @isCookie('sidebarToggle', 'toggled')">
        <li class="nav-item navbar-light fake-toggler">
            <a href="#" class="navbar-toggler" role="button" id="sidebarToggle">
                <span class="navbar-toggler-icon"></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.home.index') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i><span> Dashboard</span>
            </a>
        </li>
        @can('index pages')
        <li class="nav-item @isUrlContains(['*/pages', '*/pages/*'])">
            <a class="nav-link" href="{{ route('admin.page.index') }}">
                <i class="fas fa-fw fa-file-word"></i><span> {{ trans('icore::pages.page.index') }}</span>
            </a>
        </li>
        @endcan
        @can('index posts')
        <li class="nav-item @isUrlContains(['*/posts', '*/posts/*'])">
            <a class="nav-link" href="{{ route('admin.post.index') }}">
                <i class="fas fa-fw fa-blog"></i><span> {{ trans('icore::posts.page.index') }}</span>
            </a>
        </li>
        @endcan
        @can('index comments')
        <li class="nav-item dropdown @isUrl([
            route('admin.comment.post.index'),
            route('admin.comment.page.index'),
        ])">
            <a class="nav-link dropdown-toggle"
            href="#" id="commentDropdown" role="button"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-fw fa-comments"></i>
                <span> {{ trans('icore::comments.page.index') }} </span>
                <span class="badge badge-warning">{{ $comments_inactive_count->sum('count') }}</span>
            </a>
            <div class="dropdown-menu" aria-labelledby="commentDropdown">
                <h6 class="dropdown-header">{{ trans('icore::default.type') }}:</h6>
                @foreach(['post', 'page'] as $type)
                <div class="position-relative">
                    <a class="dropdown-item @isUrl(route("admin.comment.{$type}.index"))" href="{{ route("admin.comment.{$type}.index") }}">
                        {{ trans("icore::comments.page.type.{$type}") }}
                    </a>
                    @if ($count = $comments_inactive_count->where('model', $type)->first())
                    <a href="{{ route("admin.comment.{$type}.index", ['filter[status]' => 0]) }}"
                    class="badge badge-warning">
                        {{ $count->count }}
                    </a>
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
                <i class="fas fa-fw fa-layer-group"></i><span> {{ trans('icore::categories.page.index') }}</span>
            </a>
            <div class="dropdown-menu" aria-labelledby="pagesDropdown">
                <h6 class="dropdown-header">{{ trans('icore::default.type') }}:</h6>
                <a class="dropdown-item @isUrl(route('admin.category.post.index'))"
                href="{{ route('admin.category.post.index') }}">
                    {{ trans('icore::categories.page.type.post') }}
                </a>
                <a class="dropdown-item @isUrl(route('admin.category.dir.index'))"
                href="{{ route('admin.category.dir.index') }}">
                    {{ trans('idir::categories.page.type.dir') }}
                </a>
            </div>
        </li>
        @endcan
        @can('index groups')
        <li class="nav-item @isUrlContains(['*/groups', '*/groups/*'])">
            <a class="nav-link" href="{{ route('admin.group.dir.index') }}">
                <i class="fas fa-fw fa-object-group"></i><span> {{ trans('idir::groups.page.index') }}</span>
            </a>
        </li>
        @endcan
        @can('index mailings')
        <li class="nav-item @isUrlContains(['*/mailings', '*/mailings/*'])">
            <a class="nav-link" href="{{ route('admin.mailing.index') }}">
                <i class="fas fa-fw fa-envelope"></i><span> {{ trans('icore::mailings.page.index') }}</span>
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
                <i class="fas fa-fw fa-users"></i><span> {{ trans('icore::users.page.index') }}</span>
            </a>
            <div class="dropdown-menu" aria-labelledby="userDropdown">
                @can('index users')
                <a class="dropdown-item @isUrl(route('admin.user.index'))"
                href="{{ route('admin.user.index') }}">
                    {{ trans('icore::users.page.index') }}
                </a>
                @endcan
                @can('index roles')
                <a class="dropdown-item @isUrlContains(['*/roles', '*/roles/*'])"
                href="{{ route('admin.role.index') }}">
                    {{ trans('icore::roles.page.index') }}
                </a>
                @endcan
                @can('index bans')
                <h6 class="dropdown-header">{{ trans('icore::bans.page.index') }}:</h6>
                <a class="dropdown-item @isUrl(route('admin.banmodel.user.index'))"
                href="{{ route('admin.banmodel.user.index') }}">
                    {{ trans('icore::bans.model.user.page.index') }}
                </a>
                <a class="dropdown-item @isUrl(route('admin.banvalue.index', ['type' => 'ip']))"
                href="{{ route('admin.banvalue.index', ['type' => 'ip']) }}">
                    {{ trans('icore::bans.value.ip.page.index') }}
                </a>
                @endcan
            </div>
        </li>
        @endcanany
        @canany(['index bans'])
        <li class="nav-item dropdown @isUrl([route('admin.banvalue.index', ['word'])])">
            <a class="nav-link dropdown-toggle"
            href="#" id="pagesDropdown" role="button"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-fw fa-tools"></i><span> {{ trans('icore::admin.page.settings') }}</span>
            </a>
            <div class="dropdown-menu" aria-labelledby="pagesDropdown">
                @can('index bans')
                <a class="dropdown-item @isUrl(route('admin.banvalue.index', ['word']))"
                href="{{ route('admin.banvalue.index', ['word']) }}">
                    {{ trans('icore::bans.value.word.page.index') }}
                </a>
                @endcan
            </div>
        </li>
        @endcanany
    </ul>
</div>
