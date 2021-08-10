@inject('dir', 'N1ebieski\IDir\Models\Dir')
@inject('report', 'N1ebieski\IDir\Models\Report\Dir\Report')

@component('icore::admin.partials.modal')
@slot('modal_id', 'filter-modal')

@slot('modal_title')
<i class="fas fa-sort-amount-up"></i>
<span> {{ trans('icore::filter.filter_title') }}</span>
@endslot

@slot('modal_body')
<div class="form-group">
    <label for="filter-search">
        {{ trans('icore::filter.search.label') }}
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
        {{ trans('icore::filter.filter') }} "{{ trans('icore::filter.status.label') }}"
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
            value="{{ $dir::ACTIVE }}" 
            {{ ($filter['status'] === $dir::ACTIVE) ? 'selected' : '' }}
        >
            {{ trans('idir::dirs.status.'.$dir::ACTIVE) }}
        </option>
        <option 
            value="{{ $dir::INACTIVE }}" 
            {{ ($filter['status'] === $dir::INACTIVE) ? 'selected' : '' }}
        >
            {{ trans('idir::dirs.status.'.$dir::INACTIVE) }}
        </option>
        <option 
            value="{{ $dir::PAYMENT_INACTIVE }}" 
            {{ ($filter['status'] === $dir::PAYMENT_INACTIVE) ? 'selected' : '' }}
        >
            {{ trans('idir::dirs.status.'.$dir::PAYMENT_INACTIVE) }}
        </option>
        <option 
            value="{{ $dir::BACKLINK_INACTIVE }}" 
            {{ ($filter['status'] === $dir::BACKLINK_INACTIVE) ? 'selected' : '' }}
        >
            {{ trans('idir::dirs.status.'.$dir::BACKLINK_INACTIVE) }}
        </option>
        <option 
            value="{{ $dir::STATUS_INACTIVE }}" 
            {{ ($filter['status'] === $dir::STATUS_INACTIVE) ? 'selected' : '' }}
        >
            {{ trans('idir::dirs.status.'.$dir::STATUS_INACTIVE) }}
        </option>
        <option 
            value="{{ $dir::INCORRECT_INACTIVE }}" 
            {{ ($filter['status'] === $dir::INCORRECT_INACTIVE) ? 'selected' : '' }}
        >
            {{ trans('idir::dirs.status.'.$dir::INCORRECT_INACTIVE) }}
        </option>
    </select>
</div>
@if ($groups->isNotEmpty())
<div class="form-group">
    <label for="filter-group">
        {{ trans('icore::filter.filter') }} "{{ trans('idir::filter.group') }}"
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
        {{ trans('icore::filter.filter') }} "{{ trans('icore::filter.category') }}"
    </label>
    <select 
        class="selectpicker select-picker-category" 
        data-live-search="true"
        data-abs="true"
        data-abs-max-options-length="10"
        data-abs-text-attr="name"
        data-abs-ajax-url="{{ route("api.category.dir.index") }}"
        data-abs-default-options="{{ json_encode([['value' => '', 'text' => trans('icore::filter.default')]]) }}"
        data-style="border"
        data-width="100%"
        name="filter[category]"
        id="filter-category"
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
        {{ trans('icore::filter.filter') }} "{{ trans('icore::filter.report.label') }}"
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
            value="{{ $report::REPORTED }}" 
            {{ ($filter['report'] === $report::REPORTED) ? 'selected' : '' }}
        >
            {{ trans('icore::filter.report.'.$report::REPORTED) }}
        </option>
        <option 
            value="{{ $report::UNREPORTED }}" 
            {{ ($filter['report'] === $report::UNREPORTED) ? 'selected' : '' }}
        >
            {{ trans('icore::filter.report.'.$report::UNREPORTED) }}
        </option>
    </select>
</div>
<div class="d-inline">
    <button type="button" class="btn btn-primary btn-send" id="filter-filter">
        <i class="fas fa-check"></i>
        <span>{{ trans('icore::default.apply') }}</span>
    </button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
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
