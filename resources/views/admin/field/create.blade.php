<form data-route="{{ route("admin.field.{$field->poli}.store") }}" id="store">
    <div class="form-group">
        <label for="title">{{ trans('idir::fields.title') }}</label>
        <input type="text" value="" name="title" class="form-control" id="title">
    </div>
    <div class="form-group">
        <label for="desc">{{ trans('idir::fields.desc') }}:</label>
        <textarea class="form-control" id="desc" name="desc" rows="3"></textarea>
    </div>
    <div class="form-group">
        <label for="type">{{ trans('idir::fields.choose_type') }}:</label>
        <div id="type">
            <nav>
                <div class="btn-group btn-group-toggle nav d-block" data-toggle="buttons" id="nav-tab" role="tablist">
                    @foreach (['input', 'textarea', 'select', 'multiselect', 'checkbox', 'image'] as $type)
                    <a class="nav-item btn btn-light {{ $loop->first ? 'active' : null }}" id="nav-{{ $type }}-tab"
                    data-toggle="tab" href="#nav-{{ $type }}" role="tab"
                    aria-controls="nav-{{ $type }}" aria-selected="true">
                        <input type="radio" name="type" value="{{ $type }}" id="nav-{{ $type }}-tab"
                        autocomplete="off" {{ $loop->first ? 'checked' : null }}>
                        {{ $type }}
                    </a>
                    @endforeach
                </div>
            </nav>
            <div class="tab-content mt-3" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-input" role="tabpanel" aria-labelledby="nav-input-tab">
                    @component('idir::admin.field.partials.input')
                        @slot('name', ['input', 'min'])
                        @slot('value', 3)
                    @endcomponent
                    @component('idir::admin.field.partials.input')
                        @slot('name', ['input', 'max'])
                        @slot('value', 255)
                    @endcomponent
                </div>
                <div class="tab-pane fade" id="nav-textarea" role="tabpanel" aria-labelledby="nav-textarea-tab">
                    @component('idir::admin.field.partials.input')
                        @slot('name', ['textarea', 'min'])
                        @slot('value', 3)
                    @endcomponent
                    @component('idir::admin.field.partials.input')
                        @slot('name', ['textarea', 'max'])
                        @slot('value', 5000)
                    @endcomponent
                </div>
                <div class="tab-pane fade" id="nav-select" role="tabpanel" aria-labelledby="nav-select-tab">
                    @component('idir::admin.field.partials.textarea')
                        @slot('name', ['select', 'options'])
                    @endcomponent
                </div>
                <div class="tab-pane fade" id="nav-multiselect" role="tabpanel" aria-labelledby="nav-multiselect-tab">
                    @component('idir::admin.field.partials.textarea')
                        @slot('name', ['multiselect', 'options'])
                    @endcomponent
                </div>
                <div class="tab-pane fade" id="nav-checkbox" role="tabpanel" aria-labelledby="nav-checkbox-tab">
                    @component('idir::admin.field.partials.textarea')
                        @slot('name', ['checkbox', 'options'])
                    @endcomponent
                </div>
                <div class="tab-pane fade" id="nav-image" role="tabpanel" aria-labelledby="nav-image-tab">
                    @component('idir::admin.field.partials.input')
                        @slot('name', ['image', 'width'])
                        @slot('value', 720)
                    @endcomponent
                    @component('idir::admin.field.partials.input')
                        @slot('name', ['image', 'height'])
                        @slot('value', 480)
                    @endcomponent
                    @component('idir::admin.field.partials.input')
                        @slot('name', ['image', 'size'])
                        @slot('value', 2048)
                    @endcomponent
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="visible">
            {{ trans('idir::fields.visible') }}: <i data-toggle="tooltip" data-placement="top"
            title="{{ trans("idir::fields.visible_tooltip") }}" class="far fa-question-circle"></i>
        </label>
        <select class="form-control" id="visible" name="visible">
            <option value="1">{{ trans('idir::fields.visible_1') }}</option>
            <option value="0">{{ trans('idir::fields.visible_0') }}</option>
        </select>
    </div>
    <div class="form-group">
        <label for="required">{{ trans('idir::fields.required') }}:</label>
        <select class="form-control" id="required" name="required">
            <option value="0">{{ trans('idir::fields.required_0') }}</option>
            <option value="1">{{ trans('idir::fields.required_1') }}</option>
        </select>
    </div>
    @yield('morphs')
    <button type="button" class="btn btn-primary store">
        <i class="fas fa-check"></i>
        {{ trans('icore::default.submit') }}
    </button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        <i class="fas fa-ban"></i>
        {{ trans('icore::default.cancel') }}
    </button>
</form>
