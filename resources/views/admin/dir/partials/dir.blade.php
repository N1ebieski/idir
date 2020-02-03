@section('thumbnail')
<div class="d-flex flex-column">
    <div class="thumbnail d-inline position-relative" style="width:90px;height:68px">
        <img src="{{ $dir->thumbnail_url }}" class="img-fluid border"
        alt="{{ $dir->title }}">
    </div>
    <a href="#" data-route="{{ route('admin.thumbnail.dir.reload', [$dir->id]) }}" 
    class="badge badge-primary reloadThumbnail">
        {{ trans('idir::dirs.reload_thumbnail') }}
    </a>
</div>
@overwrite

<div id="row{{ $dir->id }}" class="row border-bottom py-3 position-relative transition"
data-id="{{ $dir->id }}">
    <div class="col my-auto d-flex w-100 justify-content-between">
        <div class="custom-control custom-checkbox">
            @can('destroy dirs')
            <input name="select[]" type="checkbox" class="custom-control-input select" id="select{{ $dir->id }}" value="{{ $dir->id }}">
            <label class="custom-control-label" for="select{{ $dir->id }}">
            @endcan
            <ul class="list-unstyled mb-0 pb-0">
                <li>
                    {!! $dir->title_as_link !!}
                    @if ($dir->reports_count > 0)
                    <span>
                        <a href="#" class="badge badge-danger show" data-toggle="modal"
                        data-route="{{ route('admin.report.dir.show', [$dir->id]) }}"
                        data-target="#showReportDirModal">
                            {{ trans('icore::reports.page.show') }}: {{ $dir->reports_count }}
                        </a>
                    </span>
                    @endif
                </li>
                <li class="text-break">
                    <span contenteditable="true" spellcheck="true" id="content.{{ $dir->id }}">
                        {{ $dir->short_content }}...
                    </span>
                    <a href="#" class="badge badge-primary checkContent">
                        {{ trans('idir::dirs.check_content') }}
                    </a>
                </li>
                @if ($dir->group->fields->isNotEmpty())
                @foreach ($dir->group->fields as $field)
                @if ($value = optional($dir->fields->where('id', $field->id)->first())->decode_value)
                <li class="text-break">
                    {{ $field->title }}: 
                    <span>
                    @if (in_array($field->type, ['input', 'textarea', 'select']))
                        {{ $value }}
                    @elseif (in_array($field->type, ['multiselect', 'checkbox']))
                        {{ implode(', ', $value) }}
                    @elseif ($field->type === 'regions')
                        {{ implode(', ', $dir->regions->pluck('name')->toArray()) }}
                    @else
                        <img class="img-fluid" src="{{ Storage::url($value) }}">
                    @endif
                    </span>
                </li>
                @endif
                @endforeach
                @endif                
            </ul>
            @can('destroy dirs')
            </label>
            @endcan            
            <div class="d-flex">
                <ul class="list-unstyled mb-0 pb-0 flex-grow-1">
                    @if ($dir->tagList)
                    <li class="text-break"><small>{{ trans('idir::dirs.tags') }}: {{ $dir->tagList }}</small></li>
                    @endif
                    @if ($dir->categories->isNotEmpty())
                    <li>
                        <small>
                            <span>{{ trans('icore::categories.categories') }}:</span> 
                            <span>
                                @foreach ($dir->categories as $category)
                                <a href="{{ route('admin.dir.index', ['filter[category]' => $category->id]) }}">{{ $category->name }}</a>
                                {{ (!$loop->last) ? ', ' : '' }}
                                @endforeach
                            </span>
                        </small>
                    </li>
                    @endif                   
                    <li>
                        <small>
                            <span>{{ trans('idir::dirs.group') }}:</span>
                            <span>
                                <a href="{{ route('admin.dir.index', ['filter[group]' => $dir->group->id]) }}">{{ $dir->group->name }}</a>
                            </span>
                        </small>
                        @if ($dir->group->prices->isNotEmpty() && $dir->payments->isNotEmpty())
                        <span>
                            <a href="#" class="badge badge-warning show" data-toggle="modal"
                            data-route="{{ route('admin.payment.dir.show_logs', [$dir->id]) }}"
                            data-target="#showPaymentLogsDirModal">
                                {{ trans('idir::payments.page.show_logs') }}
                            </a>
                        </span>
                        @endif
                    </li>
                    @if (isset($dir->user))
                    <li>
                        <small>
                            <span>{{ trans('idir::dirs.author') }}:</span>
                            <span><a href="{{ route('admin.dir.index', ['filter[author]' => $dir->user->id]) }}">{{ $dir->user->name }}</a></span>
                        </small>
                    </li>
                    @endif
                    <li><small>{{ trans('icore::filter.created_at') }}: {{ $dir->created_at_diff }}</small></li>
                    <li><small>{{ trans('icore::filter.updated_at') }}: {{ $dir->updated_at_diff }}</small></li>
                </ul>
                @if ($dir->url !== null)
                <div class="pt-2 pl-2 d-xl-none">
                    @yield('thumbnail')
                </div>
                @endif
            </div>
        </div>
        <div class="text-right ml-3 d-flex flex-column">
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
                @if ($dir->isUpdateStatus())
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
                @endif
                @endcan
                <div class="btn-group-vertical">
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
                    @can('create bans')
                    <button type="button" class="btn btn-dark create"
                    data-route="{{ route('admin.banmodel.dir.create', [$dir->id]) }}"
                    data-toggle="modal" data-target="#createBanDirModal">
                        <i class="fas fa-user-slash"></i>
                        <span class="d-none d-sm-inline"> {{ trans('icore::default.ban') }}</span>
                    </button>
                    @endcan
                </div>
            </div>
            @if ($dir->url !== null)
            <div class="d-none d-xl-block mt-auto ml-auto pt-2">
                @yield('thumbnail')
            </div>
            @endif 
        </div>      
    </div>     
</div>
