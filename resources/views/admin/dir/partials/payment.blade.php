@inject('price', 'N1ebieski\IDir\Models\Price')

<div class="form-group">
    <label for="payment">
        {{ trans('idir::dirs.choose_payment_type') }}:
    </label>
    <div id="payment">
        <nav>
            <div 
                class="btn-group btn-group-toggle nav nav-tabs" 
                data-toggle="buttons" 
                id="nav-tab" 
                role="tablist"
            >
                @foreach (Price\Type::getAvailable() as $type)
                @if ($pricesByType($type)->isNotEmpty())
                <a 
                    class="nav-item nav-link btn btn-link flex-grow-0 text-decoration-none shadow-none {{ old('payment_type', $paymentType) === $type ? 'active' : null }}"
                    id="nav-{{ $type }}-tab" 
                    data-toggle="tab" 
                    href="#nav-{{ $type }}" 
                    role="tab"
                    aria-controls="nav-{{ $type }}" 
                    aria-selected="{{ old('payment_type', $paymentType) === $type ? 'true' : 'false' }}"
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
            @if ($pricesByType(Price\Type::TRANSFER)->isNotEmpty())
            <div 
                class="tab-pane fade {{ old('payment_type', $paymentType) === Price\Type::TRANSFER ? 'show active' : null }}"
                id="nav-transfer" 
                role="tabpanel" 
                aria-labelledby="nav-transfer-tab"
            >
                <div class="form-group">
                    <label for="payment_transfer" class="sr-only">
                        {{ trans('idir::dirs.payment_transfer') }}
                    </label>
                    <select 
                        class="selectpicker select-picker {{ $isValid('payment_transfer') }}" 
                        data-style="border"
                        data-width="100%"                        
                        id="payment_transfer" 
                        name="payment_transfer"
                    >
                        @foreach ($pricesByType(Price\Type::TRANSFER) as $price)
                        <option 
                            value="{{ $price->id }}" 
                            data-content="
                                @if ($price->discount_price)
                                <span class='badge bg-success text-white'>-{{ $price->discount }}%</span>
                                <span><s>{{ $price->regular_price }} {{ config("services.{$driverByType(Price\Type::TRANSFER)}.transfer.currency", 'PLN') }}</s></span>
                                @endif
                                <span>
                                    {{ trans('idir::dirs.price', [
                                        'price' => $price->price,
                                        'currency' => config("services.{$driverByType(Price\Type::TRANSFER)}.transfer.currency"),
                                        'days' => $days = $price->days,
                                        'limit' => $days !== null ? 
                                            mb_strtolower(trans('idir::prices.days')) 
                                            : mb_strtolower(trans('idir::prices.unlimited'))
                                    ]) }}
                                </span>
                            "                            
                            {{ old('payment_transfer') == $price->id ? 'selected' : null }}
                        >
                            {{ trans('idir::dirs.price', [
                                'price' => $price->price,
                                'currency' => config("services.{$driverByType(Price\Type::TRANSFER)}.transfer.currency"),                            
                                'days' => $days = $price->days,
                                'limit' => $days !== null ? 
                                    mb_strtolower(trans('idir::prices.days')) 
                                    : mb_strtolower(trans('idir::prices.unlimited'))
                            ]) }}
                        </option>
                        @endforeach
                    </select>
                    @includeWhen($errors->has('payment_transfer'), 'icore::admin.partials.errors', ['name' => 'payment_transfer'])
                </div>
                <p>
                    {!! trans('idir::dirs.payment.transfer.info', [
                        'provider_url' => config("idir.payment.{$driverByType(Price\Type::TRANSFER)}.url"),
                        'provider_name' => config("idir.payment.{$driverByType(Price\Type::TRANSFER)}.name"),
                        'provider_docs_url' => config("idir.payment.{$driverByType(Price\Type::TRANSFER)}.docs_url"),
                        'provider_rules_url' => config("idir.payment.{$driverByType(Price\Type::TRANSFER)}.rules_url"),
                        'rules_url' => route('web.page.show', [str_slug(trans('idir::dirs.rules'))])
                    ]) !!}
                </p>
            </div>
            @endif
            @if ($pricesByType(Price\Type::CODE_TRANSFER)->isNotEmpty())
            <div 
                class="tab-pane fade {{ old('payment_type', $paymentType) === Price\Type::CODE_TRANSFER ? 'show active' : null }}"
                id="nav-code_transfer" 
                role="tabpanel" 
                aria-labelledby="nav-code_transfer-tab"
            >
                <div class="form-group">
                    <label for="payment_code_transfer" class="sr-only">
                        {{ trans('idir::dirs.payment_code_transfer') }}
                    </label>
                    <select 
                        class="selectpicker select-picker {{ $isValid('payment_code_transfer') }}"
                        data-style="border"
                        data-width="100%"                         
                        id="payment_code_transfer" 
                        name="payment_code_transfer"
                    >
                        @foreach ($pricesByType(Price\Type::CODE_TRANSFER) as $price)
                        <option 
                            value="{{ $price->id }}"
                            data-content="
                                @if ($price->discount_price)
                                <span class='badge bg-success text-white'>-{{ $price->discount }}%</span>
                                <span><s>{{ $price->regular_price }} {{ config("services.{$driverByType(Price\Type::CODE_TRANSFER)}.code_transfer.currency", 'PLN') }}</s></span>
                                @endif
                                <span>
                                    {{ trans('idir::dirs.price', [
                                        'price' => $price->price,
                                        'currency' => config("services.{$driverByType(Price\Type::CODE_TRANSFER)}.code_transfer.currency"),
                                        'days' => $days = $price->days,
                                        'limit' => $days !== null ? 
                                            mb_strtolower(trans('idir::prices.days')) 
                                            : mb_strtolower(trans('idir::prices.unlimited'))
                                    ]) }}
                                </span>
                            "                              
                            data="{{ json_encode($price->only(['code', 'price'])) }}"
                            {{ old('payment_code_transfer') == $price->id ? 'selected' : null }}
                        >
                            {{ trans('idir::dirs.price', [
                                'price' => $price->price,
                                'currency' => config("services.{$driverByType(Price\Type::CODE_TRANSFER)}.code_transfer.currency"),                            
                                'days' => $days = $price->days,
                                'limit' => $days !== null ? 
                                    mb_strtolower(trans('idir::prices.days')) 
                                    : mb_strtolower(trans('idir::prices.unlimited'))
                            ]) }}
                        </option>
                        @endforeach
                    </select>
                    @includeWhen($errors->has('payment_code_transfer'), 'icore::admin.partials.errors', ['name' => 'payment_code_transfer'])
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
                        class="form-control {{ $isValid(Price\Type::CODE_TRANSFER) }}"
                    >
                    @includeWhen($errors->has('code_transfer'), 'icore::admin.partials.errors', ['name' => 'code_transfer'])
                </div>
                <p>
                    {!! trans('idir::dirs.payment.code_transfer.info', [
                        'code_transfer_url' => config("services.{$driverByType(Price\Type::CODE_TRANSFER)}.code_transfer.url") . $paymentCodeTransferSelection->code,
                        'price' => $paymentCodeTransferSelection->price,
                        'provider_url' => config("idir.payment.{$driverByType(Price\Type::CODE_TRANSFER)}.url"),
                        'provider_name' => config("idir.payment.{$driverByType(Price\Type::CODE_TRANSFER)}.name"),
                        'provider_docs_url' => config("idir.payment.{$driverByType(Price\Type::CODE_TRANSFER)}.docs_url"),
                        'provider_rules_url' => config("idir.payment.{$driverByType(Price\Type::CODE_TRANSFER)}.rules_url"),
                        'rules_url' => route('web.page.show', [str_slug(trans('idir::dirs.rules'))])
                    ]) !!}
                </p>
            </div>
            @endif
            @if ($pricesByType(Price\Type::CODE_SMS)->isNotEmpty())
            <div 
                class="tab-pane fade {{ old('payment_type', $paymentType) === Price\Type::CODE_SMS ? 'show active' : null }}"
                id="nav-code_sms" 
                role="tabpanel" 
                aria-labelledby="nav-code_sms-tab"
            >
                <div class="form-group">
                    <label for="payment_code_sms" class="sr-only">
                        {{ trans('idir::dirs.payment_code_sms') }}
                    </label>
                    <select 
                        class="selectpicker select-picker {{ $isValid('payment_code_sms') }}" 
                        data-style="border"
                        data-width="100%"                        
                        id="payment_code_sms" 
                        name="payment_code_sms"
                    >
                        @foreach ($pricesByType(Price\Type::CODE_SMS) as $price)
                        <option 
                            value="{{ $price->id }}"
                            data-content="
                                @if ($price->discount_price)
                                <span class='badge bg-success text-white'>-{{ $price->discount }}%</span>
                                <span><s>{{ $price->regular_price }} {{ config("services.{$driverByType(Price\Type::CODE_SMS)}.code_sms.currency", 'PLN') }}</s></span>
                                @endif
                                <span>
                                    {{ trans('idir::dirs.price', [
                                        'price' => $price->price,
                                        'currency' => config("services.{$driverByType(Price\Type::CODE_SMS)}.code_sms.currency"),                            
                                        'days' => $days = $price->days,
                                        'limit' => $days !== null ? 
                                            mb_strtolower(trans('idir::prices.days')) 
                                            : mb_strtolower(trans('idir::prices.unlimited'))
                                    ]) }}
                                </span>
                            "                             
                            data="{{ json_encode($price->only(['code', 'price', 'number', 'qr_as_image'])) }}"
                            {{ old('payment_code_sms') == $price->id ? 'selected' : null }}
                        >
                            {{ trans('idir::dirs.price', [
                                'price' => $price->price,
                                'currency' => config("services.{$driverByType(Price\Type::CODE_SMS)}.code_sms.currency"),                             
                                'days' => $days = $price->days,
                                'limit' => $days !== null ? 
                                    mb_strtolower(trans('idir::prices.days')) 
                                    : mb_strtolower(trans('idir::prices.unlimited'))
                            ]) }}
                        </option>
                        @endforeach
                    </select>
                    @includeWhen($errors->has('payment_code_sms'), 'icore::admin.partials.errors', ['name' => 'payment_code_sms'])
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
                    @includeWhen($errors->has('code_sms'), 'icore::admin.partials.errors', ['name' => 'code_sms'])
                </div>
                <div id="qr_image">
                    {!! $paymentCodeSmsSelection->qr_as_image !!}
                </div>             
                <p>
                    {!! trans('idir::dirs.payment.code_sms.info', [
                        'number' => $paymentCodeSmsSelection->number,
                        'code_sms' => $paymentCodeSmsSelection->code,
                        'price' => $paymentCodeSmsSelection->price,
                        'provider_url' => config("idir.payment.{$driverByType(Price\Type::CODE_SMS)}.url"),
                        'provider_name' => config("idir.payment.{$driverByType(Price\Type::CODE_SMS)}.name"),
                        'provider_docs_url' => config("idir.payment.{$driverByType(Price\Type::CODE_SMS)}.docs_url"),
                        'provider_rules_url' => config("idir.payment.{$driverByType(Price\Type::CODE_SMS)}.rules_url"),
                        'rules_url' => route('web.page.show', [str_slug(trans('idir::dirs.rules'))])
                    ]) !!}
                </p>
            </div>
            @endif
            @if ($pricesByType(Price\Type::PAYPAL_EXPRESS)->isNotEmpty())
            <div 
                class="tab-pane fade {{ old('payment_type', $paymentType) === Price\Type::PAYPAL_EXPRESS ? 'show active' : null }}"
                id="nav-paypal_express" 
                role="tabpanel" 
                aria-labelledby="nav-paypal_express-tab"
            >
                <div class="form-group">
                    <label for="payment_paypal_express" class="sr-only"> 
                        {{ trans('idir::dirs.payment_paypal_express') }}
                    </label>
                    <select 
                        class="selectpicker select-picker {{ $isValid('payment_paypal_express') }}" 
                        data-style="border" 
                        data-width="100%"                         
                        id="payment_paypal_express" 
                        name="payment_paypal_express"
                    >
                        @foreach ($pricesByType(Price\Type::PAYPAL_EXPRESS)->sortBy('price') as $price)
                        <option 
                            value="{{ $price->id }}"
                            data-content="
                                @if ($price->discount_price)
                                <span class='badge bg-success text-white'>-{{ $price->discount }}%</span>
                                <span><s>{{ $price->regular_price }} {{ config("services.{$driverByType(Price\Type::PAYPAL_EXPRESS)}.paypal_express.currency", 'PLN') }}</s></span>
                                @endif
                                <span>
                                    {{ trans('idir::dirs.price', [
                                        'price' => $price->price,
                                        'currency' => config("services.{$driverByType(Price\Type::PAYPAL_EXPRESS)}.paypal_express.currency"),
                                        'days' => $days = $price->days,
                                        'limit' => $days !== null ? 
                                            mb_strtolower(trans('idir::prices.days')) 
                                            : mb_strtolower(trans('idir::prices.unlimited'))
                                    ]) }}
                                </span>
                            "                             
                            {{ old('payment_paypal_express') == $price->id ? 'selected' : null }}
                        >
                            {{ trans('idir::dirs.price', [
                                'price' => $price->price,
                                'currency' => config("services.{$driverByType(Price\Type::PAYPAL_EXPRESS)}.paypal_express.currency"),
                                'days' => $days = $price->days,
                                'limit' => $days !== null ? 
                                    mb_strtolower(trans('idir::prices.days')) 
                                    : mb_strtolower(trans('idir::prices.unlimited'))
                            ]) }}
                        </option>
                        @endforeach
                    </select>
                    @includeWhen($errors->has('payment_paypal_express'), 'icore::web.partials.errors', ['name' => 'payment_paypal_express'])
                </div>
                <p>
                    {!! trans('idir::dirs.payment.paypal_express.info', [
                        'provider_url' => config("idir.payment.{$driverByType(Price\Type::PAYPAL_EXPRESS)}.url"),
                        'provider_name' => config("idir.payment.{$driverByType(Price\Type::PAYPAL_EXPRESS)}.name"),
                        'provider_docs_url' => config("idir.payment.{$driverByType(Price\Type::PAYPAL_EXPRESS)}.docs_url"),
                        'provider_rules_url' => config("idir.payment.{$driverByType(Price\Type::PAYPAL_EXPRESS)}.rules_url"),
                        'rules_url' => route('web.page.show', [str_slug(trans('idir::dirs.rules'))])
                    ]) !!}
                </p>
            </div>
            @endif            
        </div>
    </div>
</div>
