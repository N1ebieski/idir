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
                id="count-dirs-by-status"
                data="{{ json_encode($countDirsByStatus) }}"
                data-label="{{ trans('idir::dirs.chart.count_by_status') }}"
                data-font-color="{{ $isTheme('dark', '#d3d3d3') }}"
            ></canvas>
        </div>
        <div class="mt-3" style="width: 100%; overflow-x: auto; overflow-y: hidden;">
            <div style="height: 400px;">
                <canvas
                    id="count-dirs-by-date-and-group"
                    data="{{ json_encode($countDirsByDateAndGroup) }}"
                    data-font-color="{{ $isTheme('dark', '#d3d3d3') }}"                    
                    data-label="{{ trans('idir::dirs.chart.count_by_date_and_group') }}"
                    data-x-label="{{ trans('idir::dirs.chart.x.label') }}"
                    data-y-label="{{ trans('idir::dirs.chart.y.label') }}"
                    data-all-label="{{ trans('icore::default.all') }}"
                ></canvas>
            </div>
        </div>
        <div class="mt-3" style="height:400px;">
            <canvas 
                id="count-dirs-by-group"
                data="{{ json_encode($countDirsByGroup) }}"
                data-label="{{ trans('idir::dirs.chart.count_by_group') }}"
                data-font-color="{{ $isTheme('dark', '#d3d3d3') }}"
            ></canvas>
        </div>
        <div class="mt-3" style="width: 100%; overflow-x: auto; overflow-y: hidden;">
            <div style="height: 400px;">
                <canvas
                    id="count-posts-and-pages-by-date"
                    data="{{ json_encode($countPostsAndPagesByDate) }}"
                    data-font-color="{{ $isTheme('dark', '#d3d3d3') }}"                    
                    data-label="{{ trans('icore::posts.chart.count_by_date') }}"
                    data-x-label="{{ trans('icore::posts.chart.x.label') }}"
                    data-y-label="{{ trans('icore::posts.chart.y.label') }}"
                    data-all-label="{{ trans('icore::default.all') }}"
                ></canvas>
            </div>
        </div>        
    </div>
    <div class="col-lg-6 mt-5 mt-lg-0">
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
