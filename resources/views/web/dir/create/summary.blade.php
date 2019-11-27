@extends(config('idir.layout') . '::web.layouts.layout', [
    'title' => [trans('idir::dirs.page.step', ['step' => 3]), trans('idir::dirs.page.create.summary')],
    'desc' => [trans('idir::dirs.page.create.summary')],
    'keys' => [trans('idir::dirs.page.create.summary')]
])

@section('breadcrumb')
<li class="breadcrumb-item"><a href="/">{{ trans('icore::home.page.index') }}</a></li>
<li class="breadcrumb-item">{{ trans('idir::dirs.page.index') }}</li>
<li class="breadcrumb-item active" aria-current="page">
    {{ trans('idir::dirs.page.step', ['step' => 3]) }} {{ trans('idir::dirs.page.create.summary') }}
</li>
@endsection

@section('content')
<div class="container">
    <h3 class="h5 border-bottom pb-2">{{ trans('idir::dirs.page.create.summary') }}</h3>
    <div class="row mb-4">
        <div class="col-md-8">
            <div>
                @if (session('dir.title') !== null)
                <p>
                    {{ trans('idir::dirs.title') }}:<br>
                    <span>{{ session('dir.title') }}</span>
                </p>
                @endif
                @if (session('dir.content_html') !== null)
                <p>
                    {{ trans('idir::dirs.content') }}:<br>
                    <span>{!! session('dir.content_html') !!}</span>
                </p>
                @endif
                @if (session('dir.notes') !== null)
                <p>
                    {{ trans('idir::dirs.notes') }}:<br>
                    <span>{{ session('dir.notes') }}</span>
                </p>
                @endif
                @if (session('dir.tags') !== null)
                <p>
                    {{ trans('idir::dirs.tags') }}:<br>
                    <span>{{ implode(', ', session('dir.tags')) }}</span>
                </p>
                @endif
                @if (session('dir.url') !== null)
                <p>
                    {{ trans('idir::dirs.url') }}:<br>
                    <span><a href="{{ session('dir.url') }}" target="_blank">{{ session('dir.url') }}</a></span>
                </p>
                @endif
                @if ($categories->isNotEmpty())
                <div>
                    {{ trans('idir::dirs.categories') }}:<br>
                    <ul class="pl-3">
                    @foreach ($categories as $category)
                        <li>
                        @if ($category->ancestors->count() > 0)
                            @foreach ($category->ancestors as $ancestor)
                                {{ $ancestor->name }} &raquo;
                            @endforeach
                        @endif
                            <strong>{{ $category->name }}</strong>
                        </li>
                    @endforeach
                    </ul>
                </div>
                @endif
            </div>
            <form method="post" action="{{ route('web.dir.store_summary', [$group->id]) }}" id="createSummary">
                @csrf
                @if ($group->backlink > 0 && $backlinks->isNotEmpty())
                <div class="form-group">
                    <label for="backlink">{{ trans('idir::dirs.choose_backlink') }}:</label>
                    <select class="form-control @isValid('backlink')" id="backlink" name="backlink">
                        @foreach ($backlinks as $backlink)
                        <option value="{{ $backlink->id }}" {{ old('backlink') == $backlink->id ? 'selected' : null }}
                        data="{{ json_encode($backlink->only(['name', 'url', 'img_url_from_storage'])) }}">
                            {{ $backlink->name }} [{{ $backlink->url }}]
                        </option>
                        @endforeach
                    </select>
                    @includeWhen($errors->has('backlink'), 'icore::admin.partials.errors', ['name' => 'backlink'])
                </div>
                <div class="form-group">
                    <textarea class="form-control" id="backlink_code" rows="5" readonly>{{ old('backlink_model', $backlinks->first())->link_as_html }}</textarea>
                </div>
                <div class="form-group">
                    <label for="backlink_url">{{ trans('idir::dirs.backlink_url') }}:</label>
                    <input type="text" name="backlink_url" id="backlink_url" placeholder="https://"
                    value="{{ old('backlink_url') }}" class="form-control @isValid('backlink_url')">
                    @includeWhen($errors->has('backlink_url'), 'icore::admin.partials.errors', ['name' => 'backlink_url'])
                </div>
                @endif
                @if ($group->prices->isNotEmpty())
                <div class="form-group">
                    <label for="payment">{{ trans('idir::dirs.choose_payment_type') }}:</label>
                    <div id="payment">
                        <nav>
                            <div class="btn-group btn-group-toggle nav d-block" data-toggle="buttons" id="nav-tab" role="tablist">
                                @foreach (['transfer', 'code_transfer', 'code_sms'] as $payment_type)
                                @if ($group->prices->where('type', $payment_type)->isNotEmpty())
                                <a class="nav-item btn btn-light {{ old('payment_type') === $payment_type ? 'active' : null }}"
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
                                    {!! trans('idir::dirs.payment.transfer_info', [
                                        'provider_url' => config("idir.payment.{$driver['transfer']}.url"),
                                        'provider_name' => config("idir.payment.{$driver['transfer']}.name"),
                                        'provider_docs_url' => config("idir.payment.{$driver['transfer']}.docs_url"),
                                        'provider_rules_url' => config("idir.payment.{$driver['transfer']}.rules_url"),
                                        'rules_url' => route('web.page.show', ['slug' => strtolower(trans('idir::dirs.rules'))])
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
                                    {!! trans('idir::dirs.payment.code_transfer_info', [
                                        'code_transfer_url' => config("services.{$driver['code_transfer']}.code_transfer.url")
                                        . old('payment_code_transfer_model', $codes->first())->code,
                                        'price' => old('payment_code_transfer_model', $codes->first())->price,
                                        'provider_url' => config("idir.payment.{$driver['code_transfer']}.url"),
                                        'provider_name' => config("idir.payment.{$driver['code_transfer']}.name"),
                                        'provider_docs_url' => config("idir.payment.{$driver['code_transfer']}.docs_url"),
                                        'provider_rules_url' => config("idir.payment.{$driver['code_transfer']}.rules_url"),
                                        'rules_url' => route('web.page.show', ['slug' => strtolower(trans('idir::dirs.rules'))])
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
                                <p>
                                    {!! trans('idir::dirs.payment.code_sms_info', [
                                        'number' => old('payment_code_sms_model', $codes->first())->number,
                                        'code_sms' => old('payment_code_sms_model', $codes->first())->code,
                                        'price' => old('payment_code_sms_model', $codes->first())->price,
                                        'provider_url' => config("idir.payment.{$driver['code_sms']}.url"),
                                        'provider_name' => config("idir.payment.{$driver['code_sms']}.name"),
                                        'provider_docs_url' => config("idir.payment.{$driver['code_sms']}.docs_url"),
                                        'provider_rules_url' => config("idir.payment.{$driver['code_sms']}.rules_url"),
                                        'rules_url' => route('web.page.show', ['slug' => strtolower(trans('idir::dirs.rules'))])
                                    ]) !!}
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @else
                @render('icore::captchaComponent')
                @endif
                <div class="d-flex mb-3">
                    <div class="mr-auto">
                        <a href="{{ route('web.dir.create_form', [$group->id]) }}" class="btn btn-secondary" style="width:6rem">
                            &laquo; {{ trans('icore::default.back') }}
                        </a>
                    </div>
                    <div class="ml-auto">
                        <button type="submit" class="btn btn-primary" style="width:6rem">
                            {{ trans('icore::default.next') }} &raquo;
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                @include('idir::web.dir.partials.group')
            </div>
        </div>
    </div>
</div>
@endsection

{{-- @push('script')
@component('icore::admin.partials.jsvalidation')
{!! JsValidator::formRequest('N1ebieski\IDir\Http\Requests\Web\Dir\StoreSummaryRequest', '#createSummary'); !!}
@endcomponent
@endpush --}}
