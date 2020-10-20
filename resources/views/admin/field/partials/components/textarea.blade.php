<div class="form-group">
    <label for="{{ $name[0] }}.{{ $name[1] }}">
        <span>{{ trans("idir::fields.{$name[1]}.label") }}:</span>
        <i 
            data-toggle="tooltip" 
            data-placement="top"
            title="{{ trans("idir::fields.{$name[1]}.tooltip") }}" 
            class="far fa-question-circle"
        ></i>
    </label>
    <textarea 
        class="form-control" 
        id="{{ $name[0] }}.{{ $name[1] }}"
        name="{{ $name[0] }}[{{ $name[1] }}]" 
        rows="3"
    >{{ $value ?? null }}</textarea>
</div>
