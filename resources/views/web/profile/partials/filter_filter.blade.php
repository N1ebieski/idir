@component('icore::web.partials.modal')
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
    <label for="FormStatus">{{ trans('icore::filter.filter') }} "{{ trans('icore::filter.status') }}"</label>
    <select class="form-control custom-select" id="FormVisible" name="filter[status]">
        <option value="">{{ trans('icore::filter.default') }}</option>
        <option value="1" {{ ($filter['status'] == '1') ? 'selected' : '' }}>{{ trans('idir::dirs.status.1') }}</option>
        <option value="0" {{ ($filter['status'] == '0') ? 'selected' : '' }}>{{ trans('idir::dirs.status.0') }}</option>
        <option value="2" {{ ($filter['status'] == '2') ? 'selected' : '' }}>{{ trans('idir::dirs.status.2') }}</option>
        <option value="3" {{ ($filter['status'] == '3') ? 'selected' : '' }}>{{ trans('idir::dirs.status.3') }}</option>
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
{!! JsValidator::formRequest(N1ebieski\IDir\Http\Requests\Web\Profile\EditDirRequest::class, '#filter'); !!}
@endcomponent
@endpush
