<div class="form-group">
    <label for="{{ $name }}.codes.codes">
        {{ trans('idir::prices.codes') }}:
    </label>
    <div class="input-group mb-3">
        <div class="input-group-prepend">
            <div class="input-group-text">
                <div class="custom-control custom-checkbox">
                    <input 
                        type="checkbox" 
                        class="custom-control-input" 
                        id="{{ $name }}.codes.sync"
                        name="{{ $name }}[codes][sync]"
                    >
                    <label 
                        class="custom-control-label" 
                        for="{{ $name }}.codes.sync"
                    >
                        <span>{{ trans('idir::prices.sync_codes') }}</span>
                        @if (isset($count))
                        <span class="badge badge-pill badge-primary">
                            {{ $count }}
                        </span>
                        @endif
                    </label>
                </div>
            </div>
        </div>
        <textarea 
            class="form-control"
            id="{{ $name }}.codes.codes"
            name="{{ $name }}[codes][codes]" 
            data-autogrow="false"
            readonly
        >{{ $value ?? null }}</textarea>
    </div>
</div>