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

{!! trans('idir::dir_status.mail.forbidden.info', [
    'dir_link' => $dirStatus->dir->title_as_link,
    'dir_page' => route('web.dir.show', [$dirStatus->dir->slug]),
    'dir_url' => $dirStatus->dir->url_as_link
]) !!}

<p>{{ trans('idir::dir_status.mail.forbidden.result') }}</p>

<p>{{ trans('idir::dir_status.mail.forbidden.solve') }}</p>

<ul>
    <li>IP: {{ request()->server('SERVER_ADDR') }}</li>
    <li>User-Agent: iDir v{{ config('idir.version') }} {{ parse_url(config('app.url'), PHP_URL_HOST) }}</li>
</ul>

@endcomponent