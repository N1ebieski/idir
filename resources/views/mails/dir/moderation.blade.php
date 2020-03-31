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

<h2>
    <span>{{ trans('idir::dirs.latest') }}: </span>
    @if ($dirs_inactive_count > 0)
    <span>
        <a style="color:orange" href="{{ route('admin.dir.index', ['filter[status]' => '0']) }}">
            ({{ trans('icore::filter.inactive') }}: {{ $dirs_inactive_count }})
        </a>
    </span>
    @endif
    @if ($dirs_reported_count > 0)
    <span>
        <a style="color:red" href="{{ route('admin.dir.index', ['filter[report]' => '1']) }}">
            ({{ trans('icore::filter.report.1') }}: {{ $dirs_reported_count }})
        </a>
    </span>
    @endif    
</h2>
@foreach ($dirs as $dir)
<p>
    <span>
        <a style="color:{{ $dir->isActive() ? 'green' : 'orange' }}" 
        href="{{ route('admin.dir.index', [
            'filter[search]' => '"' . $dir->title . '"' . ($dir->isUrl() ? ' "' . $dir->url . '"' : null)
        ]) }}">
            {{ $dir->title }}
        </a>
    </span>
    @if ($dir->group->prices->isNotEmpty() && $dir->payments->isNotEmpty())
    <span style="background-color:yellow;color:initial">
        <b>({{ trans('idir::groups.payment.1') }})</b>
    </span>
    @endif 
</p>       
<p>{{ $dir->short_content }}...</p>
@endforeach

@endcomponent
