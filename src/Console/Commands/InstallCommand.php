<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\IDir\Console\Commands;

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

        $this->call('vendor:publish', ['--tag' => 'icore.lang', '--force' => true]);

        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'idir.lang', '--force' => true]);

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

        $this->call('vendor:publish', ['--tag' => 'icore.migrations', '--force' => true]);

        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'idir.migrations', '--force' => true]);

        $this->line("\n");

        $bar->advance();

        $this->line("\n");

        $this->line($this->lang->get('icore::install.publish.factories'));

        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'icore.factories', '--force' => true]);

        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'idir.factories', '--force' => true]);

        $this->line("\n");

        $bar->advance();

        $this->line("\n");

        $this->line($this->lang->get('icore::install.publish.Seeders'));

        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'icore.seeders', '--force' => true]);

        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'idir.seeders', '--force' => true]);

        $this->line("\n");

        $bar->advance();

        $this->line("\n");

        $this->line($this->lang->get('icore::install.publish.config'));

        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'icore.config', '--force' => true]);

        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'idir.config', '--force' => true]);

        $this->line("\n");

        $bar->advance();

        $this->line("\n");

        $this->line($this->lang->get('icore::install.publish.js'));

        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'icore.js', '--force' => true]);

        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'idir.js', '--force' => true]);

        $this->line("\n");

        $bar->advance();

        $this->line("\n");

        $this->line($this->lang->get('icore::install.publish.sass'));

        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'icore.sass', '--force' => true]);

        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'idir.sass', '--force' => true]);

        $this->line("\n");

        $bar->advance();

        $this->line("\n");

        $this->line($this->lang->get('icore::install.publish.views'));

        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'icore.views.web', '--force' => true]);

        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'idir.views.web', '--force' => true]);

        $this->line("\n");

        $bar->advance();

        $this->line("\n");

        $this->line($this->lang->get('icore::install.publish.public'));

        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'icore.public.images', '--force' => true]);

        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'idir.public.images', '--force' => true]);

        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'icore.public.css', '--force' => true]);

        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'idir.public.css', '--force' => true]);

        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'icore.public.js', '--force' => true]);

        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'idir.public.js', '--force' => true]);

        $this->line("\n");

        $bar->advance();

        $this->line("\n");

        $this->line($this->lang->get('icore::install.publish.vendor'));

        $this->line("\n");

        $this->call('vendor:publish', ['--provider' => 'N1ebieski\LogicCaptcha\Providers\LogicCaptchaServiceProvider', '--force' => true]);

        $this->line("\n");

        $this->call('vendor:publish', ['--provider' => 'Proengsoft\JsValidation\JsValidationServiceProvider', '--force' => true]);

        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'fm-css', '--force' => true]);

        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'fm-js', '--force' => true]);

        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'fm-views', '--force' => true]);

        $this->line("\n");

        $this->call('vendor:publish', ['--provider' => 'Laravel\Sanctum\SanctumServiceProvider', '--tag' => 'sanctum-migrations', '--force' => true]);

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

        $this->call('migrate:fresh', ['--path' => 'database/migrations/2019_12_14_000001_create_personal_access_tokens_table.php', '--force' => true]);

        $this->line("\n");

        $this->call('migrate', ['--path' => 'database/migrations/vendor/icore', '--force' => true]);

        $this->line("\n");

        $this->call('migrate', ['--path' => 'database/migrations/vendor/idir', '--force' => true]);

        $this->line("\n");

        $bar->advance();

        $this->line("\n");

        $this->line($this->lang->get('icore::install.seed'));

        $this->line("\n");

        $this->call('db:seed', ['--class' => 'N1ebieski\ICore\Database\Seeders\Install\InstallSeeder', '--force' => true]);

        $this->line("\n");

        $this->call('db:seed', ['--class' => 'N1ebieski\IDir\Database\Seeders\Install\InstallSeeder', '--force' => true]);

        $this->line("\n");

        $bar->advance();

        $this->line("\n");

        $this->line($this->lang->get('icore::install.register_superadmin'));

        $this->line("\n");

        $this->call('icore:superadmin', []);

        $this->line("\n");

        $bar->advance();

        $this->line("\n");

        $this->line($this->lang->get('icore::install.cache.routes'));

        $this->line("\n");

        $this->call('route:cache', []);

        $this->line("\n");

        $bar->advance();

        $this->line("\n");

        $this->line($this->lang->get('icore::install.cache.config'));

        $this->line("\n");

        $this->call('config:cache', []);

        $this->line("\n");

        $bar->advance();

        $this->line("\n");

        $this->line($this->lang->get('icore::install.storage_link'));

        $this->line("\n");

        $this->call('storage:link', []);

        $this->line("\n");

        $bar->finish();
    }
}
