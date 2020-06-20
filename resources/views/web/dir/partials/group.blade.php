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
        <li class="list-group-item d-flex">
            <i class="fas fa-check my-auto text-success"></i>
            <div class="ml-3">{{ __($privilege->name) }}</div>
        </li>
        @endforeach
        @endif
        @if (!empty($group->border))
        <li class="list-group-item d-flex">
            <i class="fas fa-check my-auto text-success"></i>
            <div class="ml-3">{{ __('card of your site highlighted on listings') }}</div>
        </li>      
        @endif
        <li class="list-group-item d-flex">
            <i class="fas fa-check my-auto text-success"></i>
            <div class="ml-3">
                {{ mb_strtolower(trans('idir::groups.apply_status.label')) }}: {{ trans("idir::groups.apply_status.{$group->apply_status}") }}
            </div>
        </li>
        <li class="list-group-item d-flex">
            <i class="fas fa-check my-auto text-success"></i>
            <div class="ml-3">
                {{ mb_strtolower(trans('idir::groups.max_cats.label')) }}: {{ $group->max_cats }}
            </div>
        </li>
        <li class="list-group-item d-flex">
            <i class="fas fa-check my-auto text-success"></i>
            <div class="ml-3">
                {{ mb_strtolower(trans('idir::groups.url.label')) }}: {{ trans("idir::groups.url.{$group->url}") }}
            </div>
        </li>
        <li class="list-group-item d-flex">
            <i class="fas fa-check my-auto text-success"></i>
            <div class="ml-3">
                {{ mb_strtolower(trans('idir::groups.backlink.label')) }}: {{ trans("idir::groups.backlink.{$group->backlink}") }}
            </div>
        </li>
        <li class="list-group-item">
            {{ mb_strtolower(trans('idir::groups.price')) }}:
            <span class="font-weight-bold">
                {{ $group->prices->isNotEmpty() ? trans('idir::groups.price_from', [
                    'price' => $group->prices->sortBy('price')->first()->price,
                    'days' => $days = $group->prices->sortBy('price')->first()->days,
                    'limit' => $days !== null ? mb_strtolower(trans('idir::groups.days')) : mb_strtolower(trans('idir::groups.unlimited'))
                ]) : trans('idir::groups.payment.0') }}
            </span>
        </li>
    </ul>
</div>
