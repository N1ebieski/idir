@if (collect($tags)->isNotEmpty())
<h3 class="h5">{{ trans('icore::tags.popular') }}</h3>
<div class="mb-3">
    @foreach ($tags as $tag)
        <a href="{{ route('web.tag.dir.show', $tag->normalized) }}" class="h{{ rand(1, 6) }}">
            {{ $tag->name }}
        </a>
        {{ (!$loop->last) ? ', ' : '' }}
    @endforeach
</div>
@endif