@extends(config('idir.layout') . '::web.layouts.layout', [
    'title' => ['Wybierz typ wpisu'],
    'desc' => ['Wybierz typ wpisu'],
    'keys' => ['Wybierz typ wpisu']
])

@section('breadcrumb')
<li class="breadcrumb-item"><a href="/">{{ trans('icore::home.page.index') }}</a></li>
<li class="breadcrumb-item">Katalog stron</li>
<li class="breadcrumb-item active" aria-current="page">Wybierz typ wpisu</li>
@endsection

@section('content')
<div class="container">

    <div class="row">
    @for($i=0; $i<4; $i++)
        <div class="col-lg col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    Featured
                </div>
                <ul class="list-group list-group-flush" style="min-height:30rem">
                    <li class="list-group-item">Cras justo odio</li>
                    <li class="list-group-item">Dapibus ac facilisis in</li>
                    <li class="list-group-item">Vestibulum at eros</li>
                    <li class="list-group-item">DHjsdu sydusds</li>
                    <li class="list-group-item">Dsdsa sdsd sdaadsa</li>
                    <li class="list-group-item">Dsjdh shshjs hjsdhjs</li>
                </ul>
                <div class="card-footer">
                    <a href="{{ route('web.dir.create', ['group_id' => $i]) }}">Dodaj stronę do grupy &raquo;</a>
                </div>
            </div>
        </div>
    @endfor
    </div>
</div>
@endsection
