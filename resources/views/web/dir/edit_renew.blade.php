@extends(config('idir.layout') . '::web.layouts.layout', [
    'title' => [$dir->title, trans('idir::dirs.route.edit.renew')],
    'desc' => [$dir->title, trans('idir::dirs.route.edit.renew')],
    'keys' => [$dir->title, trans('idir::dirs.route.edit.renew')]
])

@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="{{ route('web.dir.index') }}" title="{{ trans('idir::dirs.route.index') }}">
        {{ trans('idir::dirs.route.index') }}
    </a>
</li>
<li class="breadcrumb-item">{{ trans('idir::dirs.route.edit.index') }}</li>
<li class="breadcrumb-item">{{ $dir->title }}</li>
<li class="breadcrumb-item active" aria-current="page">{{ trans('idir::dirs.route.edit.renew') }}</li>
@endsection

@section('content')
<div class="container">
    <h3 class="h5 border-bottom pb-2">{{ trans('idir::dirs.route.edit.renew') }}</h3>
    <div class="row mb-4">
        <div class="col-md-8">
            @include('idir::web.dir.partials.summary', [
                'value' => $dir->getAttributes(),
                'categories' => $dir->categories,
                'group' => $dir->group
            ])
            <form method="post" action="{{ route('web.dir.update_renew', [$dir->id]) }}" id="edit_renew">
                @csrf
                @method('patch')
                @include('idir::web.dir.partials.payment', ['group' => $dir->group])
                <button type="submit" class="btn btn-primary btn-send mb-3">{{ trans('icore::default.submit') }}</button>
            </form>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                @include('idir::web.dir.partials.group', ['group' => $dir->group])
            </div>
        </div>
    </div>
</div>
@endsection
