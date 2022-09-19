@component('icore::admin.partials.modal')

@slot('modal_id', 'edit-modal')

@slot('modal_size', 'modal-lg')

@slot('modal_title')
<i class="far fa-edit"></i>
<span> {{ trans('idir::fields.route.edit') }}</span>
@endslot

@slot('modal_body')
<form 
    id="edit-field"
    data-route="{{ route("admin.field.{$field->poli}.update", [$field->id]) }}" 
    data-id="{{ $field->id }}"
>
    <div class="form-group">
        <label for="title">
            {{ trans('idir::fields.title') }}
        </label>
        <input 
            type="text" 
            value="{{ $field->title }}" 
            name="title" 
            class="form-control" 
            id="title"
        >
    </div>
    <div class="form-group">
        <label for="desc">
            {{ trans('idir::fields.desc') }}:
        </label>
        <textarea 
            class="form-control" 
            id="desc" 
            name="desc" 
            rows="3"
        >{{ $field->desc }}</textarea>
    </div>
    @if (!$field->type->isDefault())
    <div class="form-group">
        <label for="type">
            {{ trans('idir::fields.choose_type') }}:
        </label>
        <div id="type">
            <nav>
                <div 
                    class="btn-group btn-group-toggle nav nav-tabs" 
                    data-toggle="buttons" 
                    id="nav-tab" 
                    role="tablist"
                >
                    @foreach (Field\Type::getAvailable() as $type)
                    <a 
                        class="nav-item nav-link btn btn-link flex-grow-0 text-decoration-none shadow-none {{ $field->type->getValue() === $type ? 'active' : null }}" 
                        id="nav-{{ $type }}-tab"
                        data-toggle="tab" 
                        href="#nav-{{ $type }}-edit" 
                        role="tab"
                        aria-controls="nav-{{ $type }}-edit" 
                        aria-selected="{{ $field->type->getValue() === $type ? 'true' : 'false' }}"
                    >
                        <input 
                            type="radio" 
                            name="type" 
                            value="{{ $type }}" 
                            id="nav-{{ $type }}-tab"
                            autocomplete="off" 
                            {{ $field->type->getValue() === $type ? 'checked' : null }}
                        >
                        {{ $type }}
                    </a>
                    @endforeach
                </div>
            </nav>
            <div class="tab-content mt-3" id="nav-tabContent">
                <div 
                    class="tab-pane fade {{ $field->type->isInput() ? 'show active' : null }}"
                    id="nav-input-edit" 
                    role="tabpanel" 
                    aria-labelledby="nav-input-tab"
                >
                    @component('idir::admin.field.partials.components.input')
                        @slot('name', [Field\Type::INPUT, 'min'])
                        @slot('value', $field->options->min ?? 3)
                    @endcomponent
                    @component('idir::admin.field.partials.components.input')
                        @slot('name', [Field\Type::INPUT, 'max'])
                        @slot('value', $field->options->max ?? 255)
                    @endcomponent
                </div>
                <div 
                    class="tab-pane fade {{ $field->type->isTextarea() ? 'show active' : null }}"
                    id="nav-textarea-edit" 
                    role="tabpanel" 
                    aria-labelledby="nav-textarea-tab"
                >
                    @component('idir::admin.field.partials.components.input')
                        @slot('name', [Field\Type::TEXTAREA, 'min'])
                        @slot('value', $field->options->min ?? 3)
                    @endcomponent
                    @component('idir::admin.field.partials.components.input')
                        @slot('name', [Field\Type::TEXTAREA, 'max'])
                        @slot('value', $field->options->max ?? 5000)
                    @endcomponent
                </div>
                <div 
                    class="tab-pane fade {{ $field->type->isSelect() ? 'show active' : null }}"
                    id="nav-select-edit" 
                    role="tabpanel" 
                    aria-labelledby="nav-select-tab"
                >
                    @component('idir::admin.field.partials.components.textarea')
                        @slot('name', [Field\Type::SELECT, 'options'])
                        @slot('value', $field->options->getOptionsAsString() ?? null)
                    @endcomponent
                </div>
                <div 
                    class="tab-pane fade {{ $field->type->isMultiselect() ? 'show active' : null }}"
                    id="nav-multiselect-edit" 
                    role="tabpanel" 
                    aria-labelledby="nav-multiselect-tab"
                >
                    @component('idir::admin.field.partials.components.textarea')
                        @slot('name', [Field\Type::MULTISELECT, 'options'])
                        @slot('value', $field->options->getOptionsAsString() ?? null)
                    @endcomponent
                </div>
                <div 
                    class="tab-pane fade {{ $field->type->isCheckbox() ? 'show active' : null }}"
                    id="nav-checkbox-edit" 
                    role="tabpanel" 
                    aria-labelledby="nav-checkbox-tab"
                >
                    @component('idir::admin.field.partials.components.textarea')
                        @slot('name', [Field\Type::CHECKBOX, 'options'])
                        @slot('value', $field->options->getOptionsAsString() ?? null)
                    @endcomponent
                </div>
                <div  
                    class="tab-pane fade {{ $field->type->isSwitch() ? 'show active' : null }}" 
                    id="nav-switch-edit" 
                    role="tabpanel" 
                    aria-labelledby="nav-switch-tab"
                >
                </div>          
                <div 
                    class="tab-pane fade {{ $field->type->isImage() ? 'show active' : null }}"
                    id="nav-image-edit" 
                    role="tabpanel" 
                    aria-labelledby="nav-image-tab"
                >
                    @component('idir::admin.field.partials.components.input')
                        @slot('name', [Field\Type::IMAGE, 'width'])
                        @slot('value', $field->options->width ?? 720)
                    @endcomponent
                    @component('idir::admin.field.partials.components.input')
                        @slot('name', [Field\Type::IMAGE, 'height'])
                        @slot('value', $field->options->height ?? 480)
                    @endcomponent
                    @component('idir::admin.field.partials.components.input')
                        @slot('name', [Field\Type::IMAGE, 'size'])
                        @slot('value', $field->options->size ?? 2048)
                    @endcomponent
                </div>
            </div>
        </div>
    </div>
    @endif
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
            <option 
                value="{{ Field\Visible::ACTIVE }}" 
                {{ $field->visible->isActive() ? 'selected' : null }}
            >
                {{ trans('idir::fields.visible.'.Field\Visible::ACTIVE) }}
            </option>
            <option 
                value="{{ Field\Visible::INACTIVE }}" 
                {{ $field->visible->isInactive() ? 'selected' : null }}
            >
                {{ trans('idir::fields.visible.'.Field\Visible::INACTIVE) }}
            </option>
        </select>
    </div>
    <div class="form-group">
        <label for="required">
            {{ trans('idir::fields.required.label') }}:
        </label>
        <select class="form-control custom-select" id="required" name="required">
            <option 
                value="{{ Field\Required::INACTIVE }}" 
                {{ $field->options->required->isInactive() ? 'selected' : null }}
            >
                {{ trans('idir::fields.required.'.Field\Required::INACTIVE) }}
            </option>
            <option 
                value="{{ Field\Required::ACTIVE }}" 
                {{ $field->options->required->isActive() ? 'selected' : null }}
            >
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
        class="btn btn-primary update"
        form="edit-field"
    >
        <i class="fas fa-check"></i>
        <span>{{ trans('icore::default.save') }}</span>
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
