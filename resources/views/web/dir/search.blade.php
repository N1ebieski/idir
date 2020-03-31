@extends(config('idir.layout') . '::web.layouts.layout', [
    'title' => [trans('idir::dirs.route.search', ['search' => $search]), trans('icore::pagination.page', ['num' => $dirs->currentPage()])],
    'desc' => [trans('idir::dirs.route.search', ['search' => $search])],
    'keys' => [trans('idir::dirs.route.search', ['search' => $search])]
])

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('web.home.index') }}">{{ trans('icore::home.route.index') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('web.dir.index') }}">{{ trans('idir::dirs.route.index') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ trans('idir::dirs.route.search', ['search' => $search]) }}</li>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 order-sm-1 order-md-2">
            <h1 class="h4 border-bottom pb-2 mb-4">
                {{ trans('idir::dirs.route.search', ['search' => $search]) }}
            </h1>
            <div id="filterContent">
                @include('idir::web.dir.partials.filter')             
                @if ($dirs->isNotEmpty())
                <div id="infinite-scroll">
                    @foreach ($dirs as $dir)
                        @include('idir::web.dir.partials.dir', [$dir])
                    @endforeach
                    @include('icore::admin.partials.pagination', ['items' => $dirs, 'next' => true])
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
