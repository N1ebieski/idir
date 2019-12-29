<div class="form-group">
    <label for="field.{{ $field->id }}">
        <span>{{ $field->title }}:</span>
        @if ($field->desc !== null)
        <i data-toggle="tooltip" data-placement="top"
        title="{{ $field->desc }}" class="far fa-question-circle"></i>
        @endif
    </label>
    <textarea class="form-control @isValid("field.{$field->id}")" id="field.{{ $field->id }}"
    name="field[{{ $field->id }}]" rows="3">{{ old("field.{$field->id}", $value ?? null) }}</textarea>
    @includeWhen($errors->has("field.{$field->id}"), 'icore::web.partials.errors', ['name' => "field.{$field->id}"])
</div>
