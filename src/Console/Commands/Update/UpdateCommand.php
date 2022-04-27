<?php

namespace N1ebieski\IDir\Console\Commands\Update;

use N1ebieski\ICore\Utils\Updater\Updater;
use N1ebieski\ICore\Console\Commands\Update\UpdateCommand as BaseCommand;

class UpdateCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'idir:update {version : The version to which the application files will be updated}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'iDir application updater.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $bar = $this->output->createProgressBar(3);

        $this->line("iDir updater");
        $this->line("Author: Mariusz Wysokiński");
        $this->line("Version: {$this->config->get('idir.version')}");
        $this->line("\n");
        $this->confirmation();
        $this->line("\n");
        $bar->start();
        $this->line("\n");
        $this->validateBackup();
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->backup();
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->update();
        $this->line("\n");
        $bar->finish();
    }
}
