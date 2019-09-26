@extends(config('idir.layout') . '::admin.layouts.layout', [
    'title' => [trans('idir::groups.page.edit')],
    'desc' => [trans('idir::groups.page.edit')],
    'keys' => [trans('idir::groups.page.edit')]
])

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.home.index') }}">{{ trans('icore::home.page.index') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route("admin.group.{$group->poli}.index") }}">{{ trans('idir::groups.page.index') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ trans('idir::groups.page.edit') }}</li>
@endsection

@section('content')
<div class="w-100">
    <h1 class="h5 mb-4 border-bottom pb-2">
        <i class="fas fa-edit"></i>
        <span> {{ trans('idir::groups.page.edit') }}:</span>
    </h1>
    <form class="mb-3" method="post" action="{{ route("admin.group.update", [$group->id]) }}" id="editGroup">
        @csrf
        @method('put')
        <div class="row">
            <div class="col-lg-6 order-lg-last">
                @foreach ($privileges as $privilege)
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="hidden" name="priv[{{ $privilege->id }}]" value="">
                        <input type="checkbox" class="custom-control-input" id="priv{{ $privilege->id }}"
                        {{ old("priv.{$privilege->id}", optional($privilege->groups->first())->id) !== null ? 'checked' : '' }}
                        name="priv[{{ $privilege->id }}]" value="{{ $privilege->id }}">
                        <label class="custom-control-label" for="priv{{ $privilege->id }}">
                            {{ __($privilege->name) }}
                        </label>
                    </div>
                </div>
                @endforeach
                <div class="form-group">
                    <label for="max_cats">
                        <span>{{ trans('idir::groups.max_cats') }}: </span>
                        <i data-toggle="tooltip" data-placement="top" title="{{ trans('idir::groups.max_cats_tooltip') }}"
                        class="far fa-question-circle"></i>
                    </label>
                    <input type="text" value="{{ old('max_cats', $group->max_cats) }}" name="max_cats"
                    id="max_cats" class="form-control @isValid('max_cats')">
                    @includeWhen($errors->has('max_cats'), 'icore::admin.partials.errors', ['name' => 'max_cats'])
                </div>
                <div class="form-group">
                    <label for="url">{{ trans('idir::groups.url') }}:</label>
                    <select class="form-control @isValid('url')" id="url" name="url">
                        <option value="0" {{ old('url', $group->url) == "0" ? 'selected' : null }}>{{ trans('idir::groups.url_0') }}</option>
                        <option value="1" {{ old('url', $group->url) == "1" ? 'selected' : null }}>{{ trans('idir::groups.url_1') }}</option>
                        <option value="2" {{ old('url', $group->url) == "2" ? 'selected' : null }}>{{ trans('idir::groups.url_2') }}</option>
                    </select>
                    @includeWhen($errors->has('url'), 'icore::admin.partials.errors', ['name' => 'url'])
                </div>
                <div class="form-group">
                    <label for="days">{{ trans('idir::groups.days') }}:</label>
                    <input type="text" value="{{ old('days', $group->days) }}" name="days"
                    id="days" class="form-control @isValid('days')">
                    @includeWhen($errors->has('days'), 'icore::admin.partials.errors', ['name' => 'days'])
                </div>
            </div>
            <div class="col-lg-6 order-lg-first">
                <div class="form-group">
                    <label for="name">{{ trans('idir::groups.name') }}:</label>
                    <input type="text" value="{{ old('name', $group->name) }}" name="name"
                    id="name" class="form-control @isValid('name')">
                    @includeWhen($errors->has('name'), 'icore::admin.partials.errors', ['name' => 'name'])
                </div>
                <div class="form-group">
                    <label for="visible">{{ trans('idir::groups.visible') }}:</label>
                    <select class="form-control @isValid('visible')" id="visible" name="visible">
                        <option value="1" {{ old('visible', $group->visible) == "1" ? 'selected' : null }}>{{ trans('idir::groups.visible_1') }}</option>
                        <option value="0" {{ old('visible', $group->visible) == "0" ? 'selected' : null }}>{{ trans('idir::groups.visible_0') }}</option>
                    </select>
                    @includeWhen($errors->has('visible'), 'icore::admin.partials.errors', ['name' => 'visible'])
                </div>
                <div class="form-group">
                    <label for="icon">
                        {{ trans('idir::groups.border') }}: <i data-toggle="tooltip" data-placement="top"
                        title="{{ trans('idir::groups.border_tooltip') }}" class="far fa-question-circle"></i>
                    </label>
                    <input type="text" value="{{ old('border', $group->border) }}" name="border" id="border"
                    class="form-control @isValid('border')" placeholder="{{ trans('idir::groups.border_placeholder') }}">
                    @includeWhen($errors->has('border'), 'icore::admin.partials.errors', ['name' => 'border'])
                </div>
                <div class="form-group">
                    <label for="desc">{{ trans('idir::groups.desc') }}:</label>
                    <textarea class="form-control @isValid('desc')" id="desc" name="desc" rows="3">{{ old('desc', $group->desc) }}</textarea>
                    @includeWhen($errors->has('desc'), 'icore::admin.partials.errors', ['name' => 'desc'])
                </div>
                <div class="form-group">
                    <label for="max_dirs">{{ trans('idir::groups.max_dirs') }}:</label>
                    <input type="text" value="{{ old('max_dirs', $group->max_dirs) }}" name="max_dirs"
                    id="max_dirs" class="form-control @isValid('max_dirs')">
                    @includeWhen($errors->has('max_dirs'), 'icore::admin.partials.errors', ['name' => 'max_dirs'])
                </div>
                <div class="form-group_daily">
                    <label for="max_dirs_daily">{{ trans('idir::groups.max_dirs_daily') }}:</label>
                    <input type="text" value="{{ old('max_dirs_daily', $group->max_dirs_daily) }}" name="max_dirs_daily"
                    id="max_dirs" class="form-control @isValid('max_dirs_daily')">
                    @includeWhen($errors->has('max_dirs_daily'), 'icore::admin.partials.errors', ['name' => 'max_dirs_daily'])
                </div>
                <div class="form-group">
                    <label for="backlink">{{ trans('idir::groups.backlink') }}:</label>
                    <select class="form-control @isValid('backlink')" id="backlink" name="backlink">
                        <option value="0" {{ old('backlink', $group->backlink) == "0" ? 'selected' : null }}>{{ trans('idir::groups.backlink_0') }}</option>
                        <option value="1" {{ old('backlink', $group->backlink) == "1" ? 'selected' : null }}>{{ trans('idir::groups.backlink_1') }}</option>
                        <option value="2" {{ old('backlink', $group->backlink) == "2" ? 'selected' : null }}>{{ trans('idir::groups.backlink_2') }}</option>
                    </select>
                    @includeWhen($errors->has('backlink'), 'icore::admin.partials.errors', ['name' => 'backlink'])
                </div>
                <hr>
                <button type="submit" class="btn btn-primary">{{ trans('icore::default.save') }}</button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('script')
@component('icore::admin.partials.jsvalidation')
{!! JsValidator::formRequest('N1ebieski\IDir\Http\Requests\Admin\Group\UpdateRequest', '#editGroup'); !!}
@endcomponent
@endpush
