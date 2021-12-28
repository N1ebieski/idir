@extends(config('idir.layout') . '::admin.layouts.layout')

@section('breadcrumb')
<li class="breadcrumb-item active">
    Overview
</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-6">
        <div style="height:400px;">
            <canvas 
                id="countDirsByStatus"
                data="{{ json_encode($countDirsByStatus) }}"
                data-label="{{ trans('idir::dirs.chart.count_by_status') }}"
                data-font-color="{{ $isTheme('dark', '#d3d3d3') }}"
            ></canvas>
        </div>
        <div style="width: 100%; overflow-x: auto; overflow-y: hidden;">
            <div style="height: 400px;">
                <canvas
                    id="countDirsByDateAndGroup"
                    data="{{ json_encode($countDirsByDateAndGroup) }}"
                    data-label="{{ trans('idir::dirs.chart.count_by_status') }}"
                    data-font-color="{{ $isTheme('dark', '#d3d3d3') }}"
                ></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mt-3 mt-lg-0">
        @if (is_null($posts))
        <div class="alert alert-warning alert-time" role="alert">
            {{ trans('icore::home.warning.no_posts') }}
        </div>        
        @elseif ($posts->isNotEmpty())
        <div>
            @foreach($posts->take(3) as $post)
            @include('icore::admin.home.partials.post', ['post' => $post])
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
