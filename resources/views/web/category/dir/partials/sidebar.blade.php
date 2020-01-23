{{-- <h3 class="h5">{{ trans('icore::pages.pages') }}</h3>
<div class="list-group list-group-flush mb-3">
    @if ($page->relationLoaded('ancestors'))
        @include('icore::web.page.partials.pages', ['pages' => $page->ancestors])
    @endif
    <div class="list-group-item d-flex justify-content-between align-items-center">
        <a href="{{ route('web.page.show', $page->slug) }}"
        class="@isUrl(route('web.page.show', $page->slug), 'font-weight-bold')">
            {{ str_repeat('-', $page->real_depth) }} {{ $page->title }}
        </a>
    </div>
    @if ($page->relationLoaded('childrensRecursiveWithAllRels'))
        @include('icore::web.page.partials.pages', ['pages' => $page->childrensRecursiveWithAllRels])
    @endif
</div> --}}
@render('idir::tag.dir.tagComponent', ['limit' => 25, 'cats' => $catsAsArray['self'] ?? null])