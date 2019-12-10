@extends(config('idir.layout') . '::web.layouts.layout', [
    'title' => [trans('idir::dirs.page.step', ['step' => 2]), trans('idir::dirs.page.create.form')],
    'desc' => [trans('idir::dirs.page.create.form')],
    'keys' => [trans('idir::dirs.page.create.form')]
])

@section('breadcrumb')
<li class="breadcrumb-item"><a href="/">{{ trans('icore::home.page.index') }}</a></li>
<li class="breadcrumb-item">{{ trans('idir::dirs.page.index') }}</li>
<li class="breadcrumb-item">{{ trans('idir::dirs.page.create.index') }}</li>
<li class="breadcrumb-item active" aria-current="page">
    {{ trans('idir::dirs.page.step', ['step' => 2]) }} {{ trans('idir::dirs.page.create.form') }}
</li>
@endsection

@section('content')
<div class="container">
    @include('icore::web.partials.alerts')
    <h3 class="h5 border-bottom pb-2">{{ trans('idir::dirs.page.create.form') }}</h3>
    <div class="row mb-4">
        <div class="col-md-8">
            <form method="post" action="{{ route('web.dir.store_2', [$group->id]) }}"
            enctype="multipart/form-data" id="createForm">
                @csrf
                <div class="form-group">
                    <label for="title">{{ trans('idir::dirs.title') }}:</label>
                    <input type="text" value="{{ old('title', session('dir.title')) }}" name="title"
                    id="title" class="form-control @isValid('title')">
                    @includeWhen($errors->has('title'), 'icore::admin.partials.errors', ['name' => 'title'])
                </div>
                <div class="form-group">
                    <label for="content_html{{ $trumbowyg }}">
                        {{ trans('idir::groups.content') }}:
                    </label>
                    <div class="@isTheme('dark', 'trumbowyg-dark')">
                        <textarea class="form-control @isValid('content')" id="content_html{{ $trumbowyg }}"
                        name="content_html" rows="5">{{ old('content_html', session('dir.content_html')) }}</textarea>
                    </div>
                    @includeWhen($errors->has('content'), 'icore::admin.partials.errors', ['name' => 'content'])
                </div>
                <div class="form-group">
                    <label for="notes">{{ trans('idir::dirs.notes') }}:</label>
                    <input type="text" value="{{ old('notes', session('dir.notes')) }}" name="notes"
                    id="notes" class="form-control @isValid('notes')">
                    @includeWhen($errors->has('notes'), 'icore::admin.partials.errors', ['name' => 'notes'])
                </div>
                <div class="form-group">
                    <label for="tags">
                        {{ trans('idir::dirs.tags') }}: <i data-toggle="tooltip" data-placement="top"
                        title="{{ trans('idir::dirs.tags_tooltip', ['max_tags' => $max_tags]) }}"
                        class="far fa-question-circle"></i>
                    </label>
                    <input name="tags" id="tags" class="form-control tagsinput @isValid('tags')"
                    value="{{ old('tags', session('dir.tags') !== null ? implode(',', session('dir.tags')) : null) }}"
                    placeholder="{{ trans('idir::dirs.tags_placeholder') }}" data-max="{{ $max_tags }}">
                    @includeWhen($errors->has('tags'), 'icore::admin.partials.errors', ['name' => 'tags'])
                </div>
                @if ($group->url > 0)
                <div class="form-group">
                    <label for="url">{{ trans('idir::dirs.url') }}:</label>
                    <input type="text" value="{{ old('url', session('dir.url')) }}" name="url"
                    id="url" class="form-control @isValid('url')" placeholder="https://">
                    @includeWhen($errors->has('url'), 'icore::admin.partials.errors', ['name' => 'url'])
                </div>
                @endif
                <div class="form-group">
                    <label for="category">
                        {{ trans('icore::categories.categories') }}: <i data-toggle="tooltip" data-placement="top"
                        title="{{ trans('icore::categories.categories_tooltip', ['max_categories' => $group->max_cats]) }}"
                        class="far fa-question-circle"></i>
                    </label>
                    <div id="category">
                        <div id="categoryOptions">
                            @include('icore::web.category.partials.search', ['categories'
                            => old('categories_collection', collect([])), 'checked' => true])
                        </div>
                        <div id="searchCategory"
                        {{ (old('categories_collection', collect([]))->count() >= $group->max_cats) ? 'style=display:none' : '' }}
                        data-route="{{ route('web.category.dir.search') }}" data-max="{{ $group->max_cats }}"
                        class="position-relative">
                            <div class="input-group">
                                <input type="text" class="form-control @isValid('category')"
                                placeholder="{{ trans('icore::categories.search_categories') }}">
                                <span class="input-group-append">
                                    <button class="btn btn-outline-secondary border border-left-0"
                                    type="button">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </span>
                            </div>
                            <div id="searchCategoryOptions" class="my-3"></div>
                        </div>
                    </div>
                    @includeWhen($errors->has('categories'), 'icore::web.partials.errors', ['name' => 'categories'])
                </div>
                @if ($group->fields->isNotEmpty())
                @foreach ($group->fields as $field)
                @include("idir::web.field.partials.{$field->type}", ['value' => session("dir.field.{$field->id}")])
                @endforeach
                @endif
                <hr>
                <div class="d-flex mb-3">
                    <div class="mr-auto">
                        <a href="{{ route('web.dir.create_1') }}" class="btn btn-secondary" style="width:6rem">
                            &laquo; {{ trans('icore::default.back') }}
                        </a>
                    </div>
                    <div class="ml-auto">
                        <button type="submit" class="btn btn-primary" style="width:6rem">
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

{{-- @push('script')
@component('icore::admin.partials.jsvalidation')
{!! str_replace('"content"', '"content_html"', JsValidator::formRequest(\N1ebieski\IDir\Http\Requests\Web\Dir\Store2Request::class, '#createForm')); !!}
@endcomponent
@endpush --}}
