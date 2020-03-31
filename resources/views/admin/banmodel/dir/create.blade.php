<form data-route="{{ route('admin.banmodel.dir.store', [$dir->id]) }}">
    @if (isset($dir->user))    
    <div class="form-group">
        <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input" id="user" name="user" value="{{ $dir->user->id }}">
              <label class="custom-control-label" for="user">{{ trans('icore::bans.model.user.user') }}: {{ $dir->user->name }}</label>
        </div>
    </div>
    @endif
    @if (!is_null(optional($dir->user)->ip))
    <div class="form-group">
        <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input" id="ip" name="ip" value="{{ $dir->user->ip }}">
              <label class="custom-control-label" for="ip">{{ trans('icore::bans.value.ip.ip') }}: {{ $dir->user->ip }}</label>
        </div>
    </div>
    @endif
    @if (!is_null($dir->url))
    <div class="form-group">
        <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input" id="url" name="url" value="{{ $dir->url }}">
              <label class="custom-control-label" for="url">{{ trans('idir::bans.value.url.url') }}: {{ $dir->url }}</label>
        </div>
    </div>
    @endif
    <button type="button" class="btn btn-primary storeBanModel">
        <i class="fas fa-check"></i>
        <span>{{ trans('icore::default.save') }}</span>
    </button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        <i class="fas fa-ban"></i>
        <span>{{ trans('icore::default.cancel') }}</span>
    </button>
</form>
