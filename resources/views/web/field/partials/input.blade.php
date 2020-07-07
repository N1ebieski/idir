<div class="form-group">
    <label for="field.{{ $field->id }}" class="d-flex justify-content-between">
        <div>
            <span>{{ $field->title }}:</span>
            @if ($field->isRequired())
            <span>*</span>
            @endif        
            @if ($field->desc !== null)
            <i data-toggle="tooltip" data-placement="top"
            title="{{ $field->desc }}" class="far fa-question-circle"></i>
            @endif        
        </div>
        @include('icore::web.partials.counter', [
            'string' => old("field.{$field->id}", $value ?? null),
            'min' => $field->options->min,
            'max' => $field->options->max,
            'name' => "field[{$field->id}]"
        ])
    </label>
    <input type="text" value="{{ old("field.{$field->id}", $value ?? null) }}" name="field[{{ $field->id }}]"
    class="form-control {{ $isValid("field.{$field->id}") }}" id="field.{{ $field->id }}">
    @includeWhen($errors->has("field.{$field->id}"), 'icore::web.partials.errors', ['name' => "field.{$field->id}"])
</div>
