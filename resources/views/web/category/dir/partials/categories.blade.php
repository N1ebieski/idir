@foreach ($pages as $page)
    <div class="list-group-item d-flex justify-content-between align-items-center">
        @if (!empty($page->content))
        <a href="{{ route('web.page.show', $page->slug) }}"
        class="@isUrl(route('web.page.show', $page->slug), 'font-weight-bold')">
            {{ str_repeat('-', $page->real_depth) }} {{ $page->title }}
        </a>
        @else
            {{ str_repeat('-', $page->real_depth) }} {{ $page->title }}
        @endif
    </div>
    @if ($page->relationLoaded('childrensRecursiveWithAllRels'))
        @include('icore::web.page.partials.pages', ['pages' => $page->childrensRecursiveWithAllRels])
    @endif
@endforeach