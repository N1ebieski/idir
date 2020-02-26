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

{!! trans('idir::dirs.mail.completed.info', [
    'dir_link' => $dir->title_as_link,
    'group' => $dir->group->name
]) !!}

{{ $result }}

{{ trans('idir::dirs.mail.completed.edit_dir') }}

@component('mail::button', [
    'url' => route('web.dir.edit_1', [
        $dir->id
    ]),
    'color' => 'primary'
])
{{ trans('idir::dirs.page.edit.index') }}
@endcomponent

{{-- Subcopy --}}
@component('mail::subcopy')
@lang(
    "If you’re having trouble clicking the \":actionText\" button, copy and paste the URL below\n".
    'into your web browser: [:actionURL](:actionURL)',
    [
        'actionText' => trans('idir::dirs.page.edit.index'),
        'actionURL' => route('web.dir.edit_1', [$dir->id])
    ]
)
@endcomponent
@endcomponent
