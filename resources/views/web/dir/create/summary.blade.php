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
            <form method="post" action="{{ route('web.dir.store_summary', ['group' => $group->id]) }}">
                @csrf
                @if ($group->prices->isNotEmpty())
                <div class="form-group">
                    <label for="payment">{{ trans('idir::dirs.choose_payment_type') }}:</label>
                    <div id="payment">
                        <nav>
                            <div class="btn-group btn-group-toggle nav d-block" data-toggle="buttons" id="nav-tab" role="tablist">
                                <a class="nav-item btn btn-light active" id="nav-transfer-tab" data-toggle="tab"
                                href="#nav-transfer" role="tab" aria-controls="nav-transfer" aria-selected="true">
                                    <input type="radio" name="payment_type" value="transfer" id="nav-tansfer-tab"
                                    autocomplete="off" {{ (!old('payment_type') || old('payment_type') === "transfer") ? 'checked' : null }}>
                                    {{ trans('idir::dirs.payment_transfer') }}
                                </a>
                                <a class="nav-item btn btn-light" id="nav-auto-sms-tab" data-toggle="tab"
                                href="#nav-auto-sms" role="tab" aria-controls="nav-auto-sms" aria-selected="false">
                                    <input type="radio" name="payment_type" value="auto_sms" id="nav-auto-sms-tab"
                                    autocomplete="off" {{ (old('payment_type') === "auto_sms") ? 'checked' : null }}>
                                    {{ trans('idir::dirs.payment_auto_sms') }}
                                </a>
                            </div>
                        </nav>
                        <div class="tab-content mt-3" id="nav-tabContent">
                            <div class="tab-pane fade {{ (!old('payment_type') || old('payment_type') === "transfer") ? 'show active' : null }}"
                            id="nav-transfer" role="tabpanel" aria-labelledby="nav-transfer-tab">
                                <div class="form-group">
                                    <label for="payment_transfer" class="sr-only"> {{ trans('idir::dirs.payment_transfer') }}</label>
                                    <select class="form-control @isValid('visible')" id="payment_transfer" name="payment_transfer">
                                        @foreach ($group->prices->where('type', 'transfer')->sortBy('price') as $transfer)
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
                                    {!! trans('idir::dirs.payment_transfer_info', [
                                        'provider_url' => config("idir.payment.{$transfer_driver}.url"),
                                        'provider_name' => config("idir.payment.{$transfer_driver}.name"),
                                        'provider_docs_url' => config("idir.payment.{$transfer_driver}.docs_url"),
                                        'provider_rules_url' => config("idir.payment.{$transfer_driver}.rules_url"),
                                        'rules_url' => route('web.page.show', ['slug' => trans('idir::dirs.rules')])
                                    ]) !!}
                                </p>
                            </div>
                            <div class="tab-pane fade {{ (old('payment_type') === "auto_sms") ? 'show active' : null }}"
                            id="nav-auto-sms" role="tabpanel" aria-labelledby="nav-auto-sms-tab">
                                <div class="form-group">
                                    <label for="payment_auto_sms" class="sr-only"> {{ trans('idir::dirs.payment_auto_sms') }}</label>
                                    <select class="form-control @isValid('visible')" id="payment_auto_sms" name="payment_auto_sms">
                                        @foreach ($group->prices->where('type', 'auto_sms')->sortBy('price') as $auto_sms)
                                            <option value="{{ $auto_sms->id }}" {{ old('payment_auto_sms') == $auto_sms->id ? 'selected' : null }}>
                                                {{ trans('idir::dirs.price', [
                                                'price' => $auto_sms->price,
                                                'days' => $days = $auto_sms->days,
                                                'limit' => $days !== null ? strtolower(trans('idir::groups.days')) : strtolower(trans('idir::groups.unlimited'))
                                                ]) }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @includeWhen($errors->has('payment_auto_sms'), 'icore::admin.partials.errors', ['name' => 'payment_auto_sms'])
                                </div>
                            </div>
                        </div>
                        <p>
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
{!! JsValidator::formRequest('N1ebieski\IDir\Http\Requests\Web\Dir\StoreFormRequest', '#createForm'); !!}
@endcomponent
@endpush --}}
