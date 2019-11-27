@extends(config('idir.layout') . '::admin.layouts.layout', [
    'title' => [trans('idir::fields.page.index'), trans('icore::pagination.page', ['num' => $fields->currentPage()])],
    'desc' => [trans('idir::fields.page.index')],
    'keys' => [trans('idir::fields.page.index')]
])

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.home.index') }}">{{ trans('icore::home.page.index') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ trans('idir::fields.page.index') }}</li>
@endsection

@section('content')
<h1 class="h5 border-bottom pb-2 d-flex">
    <div class="mr-auto my-auto">
        <i class="fab fa-fw fa-wpforms"></i>
        <span> {{ trans('idir::fields.page.index') }}</span>
    </div>
    @can('create fields')
    <div class="ml-auto text-right">
        <div class="responsive-btn-group">
            <button type="button" class="btn btn-primary text-nowrap create" data-toggle="modal"
            data-route="{{ route("admin.field.{$field->poli}.create") }}" data-target="#createModal">
                <i class="far fa-plus-square"></i>
                <span class="d-none d-sm-inline"> {{ trans('icore::default.create') }}</span>
            </button>
        </div>
    </div>
    @endcan
</h1>
<div id="filterContent">
    @include('idir::admin.field.filter')
    @if ($fields->isNotEmpty())
    <div id="infinite-scroll">
        @foreach ($fields as $field)
            @include("idir::admin.field.field", ['field' => $field])
        @endforeach
        @include('icore::admin.partials.pagination', ['items' => $fields])
    </div>
    @else
    <p>{{ trans('icore::default.empty') }}</p>
    @endif
</div>

@component('icore::admin.partials.modal')
@slot('modal_id', 'editModal')
@slot('modal_size', 'modal-lg')
@slot('modal_title')
<i class="far fa-edit"></i>
<span> {{ trans('idir::fields.page.edit') }}</span>
@endslot
@endcomponent

@component('icore::admin.partials.modal')
@slot('modal_id', 'editPositionModal')
@slot('modal_title')
<i class="fas fa-sort-amount-up"></i>
<span> {{ trans('idir::fields.page.edit_position') }}</span>
@endslot
@endcomponent

@component('icore::admin.partials.modal')
@slot('modal_id', 'createModal')
@slot('modal_size', 'modal-lg')
@slot('modal_title')
<i class="far fa-plus-square"></i>
<span> {{ trans('idir::fields.page.create') }}</span>
@endslot
@endcomponent

@endsection
