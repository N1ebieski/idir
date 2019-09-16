<footer class="page-footer font-small mx-3 d-flex flex-row-reverse">
    <div class="footer-copyright text-right my-auto">
        <small>2019 Copyright © <a href="">iCore</a> by Mariusz Wysokiński</small>
    </div>
    <div class="btn-group my-auto mr-3" id="themeToggle" role="group" aria-label="Zmień motyw">
        <button type="button" class="btn btn-sm btn-light border" style="width:80px;"
        @isTheme(['', null], 'disabled')>{{ trans('icore::default.light') }}</button>
        <button type="button" class="btn btn-sm btn-dark border" style="width:80px;"
        @isTheme('dark', 'disabled')>{{ trans('icore::default.dark') }}</button>
    </div>
</footer>
