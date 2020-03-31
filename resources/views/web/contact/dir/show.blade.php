<div>
    <form data-route="{{ url()->current() }}" id="showContact">
        <div class="form-group">
            <label for="email">{{ trans('icore::contact.address.label') }}</label>
            <input type="email" name="email" id="email" 
            value="{{ old('email', auth()->user()->email ?? null) }}"
            class="form-control"
            placeholder="{{ trans('icore::contact.address.placeholder') }}">
        </div>
        <div class="form-group">
            <label for="title">{{ trans('icore::contact.title.label') }}</label>
            <input type="text" name="title" id="title"
            class="form-control"
            placeholder="{{ trans('icore::contact.title.placeholder') }}">
        </div>
        <div class="form-group">
            <label for="content">{{ trans('icore::contact.content') }}</label>
            <textarea name="content" id="content"
            class="form-control"
            rows="3"></textarea>
        </div>
        @render('icore::captchaComponent', ['id' => 1000])
        <button type="button" class="btn btn-primary sendContact">
            <i class="fas fa-check"></i>
            {{ trans('icore::default.submit') }}
        </button>        
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fas fa-ban"></i>
            {{ trans('icore::default.cancel') }}
        </button>        
    </form>
</div>