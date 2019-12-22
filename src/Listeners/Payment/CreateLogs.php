<?php

namespace N1ebieski\IDir\Listeners\Payment;

use Illuminate\Http\Request;

/**
 * [CreateLogs description]
 */
class CreateLogs
{
    /**
     * [private description]
     * @var Request
     */
    protected $request;

    /**
     * Create the event listener.
     *
     * @param  Request  $request  [description]
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        if (!$this->request->has('logs')) {
            return;
        }

        $logs = "";
        foreach (array_map('strval', $this->request->input('logs')) as $key => $value) {
            $logs .= $key . ': ' . $value . "\n";
        }

        $event->payment->makeService()->updateLogs(['logs' => $logs]);
    }
}
