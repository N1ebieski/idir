@extends(config('idir.layout') . '::web.layouts.layout', [
    'title' => [trans('icore::categories.page.show', ['category' => $category->name]), trans('icore::pagination.page', ['num' => $dirs->currentPage()])],
    'desc' => [trans('icore::categories.page.show', ['category' => $category->name])],
    'keys' => [trans('icore::categories.page.show', ['category' => $category->name])]
])

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('web.home.index') }}">{{ trans('icore::home.page.index') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('web.dir.index') }}">{{ trans('idir::dirs.page.index') }}</a></li>
<li class="breadcrumb-item">{{ trans('icore::categories.page.index') }}</li>
@if ($category->ancestors->count() > 0)
@foreach ($category->ancestors as $ancestor)
<li class="breadcrumb-item">
    <a href="{{ route('web.category.dir.show', [$ancestor->slug]) }}">
        {{ $ancestor->name }}
    </a>
</li>
@endforeach
@endif
<li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 order-sm-1 order-md-2">
            <h1 class="h4 border-bottom pb-2 mb-4">
                @if (!empty($category->icon))
                    <i class="{{ $category->icon }}"></i>
                @endif
                <span> {{ trans('icore::categories.page.show', ['category' => $category->name]) }}</span>
            </h1>
            @if ($dirs->isNotEmpty())
            <div id="infinite-scroll">
                @foreach ($dirs as $dir)
                    @include('idir::web.dir.partials.dir', [$dir])
                @endforeach
                @include('icore::web.partials.pagination', ['items' => $dirs])
            </div>
            @else
            <p>{{ trans('icore::default.empty') }}</p>
            @endif
        </div>
        <div class="col-md-4 order-sm-2 order-md-1">
            @include('idir::web.category.dir.partials.sidebar')
        </div>
    </div>
</div>
@endsection
