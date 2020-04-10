@extends(config('idir.layout') . '::web.layouts.layout', [
    'title' => [trans('icore::friends.route.index')],
    'desc' => [trans('icore::friends.route.index')],
    'keys' => [trans('icore::friends.route.index')]
])

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('web.home.index') }}">{{ trans('icore::home.route.index') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ trans('icore::friends.route.index') }}</li>
@endsection

@section('content')
<div class="container">
    <h1 class="h4 border-bottom pb-2">{{ trans('icore::friends.friends') }}</h1>
    <ul>
        <li><a href="https://intelekt.net.pl/icore" target="_blank">iCore - mini platforma blogowa</a></li>
        <li><a href="https://intelekt.net.pl/idir" target="_blank">iDir - nowoczesny katalog stron lub firm</a></li>
        <li><a href="https://www.iconpacks.net" target="_blank">Iconpacks - completely free icons</a></li>
        @foreach ($dirs as $dir)
        <li>{!! $dir->link !!}</li>
        @endforeach
    </ul>
</div>
@endsection
