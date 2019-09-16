@if ($errors->has($name))
  <span class="invalid-feedback d-block font-weight-bold" id="error-{{ $name }}">{{ $errors->first($name) }}</span>
@endif
