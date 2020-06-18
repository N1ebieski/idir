<form data-route="{{ route('admin.dir.update', [$dir->id]) }}" id="editDir" data-id="{{ $dir->id }}">
    <div class="form-group">
        <label for="title">{{ trans('idir::dirs.title') }}:</label>
        <input type="text" value="{{ $dir->title }}" name="title"
        id="title" class="form-control">
    </div>
    <div class="form-group">
        <label for="content_html{{ $dir->group->hasEditorPrivilege() ? '_dir_trumbowyg' : null }}">
            {{ trans('idir::dirs.content') }}:
        </label>
        <div id="content" class="{{ $isTheme('dark', 'trumbowyg-dark') }}">
            <textarea class="form-control" 
            id="content_html{{ $dir->group->hasEditorPrivilege() ? '_dir_trumbowyg' : null }}"
            name="content_html" rows="5">{{ $dir->content_html }}</textarea>
        </div>
    </div>
    <div class="form-group">
        <label for="notes">{{ trans('idir::dirs.notes') }}:</label>
        <input type="text" value="{{ $dir->notes }}" name="notes"
        id="notes" class="form-control">
    </div>
    <div class="form-group">
        <label for="tags">
            {{ trans('idir::dirs.tags.label') }}: <i data-toggle="tooltip" data-placement="top"
            title="{{ trans('idir::dirs.tags.tooltip', ['max_tags' => $max_tags = config('idir.dir.max_tags')]) }}"
            class="far fa-question-circle"></i>
        </label>
        <input name="tags" id="tags" class="form-control tagsinput"
        value="{{ $dir->tagList }}"
        placeholder="{{ trans('idir::dirs.tags.placeholder') }}" data-max="{{ $max_tags }}">
    </div>
    @if ($dir->group->url > 0)
    <div class="form-group">
        <label for="url">{{ trans('idir::dirs.url') }}:</label>
        <input type="text" value="{{ $dir->url }}" name="url"
        id="url" class="form-control" placeholder="https://">
    </div>
    @endif
    <div class="form-group">
        <label for="category">
            {{ trans('icore::categories.categories.label') }}: <i data-toggle="tooltip" data-placement="top"
            title="{{ trans('icore::categories.categories.tooltip', ['max_categories' => $dir->group->max_cats]) }}"
            class="far fa-question-circle"></i>
        </label>
        <div id="category">
            <div id="categoryOptions">
                @include('icore::web.category.partials.search', [
                    'categories' => $dir->categories, 
                    'checked' => true
                ])
            </div>
            <div id="searchCategory"
            {{ ($dir->categories->count() >= $dir->group->max_cats) ? 'style=display:none' : '' }}
            data-route="{{ route('web.category.dir.search') }}" data-max="{{ $dir->group->max_cats }}"
            class="position-relative">
                <div class="input-group">
                    <input type="text" class="form-control" id="categories"
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
    </div>
    @if ($dir->group->fields->isNotEmpty())
    @foreach ($dir->group->fields as $field)
    @include("idir::admin.field.partials.{$field->type}", [
        'value' => optional($dir->fields->where('id', $field->id)->first())->decode_value
    ])
    @endforeach
    @endif
    <button type="button" class="btn btn-primary update">
        <i class="fas fa-check"></i>
        {{ trans('icore::default.save') }}
    </button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        <i class="fas fa-ban"></i>
        {{ trans('icore::default.cancel') }}
    </button>
</form>
