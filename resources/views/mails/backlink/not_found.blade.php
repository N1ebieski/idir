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

{{ trans('idir::backlinks.not_found_info', ['attempt' => $dirBacklink->attempts]) }}

{{ trans('idir::backlinks.backlink_info') }}

<div>
    <textarea name="backlink" rows="3" cols="50" readonly>{{ $dirBacklink->link->linkAsHtml }}</textarea>
</div>
<br>
{{ trans('idir::backlinks.edit_dir_info') }}

@component('mail::button', [
    'url' => route('web.dir.edit_1', [
        $dirBacklink->dir->id
    ]),
    'color' => 'primary'
])
{{ trans('idir::backlinks.edit_dir') }}
@endcomponent

{{-- Subcopy --}}
@component('mail::subcopy')
@lang(
    "If you’re having trouble clicking the \":actionText\" button, copy and paste the URL below\n".
    'into your web browser: [:actionURL](:actionURL)',
    [
        'actionURL' => route('web.dir.edit_1', [$dirBacklink->dir->id])
    ]
)
@endcomponent
@endcomponent
