<form 
    data-route="{{ route('admin.dir.update', [$dir->id]) }}" 
    id="editDir" 
    data-id="{{ $dir->id }}"
>
    <div class="form-group">
        <label for="title" class="d-flex justify-content-between">
            <div>
                {{ trans('idir::dirs.title') }}: *
            </div>
            @include('icore::admin.partials.counter', [
                'string' => $dir->title,
                'min' => 3,
                'max' => config('idir.dir.max_title'),
                'name' => 'title'
            ])
        </label>
        <input 
            type="text" 
            value="{{ $dir->title }}" 
            name="title"
            id="title" 
            class="form-control"
        >
    </div>
    <div class="form-group">
        <label 
            class="d-flex justify-content-between" 
            for="content_html{{ $dir->group->hasEditorPrivilege() ? '_dir_trumbowyg' : null }}"
        >
            <div>
                {{ trans('idir::dirs.content') }}: *
            </div>
            @include('icore::admin.partials.counter', [
                'string' => $dir->content_html,
                'min' => config('idir.dir.min_content'),
                'max' => config('idir.dir.max_content'),
                'name' => 'content_html'
            ])
        </label>
        <div id="content" class="{{ $isTheme('dark', 'trumbowyg-dark') }}">
            <textarea 
                class="form-control" 
                data-lang="{{ config('app.locale') }}"
                id="content_html{{ $dir->group->hasEditorPrivilege() ? '_dir_trumbowyg' : null }}"
                name="content_html" 
                rows="5"
            >{{ $dir->content_html }}</textarea>
        </div>
    </div>
    <div class="form-group">
        <label for="notes">
            {{ trans('idir::dirs.notes') }}:
        </label>
        <input 
            type="text" 
            value="{{ $dir->notes }}" 
            name="notes"
            id="notes" 
            class="form-control"
        >
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
            class="form-control tagsinput"
            value="{{ $dir->tagList }}"
            placeholder="{{ trans('idir::dirs.tags.placeholder') }}" 
            data-max="{{ config('idir.dir.max_tags') }}"
            data-max-chars="{{ config('icore.tag.max_chars') }}"
        >
    </div>
    @if ($dir->group->url > 0)
    <div class="form-group">
        <label for="url">
            <span>{{ trans('idir::dirs.url') }}:</span>
            @if ($dir->group->url === $dir->group::OBLIGATORY_URL)
            <span>*</span>
            @endif
        </label>
        <input 
            type="text" 
            value="{{ $dir->url }}" 
            name="url"
            id="url" 
            class="form-control" 
            placeholder="https://"
        >
    </div>
    @endif
    <div class="form-group">
        <label for="category">
            <span>{{ trans('icore::categories.categories.label') }}: *</span>
            <i 
                data-toggle="tooltip" 
                data-placement="top"
                title="{{ trans('icore::categories.categories.tooltip', ['max_categories' => $dir->group->max_cats]) }}"
                class="far fa-question-circle"
            ></i>
        </label>
        <select 
            class="selectpicker select-picker-category" 
            data-live-search="true"
            data-abs="true"
            data-abs-max-options-length="10"
            data-abs-text-attr="name"
            data-abs-ajax-url="{{ route('api.category.dir.index') }}"
            data-style="border"
            data-width="100%"
            data-max-options="{{ $dir->group->max_cats }}"
            multiple
            name="categories[]"
            id="categories"
        >
            @if ($dir->categories->isNotEmpty())
            <optgroup label="{{ trans('icore::default.current_option') }}">
                @foreach ($dir->categories as $category)
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
