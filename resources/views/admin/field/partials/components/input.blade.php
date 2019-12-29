<div class="form-group">
    <label for="{{ $name[0] }}.{{ $name[1] }}">{{ trans("idir::fields.{$name[1]}") }}</label>
    <input type="text" value="{{ $value ?? null }}" name="{{ $name[0] }}[{{ $name[1] }}]"
    class="form-control" id="{{ $name[0] }}.{{ $name[1] }}">
</div>
