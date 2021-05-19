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
    protected function import() : void
    {
        $this->command->getOutput()->writeln("\n");

        $startSize = $this->queue->size('import');
        $importBar = $this->command->getOutput()->createProgressBar($startSize);
        $importBar->start();

        for ($i = 0; $i < $this->workers; $i++) {
            $process[$i] = new Process('php artisan queue:work --daemon --stop-when-empty --queue=import');
            $process[$i]->setTimeout(null);
            $process[$i]->start();
        }

        while (true) {
            sleep(10);

            $currentSize = $this->queue->size('import');
            $j = 0;

            for ($i = 0; $i < $this->workers; $i++) {
                if (!$process[$i]->isRunning()) {
                    $j++;
                }
            }

            if ($j === $this->workers) {
                $importBar->finish();
                break;
            }

            $importBar->advance($startSize - $currentSize);
            $startSize = $currentSize;
        }

        $this->command->getOutput()->writeln("\n");
    }
}
