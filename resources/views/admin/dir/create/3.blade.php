@extends(config('idir.layout') . '::admin.layouts.layout', [
    'title' => [trans('idir::dirs.route.step', ['step' => 3]), trans('idir::dirs.route.create.3')],
    'desc' => [trans('idir::dirs.route.create.3')],
    'keys' => [trans('idir::dirs.route.create.3')]
])

@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="{{ route('admin.dir.index') }}" title="{{ trans('idir::dirs.route.index') }}">
        {{ trans('idir::dirs.route.index') }}
    </a>
</li>
<li class="breadcrumb-item">{{ trans('idir::dirs.route.create.index') }}</li>
<li class="breadcrumb-item active" aria-current="page">
    {{ trans('idir::dirs.route.step', ['step' => 3]) }} {{ trans('idir::dirs.route.create.3') }}
</li>
@endsection

@section('content')
<div class="w-100">
    <h1 class="h5 border-bottom pb-2">
        <i class="far fa-plus-square"></i>
        <span>{{ trans('idir::dirs.route.create.3') }}</span>
    </h1>
    <div class="row mb-4">
        <div class="col-lg-8">
            @include('idir::admin.dir.partials.summary', [
                'value' => session('dir'),
                'categories' => $categoriesSelection
            ])
            <form method="post" action="{{ route('admin.dir.store_3', [$group->id]) }}" id="createSummary">
                @csrf
                @includeWhen($group->backlink > 0 && optional($backlinks)->isNotEmpty(), 'idir::admin.dir.partials.backlink')
                @if ($group->prices->isNotEmpty())
                @include('idir::admin.dir.partials.payment')
                @endif
                <div class="d-flex mb-3">
                    <div class="mr-auto">
                        <a href="{{ route('admin.dir.create_2', [$group->id]) }}"
                        class="btn btn-secondary" style="width:6rem">
                            &laquo; {{ trans('icore::default.back') }}
                        </a>
                    </div>
                    <div class="ml-auto">
                        <button type="submit" class="btn btn-primary" style="width:6rem">
                            {{ trans('icore::default.next') }} &raquo;
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-lg-4">
            <div class="card h-100">
                @include('idir::admin.dir.partials.group')
            </div>
        </div>
    </div>
</div>
@endsection
