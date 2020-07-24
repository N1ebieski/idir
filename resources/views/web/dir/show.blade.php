@extends(config('idir.layout') . '::web.layouts.layout', [
    'title' => [
        $dir->title,
        $comments->currentPage() > 1 ?
            trans('icore::pagination.page', ['num' => $comments->currentPage()])
            : null
    ],
    'desc' => [$dir->short_content],
    'keys' => [$dir->tagList],
    'og' => [
        'title' => $dir->title,
        'desc' => $dir->short_content,
        'image' => $dir->url !== null ? $dir->thumbnail_url : null
    ]
])

@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="{{ route('web.dir.index') }}" title="{{ trans('idir::dirs.route.index') }}">
        {{ trans('idir::dirs.route.index') }}
    </a>
</li>
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
                </div>
                <div class="mb-3">{!! $dir->content_as_html !!}</div>
                <div class="d-flex mb-3">
                    @if ($dir->categories->isNotEmpty())
                    <small class="mr-auto">{{ trans('icore::categories.categories.label') }}:
                        @foreach ($dir->categories as $category)
                        <a href="{{ route('web.category.dir.show', [$category->slug]) }}"
                        title="{{ $category->name }}">
                            {{ $category->name }}
                        </a>{{ (!$loop->last) ? ', ' : '' }}
                        @endforeach
                    </small>
                    @endif
                    @if ($dir->tags->isNotEmpty())
                    <small class="ml-auto text-right">{{ trans('idir::dirs.tags.label') }}:
                        @foreach ($dir->tags as $tag)
                        <a href="{{ route('web.tag.dir.show', [$tag->normalized]) }}"
                        title="{{ $tag->name }}">
                            {{ $tag->name }}
                        </a>{{ (!$loop->last) ? ', ' : '' }}
                        @endforeach
                    </small>
                    @endif
                </div>
                <div class="mb-3">
                    {{-- @render('idir::map.dir.mapComponent', [
                        'dir' => $dir,
                        'address_marker_pattern' => [[4]]
                    ]) --}}
                </div>
                @if ($related->isNotEmpty())
                <h3 class="h5">{{ trans('idir::dirs.related') }}</h3>
                <ul class="list-group list-group-flush mb-3">
                    @foreach ($related as $rel)
                    <li class="list-group-item">
                        <a href="{{ route('web.dir.show', [$rel->slug]) }}"
                        title="{{ $rel->title }}">
                            {{ $rel->title }}
                        </a>
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
                        @canany(['web.comments.create', 'web.comments.suggest'])
                        @include('icore::web.comment.create', ['model' => $dir, 'parent_id' => 0])
                        @endcanany
                        @else
                        <a href="{{ route('login') }}" title="{{ trans('icore::comments.log_to_comment') }}">
                            {{ trans('icore::comments.log_to_comment') }}
                        </a>
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
            </div>
        </div>
        <div class="col-md-4 order-1">
            @if ($dir->url !== null)
            <div>
                <img src="{{ $dir->thumbnail_url }}" class="img-fluid border mx-auto d-block"
                alt="{{ $dir->title }}">
            </div>
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
                    class="rating-loading" data-language="{{ config('app.locale') }}">
                </div>
                @if ($dir->url !== null)
                <div class="list-group-item">
                    <div class="float-left mr-2">{{ trans('idir::dirs.url') }}:</div>
                    <div class="float-right">{{ $dir->url_as_host }}</div>
                </div>
                @endif
                @if ($dir->group->fields->isNotEmpty())
                @foreach ($dir->group->fields->where('type', '!=', 'map') as $field)
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
                        {{-- {{ dd(app())}} --}}
                            <img class="img-fluid" src="{{ app('filesystem')->url($value) }}">
                            @break;
                     @endswitch
                    </div>
                </div>
                @endif   
                @endforeach
                @endif
                <div class="list-group-item">
                    <a href="#" data-toggle="modal" data-target="#linkModal"
                    title="{{ trans('idir::dirs.link_dir_page') }}">
                        <i class="fas fa-link"></i>
                        <span>{{ trans('idir::dirs.link_dir_page') }}</span>
                    </a>
                </div>                
                @if (isset($dir->user->email) && app('router')->has('web.contact.dir.show'))
                <div class="list-group-item">
                @auth
                    <a href="#" data-route="{{ route('web.contact.dir.show', [$dir->id]) }}"
                    title="{{ trans('idir::contact.dir.route.show') }}"
                    data-toggle="modal" data-target="#contactModal" class="showContact">
                        <i class="fas fa-paper-plane"></i>
                        <span>{{ trans('idir::contact.dir.route.show') }}</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" title="{{ trans('idir::contact.dir.log_to_contact') }}">
                        <i class="fas fa-paper-plane"></i>
                        <span> {{ trans('idir::contact.dir.log_to_contact') }}</span>
                    </a>
                @endauth
                </div>
                @endif           
                @can('web.dirs.edit')
                @can('edit', $dir)
                <div class="list-group-item">
                    <a href="{{ route('web.dir.edit_1', [$dir->id]) }}"
                    title="{{ trans('idir::dirs.premium_dir') }}">
                        <i class="fas fa-edit"></i>
                        <span>{{ trans('idir::dirs.premium_dir') }}</span>
                    </a>
                </div>
                @endcan
                @endcan
                <div class="list-group-item">
                @auth
                    <a href="#" data-route="{{ route('web.report.dir.create', [$dir->id]) }}"
                    title="{{ trans('icore::reports.route.create') }}"
                    data-toggle="modal" data-target="#createReportModal" class="createReport">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>{{ trans('icore::reports.route.create') }}</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" title="{{ trans('icore::reports.log_to_report') }}">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span> {{ trans('icore::reports.log_to_report') }}</span>
                    </a>
                @endauth  
                </div>                 
            </div>
        </div>
    </div>
</div>

@component('icore::web.partials.modal')
@slot('modal_id', 'linkModal')
@slot('modal_title')
<i class="fas fa-link"></i>
<span> {{ trans('idir::dirs.link_dir_page') }}</span>
@endslot
@slot('modal_body')
<div class="form-group">
    <textarea class="form-control" name="dir" rows="5" readonly>{{ $dir->link_as_html }}</textarea>
</div>
@endslot
@endcomponent

@auth
@component('icore::web.partials.modal')
@slot('modal_id', 'createReportModal')
@slot('modal_title')
<i class="fas fa-exclamation-triangle"></i>
<span> {{ trans('icore::reports.route.create') }}</span>
@endslot
@endcomponent

@component('icore::web.partials.modal')
@slot('modal_id', 'contactModal')
@slot('modal_size', 'modal-lg')
@slot('modal_title')
<i class="fas fa-paper-plane"></i>
<span> {{ trans('idir::contact.dir.route.show') }}</span>
@endslot
@endcomponent
@endauth

@endsection

@php
App::make(N1ebieski\ICore\View\Components\CaptchaComponent::class)->toHtml()->render();
@endphp