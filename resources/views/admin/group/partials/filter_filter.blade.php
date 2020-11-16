@inject('group', 'N1ebieski\IDir\Models\Group')

@component('icore::admin.partials.modal')
@slot('modal_id', 'filterModal')

@slot('modal_title')
<i class="fas fa-sort-amount-up"></i>
<span> {{ trans('icore::filter.filter_title') }}</span>
@endslot

@slot('modal_body')
<div class="form-group">
    <label for="FormSearch">
        {{ trans('icore::filter.search.label') }}
    </label>
    <input 
        type="text" 
        class="form-control" 
        id="FormSearch" 
        placeholder="{{ trans('icore::filter.search.placeholder') }}"
        name="filter[search]" 
        value="{{ isset($filter['search']) ? $filter['search'] : '' }}"
    >
</div>
<div class="form-group">
    <label for="FormVisible">
        {{ trans('icore::filter.filter') }} "{{ trans('idir::groups.visible.label') }}"
    </label>
    <select 
        class="form-control custom-select" 
        id="FormVisible" 
        name="filter[visible]"
    >
        <option value="">
            {{ trans('icore::filter.default') }}
        </option>
        <option 
            value="{{ $group::VISIBLE }}" 
            {{ ($filter['visible'] === $group::VISIBLE) ? 'selected' : '' }}
        >
            {{ trans('idir::groups.visible.'.$group::VISIBLE) }}
        </option>
        <option 
            value="{{ $group::INVISIBLE }}" 
            {{ ($filter['visible'] === $group::INVISIBLE) ? 'selected' : '' }}
        >
            {{ trans('idir::groups.visible.'.$group::INVISIBLE) }}
        </option>
    </select>
</div>
<div class="d-inline">
    <button type="button" class="btn btn-primary btn-send" id="filterFilter">
        <i class="fas fa-check"></i>
        {{ trans('icore::default.apply') }}
    </button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        <i class="fas fa-ban"></i>
        {{ trans('icore::default.cancel') }}
    </button>
</div>
@endslot
@endcomponent

@push('script')
@component('icore::admin.partials.jsvalidation')
{!! JsValidator::formRequest('N1ebieski\IDir\Http\Requests\Admin\Group\IndexRequest', '#filter'); !!}
@endcomponent
@endpush
