<?php

namespace N1ebieski\IDir\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Composer;

/**
 * [EnvTestingCommand description]
 */
class EnvTestingCommand extends Command
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
    protected $signature = 'idir:env-testing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a testing environment';

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
        $bar = $this->output->createProgressBar(12);

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
        $this->call('db:seed', ['--class' => 'N1ebieski\ICore\Seeds\Install\InstallSeeder'], $this->getOutput());
        $this->info("\r");
        $bar->advance();
        $this->info("\n");
        $this->call('db:seed', ['--class' => 'N1ebieski\IDir\Seeds\Install\InstallSeeder'], $this->getOutput());
        $this->info("\r");
        $bar->advance();
        $this->info("\n");
        $bar->finish();
    }
}
