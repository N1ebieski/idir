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

{!! trans('idir::dirs.mail.incorrect.info', ['dir_link' => $dir->title_as_link]) !!}

{{ $reason !== null ? trans('idir::dirs.reason.label') . ': ' . $reason : null }}

{{ trans('idir::dirs.mail.incorrect.edit_dir') }}

@component('mail::button', [
    'url' => route('web.dir.edit_1', [
        $dir->id
    ]),
    'color' => 'primary'
])
{{ trans('idir::dirs.route.edit.index') }}
@endcomponent

{{-- Subcopy --}}
@component('mail::subcopy')
@lang(
    "If you’re having trouble clicking the \":actionText\" button, copy and paste the URL below\n".
    'into your web browser: [:actionURL](:actionURL)',
    [
        'actionText' => trans('idir::dirs.route.edit.index'),
        'actionURL' => route('web.dir.edit_1', [$dir->id])
    ]
)
@endcomponent
@endcomponent
