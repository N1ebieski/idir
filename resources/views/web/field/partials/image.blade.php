<div class="form-group">
    <label for="field.{{ $field->id }}">
        <span>{{ $field->title }}:</span>
        @if ($field->desc !== null)
        <i data-toggle="tooltip" data-placement="top"
        title="{{ $field->desc }}" class="far fa-question-circle"></i>
        @endif
    </label>
    <div class="custom-file" id="field.{{ $field->id }}">
        <input type="file" class="custom-file-input {{ $isValid("field.{$field->id}") }}" id="img" name="field[{{ $field->id }}]"
        {{ $value !== null && !old("delete_img.{$field->id}") ? 'disabled' : null }}>
        <label class="custom-file-label" for="img">{{ trans('icore::default.choose_file') }}</label>
    </div>
    @if ($value !== null)
    <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" id="delete_img.{{ $field->id }}"
        name="delete_img[{{ $field->id }}]" {{ old("delete_img.{$field->id}") ? 'checked' : null }}>
        <label class="custom-control-label" for="delete_img.{{ $field->id }}">{{ trans('icore::default.delete_img') }}</label>
        <input type="hidden" name="field[{{ $field->id }}]" value="{{ $value }}"
        {{ $value === null || old("delete_img.{$field->id}") ? 'disabled' : null }}>
    </div>
    @endif
    @includeWhen($errors->has("field.{$field->id}"), 'icore::web.partials.errors', ['name' => "field.{$field->id}"])
</div>
