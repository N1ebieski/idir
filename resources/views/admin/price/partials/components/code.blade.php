<div class="form-group">
    <label for="{{ $name }}.code">
        {{ trans("idir::prices.{$name}") }}:
    </label>
    <input 
        type="text" 
        value="{{ $value ?? null }}" 
        name="{{ $name }}[code]" 
        class="form-control" 
        id="{{ $name }}.code"
    >
</div>