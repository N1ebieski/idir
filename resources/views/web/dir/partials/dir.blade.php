<div class="mb-5 {{ $dir->group->border ?? null }}">
    <h2 class="h5 border-bottom pb-2">
        {!! $dir->link !!}
    </h2>
    <div class="d-flex mb-2">
        <small class="mr-auto">{{ trans('icore::default.created_at_diff') }}: {{ $dir->created_at_diff }}</small>
        <small class="ml-auto">
            <input id="star-rating{{ $dir->id }}" name="star-rating{{ $dir->id }}" 
            value="{{ $dir->sum_rating }}" data-stars="5" data-display-only="true"
            data-size="xs" class="rating-loading">
        </small>
    </div>
    <div class="text-break">{!! $dir->less_content_html !!}</div>
</div>  
