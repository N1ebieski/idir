@if ($categories->isNotEmpty())
<h3 class="h5">{{ trans('icore::categories.categories.label') }}</h3>
<div class="list-group list-group-flush mb-3">
    @foreach ($categories as $category)
    <div class="list-group-item d-flex justify-content-between align-items-center">
        <a href="{{ route('web.category.dir.show', $category->slug) }}"
        title="{{ $category->name }}"
        class="{{ $isUrl(route('web.category.dir.show', $category->slug), 'font-weight-bold') }}">
            @if ($icon === true && !empty($category->icon))
            <i class="{{ $category->icon }}"></i>
            @endif
            <span>{{ $category->name }}</span>
        </a>
        <span class="badge badge-primary badge-pill">{{ $category->nested_morphs_count }}</span>
    </div>
    @endforeach
</div>
@endif