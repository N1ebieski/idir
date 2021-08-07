<form 
    data-route="{{ route("admin.field.{$field->poli}.update", [$field->id]) }}" 
    id="update" 
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
    @if ($field->isNotDefault())
    <div class="form-group">
        <label for="type">
            {{ trans('idir::fields.choose_type') }}:
        </label>
        <div id="type">
            <nav>
                <div 
                    class="btn-group btn-group-toggle nav d-block" 
                    data-toggle="buttons" 
                    id="nav-tab" 
                    role="tablist"
                >
                    @foreach ($field::AVAILABLE as $type)
                    <a 
                        class="nav-item btn btn-info {{ $field->type == $type ? 'active' : null }}" 
                        id="nav-{{ $type }}-tab"
                        data-toggle="tab" 
                        href="#nav-{{ $type }}-edit" 
                        role="tab"
                        aria-controls="nav-{{ $type }}-edit" 
                        aria-selected="true"
                    >
                        <input 
                            type="radio" 
                            name="type" 
                            value="{{ $type }}" 
                            id="nav-{{ $type }}-tab"
                            autocomplete="off" 
                            {{ $field->type == $type ? 'checked' : null }}
                        >
                        {{ $type }}
                    </a>
                    @endforeach
                </div>
            </nav>
            <div class="tab-content mt-3" id="nav-tabContent">
                <div 
                    class="tab-pane fade {{ $field->type == 'input' ? 'show active' : null }}"
                    id="nav-input-edit" 
                    role="tabpanel" 
                    aria-labelledby="nav-input-tab"
                >
                    @component('idir::admin.field.partials.components.input')
                        @slot('name', ['input', 'min'])
                        @slot('value', $field->options->min ?? 3)
                    @endcomponent
                    @component('idir::admin.field.partials.components.input')
                        @slot('name', ['input', 'max'])
                        @slot('value', $field->options->max ?? 255)
                    @endcomponent
                </div>
                <div 
                    class="tab-pane fade {{ $field->type == 'textarea' ? 'show active' : null }}"
                    id="nav-textarea-edit" 
                    role="tabpanel" 
                    aria-labelledby="nav-textarea-tab"
                >
                    @component('idir::admin.field.partials.components.input')
                        @slot('name', ['textarea', 'min'])
                        @slot('value', $field->options->min ?? 3)
                    @endcomponent
                    @component('idir::admin.field.partials.components.input')
                        @slot('name', ['textarea', 'max'])
                        @slot('value', $field->options->max ?? 5000)
                    @endcomponent
                </div>
                <div 
                    class="tab-pane fade {{ $field->type == 'select' ? 'show active' : null }}"
                    id="nav-select-edit" 
                    role="tabpanel" 
                    aria-labelledby="nav-select-tab"
                >
                    @component('idir::admin.field.partials.components.textarea')
                        @slot('name', ['select', 'options'])
                        @slot('value', $field->options->options_as_string ?? null)
                    @endcomponent
                </div>
                <div 
                    class="tab-pane fade {{ $field->type == 'multiselect' ? 'show active' : null }}"
                    id="nav-multiselect-edit" 
                    role="tabpanel" 
                    aria-labelledby="nav-multiselect-tab"
                >
                    @component('idir::admin.field.partials.components.textarea')
                        @slot('name', ['multiselect', 'options'])
                        @slot('value', $field->options->options_as_string ?? null)
                    @endcomponent
                </div>
                <div 
                    class="tab-pane fade {{ $field->type == 'checkbox' ? 'show active' : null }}"
                    id="nav-checkbox-edit" 
                    role="tabpanel" 
                    aria-labelledby="nav-checkbox-tab"
                >
                    @component('idir::admin.field.partials.components.textarea')
                        @slot('name', ['checkbox', 'options'])
                        @slot('value', $field->options->options_as_string ?? null)
                    @endcomponent
                </div>
                <div 
                    class="tab-pane fade {{ $field->type == 'image' ? 'show active' : null }}"
                    id="nav-image-edit" 
                    role="tabpanel" 
                    aria-labelledby="nav-image-tab"
                >
                    @component('idir::admin.field.partials.components.input')
                        @slot('name', ['image', 'width'])
                        @slot('value', $field->options->width ?? 720)
                    @endcomponent
                    @component('idir::admin.field.partials.components.input')
                        @slot('name', ['image', 'height'])
                        @slot('value', $field->options->height ?? 480)
                    @endcomponent
                    @component('idir::admin.field.partials.components.input')
                        @slot('name', ['image', 'size'])
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
                value="{{ $field::VISIBLE }}" 
                {{ $field->visible == $field::VISIBLE ? 'selected' : null }}
            >
                {{ trans('idir::fields.visible.'.$field::VISIBLE) }}
            </option>
            <option 
                value="{{ $field::INVISIBLE }}" 
                {{ $field->visible == $field::INVISIBLE ? 'selected' : null }}
            >
                {{ trans('idir::fields.visible.'.$field::INVISIBLE) }}
            </option>
        </select>
    </div>
    <div class="form-group">
        <label for="required">
            {{ trans('idir::fields.required.label') }}:
        </label>
        <select class="form-control custom-select" id="required" name="required">
            <option 
                value="{{ $field::OPTIONAL }}" 
                {{ $field->options->required == $field::OPTIONAL ? 'selected' : null }}
            >
                {{ trans('idir::fields.required.'.$field::OPTIONAL) }}
            </option>
            <option 
                value="{{ $field::REQUIRED }}" 
                {{ $field->options->required == $field::REQUIRED ? 'selected' : null }}
            >
                {{ trans('idir::fields.required.'.$field::REQUIRED) }}
            </option>
        </select>
    </div>
    @yield('morphs')
    <button type="button" class="btn btn-primary update">
        <i class="fas fa-check"></i>
        <span>{{ trans('icore::default.save') }}</span>
    </button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        <i class="fas fa-ban"></i>
        <span>{{ trans('icore::default.cancel') }}</span>
    </button>
</form>
