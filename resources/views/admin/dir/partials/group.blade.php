<div class="card-header">
    {{ $group->name }}
</div>
<div>
    <div class="card-body flex-wrap">
        @if (!empty($group->desc))
        <p class="card-text">{{ $group->desc }}</p>
        @endif
        @if (($price = $group->prices->sortBy('price')->first()) && $price->discount_price)
        <p class="mb-0">
            <span class="badge bg-success text-white">-{{ $price->discount }}%</span>
            <span><s>{{ $price->regular_price }} {{ config("services.{$driverByType($price->type)}.{$price->type}.currency", 'PLN') }}</s></span>
        </p>
        @endif
        <p class="card-text h4">
            {!! !is_null($price) ? trans('idir::prices.price_from', [
                'price' => $price->price,
                'days' => $days = $price->days,
                'limit' => !is_null($days) ? 
                    mb_strtolower(trans('idir::prices.days')) 
                    : mb_strtolower(trans('idir::prices.unlimited')),
                'currency' => config("services.{$driverByType($price->type)}.{$price->type}.currency", 'PLN')
            ]) : trans('idir::groups.payment.0') !!}
        </p>
    </div>
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
    </ul>
</div>
