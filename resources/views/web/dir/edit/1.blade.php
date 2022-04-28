@extends(config('idir.layout') . '::web.layouts.layout', [
    'title' => [
        $dir->title, 
        trans('idir::dirs.route.step', ['step' => 1]), 
        trans('idir::dirs.route.edit.1')
    ],
    'desc' => [$dir->title, trans('idir::dirs.route.edit.1')],
    'keys' => [$dir->title, trans('idir::dirs.route.edit.1')]
])

@section('breadcrumb')
<li class="breadcrumb-item">
    <a 
        href="{{ route('web.dir.index') }}" 
        title="{{ trans('idir::dirs.route.index') }}"
    >
        {{ trans('idir::dirs.route.index') }}
    </a>
</li>
<li class="breadcrumb-item">
    {{ trans('idir::dirs.route.edit.index') }}
</li>
<li class="breadcrumb-item">
    {{ $dir->title }}
</li>
<li class="breadcrumb-item active" aria-current="page">
    {{ trans('idir::dirs.route.step', ['step' => 1]) }} {{ trans('idir::dirs.route.edit.1') }}
</li>
@endsection

@section('content')
<div class="container">
    @include('icore::web.partials.alerts')
    <h1 class="h5 border-bottom pb-2">
        {{ trans('idir::dirs.route.edit.1') }}
    </h1>
    @if ($groups->isNotEmpty())
    <div class="row">
        @foreach($groups as $group)
        <div class="col-lg-4 col-md-6 mb-4">
            <div 
                class="card h-100 {{ $group->id === $dir->group->id ? 'border-primary border' : null }}"
            >
                @include('idir::web.dir.partials.group')
                <div 
                    class="card-footer mt-auto
                    {{ $group->id === $dir->group->id || $group->isAvailable() ? null : 'bg-warning' }}"
                >
                    @if ($group->id === $dir->group->id)
                        @if ($dir->isRenew())
                        <a 
                            href="{{ route('web.dir.edit_renew', [$dir->id]) }}" 
                            class="btn btn-link"
                        >
                            {{ trans('idir::dirs.renew_group') }} &raquo;
                        </a>
                        @endif
                    <a 
                        href="{{ route('web.dir.edit_2', [$dir->id, $group->id]) }}" 
                        class="btn btn-link"
                    >
                        {{ trans('idir::dirs.choose_group') }} &raquo;
                    </a>
                    @elseif ($group->isAvailable())
                    <a 
                        href="{{ route('web.dir.edit_2', [$dir->id, $group->id]) }}" 
                        class="btn btn-link"
                    >
                        {{ trans('idir::dirs.change_group') }} &raquo;
                    </a>
                    @else
                    <div class="btn text-dark">
                    {{ trans('idir::dirs.group_limit', [
                        'dirs' => $group->max_models ?? trans('idir::dirs.unlimited'),
                        'dirs_today' => $group->max_models_daily ?? trans('idir::dirs.unlimited')
                    ]) }}
                    </div>
                    @endif
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
