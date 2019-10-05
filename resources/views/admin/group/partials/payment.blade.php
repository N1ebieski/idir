<div class="form-group">
    <label for="prices_{{ $type }}">{{ trans("idir::groups.payment.{$type}") }}:</label>
    <div id="prices_{{ $type }}">
        @foreach ($prices as $price)
        <div class="price">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="price_{{ $type.$loop->index }}"
                            name="prices[{{ $type }}][{{ $loop->index }}][select]" {{ ($price->price !== null) ? 'checked' : null }}>
                            <input type="hidden" name="prices[{{ $type }}][{{ $loop->index }}][id]"
                            value="{{ $price->id ?? null }}">
                            <input type="hidden" name="prices[{{ $type }}][{{ $loop->index }}][type]"
                            value="{{ $type }}">
                            <label class="custom-control-label" for="price_{{ $type.$loop->index }}"></label>
                        </div>
                    </div>
                </div>
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        {{ trans('idir::groups.price') }}
                    </div>
                </div>
                <input type="text" class="form-control @isValid("prices.{$type}.{$loop->index}.price")"
                name="prices[{{ $type }}][{{ $loop->index }}][price]" value="{{ $price->price ?? null }}">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        {{ trans('idir::groups.days') }}
                    </div>
                </div>
                <input type="text" class="form-control @isValid("prices.{$type}.{$loop->index}.days")"
                name="prices[{{ $type }}][{{ $loop->index }}][days]"
                placeholder="{{ trans('idir::groups.days_placeholder') }}" value="{{ $price->days ?? null }}">
                @includeWhen($errors->has("prices.{$type}.{$loop->index}.price"), 'icore::admin.partials.errors', ['name' => "prices.{$type}.{$loop->index}.price"])
                @includeWhen($errors->has("prices.{$type}.{$loop->index}.days"), 'icore::admin.partials.errors', ['name' => "prices.{$type}.{$loop->index}.days"])
            </div>
        </div>
        @endforeach
    </div>
</div>
