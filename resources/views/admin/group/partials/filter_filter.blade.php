@inject('group', 'N1ebieski\IDir\Models\Group')

@component('icore::admin.partials.modal')

@slot('modal_id', 'filter-modal')

@slot('modal_title')
<i class="fas fa-filter"></i>
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
    <label for="filter-visible">
        {{ trans('icore::filter.filter') }} "{{ trans('idir::groups.visible.label') }}"
    </label>
    <select 
        class="form-control custom-select" 
        id="filter-visible" 
        name="filter[visible]"
    >
        <option value="">
            {{ trans('icore::filter.default') }}
        </option>
        <option 
            value="{{ Group\Visible::ACTIVE }}" 
            {{ ($filter['visible'] === Group\Visible::ACTIVE) ? 'selected' : '' }}
        >
            {{ trans('idir::groups.visible.'.Group\Visible::ACTIVE) }}
        </option>
        <option 
            value="{{ Group\Visible::INACTIVE }}" 
            {{ ($filter['visible'] === Group\Visible::INACTIVE) ? 'selected' : '' }}
        >
            {{ trans('idir::groups.visible.'.Group\Visible::INACTIVE) }}
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
        {{ trans('icore::default.apply') }}
    </button>
    <button 
        type="button" 
        class="btn btn-secondary" 
        data-dismiss="modal"
    >
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
