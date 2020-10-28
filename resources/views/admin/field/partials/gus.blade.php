<div class="form-group">
    <label for="field.{{ $field->id }}" class="d-flex justify-content-between">
        <div>
            <span>{{ $field->title }}:</span>     
            @if ($field->desc !== null)
            <i 
                data-toggle="tooltip" 
                data-placement="top"
                title="{{ $field->desc }}" 
                class="far fa-question-circle"
            ></i>
            @endif        
        </div>
    </label>
    <div 
        data-route="{{ route('admin.field.gus') }}"
        class="search position-relative"
        id="searchGus" 
    >
        <div class="input-group">
            <select class="custom-select w-20" id="type" name="type">
                <option value="nip">
                    NIP
                </option>
                <option value="regon">
                    REGON
                </option>
                <option value="krs">
                    KRS
                </option>            
            </select>
            <input
                type="text" 
                value="" 
                class="form-control" 
                id="number"
                name="number"
                placeholder="{{ trans('idir::fields.gus.placeholder') }}"
            >
            <span class="input-group-append">
                <button 
                    class="btn btn-outline-secondary border border-left-0"
                    type="button"
                >
                    <i class="fa fa-search"></i>
                </button>
            </span>        
        </div>
    </div>
</div>
