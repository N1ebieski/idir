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

{!! trans('idir::dirs.mail.delete.info', ['dir_link' => $dir->title_as_link]) !!}

{{ $reason !== null ? trans('idir::dirs.reason.label') . ': ' . $reason : null }}

{{ trans('idir::dirs.content') }}: {{ $dir->content }}

@endcomponent
