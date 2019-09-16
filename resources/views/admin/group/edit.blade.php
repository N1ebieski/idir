<form data-route="{{ route('admin.category.update', ['category' => $category->id]) }}"
data-id="{{ $category->id }}" id="update">
    <div class="form-group">
        <label for="name">{{ trans('icore::categories.name') }}</label>
        <input type="text" value="{{ $category->name }}" name="name"
        class="form-control" id="name">
    </div>
    <div class="form-group">
        <label for="icon">
            {{ trans('icore::categories.icon') }} <i data-toggle="tooltip" data-placement="top" title="{{ trans('icore::categories.icon_tooltip') }}"
            class="far fa-question-circle"></i>
        </label>
        <input type="text" value="{{ old('icon', $category->icon) }}" name="icon" id="icon"
        class="form-control @isValid('icon')" placeholder="{{ trans('icore::categories.icon_placeholder') }}">
    </div>
    @if ($categories->count() > 0)
    <div class="form-group">
        <label for="parent_id">{{ trans('icore::categories.parent_id') }}</label>
        <select class="form-control" id="parent_id" name="parent_id">
            <option value="null" {{ ($category->isRoot()) ? 'selected' : '' }}>{{ trans('icore::categories.null') }}</option>
            @foreach ($categories as $cats)
                @if ($cats->real_depth === 0)
                    <optgroup label="----------"></optgroup>
                @endif
                <option value="{{ $cats->id }}" {{ ($category->parent_id === $cats->id) ? 'selected' : '' }}>
                    {{ str_repeat('-', $cats->real_depth) }} {{ $cats->name }}
                </option>
            @endforeach
        </select>
    </div>
    @endif
    <button type="button" data-id="{{ $category->id }}" class="btn btn-primary update">
        <i class="fas fa-check"></i>
        {{ trans('icore::default.save') }}
    </button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        <i class="fas fa-ban"></i>
        {{ trans('icore::default.cancel') }}
    </button>
</form>
