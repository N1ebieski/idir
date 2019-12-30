<form data-route="{{ route('admin.group.update_position', [$group->id]) }}"
data-id="{{ $group->id }}" id="update">
    @if ((int)$siblings_count > 0)
    <div class="form-group">
        <label for="position">{{ trans('icore::default.position') }}</label>
        <select class="form-control" id="position" name="position">
        @for ($i=0; $i<$siblings_count; $i++)
            <option value="{{ $i }}" {{ (old('position', $group->position) === $i) ? 'selected' : '' }}>
                {{ $i+1 }}
            </option>
        @endfor
        </select>
    </div>
    <button type="button" class="btn btn-primary updatePositionPage">
        <i class="fas fa-check"></i>
        {{ trans('icore::default.save') }}
    </button>
    @endif
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        <i class="fas fa-ban"></i>
        {{ trans('icore::default.cancel') }}
    </button>
</form>
