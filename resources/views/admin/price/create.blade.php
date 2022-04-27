@component('icore::admin.partials.modal')

@slot('modal_id', 'create-modal')

@slot('modal_title')
<i class="far fa-plus-square"></i>
<span> {{ trans('idir::prices.route.create') }}</span>
@endslot

@slot('modal_body')
<form 
    id="create-price"
    data-route="{{ route("admin.price.store") }}" 
>
    <div class="form-group">
        <label for="price">
            {{ trans('idir::prices.price') }}:
        </label>
        <input 
            type="text" 
            value="" 
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
            value="" 
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
                    @foreach (Price\Type::getAvailable() as $type)
                    <a 
                        class="nav-item nav-link btn btn-link flex-grow-0 text-decoration-none shadow-none {{ $loop->first ? 'active' : null }}" 
                        id="nav-{{ $type }}-tab"
                        data-toggle="tab" 
                        href="#nav-{{ $type }}-create" 
                        role="tab"
                        aria-controls="nav-{{ $type }}-create" 
                        aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                    >
                        <input 
                            type="radio" 
                            name="type" 
                            value="{{ $type }}" 
                            id="nav-{{ $type }}-tab"
                            autocomplete="off" 
                            {{ $loop->first ? 'checked' : null }}
                        >
                        {{ trans("idir::prices.payment.{$type}") }}
                    </a>
                    @endforeach
                </div>
            </nav>
            <div class="tab-content mt-3" id="nav-tabContent">
                <div 
                    class="tab-pane fade show active" 
                    id="nav-transfer-create" 
                    role="tabpanel" 
                    aria-labelledby="nav-input-tab"
                ></div>
                <div 
                    class="tab-pane fade hide" 
                    id="nav-code_sms-create" 
                    role="tabpanel" 
                    aria-labelledby="nav-input-tab"
                >
                    @component('idir::admin.price.partials.components.code')
                        @slot('name', Price\Type::CODE_SMS)
                    @endcomponent 
                    <div class="form-group">
                        <label for="number">
                            {{ trans('idir::prices.number') }}:
                        </label>
                        <input 
                            type="text" 
                            value="" 
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
                            value="" 
                            name="code_sms[token]" 
                            class="form-control"
                            id="code_sms.token"
                        >
                    </div>
                    @component('idir::admin.price.partials.components.codes')
                        @slot('name', Price\Type::CODE_SMS)
                    @endcomponent                                                    
                </div>
                <div 
                    class="tab-pane fade hide" 
                    id="nav-code_transfer-create" 
                    role="tabpanel" 
                    aria-labelledby="nav-input-tab"
                >
                    @component('idir::admin.price.partials.components.code')
                        @slot('name', Price\Type::CODE_TRANSFER)
                    @endcomponent 
                    @component('idir::admin.price.partials.components.codes')
                        @slot('name', Price\Type::CODE_TRANSFER)
                    @endcomponent                                                    
                </div>                
                <div 
                    class="tab-pane fade show active" 
                    id="nav-paypal_express-create" 
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
                {{ $group->id === $group_id ? 'selected' : null }}
            >
                {{ $group->name }}
            </option>
            @endforeach
        </select>
    </div>
    @endif    
</form>
@endslot

@slot('modal_footer')
<div class="d-inline">
    <button 
        type="button" 
        class="btn btn-primary store"
        form="create-price"
    >
        <i class="fas fa-check"></i>
        <span>{{ trans('icore::default.submit') }}</span>
    </button>
    <button 
        type="button" 
        class="btn btn-secondary" 
        data-dismiss="modal"
    >
        <i class="fas fa-ban"></i>
        <span>{{ trans('icore::default.cancel') }}</span>
    </button>
</div>
@endslot

@endcomponent
