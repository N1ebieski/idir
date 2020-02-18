<?php

namespace N1ebieski\IDir\Crons\Dir;

use N1ebieski\IDir\Models\DirStatus;
use N1ebieski\IDir\Jobs\Dir\CheckStatus;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\User;

/**
 * [StatusCron description]
 */
class StatusCron
{
    /**
     * [private description]
     * @var DirStatus
     */
    protected $dirStatus;

    /**
     * [protected description]
     * @var CheckStatus
     */
    protected $checkStatus;

    /**
     * [__construct description]
     * @param DirStatus     $dirStatus     [description]
     * @param CheckStatus $checkStatus [description]
     */
    public function __construct(DirStatus $dirStatus, CheckStatus $checkStatus)
    {
        $this->dirStatus = $dirStatus;
        
        $this->checkStatus = $checkStatus;
    }

    /**
     * [__invoke description]
     */
    public function __invoke() : void
    {
        $this->addToQueue();
    }

    /**
     * Adds new jobs to the queue.
     */
    private function addToQueue() : void
    {
        echo "\n Memory Consumption is   ";
        echo round(memory_get_usage()/1048576.2).''.' MB';

        $i = 0;

        User::chunk(1000, function ($users) {
            $users->filter(function ($user) {
                return $user->id > 500;
                // $this->checkStatus->dispatch($dirStatus);
            });
        });

        // $users = User::all()
        //     ->filter(function ($user) {
        //         return $user->id > 500;
        //         // $this->checkStatus->dispatch($dirStatus);
        //     });

        // foreach ($users as $user) {
        //     $i++;
        // }

        // dump($users->load('dirs')->where('id', 501)->first());

        echo "\n Memory Consumption for " . $i . " items is   ";
        echo round(memory_get_usage()/1048576.2).''.' MB';
    }
}
