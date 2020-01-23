<div class="mb-5">
    <h2 class="h5 border-bottom pb-2">
        <a href="{{ route('web.dir.show', [$dir->slug]) }}">{{ $dir->title }}</a>
    </h2>
    <div class="d-flex mb-2">
        <small class="mr-auto">{{ trans('icore::default.created_at_diff') }}: {{ $dir->created_at_diff }}</small>
    </div>
    <div class="text-break">{{ $dir->short_content }}...</div>
</div>
