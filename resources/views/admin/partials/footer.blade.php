<footer class="page-footer font-small mx-3 d-flex">
    <div class="footer-copyright text-right my-auto mr-3">
        <small>2019-{{ now()->year }} Copyright © <a href="https://intelekt.net.pl/idir">iDir 
        v{{ config('idir.version') }}</a> by Mariusz Wysokiński</small>
    </div>
    <div class="btn-group my-auto" id="themeToggle" role="group" aria-label="Zmień motyw">
        <button type="button" class="btn btn-sm btn-light border" style="width:80px;"
        @isTheme(['', null], 'disabled')>{{ trans('icore::default.light') }}</button>
        <button type="button" class="btn btn-sm btn-dark border" style="width:80px;"
        @isTheme('dark', 'disabled')>{{ trans('icore::default.dark') }}</button>
    </div>
</footer>
