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

{{ trans('idir::dirs.mail.reminder.info', [
    'dir_link' => $dir->title_as_link,
    'dir_page' => route('web.dir.show', [$dir->slug]),
    'group' => $dir->group->name
]) }}

{{ $result }}

{{ trans('idir::dirs.mail.reminder.renew_dir') }}

@component('mail::button', [
    'url' => route('web.dir.edit_renew', [
        $dir->id
    ]),
    'color' => 'primary'
])
{{ trans('idir::dirs.page.edit.renew') }}
@endcomponent

{{-- Subcopy --}}
@component('mail::subcopy')
@lang(
    "If you’re having trouble clicking the \":actionText\" button, copy and paste the URL below\n".
    'into your web browser: [:actionURL](:actionURL)',
    [
        'actionText' => trans('idir::dirs.page.edit.renew'),
        'actionURL' => route('web.dir.edit_renew', [$dir->id])
    ]
)
@endcomponent
@endcomponent
