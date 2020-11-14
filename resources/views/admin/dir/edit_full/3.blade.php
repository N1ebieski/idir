@extends(config('idir.layout') . '::admin.layouts.layout', [
    'title' => [
        $dir->title,
        trans('idir::dirs.route.step', ['step' => 3]),
        trans('idir::dirs.route.edit.3')
    ],
    'desc' => [$dir->title, trans('idir::dirs.route.edit.3')],
    'keys' => [$dir->title, trans('idir::dirs.route.edit.3')]
])

@section('breadcrumb')
<li class="breadcrumb-item">
    <a 
        href="{{ route('admin.dir.index') }}" 
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
    {{ trans('idir::dirs.route.step', ['step' => 3]) }} {{ trans('idir::dirs.route.edit.3') }}
</li>
@endsection

@section('content')
<div class="w-100">
    <h1 class="h5 border-bottom pb-2">
        <i class="fas fa-edit"></i>
        <span> {{ trans('idir::dirs.route.edit.3') }}</span>
    </h1>
    <div class="row mb-4">
        <div class="col-lg-8">
            @include('idir::admin.dir.partials.summary', [
                'value' => session("dirId.{$dir->id}"),
                'categories' => $categoriesSelection
            ])
            <hr>
            <form 
                method="post" 
                action="{{ route('admin.dir.update_full_3', [$dir->id, $group->id]) }}" 
                id="edit3"
            >
                @csrf
                @method('put')

                @includeWhen(
                    $group->backlink > 0 && optional($backlinks)->isNotEmpty(),
                    'idir::admin.dir.partials.backlink'
                )

                @if ($dir->isPayment($group->id) && $group->prices->isNotEmpty())
                    @include('idir::admin.dir.partials.payment')
                @endif

                <div class="d-flex mb-3">
                    <div class="mr-auto">
                        <a 
                            href="{{ route('admin.dir.edit_full_2', [$dir->id, $group->id]) }}" 
                            class="btn btn-secondary" 
                            style="width:6rem"
                        >
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
