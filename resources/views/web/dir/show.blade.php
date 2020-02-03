@extends(config('idir.layout') . '::web.layouts.layout', [
    'title' => [
        $dir->title,
        // trans('icore::pagination.page', ['num' => $comments->currentPage()])
    ],
    // 'desc' => [$post->meta_desc],
    // 'keys' => [$post->tagList],
    // 'index' => (bool)$post->seo_noindex === true ? 'noindex' : 'index',
    // 'follow' => (bool)$post->seo_nofollow === true ? 'nofollow' : 'follow',
    // 'og' => [
    //     'title' => $post->meta_title,
    //     'desc' => $post->meta_desc,
    //     'image' => $post->first_image
    // ]
])

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('web.home.index') }}">{{ trans('icore::home.page.index') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('web.dir.index') }}">{{ trans('idir::dirs.page.index') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ $dir->title }}</li>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 order-2">
            <div class="mb-5">
                <h1 class="h4 border-bottom pb-2">{!! $dir->title_as_link !!}</h1>
                <div class="d-flex mb-2">
                    <small class="mr-auto">{{ trans('icore::default.created_at_diff') }}: {{ $dir->created_at_diff }}</small>
                    {{-- <small class="ml-auto text-right">{{ trans('icore::posts.author') }}: {{ $post->user->name ?? '' }}</small> --}}
                </div>
                <div class="mb-3">{!! $dir->content_html !!}</div>
                <div class="d-flex mb-3">
                    <small class="mr-auto">{{ trans('icore::categories.categories') }}:
                        @if ($dir->categories->isNotEmpty())
                        @foreach ($dir->categories as $category)
                        <a href="{{ route('web.category.dir.show', [$category->slug]) }}">{{ $category->name }}</a>
                        {{ (!$loop->last) ? ', ' : '' }}
                        @endforeach
                        @endif
                    </small>
                    <small class="ml-auto text-right">{{ trans('icore::dirs.tags') }}:
                        @if ($dir->tags->isNotEmpty())
                        @foreach ($dir->tags as $tag)
                        <a href="{{ route('web.tag.dir.show', [$tag->normalized]) }}">{{ $tag->name }}</a>
                        {{ (!$loop->last) ? ', ' : '' }}
                        @endforeach
                        @endif
                    </small>
                </div>
                <div class="mb-3">
                    @render('idir::map.dir.mapComponent', [
                        'dir' => $dir,
                        // 'address_marker' => ['Platynowa 15/22 80-041 Gdańsk'],
                        'address_marker_pattern' => [[2]]
                    ])
                </div>
                @if ($related->isNotEmpty())
                <h3 class="h5">{{ trans('idir::dirs.related') }}</h3>
                <ul class="list-group list-group-flush mb-3">
                    @foreach ($related as $rel)
                    <li class="list-group-item">
                        <a href="{{ route('web.dir.show', [$rel->slug]) }}">{{ $rel->title }}</a>
                    </li>
                    @endforeach
                </ul>
                @endif                
                <h3 class="h5 border-bottom pb-2" id="comments">{{ trans('icore::comments.comments') }}</h3>
                <div id="filterContent">
                    @if ($comments->isNotEmpty())
                        @include('icore::web.comment.partials.filter')
                    @endif
                    <div id="comment">
                        @auth
                        @canany(['create comments', 'suggest comments'])
                        @include('icore::web.comment.create', ['model' => $dir, 'parent_id' => 0])
                        @endcanany
                        @else
                        <a href="{{ route('login') }}">{{ trans('icore::comments.log_to_comment') }}</a>
                        @endauth
                    </div>
                    @if ($comments->isNotEmpty())
                    <div id="infinite-scroll">
                        @foreach ($comments as $comment)
                            @include('icore::web.comment.partials.comment', ['comment' => $comment])
                        @endforeach
                        @include('icore::web.partials.pagination', ['items' => $comments, 'fragment'
                        => 'comments'])
                    </div>
                    @endif
                </div>
                {{--                 
                @component('icore::web.partials.modal')
                @slot('modal_id', 'createReportModal')
                @slot('modal_title')
                {{ trans('icore::reports.page.create') }}
                @endslot
                @endcomponent
                @endif --}}
            </div>
        </div>
        <div class="col-md-4 order-1">
            @if ($dir->url !== null)
            <img src="{{ $dir->thumbnail_url }}" class="img-fluid border"
            alt="{{ $dir->title }}">
            @endif
            <div class="list-group list-group-flush mb-3">   
                <div class="list-group-item">
                    <label for="star-rating" class="float-left mt-2 mr-2">{{ trans('idir::dirs.rating') }}:</label>
                    <input id="star-rating" name="star-rating" data-route="{{ route('web.rating.dir.rate', [$dir->id]) }}"
                    value="{{ $dir->sum_rating }}" data-stars="5" data-step="1"
                    data-size="sm" data-container-class="float-right ml-auto"
                    @auth
                    data-user-value="{{ $rating = optional($dir->ratings->where('user_id', auth()->user()->id)->first())->rating ?? false }}"
                    data-show-clear="{{ $rating ? true : false }}"
                    @else
                    data-display-only="true"
                    @endauth
                    class="rating-loading">
                </div>
                @if ($dir->url !== null)
                <div class="list-group-item">
                    <div class="float-left mr-2">{{ trans('idir::dirs.url') }}:</div>
                    <div class="float-right">{{ $dir->url_as_host }}</div>
                </div>
                @endif
                @if ($dir->group->fields->isNotEmpty())
                @foreach ($dir->group->fields as $field)
                @if ($value = optional($dir->fields->where('id', $field->id)->first())->decode_value)
                <div class="list-group-item">
                    <div class="float-left mr-2">{{ $field->title }}:</div>
                    <div class="float-right">
                    @switch ($field->type)
                        @case ('input')
                        @case ('textarea')
                        @case ('select')
                            {{ $value }}
                            @break;

                        @case ('multiselect')
                        @case ('checkbox')
                            {{ implode(', ', $value) }}
                            @break;

                        @case ('regions')
                            {{ implode(', ', $dir->regions->pluck('name')->toArray()) }}
                            @break;

                        @case ('image')
                            <img class="img-fluid" src="{{ Storage::url($value) }}">
                            @break;
                     @endswitch
                    </div>
                </div>
                @endif   
                @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
@endsection