<form 
    data-route="{{ route('admin.price.update', [$price->id]) }}" 
    id="update"
    data-id="{{ $price->id }}"
>
    <div class="form-group">
        <label for="price">
            {{ trans('idir::prices.price') }}:
        </label>
        <input 
            type="text" 
            value="{{ $price->regular_price }}" 
            name="price" 
            class="form-control" 
            id="price"
        >
    </div>
    @if ($price->type !== 'code_sms')
    <div class="form-group">
        <label for="discount_price">
            <span>{{ trans('idir::prices.discount_price.label') }}:</span>
            <i 
                data-toggle="tooltip" 
                data-placement="top"
                title="{{ trans('idir::prices.discount_price.tooltip') }}"
                class="far fa-question-circle"
            ></i>
        </label>
        <div class="input-group">
            <input 
                type="text" 
                value="{{ $price->discount_price }}" 
                name="discount_price"
                class="form-control" 
                id="discount_price"
            >
            <input 
                type="text"
                class="form-control" 
                id="discount" 
                value="{{ $price->discount }}"
            >
            <div class="input-group-append">
                <div class="input-group-text">%</div>
            </div>
        </div>
    </div>
    @endif
    <div class="form-group">
        <label for="days">
            {{ trans('idir::prices.days') }}:
        </label>
        <input 
            type="text" 
            value="{{ $price->days }}" 
            name="days" 
            class="form-control" 
            id="days"
        >
    </div>
    <div class="form-group">
        <label for="type">
            {{ trans('idir::prices.payment.label') }}:
        </label>
        <div id="type">
            <nav>
                <div 
                    class="btn-group btn-group-toggle nav nav-tabs" 
                    data-toggle="buttons" 
                    id="nav-tab" 
                    role="tablist"
                >
                    @foreach ($price::AVAILABLE as $type)
                    <a 
                        class="nav-item nav-link btn btn-link flex-grow-0 text-decoration-none shadow-none {{ $price->type === $type ? 'active' : null }}" 
                        id="nav-{{ $type }}-tab"
                        data-toggle="tab" 
                        href="#nav-{{ $type }}-edit" 
                        role="tab"
                        aria-controls="nav-{{ $type }}-edit" 
                        aria-selected="{{ $price->type === $type ? 'true' : 'false' }}"
                    >
                        <input 
                            type="radio" 
                            name="type" 
                            value="{{ $type }}" 
                            id="nav-{{ $type }}-tab"
                            autocomplete="off" 
                            {{ $price->type === $type ? 'checked' : null }}
                        >
                        {{ trans("idir::prices.payment.{$type}") }}
                    </a>
                    @endforeach
                </div>
            </nav>
            <div class="tab-content mt-3" id="nav-tabContent">
                <div 
                    class="tab-pane fade {{ $price->type === 'transfer' ? 'show active' : null }}" 
                    id="nav-transfer-edit" 
                    role="tabpanel" 
                    aria-labelledby="nav-input-tab"
                ></div>
                <div 
                    class="tab-pane fade {{ $price->type === 'code_sms' ? 'show active' : null }}" 
                    id="nav-code_sms-edit" 
                    role="tabpanel" 
                    aria-labelledby="nav-input-tab"
                >
                    @component('idir::admin.price.partials.components.code')
                        @slot('name', 'code_sms')
                        @slot('value', $price->code)
                    @endcomponent 
                    <div class="form-group">
                        <label for="number">
                            {{ trans('idir::prices.number') }}:
                        </label>
                        <input 
                            type="text" 
                            value="{{ $price->number }}" 
                            name="code_sms[number]" 
                            class="form-control" 
                            id="code_sms.number"
                        >
                    </div>
                    <div class="form-group">
                        <label for="token">
                            {{ trans('idir::prices.token') }}:
                        </label>
                        <input 
                            type="text" 
                            value="{{ $price->token }}" 
                            name="code_sms[token]" 
                            class="form-control"
                            id="code_sms.token"
                        >
                    </div>
                    @component('idir::admin.price.partials.components.codes')
                        @slot('name', 'code_sms')
                        @slot('count', $price->codes->count())
                        @slot('value', $price->codes_as_string)
                    @endcomponent                                                    
                </div>
                <div 
                    class="tab-pane fade {{ $price->type === 'code_transfer' ? 'show active' : null }}" 
                    id="nav-code_transfer-edit" 
                    role="tabpanel" 
                    aria-labelledby="nav-input-tab"
                >
                    @component('idir::admin.price.partials.components.code')
                        @slot('name', 'code_transfer')
                        @slot('value', $price->code)
                    @endcomponent 
                    @component('idir::admin.price.partials.components.codes')
                        @slot('name', 'code_transfer')
                        @slot('count', $price->codes->count())
                        @slot('value', $price->codes_as_string)
                    @endcomponent                                                    
                </div>                
                <div 
                    class="tab-pane fade {{ $price->type === 'paypal_express' ? 'show active' : null }}" 
                    id="nav-paypal_express-edit" 
                    role="tabpanel" 
                    aria-labelledby="nav-input-tab"
                ></div>                
            </div>
        </div>
    </div>
    @if ($groups->isNotEmpty())
    <div class="form-group">
        <label for="group">
            {{ trans('idir::prices.group') }}:
        </label>
        <select class="form-control custom-select" id="group" name="group">
            @foreach ($groups as $group)
            <option 
                value="{{ $group->id }}"
                {{ $group->id === $price->group->id ? 'selected' : null }}
            >
                {{ $group->name }}
            </option>
            @endforeach
        </select>
    </div>
    @endif    
    <button type="button" class="btn btn-primary update">
        <i class="fas fa-check"></i>
        <span>{{ trans('icore::default.save') }}</span>
    </button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        <i class="fas fa-ban"></i>
        <span>{{ trans('icore::default.cancel') }}</span>
    </button>
</form>
