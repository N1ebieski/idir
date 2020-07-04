@if ($dirs->isNotEmpty())
<div id="carousel" class="carousel slide pb-3 mt-3" data-ride="carousel">
    <div class="carousel-inner">
        @foreach ($dirs as $dir)
        <div class="carousel-item {{ $loop->first ? 'active' : null }}">
            <div class="row">
                <div class="col-md-{{ $dir->isUrl() ? '8' : '12' }} order-2">
                    <h2 class="h5 border-bottom pb-2 my-2">
                        <a href="{{ route('web.dir.show', [$dir->slug]) }}" title="{{ $dir->title }}">
                            {{ $dir->title }}
                        </a>
                    </h2>
                    <div class="d-flex mb-2">
                        <small class="mr-auto">
                            {{ trans('icore::default.created_at_diff') }}: {{ $dir->created_at_diff }}
                        </small>
                        <small class="ml-auto">
                            <input id="star-rating{{ $dir->id }}" name="star-rating{{ $dir->id }}" 
                            value="{{ $dir->sum_rating }}" data-stars="5" data-display-only="true"
                            data-size="xs" class="rating-loading">
                        </small>
                    </div>
                    <div class="text-break">{{ $dir->short_content }}...</div>
                </div>
                @if ($dir->isUrl())
                <div class="col-md-4 order-1">
                    <img src="{{ $dir->thumbnail_url }}" class="img-fluid border mx-auto d-block"
                    alt="{{ $dir->title }}">
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    <div class="bottom-controls d-block w-100 position-relative">
        <a class="carousel-control-prev" href="#carousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <ol class="carousel-indicators">
            @foreach ($dirs as $dir)
            <li data-target="#carousel" data-slide-to="{{ $loop->index }}" class="{{ $loop->first ? 'active' : null }}"></li>
            @endforeach
        </ol>
        <a class="carousel-control-next" href="#carousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>
</div>
@endif