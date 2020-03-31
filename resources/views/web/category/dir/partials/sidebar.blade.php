@render('idir::region.category.regionComponent', [
    'region' => $region, 
    'category' => $category
])
<h3 class="h5">{{ trans('icore::categories.categories.label') }}</h3>
<div class="list-group list-group-flush mb-3">
    @if ($category->relationLoaded('ancestors'))
        @include('idir::web.category.dir.partials.categories', [
            'categories' => $category->ancestors
        ])
    @endif
    <div class="list-group-item d-flex justify-content-between align-items-center">
        <a href="{{ route('web.category.dir.show', [$category->slug, $region->slug]) }}"
        class="@isUrl(route('web.category.dir.show', [$category->slug, $region->slug]), 'font-weight-bold')">
            <span>{{ str_repeat('-', $category->real_depth) }}</span>
            @if (!empty($category->icon))
            <i class="{{ $category->icon }}"></i>
            @endif
            <span>{{ $category->name }}</span>            
        </a>
        <span class="badge badge-primary badge-pill">{{ $category->morphs_count }}</span>
    </div>
    @if ($category->relationLoaded('childrens'))
        @include('idir::web.category.dir.partials.categories', [
            'categories' => $category->childrens
        ])
    @endif
</div>
@render('idir::tag.dir.tagComponent', ['limit' => 25, 'cats' => $catsAsArray['self'] ?? null])