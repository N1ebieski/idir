@inject('dir', 'N1ebieski\IDir\Models\Dir')
@inject('report', 'N1ebieski\IDir\Models\Report\Dir\Report')

@component('icore::admin.partials.modal')
@slot('modal_id', 'filterModal')

@slot('modal_title')
<i class="fas fa-sort-amount-up"></i>
<span> {{ trans('icore::filter.filter_title') }}</span>
@endslot

@slot('modal_body')
<div class="form-group">
    <label for="FormSearch">{{ trans('icore::filter.search.label') }}</label>
    <input type="text" class="form-control" id="FormSearch" placeholder="{{ trans('icore::filter.search.placeholder') }}"
    name="filter[search]" value="{{ isset($filter['search']) ? $filter['search'] : '' }}">
</div>
<div class="form-group">
    <label for="FormStatus">{{ trans('icore::filter.filter') }} "{{ trans('icore::filter.status.label') }}"</label>
    <select class="form-control custom-select" id="FormVisible" name="filter[status]">
        <option value="">{{ trans('icore::filter.default') }}</option>
        <option value="{{ $dir::ACTIVE }}" {{ ($filter['status'] === $dir::ACTIVE) ? 'selected' : '' }}>
            {{ trans('idir::dirs.status.'.$dir::ACTIVE) }}
        </option>
        <option value="{{ $dir::INACTIVE }}" {{ ($filter['status'] === $dir::INACTIVE) ? 'selected' : '' }}>
            {{ trans('idir::dirs.status.'.$dir::INACTIVE) }}
        </option>
        <option value="{{ $dir::PAYMENT_INACTIVE }}" {{ ($filter['status'] === $dir::PAYMENT_INACTIVE) ? 'selected' : '' }}>
            {{ trans('idir::dirs.status.'.$dir::PAYMENT_INACTIVE) }}
        </option>
        <option value="{{ $dir::BACKLINK_INACTIVE }}" {{ ($filter['status'] === $dir::BACKLINK_INACTIVE) ? 'selected' : '' }}>
            {{ trans('idir::dirs.status.'.$dir::BACKLINK_INACTIVE) }}
        </option>
        <option value="{{ $dir::STATUS_INACTIVE }}" {{ ($filter['status'] === $dir::STATUS_INACTIVE) ? 'selected' : '' }}>
            {{ trans('idir::dirs.status.'.$dir::STATUS_INACTIVE) }}
        </option>
    </select>
</div>
@if ($groups->isNotEmpty())
<div class="form-group">
    <label for="group">{{ trans('icore::filter.filter') }} "{{ trans('idir::filter.group') }}"</label>
    <select class="form-control custom-select" id="group" name="filter[group]">
        <option value="">{{ trans('icore::filter.default') }}</option>
        @foreach ($groups as $group)
        <option value="{{ $group->id }}" {{ ($filter['group'] !== null && $filter['group']->id == $group->id) ? 'selected' : '' }}>
            {{ $group->name }}
        </option>
        @endforeach
    </select>
</div>
@endif
@if ($categories->isNotEmpty())
<div class="form-group">
    <label for="category">{{ trans('icore::filter.filter') }} "{{ trans('icore::filter.category') }}"</label>
    <select class="form-control custom-select" id="category" name="filter[category]">
        <option value="">{{ trans('icore::filter.default') }}</option>
        @foreach ($categories as $cats)
            @if ($cats->real_depth == 0)
                <optgroup label="----------"></optgroup>
            @endif
        <option value="{{ $cats->id }}" {{ ($filter['category'] !== null && $filter['category']->id == $cats->id) ? 'selected' : '' }}>
            {{ str_repeat('-', $cats->real_depth) }} {{ $cats->name }}
        </option>
        @endforeach
    </select>
</div>
@endif
<div class="form-group">
    <label for="FormReport">{{ trans('icore::filter.filter') }} "{{ trans('icore::filter.report.label') }}"</label>
    <select class="form-control custom-select" id="FormReport" name="filter[report]">
        <option value="">{{ trans('icore::filter.default') }}</option>
        <option value="{{ $report::REPORTED }}" {{ ($filter['report'] === $report::REPORTED) ? 'selected' : '' }}>
            {{ trans('icore::filter.report.'.$report::REPORTED) }}
        </option>
        <option value="{{ $report::UNREPORTED }}" {{ ($filter['report'] === $report::UNREPORTED) ? 'selected' : '' }}>
            {{ trans('icore::filter.report.'.$report::UNREPORTED) }}
        </option>
    </select>
</div>
<div class="d-inline">
    <button type="button" class="btn btn-primary btn-send" id="filterFilter">
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
