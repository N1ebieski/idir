<?php

namespace N1ebieski\IDir\Console\Commands;

use Illuminate\Support\Composer;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Database\DatabaseManager as DB;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Translation\Translator as Lang;
use Illuminate\Contracts\Validation\Factory as Validator;
use N1ebieski\ICore\Console\Commands\InstallCommand as BaseInstallCommand;

class InstallCommand extends BaseInstallCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'idir:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'iDir application installer.';

    /**
     * [protected description]
     * @var GuzzleClient
     */
    protected $guzzle;

    /**
     * Undocumented function
     *
     * @param Composer $composer
     * @param Config $config
     * @param Lang $lang
     * @param Validator $validator
     * @param DB $db
     * @param GuzzleClient $guzzle
     */
    public function __construct(
        Composer $composer,
        Config $config,
        Lang $lang,
        Validator $validator,
        DB $db,
        GuzzleClient $guzzle

    ) {
        parent::__construct($composer, $config, $lang, $validator, $db);

        $this->guzzle = $guzzle;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $bar = $this->output->createProgressBar(17);

        $this->line("iDir installer");
        $this->line("Author: Mariusz Wysokiński");
        $this->line("Version: {$this->config->get('idir.version')}");
        $this->line("\n");
        $this->confirmation();
        $this->line("\n");
        $bar->start();
        $this->line("\n");
        $this->line($this->lang->get('icore::install.publish.langs'));
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'icore.lang', '--force' => true], $this->getOutput());
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'idir.lang', '--force' => true], $this->getOutput());
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->validateUrl();
        $this->validateConnectionMail();
        $this->validateConnectionDatabase();
        $this->validateLicense();
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->line($this->lang->get('icore::install.publish.migrations'));
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'icore.migrations', '--force' => true], $this->getOutput());
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'idir.migrations', '--force' => true], $this->getOutput());
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->line($this->lang->get('icore::install.publish.factories'));
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'icore.factories', '--force' => true], $this->getOutput());
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'idir.factories', '--force' => true], $this->getOutput());
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->line($this->lang->get('icore::install.publish.seeds'));
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'icore.seeds', '--force' => true], $this->getOutput());
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'idir.seeds', '--force' => true], $this->getOutput());
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->line($this->lang->get('icore::install.publish.config'));
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'icore.config', '--force' => true], $this->getOutput());
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'idir.config', '--force' => true], $this->getOutput());
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->line($this->lang->get('icore::install.publish.js'));
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'icore.js', '--force' => true], $this->getOutput());
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'idir.js', '--force' => true], $this->getOutput());
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->line($this->lang->get('icore::install.publish.sass'));
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'icore.sass', '--force' => true], $this->getOutput());
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'idir.sass', '--force' => true], $this->getOutput());
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->line($this->lang->get('icore::install.publish.views'));
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'icore.views.web', '--force' => true], $this->getOutput());
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'idir.views.web', '--force' => true], $this->getOutput());
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->line($this->lang->get('icore::install.publish.public'));
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'icore.public.images', '--force' => true], $this->getOutput());
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'idir.public.images', '--force' => true], $this->getOutput());
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'icore.public.css', '--force' => true], $this->getOutput());
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'idir.public.css', '--force' => true], $this->getOutput());
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'icore.public.js', '--force' => true], $this->getOutput());
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'idir.public.js', '--force' => true], $this->getOutput());
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->line($this->lang->get('icore::install.publish.vendor'));
        $this->line("\n");
        $this->call('vendor:publish', ['--provider' => 'N1ebieski\LogicCaptcha\Providers\LogicCaptchaServiceProvider', '--force' => true], $this->getOutput());
        $this->line("\n");
        $this->call('vendor:publish', ['--provider' => 'Proengsoft\JsValidation\JsValidationServiceProvider', '--force' => true], $this->getOutput());
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'fm-css', '--force' => true], $this->getOutput());
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'fm-js', '--force' => true], $this->getOutput());
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'fm-views', '--force' => true], $this->getOutput());
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->line($this->lang->get('icore::install.dump'));
        $this->line("\n");
        $this->composer->dumpOptimized();
        $this->line("\n");
        $this->info("OK");
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->line($this->lang->get('icore::install.migrate'));
        $this->line("\n");
        $this->call('migrate:fresh', ['--path' => 'database/migrations/vendor/icore', '--force' => true], $this->getOutput());
        $this->line("\n");
        $this->call('migrate', ['--path' => 'database/migrations/vendor/idir', '--force' => true], $this->getOutput());
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->line($this->lang->get('icore::install.seed'));
        $this->line("\n");
        $this->call('db:seed', ['--class' => 'N1ebieski\ICore\Seeds\Install\InstallSeeder', '--force' => true], $this->getOutput());
        $this->line("\n");
        $this->call('db:seed', ['--class' => 'N1ebieski\IDir\Seeds\Install\InstallSeeder', '--force' => true], $this->getOutput());
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->line($this->lang->get('icore::install.register_superadmin'));
        $this->line("\n");
        $this->call('icore:superadmin', [], $this->getOutput());
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->line($this->lang->get('icore::install.cache.routes'));
        $this->line("\n");
        $this->call('route:cache', [], $this->getOutput());
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->line($this->lang->get('icore::install.cache.config'));
        $this->line("\n");
        $this->call('config:cache', [], $this->getOutput());
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->line($this->lang->get('icore::install.storage_link'));
        $this->line("\n");
        $this->call('storage:link', [], $this->getOutput());
        $this->line("\n");
        $bar->finish();
    }
}
