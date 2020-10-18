@php
$value = old("field.{$field->id}", $value ?? null);
@endphp

<div class="form-group">
    <label for="field.{{ $field->id }}">
        <span>{{ $field->title }}:</span>
        @if ($field->isRequired())
        <span>*</span>
        @endif        
        @if ($field->desc !== null)
        <i 
            data-toggle="tooltip" 
            data-placement="top"
            title="{{ $field->desc }}" 
            class="far fa-question-circle"
        ></i>
        @endif
        <span>
            <a 
                id="remove-marker" 
                href="#" 
                class="badge badge-primary"
                {{ !isset($value) || count($value) === 0 ? 'style=display:none' : null }}
            >             
                {{ trans('idir::fields.remove_marker') }}
            </a>
        </span>        
        <span>
            <a 
                id="add-marker" 
                href="#" 
                class="badge badge-primary"
                {{ isset($value) && count($value) > 0 ? 'style=display:none' : null }}
            >            
                {{ trans('idir::fields.add_marker') }}
            </a>
        </span>       
    </label>
    @render('idir::map.dir.mapComponent', [
        'selector' => 'map-select',
        'zoom' => 8
    ])
    <div id="marker0">
        <div>
            <input 
                type="hidden" 
                id="field.{{ $field->id }}.0.lat" 
                name="field[{{ $field->id }}][0][lat]" 
                value="{{ $value[0]->lat ?? ($value[0]['lat'] ?? null) }}"
            >
            @includeWhen($errors->has("field.{$field->id}.0.lat"), 'icore::web.partials.errors', ['name' => "field.{$field->id}.0.lat"])
            <input 
                type="hidden" 
                id="field.{{ $field->id }}.0.long" 
                name="field[{{ $field->id }}][0][long]" 
                value="{{ $value[0]->long ?? ($value[0]['long'] ?? null) }}"
            >   
            @includeWhen($errors->has("field.{$field->id}.0.long"), 'icore::web.partials.errors', ['name' => "field.{$field->id}.0.long"]) 
        </div>
    </div>
</div>
