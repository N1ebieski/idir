@extends(config('idir.layout') . '::web.layouts.layout', [
    'title' => [trans('idir::dirs.page.step', ['step' => 3]), trans('idir::dirs.page.create.summary')],
    'desc' => [trans('idir::dirs.page.create.summary')],
    'keys' => [trans('idir::dirs.page.create.summary')]
])

@section('breadcrumb')
<li class="breadcrumb-item"><a href="/">{{ trans('icore::home.page.index') }}</a></li>
<li class="breadcrumb-item">{{ trans('idir::dirs.page.index') }}</li>
<li class="breadcrumb-item active" aria-current="page">
    {{ trans('idir::dirs.page.step', ['step' => 3]) }} {{ trans('idir::dirs.page.create.summary') }}
</li>
@endsection

@section('content')
<div class="container">
    <h3 class="h5 border-bottom pb-2">{{ trans('idir::dirs.page.create.summary') }}</h3>
    <div class="row mb-4">
        <div class="col-md-8">
            <div>
                @if (session('dir.title') !== null)
                <p>
                    <b>{{ trans('idir::dirs.title') }}:</b><br>
                    <span>{{ session('dir.title') }}</span>
                </p>
                @endif
                @if (session('dir.content_html') !== null)
                <p>
                    <b>{{ trans('idir::dirs.content') }}:</b><br>
                    <span>{!! session('dir.content_html') !!}</span>
                </p>
                @endif
                @if (session('dir.notes') !== null)
                <p>
                    <b>{{ trans('idir::dirs.notes') }}:</b><br>
                    <span>{{ session('dir.notes') }}</span>
                </p>
                @endif
                @if (session('dir.tags') !== null)
                <p>
                    <b>{{ trans('idir::dirs.tags') }}:</b><br>
                    <span>{{ implode(', ', session('dir.tags')) }}</span>
                </p>
                @endif
                @if (session('dir.url') !== null)
                <p>
                    <b>{{ trans('idir::dirs.url') }}:</b><br>
                    <span><a href="{{ session('dir.url') }}" target="_blank">{{ session('dir.url') }}</a></span>
                </p>
                @endif
                @if ($categories->isNotEmpty())
                <div>
                    <b>{{ trans('idir::dirs.categories') }}:</b><br>
                    <ul class="pl-3">
                    @foreach ($categories as $category)
                        <li>
                        @if ($category->ancestors->count() > 0)
                            @foreach ($category->ancestors as $ancestor)
                                {{ $ancestor->name }} &raquo;
                            @endforeach
                        @endif
                            <strong>{{ $category->name }}</strong>
                        </li>
                    @endforeach
                    </ul>
                </div>
                @endif
            </div>
            <form method="post" action="{{ route('web.dir.store_summary', ['group' => $group->id]) }}">
                @csrf
                <div class="d-flex mb-3">
                    <div class="mr-auto">
                        <a href="{{ route('web.dir.create_form', [$group->id]) }}" class="btn btn-secondary" style="width:6rem">
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
        <div class="col-md-4">
            <div class="card h-100">
                @include('idir::web.dir.partials.group')
            </div>
        </div>
    </div>
</div>
@endsection

{{-- @push('script')
@component('icore::admin.partials.jsvalidation')
{!! JsValidator::formRequest('N1ebieski\IDir\Http\Requests\Web\Dir\StoreFormRequest', '#createForm'); !!}
@endcomponent
@endpush --}}
