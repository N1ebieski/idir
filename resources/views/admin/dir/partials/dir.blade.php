<div id="row{{ $dir->id }}" class="row border-bottom py-3 position-relative transition"
data-id="{{ $dir->id }}">
    <div class="col my-auto d-flex justify-content-between">
        @can('destroy dirs')
        <div class="custom-control custom-checkbox">
            <input name="select[]" type="checkbox" class="custom-control-input select" id="select{{ $dir->id }}" value="{{ $dir->id }}">
            <label class="custom-control-label" for="select{{ $dir->id }}">
        @endcan
                <ul class="list-unstyled mb-0 pb-0">
                    <li>{{ $dir->title_as_link }}</li>
                    <li contenteditable="true" spellcheck="true">{{ $dir->shortContent }}...</li>
                    <li>{{ $dir->tagList }}</li>
                    <li><small>{{ trans('idir::dirs.author') }}: <a href="{{ route('admin.dir.index', ['filter[author]' => $dir->user->id]) }}">{{ $dir->user->name }}</a></small></li>
                    <li><small>{{ trans('icore::filter.created_at') }}: {{ $dir->created_at_diff }}</small></li>
                    <li><small>{{ trans('icore::filter.updated_at') }}: {{ $dir->updated_at_diff }}</small></li>
                </ul>
        @can('destroy dirs')
            </label>
        </div>
        @endcan
        <div class="text-right ml-3">
            <div class="responsive-btn-group">
                @can('edit dirs')
                <div class="btn-group-vertical">
                    <button data-toggle="modal" data-target="#editModal"
                    data-route="{{ route('admin.dir.edit', [$dir->id]) }}"
                    type="button" class="btn btn-primary edit">
                        <i class="far fa-edit"></i>
                        <span class="d-none d-sm-inline"> {{ trans('icore::default.edit') }}</span>
                    </button>
                    <a class="btn btn-primary align-bottom" href="{{ route('admin.dir.edit_full_1', [$dir->id]) }}"
                    role="button" target="_blank">
                        <i class="fas fa-edit"></i>
                        <span class="d-none d-sm-inline"> {{ trans('icore::default.editFull') }}</span>
                    </a>
                </div>
                @endcan
                @can('status dirs')
                <button data-status="1" type="button" class="btn btn-success status"
                data-route="{{ route('admin.dir.update_status', [$dir->id]) }}"
                {{ $dir->status == 1 ? 'disabled' : '' }}>
                    <i class="fas fa-toggle-on"></i>
                    <span class="d-none d-sm-inline"> {{ trans('icore::default.active') }}</span>
                </button>
                <button data-status="0" type="button" class="btn btn-warning status"
                data-route="{{ route('admin.dir.update_status', [$dir->id]) }}"
                {{ $dir->status == 0 ? 'disabled' : '' }}>
                    <i class="fas fa-toggle-off"></i>
                    <span class="d-none d-sm-inline"> {{ trans('icore::default.inactive') }}</span>
                </button>
                @endcan
                @can('destroy dirs')
                <button class="btn btn-danger" data-status="delete" data-toggle="dir-confirmation"
                data-route="{{ route('admin.dir.destroy', [$dir->id]) }}" data-id="{{ $dir->id }}"
                type="button" data-btn-ok-label=" {{ trans('icore::default.yes') }}" data-btn-ok-icon-class="fas fa-check"
                data-btn-ok-class="btn-primary btn-popover destroyDir" data-btn-cancel-label=" {{ trans('icore::default.cancel') }}"
                data-btn-cancel-class="btn-secondary btn-popover" data-btn-cancel-icon-class="fas fa-ban"
                data-title="{{ trans('icore::default.confirm') }}">
                    <i class="far fa-trash-alt"></i>
                    <span class="d-none d-sm-inline">&nbsp;{{ trans('icore::default.delete') }}</span>
                </button>
                @endcan
            </div>
        </div>
    </div>
</div>
