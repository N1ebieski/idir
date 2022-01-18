@inject('price', 'N1ebieski\IDir\Models\Price')

@component('icore::admin.partials.modal')

@slot('modal_id', 'filter-modal')

@slot('modal_title')
<i class="fas fa-filter"></i>
<span> {{ trans('icore::filter.filter_title') }}</span>
@endslot

@slot('modal_body')
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
    <label for="filter-type">
        {{ trans('icore::filter.filter') }} "{{ trans('icore::filter.type') }}"
    </label>
    <select 
        class="form-control custom-select" 
        id="filter-type" 
        name="filter[type]"
    >
        <option value="">
            {{ trans('icore::filter.default') }}
        </option>
        @foreach ($price::AVAILABLE as $type)
        <option 
            value="{{ $type }}" 
            {{ ($filter['type'] == $type) ? 'selected' : '' }}
        >
            {{ trans('idir::prices.payment.' . $type) }}
        </option>
        @endforeach
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
{!! JsValidator::formRequest('N1ebieski\IDir\Http\Requests\Admin\Price\IndexRequest', '#filter'); !!}
@endcomponent
@endpush
