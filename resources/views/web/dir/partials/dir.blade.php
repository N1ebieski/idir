<div class="mb-5 {{ $dir->group->border ?? null }}">
    <div class="d-flex justify-content-between">
        <h2 class="h5 border-bottom pb-2">
            {!! $dir->link !!}
        </h2>
        @can ('admin.dirs.view')
        <div>
            <a
                href="{{ route('admin.dir.index', ['filter[search]' => 'id:"' . $dir->id . '"']) }}"
                target="_blank"
                rel="noopener"
                title="{{ trans('icore::dirs.route.index') }}"
                class="badge badge-primary"
            >
                {{ trans('icore::default.admin') }}
            </a>
        </div>
        @endcan
    </div>
    <div class="d-flex mb-2">
        <small class="mr-auto">
            {{ trans('icore::default.created_at_diff') }}: {{ $dir->created_at_diff }}
        </small>
        <small class="ml-auto">
            <input 
                id="star-rating{{ $dir->id }}" 
                name="star-rating{{ $dir->id }}" 
                value="{{ $dir->sum_rating }}" 
                data-stars="5" 
                data-display-only="true"
                data-size="xs" 
                class="rating-loading" 
                data-language="{{ config('app.locale') }}"
            >
        </small>
    </div>
    <div class="text-break" style="word-break:break-word">
        {!! $dir->less_content_html !!}
    </div>
</div>
