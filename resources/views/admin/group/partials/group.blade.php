<div 
    id="row{{ $group->id }}" 
    class="row border-bottom py-3 position-relative transition"
    data-id="{{ $group->id }}"
>
    <div class="col my-auto d-flex justify-content-between">
        <ul class="list-unstyled mb-0 pb-0">
            <li>
                <a 
                    href="#" 
                    class="edit" 
                    data-route="{{ route('admin.group.edit_position', [$group->id]) }}"
                    data-toggle="modal" 
                    data-target="#editPositionModal" 
                    role="button"
                >
                    <span id="position" class="badge badge-pill badge-primary">
                        {{ $group->position + 1 }}
                    </span>
                </a>
                <span>{{ $group->name }}</span>
                <span class="badge badge-success">ID {{ $group->id }}</span>                
            </li>
            <li>
                {{ trans("idir::groups.visible.{$group->visible}") }}
            </li>
            <li>
                <small>{{ trans('icore::filter.created_at') }}: {{ $group->created_at_diff }}</small>
            </li>
            <li>
                <small>{{ trans('icore::filter.updated_at') }}: {{ $group->updated_at_diff }}</small>
            </li>
        </ul>
        <div class="text-right ml-3">
            <div class="responsive-btn-group">
                @can('admin.groups.edit')
                @if ($group->isNotDefault())
                <a 
                    class="btn btn-primary align-bottom" 
                    href="{{ route('admin.group.edit', [$group->id]) }}"
                    role="button" 
                    target="_blank" 
                    rel="noopener"
                >
                    <i class="fas fa-edit"></i>
                    <span class="d-none d-sm-inline">{{ trans('icore::default.edit') }}</span>
                </a>
                @endif
                @endcan
                @can('admin.groups.delete')
                @if ($group->isNotDefault())
                <form action="{{ route('admin.group.destroy', [$group->id]) }}" method="post">
                    @csrf
                    @method('delete')
                    <button 
                        class="btn btn-danger submit" 
                        data-status="delete" 
                        data-toggle="confirmation"
                        type="button" 
                        data-btn-ok-label=" {{ trans('icore::default.yes') }}" 
                        data-btn-ok-icon-class="fas fa-check mr-1"
                        data-btn-ok-class="btn h-100 d-flex justify-content-center btn-primary btn-popover destroy" 
                        data-btn-cancel-label=" {{ trans('icore::default.cancel') }}"
                        data-btn-cancel-class="btn h-100 d-flex justify-content-center btn-secondary btn-popover" 
                        data-btn-cancel-icon-class="fas fa-ban mr-1"
                        data-title="{{ trans('icore::default.confirm') }}"
                    >
                        <i class="far fa-trash-alt"></i>
                        <span class="d-none d-sm-inline">{{ trans('icore::default.delete') }}</span>
                    </button>
                </form>
                @endif
                @endcan
            </div>
        </div>
    </div>
</div>
