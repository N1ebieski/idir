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

namespace N1ebieski\IDir\Console\Commands\Update;

use N1ebieski\ICore\Console\Commands\Update\RollbackCommand as BaseRollbackCommand;

class RollbackCommand extends BaseRollbackCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'idir:update:rollback {version : The version to which the application files will be restored}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'iDir application updater rollback.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $bar = $this->output->createProgressBar(2);

        $this->line("iDir updater rollback");
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

        $this->rollback();

        $this->line("\n");

        $bar->finish();
    }
}
