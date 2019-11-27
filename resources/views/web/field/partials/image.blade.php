<div class="form-group">
    <label for="field.{{ $field->id }}">
        <span>{{ $field->title }}:</span>
        @if ($field->desc !== null)
        <i data-toggle="tooltip" data-placement="top"
        title="{{ $field->desc }}" class="far fa-question-circle"></i>
        @endif
    </label>
    <div class="custom-file" id="field.{{ $field->id }}">
        <input type="file" class="custom-file-input @isValid("field.{$field->id}")" id="img" name="field[{{ $field->id }}]">
        <label class="custom-file-label" for="img">{{ trans('icore::default.choose_file') }}</label>
    </div>
    @includeWhen($errors->has("field.{$field->id}"), 'icore::admin.partials.errors', ['name' => "field.{$field->id}"])
</div>
