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

{{ trans('idir::backlinks.mail.not_found.info', ['attempt' => $dirBacklink->attempts]) }}

{{ trans('idir::backlinks.mail.not_found.backlink') }}

<div>
    <textarea name="backlink" rows="3" cols="50" readonly>{{ $dirBacklink->link->linkAsHtml }}</textarea>
</div>
<br>
{{ trans('idir::backlinks.mail.not_found.edit_dir') }}

@component('mail::button', [
    'url' => route('web.dir.edit_1', [
        $dirBacklink->dir->id
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
        'actionURL' => route('web.dir.edit_1', [$dirBacklink->dir->id])
    ]
)
@endcomponent
@endcomponent
