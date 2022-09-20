@extends(config('idir.layout') . '::web.layouts.layout', [
    'title' => [
        trans('idir::dirs.route.index'),
        $dirs->currentPage() > 1 ?
            trans('icore::pagination.page', ['num' => $dirs->currentPage()])
            : null
    ],
    'desc' => [trans('idir::dirs.route.index')],
    'keys' => [trans('idir::dirs.route.index')]
])

@section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page">
    {{ trans('idir::dirs.route.index') }}
</li>
@endsection

@section('content')
<div class="container">
    <x-idir::dir.carousel-component
        max_content="500"
    />
    <div class="row mt-3">
        <div class="col-md-8 order-sm-1 order-md-2">
            <div id="filter-content">
                @include('idir::web.dir.partials.filter')
                @if ($dirs->isNotEmpty())
                <div id="infinite-scroll">
                    @foreach ($dirs as $dir)
                        @include('idir::web.dir.partials.dir', [$dir])
                    @endforeach
                    @include('icore::web.partials.pagination', [
                        'items' => $dirs,
                        'next' => true
                    ])
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
