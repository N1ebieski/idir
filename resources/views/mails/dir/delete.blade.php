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

{{ trans('idir::dirs.delete_info', ['dir' => $dir->title_as_link]) }}

{{ $reason !== null ? trans('idir::dirs.delete_reason') . ': ' . $reason : null }}

{{ trans('idir::dirs.content') }}: {{ $dir->content }}

@endcomponent
