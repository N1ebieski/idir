@extends(config('idir.layout') . '::admin.field.index')

@section('filter-morph')
@if ($groups->isNotEmpty())
<div class="form-group">
    <label for="morph">{{ trans('icore::filter.filter') }} "{{ trans('idir::filter.group') }}"</label>
    <select class="form-control custom-select" id="morph" name="filter[morph]">
        <option value="">{{ trans('icore::filter.default') }}</option>
        @foreach ($groups as $group)
        <option value="{{ $group->id }}" {{ ($filter['morph'] !== null && $filter['morph']->id == $group->id) ? 'selected' : '' }}>
            {{ $group->name }}
        </option>
        @endforeach
    </select>
</div>
@endif
@endsection

@section('filter-morph-option')
@if ($filter['morph'] !== null)
<a href="#" class="badge badge-primary filterOption" data-name="filter[morph]">
    {{ trans('idir::filter.group') }}: {{ $filter['morph']->name }}
    <span aria-hidden="true">&times;</span>
</a>&nbsp;
@endif
@endsection
