<div class="form-group">
    <label for="payment">
        {{ trans('idir::dirs.choose_payment_type') }}:
    </label>
    <div id="payment">
        <nav>
            <div 
                class="btn-group btn-group-toggle nav d-block" 
                data-toggle="buttons" 
                id="nav-tab" 
                role="tablist"
            >
                @foreach (['transfer', 'code_transfer', 'code_sms'] as $type)
                @if ($pricesByType($type)->isNotEmpty())
                <a 
                    class="nav-item btn btn-info {{ old('payment_type', $paymentType) === $type ? 'active' : null }}"
                    id="nav-{{ $type }}-tab" 
                    data-toggle="tab" 
                    href="#nav-{{ $type }}" 
                    role="tab"
                    aria-controls="nav-{{ $type }}" 
                    aria-selected="true"
                >
                    <input 
                        type="radio" 
                        name="payment_type" 
                        value="{{ $type }}" 
                        id="nav-{{ $type }}-tab"
                        autocomplete="off" 
                        {{ old('payment_type', $paymentType) === $type ? 'checked' : null }}
                    >
                    {{ trans("idir::dirs.payment.{$type}.label") }}
                </a>
                @endif
                @endforeach
            </div>
        </nav>
        <div class="tab-content mt-3" id="nav-tabContent">
            @if ($pricesByType('transfer')->isNotEmpty())
            <div 
                class="tab-pane fade {{ old('payment_type', $paymentType) === "transfer" ? 'show active' : null }}"
                id="nav-transfer" 
                role="tabpanel" 
                aria-labelledby="nav-transfer-tab"
            >
                <div class="form-group">
                    <label for="payment_transfer" class="sr-only"> 
                        {{ trans('idir::dirs.payment_transfer') }}
                    </label>
                    <select 
                        class="form-control {{ $isValid('payment_transfer') }}" 
                        id="payment_transfer" 
                        name="payment_transfer"
                    >
                        @foreach ($pricesByType('transfer')->sortBy('price') as $price)
                        <option 
                            value="{{ $price->id }}" 
                            {{ old('payment_transfer') == $price->id ? 'selected' : null }}
                        >
                            {{ trans('idir::dirs.price', [
                                'price' => $price->price,
                                'currency' => config("services.{$driverByType('transfer')}.transfer.currency"),
                                'days' => $days = $price->days,
                                'limit' => $days !== null ? 
                                    mb_strtolower(trans('idir::prices.days')) 
                                    : mb_strtolower(trans('idir::prices.unlimited'))
                            ]) }}
                        </option>
                        @endforeach
                    </select>
                    @includeWhen($errors->has('payment_transfer'), 'icore::web.partials.errors', ['name' => 'payment_transfer'])
                </div>
                <p>
                    {!! trans('idir::dirs.payment.transfer.info', [
                        'provider_url' => config("idir.payment.{$driverByType('transfer')}.url"),
                        'provider_name' => config("idir.payment.{$driverByType('transfer')}.name"),
                        'provider_docs_url' => config("idir.payment.{$driverByType('transfer')}.docs_url"),
                        'provider_rules_url' => config("idir.payment.{$driverByType('transfer')}.rules_url"),
                        'rules_url' => route('web.page.show', [str_slug(trans('idir::dirs.rules'))])
                    ]) !!}
                </p>
            </div>
            @endif
            @if ($pricesByType('code_transfer')->isNotEmpty())
            <div 
                class="tab-pane fade {{ old('payment_type', $paymentType) === "code_transfer" ? 'show active' : null }}"
                id="nav-code_transfer" 
                role="tabpanel" 
                aria-labelledby="nav-code_transfer-tab"
            >
                <div class="form-group">
                    <label for="payment_code_transfer" class="sr-only"> 
                        {{ trans('idir::dirs.payment_code_transfer') }}
                    </label>
                    <select 
                        class="form-control {{ $isValid('payment_code_transfer') }}" 
                        id="payment_code_transfer" 
                        name="payment_code_transfer"
                    >
                        @foreach ($pricesByType('code_transfer')->sortBy('price') as $price)
                        <option 
                            value="{{ $price->id }}" {{ old('payment_code_transfer') == $price->id ? 'selected' : null }}
                            data="{{ json_encode($price->only(['code', 'price'])) }}"
                        >
                            {{ trans('idir::dirs.price', [
                                'price' => $price->price,
                                'currency' => config("services.{$driverByType('code_transfer')}.code_transfer.currency"),
                                'days' => $days = $price->days,
                                'limit' => $days !== null ? 
                                    mb_strtolower(trans('idir::prices.days')) 
                                    : mb_strtolower(trans('idir::prices.unlimited'))
                            ]) }}
                        </option>
                        @endforeach
                    </select>
                    @includeWhen($errors->has('payment_code_transfer'), 'icore::web.partials.errors', ['name' => 'payment_code_transfer'])
                </div>
                <div class="form-group">
                    <label for="code_transfer">
                        {{ trans('idir::dirs.code') }}: *
                    </label>
                    <input 
                        type="text" 
                        value="" 
                        name="code_transfer" 
                        id="code_transfer" 
                        class="form-control {{ $isValid('code_transfer') }}"
                    >
                    @includeWhen($errors->has('code_transfer'), 'icore::web.partials.errors', ['name' => 'code_transfer'])
                </div>               
                <p>
                    {!! trans('idir::dirs.payment.code_transfer.info', [
                        'code_transfer_url' => config("services.{$driverByType('code_transfer')}.code_transfer.url") . $paymentCodeTransferSelection->code,
                        'price' => $paymentCodeTransferSelection->price,
                        'provider_url' => config("idir.payment.{$driverByType('code_transfer')}.url"),
                        'provider_name' => config("idir.payment.{$driverByType('code_transfer')}.name"),
                        'provider_docs_url' => config("idir.payment.{$driverByType('code_transfer')}.docs_url"),
                        'provider_rules_url' => config("idir.payment.{$driverByType('code_transfer')}.rules_url"),
                        'rules_url' => route('web.page.show', [str_slug(trans('idir::dirs.rules'))])
                    ]) !!}
                </p>
            </div>
            @endif
            @if ($pricesByType('code_sms')->isNotEmpty())
            <div 
                class="tab-pane fade {{ old('payment_type', $paymentType) === "code_sms" ? 'show active' : null }}"
                id="nav-code_sms" 
                role="tabpanel" 
                aria-labelledby="nav-code_sms-tab"
            >
                <div class="form-group">
                    <label for="payment_code_sms" class="sr-only"> 
                        {{ trans('idir::dirs.payment_code_sms') }}
                    </label>
                    <select 
                        class="form-control {{ $isValid('payment_code_sms') }}" 
                        id="payment_code_sms" 
                        name="payment_code_sms"
                    >
                        @foreach ($pricesByType('code_sms')->sortBy('price') as $price)
                        <option 
                            value="{{ $price->id }}" {{ old('payment_code_sms') == $price->id ? 'selected' : null }}
                            data="{{ json_encode($price->only(['code', 'price', 'number'])) }}"
                        >
                            {{ trans('idir::dirs.price', [
                                'price' => $price->price,
                                'currency' => config("services.{$driverByType('code_sms')}.code_sms.currency"),                            
                                'days' => $days = $price->days,
                                'limit' => $days !== null ? 
                                    mb_strtolower(trans('idir::prices.days')) 
                                    : mb_strtolower(trans('idir::prices.unlimited'))
                            ]) }}
                        </option>
                        @endforeach
                    </select>
                    @includeWhen($errors->has('payment_code_sms'), 'icore::web.partials.errors', ['name' => 'payment_code_sms'])
                </div>
                <div class="form-group">
                    <label for="code_sms">
                        {{ trans('idir::dirs.code') }}: *
                    </label>
                    <input 
                        type="text" 
                        value="" 
                        name="code_sms" 
                        id="code_sms" 
                        class="form-control {{ $isValid('code_sms') }}"
                    >
                    @includeWhen($errors->has('code_sms'), 'icore::web.partials.errors', ['name' => 'code_sms'])
                </div>              
                <p>
                    {!! trans('idir::dirs.payment.code_sms.info', [
                        'number' => $paymentCodeSmsSelection->number,
                        'code_sms' => $paymentCodeSmsSelection->code,
                        'price' => $paymentCodeSmsSelection->price,
                        'provider_url' => config("idir.payment.{$driverByType('code_sms')}.url"),
                        'provider_name' => config("idir.payment.{$driverByType('code_sms')}.name"),
                        'provider_docs_url' => config("idir.payment.{$driverByType('code_sms')}.docs_url"),
                        'provider_rules_url' => config("idir.payment.{$driverByType('code_sms')}.rules_url"),
                        'rules_url' => route('web.page.show', [str_slug(trans('idir::dirs.rules'))])
                    ]) !!}
                </p>
            </div>
            @endif
        </div>
    </div>
</div>
