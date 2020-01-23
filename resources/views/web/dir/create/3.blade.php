@extends(config('idir.layout') . '::web.layouts.layout', [
    'title' => [trans('idir::dirs.page.step', ['step' => 3]), trans('idir::dirs.page.create.summary')],
    'desc' => [trans('idir::dirs.page.create.summary')],
    'keys' => [trans('idir::dirs.page.create.summary')]
])

@section('breadcrumb')
<li class="breadcrumb-item"><a href="/">{{ trans('icore::home.page.index') }}</a></li>
<li class="breadcrumb-item">
    <a href="{{ route('web.dir.index') }}">{{ trans('idir::dirs.page.index') }}</a>
</li>
<li class="breadcrumb-item">{{ trans('idir::dirs.page.create.index') }}</li>
<li class="breadcrumb-item active" aria-current="page">
    {{ trans('idir::dirs.page.step', ['step' => 3]) }} {{ trans('idir::dirs.page.create.summary') }}
</li>
@endsection

@section('content')
<div class="container">
    <h3 class="h5 border-bottom pb-2">{{ trans('idir::dirs.page.create.summary') }}</h3>
    <div class="row mb-4">
        <div class="col-md-8">
            @include('idir::web.dir.partials.summary', ['value' => session('dir')])
            <form method="post" action="{{ route('web.dir.store_3', [$group->id]) }}" id="createSummary">
                @csrf
                @includeWhen($group->backlink > 0 && $backlinks->isNotEmpty(), 'idir::web.dir.partials.backlink')
                @if ($group->prices->isNotEmpty())
                @include('idir::web.dir.partials.payment')
                @else
                @render('icore::captchaComponent')
                @endif
                <div class="d-flex mb-3">
                    <div class="mr-auto">
                        <a href="{{ route('web.dir.create_2', [$group->id]) }}"
                        class="btn btn-secondary" style="width:6rem">
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
