@extends(config('idir.layout') . '::admin.layouts.layout', [
    'title' => [
        trans('idir::fields.route.index'),
        $fields->currentPage() > 1 ?
            trans('icore::pagination.page', ['num' => $fields->currentPage()])
            : null
    ],
    'desc' => [trans('idir::fields.route.index')],
    'keys' => [trans('idir::fields.route.index')]
])

@section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page">
    {{ trans('idir::fields.route.index') }}
</li>
@endsection

@section('content')
<h1 class="h5 border-bottom pb-2 d-flex">
    <div class="mr-auto my-auto">
        <i class="fab fa-fw fa-wpforms"></i>
        <span>{{ trans('idir::fields.route.index') }}</span>
    </div>
    @can('admin.fields.create')
    <div class="ml-auto text-right">
        <div class="responsive-btn-group">
            <button 
                type="button" 
                class="btn btn-primary text-nowrap create" 
                data-toggle="modal"
                data-route="{{ route("admin.field.{$field->poli}.create") }}" 
                data-target="#create-modal"
            >
                <i class="far fa-plus-square"></i>
                <span class="d-none d-sm-inline">{{ trans('icore::default.create') }}</span>
            </button>
        </div>
    </div>
    @endcan
</h1>
<div id="filter-content">
    @include('idir::admin.field.partials.filter')
    @if ($fields->isNotEmpty())
    <div id="infinite-scroll">
        @foreach ($fields as $field)
            @include("idir::admin.field.partials.field", [
                'field' => $field
            ])
        @endforeach
        @include('icore::admin.partials.pagination', [
            'items' => $fields
        ])
    </div>
    @else
    <p>{{ trans('icore::default.empty') }}</p>
    @endif
</div>

@include('idir::admin.field.partials.filter_filter')

@component('icore::admin.partials.modal')
@slot('modal_id', 'edit-modal')
@slot('modal_size', 'modal-lg')
@slot('modal_title')
<i class="far fa-edit"></i>
<span> {{ trans('idir::fields.route.edit') }}</span>
@endslot
@endcomponent

@component('icore::admin.partials.modal')
@slot('modal_id', 'edit-position-modal')
@slot('modal_title')
<i class="fas fa-sort-amount-up"></i>
<span> {{ trans('idir::fields.route.edit_position') }}</span>
@endslot
@endcomponent

@component('icore::admin.partials.modal')
@slot('modal_id', 'create-modal')
@slot('modal_size', 'modal-lg')
@slot('modal_title')
<i class="far fa-plus-square"></i>
<span> {{ trans('idir::fields.route.create') }}</span>
@endslot
@endcomponent

@endsection
