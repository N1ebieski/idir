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
    <textarea name="backlink" rows="3" cols="50" readonly>{{ $backlinkAsLink }}</textarea>
</div>
<br>
{{ trans('idir::backlinks.edit_dir_info') }}

@component('mail::button', ['url' => $actionUrl, 'color' => 'primary'])
{{ $actionText }}
@endcomponent

{{-- Subcopy --}}
@isset($actionText)
@component('mail::subcopy')
@lang(
    "If you’re having trouble clicking the \":actionText\" button, copy and paste the URL below\n".
    'into your web browser: [:actionURL](:actionURL)',
    [
        'actionText' => $actionText,
        'actionURL' => $actionUrl,
    ]
)
@endcomponent
@endisset
@endcomponent
