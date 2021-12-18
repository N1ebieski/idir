@component('icore::admin.partials.modal')

@slot('modal_id', 'edit-position-modal')

@slot('modal_title')
<i class="fas fa-sort-amount-up"></i>
<span> {{ trans('idir::groups.route.edit_position') }}</span>
@endslot

@slot('modal_body')
<form 
    data-route="{{ route('admin.group.update_position', [$group->id]) }}"
    data-id="{{ $group->id }}" 
    id="update"
>
    @if ((int)$siblings_count > 0)
    <div class="form-group">
        <label for="position">
            {{ trans('icore::default.position') }}
        </label>
        <select class="form-control custom-select" id="position" name="position">
        @for ($i = 0; $i < $siblings_count; $i++)
            <option 
                value="{{ $i }}" 
                {{ (old('position', $group->position) === $i) ? 'selected' : '' }}
            >
                {{ $i + 1 }}
            </option>
        @endfor
        </select>
    </div>
    @endif    
</form>
@endslot

@slot('modal_footer')
<div class="d-inline">
    <button 
        type="button" 
        class="btn btn-primary update-position"
        form="edit-position"
    >
        <i class="fas fa-check"></i>
        {{ trans('icore::default.save') }}
    </button>
    <button 
        type="button" 
        class="btn btn-secondary" 
        data-dismiss="modal"
    >
        <i class="fas fa-ban"></i>
        {{ trans('icore::default.cancel') }}
    </button>
</div>
@endslot

@endcomponent
