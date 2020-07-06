@extends(config('icore.layout') . '::web.layouts.layout', [
    'title' => [trans('icore::tags.route.show', ['tag' => $tag->name]), trans('icore::pagination.page', ['num' => $dirs->currentPage()])],
    'desc' => [trans('icore::tags.route.show', ['tag' => $tag->name])],
    'keys' => [trans('icore::tags.route.show', ['tag' => $tag->name])],
    'index' => 'noindex'
])

@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="{{ route('web.dir.index') }}" title="{{ trans('idir::dirs.route.index') }}">
        {{ trans('idir::dirs.route.index') }}
    </a>
</li>
<li class="breadcrumb-item">{{ trans('icore::tags.route.index') }}</li>
<li class="breadcrumb-item active" aria-current="page">{{ $tag->name }}</li>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 order-sm-1 order-md-2">
            <h1 class="h4 border-bottom pb-2">
                {{ trans('icore::tags.route.show', ['tag' => $tag->name]) }}
            </h1>
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
