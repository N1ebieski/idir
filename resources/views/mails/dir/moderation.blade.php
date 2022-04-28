@inject('dir', 'N1ebieski\IDir\Models\Dir')
@inject('report', 'N1ebieski\ICore\Models\Report\Report')
@inject('payment', 'N1ebieski\IDir\Models\Payment\Payment')

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
        <a 
            style="color:orange" 
            href="{{ route('admin.dir.index', ['filter[status]' => Dir\Status::INACTIVE]) }}"
        >
            ({{ trans('icore::filter.inactive') }}: {{ $dirs_inactive_count }})
        </a>
    </span>
    @endif
    @if ($dirs_reported_count > 0)
    <span>
        <a 
            style="color:red" 
            href="{{ route('admin.dir.index', ['filter[report]' => Report\Reported::ACTIVE]) }}"
        >
            ({{ trans('icore::filter.report.' . Report\Reported::ACTIVE) }}: {{ $dirs_reported_count }})
        </a>
    </span>
    @endif    
</h2>
@foreach ($dirs as $dir)
<p>
    <span>
        <a 
            style="color:{{ $dir->status->isActive() ? 'green' : 'orange' }}" 
            href="{{ route('admin.dir.index', ['filter[search]' => 'id:"' . $dir->id . '"']) }}"
        >
            {{ $dir->title }}
        </a>
    </span>
    @if ($dir->group->prices->isNotEmpty() && $dir->payments->isNotEmpty())
    <span style="background-color:yellow;color:initial">
        <b>({{ trans('idir::groups.payment.' . Payment\Status::FINISHED) }})</b>
    </span>
    @endif 
</p>       
<p>{{ $dir->short_content }}...</p>
@endforeach

@endcomponent
