<div 
    id="row{{ $price->id }}" 
    class="row border-bottom py-3 position-relative transition"
    data-id="{{ $price->id }}"
>
    <div class="col my-auto d-flex justify-content-between">
        <ul class="list-unstyled mb-0 pb-0">
            <li>
                <span>{{ trans('idir::prices.price') }}:</span>
                @if ($price->discount_price)
                <span class='badge bg-success text-white'>-{{ $price->discount }}%</span>
                <span><s>{{ $price->regular_price }} {{ config("services.{$driverByType($price->type)}.{$price->type}.currency", 'PLN') }}</s></span>
                @endif
                <span>{{ $price->price }} {{ config("services.{$driverByType($price->type)}.{$price->type}.currency", 'PLN') }}</span>
                <span>{{ trans('idir::prices.days') }}: {{ $price->isUnlimited() ? trans('idir::prices.unlimited') : $price->days }}</span>
                <span class="badge badge-primary">{{ strtolower(trans("idir::prices.payment.{$price->type}")) }}</span>
            </li>
            <li>
                <small>
                    <span>
                        {{ trans('idir::prices.group') }}:
                    </span>
                    <span>
                        <a 
                            href="{{ route('admin.price.index', ['filter[group]' => $price->group->id]) }}"
                            title="{{ $price->group->name }}"
                        >
                            {{ $price->group->name }}
                        </a>
                    </span>
                </small>
            </li>
            <li>
                <small>{{ trans('icore::filter.created_at') }}: {{ $price->created_at_diff }}</small>
            </li>
            <li>
                <small>{{ trans('icore::filter.updated_at') }}: {{ $price->updated_at_diff }}</small>
            </li>
        </ul>
        <div class="text-right ml-3">
            <div class="responsive-btn-group">
                @can('admin.prices.edit')
                <div class="btn-group-vertical">
                    <button 
                        data-toggle="modal" 
                        data-target="#edit-modal"
                        data-route="{{ route("admin.price.edit", ['price' => $price->id]) }}"
                        type="button" 
                        class="btn btn-primary edit"
                    >
                        <i class="far fa-edit"></i>
                        <span class="d-none d-sm-inline">
                            {{ trans('icore::default.edit') }}
                        </span>
                    </button>
                </div>
                @endcan
                @can('admin.prices.delete')
                <button 
                    type="button"                
                    class="btn btn-danger" 
                    data-status="delete" 
                    data-toggle="confirmation"
                    data-route="{{ route('admin.price.destroy', ['price' => $price->id]) }}" 
                    data-id="{{ $price->id }}"
                    data-btn-ok-label="{{ trans('icore::default.yes') }}" 
                    data-btn-ok-icon-class="fas fa-check mr-1"
                    data-btn-ok-class="btn h-100 d-flex justify-content-center btn-primary btn-popover destroy" 
                    data-btn-cancel-label=" {{ trans('icore::default.cancel') }}"
                    data-btn-cancel-class="btn h-100 d-flex justify-content-center btn-secondary btn-popover"
                    data-btn-cancel-icon-class="fas fa-ban mr-1"
                    data-title="{{ trans('icore::default.confirm') }}"
                >
                    <i class="far fa-trash-alt"></i>
                    <span class="d-none d-sm-inline">
                        {{ trans('icore::default.delete') }}
                    </span>
                </button>
                @endcan
            </div>
        </div>
    </div>
</div>
