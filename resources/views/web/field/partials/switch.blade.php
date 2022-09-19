<div class="form-group">
    <input type="hidden" name="field[{{ $field->id }}]" value="0">
    <div class="custom-control custom-switch">
        <input 
            type="checkbox" 
            class="custom-control-input {{ $isValid("field.{$field->id}") }}"
            id="field.{{ $field->id }}" 
            name="field[{{ $field->id }}]"
            value="1" 
            {{ old("field.{$field->id}", $value ?? null) == 1 ? 'checked' : null }}
        >
        <label 
            class="custom-control-label" 
            for="field.{{ $field->id }}"
        >
            @if ($field->options->required->isActive())
            <span>*</span>
            @endif            
            <span>{{ $field->title }}</span>
            @if ($field->desc !== null)
            <i 
                data-toggle="tooltip" 
                data-placement="top"
                title="{{ $field->desc }}" 
                class="far fa-question-circle"
            ></i>
            @endif
        </label>
    </div>
    @includeWhen($errors->has("field.{$field->id}"), 'icore::admin.partials.errors', ['name' => "field.{$field->id}"])
</div>
