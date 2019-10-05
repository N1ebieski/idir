@component('icore::admin.partials.modal')
@slot('modal_id', 'filterModal')

@slot('modal_title')
<i class="fas fa-sort-amount-up"></i> {{ trans('icore::filter.filter_title') }}
@endslot

@slot('modal_body')
<div class="form-group">
    <label for="FormSearch">{{ trans('icore::filter.search') }}</label>
    <input type="text" class="form-control" id="FormSearch" placeholder="{{ trans('icore::filter.search_placeholder') }}"
    name="filter[search]" value="{{ isset($filter['search']) ? $filter['search'] : '' }}">
</div>
<div class="form-group">
    <label for="FormVisible">{{ trans('icore::filter.filter') }} "{{ trans('idir::groups.visible') }}"</label>
    <select class="form-control custom-select" id="FormVisible" name="filter[visible]">
        <option value="">{{ trans('icore::filter.default') }}</option>
        <option value="1" {{ ($filter['visible'] == '1') ? 'selected' : '' }}>{{ trans('idir::groups.visible_1') }}</option>
        <option value="0" {{ ($filter['visible'] == '0') ? 'selected' : '' }}>{{ trans('idir::groups.visible_0') }}</option>
    </select>
</div>
<button type="button" class="btn btn-primary btn-send" id="filterFilter">
    <i class="fas fa-check"></i>
    {{ trans('icore::default.apply') }}
</button>
<button type="button" class="btn btn-secondary" data-dismiss="modal">
    <i class="fas fa-ban"></i>
    {{ trans('icore::default.cancel') }}
</button>
@endslot
@endcomponent

@push('script')
@component('icore::admin.partials.jsvalidation')
{!! JsValidator::formRequest('N1ebieski\IDir\Http\Requests\Admin\Group\IndexRequest', '#filter'); !!}
@endcomponent
@endpush
