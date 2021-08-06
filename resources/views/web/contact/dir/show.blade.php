<div>
    <form data-route="{{ url()->current() }}" id="show-contact">
        <div class="form-group">
            <label for="email">
                {{ trans('icore::contact.address.label') }}
            </label>
            <input 
                type="email" 
                name="email" 
                id="email" 
                value="{{ old('email', auth()->user()->email ?? null) }}"
                class="form-control"
                placeholder="{{ trans('icore::contact.address.placeholder') }}"
            >
        </div>
        <div class="form-group">
            <label for="title">
                {{ trans('icore::contact.title.label') }}
            </label>
            <input 
                type="text" 
                name="title" 
                id="title"
                class="form-control"
                placeholder="{{ trans('icore::contact.title.placeholder') }}"
            >
        </div>
        <div class="form-group">
            <label for="content">
                {{ trans('icore::contact.content') }}
            </label>
            <textarea 
                name="content" 
                id="content"
                class="form-control"
                rows="3"
            ></textarea>
        </div>
        <div class="form-group">
            <div class="custom-control custom-checkbox">
                <input 
                    type="checkbox" 
                    class="custom-control-input" 
                    id="contact_agreement" 
                    name="contact_agreement" 
                    value="1"
                >
                <label class="custom-control-label text-left" for="contact_agreement">
                    <small>{{ trans('icore::policy.agreement.contact') }}</small>
                </label>
            </div>
        </div>        
        @render('icore::captchaComponent', [
            'id' => 1000
        ])
        <button type="button" class="btn btn-primary send-contact">
            <i class="fas fa-check"></i>
            <span>{{ trans('icore::default.submit') }}</span>
        </button>        
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fas fa-ban"></i>
            <span>{{ trans('icore::default.cancel') }}</span>
        </button>        
    </form>
</div>
