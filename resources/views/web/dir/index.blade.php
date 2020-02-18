@extends(config('idir.layout') . '::web.layouts.layout', [
    'title' => [trans('idir::dirs.page.index'), trans('icore::pagination.page', ['num' => $dirs->currentPage()])],
    'desc' => [trans('idir::dirs.page.index')],
    'keys' => [trans('idir::dirs.page.index')]
])

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('web.home.index') }}">{{ trans('icore::home.page.index') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ trans('idir::dirs.page.index') }}</li>
@endsection

@section('content')
<div class="container">
    @render('idir::dir.carouselComponent')
    <div class="row">
        <div class="col-md-8 order-sm-1 order-md-2">
            <div id="filterContent">
                @include('idir::web.dir.partials.filter')
                @if ($dirs->isNotEmpty())
                <div id="infinite-scroll">
                    @foreach ($dirs as $dir)
                        @include('idir::web.dir.partials.dir', [$dir])
                    @endforeach
                    @include('icore::web.partials.pagination', ['items' => $dirs, 'next' => true])
                </div>
                @else
                <p>{{ trans('icore::default.empty') }}</p>
                @endif
            </div>
        </div>
        <div class="col-md-4 order-sm-2 order-md-1">
            @include('idir::web.dir.partials.sidebar')
        </div>
    </div>
</div>
@endsection
