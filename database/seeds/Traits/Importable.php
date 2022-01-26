<?php

namespace N1ebieski\IDir\Seeds\Traits;

use Symfony\Component\Process\Process;

trait Importable
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
     * @return void
     */
    public function setWorkers(int $workers)
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
            $process[$i] = new Process('php artisan queue:work --daemon --stop-when-empty --queue=import --force --once');
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
