<div class="form-group">
    <label for="prices_code_sms">
        {{ trans("idir::groups.payment.code_sms") }}:
    </label>
    <div id="prices_code_sms">
        @foreach ($prices as $price)
        <div class="price">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <div class="custom-control custom-checkbox">
                            <input 
                                type="checkbox" 
                                class="custom-control-input" 
                                id="price_code_sms{{ $loop->index }}"
                                name="prices[code_sms][{{ $loop->index }}][select]" 
                                {{ ($price->price !== null) ? 'checked' : null }}
                            >
                            <input 
                                type="hidden" 
                                name="prices[code_sms][{{ $loop->index }}][id]"
                                value="{{ $price->id ?? null }}"
                            >
                            <input 
                                type="hidden" 
                                name="prices[code_sms][{{ $loop->index }}][type]"
                                value="code_sms"
                            >
                            <label 
                                class="custom-control-label" 
                                for="price_code_sms{{ $loop->index }}"
                            ></label>
                        </div>
                    </div>
                </div>

                <div class="input-group-prepend">
                    <div class="input-group-text">
                        {{ trans('idir::groups.price') }}
                    </div>
                </div>
                <input 
                    type="text" 
                    class="form-control {{ $isValid("prices.code_sms.{$loop->index}.price") }}"
                    name="prices[code_sms][{{ $loop->index }}][price]" 
                    value="{{ $price->price ?? null }}"
                >

                <div class="input-group-prepend">
                    <div class="input-group-text">
                        {{ trans('idir::groups.days') }}
                    </div>
                </div>
                <input 
                    type="text" 
                    class="form-control {{ $isValid("prices.code_sms.{$loop->index}.days") }}"
                    name="prices[code_sms][{{ $loop->index }}][days]" 
                    value="{{ $price->days ?? null }}"
                >
            </div>

            @includeWhen($errors->has("prices.code_sms.{$loop->index}.price"), 'icore::admin.partials.errors', ['name' => "prices.code_sms.{$loop->index}.price"])
            @includeWhen($errors->has("prices.code_sms.{$loop->index}.days"), 'icore::admin.partials.errors', ['name' => "prices.code_sms.{$loop->index}.days"])

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        {{ trans('idir::groups.code_sms') }}
                    </div>
                </div>
                <input 
                    type="text" 
                    class="form-control {{ $isValid("prices.code_sms.{$loop->index}.code") }}"
                    name="prices[code_sms][{{ $loop->index }}][code]" 
                    value="{{ $price->code ?? null }}"
                >

                <div class="input-group-prepend">
                    <div class="input-group-text">
                        {{ trans('idir::groups.number') }}
                    </div>
                </div>
                <input 
                    type="text" 
                    class="form-control {{ $isValid("prices.code_sms.{$loop->index}.number") }}"
                    name="prices[code_sms][{{ $loop->index }}][number]" 
                    value="{{ $price->number ?? null }}"
                >
            </div>

            @includeWhen($errors->has("prices.code_sms.{$loop->index}.code"), 'icore::admin.partials.errors', ['name' => "prices.code_sms.{$loop->index}.code"])
            @includeWhen($errors->has("prices.code_sms.{$loop->index}.number"), 'icore::admin.partials.errors', ['name' => "prices.code_sms.{$loop->index}.number"])

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        {{ trans('idir::groups.token') }}
                    </div>
                </div>
                <input 
                    type="text" 
                    class="form-control {{ $isValid("prices.code_sms.{$loop->index}.token") }}"
                    name="prices[code_sms][{{ $loop->index }}][token]" 
                    value="{{ $price->token ?? null }}"
                >
            </div>

            @includeWhen($errors->has("prices.code_sms.{$loop->index}.token"), 'icore::admin.partials.errors', ['name' => "prices.code_transfer.{$loop->index}.token"])

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <div>
                            {{ trans('idir::groups.codes') }}<br>
                            <div class="custom-control custom-checkbox">
                                <input 
                                    type="checkbox" 
                                    class="custom-control-input" 
                                    id="price_code_sms_sync_codes{{ $loop->index }}"
                                    name="prices[code_sms][{{ $loop->index }}][codes][sync]"
                                >
                                <label 
                                    class="custom-control-label" 
                                    for="price_code_sms_sync_codes{{ $loop->index }}"
                                >
                                    <span>{{ trans('idir::groups.sync_codes') }}</span>
                                    <span class="badge badge-pill badge-primary">
                                        {{ $price->codes->count() }}
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <textarea 
                    class="form-control {{ $isValid("prices.code_sms.{$loop->index}.codes") }}"
                    name="prices[code_sms][{{ $loop->index }}][codes][codes]"
                    data-autogrow="false" 
                    readonly
                >{{ $price->codes_as_string }}</textarea>
            </div>

            @includeWhen($errors->has("prices.code_sms.{$loop->index}.codes.codes"), 'icore::admin.partials.errors', ['name' => "prices.code_sms.{$loop->index}.codes.codes"])
            <hr>
        </div>
        @endforeach
    </div>
</div>
