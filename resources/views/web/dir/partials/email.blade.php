<div class="form-group">
    <label for="email">
        <span>{{ trans('idir::dirs.email.label') }}: *</span>
        <i 
            data-toggle="tooltip" 
            data-placement="top"
            title="{{ trans('idir::dirs.email.tooltip') }}"
            class="far fa-question-circle"
        ></i>
    </label>
    <input 
        type="text" 
        value="{{ old('email', session('dir.email')) }}" 
        name="email"
        id="email" 
        class="form-control {{ $isValid('email') }}"
    >
    @includeWhen($errors->has('email'), 'icore::web.partials.errors', ['name' => 'email'])
</div>