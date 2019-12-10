<div id="row{{ $dir->id }}" class="row border-bottom py-3 position-relative transition">
    <div class="col my-auto d-flex justify-content-between">
        <ul class="list-unstyled mb-0 pb-0">
            <li><a href="{{ route('web.dir.show', [$dir->slug]) }}" target="_blank">{{ $dir->title }}</a></li>
            <li>{{ $dir->short_content }}...</li>
        </ul>
        <div class="text-right ml-3">
            <div class="responsive-btn-group">
                <div class="btn-group-vertical">
                    <a class="btn btn-primary align-bottom" href="{{ route('web.dir.edit_1', [$dir->id]) }}"
                    role="button" target="_blank">
                        <i class="fas fa-edit"></i>
                        <span class="d-none d-sm-inline">&nbsp;{{ trans('icore::default.edit') }}</span>
                    </a>
                </div>
                <button class="btn btn-danger" data-status="delete" data-toggle="confirmation"
                data-route="{{ route('web.dir.destroy', [$dir->id]) }}" data-id="{{ $dir->id }}"
                type="button" data-btn-ok-label=" {{ trans('icore::default.yes') }}" data-btn-ok-icon-class="fas fa-check"
                data-btn-ok-class="btn-primary btn-popover destroy" data-btn-cancel-label=" {{ trans('icore::default.cancel') }}"
                data-btn-cancel-class="btn-secondary btn-popover" data-btn-cancel-icon-class="fas fa-ban"
                data-title="{{ trans('icore::default.confirm') }}">
                    <i class="far fa-trash-alt"></i>
                    <span class="d-none d-sm-inline">&nbsp;{{ trans('icore::default.delete') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>
