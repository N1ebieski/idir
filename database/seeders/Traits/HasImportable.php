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

namespace N1ebieski\IDir\Database\Seeders\Traits;

use Symfony\Component\Process\Process;

trait HasImportable
{
    /**
     * Undocumented variable
     *
     * @var int
     */
    private $workers;

    /**
     * Undocumented function
     *
     * @param integer $workers
     * @return self
     */
    public function setWorkers(int $workers): self
    {
        $this->workers = $workers;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function import(): void
    {
        $this->command->getOutput()->writeln("\n");

        $startSize = $prevSize = $this->queue->size('import');
        $importBar = $this->command->getOutput()->createProgressBar($startSize);
        $importBar->start();

        $process = [];
        $cycle = 1;

        for ($i = 0; $i < $this->workers; $i++) {
            $process[$i] = Process::fromShellCommandline('php artisan queue:work --daemon --stop-when-empty --queue=import --force --once');
            $process[$i]->setTimeout(null);
        }

        while (true) {
            for ($i = 0; $i < $this->workers; $i++) {
                if (!$process[$i]->isRunning()) {
                    $process[$i]->start();
                }
            }

            sleep(10);

            $currentSize = $this->queue->size('import');

            if ($currentSize !== $prevSize) {
                $cycle = 1;
            }

            if ($currentSize === 0 || $cycle > 100) {
                $importBar->finish();
                break;
            }

            $importBar->advance($startSize - $currentSize);
            $startSize = $prevSize = $currentSize;
            $cycle++;
        }

        $this->command->getOutput()->writeln("\n");
    }
}
