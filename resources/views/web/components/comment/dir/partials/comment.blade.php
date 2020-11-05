<div class="mb-3">
    <h4 class="h6 border-bottom pb-2">
        <a 
            href="{{ route('web.dir.show', [$comment->morph->slug, '#comments']) }}"
            title="{{ $comment->morph->title }}"
        >
            {{ $comment->morph->title }}
        </a>
    </h4>
    <div class="d-flex mb-2">
        <small class="mr-auto">
            {{ trans('icore::comments.created_at_diff') }}: {{ $comment->created_at_diff }}
        </small>
        @if ($comment->user)
        <small class="ml-auto text-right">
            {{ trans('icore::comments.author') }}: {{ $comment->user->name }}
        </small>
        @endif
    </div>
    <div>
        {{ $comment->content }}
    </div>
</div>
