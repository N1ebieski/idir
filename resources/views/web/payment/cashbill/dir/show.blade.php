@extends(config('idir.layout') . '::web.layouts.layout')

@section('content')
<div class="jumbotron jumbotron-fluid m-0 background">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <h5 class="card-header">{{ trans('idir::payments.page.show') }}</h5>
                <div class="card-body text-center">
                    @include('icore::web.partials.alerts')
                    <form action="{{ $payment['transfer_url'] }}" method="POST" id="transfer_redirect">
                        <p>{{ trans('idir::payments.redirect', ['provider' => config("idir.payment.{$payment['driver']}.name")]) }}:</p>
                        <div class="loader mb-3">
                            <div class="spinner-border">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                        <input type="hidden" name="service" value="{{ $payment['service'] }}"/>
                        <input type="hidden" name="amount" value="{{ $payment['amount'] }}"/>
                        <input type="hidden" name="desc" value="{{ $payment['desc'] }}"/>
                        <input type="hidden" name="userdata" value="{{ $payment['userdata'] }}"/>
                        <input type="hidden" name="sign" value="{{ $payment['sign'] }}"/>
                        <button type="submit" class="btn btn-primary">
                            {{ trans('idir::payments.page.show') }}
                            <span> ( <span id="counter">5</span> )<span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
