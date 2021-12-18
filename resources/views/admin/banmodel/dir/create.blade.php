@component('icore::admin.partials.modal')

@slot('modal_id', 'create-banmodel-dir-modal')

@slot('modal_title')
<i class="fas fa-user-slash"></i>
<span> {{ trans('icore::bans.route.create') }}</span>
@endslot

@slot('modal_body')
<form 
    id="create-banmodel"
    data-route="{{ route('admin.banmodel.dir.store', [$dir->id]) }}"
>
    @if (isset($dir->user))    
    <div class="form-group">
        <div class="custom-control custom-checkbox">
            <input 
                type="checkbox" 
                class="custom-control-input" 
                id="user" 
                name="user" 
                value="{{ $dir->user->id }}"
            >
            <label class="custom-control-label" for="user">
                {{ trans('icore::bans.model.user.user') }}: {{ $dir->user->name }}
            </label>
        </div>
    </div>
    @endif
    @if (!is_null(optional($dir->user)->ip))
    <div class="form-group">
        <div class="custom-control custom-checkbox">
            <input 
                type="checkbox" 
                class="custom-control-input" 
                id="ip" 
                name="ip" 
                value="{{ $dir->user->ip }}"
            >
            <label class="custom-control-label" for="ip">
                {{ trans('icore::bans.value.ip.ip') }}: {{ $dir->user->ip }}
            </label>
        </div>
    </div>
    @endif
    @if (!is_null($dir->url))
    <div class="form-group">
        <div class="custom-control custom-checkbox">
            <input 
                type="checkbox" 
                class="custom-control-input" 
                id="url" 
                name="url" 
                value="{{ $dir->url }}"
            >
            <label class="custom-control-label" for="url">
                {{ trans('idir::bans.value.url.url') }}: {{ $dir->url }}
            </label>
        </div>
    </div>
    @endif
</form>
@endslot

@slot('modal_footer')
<div class="d-inline">
    <button 
        type="button" 
        class="btn btn-primary store-banmodel"
        form="create-banmodal"
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
@endslot

@endcomponent
