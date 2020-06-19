<div id="row{{ $field->id }}" class="row border-bottom py-3 position-relative transition"
data-id="{{ $field->id }}">
    <div class="col my-auto d-flex justify-content-between">
        <ul class="list-unstyled mb-0 pb-0">
            <li>
                <a href="#" class="edit" data-route="{{ route('admin.field.edit_position', [$field->id]) }}"
                data-toggle="modal" data-target="#editPositionModal" role="button">
                    <span id="position" class="badge badge-pill badge-primary">{{ $field->position + 1 }}</span>
                </a>
                <span> {{ $field->title }}</span>&nbsp;
                <span class="badge badge-primary">{{ $field->type }}</span>&nbsp;
                <span class="badge badge-success">ID {{ $field->id }}</span>
            </li>
            <li><small>{{ trans('icore::filter.created_at') }}: {{ $field->created_at_diff }}</small></li>
            <li><small>{{ trans('icore::filter.updated_at') }}: {{ $field->updated_at_diff }}</small></li>
        </ul>
        <div class="text-right ml-3">
            <div class="responsive-btn-group">
                @can('admin.fields.edit')
                <div class="btn-group-vertical">
                    <button data-toggle="modal" data-target="#editModal"
                    data-route="{{ route("admin.field.{$field->poli}.edit", ['field' => $field->id]) }}"
                    type="button" class="btn btn-primary edit">
                        <i class="far fa-edit"></i>
                        <span class="d-none d-sm-inline"> {{ trans('icore::default.edit') }}</span>
                    </button>
                </div>
                @endcan
                @can('admin.fields.delete')
                @if ($field->isNotDefault())
                <button class="btn btn-danger" data-status="delete" data-toggle="confirmation"
                data-route="{{ route('admin.field.destroy', ['field' => $field->id]) }}" data-id="{{ $field->id }}"
                type="button" data-btn-ok-label=" {{ trans('icore::default.yes') }}" data-btn-ok-icon-class="fas fa-check mr-1"
                data-btn-ok-class="btn h-100 d-flex justify-content-center btn-primary btn-popover destroyCategory" 
                data-btn-cancel-label=" {{ trans('icore::default.cancel') }}"
                data-btn-cancel-class="btn h-100 d-flex justify-content-center btn-secondary btn-popover"
                data-btn-cancel-icon-class="fas fa-ban mr-1"
                data-title="{{ trans('icore::default.confirm') }}">
                    <i class="far fa-trash-alt"></i>
                    <span class="d-none d-sm-inline"> {{ trans('icore::default.delete') }}</span>
                </button>
                @endif
                @endcan
            </div>
        </div>
    </div>
</div>
