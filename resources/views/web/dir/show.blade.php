@extends(config('idir.layout') . '::web.layouts.layout', [
    'title' => [
        $dir->title,
        $comments->currentPage() > 1 ?
            trans('icore::pagination.page', ['num' => $comments->currentPage()])
            : null
    ],
    'desc' => [$dir->short_content],
    'keys' => [$dir->tag_list],
    'og' => [
        'title' => $dir->title,
        'desc' => $dir->short_content,
        'image' => $dir->isUrl() ? $dir->thumbnail_url : null
    ]
])

@section('breadcrumb')
<li class="breadcrumb-item">
    <a 
        href="{{ route('web.dir.index') }}" 
        title="{{ trans('idir::dirs.route.index') }}"
    >
        {{ trans('idir::dirs.route.index') }}
    </a>
</li>
<li class="breadcrumb-item active" aria-current="page">
    {{ $dir->title }}
</li>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 order-2">
            <div class="mb-5">
                <div class="d-flex border-bottom mb-2 justify-content-between">
                    <h1 class="h4">
                        {{ $dir->title }}
                    </h1>
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
                </div>
                <div class="mb-3">
                    {!! $dir->content_as_html !!}
                </div>
                <div class="d-flex mb-3">
                    @if ($dir->categories->isNotEmpty())
                    <small class="mr-auto">
                        {{ trans('icore::categories.categories.label') }}:
                        @foreach ($dir->categories as $category)
                        <a 
                            href="{{ route('web.category.dir.show', [$category->slug]) }}"
                            title="{{ $category->name }}"
                        >
                            {{ $category->name }}
                        </a>{{ (!$loop->last) ? ', ' : '' }}
                        @endforeach
                    </small>
                    @endif
                    @if ($dir->tags->isNotEmpty())
                    <small class="ml-auto text-right">
                        {{ trans('idir::dirs.tags.label') }}:
                        @foreach ($dir->tags as $tag)
                        <a 
                            href="{{ route('web.tag.dir.show', [$tag->normalized]) }}"
                            title="{{ $tag->name }}"
                        >
                            {{ $tag->name }}
                        </a>{{ (!$loop->last) ? ', ' : '' }}
                        @endforeach
                    </small>
                    @endif
                </div>
                @if (!is_null($dir->group->fields->firstWhere('type', 'map')))
                <div class="mb-3">
                    <x-idir::map.dir.map-component
                        :dir="$dir"
                    />
                </div>
                @endif
                @if ($related->isNotEmpty())
                <h3 class="h5">
                    {{ trans('idir::dirs.related') }}
                </h3>
                <ul class="list-group list-group-flush mb-3">
                    @foreach ($related as $rel)
                    <li class="list-group-item">
                        <a 
                            href="{{ route('web.dir.show', [$rel->slug]) }}"
                            title="{{ $rel->title }}"
                        >
                            {{ $rel->title }}
                        </a>
                    </li>
                    @endforeach
                </ul>
                @endif                
                <h3 class="h5 border-bottom pb-2" id="comments">
                    {{ trans('icore::comments.comments') }}
                </h3>
                <div id="filter-content">
                    @if ($comments->isNotEmpty())
                        @include('icore::web.comment.partials.filter')
                    @endif
                    <div id="comment">
                        @auth
                        @canany(['web.comments.create', 'web.comments.suggest'])
                        @include('icore::web.comment.create', [
                            'model' => $dir,
                            'parent_id' => 0
                        ])
                        @endcanany
                        @else
                        <a 
                            href="{{ route('login') }}" 
                            title="{{ trans('icore::comments.log_to_comment') }}"
                        >
                            {{ trans('icore::comments.log_to_comment') }}
                        </a>
                        @endauth
                    </div>
                    @if ($comments->isNotEmpty())
                    <div id="infinite-scroll">
                        @foreach ($comments as $comment)
                            @include('icore::web.comment.partials.comment', [
                                'comment' => $comment
                            ])
                        @endforeach
                        @include('icore::web.partials.pagination', [
                            'items' => $comments,
                            'fragment' => 'comments'
                        ])
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4 order-1">
            @if ($dir->isUrl())
            <div>
                <img 
                    src="{{ $dir->thumbnail_url }}" 
                    class="img-fluid border mx-auto d-block"
                    alt="{{ $dir->title }}"
                >
            </div>
            @endif
            <div class="list-group list-group-flush mb-3">   
                <div class="list-group-item">
                    <label for="star-rating" class="float-left mt-2 mr-2">
                        {{ trans('idir::dirs.rating') }}:
                    </label>
                    <input 
                        id="star-rating" 
                        name="star-rating" 
                        data-route="{{ route('web.rating.dir.rate', [$dir->id]) }}"
                        value="{{ $dir->sum_rating }}" 
                        data-stars="5" 
                        data-step="1"
                        data-size="sm" 
                        data-container-class="float-right ml-auto"
                        @auth
                        data-user-value="{{ $ratingUserValue }}"
                        data-show-clear="{{ $ratingUserValue ? true : false }}"
                        @else
                        data-display-only="true"
                        @endauth
                        class="rating-loading d-none" 
                        data-language="{{ config('app.locale') }}"
                    >
                </div>
                @if ($dir->isUrl())
                <div class="list-group-item">
                    <div class="float-left mr-2">
                        {{ trans('idir::dirs.url') }}:
                    </div>
                    <div class="float-right">
                        {!! $dir->url_as_link !!}
                    </div>
                </div>
                @endif
                @if ($dir->relationLoaded('stats'))
                @foreach ($dir->stats as $stat)
                <div class="list-group-item">
                    <div class="float-left mr-2">
                        {{ trans("icore::stats.{$stat->slug}") }}:
                    </div>
                    <div class="float-right">
                        {{ $stat->pivot->value }}
                    </div>
                </div>
                @endforeach
                @if ($statCtr > 0)
                <div class="list-group-item">
                    <div class="float-left mr-2">
                        CTR:
                    </div>
                    <div class="float-right">
                        {{ $statCtr }}%
                    </div>
                </div>
                @endif
                @endif
                @if ($dir->group->fields->isNotEmpty())
                @foreach ($dir->group->fields->where('type', '!=', 'map') as $field)
                @if ($value = optional($dir->fields->where('id', $field->id)->first())->decode_value)
                <div class="list-group-item">
                    <div class="float-left mr-2">
                        {{ $field->title }}@if (!$field->type->isSwitch()):@endif
                    </div>
                    <div class="float-right">
                    @switch ($field->type)
                        @case (Field\Type::INPUT)
                        @case (Field\Type::TEXTAREA)
                        @case (Field\Type::SELECT)
                            {{ $value }}
                            @break;

                        @case (Field\Type::MULTISELECT)
                        @case (Field\Type::CHECKBOX)
                            {{ implode(', ', $value) }}
                            @break;

                        @case (Field\Type::REGIONS)
                            {{ implode(', ', $dir->regions->pluck('name')->toArray()) }}
                            @break;

                        @case (Field\Type::IMAGE)
                            <img class="img-fluid" src="{{ app('filesystem')->url($value) }}">
                            @break;
                     @endswitch
                    </div>
                </div>
                @endif   
                @endforeach
                @endif
                <div class="list-group-item">
                    <a 
                        href="#" 
                        data-toggle="modal" 
                        data-target="#link-modal"
                        title="{{ trans('idir::dirs.link_dir_page') }}"
                    >
                        <i class="fas fa-fw fa-link"></i>
                        <span>{{ trans('idir::dirs.link_dir_page') }}</span>
                    </a>
                </div>                
                @if (isset($dir->user->email) && app('router')->has('web.contact.dir.show'))
                <div class="list-group-item">
                @auth
                    <a 
                        href="#" 
                        data-route="{{ route('web.contact.dir.show', [$dir->id]) }}"
                        title="{{ trans('idir::contact.dir.route.show') }}"
                        data-toggle="modal" 
                        data-target="#contact-modal" 
                        class="show-contact"
                    >
                        <i class="fas fa-fw fa-paper-plane"></i>
                        <span>{{ trans('idir::contact.dir.route.show') }}</span>
                    </a>
                @else
                    <a 
                        href="{{ route('login') }}" 
                        title="{{ trans('idir::contact.dir.log_to_contact') }}"
                    >
                        <i class="fas fa-fw fa-paper-plane"></i>
                        <span> {{ trans('idir::contact.dir.log_to_contact') }}</span>
                    </a>
                @endauth
                </div>
                @endif           
                @can('web.dirs.edit')
                @can('edit', $dir)
                <div class="list-group-item">
                    <a 
                        href="{{ route('web.dir.edit_1', [$dir->id]) }}"
                        title="{{ trans('idir::dirs.premium_dir') }}"
                    >
                        <i class="fas fa-fw fa-edit"></i>
                        <span>{{ trans('idir::dirs.premium_dir') }}</span>
                    </a>
                </div>
                @endcan
                @endcan
                <div class="list-group-item">
                    <a 
                        href="#" 
                        data-route="{{ route('web.report.dir.create', [$dir->id]) }}"
                        title="{{ trans('icore::reports.route.create') }}"
                        data-toggle="modal" 
                        data-target="#create-report-modal" 
                        class="createReport"
                    >
                        <i class="fas fa-fw fa-exclamation-triangle"></i>
                        <span>{{ trans('icore::reports.route.create') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@component('icore::web.partials.modal')
@slot('modal_id', 'link-modal')
@slot('modal_title')
<i class="fas fa-link"></i>
<span> {{ trans('idir::dirs.link_dir_page') }}</span>
@endslot
@slot('modal_body')
<div 
    class="form-group clipboard position-relative"
    data-lang="{{ config('app.locale') }}"
>
    <textarea class="form-control" name="dir" rows="5" readonly>{{ $dir->link_as_html }}</textarea>
</div>
@endslot
@endcomponent

@component('icore::web.partials.modal')
@slot('modal_id', 'create-report-modal')
@slot('modal_title')
<i class="fas fa-exclamation-triangle"></i>
<span> {{ trans('icore::reports.route.create') }}</span>
@endslot
@endcomponent

@auth
@component('icore::web.partials.modal')
@slot('modal_id', 'contact-modal')
@slot('modal_size', 'modal-lg')
@slot('modal_title')
<i class="fas fa-paper-plane"></i>
<span> {{ trans('idir::contact.dir.route.show') }}</span>
@endslot
@endcomponent
@endauth

@endsection

@if (!empty(config('icore.captcha.driver')))
@php
app(\N1ebieski\ICore\View\Components\CaptchaComponent::class)->render()->render();
@endphp
@endif
