@extends(config('idir.layout') . '::admin.layouts.layout', [
    'title' => [
        trans('idir::groups.route.index'),
        $groups->currentPage() > 1 ?
            trans('icore::pagination.page', ['num' => $groups->currentPage()])
            : null
    ],
    'desc' => [trans('idir::groups.route.index')],
    'keys' => [trans('idir::groups.route.index')]
])

@section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page">
    {{ trans('idir::groups.route.index') }}
</li>
@endsection

@section('content')
<h1 class="h5 border-bottom pb-2 d-flex">
    <div class="mr-auto my-auto">
        <i class="fas fa-fw fa-object-group"></i>
        <span> {{ trans('idir::groups.route.index') }}</span>
    </div>
    @can('admin.groups.create')
    <div class="ml-auto text-right responsive-btn-group">
        <a 
            href="{{ route("admin.group.create") }}" 
            role="button" 
            class="btn btn-primary text-nowrap"
        >
            <i class="far fa-plus-square"></i>
            <span class="d-none d-sm-inline"> {{ trans('icore::default.create') }}</span>
        </a>
    </div>
    @endcan
</h1>
<div id="filter-content">
    @include('idir::admin.group.partials.filter')
    @if ($groups->isNotEmpty())
    <div id="infinite-scroll">
        @foreach ($groups as $group)
            @include('idir::admin.group.partials.group', [
                'group' => $group
            ])
        @endforeach
        @include('icore::admin.partials.pagination', [
            'items' => $groups
        ])
    </div>
    @else
    <p>{{ trans('icore::default.empty') }}</p>
    @endif
</div>

@component('icore::admin.partials.modal')
@slot('modal_id', 'edit-position-modal')
@slot('modal_title')
<i class="fas fa-sort-amount-up"></i>
<span> {{ trans('idir::groups.route.edit_position') }}</span>
@endslot
@endcomponent

@endsection
