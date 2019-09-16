<footer class="page-footer font-small pt-4">
    <div class="container text-center text-md-left">
        <div class="row ">
            <div class="col-md mx-auto">
                <h5 class="mt-3 mb-4">Footer Content:</h5>
                <p>Here you can use rows and columns here to organize your footer content. Lorem
                    ipsum dolor sit amet, consectetur
                    adipisicing elit.</p>
            </div>
            <hr class="clearfix w-100 d-md-none">
            <div class="col-md mx-auto">
                @render('icore::newsletterComponent')
            </div>
            <hr class="clearfix w-100 d-md-none">
            <div class="col-md-auto mx-auto">
                <h5 class="mt-3 mb-4">{{ trans('icore::default.theme_toggle') }}:</h5>
                <div class="btn-group mb-3" id="themeToggle" role="group" aria-label="Zmień motyw">
                    <button type="button" class="btn btn-sm btn-light border" style="width:80px;"
                    @isTheme(['', null], 'disabled')>{{ trans('icore::default.light') }}</button>
                    <button type="button" class="btn btn-sm btn-dark border" style="width:80px;"
                    @isTheme('dark', 'disabled')>{{ trans('icore::default.dark') }}</button>
                </div>
            </div>
            <hr class="clearfix w-100 d-md-none">
        </div>
        {{-- @render('icore::page.footerComponent', ['pattern' => [[18, 19, 32], [45], [3, 1]]]) --}}
        @render('icore::page.footerComponent', ['cols' => 3])
    </div>
    <hr>
    <div class="footer-copyright text-center py-3">
        <small>
            2019 Copyright © <a href="">iCore</a> by Mariusz Wysokiński
        </small>
    </div>
</footer>
