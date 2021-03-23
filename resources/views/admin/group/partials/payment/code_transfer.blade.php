<div class="form-group">
    <label for="prices_code_transfer">
        {{ trans("idir::groups.payment.code_transfer") }}:
    </label>
    <div id="prices_code_transfer">
        @foreach ($prices as $price)
        <div class="price">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <div class="custom-control custom-checkbox">
                            <input 
                                type="checkbox" 
                                class="custom-control-input" 
                                id="price_code_transfer{{ $loop->index }}"
                                name="prices[code_transfer][{{ $loop->index }}][select]" 
                                {{ ($price->price !== null) ? 'checked' : null }}
                            >
                            <input 
                                type="hidden" 
                                name="prices[code_transfer][{{ $loop->index }}][id]"
                                value="{{ $price->id ?? null }}"
                            >
                            <input 
                                type="hidden" 
                                name="prices[code_transfer][{{ $loop->index }}][type]"
                                value="code_transfer"
                            >
                            <label 
                                class="custom-control-label" 
                                for="price_code_transfer{{ $loop->index }}"
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
                    class="form-control {{ $isValid("prices.code_transfer.{$loop->index}.price") }}"
                    name="prices[code_transfer][{{ $loop->index }}][price]" 
                    value="{{ $price->price ?? null }}"
                >

                <div class="input-group-prepend">
                    <div class="input-group-text">
                        {{ trans('idir::groups.days') }}
                    </div>
                </div>
                <input 
                    type="text" 
                    class="form-control {{ $isValid("prices.code_transfer.{$loop->index}.days") }}"
                    name="prices[code_transfer][{{ $loop->index }}][days]" 
                    value="{{ $price->days ?? null }}"
                >
            </div>

            @includeWhen($errors->has("prices.code_transfer.{$loop->index}.price"), 'icore::admin.partials.errors', ['name' => "prices.code_transfer.{$loop->index}.price"])
            @includeWhen($errors->has("prices.code_transfer.{$loop->index}.days"), 'icore::admin.partials.errors', ['name' => "prices.code_transfer.{$loop->index}.days"])

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        {{ trans('idir::groups.code_transfer') }}
                    </div>
                </div>
                <input 
                    type="text" 
                    class="form-control {{ $isValid("prices.code_transfer.{$loop->index}.code") }}"
                    name="prices[code_transfer][{{ $loop->index }}][code]" 
                    value="{{ $price->code ?? null }}"
                >
            </div>

            @includeWhen($errors->has("prices.code_transfer.{$loop->index}.code"), 'icore::admin.partials.errors', ['name' => "prices.code_transfer.{$loop->index}.code"])

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <div>
                            {{ trans('idir::groups.codes') }}<br>
                            <div class="custom-control custom-checkbox">
                                <input 
                                    type="checkbox" 
                                    class="custom-control-input" 
                                    id="price_code_transfer_sync_codes{{ $loop->index }}"
                                    name="prices[code_transfer][{{ $loop->index }}][codes][sync]"
                                >
                                <label 
                                    class="custom-control-label" 
                                    for="price_code_transfer_sync_codes{{ $loop->index }}"
                                >
                                    <span>{{ trans('idir::groups.sync_codes') }} </span>
                                    <span class="badge badge-pill badge-primary">
                                        {{ $price->codes->count() }}
                                    </span>                                    
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <textarea 
                    class="form-control {{ $isValid("prices.code_transfer.{$loop->index}.codes") }}"
                    name="prices[code_transfer][{{ $loop->index }}][codes][codes]" 
                    data-autogrow="false"
                    readonly
                >{{ $price->codes_as_string }}</textarea>
            </div>

            @includeWhen($errors->has("prices.code_transfer.{$loop->index}.codes.codes"), 'icore::admin.partials.errors', ['name' => "prices.code_transfer.{$loop->index}.codes.codes"])
            <hr>
        </div>
        @endforeach
    </div>
</div>
