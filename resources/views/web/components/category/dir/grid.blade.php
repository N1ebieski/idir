@if ($categories->isNotEmpty())
<h3 class="h5">{{ trans('icore::categories.categories.label') }}</h3>
<div class="row">
    @foreach ($categories as $category)
    <div class="col-xl-{{ floor(12/$cols) }} col-md-6 col-12 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('web.category.dir.show', $category->slug) }}" title="{{ $category->name }}"
                    class="{{ $isUrl(route('web.category.dir.show', $category->slug), 'font-weight-bold') }}">
                        <span>{{ $category->name }}</span>
                    </a>
                    @if (isset($category->nested_morphs_count))
                    <span class="badge badge-primary badge-pill align-self-center">
                        {{ $category->nested_morphs_count }}
                    </span>
                    @endif
                </div>
            </div>
            @if ($category->childrens->isNotEmpty())
            <div class="d-flex">
                @if ($category_icon === true && !empty($category->icon))
                <div style="width:5rem" class="align-self-center text-center">
                    <i class="{{ $category->icon }} p-3" style="font-size:3rem"></i>
                </div>
                @endif
                <ul class="list-group list-group-flush flex-grow-1">
                @foreach ($category->childrens as $children)
                    <li class="list-group-item d-flex justify-content-between">
                        <a href="{{ route('web.category.dir.show', $children->slug) }}" title="{{ $children->name }}"
                        class="{{ $isUrl(route('web.category.dir.show', $children->slug), 'font-weight-bold') }}">
                            <span>{{ $children->name }}</span>
                        </a>
                        @if (isset($children->morphs_count))
                        <span class="badge badge-primary badge-pill align-self-center">
                            {{ $children->morphs_count }}
                        </span>
                        @endif
                    </li>
                @endforeach
                </ul>
            </div>
            @endif
        </div>
    </div>
    @endforeach
</div>
@endif