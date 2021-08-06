<footer class="page-footer font-small pt-4">
    <div class="container text-center text-md-left">
        <div class="row ">
            <div class="col-md mx-auto">
                <h5 class="mt-3 mb-4">
                    {{ config('app.name') }}
                </h5>
                <p>{{ config('app.desc') }}</p>
            </div>
            @if (app('router')->has('web.newsletter.store'))
            <hr class="clearfix w-100 d-md-none">
            <div class="col-md mx-auto">
                @render('icore::newsletterComponent')
            </div>
            @endif
            <hr class="clearfix w-100 d-md-none">
        </div>
        <div class="row">
            <div class="col-lg-9 col-12">
                <h5 class="mt-3 mb-2">
                    {{ trans('icore::pages.map') }}:
                </h5>
            </div>
            <div class="col-3 d-none d-lg-block">
                <h5 class="mt-3 mb-2">
                    {{ trans('icore::stats.stats') }}:
                </h5>
            </div>
        </div>
        <div class="row justify-content-end">
            @render('icore::page.footerComponent', ['cols' => 2])
            <div class="col-lg-3 col-sm-6">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <a 
                            href="{{ route('web.dir.index') }}"
                            title="{{ trans('idir::dirs.route.index') }}"
                            class="{{ $isUrl(route('web.dir.index'), 'font-weight-bold') }}"
                        >
                            {{ trans('idir::dirs.route.index') }}
                        </a>
                    </li>                    
                    <li class="list-group-item">
                        <a 
                            href="{{ route('web.post.index') }}" 
                            title="{{ trans('icore::posts.route.blog') }}"
                            class="{{ $isUrl(route('web.post.index'), 'font-weight-bold') }}"
                        >
                            {{ trans('icore::posts.route.blog') }}
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a 
                            href="{{ route('web.contact.show') }}" 
                            title="{{ trans('icore::contact.route.show') }}" 
                            class="{{ $isUrl(route('web.contact.show'), 'font-weight-bold') }}"
                        >
                            {{ trans('icore::contact.route.show') }}
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a 
                            href="{{ route('web.friend.index') }}" 
                            title="{{ trans('icore::friends.route.index') }}" 
                            class="{{ $isUrl(route('web.friend.index'), 'font-weight-bold') }}"
                        >
                            {{ trans('icore::friends.route.index') }}
                        </a>
                    </li>                    
                </ul>
                @render('idir::linkComponent', ['limit' => 5, 'cats' => $catsAsArray ?? null])
            </div> 
            <div class="col-lg-3 col-sm-6">
                <h5 class="mt-4 mt-sm-0 mb-2 d-lg-none">
                    {{ trans('icore::stats.stats') }}:
                </h5>
                @render('idir::statComponent')
            </div>
        </div>        
        <hr>
        <div class="d-flex justify-content-center">
            <div class="footer-copyright text-center py-3 mr-3">
                <small>
                    2019-{{ now()->year }} Copyright © <a href="https://intelekt.net.pl/idir">iDir 
                    v{{ config('idir.version') }}</a> by Mariusz Wysokiński
                </small>
            </div>
            <div 
                class="btn-group my-auto" 
                id="theme-toggle" 
                role="group"
                aria-label="{{ trans('icore::default.theme_toggle') }}"
            >
                <button 
                    type="button" 
                    class="btn btn-sm btn-light border" 
                    style="width:80px;"
                    {{ $isTheme(['', null], 'disabled') }}
                >
                    {{ trans('icore::default.light') }}
                </button>
                <button 
                    type="button" 
                    class="btn btn-sm btn-dark border" 
                    style="width:80px;"
                    {{ $isTheme('dark', 'disabled') }}
                >
                    {{ trans('icore::default.dark') }}
                </button>
            </div>
        </div>
    </div>
</footer>
