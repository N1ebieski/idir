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
            value="{{ $price->price }}" 
            name="price" 
            class="form-control" 
            id="price"
        >
    </div>
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
                    class="btn-group btn-group-toggle nav d-block" 
                    data-toggle="buttons" 
                    id="nav-tab" 
                    role="tablist"
                >
                    @foreach ($price::AVAILABLE as $type)
                    <a 
                        class="nav-item btn btn-info {{ $price->type === $type ? 'active' : null }}" 
                        id="nav-{{ $type }}-tab"
                        data-toggle="tab" 
                        href="#nav-{{ $type }}" 
                        role="tab"
                        aria-controls="nav-{{ $type }}" 
                        aria-selected="true"
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
                    id="nav-transfer" 
                    role="tabpanel" 
                    aria-labelledby="nav-input-tab"
                ></div>
                <div 
                    class="tab-pane fade {{ $price->type === 'code_sms' ? 'show active' : null }}" 
                    id="nav-code_sms" 
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
                    id="nav-code_transfer" 
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
                    id="nav-paypal_express" 
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
        <select class="form-control" id="group" name="group">
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
