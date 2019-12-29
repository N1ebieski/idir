<div class="form-group">
    <label for="field.{{ $field->id }}">
        <span>{{ $field->title }}:</span>
        @if ($field->desc !== null)
        <i data-toggle="tooltip" data-placement="top"
        title="{{ $field->desc }}" class="far fa-question-circle"></i>
        @endif
    </label>
    <div id="field.{{ $field->id }}">
        @foreach ($field->options->options as $option)
        <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input @isValid("field.{$field->id}")"
            id="field.{{ $field->id }}.{{ $loop->index }}" name="field[{{ $field->id }}][]"
            value="{{ $option }}" {{ in_array($option, old("field.{$field->id}", $value ?? null) ?? []) ? 'checked' : null }}>
            <label class="custom-control-label" for="field.{{ $field->id }}.{{ $loop->index }}">{{ $option }}</label>
        </div>
        @endforeach
    </div>
    @includeWhen($errors->has("field.{$field->id}"), 'icore::web.partials.errors', ['name' => "field.{$field->id}"])
</div>
