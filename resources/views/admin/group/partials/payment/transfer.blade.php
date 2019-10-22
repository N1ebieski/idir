<div class="form-group">
    <label for="prices_transfer">{{ trans("idir::groups.payment.transfer") }}:</label>
    <div id="prices_transfer">
        @foreach ($prices as $price)
        <div class="price">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="price_transfer{{ $loop->index }}"
                            name="prices[transfer][{{ $loop->index }}][select]" {{ ($price->price !== null) ? 'checked' : null }}>
                            <input type="hidden" name="prices[transfer][{{ $loop->index }}][id]"
                            value="{{ $price->id ?? null }}">
                            <input type="hidden" name="prices[transfer][{{ $loop->index }}][type]"
                            value="transfer">
                            <label class="custom-control-label" for="price_transfer{{ $loop->index }}"></label>
                        </div>
                    </div>
                </div>
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        {{ trans('idir::groups.price') }}
                    </div>
                </div>
                <input type="text" class="form-control @isValid("prices.transfer.{$loop->index}.price")"
                name="prices[transfer][{{ $loop->index }}][price]" value="{{ $price->price ?? null }}">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        {{ trans('idir::groups.days') }}
                    </div>
                </div>
                <input type="text" class="form-control @isValid("prices.transfer.{$loop->index}.days")"
                name="prices[transfer][{{ $loop->index }}][days]" value="{{ $price->days ?? null }}">
                @includeWhen($errors->has("prices.transfer.{$loop->index}.price"), 'icore::admin.partials.errors', ['name' => "prices.transfer.{$loop->index}.price"])
                @includeWhen($errors->has("prices.transfer.{$loop->index}.days"), 'icore::admin.partials.errors', ['name' => "prices.transfer.{$loop->index}.days"])
            </div>
        </div>
        @endforeach
    </div>
</div>
