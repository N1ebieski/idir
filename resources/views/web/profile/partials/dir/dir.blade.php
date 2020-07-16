<div id="row{{ $dir->id }}" class="row border-bottom py-3 position-relative transition"
data-id="{{ $dir->id }}">
    <div class="col my-auto d-flex justify-content-between">
        <ul class="list-unstyled mb-0 pb-0">
            <li>
                <a href="{{ route('web.dir.show', [$dir->slug]) }}" target="_blank"
                title="{{ $dir->title }}">
                    {{ $dir->title }}
                </a>
                <span class="badge badge-{{ $dir->status === $dir::ACTIVE ? 'success' : 'warning' }}">
                    {{ trans("idir::dirs.status.{$dir->status}") }}
                </span>
            </li>
            <li class="text-break" style="word-break:break-word">{{ $dir->short_content }}...</li>
            @if ($dir->notes)
            <li class="text-break font-weight-bold" style="word-break:break-word">
                {{ $dir->notes }}
            </li>
            @endif
            <li>
                <div class="d-flex">
                    <small class="mr-auto">{{ trans('idir::dirs.group') }}: {{ $dir->group->name }}</small>
                    @if ($dir->tags->isNotEmpty())
                    <small class="ml-auto text-right">{{ trans('idir::dirs.tags.label') }}:
                        @foreach ($dir->tags as $tag)
                        <a href="{{ route('web.tag.dir.show', [$tag->normalized]) }}"
                        title="{{ $tag->name }}">
                            {{ $tag->name }}
                        </a>{{ (!$loop->last) ? ', ' : '' }}
                        @endforeach
                    </small>
                    @endif
                </div>
            </li>
            @if ($dir->privileged_to !== null)
            <li><small>{{ trans('idir::dirs.privileged_to') }}: {{ $dir->privileged_to_diff }}</small></li>
            @endif
            <li><small>{{ trans('icore::filter.created_at') }}: {{ $dir->created_at_diff }}</small></li>
            <li><small>{{ trans('icore::filter.updated_at') }}: {{ $dir->updated_at_diff }}</small></li>
        </ul>
        <div class="text-right ml-3">
            <div class="responsive-btn-group">
                @can('web.dirs.edit')
                <div class="btn-group-vertical">
                    <a class="btn btn-primary align-bottom" href="{{ route('web.dir.edit_1', [$dir->id]) }}"
                    role="button" target="_blank">
                        <i class="fas fa-edit"></i>
                        <span class="d-none d-md-inline">&nbsp;{{ trans('icore::default.edit') }}</span>
                    </a>
                </div>
                @endcan
                @can('web.dirs.delete')
                <button class="btn btn-danger" data-status="delete" data-toggle="confirmation"
                data-route="{{ route('web.dir.destroy', [$dir->id]) }}" data-id="{{ $dir->id }}"
                type="button" data-btn-ok-label=" {{ trans('icore::default.yes') }}" data-btn-ok-icon-class="fas fa-check mr-1"
                data-btn-ok-class="btn h-100 d-flex justify-content-center btn-primary btn-popover destroy" 
                data-btn-cancel-label=" {{ trans('icore::default.cancel') }}"
                data-btn-cancel-class="btn h-100 d-flex justify-content-center btn-secondary btn-popover" 
                data-btn-cancel-icon-class="fas fa-ban mr-1"
                data-title="{{ trans('icore::default.confirm') }}">
                    <i class="far fa-trash-alt"></i>
                    <span class="d-none d-md-inline">&nbsp;{{ trans('icore::default.delete') }}</span>
                </button>
                @endcan
            </div>
        </div>
    </div>
</div>
