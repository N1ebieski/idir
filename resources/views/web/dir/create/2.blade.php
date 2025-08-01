@extends(config('idir.layout') . '::web.layouts.layout', [
    'title' => [
        trans('idir::dirs.route.step', ['step' => 2]), 
        trans('idir::dirs.route.create.2')
    ],
    'desc' => [trans('idir::dirs.route.create.2')],
    'keys' => [trans('idir::dirs.route.create.2')]
])

@section('breadcrumb')
<li class="breadcrumb-item">
    <a 
        href="{{ route('web.dir.index') }}" 
        title="{{ trans('idir::dirs.route.index') }}"
    >
        {{ trans('idir::dirs.route.index') }}
    </a>
</li>
<li class="breadcrumb-item">
    {{ trans('idir::dirs.route.create.index') }}
</li>
<li class="breadcrumb-item active" aria-current="page">
    {{ trans('idir::dirs.route.step', ['step' => 2]) }} {{ trans('idir::dirs.route.create.2') }}
</li>
@endsection

@section('content')
<div class="container">
    @include('icore::web.partials.alerts')
    <h3 class="h5 border-bottom pb-2">
        {{ trans('idir::dirs.route.create.2') }}
    </h3>
    <div class="row mb-4">
        <div class="col-md-8">
            <form 
                method="post" 
                action="{{ route('web.dir.store_2', [$group->id]) }}"
                enctype="multipart/form-data" 
                id="create-dir-2"
            >
                @csrf
                <div class="form-group">
                    <label for="title" class="d-flex justify-content-between">
                        <div>{{ trans('idir::dirs.title') }}: *</div>
                        @include('icore::web.partials.counter', [
                            'string' => old('title', session('dir.title')),
                            'min' => 3,
                            'max' => config('idir.dir.max_title'),
                            'name' => 'title'
                        ])
                    </label>
                    <input 
                        type="text" 
                        value="{{ old('title', session('dir.title')) }}" 
                        name="title"
                        id="title" 
                        class="form-control {{ $isValid('title') }}"
                    >
                    @includeWhen($errors->has('title'), 'icore::web.partials.errors', ['name' => 'title'])
                </div>
                @if (!$group->url->isInactive())
                <div class="form-group">
                    <label for="url">
                        <span>{{ trans('idir::dirs.url') }}:</span>
                        @if ($group->url->isActive())
                        <span>*</span>
                        @endif
                    </label>
                    @if ($group->hasGenerateContentPrivilege())
                    <div 
                        data-route="{{ route('web.dir.generate_content', [$group->id]) }}"
                        class="search position-relative"
                        id="generate-content" 
                    >
                        <div class="input-group">
                    @endif
                            <input 
                                type="text" 
                                value="{{ old('url', session('dir.url')) }}" 
                                name="url"
                                id="url" 
                                class="form-control {{ $isValid('url') }}" 
                                placeholder="https://"
                            >
                    @if ($group->hasGenerateContentPrivilege())
                            <span class="input-group-append">
                                <button 
                                    class="btn btn-outline-secondary border border-left-0"
                                    type="button"
                                >
                                    <i class="fas fa-pencil-alt"></i>
                                    <span class="d-none d-md-inline">{{ trans('idir::dirs.generate_content') }}</span>
                                </button>
                            </span> 
                        </div>
                    </div>
                    @endif
                    @includeWhen($errors->has('url'), 'icore::web.partials.errors', ['name' => 'url'])
                </div>
                @endif                
                <div class="form-group">
                    <label 
                        class="d-flex justify-content-between" 
                        for="content_html{{ $group->hasEditorPrivilege() ? '_dir_trumbowyg' : null }}"
                    >
                        <div>{{ trans('idir::dirs.content') }}: *</div>
                        @include('icore::web.partials.counter', [
                            'string' => $oldContentHtml,
                            'min' => config('idir.dir.min_content'),
                            'max' => config('idir.dir.max_content'),
                            'name' => 'content_html'
                        ])
                    </label>
                    <div class="{{ $isTheme('dark', 'trumbowyg-dark') }}">
                        <textarea 
                            class="form-control {{ $isValid('content') }}" 
                            data-lang="{{ config('app.locale') }}"
                            id="content_html{{ $group->hasEditorPrivilege() ? '_dir_trumbowyg' : null }}"
                            name="content_html" 
                            rows="5"
                        >{{ $oldContentHtml }}</textarea>
                    </div>
                    @includeWhen($errors->has('content'), 'icore::web.partials.errors', ['name' => 'content'])
                </div>
                <div class="form-group">
                    <label for="notes">
                        {{ trans('idir::dirs.notes') }}:
                    </label>
                    <input 
                        type="text" 
                        value="{{ old('notes', session('dir.notes')) }}" 
                        name="notes"
                        id="notes" 
                        class="form-control {{ $isValid('notes') }}"
                    >
                    @includeWhen($errors->has('notes'), 'icore::web.partials.errors', ['name' => 'notes'])
                </div>
                <div class="form-group">
                    <label for="tags">
                        <span>{{ trans('idir::dirs.tags.label') }}:</span>
                        <i 
                            data-toggle="tooltip" 
                            data-placement="top"
                            title="{{ trans('idir::dirs.tags.tooltip', ['max_tags' => config('idir.dir.max_tags'), 'max_chars' => config('icore.tag.max_chars')]) }}"
                            class="far fa-question-circle"
                        ></i>
                    </label>
                    <input 
                        name="tags" 
                        id="tags" 
                        class="form-control tagsinput {{ $isValid('tags') }}"
                        value="{{ old('tags', session('dir.tags') !== null ? implode(',', session('dir.tags')) : null) }}"
                        placeholder="{{ trans('idir::dirs.tags.placeholder') }}" 
                        data-max="{{ config('idir.dir.max_tags') }}"
                        data-max-chars="{{ config('icore.tag.max_chars') }}"
                    >
                    @includeWhen($errors->has('tags'), 'icore::web.partials.errors', ['name' => 'tags'])
                </div>
                <div class="form-group">
                    <label for="category">
                        <span>{{ trans('icore::categories.categories.label') }}: *</span>
                        <i 
                            data-toggle="tooltip" 
                            data-placement="top"
                            title="{{ trans('icore::categories.categories.tooltip', ['max_categories' => $group->max_cats]) }}"
                            class="far fa-question-circle"
                        ></i>
                    </label>
                    <input type="hidden" name="categories" value="">
                    <select 
                        class="selectpicker select-picker-category" 
                        data-live-search="true"
                        data-abs="true"
                        data-abs-max-options-length="10"
                        data-abs-text-attr="name"
                        data-abs-ajax-url="{{ route('api.category.dir.index') }}"
                        data-optgroup-label="{{ trans('icore::default.current_option') }}"
                        data-style="border"
                        data-width="100%"
                        data-max-options="{{ $group->max_cats }}"
                        multiple
                        name="categories[]"
                        id="categories"
                    >
                        @if (collect($categoriesSelection)->isNotEmpty())
                        <optgroup label="{{ trans('icore::default.current_option') }}">
                            @foreach ($categoriesSelection as $category)
                            <option
                                @if ($category->ancestors->isNotEmpty())
                                data-content='<small class="p-0 m-0">{{ implode(' &raquo; ', $category->ancestors->pluck('name')->toArray()) }} &raquo; </small>{{ $category->name }}'
                                @endif
                                value="{{ $category->id }}"
                                selected
                            >
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </optgroup>
                        @endif
                    </select>
                    @includeWhen($errors->has('categories'), 'icore::admin.partials.errors', ['name' => 'categories'])
                </div>
                @if ($group->fields->isNotEmpty())
                    @foreach ($group->fields as $field)
                        @include("idir::web.field.partials.{$field->type}", [
                            'value' => session("dir.field.{$field->id}")
                        ])
                    @endforeach
                @endif
                <hr>
                <div class="d-flex mb-3">
                    <div class="mr-auto">
                        <a 
                            href="{{ route('web.dir.create_1') }}" 
                            class="btn btn-secondary" 
                            style="width:6rem"
                        >
                            &laquo; {{ trans('icore::default.back') }}
                        </a>
                    </div>
                    <div class="ml-auto">
                        <button 
                            type="submit" 
                            class="btn btn-primary" 
                            style="width:6rem"
                        >
                            {{ trans('icore::default.next') }} &raquo;
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                @include('idir::web.dir.partials.group')
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
@component('icore::web.partials.jsvalidation')
{!! str_replace('"content"', '"content_html"', JsValidator::formRequest(\N1ebieski\IDir\Http\Requests\Web\Dir\Store2Request::class, '#create-dir-2')); !!}
@endcomponent
@endpush
