@extends(config('idir.layout') . '::web.layouts.layout', [
    'title' => [
        trans('idir::dirs.route.step', ['step' => 1]), 
        trans('idir::dirs.route.create.1')
    ],
    'desc' => [trans('idir::dirs.route.create.1')],
    'keys' => [trans('idir::dirs.route.create.1')]
])

@section('breadcrumb')
<li class="breadcrumb-item">
    <a 
        href="{{ route('web.dir.index') }}" 
        title="{{ trans('idir::dirs.route.create.index') }}"
    >
        {{ trans('idir::dirs.route.index') }}
    </a>
</li>
<li class="breadcrumb-item">
    {{ trans('idir::dirs.route.create.index') }}
</li>
<li class="breadcrumb-item active" aria-current="page">
    {{ trans('idir::dirs.route.step', ['step' => 1]) }} {{ trans('idir::dirs.route.create.1') }}
</li>
@endsection

@section('content')
<div class="container">
    @include('icore::web.partials.alerts')
    <h3 class="h5 border-bottom pb-2">
        {{ trans('idir::dirs.route.create.1') }}
    </h3>
    @if ($groups->isNotEmpty())
    <div class="row">
        @foreach($groups as $group)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                @include('idir::web.dir.partials.group')
                <div class="card-footer mt-auto {{ $group->isAvailable() ? null : 'bg-warning' }}">
                    @if ($group->isAvailable())
                    <a 
                        href="{{ route('web.dir.create_2', [$group->id]) }}" 
                        class="btn btn-link"
                    >
                        {{ trans('idir::dirs.choose_group') }} &raquo;
                    </a>
                    @else
                    <div class="btn text-dark">
                    {{ trans('idir::dirs.group_limit', [
                        'dirs' => $group->max_models ?? trans('idir::dirs.unlimited'),
                        'dirs_today' =>$group->max_models_daily ?? trans('idir::dirs.unlimited')
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
