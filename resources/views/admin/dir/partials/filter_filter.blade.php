@component('icore::admin.partials.modal')

@slot('modal_id', 'filter-modal')

@slot('modal_title')
<i class="fas fa-filter"></i>
<span> {{ trans('icore::filter.filter_title') }}</span>
@endslot

@slot('modal_body')
<div class="form-group">
    <label for="filter-search">
        {{ trans('icore::filter.search.label') }}:
    </label>
    <input 
        type="text" 
        class="form-control" 
        id="filter-search" 
        placeholder="{{ trans('icore::filter.search.placeholder') }}"
        name="filter[search]" 
        value="{{ isset($filter['search']) ? $filter['search'] : '' }}"
    >
</div>
<div class="form-group">
    <label for="filter-status">
        {{ trans('icore::filter.filter') }} "{{ trans('icore::filter.status.label') }}":
    </label>
    <select 
        class="form-control custom-select" 
        id="filter-status" 
        name="filter[status]"
    >
        <option value="">
            {{ trans('icore::filter.default') }}
        </option>
        <option 
            value="{{ Dir\Status::ACTIVE }}" 
            {{ ($filter['status'] === Dir\Status::ACTIVE) ? 'selected' : '' }}
        >
            {{ trans('idir::dirs.status.'.Dir\Status::ACTIVE) }}
        </option>
        <option 
            value="{{ Dir\Status::INACTIVE }}" 
            {{ ($filter['status'] === Dir\Status::INACTIVE) ? 'selected' : '' }}
        >
            {{ trans('idir::dirs.status.'.Dir\Status::INACTIVE) }}
        </option>
        <option 
            value="{{ Dir\Status::PAYMENT_INACTIVE }}" 
            {{ ($filter['status'] === Dir\Status::PAYMENT_INACTIVE) ? 'selected' : '' }}
        >
            {{ trans('idir::dirs.status.'.Dir\Status::PAYMENT_INACTIVE) }}
        </option>
        <option 
            value="{{ Dir\Status::BACKLINK_INACTIVE }}" 
            {{ ($filter['status'] === Dir\Status::BACKLINK_INACTIVE) ? 'selected' : '' }}
        >
            {{ trans('idir::dirs.status.'.Dir\Status::BACKLINK_INACTIVE) }}
        </option>
        <option 
            value="{{ Dir\Status::STATUS_INACTIVE }}" 
            {{ ($filter['status'] === Dir\Status::STATUS_INACTIVE) ? 'selected' : '' }}
        >
            {{ trans('idir::dirs.status.'.Dir\Status::STATUS_INACTIVE) }}
        </option>
        <option 
            value="{{ Dir\Status::INCORRECT_INACTIVE }}" 
            {{ ($filter['status'] === Dir\Status::INCORRECT_INACTIVE) ? 'selected' : '' }}
        >
            {{ trans('idir::dirs.status.'.Dir\Status::INCORRECT_INACTIVE) }}
        </option>
    </select>
</div>
@if ($groups->isNotEmpty())
<div class="form-group">
    <label for="filter-group">
        {{ trans('icore::filter.filter') }} "{{ trans('idir::filter.group') }}":
    </label>
    <select 
        class="form-control custom-select" 
        id="filter-group" 
        name="filter[group]"
    >
        <option value="">
            {{ trans('icore::filter.default') }}
        </option>
        @foreach ($groups as $group)
        <option 
            value="{{ $group->id }}" 
            {{ ($filter['group'] !== null && $filter['group']->id == $group->id) ? 'selected' : '' }}
        >
            {{ $group->name }}
        </option>
        @endforeach
    </select>
</div>
@endif
<div class="form-group">
    <label for="filter-category">
        {{ trans('icore::filter.filter') }} "{{ trans('icore::filter.category') }}":
    </label>
    <select 
        id="filter-category"
        name="filter[category]"
        class="selectpicker select-picker-category" 
        data-live-search="true"
        data-abs="true"
        data-abs-max-options-length="10"
        data-abs-text-attr="name"
        data-abs-ajax-url="{{ route("api.category.dir.index") }}"
        data-abs-default-options="{{ json_encode([['value' => '', 'text' => trans('icore::filter.default')]]) }}"
        data-style="border"
        data-width="100%"
        data-container="body"
    >
        <optgroup label="{{ trans('icore::default.current_option') }}">
            <option value="">
                {{ trans('icore::filter.default') }}
            </option>
            @if ($filter['category'] !== null)
            <option 
                @if ($filter['category']->ancestors->isNotEmpty())
                data-content='<small class="p-0 m-0">{{ implode(' &raquo; ', $filter['category']->ancestors->pluck('name')->toArray()) }} &raquo; </small>{{ $filter['category']->name }}'
                @endif
                value="{{ $filter['category']->id }}" 
                selected
            >
                {{ $filter['category']->name }}
            </option>
            @endif
        </optgroup>
    </select>
</div>
<div class="form-group">
    <label for="filter-report">
        {{ trans('icore::filter.filter') }} "{{ trans('icore::filter.report.label') }}":
    </label>
    <select 
        class="form-control custom-select" 
        id="filter-report" 
        name="filter[report]"
    >
        <option value="">
            {{ trans('icore::filter.default') }}
        </option>
        <option 
            value="{{ Report\Reported::ACTIVE }}" 
            {{ ($filter['report'] === Report\Reported::ACTIVE) ? 'selected' : '' }}
        >
            {{ trans('icore::filter.report.'.Report\Reported::ACTIVE) }}
        </option>
        <option 
            value="{{ Report\Reported::INACTIVE }}" 
            {{ ($filter['report'] === Report\Reported::INACTIVE) ? 'selected' : '' }}
        >
            {{ trans('icore::filter.report.'.Report\Reported::INACTIVE) }}
        </option>
    </select>
</div>
@endslot

@slot('modal_footer')
<div class="d-inline">
    <button 
        id="filter-filter"
        type="button" 
        class="btn btn-primary btn-send"
        form="filter"
    >
        <i class="fas fa-check"></i>
        <span>{{ trans('icore::default.apply') }}</span>
    </button>
    <button 
        type="button" 
        class="btn btn-secondary" 
        data-dismiss="modal"
    >
        <i class="fas fa-ban"></i>
        <span>{{ trans('icore::default.cancel') }}</span>
    </button>
</div>
@endslot

@endcomponent

@push('script')
@component('icore::admin.partials.jsvalidation')
{!! JsValidator::formRequest('N1ebieski\IDir\Http\Requests\Admin\Dir\IndexRequest', '#filter'); !!}
@endcomponent
@endpush
