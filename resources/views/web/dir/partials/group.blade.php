<div class="card-header">
    {{ $group->name }}
</div>
<div>
    @if (!empty($group->desc))
    <div class="card-body flex-wrap">
        <p class="card-text">{{ $group->desc }}</p>
    </div>
    @endif
    <ul class="list-group list-group-flush">
        @if ($group->privileges->isNotEmpty())
        @foreach ($group->privileges as $privilege)
        <li class="list-group-item">{{ __($privilege->name) }}</li>
        @endforeach
        @endif
        @if ($group->days > 0)
        <li class="list-group-item">
            {{ strtolower(trans('idir::groups.days')) }}: {{ $group->days }}
        </li>
        @endif
        <li class="list-group-item">
            {{ strtolower(trans('idir::groups.max_cats')) }}: {{ $group->max_cats }}
        </li>
        <li class="list-group-item">
            {{ strtolower(trans('idir::groups.url')) }}: {{ trans("idir::groups.url_{$group->url}") }}
        </li>
        <li class="list-group-item">
            {{ strtolower(trans('idir::groups.backlink')) }}: {{ trans("idir::groups.backlink_{$group->backlink}") }}
        </li>
    </ul>
</div>
