@inject('dir', 'N1ebieski\IDir\Models\Dir')

@component('icore::web.partials.modal')
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
<button type="button" class="btn btn-primary btn-send" id="filterFilter">
    <i class="fas fa-check"></i>
    {{ trans('icore::default.apply') }}
</button>
&nbsp;
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
