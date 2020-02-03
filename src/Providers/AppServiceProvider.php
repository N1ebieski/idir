<?php

namespace N1ebieski\IDir\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * [AppServiceProvider description]
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('crypt.thumbnail', function() {
            return new \Illuminate\Encryption\Encrypter(
                $this->app['config']['idir.dir.thumbnail.key'],
                $this->app['config']['app.cipher']
            );
        });

        $this->app->extend('translator', function() {
            return new \N1ebieski\IDir\Translation\Translator(
                $this->app['translation.loader'],
                $this->app['config']['app.locale']
            );
        });

        $this->app->bind(\N1ebieski\ICore\Http\Requests\Admin\BanValue\IndexRequest::class, function($app, array $with) {
            return $this->app->make(\N1ebieski\IDir\Http\Requests\Admin\BanValue\IndexRequest::class, $with);
        });

        $this->app->bind(\N1ebieski\ICore\Http\Requests\Admin\BanValue\CreateRequest::class, function($app, array $with) {
            return $this->app->make(\N1ebieski\IDir\Http\Requests\Admin\BanValue\CreateRequest::class, $with);
        });

        $this->app->bind(\N1ebieski\ICore\Http\Requests\Admin\BanValue\StoreRequest::class, function($app, array $with) {
            return $this->app->make(\N1ebieski\IDir\Http\Requests\Admin\BanValue\StoreRequest::class, $with);
        });

        $this->app->bind(\N1ebieski\ICore\Http\Requests\Admin\Role\UpdateRequest::class, function($app, array $with) {
            return $this->app->make(\N1ebieski\IDir\Http\Requests\Admin\Role\UpdateRequest::class, $with);
        });

        $this->app->bind(\N1ebieski\ICore\Http\Requests\Web\Search\IndexRequest::class, function($app, array $with) {
            return $this->app->make(\N1ebieski\IDir\Http\Requests\Web\Search\IndexRequest::class, $with);
        });        

        $this->app->bindMethod(\N1ebieski\IDir\Jobs\Tag\Dir\CachePopularTags::class.'@handle', function($job, $app) {
            return $job->handle($app->make(\N1ebieski\IDir\Models\Tag\Dir\Tag::class));
        });  

        // $this->app->bind(\N1ebieski\ICore\Models\User::class, function($app, array $with) {
        //     return $this->app->make(\N1ebieski\IDir\Models\User::class, $with);
        // });
    }
}
