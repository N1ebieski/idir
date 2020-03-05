@extends(config('icore.layout') . '::web.layouts.layout')

@section('content')
@include('icore::web.partials.alerts-absolute')
<div class="jumbotron jumbotron-fluid m-0 background">
    <div class="container">
        <div class="w-md-75 mx-auto">
            <h1 class="display-4 text-white text-center">Lorem ipsum</h1>
            <p class="lead text-white text-center">Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
            <form id="searchForm" method="get" action="{{ route('web.search.index') }}" 
            class="justify-content-center search">
                <div class="input-group justify-content-center">          
                    <input type="text" class="border border-right-0 form-control-lg" 
                    id="typeahead" data-route="{{ route('web.search.autocomplete') }}"
                    placeholder="{{ trans('icore::search.search') }}" name="search">
                    <input type="hidden" name="source" value="dir">
                    <span class="input-group-append">
                        <button class="btn btn-outline-secondary bg-primary border-0" 
                        type="submit" disabled>
                            <i class="fa fa-search text-white"></i>
                        </button>
                    </span>
                </div>
            </form>
            <div class="text-white d-block mt-5">
                @render('idir::tag.dir.tagComponent', [
                    'limit' => 25,
                    'colors' => ['text-white']
                ])
            </div>
        </div>
    </div>
</div>
<div class="container">
    @render('idir::dir.carouselComponent')
    <div class="row">
        <div class="col-md-8 order-sm-1 order-md-2">
            @if ($dirs->isNotEmpty())
            <div>
                @foreach ($dirs as $dir)
                    @include('idir::web.dir.partials.dir')
                @endforeach
            </div>
            @else
            <p>{{ trans('icore::default.empty') }}</p>
            @endif
        </div>
        <div class="col-md-4 order-sm-2 order-md-1">
            @render('idir::category.dir.categoryComponent')
            @render('idir::comment.dir.latestComponent')
        </div>
    </div>
</div>
@endsection