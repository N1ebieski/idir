<div class="form-group">
    <label for="payment">{{ trans('idir::dirs.choose_payment_type') }}:</label>
    <div id="payment">
        <nav>
            <div class="btn-group btn-group-toggle nav d-block" data-toggle="buttons" id="nav-tab" role="tablist">
                @foreach (['transfer', 'code_transfer', 'code_sms'] as $payment_type)
                @if ($group->prices->where('type', $payment_type)->isNotEmpty())
                <a class="nav-item btn btn-info {{ old('payment_type') === $payment_type ? 'active' : null }}"
                id="nav-{{ $payment_type }}-tab" data-toggle="tab" href="#nav-{{ $payment_type }}" role="tab"
                aria-controls="nav-{{ $payment_type }}" aria-selected="true">
                    <input type="radio" name="payment_type" value="{{ $payment_type }}" id="nav-{{ $payment_type }}-tab"
                    autocomplete="off" {{ (old('payment_type') === $payment_type) ? 'checked' : null }}>
                    {{ trans("idir::dirs.payment.{$payment_type}") }}
                </a>
                @endif
                @endforeach
            </div>
        </nav>
        <div class="tab-content mt-3" id="nav-tabContent">
            @if ($group->prices->where('type', 'transfer')->isNotEmpty())
            <div class="tab-pane fade {{ old('payment_type') === "transfer" ? 'show active' : null }}"
            id="nav-transfer" role="tabpanel" aria-labelledby="nav-transfer-tab">
                <div class="form-group">
                    <label for="payment_transfer" class="sr-only"> {{ trans('idir::dirs.payment_transfer') }}</label>
                    <select class="form-control @isValid('payment_transfer')" id="payment_transfer" name="payment_transfer">
                        @foreach ($transfers = $group->prices->where('type', 'transfer')->sortBy('price') as $transfer)
                        <option value="{{ $transfer->id }}" {{ old('payment_transfer') == $transfer->id ? 'selected' : null }}>
                            {{ trans('idir::dirs.price', [
                            'price' => $transfer->price,
                            'days' => $days = $transfer->days,
                            'limit' => $days !== null ? strtolower(trans('idir::groups.days')) : strtolower(trans('idir::groups.unlimited'))
                            ]) }}
                        </option>
                        @endforeach
                    </select>
                    @includeWhen($errors->has('payment_transfer'), 'icore::admin.partials.errors', ['name' => 'payment_transfer'])
                </div>
                <p>
                    @php $driver['transfer'] = config('idir.payment.transfer.driver'); @endphp
                    {!! trans('idir::dirs.payment.transfer_info', [
                        'provider_url' => config("idir.payment.{$driver['transfer']}.url"),
                        'provider_name' => config("idir.payment.{$driver['transfer']}.name"),
                        'provider_docs_url' => config("idir.payment.{$driver['transfer']}.docs_url"),
                        'provider_rules_url' => config("idir.payment.{$driver['transfer']}.rules_url"),
                        'rules_url' => route('web.page.show', [strtolower(trans('idir::dirs.rules'))])
                    ]) !!}
                </p>
            </div>
            @endif
            @if ($group->prices->where('type', 'code_transfer')->isNotEmpty())
            <div class="tab-pane fade {{ old('payment_type') === "code_transfer" ? 'show active' : null }}"
            id="nav-code_transfer" role="tabpanel" aria-labelledby="nav-code_transfer-tab">
                <div class="form-group">
                    <label for="payment_code_transfer" class="sr-only"> {{ trans('idir::dirs.payment_code_transfer') }}</label>
                    <select class="form-control @isValid('payment_code_transfer')" id="payment_code_transfer" name="payment_code_transfer">
                        @foreach ($codes = $group->prices->where('type', 'code_transfer')->sortBy('price') as $code)
                        <option value="{{ $code->id }}" {{ old('payment_code_transfer') == $code->id ? 'selected' : null }}
                        data="{{ json_encode($code->only(['code', 'price'])) }}">
                            {{ trans('idir::dirs.price', [
                            'price' => $code->price,
                            'days' => $days = $code->days,
                            'limit' => $days !== null ? strtolower(trans('idir::groups.days')) : strtolower(trans('idir::groups.unlimited'))
                            ]) }}
                        </option>
                        @endforeach
                    </select>
                    @includeWhen($errors->has('payment_code_transfer'), 'icore::admin.partials.errors', ['name' => 'payment_code_transfer'])
                </div>
                <div class="form-group">
                    <label for="code_transfer">{{ trans('idir::dirs.code') }}:</label>
                    <input type="text" value="" name="code_transfer" id="code_transfer" class="form-control @isValid('code_transfer')">
                    @includeWhen($errors->has('code_transfer'), 'icore::admin.partials.errors', ['name' => 'code_transfer'])
                </div>
                <p>
                    @php $driver['code_transfer'] = config('idir.payment.code_transfer.driver'); @endphp
                    {!! trans('idir::dirs.payment.code_transfer_info', [
                        'code_transfer_url' => config("services.{$driver['code_transfer']}.code_transfer.url")
                        . old('payment_code_transfer_model', $codes->first())->code,
                        'price' => old('payment_code_transfer_model', $codes->first())->price,
                        'provider_url' => config("idir.payment.{$driver['code_transfer']}.url"),
                        'provider_name' => config("idir.payment.{$driver['code_transfer']}.name"),
                        'provider_docs_url' => config("idir.payment.{$driver['code_transfer']}.docs_url"),
                        'provider_rules_url' => config("idir.payment.{$driver['code_transfer']}.rules_url"),
                        'rules_url' => route('web.page.show', [strtolower(trans('idir::dirs.rules'))])
                    ]) !!}
                </p>
            </div>
            @endif
            @if ($group->prices->where('type', 'code_sms')->isNotEmpty())
            <div class="tab-pane fade {{ old('payment_type') === "code_sms" ? 'show active' : null }}"
            id="nav-code_sms" role="tabpanel" aria-labelledby="nav-code_sms-tab">
                <div class="form-group">
                    <label for="payment_code_sms" class="sr-only"> {{ trans('idir::dirs.payment_code_sms') }}</label>
                    <select class="form-control @isValid('payment_code_sms')" id="payment_code_sms" name="payment_code_sms">
                        @foreach ($codes = $group->prices->where('type', 'code_sms')->sortBy('price') as $code)
                        <option value="{{ $code->id }}" {{ old('payment_code_sms') == $code->id ? 'selected' : null }}
                        data="{{ json_encode($code->only(['code', 'price', 'number'])) }}">
                            {{ trans('idir::dirs.price', [
                            'price' => $code->price,
                            'days' => $days = $code->days,
                            'limit' => $days !== null ? strtolower(trans('idir::groups.days')) : strtolower(trans('idir::groups.unlimited'))
                            ]) }}
                        </option>
                        @endforeach
                    </select>
                    @includeWhen($errors->has('payment_code_sms'), 'icore::admin.partials.errors', ['name' => 'payment_code_sms'])
                </div>
                <div class="form-group">
                    <label for="code_sms">{{ trans('idir::dirs.code') }}:</label>
                    <input type="text" value="" name="code_sms" id="code_sms" class="form-control @isValid('code_sms')">
                    @includeWhen($errors->has('code_sms'), 'icore::admin.partials.errors', ['name' => 'code_sms'])
                </div>
                @php $driver['code_sms'] = config('idir.payment.code_sms.driver') @endphp
                <p>
                    {!! trans('idir::dirs.payment.code_sms_info', [
                        'number' => old('payment_code_sms_model', $codes->first())->number,
                        'code_sms' => old('payment_code_sms_model', $codes->first())->code,
                        'price' => old('payment_code_sms_model', $codes->first())->price,
                        'provider_url' => config("idir.payment.{$driver['code_sms']}.url"),
                        'provider_name' => config("idir.payment.{$driver['code_sms']}.name"),
                        'provider_docs_url' => config("idir.payment.{$driver['code_sms']}.docs_url"),
                        'provider_rules_url' => config("idir.payment.{$driver['code_sms']}.rules_url"),
                        'rules_url' => route('web.page.show', [strtolower(trans('idir::dirs.rules'))])
                    ]) !!}
                </p>
            </div>
            @endif
        </div>
    </div>
</div>
