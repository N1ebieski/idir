@extends(config('icore.layout') . '::web.profile.layouts.layout', [
    'title' => [trans('idir::profile.page.edit_dir'), trans('icore::pagination.page', ['num' => $dirs->currentPage()])],
    'desc' => [trans('idir::profile.page.edit_dir')],
    'keys' => [trans('idir::profile.page.edit_dir')]
])

@section('breadcrumb')
<li class="breadcrumb-item"><a href="/">{{ trans('icore::home.page.index') }}</a></li>
<li class="breadcrumb-item">{{ trans('icore::profile.page.index') }}</li>
<li class="breadcrumb-item active" aria-current="page">{{ trans('idir::profile.page.edit_dir') }}</li>
@endsection

@section('content')
<h1 class="h4 mb-4 border-bottom pb-2">
    <i class="far fa-fw fa-folder-open"></i>
    <span> {{ trans('idir::profile.page.edit_dir') }}</span>
</h1>
@if ($dirs->isNotEmpty())
<div id="infinite-scroll">
    @foreach ($dirs as $dir)
        @include('idir::web.profile.partials.dir')
    @endforeach
    @include('icore::admin.partials.pagination', ['items' => $dirs])
</div>
@else
<p>{{ trans('icore::default.empty') }}</p>
@endif
@endsection
