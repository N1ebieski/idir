<div class="form-group">
    <label for="field.{{ $field->id }}">
        <span>{{ $field->title }}:</span>
        @if ($field->isRequired())
        <span>*</span>
        @endif       
        @if ($field->desc !== null)
        <i data-toggle="tooltip" data-placement="top"
        title="{{ $field->desc }}" class="far fa-question-circle"></i>
        @endif
    </label>
    <select multiple class="form-control {{ $isValid("field.{$field->id}") }}" id="field.{{ $field->id }}"
    name="field[{{ $field->id }}][]">
        @foreach ($regions as $region)
        <option value="{{ $region->id }}"
        {{ in_array($region->id, old("field.{$field->id}", $value ?? null) ?? []) ? 'selected' : null }}>
            {{ $region->name }}
        </option>
        @endforeach
    </select>
    @includeWhen($errors->has("field.{$field->id}"), 'icore::web.partials.errors', ['name' => "field.{$field->id}"])
</div>