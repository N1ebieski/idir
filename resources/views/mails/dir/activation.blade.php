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

{!! trans('idir::dirs.mail.activation.info', [
    'dir_link' => $dir->title_as_link,
    'dir_page' => route('web.dir.show', [$dir->slug])
]) !!}

{{ trans('idir::dirs.link_dir_page') }}:

<div>
    <textarea name="dir" rows="5" cols="50" readonly>{{ $dir->link_as_html }}</textarea>
</div>

@endcomponent
