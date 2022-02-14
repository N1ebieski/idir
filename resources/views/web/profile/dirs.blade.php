@extends(config('icore.layout') . '::web.profile.layouts.layout', [
    'title' => [
        trans('idir::profile.route.dirs'),
        $dirs->currentPage() > 1 ?
            trans('icore::pagination.page', ['num' => $dirs->currentPage()])
            : null
    ],
    'desc' => [trans('idir::profile.route.dirs')],
    'keys' => [trans('idir::profile.route.dirs')]
])

@section('breadcrumb')
<li class="breadcrumb-item">
    {{ trans('icore::profile.route.index') }}
</li>
<li class="breadcrumb-item active" aria-current="page">
    {{ trans('idir::profile.route.dirs') }}
</li>
@endsection

@section('content')
<h1 class="h5 border-bottom pb-2">
    <i class="far fa-fw fa-folder-open"></i>
    <span>{{ trans('idir::profile.route.dirs') }}</span>
</h1>
<div id="filter-content">
    @include('idir::web.profile.partials.dir.filter')
    @if ($dirs->isNotEmpty())
    <div id="infinite-scroll">
        @foreach ($dirs as $dir)
            @include('idir::web.profile.partials.dir.dir')
        @endforeach
        @include('icore::web.partials.pagination', [
            'items' => $dirs
        ])
    </div>
    @else
    <p>{{ trans('icore::default.empty') }}</p>
    @endif
</div>
@endsection
