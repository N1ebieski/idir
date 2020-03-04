@extends(config('idir.layout') . '::admin.layouts.layout', [
    'title' => [trans('idir::dirs.page.step', ['step' => 1]), trans('idir::dirs.page.create.group')],
    'desc' => [trans('idir::dirs.page.create.group')],
    'keys' => [trans('idir::dirs.page.create.group')]
])

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.home.index') }}">{{ trans('icore::home.page.index') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.dir.index') }}">{{ trans('idir::dirs.page.index') }}</a></li>
<li class="breadcrumb-item">{{ trans('idir::dirs.page.create.index') }}</li>
<li class="breadcrumb-item active" aria-current="page">
    {{ trans('idir::dirs.page.step', ['step' => 1]) }} {{ trans('idir::dirs.page.create.group') }}
</li>
@endsection

@section('content')
<div class="w-100">
    @include('icore::admin.partials.alerts')
    <h1 class="h5 border-bottom pb-2">
        <i class="far fa-plus-square"></i>
        <span> {{ trans('idir::dirs.page.create.group') }}</span>
    </h1>
    @if ($groups->isNotEmpty())
    <div class="row">
        @foreach($groups as $group)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                @include('idir::admin.dir.partials.group')
                <div class="card-footer mt-auto {{ $group->isAvailable() ? null : 'bg-warning' }}">
                    @if ($group->isAvailable())
                    <a href="{{ route('admin.dir.create_2', [$group->id]) }}" class="btn btn-link">
                        {{ trans('idir::dirs.choose_group') }} &raquo;
                    </a>
                    @else
                    <div class="btn text-dark">
                    {{ trans('idir::dirs.group_limit', [
                        'dirs' => $group->max_models ?? trans('idir::dirs.unlimited'),
                        'dirs_today' => $group->max_models_daily ?? trans('idir::dirs.unlimited')
                    ]) }}
                    </div>
                    @endcan
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <p>{{ trans('idir::groups.empty') }}</p>
    @endif
</div>
@endsection
