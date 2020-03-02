@extends(config('idir.layout') . '::admin.layouts.layout', [
    'title' => [trans('idir::dirs.page.index'), trans('icore::pagination.page', ['num' => $dirs->currentPage()])],
    'desc' => [trans('idir::dirs.page.index')],
    'keys' => [trans('idir::dirs.page.index')]
])

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.home.index') }}">{{ trans('icore::home.page.index') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ trans('idir::dirs.page.index') }}</li>
@endsection

@section('content')
<h1 class="h5 border-bottom pb-2 d-flex">
    <div class="mr-auto my-auto">
        <i class="far fa-fw fa-folder-open"></i>
        <span> {{ trans('idir::dirs.page.index') }}</span>
    </div>
    @can('create dirs')
    <div class="ml-auto text-right responsive-btn-group">
        <a href="{{ route('admin.dir.create_1') }}" role="button" class="btn btn-primary text-nowrap">
            <i class="far fa-plus-square"></i>
            <span class="d-none d-sm-inline"> {{ trans('icore::default.create') }}</span>
        </a>
    </div>
    @endcan
</h1>
<div id="filterContent">
    @include('idir::admin.dir.partials.filter')
    @if ($dirs->isNotEmpty())
    <form action="{{ route('admin.dir.destroy_global') }}" method="post" id="selectForm"
    data-reasons="{{ json_encode(config('idir.dir.delete_reasons')) }}" data-reasons-label="{{ trans('idir::dirs.delete_reason') }}">
        @csrf
        @method('delete')
        @can('destroy dirs')
        <div class="row my-2">
            <div class="col my-auto">
                <div class="custom-checkbox custom-control">
                    <input type="checkbox" class="custom-control-input" id="selectAll">
                    <label class="custom-control-label" for="selectAll">{{ trans('icore::default.select_all') }}</label>
                </div>
            </div>
        </div>
        @endcan
        <div id="infinite-scroll">
            @foreach ($dirs as $dir)
                @include('idir::admin.dir.partials.dir')
            @endforeach
            @include('icore::admin.partials.pagination', ['items' => $dirs])
        </div>
        @can('destroy dirs')
        <div class="select-action rounded">
            <button class="btn btn-danger submit" data-toggle="confirmation"
            type="button" data-btn-ok-label=" {{ trans('icore::default.yes') }}" data-btn-ok-icon-class="fas fa-check mr-1"
            data-btn-ok-class="btn h-100 d-flex align-items-center btn-primary btn-popover" 
            data-btn-cancel-label=" {{ trans('icore::default.cancel') }}"
            data-btn-cancel-class="btn h-100 d-flex align-items-center btn-secondary btn-popover" 
            data-btn-cancel-icon-class="fas fa-ban mr-1"
            data-title="{{ trans('icore::default.confirm') }}">
                <i class="far fa-trash-alt"></i>&nbsp;{{ trans('icore::default.delete_global') }}
            </button>
        </div>
        @endcan
    </form>
    @else
    <p>{{ trans('icore::default.empty') }}</p>
    @endif
</div>

@component('icore::admin.partials.modal')
@slot('modal_id', 'editModal')
@slot('modal_size', 'modal-lg')
@slot('modal_title')
<i class="far fa-edit"></i>
<span> {{ trans('idir::dirs.page.edit.index') }}</span>
@endslot
@endcomponent

@component('icore::admin.partials.modal')
@slot('modal_id', 'createBanDirModal')
@slot('modal_title')
<i class="fas fa-user-slash"></i>
<span> {{ trans('icore::bans.page.create') }}</span>
@endslot
@endcomponent

@component('icore::admin.partials.modal')
@slot('modal_id', 'showReportDirModal')
@slot('modal_title')
<span>{{ trans('icore::reports.page.show') }}</span>
@endslot
@endcomponent

@component('icore::admin.partials.modal')
@slot('modal_id', 'showPaymentLogsDirModal')
@slot('modal_title')
<span>{{ trans('idir::payments.page.show_logs') }}</span>
@endslot
@endcomponent
@endsection

@pushonce('script.map')
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ config('services.googlemap.api_key') }}&callback=initMap" 
type="text/javascript"></script>
@endpushonce
