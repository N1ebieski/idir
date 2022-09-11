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

use Illuminate\Console\Command;
use Illuminate\Support\Composer;

class EnvCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'idir:env';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a work environment';

    /**
     * Create a new command instance.
     *
     * @param Composer  $composer
     * @return void
     */
    public function __construct(protected Composer $composer)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $bar = $this->output->createProgressBar(7);

        $this->info("\n");

        $bar->start();

        $this->info("\n");

        $this->call('vendor:publish', ['--tag' => 'icore.migrations', '--force' => true]);

        $this->info("\n");

        $this->call('vendor:publish', ['--tag' => 'idir.migrations', '--force' => true]);

        $this->info("\n");

        $bar->advance();

        $this->info("\n");

        $this->call('vendor:publish', ['--tag' => 'icore.factories', '--force' => true]);

        $this->info("\n");

        $this->call('vendor:publish', ['--tag' => 'idir.factories', '--force' => true]);

        $this->info("\n");

        $bar->advance();

        $this->info("\n");

        $this->call('vendor:publish', ['--tag' => 'icore.seeders', '--force' => true]);

        $this->info("\n");

        $this->call('vendor:publish', ['--tag' => 'idir.seeders', '--force' => true]);

        $this->info("\n");

        $bar->advance();

        $this->info("\n");

        $this->composer->dumpOptimized();

        $this->info("\n");

        $bar->advance();

        $this->info("\n");

        $this->call('migrate:fresh', ['--path' => 'database/migrations/vendor/icore']);

        $this->info("\n");

        $this->call('migrate', ['--path' => 'database/migrations/vendor/idir']);

        $this->info("\n");

        $this->call('migrate', ['--path' => 'database/migrations/2019_12_14_000001_create_personal_access_tokens_table.php', '--force' => true]);

        $this->line("\n");

        $bar->advance();

        $this->info("\n");

        $this->call('db:seed', ['--class' => 'N1ebieski\ICore\Database\Seeders\Env\EnvSeeder']);

        $this->info("\n");

        $this->call('db:seed', ['--class' => 'N1ebieski\IDir\Database\Seeders\Env\EnvSeeder']);

        $this->info("\n");

        $bar->advance();

        $this->line("\n");

        $this->call('icore:superadmin', []);

        $this->info("\n");

        $bar->finish();
    }
}
