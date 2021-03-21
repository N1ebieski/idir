@extends(config('idir.layout') . '::admin.layouts.layout', [
    'title' => [
        trans('idir::prices.route.index'),
        $prices->currentPage() > 1 ?
            trans('icore::pagination.page', ['num' => $prices->currentPage()])
            : null
    ],
    'desc' => [trans('idir::prices.route.index')],
    'keys' => [trans('idir::prices.route.index')]
])

@section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page">
    {{ trans('idir::prices.route.index') }}
</li>
@endsection

@section('content')
<div id="filter-content">
    <h1 class="h5 border-bottom pb-2 d-flex">
        <div class="mr-auto my-auto">
            <i class="fas fa-fw fa-tags"></i>
            <span> {{ trans('idir::prices.route.index') }}</span>
        </div>
        @can('admin.prices.create')
        <div class="ml-auto text-right">
            <div class="responsive-btn-group">
                <button 
                    type="button" 
                    class="btn btn-primary text-nowrap create" 
                    data-toggle="modal"
                    data-route="{{ route('admin.price.create', ['group_id' => $filter['group']]) }}" 
                    data-target="#createModal"
                >
                    <i class="far fa-plus-square"></i>
                    <span class="d-none d-sm-inline">
                        {{ trans('icore::default.create') }}
                    </span>
                </button>
            </div>
        </div>
        @endcan
    </h1>
    <div>
        @include('idir::admin.price.partials.filter')
        @if ($prices->isNotEmpty())
        <div id="infinite-scroll">
            @foreach ($prices as $price)
                @include('idir::admin.price.partials.price', [
                    'price' => $price
                ])
            @endforeach
            @include('icore::admin.partials.pagination', [
                'items' => $prices
            ])
        </div>
        @else
        <p>{{ trans('icore::default.empty') }}</p>
        @endif
    </div>
</div>

@component('icore::admin.partials.modal')
@slot('modal_id', 'editModal')
@slot('modal_title')
<i class="far fa-edit"></i>
<span> {{ trans('idir::prices.route.edit') }}</span>
@endslot
@endcomponent

@component('icore::admin.partials.modal')
@slot('modal_id', 'createModal')
@slot('modal_title')
<i class="far fa-plus-square"></i>
<span> {{ trans('idir::prices.route.create') }}</span>
@endslot
@endcomponent

@endsection
