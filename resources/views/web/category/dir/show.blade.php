@extends(config('idir.layout') . '::web.layouts.layout', [
    'title' => [
        trans('icore::categories.route.show', ['category' => $category->name]), 
        $region->name,
        $dirs->currentPage() > 1 ?
            trans('icore::pagination.page', ['num' => $dirs->currentPage()])
            : null
    ],
    'desc' => [trans('icore::categories.route.show', ['category' => $category->name]), $region->name],
    'keys' => [trans('icore::categories.route.show', ['category' => $category->name]), $region->name]
])

@section('breadcrumb')
<li class="breadcrumb-item">
    <a 
        href="{{ route('web.dir.index') }}" 
        title="{{ trans('idir::dirs.route.index') }}"
    >
        {{ trans('idir::dirs.route.index') }}
    </a>
</li>
<li class="breadcrumb-item">
    {{ trans('icore::categories.route.index') }}
</li>

@if ($category->ancestors->count() > 0)
@foreach ($category->ancestors as $ancestor)
<li class="breadcrumb-item">
    <a 
        href="{{ route('web.category.dir.show', [$ancestor->slug, $region->slug]) }}"
        title="{{ $ancestor->name }}"
    >
        {{ $ancestor->name }}
    </a>
</li>
@endforeach
@endif

@if (isset($region->name))
<li class="breadcrumb-item">
    {{ $category->name }}
</li>
<li class="breadcrumb-item active" aria-current="page">
    {{ $region->name }}
</li>
@else
<li class="breadcrumb-item active" aria-current="page">
    {{ $category->name }}
</li>
@endif

@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 order-sm-1 order-md-2">
            <h1 class="h4 border-bottom pb-2">
                @if (!empty($category->icon))
                    <i class="{{ $category->icon }}"></i>
                @endif
                <span>{{ trans('icore::categories.route.show', ['category' => $category->name]) }}</span>
            </h1>
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
            @include('idir::web.category.dir.partials.sidebar')
        </div>
    </div>
    @render('idir::category.dir.gridComponent', [
        'parent' => $category->id
    ])
</div>
@endsection
