@extends(config('idir.layout') . '::admin.layouts.layout', [
    'title' => [trans('idir::groups.route.edit')],
    'desc' => [trans('idir::groups.route.edit')],
    'keys' => [trans('idir::groups.route.edit')]
])

@section('breadcrumb')
<li class="breadcrumb-item">
    <a 
        href="{{ route("admin.group.index") }}" 
        title="{{ trans('idir::groups.route.index') }}"
    >
        {{ trans('idir::groups.route.index') }}
    </a>
</li>
<li class="breadcrumb-item active" aria-current="page">
    {{ trans('idir::groups.route.edit') }}
</li>
@endsection

@section('content')
<div class="w-100">
    <h1 class="h5 mb-4 border-bottom pb-2">
        <i class="fas fa-edit"></i>
        <span>{{ trans('idir::groups.route.edit') }}:</span>
    </h1>
    <form 
        class="mb-3" 
        method="post" 
        action="{{ route("admin.group.update", [$group->id]) }}" 
        id="editGroup"
    >
        @csrf
        @method('put')
        <div class="row">
            <div class="col-lg-6 order-lg-last">
                @foreach ($privileges as $privilege)
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="hidden" name="priv[{{ $privilege->id }}]" value="">
                        <input 
                            type="checkbox" 
                            class="custom-control-input" 
                            id="priv{{ $privilege->id }}"
                            name="priv[{{ $privilege->id }}]" 
                            value="{{ $privilege->id }}"
                            {{ old("priv.{$privilege->id}", optional($privilege->groups->first())->id) !== null ? 'checked' : '' }}
                        >
                        <label class="custom-control-label" for="priv{{ $privilege->id }}">
                            {{ __($privilege->name) }}
                        </label>
                    </div>
                </div>
                @endforeach
                <div class="form-group">
                    <label for="max_cats">
                        <span>{{ trans('idir::groups.max_cats.label') }}: </span>
                        <i 
                            data-toggle="tooltip" 
                            data-placement="top" 
                            title="{{ trans('idir::groups.max_cats.tooltip') }}"
                            class="far fa-question-circle"
                        ></i>
                    </label>
                    <input 
                        type="text" 
                        value="{{ old('max_cats', $group->max_cats) }}" 
                        name="max_cats"
                        id="max_cats" 
                        class="form-control {{ $isValid('max_cats') }}"
                    >
                    @includeWhen($errors->has('max_cats'), 'icore::admin.partials.errors', ['name' => 'max_cats'])
                </div>
                <div class="form-group">
                    <label for="url">
                        {{ trans('idir::groups.url.label') }}:
                    </label>
                    <select 
                        class="form-control custom-select {{ $isValid('url') }}" 
                        id="url" 
                        name="url"
                    >
                        <option 
                            value="{{ Group\Url::INACTIVE }}" 
                            {{ old('url', $group->url->getValue()) == Group\Url::INACTIVE ? 'selected' : null }}
                        >
                            {{ trans('idir::groups.url.'.Group\Url::INACTIVE) }}
                        </option>
                        <option 
                            value="{{ Group\Url::OPTIONAL }}" 
                            {{ old('url', $group->url->getValue()) == Group\Url::OPTIONAL ? 'selected' : null }}
                        >
                            {{ trans('idir::groups.url.'.Group\Url::OPTIONAL) }}
                        </option>
                        <option 
                            value="{{ Group\Url::ACTIVE }}" 
                            {{ old('url', $group->url->getValue()) == Group\Url::ACTIVE ? 'selected' : null }}
                        >
                            {{ trans('idir::groups.url.'.Group\Url::ACTIVE) }}
                        </option>
                    </select>
                    @includeWhen($errors->has('url'), 'icore::admin.partials.errors', ['name' => 'url'])
                </div>
                <div class="form-group">
                    <label for="apply_status">
                        {{ trans('idir::groups.apply_status.label') }}:
                    </label>
                    <select 
                        class="form-control custom-select {{ $isValid('apply_status') }}" 
                        id="apply_status" 
                        name="apply_status"
                    >
                        <option 
                            value="{{ Group\ApplyStatus::INACTIVE }}" 
                            {{ old('apply_status', $group->apply_status->getValue()) == Group\ApplyStatus::INACTIVE ? 'selected' : null }}
                        >
                            {{ trans('idir::groups.apply_status.'.Group\ApplyStatus::INACTIVE) }}
                        </option>
                        <option 
                            value="{{ Group\ApplyStatus::ACTIVE }}" 
                            {{ old('apply_status', $group->apply_status->getValue()) == Group\ApplyStatus::ACTIVE ? 'selected' : null }}
                        >
                            {{ trans('idir::groups.apply_status.'.Group\ApplyStatus::ACTIVE) }}
                        </option>
                    </select>
                    @includeWhen($errors->has('apply_status'), 'icore::admin.partials.errors', ['name' => 'apply_status'])
                </div>
            </div>
            <div class="col-lg-6 order-lg-first">
                <div class="form-group">
                    <label for="name">
                        {{ trans('idir::groups.name') }}:
                    </label>
                    <input 
                        type="text" 
                        value="{{ old('name', $group->name) }}" 
                        name="name"
                        id="name" 
                        class="form-control {{ $isValid('name') }}"
                    >
                    @includeWhen($errors->has('name'), 'icore::admin.partials.errors', ['name' => 'name'])
                </div>
                <div class="form-group">
                    <label for="visible">
                        {{ trans('idir::groups.visible.label') }}:
                    </label>
                    <select 
                        class="form-control custom-select {{ $isValid('visible') }}" 
                        id="visible" 
                        name="visible"
                    >
                        <option 
                            value="{{ Group\Visible::ACTIVE }}" 
                            {{ old('visible', $group->visible->getValue()) == Group\Visible::ACTIVE ? 'selected' : null }}
                        >
                            {{ trans('idir::groups.visible.'.Group\Visible::ACTIVE) }}
                        </option>
                        <option 
                            value="{{ Group\Visible::INACTIVE }}" 
                            {{ old('visible', $group->visible->getValue()) == Group\Visible::INACTIVE ? 'selected' : null }}
                        >
                            {{ trans('idir::groups.visible.'.Group\Visible::INACTIVE) }}
                        </option>
                    </select>
                    @includeWhen($errors->has('visible'), 'icore::admin.partials.errors', ['name' => 'visible'])
                </div>
                <div class="form-group">
                    <label for="icon">
                        <span>{{ trans('idir::groups.border.label') }}:</span>
                        <i 
                            data-toggle="tooltip" 
                            data-placement="top"
                            title="{{ trans('idir::groups.border.tooltip') }}" 
                            class="far fa-question-circle"
                        ></i>
                    </label>
                    <input 
                        type="text" 
                        value="{{ old('border', $group->border) }}" 
                        name="border" 
                        id="border"
                        class="form-control {{ $isValid('border') }}" 
                        placeholder="{{ trans('idir::groups.border.placeholder') }}"
                    >
                    @includeWhen($errors->has('border'), 'icore::admin.partials.errors', ['name' => 'border'])
                </div>
                <div class="form-group">
                    <label for="desc">
                        {{ trans('idir::groups.desc') }}:
                    </label>
                    <textarea 
                        class="form-control {{ $isValid('desc') }}" 
                        id="desc" 
                        name="desc" 
                        rows="3"
                    >{{ old('desc', $group->desc) }}</textarea>
                    @includeWhen($errors->has('desc'), 'icore::admin.partials.errors', ['name' => 'desc'])
                </div>
                <div class="form-group">
                    <label for="max_models">
                        {{ trans('idir::groups.max_models') }}:
                    </label>
                    <input 
                        type="text" 
                        value="{{ old('max_models', $group->max_models) }}" 
                        name="max_models"
                        id="max_models" 
                        class="form-control {{ $isValid('max_models') }}"
                    >
                    @includeWhen($errors->has('max_models'), 'icore::admin.partials.errors', ['name' => 'max_models'])
                </div>
                <div class="form-group">
                    <label for="max_models_daily">
                        {{ trans('idir::groups.max_models_daily') }}:
                    </label>
                    <input 
                        type="text" 
                        value="{{ old('max_models_daily', $group->max_models_daily) }}" 
                        name="max_models_daily"
                        id="max_models_daily" 
                        class="form-control {{ $isValid('max_models_daily') }}"
                    >
                    @includeWhen($errors->has('max_models_daily'), 'icore::admin.partials.errors', ['name' => 'max_models_daily'])
                </div>
                <div class="form-group">
                    <label for="backlink">
                        {{ trans('idir::groups.backlink.label') }}:
                    </label>
                    <select 
                        class="form-control custom-select {{ $isValid('backlink') }}" 
                        id="backlink" 
                        name="backlink"
                    >
                        <option 
                            value="{{ Group\Backlink::INACTIVE }}" 
                            {{ old('backlink', $group->backlink->getValue()) == Group\Backlink::INACTIVE ? 'selected' : null }}
                        >
                            {{ trans('idir::groups.backlink.'.Group\Backlink::INACTIVE) }}
                        </option>
                        <option 
                            value="{{ Group\Backlink::OPTIONAL }}" 
                            {{ old('backlink', $group->backlink->getValue()) == Group\Backlink::OPTIONAL ? 'selected' : null }}
                        >
                            {{ trans('idir::groups.backlink.'.Group\Backlink::OPTIONAL) }}
                        </option>
                        <option 
                            value="{{ Group\Backlink::ACTIVE }}" 
                            {{ old('backlink', $group->backlink->getValue()) == Group\Backlink::ACTIVE ? 'selected' : null }}
                        >
                            {{ trans('idir::groups.backlink.'.Group\Backlink::ACTIVE) }}
                        </option>
                    </select>
                    @includeWhen($errors->has('backlink'), 'icore::admin.partials.errors', ['name' => 'backlink'])
                </div>
                <div class="form-group">
                    <label for="alt">
                        <span>{{ trans('idir::groups.alt.label') }}: </span>
                        <i 
                            data-toggle="tooltip" 
                            data-placement="top" 
                            title="{{ trans('idir::groups.alt.tooltip') }}"
                            class="far fa-question-circle"
                        ></i>
                    </label>
                    <select 
                        class="form-control custom-select {{ $isValid('alt_id') }}" 
                        id="alt" 
                        name="alt_id"
                    >
                        <option 
                            value="" {{ (old('alt_id', $group->alt_id) == null) ? 'selected' : null }}
                        >
                            {{ trans('idir::groups.alt.null') }}
                        </option>
                        @foreach ($groups as $_group)
                        <option 
                            value="{{ $_group->id }}" 
                            {{ (old('alt_id', $group->alt_id) == $_group->id) ? 'selected' : null }}
                        >
                            {{ $_group->name }}
                        </option>
                        @endforeach
                    </select>
                    @includeWhen($errors->has('alt_id'), 'icore::admin.partials.errors', ['name' => 'alt_id'])
                </div>                
                <hr>
                <button type="submit" class="btn btn-primary">
                    {{ trans('icore::default.save') }}
                </button>
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
