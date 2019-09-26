@extends(config('idir.layout') . '::admin.layouts.layout', [
    'title' => [trans('idir::groups.page.index'), trans('icore::pagination.page', ['num' => $groups->currentPage()])],
    'desc' => [trans('idir::groups.page.index')],
    'keys' => [trans('idir::groups.page.index')]
])

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.home.index') }}">{{ trans('icore::home.page.index') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ trans('idir::groups.page.index') }}</li>
@endsection

@section('content')
<h1 class="h5 border-bottom pb-2 d-flex">
    <div class="mr-auto my-auto">
        <i class="fas fa-fw fa-object-group"></i>
        <span> {{ trans('idir::groups.page.index') }}</span>
    </div>
    @can('create groups')
    <div class="ml-auto text-right responsive-btn-group">
        <a href="{{ route("admin.group.{$group->poli}.create") }}" role="button" class="btn btn-primary text-nowrap">
            <i class="far fa-plus-square"></i>
            <span class="d-none d-sm-inline"> {{ trans('icore::default.create') }}</span>
        </a>
    </div>
    @endcan
</h1>
<div id="filterContent">
    @if ($groups->isNotEmpty())
    <div id="infinite-scroll">
        @foreach ($groups as $group)
            @include('idir::admin.group.group', ['group' => $group])
        @endforeach
        @include('icore::admin.partials.pagination', ['items' => $groups])
    </div>
    @else
    <p>{{ trans('icore::default.empty') }}</p>
    @endif
</div>

@component('icore::admin.partials.modal')
@slot('modal_id', 'editPositionModal')
@slot('modal_title')
<i class="fas fa-sort-amount-up"></i>
<span> {{ trans('idir::groups.page.edit_position') }}</span>
@endslot
@endcomponent

@endsection
