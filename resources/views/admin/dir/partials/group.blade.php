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
        <li class="list-group-item">
            {{ __($privilege->name) }}
        </li>
        @endforeach
        @endif
        <li class="list-group-item">
            {{ strtolower(trans('idir::groups.apply_status')) }}: {{ trans("idir::groups.apply_status_{$group->apply_status}") }}
        </li>
        <li class="list-group-item">
            {{ strtolower(trans('idir::groups.max_cats')) }}: {{ $group->max_cats }}
        </li>
        <li class="list-group-item">
            {{ strtolower(trans('idir::groups.url')) }}: {{ trans("idir::groups.url_{$group->url}") }}
        </li>
        <li class="list-group-item">
            {{ strtolower(trans('idir::groups.backlink')) }}: {{ trans("idir::groups.backlink_{$group->backlink}") }}
        </li>
        <li class="list-group-item">
            {{ strtolower(trans('idir::groups.price')) }}:
            <span class="font-weight-bold">
                {{ $group->prices->isNotEmpty() ? trans('idir::groups.price_from', [
                    'price' => $group->prices->sortBy('price')->first()->price,
                    'days' => $days = $group->prices->sortBy('price')->first()->days,
                    'limit' => $days !== null ? strtolower(trans('idir::groups.days')) : strtolower(trans('idir::groups.unlimited'))
                ]) : trans('idir::groups.payment_0') }}
            </span>
        </li>
    </ul>
</div>