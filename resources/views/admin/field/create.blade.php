@component('icore::admin.partials.modal')

@slot('modal_id', 'create-modal')

@slot('modal_size', 'modal-lg')

@slot('modal_title')
<i class="far fa-plus-square"></i>
<span> {{ trans('idir::fields.route.create') }}</span>
@endslot

@slot('modal_body')
<form 
    id="create-field"
    data-route="{{ route("admin.field.{$field->poli}.store") }}" 
>
    <div class="form-group">
        <label for="title">
            {{ trans('idir::fields.title') }}
        </label>
        <input 
            type="text" 
            value="" 
            name="title" 
            class="form-control" 
            id="title"
        >
    </div>
    <div class="form-group">
        <label for="desc">
            {{ trans('idir::fields.desc') }}:
        </label>
        <textarea class="form-control" id="desc" name="desc" rows="3"></textarea>
    </div>
    <div class="form-group">
        <label for="type">
            {{ trans('idir::fields.choose_type') }}:
        </label>
        <div id="type">
            <nav class="w-100">
                <div 
                    class="btn-group btn-group-toggle nav nav-tabs" 
                    data-toggle="buttons" 
                    id="nav-tab" 
                    role="tablist"
                >
                    @foreach (Field\Type::getAvailable() as $type)
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
                        {{ $type }}
                    </a>
                    @endforeach
                </div>
            </nav>
            <div class="tab-content mt-3" id="nav-tabContent">
                <div 
                    class="tab-pane fade show active" 
                    id="nav-input-create" 
                    role="tabpanel" 
                    aria-labelledby="nav-input-tab"
                >
                    @component('idir::admin.field.partials.components.input')
                        @slot('name', [Field\Type::INPUT, 'min'])
                        @slot('value', 3)
                    @endcomponent
                    @component('idir::admin.field.partials.components.input')
                        @slot('name', [Field\Type::INPUT, 'max'])
                        @slot('value', 255)
                    @endcomponent
                </div>
                <div 
                    class="tab-pane fade" 
                    id="nav-textarea-create" 
                    role="tabpanel" 
                    aria-labelledby="nav-textarea-tab"
                >
                    @component('idir::admin.field.partials.components.input')
                        @slot('name', [Field\Type::TEXTAREA, 'min'])
                        @slot('value', 3)
                    @endcomponent
                    @component('idir::admin.field.partials.components.input')
                        @slot('name', [Field\Type::TEXTAREA, 'max'])
                        @slot('value', 5000)
                    @endcomponent
                </div>
                <div 
                    class="tab-pane fade" 
                    id="nav-select-create" 
                    role="tabpanel" 
                    aria-labelledby="nav-select-tab"
                >
                    @component('idir::admin.field.partials.components.textarea')
                        @slot('name', [Field\Type::SELECT, 'options'])
                    @endcomponent
                </div>
                <div 
                    class="tab-pane fade" 
                    id="nav-multiselect-create" 
                    role="tabpanel" 
                    aria-labelledby="nav-multiselect-tab"
                >
                    @component('idir::admin.field.partials.components.textarea')
                        @slot('name', [Field\Type::MULTISELECT, 'options'])
                    @endcomponent
                </div>
                <div 
                    class="tab-pane fade" 
                    id="nav-checkbox-create" 
                    role="tabpanel" 
                    aria-labelledby="nav-checkbox-tab"
                >
                    @component('idir::admin.field.partials.components.textarea')
                        @slot('name', [Field\Type::CHECKBOX, 'options'])
                    @endcomponent
                </div>
                <div 
                    class="tab-pane fade" 
                    id="nav-image-create" 
                    role="tabpanel" 
                    aria-labelledby="nav-image-tab"
                >
                    @component('idir::admin.field.partials.components.input')
                        @slot('name', [Field\Type::IMAGE, 'width'])
                        @slot('value', 720)
                    @endcomponent
                    @component('idir::admin.field.partials.components.input')
                        @slot('name', [Field\Type::IMAGE, 'height'])
                        @slot('value', 480)
                    @endcomponent
                    @component('idir::admin.field.partials.components.input')
                        @slot('name', [Field\Type::IMAGE, 'size'])
                        @slot('value', 2048)
                    @endcomponent
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="visible">
            <span>{{ trans('idir::fields.visible.label') }}:</span>
            <i 
                data-toggle="tooltip" 
                data-placement="top"
                title="{{ trans("idir::fields.visible.tooltip") }}" 
                class="far fa-question-circle"
            ></i>
        </label>
        <select class="form-control custom-select" id="visible" name="visible">
            <option value="{{ $field::VISIBLE }}">
                {{ trans('idir::fields.visible.'.$field::VISIBLE) }}
            </option>
            <option value="{{ $field::INVISIBLE }}">
                {{ trans('idir::fields.visible.'.$field::INVISIBLE) }}
            </option>
        </select>
    </div>
    <div class="form-group">
        <label for="required">
            {{ trans('idir::fields.required.label') }}:
        </label>
        <select class="form-control custom-select" id="required" name="required">
            <option value="{{ Field\Required::INACTIVE }}">
                {{ trans('idir::fields.required.'.Field\Required::INACTIVE) }}
            </option>
            <option value="{{ Field\Required::ACTIVE }}">
                {{ trans('idir::fields.required.'.Field\Required::ACTIVE) }}
            </option>
        </select>
    </div>
    @yield('morphs')
</form>
@endslot

@slot('modal_footer')
<div class="d-inline">
    <button 
        type="button" 
        class="btn btn-primary store"
        form="create-field"
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
