@if ($comments->isNotEmpty())
<h3 class="h5 border-bottom pb-2 mb-3">{{ trans('icore::comments.latest') }}</h3>
<div>
    @foreach ($comments as $comment)
        @include('idir::web.components.comment.dir.partials.comment')
    @endforeach
</div>
@endif
