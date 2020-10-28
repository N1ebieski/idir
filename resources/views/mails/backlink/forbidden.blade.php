@component('mail::message')
{{-- Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
@if (isset($level) && $level === 'error')
# @lang('Whoops!')
@else
# @lang(trans('icore::auth.hello').'!')
@endif
@endif

{!! trans('idir::backlinks.mail.forbidden.info', [
    'dir_link' => $dirBacklink->dir->title_as_link,
    'dir_page' => route('web.dir.show', [$dirBacklink->dir->slug]),
    'dir_url' => $dirBacklink->dir->url_as_link
]) !!}

<p>{{ trans('idir::backlinks.mail.forbidden.result') }}</p>

<p>{{ trans('idir::backlinks.mail.forbidden.solve') }}</p>

<ul>
    <li>IP: {{ request()->server('SERVER_ADDR') }}</li>
    <li>User-Agent: iDir v{{ config('idir.version') }} {{ parse_url(config('app.url'), PHP_URL_HOST) }}</li>
</ul>

@endcomponent