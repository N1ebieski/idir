@if ($dirs->isNotEmpty())
<h3 class="h5 pb-2 mb-3">
    {{ trans('idir::dirs.latest') }}
</h3>
<div class="row">
    @foreach ($dirs as $dir)
    <div class="col-xl-{{ floor(12/$cols) }} col-md-6 col-12 mb-4">
        <div class="card h-100">
            @if ($dir->url->isUrl())
            <div>
                <img 
                    data-src="{{ $dir->thumbnail_url }}" 
                    class="lazy img-fluid mx-auto d-block"
                    alt="{{ $dir->title }}"
                >
            </div>
            @endif
            <div class="card-body">
                <h3 class="h5 card-title">
                    <a 
                        href="{{ route('web.dir.show', [$dir->slug]) }}" 
                        title="{{ $dir->title }}"
                    >
                        {{ $dir->title }}
                    </a>                
                </h3>
                <p class="card-text text-break" style="word-break:break-word">
                    {{ $dir->content }}...
                </p>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif
