<?php

namespace N1ebieski\IDir\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Composer;

class SEOKatalogCommand extends Command
{
    /**
     * [protected description]
     * @var Composer
     */
    protected $composer;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'idir:seokatalog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import database from SEOKatalog directory script';

    /**
     * Create a new command instance.
     *
     * @param Composer  $composer
     * @return void
     */
    public function __construct(Composer $composer)
    {
        parent::__construct();

        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $bar = $this->output->createProgressBar(15);
        
        $this->info("\r");
        $bar->start();
        $this->info("\n");
        $this->call('vendor:publish', ['--tag' => 'icore.migrations', '--force' => true], $this->getOutput());
        $this->info("\r");
        $bar->advance();
        $this->info("\n");
        $this->call('vendor:publish', ['--tag' => 'icore.factories', '--force' => true], $this->getOutput());
        $this->info("\r");
        $bar->advance();
        $this->info("\n");
        $this->call('vendor:publish', ['--tag' => 'icore.seeds', '--force' => true], $this->getOutput());
        $this->info("\r");
        $bar->advance();
        $this->info("\n");
        $this->call('vendor:publish', ['--tag' => 'idir.migrations', '--force' => true], $this->getOutput());
        $this->info("\r");
        $bar->advance();
        $this->info("\n");
        $this->call('vendor:publish', ['--tag' => 'idir.factories', '--force' => true], $this->getOutput());
        $this->info("\r");
        $bar->advance();
        $this->info("\n");
        $this->call('vendor:publish', ['--tag' => 'idir.seeds', '--force' => true], $this->getOutput());
        $this->info("\r");
        $bar->advance();
        $this->info("\n");
        $this->composer->dumpAutoloads();
        $this->info("\r");
        $bar->advance();
        $this->info("\n");
        $this->composer->dumpOptimized();
        $this->info("\r");
        $bar->advance();
        $this->info("\n");
        $this->call('migrate:fresh', ['--path' => 'database/migrations/vendor/icore'], $this->getOutput());
        $this->info("\r");
        $bar->advance();
        $this->info("\n");
        $this->call('migrate', ['--path' => 'database/migrations/vendor/idir'], $this->getOutput());
        $this->info("\r");
        $bar->advance();
        $this->info("\n");
        $this->call('db:seed', ['--class' => 'N1ebieski\ICore\Seeds\DefaultRolesAndPermissionsSeeder'], $this->getOutput());
        $this->info("\r");
        $bar->advance();
        $this->info("\n");
        $this->call('db:seed', ['--class' => 'N1ebieski\IDir\Seeds\DefaultRolesAndPermissionsSeeder'], $this->getOutput());
        $this->info("\r");
        $bar->advance();
        $this->info("\n");
        $this->call('db:seed', ['--class' => 'N1ebieski\IDir\Seeds\DefaultGroupAndPrivilegesSeeder'], $this->getOutput());
        $this->info("\r");
        $bar->advance();
        $this->info("\n");
        $this->call('db:seed', ['--class' => 'N1ebieski\IDir\Seeds\DefaultFieldsSeeder'], $this->getOutput());
        $this->info("\r");
        $bar->advance(); 
        $this->info("\n");
        $this->call('db:seed', ['--class' => 'N1ebieski\IDir\Seeds\DefaultRegionsSeeder'], $this->getOutput());
        $this->info("\r");
        $bar->advance();
        $this->info("\n");
        $this->call('db:seed', ['--class' => 'N1ebieski\IDir\Seeds\SEOKatalogSeeder'], $this->getOutput());
        $this->info("\r");
        $bar->advance();
        $this->info("\n");
        $bar->finish();
    }
}
